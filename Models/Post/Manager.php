<?php
declare(strict_types=1);

namespace Models\Post;

/**
 * @package Models\Post
 */
class Manager
{
    private array $data;

    public function __construct()
    {
        $this->data = $_POST;
    }

    /**
     * Check for valid array POST
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($this->data);
    }

    /**
     * Get data from POST
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
