<?php

namespace core\logger;

use php\lib\str;
use php\time\Time;
use php\time\TimeZone;

class Logger
{
    public static $timeFormat = "MM/dd/YYYY HH:mm:ss";

    public static function error($message)
    {
        self::log('error', $message);
    }

    public static function info($message)
    {
        self::log('info', $message);
    }

    private static function log($type, $message): void
    {
        echo sprintf("[%s] %s - %s\n", self::getTime(), str::upper($type), $message);
    }

    private static function getTime(): string
    {
        return Time::now(TimeZone::getDefault())->toString(self::$timeFormat);
    }
}