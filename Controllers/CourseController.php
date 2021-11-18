<?php
declare(strict_types=1);

namespace Controllers;

use Models\CourseModel;
use View\CourseView;

/**
 * @package Controllers
 */
class CourseController extends BaseController
{
    /**
     * @var CourseModel
     */
    private CourseModel $courseModel;

    /**
     * @var CourseView
     */
    private CourseView $courseView;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->courseView = new CourseView();
    }

    /**
     * Get courses list
     * @return void
     */
    public function listAction(): void
    {
        $data = $this->courseModel->getActiveCourses();
        $options = ['title' => 'Курсы английского',
            'content' => 'courses.phtml',
            'data' => $data];
        $options['user'] = $_SESSION['user'] ?? null;
        $this->courseView->render($options);
    }

    /**
     * Get information about a course
     * @param array $param
     * @return void
     */
    public function courseInfoAction(array $param): void
    {
        if (isset($_SESSION['user'])) {
            $data['user'] = $_SESSION['user'];
        }
        $data['course'] = $this->courseModel->getCourseInfo($param[0]);
        $options = ['title' => 'Курс',
            'content' => 'course_info.phtml',
            'data' => $data];
        $this->courseView->render($options);
    }

    /**
     * Add a claim to a course
     * @param array $params
     * @return void
     */
    public function addToCourseClaimAction(array $params): void
    {
        if ($this->courseModel->isSigned() == 'students') {
            $studentId = $params[0];
            $courseId = $params[1];
            $result = $this->courseModel->addToCourseClaim($studentId, $courseId);
            if ($result) {
               setcookie('Alert', $courseId, time()+3600, '/');
                $this->location('/course/courseInfo/' . $params[1]);
            } else {
                $this->homeLocation();
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Sort a courses by param
     * @return void
     */
    public function sortAction(): void
    {
        if ($this->checkPost()) {
            $mode = explode('/', $_POST['sort_by']);
            $showAll = isset($_POST['show_all']) ?? false;
            $data = $this->courseModel->sortCourses($mode[0], $mode[1], $showAll);
            $options = ['title' => 'Курсы английского',
                'content' => 'courses.phtml',
                'data' => $data];
            $options['user'] = $_SESSION['user'] ?? null;
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Find a course by description or title
     * @return void
     */
    public function searchAction(): void
    {
        if ($this->checkPost()) {
            $options = ['title' => 'Курсы английского',
                'content' => 'courses.phtml'];
            $data = $this->courseModel->search($_POST['search']);
            if ($data) {
                $options['data'] = $data;
            } else {
                $options['errMsg'] = 'Курс не найден';
            }
            $options['user'] = $_SESSION['user'] ?? null;
            $this->courseView->render($options);
        }
    }

    /**
     * Get info about a facultative
     * @param array $param
     * @return void
     */
    public function facultativeInfoAction(array $param): void
    {
        if (isset($_SESSION['user'])) {
            $data['user'] = $_SESSION['user'];
        }
        $data['facultative'] = $this->courseModel->getFacultativeInfo($param[0]);
        $options = ['title' => 'Факультатив',
            'content' => 'facultative_info.phtml',
            'data' => $data];
        $this->courseView->render($options);
    }

    /**
     * All statistics about courses
     * @return void
     */
    public function dashboardAction(): void
    {
        if ($this->courseModel->isSigned() == 'family') {
            $data['courses'] = $this->courseModel->getDataForDashboard();
            $data['sumRevenue'] = $this->courseModel->countSumRevenue();
            $options = ['title' => 'Дашборд',
                'content' => 'dashboard.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get students list on a course
     * @param array $param
     * @return void
     */
    public function studentsOnCourseAction(array $param): void
    {
        if ($this->courseModel->isSigned() == 'family') {
            $data = $this->courseModel->getStudentsOnCourse($param[0]);
            $options = ['title' => 'Студенты',
                'content' => 'dashboard_students.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get facultatives list on a course
     * @param array $param
     * @return void
     */
    public function facultativesOnCourseAction(array $param): void
    {
        if ($this->courseModel->isSigned() == 'family') {
            $data = $this->courseModel->facultativesOnCourse($param[0]);
            $options = ['title' => 'Факультативы',
                'content' => 'dashboard_facultatives.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get students list on a facultative
     * @param array $param
     * @return void
     */
    public function studentsOnFacultativeAction(array $param): void
    {
        if ($this->courseModel->isSigned() == 'family') {
            $data = $this->courseModel->getStudentsOnFacultative($param[0]);
            $options = ['title' => 'Факультативы',
                'content' => 'dashboard_students.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Register a student on a facultative
     * @return void
     */
    public function registerToFacultativeAction(): void
    {
        if ($this->checkPost()) {
            if ($this->courseModel->isSigned() == 'students') {
                $this->courseModel->registerToFacultative($_POST);
                $this->location($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->homeLocation();
        }
    }
}
