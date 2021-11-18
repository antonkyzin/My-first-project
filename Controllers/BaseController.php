<?php

declare(strict_types=1);

namespace Controllers;

/**
 * @package Controllers
 * Base controller for extends with base methods
 */
class BaseController
{
    /**
     * Check for valid array POST
     * @return bool
     */
    protected function checkPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST);
    }

    /**
     * Set header Location by url
     * @param string $url
     * @return void
     */
    public function location(string $url): void
    {
        header("Location: $url");
    }

    /**
     * Set header Location home
     * @return void
     */
    public function homeLocation(): void
    {
        header('Location: /');
    }
}
