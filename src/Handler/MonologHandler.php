<?php
declare(strict_types=1);

namespace Vim\ErrorTracking\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
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
    protected function write(LogRecord $record): void
    {
        $level = \strtolower($record->level->toPsrLogLevel());
        $message = $this->extractMessageFromTheRecord($record);
        $throwable = $this->extractThrowableFromTheRecord($record);
        $namespace = $throwable ? \get_class($throwable) : null;
        $code = $throwable?->getCode();

        if (\in_array(strtolower($level), \array_map('strtolower', $this->ignoredLevels))) {
            return;
        }

        foreach ($this->ignoredMessages as $ignoredMessagePattern) {
            if (\preg_match($ignoredMessagePattern, $message)) {
                return;
            }
        }

        if (\in_array($namespace, $this->ignoredExceptions)) {
            return;
        }

        if (\in_array($code, $this->ignoredCodes)) {
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

        if ($throwable) {
            $content['trace'] = $throwable->getTraceAsString();
            $content['code'] = $code;
            $content['file'] = $throwable->getFile();
            $content['line'] = $throwable->getLine();
            $content['namespace'] = $namespace;
        }

        try {
            $this->client->request('POST', $this->url, ['body' => json_encode($content)]);
        } catch (\Throwable $exception) {
            $this->logService->logThrowable($exception);
        }
    }

    private function extractMessageFromTheRecord(LogRecord $record): string
    {
        $message = $record->message;
        \preg_match_all('/{(.*?)}/', $message, $matches);
        $search = $matches[0] ?? [];
        $replace = \array_map(
            function (string $val) use ($record) {
                return $record->context[$val] ?? '['.$val.']';
            },
            $matches[1] ?? []
        );

        return \str_replace($search, $replace, $message);
    }

    private function extractThrowableFromTheRecord(LogRecord $record): ?\Throwable
    {
        $throwable = $record->context['exception'] ?? $record->context['throwable'] ?? null;

        return $throwable instanceof \Throwable ? $throwable : null;
    }
}
