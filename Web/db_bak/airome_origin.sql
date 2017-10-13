-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-05-18 18:09:24
-- 服务器版本： 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `airome`
--

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
  `command_status` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `developer`
--

CREATE TABLE IF NOT EXISTS `developer` (
  `dev_id` varchar(20) NOT NULL,
  `dev_name` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
`group_id` bigint(20) NOT NULL,
  `group_name` varchar(20) NOT NULL,
  `group_uid` varchar(20) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

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
  `dev_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `node`
--

INSERT INTO `node` (`node_id`, `node_password`, `node_name`, `node_type`, `node_uid`, `node_group_id`, `node_status`, `node_value`, `gateway_id`, `node_reg_date`, `dev_id`) VALUES
('EEFHO', 'E6NH3M2A', '我的小台灯', 1, '', 30, 1, NULL, 'gatew', '2015-05-18 08:46:09', NULL),
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
  `gateway_id` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `password`, `user_reg_date`, `gateway_id`) VALUES
('admin', 'admin', 'airome2015admin', '2015-05-15 12:21:21', NULL),
('test', 'test', '098f6bcd4621d373cade4e832627b4f6', '2015-05-16 14:21:48', 'gatew');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `command`
--
ALTER TABLE `command`
 ADD PRIMARY KEY (`command_id`), ADD KEY `user_id` (`user_id`), ADD KEY `gateway_id` (`gateway_id`), ADD KEY `node_id` (`node_id`);

--
-- Indexes for table `developer`
--
ALTER TABLE `developer`
 ADD PRIMARY KEY (`dev_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
 ADD PRIMARY KEY (`group_id`), ADD KEY `FK_group_uid` (`group_uid`);

--
-- Indexes for table `node`
--
ALTER TABLE `node`
 ADD PRIMARY KEY (`node_id`), ADD KEY `gateway_id` (`gateway_id`), ADD KEY `dev_id` (`dev_id`), ADD KEY `FK_node_group_id` (`node_group_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`user_id`), ADD KEY `FK_user_gateway_id` (`gateway_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
MODIFY `group_id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=31;
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
