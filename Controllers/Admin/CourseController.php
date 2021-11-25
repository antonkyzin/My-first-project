<?php
declare(strict_types=1);

namespace Controllers\Admin;

use \Controllers\BaseController;
use Interfaces\IDataManagement;
use Models\CourseModel;
use Models\DataRegistry;
use View\CourseView;

/**
 * @package Controllers\Admin
 */
class CourseController extends BaseController
{
    private CourseModel $courseModel;

    private CourseView $courseView;

    /**
     * Object for access to server data
     */
    private IDataManagement $serverData;

    /**
     * Object for access to POST data
     */
    private IDataManagement $postData;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->courseView = new CourseView();
        $this->serverData = DataRegistry::getInstance()->get('server');
        $this->postData = DataRegistry::getInstance()->get('post');
    }

    /**
     * Get groups list
     *
     * @return void
     */
    public function groupsListAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->getGroupsList();
            $options = $this->courseView->getOptions('Список Групп', 'admin/groups.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form by $param for action on a course
     *
     * @param $param
     * @return void
     */
    public function editCoursesAction($param): void
    {
        if ($this->courseModel->isAccess()) {
            $title = 'Редактировать курсы';
            if (!isset($param)) {
                $content = 'admin/edit_courses.phtml';
            } else {
                switch ($param[0]) {
                    case 'deactivate' :
                        $data = $this->courseModel->getActiveCourses();
                        $content = 'admin/deactivate_course.phtml';
                        break;
                    case 'activate' :
                        $data = $this->courseModel->getInactiveCourses();
                        $content = 'admin/activate_course.phtml';
                        break;
                    case 'description' :
                        $data = $this->courseModel->getActiveCourses();
                        $content = 'admin/change_course_description.phtml';
                        break;
                    case 'price' :
                        $data = $this->courseModel->getActiveCourses();
                        $content = 'admin/change_course_price.phtml';
                        break;
                    default :
                        $content = 'admin/edit_courses.phtml';
                }
            }
            $options = $this->courseView->getOptions($title, $content, $data);
            $this->courseView->render($options);
        }
    }

    /**
     * Set status 'inactive' for a course
     *
     * @return void
     */
    public function deactivateAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $this->courseModel->deactivateCourse($this->postData->getData());
                $this->location('/admin/course/editCourses');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Set status 'active' for a course
     *
     * @return void
     */
    public function activateAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $this->courseModel->activateCourse($this->postData->getData());
                $this->location('/admin/course/editCourses');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Change description for a course
     *
     * @return void
     */
    public function changeDescriptionAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $this->courseModel->changeDescription($this->postData->getData());
                $this->location('/admin/course/editCourses');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Change price for a course
     *
     * @return void
     */
    public function changePriceAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $this->courseModel->changePrice($this->postData->getData());
                $this->location('admin/course/editCourses');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form for creating new group
     *
     * @return void
     */
    public function createGroupFormAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->getActiveCourses();
            $options = $this->courseView->getOptions('Создать группу', 'admin/create_group.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Create new group
     *
     * @return void
     */
    public function createGroupAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $result = $this->courseModel->createGroup($this->postData->getData());
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
     *
     * @return void
     */
    public function closeGroupFormAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->getGroupsList();
            $options = $this->courseView->getOptions('Закрыть группу', 'admin/close_group.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Close a group
     *
     * @return void
     */
    public function closeGroupAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $result = $this->courseModel->closeGroup($this->postData->getData());
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
     *
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
            $options = $this->courseView->getOptions('Студенты группы', 'admin/group_member.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Add new student in a group
     *
     * @return void
     */
    public function addStudentInGroupAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $this->courseModel->addStudentInGroup($this->postData->getData());
                $this->location('/admin/course/groupMember/' . $this->postData->getData()['group_id'] . '/' . $this->postData->getData()['course_id']);
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get request list to join in a group
     *
     * @return void
     */
    public function courseClaimAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->courseClaim();
            $options = $this->courseView->getOptions('Заявки', 'admin/course_claim.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form creating new group
     *
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
            $options = $this->courseView->getOptions('Добавить в группу', 'admin/add_in_group.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete data from 'groups' table
     *
     * @return void
     */
    public function deleteFromGroupAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $this->courseModel->deleteFromGroup($this->postData->getData());
                $this->location($this->serverData->getReferer());
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete data from 'wait list' table
     *
     * @return void
     */
    public function deleteFromCourseClaimAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $this->courseModel->deleteFromCourseClaim($this->postData->getData());
            }
            $this->location('/admin/course/courseClaim');
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render data about all facultative and render form for adding new facultative
     *
     * @return void
     */
    public function facultativeAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data['courses'] = $this->courseModel->getActiveCourses();
            $data['facultative'] = $this->courseModel->getFacultatives();
            $options = $this->courseView->getOptions('Факультативы', 'admin/facultative.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Creat new facultative
     *
     * @return void
     */
    public function createFacultativeAction(): void
    {
        if ($this->courseModel->isAccess()) {
            if ($this->postData->isPost()) {
                $this->courseModel->createFacultative($this->postData->getData());
                $this->location('/admin/course/facultative');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get request list to join in a group
     *
     * @return void
     */
    public function facultativeClaimAction(): void
    {
        if ($this->courseModel->isAccess()) {
            $data = $this->courseModel->facultativeClaim();
            $options = $this->courseView->getOptions('Заявки', 'admin/facultative_claim.phtml', $data);
            $this->courseView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete data from facultative claim list
     *
     * @return void
     */
    public function deleteFromFacultativeClaimAction(): void
    {
        if ($this->postData->isPost()) {
            if ($this->courseModel->isAccess()) {
                $this->courseModel->deleteFromFacultativeClaim($this->postData->getData());
                $this->location($this->serverData->getReferer());
            } else {
                $this->homeLocation();
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Confirm a student to get a facultative
     *
     * @param array $param
     * @return void
     */
    public function confirmFacultativeClaimAction(array $param): void
    {
        if ($this->courseModel->isAccess()) {
            $this->courseModel->confirmFacultativeClaim($param);
            $this->location($this->serverData->getReferer());
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Set status inactive facultative
     *
     * @return void
     */
    public function deactivateFacultativeAction(): void
    {
        if ($this->postData->isPost()) {
            if ($this->courseModel->isAccess()) {
                $this->courseModel->deactivateFacultative($this->postData->getData());
                $this->location($this->serverData->getReferer());
            } else {
                $this->homeLocation();
            }
        } else {
            $this->homeLocation();
        }
    }
}
