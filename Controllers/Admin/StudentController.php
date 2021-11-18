<?php
declare(strict_types=1);

namespace Controllers\Admin;

use Controllers\BaseController;
use Models\StudentModel;
use View\StudentView;

/**
 * @package Controllers\Admin
 */
class StudentController extends BaseController
{
    /**
     * @var StudentModel
     */
    private StudentModel $studentModel;

    /**
     * @var StudentView
     */
    private StudentView $studentView;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->studentView = new StudentView();
    }

    /**
     * Get all students list and render admin/students page
     * @return void
     */
    public function allStudentsAction(): void
    {
        if ($this->studentModel->isAccess()) {
            $data = $this->studentModel->allStudents();
            $options = ['title' => 'Студенты',
                'content' => 'admin/students.phtml',
                'data' => $data];
            $this->studentView->render($options);
        } else {
            $this->homeLocation();
        }
    }
}
