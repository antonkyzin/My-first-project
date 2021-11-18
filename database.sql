CREATE DATABASE family;

USE family;

CREATE TABLE `users`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `login` VARCHAR(30) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `family_member` VARCHAR(10),
    `age` TINYINT NOT NULL,
    `address` VARCHAR(100) NOT NULL,
    `approve_status` TINYINT DEFAULT 0 NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `image` VARCHAR(255),
    PRIMARY KEY (`id`)
);

CREATE TABLE `tasks`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `time_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_by` INT NOT NULL,
    `executor` INT NOT NULL,
    `task` TEXT NOT NULL,
    `status` TINYINT NOT NULL,
    `time_start` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `time_end` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `comment` TINYTEXT,
    `approved_by` INT,
    `image` VARCHAR(100),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`executor`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`, `image`)
VALUES ('mother', 'Кузина Ольга', 'head', 45, 'Рабочая 39', 1,
        '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK', 'users/standart_avatar.jpg');

INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`, `image`)
VALUES ('father', 'Кузин Дмитрий', 'Admin', 47, 'Рабочая 39', 1,
        '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK', 'users/standart_avatar.jpg');

CREATE TABLE `courses`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `course_name` VARCHAR (50) NOT NULL,
    `description` TEXT,
    `price` INT NOT NULL,
    `status` TINYINT DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`)
);

INSERT INTO `courses` (`course_name`, `description`, `price`)
VALUES ('Starter','Курс стартер предназначен для людей без знания английского', '850');
INSERT INTO `courses` (`course_name`, `description`, `price`)
VALUES ('Beginner', 'Курс бегинер предназначен для людей которые "когда-то что-то знали но уже забыли"', '860');
INSERT INTO `courses` (`course_name`, `description`, `price`)
VALUES ('Pre-intermediate', 'Курс предназначен для людей с неплохим пониманием языка', '870');
INSERT INTO `courses` (`course_name`, `description`, `price`)
VALUES ('Intermediate', 'Курс предназначен для людей с хорошим пониманием языка', '880');
INSERT INTO `courses` (`course_name`, `description`, `price`)
VALUES ('Upper-intermediate', 'Курс предназначен для людей говорящих на английском', '900');
INSERT INTO `courses` (`course_name`, `description`, `price`)
VALUES ('Advanced', 'Курс предназначен для людей уверенно и свободно говорящих на английском', '910');

CREATE TABLE `groups`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `group_name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `schedule` VARCHAR (255),
    `course` INT NOT NULL,
    `image` VARCHAR(100),
    `status` TINYINT DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE RESTRICT
);

CREATE TABLE `students`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `login` VARCHAR(30) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `e_mail` VARCHAR(255) NOT NULL,
    `birthdate` DATE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `image` VARCHAR(255),
    PRIMARY KEY (`id`)
);

CREATE TABLE `course_claim`
(
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    PRIMARY KEY (`student_id`, `course_id`),
    INDEX `student_id` (`student_id`),
    FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
);

CREATE TABLE `groups_students`
(
    `group_id` INT NOT NULL,
    `student_id` INT NOT NULL,
    PRIMARY KEY (`group_id`, `student_id`),
    INDEX `group_id` (`group_id`),
    FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE RESTRICT
);

CREATE TABLE `facultative`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR (255) NOT NULL,
    `description` TEXT,
    `price` INT NOT NULL,
    `course` INT NOT NULL,
    `status` TINYINT DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE
);

CREATE TABLE `facultative_claim`
(
    `student_id` INT NOT NULL,
    `facultative_id` INT NOT NULL,
    `lessons_number` INT NOT NULL,
    PRIMARY KEY (`student_id`, `facultative_id`)
);

CREATE TABLE `facultative_students`
(
    `student_id` INT NOT NULL,
    `facultative_id` INT NOT NULL,
    `lessons_number` INT NOT NULL,
    FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`facultative_id`) REFERENCES `facultative` (`id`) ON DELETE CASCADE
);

-- insert into `students` (login, name, e_mail, birthdate, password)
-- values ('student1', 'Rudenko Dima', 'qwerty@qw.wed', '1994-10-02', '$2y$10$E9y3ityeuZ3XxfV1iHsKAeM8.Q.Pb3etusg/x9crXBO1ekQ0GM3r2');
-- insert into `students` (login, name, e_mail, birthdate, password)
-- values ('student2', 'Dima lemih', 'qwerty@qw.wed', '1994-10-02', '$2y$10$E9y3ityeuZ3XxfV1iHsKAeM8.Q.Pb3etusg/x9crXBO1ekQ0GM3r2');
-- insert into `students` (login, name, e_mail, birthdate, password)
-- values ('student3', 'Anton kuz', 'qwerty@qw.wed', '1994-10-02', '$2y$10$E9y3ityeuZ3XxfV1iHsKAeM8.Q.Pb3etusg/x9crXBO1ekQ0GM3r2');
-- insert into `students` (login, name, e_mail, birthdate, password)
-- values ('student4', 'Petrusha Ivan', 'qwerty@qw.wed', '1994-10-02', '$2y$10$E9y3ityeuZ3XxfV1iHsKAeM8.Q.Pb3etusg/x9crXBO1ekQ0GM3r2');
-- insert into `students` (login, name, e_mail, birthdate, password)
-- values ('student5', 'Korol Ilona', 'qwerty@qw.wed', '1994-10-02', '$2y$10$E9y3ityeuZ3XxfV1iHsKAeM8.Q.Pb3etusg/x9crXBO1ekQ0GM3r2');
--
-- INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`)
-- VALUES ('family1', 'Vasilchenko Marina', 'children', 25, 'Рабочая 39', 0,
--         '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');
-- INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`)
-- VALUES ('family3', 'Kuzin Ruslan', 'children', 25, 'Рабочая 39', 0,
--         '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');
-- INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`)
-- VALUES ('family2', 'Vasilchenko Alex', 'relatives', 25, 'Рабочая 39', 0,
--         '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');
-- INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`)
-- VALUES ('family4', 'Azov Max', 'relatives', 25, 'Рабочая 39', 0,
--         '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');
--
--     insert into `tasks` (created_by, executor, task, status) VALUES (1, 4, 'Купить молоко', 3);
-- insert into `tasks` (created_by, executor, task, status) VALUES (2, 3, 'Купить батон и масло', 3);
-- insert into `tasks` (created_by, executor, task, status) VALUES (1, 4, 'Помыть посуду', 3);
--
-- insert into    `groups` (`group_name`, `description`, `schedule`, `course` )
-- VALUES ('group 1', 'Утренняя группа', 'вт, чт, 10:00', '1');
-- insert into    `groups` (`group_name`, `description`, `schedule`, `course` )
-- VALUES ('group 2', 'Группа Аппер', 'вт, чт, 20:00', '6');
-- insert into    `groups` (`group_name`, `description`, `schedule`, `course` )
-- VALUES ('group 3', 'Утренняя группа', 'вт, чт, 14:00', '4');
-- insert into    `groups` (`group_name`, `description`, `schedule`, `course` )
-- VALUES ('group 4', 'Вечерняя группа', 'пн, сб, 10:00 43 34', '5');
-- insert into    `groups` (`group_name`, `description`, `schedule`, `course` )
-- VALUES ('group 5', 'Группа дошкольники', 'пн, ср, 09:00', '1');
--
-- INSERT INTO `facultative_students` (student_id, facultative_id, lessons_number) VALUES ('1','1','1');
-- INSERT INTO `facultative_students` (student_id, facultative_id, lessons_number) VALUES ('2','2','2');
-- INSERT INTO `facultative_students` (student_id, facultative_id, lessons_number) VALUES ('3','3','1');
-- INSERT INTO `facultative_students` (student_id, facultative_id, lessons_number) VALUES ('4','4','3');
-- INSERT INTO `facultative_students` (student_id, facultative_id, lessons_number) VALUES ('3','6','1');
-- INSERT INTO `facultative_students` (student_id, facultative_id, lessons_number) VALUES ('5','4','4');
-- INSERT INTO `facultative_students` (student_id, facultative_id, lessons_number) VALUES ('1','1','1');
