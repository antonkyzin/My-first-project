CREATE DATABASE family;

USE family;

CREATE TABLE `users`
(
    `id`             INT               NOT NULL AUTO_INCREMENT,
    `login`          VARCHAR(30)       NOT NULL,
    `name`           VARCHAR(255)      NOT NULL,
    `family_member`  VARCHAR(50)       NOT NULL,
    `age`            TINYINT           NOT NULL,
    `address`        VARCHAR(100)      NOT NULL,
    `approve_status` TINYINT DEFAULT 0 NOT NULL,
    `password`       VARCHAR(255)      NOT NULL,
    `image`          VARCHAR(255),
    PRIMARY KEY (`id`)
);

CREATE TABLE `tasks`
(
    `id`           INT     NOT NULL AUTO_INCREMENT,
    `time_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_by`   INT     NOT NULL,
    `executor`     INT     NOT NULL,
    `task`         TEXT    NOT NULL,
    `status`       TINYINT NOT NULL,
    `time_start`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `time_end`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `comment`      TINYTEXT,
    `approved_by`  INT,
    `image`        VARCHAR(100),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`executor`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`)
VALUES ('mother', 'Кузина Ольга', 'мама', 45, 'Рабочая 39', 1,
        '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');

INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`)
VALUES ('father', 'Кузин Дмитрий', 'папа', 47, 'Рабочая 39', 1,
        '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');

CREATE TABLE `courses`
(
    `id`          INT   NOT NULL AUTO_INCREMENT,
    `course_name` VARCHAR (50) NOT NULL,
    `description` TEXT,
    `price`       INT NOT NULL,
    `is_active`   TINYINT DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`)
);

INSERT INTO `courses` (`course_name`, `price`)
VALUES ('Starter', '850');
INSERT INTO `courses` (`course_name`, `price`)
VALUES ('Beginner', '860');
INSERT INTO `courses` (`course_name`, `price`)
VALUES ('Pre-intermediate', '870');
INSERT INTO `courses` (`course_name`, `price`)
VALUES ('Intermediate', '880');
INSERT INTO `courses` (`course_name`, `price`)
VALUES ('Upper-intermediate', '900');
INSERT INTO `courses` (`course_name`, `price`)
VALUES ('Advanced', '910');

CREATE TABLE `groups`
(
    `id`         INT         NOT NULL AUTO_INCREMENT,
    `group_name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `schedule` VARCHAR (255),
    `image` VARCHAR(100),
    PRIMARY KEY (`id`)
);

CREATE TABLE `students`
(
    `id`        INT          NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(255) NOT NULL,
    `e-mail`    VARCHAR(50)  NOT NULL,
    `birthdate` DATE         NOT NULL,
    `group`     INT,
    `course`    INT,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`course`) REFERENCES `courses` (`id`),
    FOREIGN KEY (`group`) REFERENCES `groups` (`id`)
);
