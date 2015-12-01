-- for MySQL

CREATE TABLE `blog_posts` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`title` VARCHAR(255) NOT NULL,
	`text` TEXT NOT NULL,
	`create_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modify_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`tags` TEXT NOT NULL,
	`published` BOOLEAN NOT NULL DEFAULT '1'
) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `blog_parameters` (
	`id` INT NOT NULL DEFAULT '1' PRIMARY KEY,
	`password_hash` TEXT NOT NULL,
	`posts_on_page` INT NOT NULL DEFAULT '10',
	`maximal_width_of_images` INT NOT NULL DEFAULT '640',
	`dropbox_access_token` VARCHAR(255) NOT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `blog_parameters` (`password_hash`)
VALUES
	('$2a$13$7RC2CWHDqafP4dvl7t5PCucccPVl7spVT4FiALXEaxWCnzCTskqAK');
