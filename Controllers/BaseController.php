<?php

declare(strict_types=1);

namespace Controllers;

/**
 * Base controller for extends with base methods
 *
 * @package Controllers
 */
class BaseController
{
    /**
     * Set header Location by url
     *
     * @param string $url
     * @return void
     */
    public function location(string $url): void
    {
        header("Location: $url");
    }

    /**
     * Set header Location home
     *
     * @return void
     */
    public function homeLocation(): void
    {
        header('Location: /');
    }
}
