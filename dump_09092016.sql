-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.6.15-log - MySQL Community Server (GPL)
-- ОС Сервера:                   Win32
-- HeidiSQL Версия:              8.1.0.4545
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица project_angular2.friend
CREATE TABLE IF NOT EXISTS `friend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `friend_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_friend_user_id` (`user_id`),
  CONSTRAINT `FK_friend_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.friend: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `friend` DISABLE KEYS */;
INSERT INTO `friend` (`id`, `user_id`, `friend_id`) VALUES
	(92, 19, 5);
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
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.gallery_has_picture: ~6 rows (приблизительно)
/*!40000 ALTER TABLE `gallery_has_picture` DISABLE KEYS */;
INSERT INTO `gallery_has_picture` (`id`, `gallery_id`, `picture_id`, `user_id`) VALUES
	(121, 79, NULL, 19),
	(123, 80, NULL, 19),
	(127, 81, NULL, 19),
	(128, 81, 9, 19),
	(129, 79, 9, 19),
	(130, 79, 8, 19);
/*!40000 ALTER TABLE `gallery_has_picture` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.picture
CREATE TABLE IF NOT EXISTS `picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `date_upload` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_show_host` bit(1) NOT NULL DEFAULT b'1',
  `resize_height` smallint(6) NOT NULL,
  `resize_width` smallint(6) NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture: ~18 rows (приблизительно)
/*!40000 ALTER TABLE `picture` DISABLE KEYS */;
INSERT INTO `picture` (`id`, `user_id`, `name`, `date_upload`, `is_show_host`, `resize_height`, `resize_width`, `filename`) VALUES
	(1, 7, 'pic1', '2016-08-31 02:41:32', b'1', 253, 450, '6.jpg'),
	(2, 5, 'pic2', '2016-07-21 13:07:30', b'1', 253, 450, '2.jpg'),
	(3, 5, 'pic3', '2016-07-21 13:07:39', b'1', 253, 450, '3.jpg'),
	(4, 7, 'pic4', '2016-08-31 02:41:39', b'1', 253, 450, '2.jpg'),
	(5, 7, 'pic5', '2016-08-15 13:04:09', b'1', 253, 450, '4.jpg'),
	(6, 7, 'pic6', '2016-08-15 13:04:19', b'1', 253, 450, '5.jpg'),
	(7, 7, 'pic7', '2016-08-31 02:41:35', b'1', 253, 450, '7.jpg'),
	(8, 5, 'pic12', '2016-08-31 03:09:23', b'1', 253, 450, '12.jpg'),
	(9, 5, 'pic13', '2016-08-31 03:09:26', b'1', 529, 450, '13.jpg'),
	(81, 6, 'test1', '2016-09-06 01:43:33', b'0', 281, 450, '413499006.jpg'),
	(82, 6, '421653357', '2016-09-04 20:36:31', b'0', 284, 450, '421653357.jpg'),
	(97, 6, '691596931', '2016-09-06 23:30:05', b'1', 281, 450, '691596931.jpg'),
	(100, 19, 'test1', '2016-09-09 05:20:54', b'0', 281, 450, 'test1.jpg'),
	(101, 19, 'test11', '2016-09-09 05:20:55', b'1', 281, 450, 'test2.jpg'),
	(102, 19, 'piy', '2016-09-09 05:38:30', b'1', 338, 450, 'piy.jpg'),
	(103, 19, 'piy1', '2016-09-09 05:39:27', b'1', 529, 450, 'piy1.jpg'),
	(104, 19, '123', '2016-09-09 10:29:56', b'0', 253, 450, '123.jpg'),
	(105, 19, 'sdfsd', '2016-09-09 10:30:34', b'0', 253, 450, 'sdfsd.jpg');
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture_comment: ~3 rows (приблизительно)
/*!40000 ALTER TABLE `picture_comment` DISABLE KEYS */;
INSERT INTO `picture_comment` (`id`, `date_comment`, `picture_id`, `user_id`, `comment`) VALUES
	(4, '2016-08-17 13:18:06', 5, 8, 'Lorem Ipsum is simply dummy text of the printing a'),
	(20, '2016-08-18 18:05:38', 4, 6, '789'),
	(22, '2016-09-09 10:46:31', 9, 19, 'test'),
	(25, '2016-09-09 10:53:30', 9, 19, 'testt');
/*!40000 ALTER TABLE `picture_comment` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.picture_gallery
CREATE TABLE IF NOT EXISTS `picture_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture_gallery: ~3 rows (приблизительно)
/*!40000 ALTER TABLE `picture_gallery` DISABLE KEYS */;
INSERT INTO `picture_gallery` (`id`, `name`) VALUES
	(79, 'g2'),
	(80, 'g3'),
	(81, 'g4');
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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture_like: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `picture_like` DISABLE KEYS */;
INSERT INTO `picture_like` (`id`, `user_id`, `picture_id`) VALUES
	(50, 19, 2);
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.picture_tag: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `picture_tag` DISABLE KEYS */;
INSERT INTO `picture_tag` (`id`, `picture_id`, `tag_id`) VALUES
	(29, 2, 4),
	(30, 2, 2),
	(31, 9, 5),
	(32, 8, 1),
	(33, 81, 7);
/*!40000 ALTER TABLE `picture_tag` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.tag
CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.tag: ~7 rows (приблизительно)
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` (`id`, `name`) VALUES
	(1, 'woman'),
	(2, 'ice-cream'),
	(3, 'girl'),
	(4, 'child'),
	(5, 'water'),
	(6, 'sport'),
	(7, 'weather');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;


-- Дамп структуры для таблица project_angular2.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `login` varchar(50) NOT NULL,
  `avatar` varchar(50) NOT NULL,
  `info` varchar(50) NOT NULL,
  `page_photo` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы project_angular2.user: ~9 rows (приблизительно)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `email`, `password`, `login`, `avatar`, `info`, `page_photo`) VALUES
	(5, 'e@tut.by', 'c4ca4238a0b923820dcc509a6f75849b', 'e', 'av1.jpg', '', ''),
	(6, 'e1@tut.by', 'c4ca4238a0b923820dcc509a6f75849b', 'e1', 'av2.jpg', 'info about e1', '2.jpg'),
	(7, 'e2@tut.by', '', 'e2', 'av3.jpg', '', ''),
	(8, 'e3@tut.by', '', 'e3', 'av4.jpg', '', ''),
	(9, 'email', 'd41d8cd98f00b204e9800998ecf8427e', '', '', '', ''),
	(13, 'email1', 'd41d8cd98f00b204e9800998ecf8427e', '', '', '', ''),
	(15, '12ed', 'a8f5f167f44f4964e6c998dee827110c', '', '', '', ''),
	(16, '1', 'c81e728d9d4c2f636f067f89cc14862c', '', '', '', ''),
	(19, '222@tut.by', 'c4ca4238a0b923820dcc509a6f75849b', 'e5', '', 'piyyy', '');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
