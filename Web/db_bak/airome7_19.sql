-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2015 �?07 �?19 �?11:10
-- 服务器版本: 5.6.11
-- PHP 版本: 5.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `airome`
--
CREATE DATABASE IF NOT EXISTS `airome` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `airome`;

-- --------------------------------------------------------

--
-- 表的结构 `command`
--

CREATE TABLE IF NOT EXISTS `command` (
  `command_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `gateway_id` varchar(5) NOT NULL,
  `node_id` varchar(20) NOT NULL,
  `command_value` varchar(20) DEFAULT NULL,
  `command_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `command_status` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`command_id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `node_id` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `developer`
--

CREATE TABLE IF NOT EXISTS `developer` (
  `dev_id` varchar(20) NOT NULL,
  `dev_name` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`dev_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `feedback`
--

CREATE TABLE IF NOT EXISTS `feedback` (
  `feedback_id` int(10) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `content`, `user_id`, `addtime`) VALUES
(1, 'ss', '', '2015-07-19 02:45:17'),
(2, 'ss', 'test', '2015-07-19 02:45:50'),
(3, 'ss', 'test', '2015-07-19 02:46:33'),
(4, 'ss', 'test', '2015-07-19 03:09:30'),
(5, 'ss', 'test', '2015-07-19 03:10:25'),
(6, 'dad', 'test', '2015-07-19 03:10:37');

-- --------------------------------------------------------

--
-- 表的结构 `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL,
  `group_uid` varchar(20) NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `FK_group_uid` (`group_uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- 转存表中的数据 `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `group_uid`) VALUES
(0, 'gateways', 'admin'),
(30, '默认分组', 'test');

-- --------------------------------------------------------

--
-- 表的结构 `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `node_id` varchar(5) NOT NULL,
  `node_password` varchar(8) NOT NULL,
  `node_name` varchar(20) NOT NULL DEFAULT 'new node',
  `node_type` int(2) NOT NULL,
  `node_uid` varchar(20) NOT NULL,
  `node_group_id` bigint(20) DEFAULT NULL,
  `node_status` int(2) NOT NULL DEFAULT '0',
  `node_value` varchar(20) DEFAULT NULL,
  `gateway_id` varchar(5) DEFAULT NULL,
  `node_reg_date` timestamp NULL DEFAULT NULL,
  `dev_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`node_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `dev_id` (`dev_id`),
  KEY `FK_node_group_id` (`node_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `node`
--

INSERT INTO `node` (`node_id`, `node_password`, `node_name`, `node_type`, `node_uid`, `node_group_id`, `node_status`, `node_value`, `gateway_id`, `node_reg_date`, `dev_id`) VALUES
('EEFHO', 'E6NH3M2A', '我的小台灯', 1, '', 30, 1, '0', 'gatew', '2015-05-18 08:46:09', NULL),
('gatew', 'gatew123', 'test''s gateway', 0, '', 0, 1, NULL, 'gatew', '2015-05-16 14:21:48', NULL),
('ILPFC', 'GJ1H6IK5', '我的空调', 2, '', 30, 1, NULL, 'gatew', '2015-05-18 16:07:20', NULL),
('K3PI2', '96H80C02', 'new node', 1, '', NULL, 0, NULL, NULL, NULL, NULL),
('MLF62', 'PDHBG8M6', 'new node', 1, '', NULL, 0, NULL, NULL, NULL, NULL),
('O48B8', '1A24D4PH', 'new node', 1, '', NULL, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` varchar(20) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `user_reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gateway_id` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `FK_user_gateway_id` (`gateway_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `password`, `user_reg_date`, `gateway_id`) VALUES
('admin', 'admin', 'airome2015admin', '2015-05-15 12:21:21', NULL),
('test', 'a', '098f6bcd4621d373cade4e832627b4f6', '2015-05-16 14:21:48', 'gatew');

--
-- 限制导出的表
--

--
-- 限制表 `command`
--
ALTER TABLE `command`
  ADD CONSTRAINT `command_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `command_ibfk_2` FOREIGN KEY (`gateway_id`) REFERENCES `node` (`node_id`),
  ADD CONSTRAINT `command_ibfk_3` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`);

--
-- 限制表 `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `FK_group_uid` FOREIGN KEY (`group_uid`) REFERENCES `user` (`user_id`);

--
-- 限制表 `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `FK_node_group_id` FOREIGN KEY (`node_group_id`) REFERENCES `groups` (`group_id`),
  ADD CONSTRAINT `node_ibfk_2` FOREIGN KEY (`gateway_id`) REFERENCES `node` (`node_id`),
  ADD CONSTRAINT `node_ibfk_3` FOREIGN KEY (`dev_id`) REFERENCES `developer` (`dev_id`);

--
-- 限制表 `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_user_gateway_id` FOREIGN KEY (`gateway_id`) REFERENCES `node` (`node_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
