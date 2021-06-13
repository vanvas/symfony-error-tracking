<?php
declare(strict_types=1);

namespace Vim\ErrorTracking\Service;

class UnexpectedErrorLogService
{
    public function __construct(private string $path)
    {
    }

    public function log(string $message): void
    {
        file_put_contents(
            $this->path . DIRECTORY_SEPARATOR . 'error_tracking_' . date('Y-m-d') . '.error.log',
            $message,
            FILE_APPEND
        );
    }

    public function logThrowable(\Throwable $throwable): void
    {
        $this->log($throwable->getMessage() . ' :: ' . $throwable->getTraceAsString());
    }
}
