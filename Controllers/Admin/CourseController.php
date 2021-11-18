<?php
declare(strict_types=1);

namespace Controllers\Admin;

use \Controllers\BaseController;
use Models\CourseModel;
use View\CourseView;

/**
 * @package Controllers\Admin
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
     * Get groups list
     * @return void
     */
    public function groupsListAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->getGroupsList();
            $options = ['title' => 'Список Групп',
                'content' => 'admin/groups.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form by $param for action on a course
     * @param $param
     * @return void
     */
    public function editCoursesAction($param): void
    {
        if ($this->courseModel->isAccess()) {
            $options = ['title' => 'Редактировать курсы'];
            if (!isset($param)) {
                $options['content'] = 'admin/edit_courses.phtml';
            } else {
                switch ($param[0]) {
                    case 'deactivate' :
                        $data = $this->courseModel->getActiveCourses();
                        $options['content'] = 'admin/deactivate_course.phtml';
                        $options['data'] = $data;
                        break;
                    case 'activate' :
                        $data = $this->courseModel->getInactiveCourses();
                        $options['content'] = 'admin/activate_course.phtml';
                        $options['data'] = $data;
                        break;
                    case 'description' :
                        $data = $this->courseModel->getActiveCourses();
                        $options['content'] = 'admin/change_course_description.phtml';
                        $options['data'] = $data;
                        break;
                    case 'price' :
                        $data = $this->courseModel->getActiveCourses();
                        $options['content'] = 'admin/change_course_price.phtml';
                        $options['data'] = $data;
                        break;
                    default :
                        $options['content'] = 'admin/edit_courses.phtml';
                }
            }
            $this->courseView->render($options);
        }
    }

    /**
     * Set status 'inactive' for a course
     * @return void
     */
    public function deactivateAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $this->courseModel->deactivateCourse($_POST);
                $this->location('/admin/course/editCourses');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Set status 'active' for a course
     * @return void
     */
    public function activateAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $this->courseModel->activateCourse($_POST);
                $this->location('/admin/course/editCourses');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Change description for a course
     * @return void
     */
    public function changeDescriptionAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $this->courseModel->changeDescription($_POST);
                $this->location('/admin/course/editCourses');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Change price for a course
     * @return void
     */
    public function changePriceAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $this->courseModel->changePrice($_POST);
                $this->location('admin/course/editCourses');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form for creating new group
     * @return void
     */
    public function createGroupFormAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->getActiveCourses();
            $options = ['title' => 'Создать группу',
                'content' => 'admin/create_group.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Create new group
     * @return void
     */
    public function createGroupAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $result = $this->courseModel->createGroup($_POST);
                if ($result) {
                    $this->location('/admin/course/groupsList');
                }
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form for closing a group
     * @return void
     */
    public function closeGroupFormAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->getGroupsList();
            $options = ['title' => 'Закрыть группу',
                'content' => 'admin/close_group.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Close a group
     * @return void
     */
    public function closeGroupAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $result = $this->courseModel->closeGroup($_POST);
                if ($result) {
                    $this->location('/admin/course/groupsList');
                }
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get the group members list
     * @param array $param
     * @return void
     */
    public function groupMemberAction(array $param): void
    {
        if ($this->courseModel->isAccess()) {
            $data['members'] = $this->courseModel->groupMemberList($param[0]);
            $data['group'] = $param[0];
            $data['course'] = $param[1];
            $data['students'] = $this->courseModel->studentsList();
            $options = ['title' => 'Студенты группы',
                'content' => 'admin/group_member.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Add new student in a group
     * @return void
     */
    public function addStudentInGroupAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $this->courseModel->addStudentInGroup($_POST);
                $this->location('/admin/course/groupMember/' . $_POST['group_id'] . '/' . $_POST['course_id']);
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get request list to join in a group
     * @return void
     */
    public function courseClaimAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->courseClaim();
            $options = ['title' => 'Заявки',
                'content' => 'admin/course_claim.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form creating new group
     * @param array $param
     * @return void
     */
    public function addInGroupFormAction(array $param): void
    {
        if ($this->courseModel->isAccess()) {
            $data['student'] = $param[0];
            $data['course'] = $param[1];
            $condition = 'course = ' . $param[1];
            $data['groups'] = $this->courseModel->getGroupsList($condition);
            $options = ['title' => 'Добавить в группу',
                'content' => 'admin/add_in_group.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete data from 'groups' table
     * @return void
     */
    public function deleteFromGroupAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $this->courseModel->deleteFromGroup($_POST);
                $this->location($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete data from 'wait list' table
     * @return void
     */
    public function deleteFromCourseClaimAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $this->courseModel->deleteFromCourseClaim($_POST);
            }
            $this->location('/admin/course/courseClaim');
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render data about all facultative and render form for adding new facultative
     * @return void
     */
    public function facultativeAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data['courses'] = $this->courseModel->getActiveCourses();
            $data['facultative'] = $this->courseModel->getFacultatives();
            $options = ['title' => 'Факультативы',
                'content' => 'admin/facultative.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Creat new facultative
     * @return void
     */
    public function createFacultativeAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->checkPost()) {
                $this->courseModel->createFacultative($_POST);
                $this->location('/admin/course/facultative');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get request list to join in a group
     * @return void
     */
    public function facultativeClaimAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->facultativeClaim();
            $options = ['title' => 'Заявки',
                'content' => 'admin/facultative_claim.phtml',
                'data' => $data];
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete data from facultative claim list
     * @return void
     */
    public function deleteFromFacultativeClaimAction(): void
    {
        if ($this->checkPost()) {
            if ($this->courseModel->isAccess()) {
                $this->courseModel->deleteFromFacultativeClaim($_POST);
                $this->location($_SERVER['HTTP_REFERER']);
            } else {
                $this->homeLocation();
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Confirm a student to get a facultative
     * @param array $param
     * @return void
     */
    public function confirmFacultativeClaimAction(array $param): void
    {
        if ($this->courseModel->isAccess()) {
            $this->courseModel->confirmFacultativeClaim($param);
            $this->location($_SERVER['HTTP_REFERER']);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Set status inactive facultative
     * @return void
     */
    public function deactivateFacultativeAction(): void
    {
        if ($this->checkPost()) {
            if ($this->courseModel->isAccess()) {
                $this->courseModel->deactivateFacultative($_POST);
                $this->location($_SERVER['HTTP_REFERER']);
            } else {
                $this->homeLocation();
            }
        } else {
            $this->homeLocation();
        }
    }
}
