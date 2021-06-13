<?php
declare(strict_types=1);

namespace Vim\ErrorTracking\Helper;

class ProcessHelper
{
    private static ?string $processId = null;

    private static bool $locked = false;

    public static function getProcessId(): string
    {
        if (null === self::$processId) {
            self::$processId = md5(uniqid() . rand(100000, 9999999));
        }

        return self::$processId;
    }

    public static function lock(bool $state = null): bool
    {
        if (null !== $state) {
            self::$locked = $state;
        }

        return self::$locked;
    }
}
