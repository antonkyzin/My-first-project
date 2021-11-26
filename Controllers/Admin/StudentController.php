<?php
declare(strict_types=1);

namespace Controllers\Admin;

use Controllers\BaseController;
use Models\StudentModel;
use View\DefaultView;

/**
 * @package Controllers\Admin
 */
class StudentController extends BaseController
{
    private StudentModel $studentModel;

    private DefaultView $defaultView;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->defaultView = new DefaultView();
    }

    /**
     * Get all students list and render admin/students page
     *
     * @return void
     */
    public function allStudentsAction(): void
    {
        if ($this->studentModel->isAccess()) {
            $data = $this->studentModel->allStudents();
            $options = $this->defaultView->getOptions('Студенты', 'admin/students.phtml', $data);
            $this->defaultView->render($options);
        } else {
            $this->homeLocation();
        }
    }
}
