<?php
declare(strict_types=1);

namespace Models;

/**
 * @package Models
 */
class StudentModel extends DataModel
{
    /**
     * Get groups where the student is
     *
     * @return array
     */
    public function getGroups(): array
    {
        $field = ['g.group_id', 'g1.course'];
        $whereCondition = 'g.student_id = ' . $this->sessionData->getUser()['id'] . ' AND g1.status = 1';
        $joinCondition = [
            ['g.group_id' => 'g1.id']
        ];
        $joinTable = ['groups'];
        return $this->selectJoinData('groups_students', $joinTable, $field, $joinCondition, $whereCondition);
    }

    /**
     * Get groups the student wait for confirmation
     *
     * @return array
     */
    public function getcourseClaim(): array
    {
        $field = ['course_id'];
        $condition = '`student_id` = ' . $this->sessionData->getUser()['id'];
        return $this->selectData('course_claim', $field, $condition);
    }

    /**
     * Set session data for student
     *
     * @param array $groups
     * @param array $courseClaim
     * @return void
     */
    public function setSessionData(array $groups, array $courseClaim): void
    {
        $this->sessionData->setUserData('groups', $groups ?? null);
        $this->sessionData->setUserData('courseClaim', $courseClaim ?? null);
    }

    /**
     * Get all students list
     *
     * @return array|false|int|mixed
     */
    public function allStudents(): array
    {
        $field = ['id', 'name', 'e_mail', 'birthdate', 'image'];
        return $this->selectData('students', $field);
    }
}
