<?php
declare(strict_types=1);

namespace Models\File;

/**
 * @package Models\File
 */
class Manager
{
    private array $data;

    public function __construct()
    {
        $this->data = $_FILES;
    }

    /**
     * Check given file
     *
     * @return bool
     */
    public function isImage(): bool
    {
        return $this->data['image']['error'] == UPLOAD_ERR_OK &&
            ($this->data['image']['type'] == 'image/jpeg' || $this->data['image']['type'] == 'image/png');
    }


    /**
     * @param string $key
     * @return string
     */
    public function getFileName(string $key): string
    {
        return $this->data[$key]['name'];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getFileTmpName(string $key): string
    {
        return $this->data[$key]['tmp_name'];
    }
}
