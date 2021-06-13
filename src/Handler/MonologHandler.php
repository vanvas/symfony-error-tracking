<?php
declare(strict_types=1);

namespace Vim\ErrorTracking\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Vim\ErrorTracking\Helper\ProcessHelper;
use Vim\ErrorTracking\Service\UnexpectedErrorLogService;

class MonologHandler extends AbstractProcessingHandler
{
    public function __construct(
        private HttpClientInterface $client,
        private string $env,
        private array $ignoredExceptions,
        private array $ignoredCodes,
        private array $ignoredLevels,
        private array $ignoredMessages,
        private UnexpectedErrorLogService $logService,
        private string $url
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        $message = $this->getMessageFromRecord($record);
        $level = $record['level_name'];
        $exception = $this->getExceptionFromRecord($record);
        $namespaceNamespace = $exception ? get_class($exception) : null;
        $code = $exception ? $exception->getCode() : null;

        if (in_array(strtolower($level), array_map('strtolower', $this->ignoredLevels))) {
            return;
        }

        foreach ($this->ignoredMessages as $ignoredMessagePattern) {
            if (preg_match($ignoredMessagePattern, $message)) {
                return;
            }
        }

        if (in_array($namespaceNamespace, $this->ignoredExceptions)) {
            return;
        }

        if (in_array($code, $this->ignoredCodes)) {
            return;
        }

        $content = [
            'level' => $level,
            'message' => $message,
            'server' => $_SERVER,
            'env' => $this->env,
            'process' => ProcessHelper::getProcessId(),
            'date' => isset($record['datetime']) ? $record['datetime']->format(\DATE_ATOM) : null,
        ];

        if ($exception) {
            $content['trace'] = $exception->getTraceAsString();
            $content['code'] = $code;
            $content['file'] = $exception->getFile();
            $content['line'] = $exception->getLine();
            $content['namespace'] = $namespaceNamespace;
        }

        try {
            $this->client->request('POST', $this->url, ['body' => json_encode($content)]);
        } catch (\Throwable $exception) {
            $this->logService->logThrowable($exception);
        }
    }

    private function getMessageFromRecord(array $record): string
    {
        $message = $record['message'];
        preg_match_all('/{(.*?)}/', $message, $matches);
        $search = $matches[0] ?? [];
        $replace = array_map(
            function (string $val) use ($record) {
                return $record['context'][$val] ?? '['.$val.']';
            },
            $matches[1] ?? []
        );

        return str_replace($search, $replace, $message);
    }

    private function getExceptionFromRecord(array $record): ?\Throwable
    {
        $context = $record['context'] ?? [];
        /** @var \Throwable|null $exception */
        $exception = $context['exception'] ?? null;
        if (!$exception instanceof \Throwable) {
            $exception = $context['throwable'] ?? null;
            if (!$exception instanceof \Throwable) {
                $exception = null;
            }
        }

        return $exception;
    }
}
