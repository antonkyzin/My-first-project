<?php
declare(strict_types=1);

namespace Controllers;

use Models\StudentModel;

/**
 * @package Controllers
 */
class StudentController extends BaseController
{
    /**
     * @var StudentModel
     */
    private StudentModel $studentModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
    }

    /**
     * Set session data about groups and claims when a student logins
     * @return void
     */
    public function loginAction(): void
    {
        if ($this->studentModel->isSigned() == 'students') {
            $groups = $this->studentModel->getGroups();
            $courseClaim = $this->studentModel->getcourseClaim();
            $this->studentModel->setSessionData($groups, $courseClaim);
        }
        $this->homeLocation();
    }
}
