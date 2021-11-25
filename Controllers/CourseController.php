<?php
declare(strict_types=1);

namespace Controllers;

use Models\CourseModel;
use Models\DataRegistry;
use View\CourseView;
use Interfaces\IDataManagement;

/**
 * @package Controllers
 */
class CourseController extends BaseController
{
    private CourseModel $courseModel;
    private CourseView $courseView;

    /**
     * Object for access to session data
     */
    private IDataManagement $sessionData;

    /**
     * Object for access to POST data
     */
    private IDataManagement $postData;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->courseView = new CourseView();
        $this->sessionData = DataRegistry::getInstance()->get('session');
        $this->postData = DataRegistry::getInstance()->get('post');
    }

    /**
     * Get courses list
     *
     * @return void
     */
    public function listAction(): void
    {
        $data['courses'] = $this->courseModel->getActiveCourses();
        $options = $this->courseView->getOptions('Курсы английского', 'courses.phtml', $data);
        $this->courseView->render($options);
    }

    /**
     * Get information about a course
     *
     * @param array $param
     * @return void
     */
    public function courseInfoAction(array $param): void
    {
        $data['user'] = $this->sessionData->getUser();
        $data['course'] = $this->courseModel->getCourseInfo($param[0]);
        $options = $this->courseView->getOptions('Курс', 'course_info.phtml', $data);
        $this->courseView->render($options);
    }

    /**
     * Add a claim to a course
     *
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
                setcookie('Alert', $courseId, time() + 60 * 60 * 24 * 30 * 12, '/');
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
     *
     * @return void
     */
    public function sortAction(): void
    {
        if ($this->postData->isPost()) {
            $mode = explode('/', $this->postData->getData()['sort_by']);
            $showAll = isset($this->postData->getData()['show_all']) ?? false;
            $data['courses'] = $this->courseModel->sortCourses($mode[0], $mode[1], $showAll);
            $options = $this->courseView->getOptions('Курсы английского', 'courses.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Find a course by description or title
     *
     * @return void
     */
    public function searchAction(): void
    {
        if ($this->postData->isPost()) {
            $title = 'Курсы английского';
            $content = 'courses.phtml';
            $data['courses'] = $this->courseModel->search($this->postData->getData()['search']);
            if (!$data['courses']) {
                $data['errMsg'] = 'Курс не найден';
            }
            $options = $this->courseView->getOptions($title, $content, $data);
            $this->courseView->render($options);
        }
    }

    /**
     * Get info about a facultative
     *
     * @param array $param
     * @return void
     */
    public function facultativeInfoAction(array $param): void
    {
        $data['user'] = $this->sessionData->getUser();
        $data['facultative'] = $this->courseModel->getFacultativeInfo($param[0]);
        $options = $this->courseView->getOptions('Факультатив', 'facultative_info.phtml', $data);
        $this->courseView->render($options);
    }

    /**
     * All statistics about courses
     *
     * @return void
     */
    public function dashboardAction(): void
    {
        if ($this->courseModel->isSigned() == 'family') {
            $data['courses'] = $this->courseModel->getDataForDashboard();
            $data['sumRevenue'] = $this->courseModel->countSumRevenue();
            $options = $this->courseView->getOptions('Дашборд', 'dashboard.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get students list on a course
     *
     * @param array $param
     * @return void
     */
    public function studentsOnCourseAction(array $param): void
    {
        if ($this->courseModel->isSigned() == 'family') {
            $data = $this->courseModel->getStudentsOnCourse($param[0]);
            $options = $this->courseView->getOptions('Студенты', 'dashboard_students.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get facultatives list on a course
     *
     * @param array $param
     * @return void
     */
    public function facultativesOnCourseAction(array $param): void
    {
        if ($this->courseModel->isSigned() == 'family') {
            $data = $this->courseModel->facultativesOnCourse($param[0]);
            $options = $this->courseView->getOptions('Факультативы', 'dashboard_facultatives.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get students list on a facultative
     *
     * @param array $param
     * @return void
     */
    public function studentsOnFacultativeAction(array $param): void
    {
        if ($this->courseModel->isSigned() == 'family') {
            $data = $this->courseModel->getStudentsOnFacultative($param[0]);
            $options = $this->courseView->getOptions('Факультативы', 'dashboard_students.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Register a student on a facultative
     *
     * @return void
     */
    public function registerToFacultativeAction(): void
    {
        if ($this->postData->isPost()) {
            if ($this->courseModel->isSigned() == 'students') {
                $this->courseModel->registerToFacultative($this->postData->getData());
                $this->location('/course/list');
            }
        } else {
            $this->homeLocation();
        }
    }
}
