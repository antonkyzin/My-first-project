<?php
declare(strict_types=1);

namespace Models;

/**
 * @package Models
 */
class CourseModel extends DataModel
{
    const INACTIVE_COURSE = 0;
    const INACTIVE_GROUP = 0;
    const ACTIVE_COURSE = 1;

    /**
     * Get active courses list
     * @param string|null $sortBy
     * @return array
     */
    public function getActiveCourses(string $sortBy = null): array
    {
        $whereCondition = 'status = ' . self::ACTIVE_COURSE;
        return $this->selectCourses($whereCondition, $sortBy);
    }

    /**
     * Get groups list
     * @param string|null $whereCondition
     * @return array
     */
    public function getGroupsList(string $whereCondition = null): array
    {
        $field = ['g.id', 'group_name', 'g.description', 'schedule', 'c1.course_name AS course', 'c1.id AS course_id', 'image', 'g.status'];
        $joinCondition = [
            ['course' => 'c1.id']
        ];
        $tables = ['courses'];
        $result = $this->selectJoinData('groups', $tables, $field, $joinCondition, $whereCondition);
        for ($i = 0; $i < count($result); $i++) {
            $whereCondition = 'group_id = ' . $result[$i]['id'];
            $result[$i]['students_count'] = $this->countData('groups_students', $whereCondition)[0]['COUNT(*)'];
        }
        return $result;
    }

    /**
     * Get inactive courses list
     * @param string|null $sortBy
     * @return array
     */
    public function getInactiveCourses(string $sortBy = null): array
    {
        $whereCondition = 'status = ' . self::INACTIVE_COURSE;
        return $this->selectCourses($whereCondition, $sortBy);
    }

    /**
     * Set status inactive to a course
     * @param array $data
     * @return int
     */
    public function deactivateCourse(array $data): int
    {
        $id = implode(',', $data);
        $field = ['status' => self::INACTIVE_COURSE];
        $condition = 'id IN (' . $id . ')';
        $result = $this->updateData('courses', $field, $condition);
        if ($result) {
            $condition = '`course` IN (' . $id . ')';
            $this->updateData('facultative', $field, $condition);
        }
        return $result;
    }

    /**
     * Set status active to a course
     * @param array $data
     * @return int
     */
    public function activateCourse(array $data): int
    {
        $id = implode(',', $data);
        $field = ['status' => self::ACTIVE_COURSE];
        $condition = 'id IN (' . $id . ')';
        $result = $this->updateData('courses', $field, $condition);
        if ($result) {
            $condition = '`course` IN (' . $id . ')';
            $this->updateData('facultative', $field, $condition);
        }
        return $result;
    }

    /**
     * Change course description
     * @param array $data
     * @return int
     */
    public function changeDescription(array $data): int
    {
        $description = array_pop($data);
        $id = implode(',', $data);
        $field = ['description' => $description];
        $condition = '`id` IN (' . $id . ')';
        return $this->updateData('courses', $field, $condition);
    }

    /**
     * Change course price
     * @param array $data
     * @return int
     */
    public function changePrice(array $data): int
    {
        $description = array_pop($data);
        $id = implode(',', $data);
        $field = ['price' => $description];
        $condition = '`id` IN (' . $id . ')';
        return $this->updateData('courses', $field, $condition);
    }

    /**
     * Create new group
     * @param array $data
     * @return bool
     */
    public function createGroup(array $data): bool
    {
        if ($_FILES['image']["error"] == UPLOAD_ERR_OK) {
            $data['image'] = $this->moveUploadFile('groups');
        }
        return $this->insertData('groups', $data);
    }

    /**
     * Set status inactive to a group
     * @param array $data
     * @return int
     */
    public function closeGroup(array $data): int
    {
        $id = implode(',', $data);
        $this->deleteFile('groups', $id);
        $field = ['status' => self::INACTIVE_GROUP];
        $condition = 'id IN (' . $id . ')';
        return $this->updateData('groups', $field, $condition);
    }

    /**
     * Get group members list
     * @param string $id
     * @return array
     */
    public function groupMemberList(string $id): array
    {
        $field = ['s1.id', 's1.name'];
        $joinCondition = [
            ['student_id' => 's1.id']
        ];
        $whereCondition = 'group_id = ' . $id;
        $joinTables = ['students'];
        return $this->selectJoinData('groups_students', $joinTables, $field, $joinCondition, $whereCondition);
    }

    /**
     * Get all students list
     * @return array
     */
    public function studentsList(): array
    {
        $field = ['id', 'name'];
        return $this->selectData('students', $field);
    }

    /**
     * Add a student to a group
     * @param array $data
     * @return bool
     */
    public function addStudentInGroup(array $data): bool
    {
        $courseId = array_pop($data);
        $studentId = $data['student_id'];
        $result = $this->insertData('groups_students', $data);
        if ($result) {
            $this->deleteFromCourseClaim([$courseId => $studentId]);
        }
        return $result;
    }

    /**
     * Delete a student from course claim
     * @param array $data
     * @return void
     */
    public function deleteFromCourseClaim(array $data): void
    {
        foreach ($data as $courseId => $studentId) {
            $whereCondition = 'student_id = ' . $studentId . ' AND course_id = ' . $courseId;
            $this->deleteDataWithWhere('course_claim', $whereCondition);
        }
    }

    /**
     * Get courses claim list
     * @return array
     */
    public function courseClaim(): array
    {
        $field = ['s1.name', 's1.id AS student_id', 'c2.id AS course_id', 'c2.course_name'];
        $joinTables = ['students', 'courses'];
        $joinConditions = [
            ['student_id' => 's1.id'],
            ['course_id' => 'c2.id']
        ];
        return $this->selectJoinData('course_claim', $joinTables, $field, $joinConditions);
    }

    /**
     * Get information about a course
     * @param string $courseId
     * @return array
     */
    public function getCourseInfo(string $courseId): array
    {
        $condition = 'id = ' . $courseId;
        $result = $this->selectCourses($condition);
        $whereCondition = 'course = ' . $courseId;
        $result[0]['facultative_list'] = $this->getFacultatives($whereCondition);
        return $result[0];
    }

    /**
     * Add a student to course claim
     * @param string $studentId
     * @param string $courseId
     * @return bool
     */
    public function addToCourseClaim(string $studentId, string $courseId): bool
    {
        $condition = "id = $courseId AND co.status = " . self::ACTIVE_COURSE;
        $course = $this->selectCourses($condition);
        if ($course[0]) {
            $data = ['student_id' => $studentId,
                'course_id' => $courseId];
            $result = $this->insertData('course_claim', $data);
            if ($result) {
                $_SESSION['user']['courseClaim'][] = ['course_id' => $courseId];
                return true;
            }
        }
        return false;
    }

    /**
     * Get courses list with students number
     * @param string|null $whereCondition
     * @param string|null $orderBy
     * @return array
     */
    public function selectCourses(string $whereCondition = null, string $orderBy = null): array
    {
        $sql = "SELECT DISTINCT co.id, co.course_name, co.description, co.price,  
                (SELECT COUNT(gs.student_id) FROM `groups_students` gs LEFT JOIN `groups` g ON gs.group_id = g.id WHERE g.course = co.id) AS students,
                (SELECT COUNT(*) FROM `facultative` fa LEFT JOIN `courses` cou ON fa.course = cou.id WHERE fa.course = co.id AND fa.status = 1) AS facultative
                FROM `courses` co LEFT JOIN `groups` gr ON co.id = gr.course";
        if (isset($whereCondition)) {
            $sql .= '  WHERE co.' . $whereCondition;
        }
        if (isset($orderBy)) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        $result = $this->pdo->query($sql);
        return $result->fetchAll();
    }

    /**
     * Sort courses by params
     * @param string $sortBy
     * @param string $mode
     * @param bool $showAll
     * @return array
     */
    public function sortCourses(string $sortBy, string $mode, bool $showAll): array
    {
        switch ($sortBy) {
            case 'popularity' :
                $mode = 'students ' . $mode;
                $result = $this->getActiveCourses($mode);
                break;
            case 'price' :
                $mode = 'price ' . $mode;
                $result = $this->getActiveCourses($mode);
                break;
            case 'facultative' :
                $mode = 'facultative ' . $mode;
                $result = $this->getActiveCourses($mode);
                break;
        }
        if ($showAll) {
            $inactive = $this->getInactiveCourses($mode);
            $result = array_merge($result, $inactive);
        }
        return $result;
    }

    /**
     * Find a course by description or title
     * @param string $search
     * @return array
     */
    public function search(string $search): array
    {
        $where = "course_name LIKE '%$search%' OR co.description LIKE '%$search%'";
        return $this->selectCourses($where);
    }

    /**
     * Delete a student from group
     * @param array $data
     * @return void
     */
    public function deleteFromGroup(array $data): void
    {
        foreach ($data as $studentId => $groupId) {
            $whereCondition = "group_id = $groupId AND student_id = $studentId";
            $this->deleteDataWithWhere('groups_students', $whereCondition);
        }
    }

    /**
     * Get facultatives list
     * @param string|null $whereCondition
     * @return array
     */
    public function getFacultatives(string $whereCondition = null): array
    {
        $field = ['f.id', 'title', 'f.description', 'f.price', 'c1.course_name AS course', 'f.status'];
        $joinTable = ['courses'];
        $joinCondition = [
            ['course' => 'c1.id']
        ];
        return $this->selectJoinData('facultative', $joinTable, $field, $joinCondition, $whereCondition);
    }

    /**
     * Create new facultative
     * @param array $data
     * @return bool|int|mixed
     */
    public function createFacultative(array $data)
    {
        return $this->insertData('facultative', $data);
    }

    /**
     * Get information about facultative
     * @param string $id
     * @return array
     */
    public function getFacultativeInfo(string $id): array
    {
        $whereCondition = 'f.id = ' . $id;
        return $this->getFacultatives($whereCondition)[0];
    }

    /**
     * Get students list on a course
     * @param string $courseID
     * @return array
     */
    public function getStudentsOnCourse(string $courseID): array
    {
        $field = ['s1.id', 's1.name'];
        $joinTables = ['students', 'groups'];
        $joinCondition = [
            ['g.student_id' => 's1.id'],
            ['g.group_id' => 'g2.id']
        ];
        $whereCondition = 'g2.course = ' . $courseID . ' AND g2.status = 1';

        return $this->selectJoinData('groups_students', $joinTables, $field, $joinCondition, $whereCondition);
    }

    /**
     * Get courses list for dashboard with students number and sum revenue
     * @return array
     */
    public function getCoursesForDashboard(): array
    {
        $sql = 'SELECT cou.id, cou.course_name, cou.description, cou.status AS course_status, gr.status AS group_status, cou.price,
                COUNT(grst.student_id) AS count_students, 
                COUNT(grst.student_id) * cou.price AS SUMM
                FROM `courses` cou 
                LEFT JOIN `groups` gr ON cou.id = gr.course 
                LEFT JOIN `groups_students` grst ON gr.id = grst.group_id
                GROUP BY cou.id, gr.status
                ORDER BY `cou`.`id` ASC';
        $result = $this->pdo->query($sql);
        return $result->fetchAll();
    }

    /**
     * Get corrected data about courses for render on dashboard
     * @return array
     */
    public function getDataForDashboard(): array
    {
        $result = $this->getCoursesForDashboard();
        $data = [];
        foreach ($result as $item) {
            foreach ($item as $key => $value) {
                if ($key == 'group_status') {
                    continue;
                }
                if ($key == 'count_students') {
                    $data[$item['course_name']]['all_students'] = isset($data[$item['course_name']]['all_students']) ?
                        $data[$item['course_name']]['all_students'] + $value :
                        $value;
                    if (!$item['group_status']) {
                        $data[$item['course_name']][$key] = 0;
                        continue;
                    }
                } elseif ($key == 'SUMM') {
                    $data[$item['course_name']][$key] = isset($data[$item['course_name']][$key]) ?
                        $data[$item['course_name']][$key] + $value :
                        $value;
                    continue;
                }
                $data[$item['course_name']][$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Get facultatives list for dashboard with students number and sum revenue
     * @param string $courseID
     * @return array
     */
    public function facultativesOnCourse(string $courseID): array
    {
        $sql = "SELECT fa.id, fa.title, fa.price,
                COUNT(DISTINCT fastu.student_id) AS students_count,
                SUM(fastu.lessons_number) AS lessons_count,
                SUM(fastu.lessons_number) * fa.price AS SUMM,
                fa.status
                FROM `facultative` fa
                JOIN `facultative_students` fastu ON fa.id = fastu.facultative_id
                WHERE fa.course = $courseID
                GROUP BY fa.id, fa.status";
        $result = $this->pdo->query($sql);
        return $result->fetchAll();
    }

    /**
     * Get students list on a facultative
     * @param string $facultativeId
     * @return array
     */
    public function getStudentsOnFacultative(string $facultativeId): array
    {
        $sql = 'SELECT DISTINCT s.id, s.name
                FROM `students`s
                JOIN `facultative_students` f on f.student_id = s.id
                WHERE f.facultative_id = ' . $facultativeId;
        $result = $this->pdo->query($sql);
        return $result->fetchAll();
    }

    /**
     * Count all revenue from courses and facultatives
     * @return int
     */
    public function countSumRevenue(): int
    {
        $sql = 'SELECT COUNT(grst.student_id) * co.price AS course_summ
                FROM `courses` co
                LEFT JOIN `groups` gr ON co.id = gr.course 
                LEFT JOIN `groups_students` grst ON gr.id = grst.group_id
                GROUP BY co.id';
        $result = $this->pdo->query($sql);
        $coursesRevenue = $result->fetchAll();
        $sql = 'SELECT SUM(fastu.lessons_number) * fa.price AS SUMM
                FROM `facultative` fa
                JOIN `facultative_students` fastu ON fa.id = fastu.facultative_id
                GROUP BY fa.id';
        $result = $this->pdo->query($sql);
        $facultativesRevenue = $result->fetchAll();
        $allRevenue = 0;
        foreach (array_merge($coursesRevenue, $facultativesRevenue) as $item) {
            foreach ($item as $value) {
                $allRevenue += $value;
            }
        }
        return $allRevenue;
    }

    /**
     * Register student on facultative
     * @param array $data
     * @return bool
     */
    public function registerToFacultative(array $data): bool
    {
        $data['student_id'] = $_SESSION['user']['id'];
        return $this->insertData('facultative_claim', $data);
    }

    /**
     * Get students claim on facultatives
     * @return array
     */
    public function facultativeClaim(): array
    {
        $field = ['s1.name', 's1.id AS student_id', 'f2.title', 'f.facultative_id', 'f.lessons_number'];
        $joinTables = ['students', 'facultative'];
        $joinConditions = [
            ['student_id' => 's1.id'],
            ['facultative_id' => 'f2.id']
        ];
        return $this->selectJoinData('facultative_claim', $joinTables, $field, $joinConditions);
    }

    /**
     * Delete a student claim on facultative
     * @param $data
     * @return void
     */
    public function deleteFromFacultativeClaim($data): void
    {
        foreach ($data as $facultativeId => $studentId) {
            $whereCondition = 'student_id = ' . $studentId . ' AND facultative_id = ' . $facultativeId;
            $this->deleteDataWithWhere('facultative_claim', $whereCondition);
        }
    }

    /**
     * Register a student on facultative
     * @param $data
     * @return void
     */
    public function confirmFacultativeClaim($data): void
    {
        $studentId = $data[0];
        $facultativeId = $data[1];
        $numberLessons = $data[2];
        $field = ['student_id' => $studentId,
            'facultative_id' => $facultativeId,
            'lessons_number' => $numberLessons];
        $result = $this->insertData('facultative_students', $field);
        if ($result) {
            $this->deleteFromFacultativeClaim([$facultativeId => $studentId]);
        }
    }

    /**
     * Set status inactive to facultative
     * @param $data
     * @return int
     */
    public function deactivateFacultative($data): int
    {
        $id = implode(',', $data);
        $field = ['status' => self::INACTIVE_COURSE];
        $condition = 'id IN (' . $id . ')';
        return $this->updateData('facultative', $field, $condition);
    }
}
