<?php
declare(strict_types=1);

namespace Models\Config;

use Interfaces\IDataManagement;

class Manager implements IDataManagement
{
    private ?array $data = null;

    public function __construct()
    {
        if (is_file('data/config.php')) {
            $this->data = require_once 'data/config.php';
        }
    }

    /**
     * Get database connection data
     *
     * @return array
     * @throws \Exception
     */
    public function getDBdata(): array
    {
        if (!isset($this->data['db_params'])) {
            throw new \Exception('Needed params or file does not exist');
        }
        return $this->data['db_params'];
    }

    public function getNewPostApiKey(): string
    {
        if (!isset($this->data['new_post_api_key'])) {
            throw new \Exception('API key does not exist');
        }
        return $this->data['new_post_api_key'];
    }
}
