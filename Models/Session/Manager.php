<?php
declare(strict_types=1);

namespace Models\Session;

use Interfaces\IDataManagement;

/**
 * @package Models\Session
 */
class Manager implements IDataManagement
{
    private ?array $data = null;

    public function __construct()
    {
        $this->initialize();
    }

    /**
     *Initialize session data in property
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->data = $_SESSION;
    }

    /**
     * Destroy session data
     *
     * @return void
     */
    public function destroy(): void
    {
        session_destroy();
    }

    /**
     * Get user data from session
     *
     * @return array|null
     */
    public function getUser()
    {
        return $this->data['user'] ?? null;
    }

    /**
     * Set session data about user
     *
     * @param string $key
     * @param string|array $data
     * @return void
     */
    public function setUserData(string $key, $data): void
    {
        if (isset($this->data['user'][$key]) && is_array($this->data['user'][$key])) {
            $this->data['user'][$key][] = $data;
        } else {
            $this->data['user'][$key] = $data;
        }
        $_SESSION['user'] = $this->data['user'];
    }
}
