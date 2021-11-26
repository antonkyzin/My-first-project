<?php
declare(strict_types=1);

namespace Models;

use Interfaces\IDataManagement;

/**
 * @package Models
 */
class ApiModel
{
    private IDataManagement $config;

    public function __construct()
    {
        $this->config = new Config\Manager();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAreas(): array
    {
        $apiKey = $this->getApiKey();
        $options = $this->setOptions($apiKey, 'Address', 'getAreas');
        $curl = $this->curlInit($options);
        return $this->curlExec($curl);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCities(): array
    {
        $apiKey = $this->getApiKey();
        $options = $this->setOptions($apiKey, 'Address', 'getCities');
        $curl = $this->curlInit($options);
        return $this->curlExec($curl);
    }

    /**
     * @return string
     */
    private function getApiKey(): string
    {
        return $this->config->getNewPostApiKey();
    }

    /**
     * Set options for query to api
     *
     * @param string $apiKey
     * @param string $model
     * @param string $method
     * @param array|null $params
     * @return string
     */
    private function setOptions(string $apiKey, string $model, string $method, array $params = null): string
    {
        $options =
            [
                'apiKey' => $apiKey,
                'modelName' => $model,
                'calledMethod' => $method,
                'methodProperties' => $params
            ];

        return json_encode($options);
    }

    /**
     * @param string $options
     * @return false|resource
     */
    private function curlInit(string $options)
    {
        $curl = curl_init('https://api.novaposhta.ua/v2.0/json/');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $options);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        return $curl;
    }

    /**
     * @param $curl
     * @return array
     * @throws \Exception
     */
    private function curlExec($curl): array
    {
        $errMsg = '';
        $output = curl_exec($curl);
        $result = json_decode($output, true);
        curl_close($curl);
        if (!$result['success']) {
            foreach ($result['errors'] as $message) {
                $errMsg .= $message . ' ';
            }
            throw new \Exception('Api error: ' . $errMsg);
        }
        return $result['data'];
    }
}
