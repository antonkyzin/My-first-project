<?php
declare(strict_types=1);

namespace Models\Server;

/**
 * @package Models\Server
 */
class Manager
{
    private array $data;

    public function __construct()
    {
        $this->data = $_SERVER;
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->data['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getReferer(): string
    {
        return $this->data['HTTP_REFERER'];
    }
}
