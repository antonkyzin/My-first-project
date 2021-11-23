<?php
declare(strict_types=1);

namespace Models;

/**
 * @package Models
 */
class DataRegistry
{
    private static DataRegistry $instance;
    private array $registry;

    /**
     * Prevent from creating multiple instances
     */
    private function __construct()
    {
    }

    /**
     * Prevent the instance from being cloned
     */
    private function __clone()
    {
    }

    /**
     * Prevent from being unserialized
     */
    private function __wakeup()
    {
    }

    /**
     * @return DataRegistry
     */
    public static function getInstance(): DataRegistry
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @param string $key
     * @param object $object
     * @throws \Exception
     */
    public function register(string $key, object $object): void
    {
        if (!isset($this->registry[$key])) {
            $this->registry[$key] = $object;
        } else {
            throw new \Exception('Item with the same key already exists.');
        }
    }

    public function get(string $key): object
    {
        return $this->registry[$key];
    }
}
