CREATE DATABASE family;

USE family;

CREATE TABLE `users` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`login` VARCHAR(30) NOT NULL,
	`name` VARCHAR(255) NOT NULL,
	`family_member` VARCHAR(50) NOT NULL,
	`age` TINYINT NOT NULL,
	`address` VARCHAR(100) NOT NULL,
	`approve_status` TINYINT NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`image` VARCHAR(255),
	PRIMARY KEY (`id`)
);

CREATE TABLE `tasks` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`time_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`created_by` INT NOT NULL,
	`executor`  INT NOT NULL,
	`task` TEXT NOT NULL,
	`status` TINYINT NOT NULL,
	`time_start` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`time_end` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`comment` TINYTEXT NOT NULL,
	`approved_by` INT NOT NULL,
	`image` VARCHAR(100),
	PRIMARY KEY (`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
    FOREIGN KEY (`executor`) REFERENCES `users` (`id`),
    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)

);

INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`) VALUES
	('mother', 'Кузина Ольга', 'мама', 45, 'Рабочая 39', 1, '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');
	
INSERT INTO `users` (`login`, `name`, `family_member`, `age`, `address`, `approve_status`, `password`) VALUES
    ('father', 'Кузин Дмитрий', 'папа', 47, 'Рабочая 39', 1, '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');
