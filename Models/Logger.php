<?php

namespace Models;

/**
 * @package Models
 */
class Logger
{
    private const FILENAME = 'logs/log.txt';

    public function log(string $data): void
    {
        $data = date('[H:i:s d-m-Y]--', time()) . $data;
        file_put_contents(self::FILENAME, $data . PHP_EOL, FILE_APPEND);
    }
}
