CREATE DATABASE family;

USE family;

CREATE TABLE `users` (
	`id` INT(10) NOT NULL AUTO_INCREMENT ,
	`name` VARCHAR(25) NOT NULL,
	`status` VARCHAR(50) NOT NULL,
	`age` INT(4) NOT NULL,
	`address` VARCHAR(100) NOT NULL,
	`approve_status` TINYINT (1) NOT NULL,
	`password` VARCHAR(100) NOT NULL,
	`image` VARCHAR(100),
	PRIMARY KEY (`id`)
);

CREATE TABLE `tasks` (
	`id` INT(10) NOT NULL AUTO_INCREMENT ,
	`time_created` INT(10) NOT NULL,
	`created_by` VARCHAR(25) NOT NULL,
	`executor`  VARCHAR(25) NOT NULL,
	`task` TEXT NOT NULL,
	`status` VARCHAR(15) NOT NULL,
	`time_start` INT(10) NOT NULL,
	`time_end` INT(10) NOT NULL,
	`comment` TINYTEXT NOT NULL,
	`approved_by` VARCHAR(10),
	`image` VARCHAR(100),
	PRIMARY KEY (`id`)
);

INSERT INTO `users` (`name`, `status`, `age`, `address`, `approve_status`, `password`) VALUES
	('Мама', 'main_admin', 45, 'Рабочая 39', 1, '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');
	
INSERT INTO `users` (`name`, `status`, `age`, `address`, `approve_status`, `password`) VALUES
	('Папа', 'admin', 46, 'Рабочая 39', 1, '$2y$10$01qVwoE5BrFzNBTTkhIESO6oXstSXYlzU7IxJdj2.3FyZAMnwZ1cK');
