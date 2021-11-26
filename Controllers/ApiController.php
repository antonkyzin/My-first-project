<?php
declare(strict_types=1);

namespace Controllers;

use Models\ApiModel;
use View\DefaultView;

/**
 * @package Controllers
 */
class ApiController extends BaseController
{
    private ApiModel $apiModel;
    private DefaultView $defaultView;

    public function __construct()
    {
        $this->apiModel = new ApiModel();
        $this->defaultView = new DefaultView();
    }

    /**
     * @return void
     */
    public function indexAction(): void
    {
        try {
            $data['api'] = $this->apiModel->getAreas();
        } catch (\Exception $exception) {
            $data['errMsg'] = $exception->getMessage();
        }
        $options = $this->defaultView->getOptions('Области Украины', 'api.phtml', $data);
        $this->defaultView->render($options);
    }

    /**
     * @return void
     */
    public function getCitiesAction(): void
    {
        try {
            $data['api'] = $this->apiModel->getCities();
        } catch (\Exception $exception) {
            $data['errMsg'] = $exception->getMessage();
        }
        $options = $this->defaultView->getOptions('Города Украины', 'api.phtml', $data);
        $this->defaultView->render($options);
    }
}
