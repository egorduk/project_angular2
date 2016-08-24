-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.6.22-log - MySQL Community Server (GPL)
-- ОС Сервера:                   Win32
-- HeidiSQL Версия:              9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры базы данных project_angular2
CREATE DATABASE IF NOT EXISTS `project_angular2` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `project_angular2`;


-- Дамп структуры для таблица project_angular2.friend
CREATE TABLE IF NOT EXISTS `friend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `friend_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_friend_user_id` (`user_id`),
  CONSTRAINT `FK_friend_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.friend: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `friend` DISABLE KEYS */;
INSERT INTO `friend` (`id`, `user_id`, `friend_id`) VALUES
	(60, 6, 5),
	(68, 6, 7);
/*!40000 ALTER TABLE `friend` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.gallery_has_picture
CREATE TABLE IF NOT EXISTS `gallery_has_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_id` int(11) NOT NULL,
  `picture_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_gallery_has_picture_picture_gallery` (`gallery_id`),
  KEY `FK_gallery_has_picture_picture` (`picture_id`),
  KEY `FK_gallery_has_picture_user_id` (`user_id`),
  CONSTRAINT `FK_gallery_has_picture_picture` FOREIGN KEY (`picture_id`) REFERENCES `picture` (`id`),
  CONSTRAINT `FK_gallery_has_picture_picture_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `picture_gallery` (`id`),
  CONSTRAINT `FK_gallery_has_picture_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.gallery_has_picture: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `gallery_has_picture` DISABLE KEYS */;
INSERT INTO `gallery_has_picture` (`id`, `gallery_id`, `picture_id`, `user_id`) VALUES
	(3, 37, 5, 6),
	(13, 39, NULL, 6),
	(18, 40, NULL, 6),
	(20, 41, NULL, 6),
	(21, 40, 4, 6),
	(42, 48, NULL, 6),
	(44, 49, NULL, 6),
	(46, 50, NULL, 6),
	(47, 51, 6, 6),
	(48, 51, NULL, 6),
	(52, 52, NULL, 6);
/*!40000 ALTER TABLE `gallery_has_picture` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.picture
CREATE TABLE IF NOT EXISTS `picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `date_upload` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture: ~6 rows (приблизительно)
/*!40000 ALTER TABLE `picture` DISABLE KEYS */;
INSERT INTO `picture` (`id`, `user_id`, `name`, `date_upload`, `filename`) VALUES
	(1, 6, 'pic1', '2016-07-21 13:07:21', '6.jpg'),
	(2, 5, 'pic2', '2016-07-21 13:07:30', '1.jpg'),
	(3, 5, 'pic3', '2016-07-21 13:07:39', '3.jpg'),
	(4, 5, 'pic4', '2016-08-10 16:33:58', '2.jpg'),
	(5, 7, 'pic5', '2016-08-15 13:04:09', '4.jpg'),
	(6, 7, 'pic6', '2016-08-15 13:04:19', '5.jpg');
/*!40000 ALTER TABLE `picture` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.picture_comment
CREATE TABLE IF NOT EXISTS `picture_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_comment` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `picture_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_picture_comment_picture_id` (`picture_id`),
  CONSTRAINT `FK_picture_comment_picture_id` FOREIGN KEY (`picture_id`) REFERENCES `picture` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture_comment: ~6 rows (приблизительно)
/*!40000 ALTER TABLE `picture_comment` DISABLE KEYS */;
INSERT INTO `picture_comment` (`id`, `date_comment`, `picture_id`, `user_id`, `comment`) VALUES
	(1, '2016-08-13 17:08:48', 6, 6, 'comment1'),
	(2, '2016-07-16 17:08:48', 6, 7, 'comment2'),
	(3, '2016-08-16 17:08:48', 6, 8, 'comment3'),
	(4, '2016-08-17 13:18:06', 5, 8, 'Lorem Ipsum is simply dummy text of the printing a'),
	(19, '2016-08-18 18:05:28', 6, 6, '456'),
	(20, '2016-08-18 18:05:38', 4, 6, '789');
/*!40000 ALTER TABLE `picture_comment` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.picture_gallery
CREATE TABLE IF NOT EXISTS `picture_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture_gallery: ~3 rows (приблизительно)
/*!40000 ALTER TABLE `picture_gallery` DISABLE KEYS */;
INSERT INTO `picture_gallery` (`id`, `name`) VALUES
	(37, 'gal1'),
	(38, 'gal2'),
	(39, 'gal3'),
	(40, 'gal4'),
	(41, 'gal5'),
	(42, 'gal6'),
	(48, 'gal6'),
	(49, 'gg'),
	(50, 'gal7'),
	(51, 'gal8'),
	(52, 'gal9');
/*!40000 ALTER TABLE `picture_gallery` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.picture_like
CREATE TABLE IF NOT EXISTS `picture_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `picture_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_picture_like_picture_id` (`picture_id`),
  KEY `FK_picture_like_user_id` (`user_id`),
  CONSTRAINT `FK_picture_like_picture_id` FOREIGN KEY (`picture_id`) REFERENCES `picture` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_picture_like_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture_like: ~7 rows (приблизительно)
/*!40000 ALTER TABLE `picture_like` DISABLE KEYS */;
INSERT INTO `picture_like` (`id`, `user_id`, `picture_id`) VALUES
	(2, 7, 6),
	(3, 7, 5),
	(8, 7, 2),
	(9, 8, 2),
	(20, 6, 4),
	(33, 6, 3),
	(36, 6, 2),
	(40, 6, 5);
/*!40000 ALTER TABLE `picture_like` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.picture_tag
CREATE TABLE IF NOT EXISTS `picture_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `picture_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_picture_tag_picture` (`picture_id`),
  KEY `FK_picture_tag_tag` (`tag_id`),
  CONSTRAINT `FK_picture_tag_picture` FOREIGN KEY (`picture_id`) REFERENCES `picture` (`id`),
  CONSTRAINT `FK_picture_tag_tag` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture_tag: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `picture_tag` DISABLE KEYS */;
INSERT INTO `picture_tag` (`id`, `picture_id`, `tag_id`) VALUES
	(1, 6, 1),
	(2, 6, 2),
	(3, 6, 3),
	(8, 4, 4),
	(10, 4, 5);
/*!40000 ALTER TABLE `picture_tag` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.tag
CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.tag: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` (`id`, `name`) VALUES
	(1, 'woman'),
	(2, 'ice-cream'),
	(3, 'girl'),
	(4, 'child'),
	(5, 'water');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `login` varchar(50) NOT NULL,
  `avatar` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.user: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `email`, `password`, `login`, `avatar`) VALUES
	(5, 'e@tut.by', 'c4ca4238a0b923820dcc509a6f75849b', 'e', 'av1.jpg'),
	(6, 'e1@tut.by', 'c4ca4238a0b923820dcc509a6f75849b', 'e1', 'av2.jpg'),
	(7, 'e2@tut.by', '', 'e2', 'av3.jpg'),
	(8, 'e3@tut.by', '', 'e3', 'av4.jpg');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
