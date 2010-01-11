-- phpMyAdmin SQL Dump
-- version 3.2.4deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 11, 2010 at 01:15 PM
-- Server version: 5.1.41
-- PHP Version: 5.2.11-2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sif`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminlog`
--

CREATE TABLE IF NOT EXISTS `adminlog` (
  `who` varchar(20) NOT NULL,
  `what` varchar(80) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` varchar(255) DEFAULT NULL,
  `row` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`row`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Things Administators have done' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `adminqueue`
--

CREATE TABLE IF NOT EXISTS `adminqueue` (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(80) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`seq`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Things Administators need to do' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `configuration`
--

CREATE TABLE IF NOT EXISTS `configuration` (
  `key` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `listener`
--

CREATE TABLE IF NOT EXISTS `listener` (
  `id` varchar(10) NOT NULL COMMENT 'short name of listener',
  `long_name` text,
  `enabled` tinyint(1) DEFAULT '1',
  `default_service` varchar(10) NOT NULL,
  `auto_service` tinyint(4) NOT NULL,
  `role` varchar(8) DEFAULT 'OUTPUT',
  `pharos_index` int(11) DEFAULT NULL,
  `tab_index` int(11) DEFAULT NULL,
  `owner` varchar(64) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `tab_index` (`tab_index`),
  KEY `tab_index_2` (`tab_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `listener2device`
--

CREATE TABLE IF NOT EXISTS `listener2device` (
  `id` varchar(16) NOT NULL COMMENT 'the source or listener id',
  `idx` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `device` varchar(64) NOT NULL COMMENT 'reference to the physical device',
  `pcm` varchar(64) NOT NULL COMMENT 'alsa pcm on the physical device',
  `tab_index` int(11) NOT NULL,
  PRIMARY KEY (`id`,`idx`),
  KEY `device` (`device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `listener2device_tabs`
--

CREATE TABLE IF NOT EXISTS `listener2device_tabs` (
  `tab_index` int(11) NOT NULL AUTO_INCREMENT,
  `tab_text` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`tab_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `listener_active_schedule`
--

CREATE TABLE IF NOT EXISTS `listener_active_schedule` (
  `listener_event_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `listener` varchar(10) DEFAULT NULL,
  `service` varchar(10) DEFAULT NULL,
  `first_date` date DEFAULT NULL,
  `last_date` date DEFAULT NULL,
  `days` varchar(7) CHARACTER SET ascii DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `start_mode` varchar(1) CHARACTER SET ascii DEFAULT NULL,
  `name` text,
  `owner` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`listener_event_id`),
  KEY `listener` (`listener`),
  KEY `service` (`service`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `listener_event`
--

CREATE TABLE IF NOT EXISTS `listener_event` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_time` datetime NOT NULL,
  `event_id` bigint(20) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `time` (`event_time`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='event schedule' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `listener_events`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`sif`@`%` SQL SECURITY DEFINER VIEW `sif`.`listener_events` AS select `sif`.`listener_event`.`id` AS `id`,`sif`.`listener_event`.`event_time` AS `event_time`,`sif`.`listener_event`.`event_id` AS `event_id`,`sif`.`listener_event`.`updated` AS `updated`,`sif`.`listener_active_schedule`.`listener_event_id` AS `listener_event_id`,`sif`.`listener_active_schedule`.`listener` AS `listener`,`sif`.`listener_active_schedule`.`service` AS `service`,`sif`.`listener_active_schedule`.`first_date` AS `first_date`,`sif`.`listener_active_schedule`.`last_date` AS `last_date`,`sif`.`listener_active_schedule`.`days` AS `days`,`sif`.`listener_active_schedule`.`start_time` AS `start_time`,`sif`.`listener_active_schedule`.`duration` AS `duration`,`sif`.`listener_active_schedule`.`start_mode` AS `start_mode`,`sif`.`listener_active_schedule`.`name` AS `name`,`sif`.`listener_active_schedule`.`owner` AS `owner` from (`sif`.`listener_event` join `sif`.`listener_active_schedule` on((`sif`.`listener_event`.`event_id` = `sif`.`listener_active_schedule`.`listener_event_id`)));

-- --------------------------------------------------------

--
-- Table structure for table `listener_planning_schedule`
--

CREATE TABLE IF NOT EXISTS `listener_planning_schedule` (
  `listener_event_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `listener` varchar(10) DEFAULT NULL,
  `service` varchar(10) DEFAULT NULL,
  `first_date` date DEFAULT NULL,
  `last_date` date DEFAULT NULL,
  `days` varchar(7) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `start_mode` varchar(1) DEFAULT NULL,
  `name` text,
  `owner` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`listener_event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `listener_tabs`
--

CREATE TABLE IF NOT EXISTS `listener_tabs` (
  `tab_index` int(11) NOT NULL AUTO_INCREMENT,
  `tab_text` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`tab_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `material`
--

CREATE TABLE IF NOT EXISTS `material` (
  `material_id` varchar(20) NOT NULL,
  `duration` time NOT NULL,
  `delete_after` date NOT NULL,
  `title` varchar(256) NOT NULL,
  `file` varchar(256) NOT NULL,
  `material_type` varchar(20) NOT NULL,
  `owner` varchar(64) NOT NULL,
  `client_ref` varchar(512) NOT NULL,
  `tx_date` date NOT NULL,
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `physicaldevicecharacteristics`
--

CREATE TABLE IF NOT EXISTS `physicaldevicecharacteristics` (
  `type` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`type`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `physicaldevices`
--

CREATE TABLE IF NOT EXISTS `physicaldevices` (
  `id` varchar(16) NOT NULL COMMENT 'friendly name',
  `macaddress` varchar(17) NOT NULL,
  `location` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`macaddress`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `physicaldevicetypes`
--

CREATE TABLE IF NOT EXISTS `physicaldevicetypes` (
  `type` varchar(20) NOT NULL,
  `description` varchar(80) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Named device types';

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE IF NOT EXISTS `service` (
  `service` varchar(10) NOT NULL,
  `service_long_name` text,
  `enabled` tinyint(1) DEFAULT '1',
  `tab_index` int(11) DEFAULT NULL,
  `owner` varchar(64) DEFAULT NULL,
  `notes` text,
  `pharos_index` int(11) NOT NULL,
  `ipv4_group_address` varchar(15) NOT NULL COMMENT 'Multicast Group for all components of this service',
  PRIMARY KEY (`service`),
  UNIQUE KEY `service` (`service`),
  KEY `tab_index` (`tab_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `services_tabs`
--

CREATE TABLE IF NOT EXISTS `services_tabs` (
  `tab_index` int(11) NOT NULL AUTO_INCREMENT,
  `tab_text` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`tab_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `service_active_schedule`
--

CREATE TABLE IF NOT EXISTS `service_active_schedule` (
  `service_event_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service` varchar(10) DEFAULT NULL,
  `source` varchar(10) DEFAULT NULL,
  `first_date` date DEFAULT NULL,
  `last_date` date DEFAULT NULL,
  `days` varchar(7) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `start_mode` varchar(1) DEFAULT NULL,
  `name` text,
  `material_id` varchar(20) DEFAULT NULL,
  `rot` tinyint(1) DEFAULT NULL,
  `ptt` varchar(1) DEFAULT NULL,
  `ptt_time` int(11) DEFAULT NULL,
  `owner` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`service_event_id`),
  KEY `i_service` (`service`),
  KEY `i_source` (`source`),
  KEY `i_material` (`material_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=156 ;

-- --------------------------------------------------------

--
-- Table structure for table `service_components`
--

CREATE TABLE IF NOT EXISTS `service_components` (
  `service` varchar(10) NOT NULL,
  `type` varchar(64) NOT NULL,
  `port` smallint(6) NOT NULL,
  PRIMARY KEY (`service`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='service components and their udp port numbers';

-- --------------------------------------------------------

--
-- Table structure for table `service_event`
--

CREATE TABLE IF NOT EXISTS `service_event` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_time` datetime NOT NULL,
  `event_id` bigint(20) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `time` (`event_time`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='event schedule' AUTO_INCREMENT=72 ;

-- --------------------------------------------------------

--
-- Table structure for table `service_events`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`sif`@`%` SQL SECURITY DEFINER VIEW `sif`.`service_events` AS select `sif`.`service_event`.`id` AS `id`,`sif`.`service_event`.`event_time` AS `event_time`,`sif`.`service_event`.`event_id` AS `event_id`,`sif`.`service_event`.`updated` AS `updated`,`sif`.`service_active_schedule`.`service_event_id` AS `service_event_id`,`sif`.`service_active_schedule`.`service` AS `service`,`sif`.`service_active_schedule`.`source` AS `source`,`sif`.`service_active_schedule`.`first_date` AS `first_date`,`sif`.`service_active_schedule`.`last_date` AS `last_date`,`sif`.`service_active_schedule`.`days` AS `days`,`sif`.`service_active_schedule`.`start_time` AS `start_time`,`sif`.`service_active_schedule`.`duration` AS `duration`,`sif`.`service_active_schedule`.`start_mode` AS `start_mode`,`sif`.`service_active_schedule`.`name` AS `name`,`sif`.`service_active_schedule`.`material_id` AS `material_id`,`sif`.`service_active_schedule`.`rot` AS `rot`,`sif`.`service_active_schedule`.`ptt` AS `ptt`,`sif`.`service_active_schedule`.`ptt_time` AS `ptt_time`,`sif`.`service_active_schedule`.`owner` AS `owner` from (`sif`.`service_event` join `sif`.`service_active_schedule` on((`sif`.`service_event`.`event_id` = `sif`.`service_active_schedule`.`service_event_id`)));

-- --------------------------------------------------------

--
-- Table structure for table `service_planning_schedule`
--

CREATE TABLE IF NOT EXISTS `service_planning_schedule` (
  `service_event_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service` varchar(10) DEFAULT NULL,
  `source` varchar(10) DEFAULT NULL,
  `first_date` date DEFAULT NULL,
  `last_date` date DEFAULT NULL,
  `days` varchar(7) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `start_mode` varchar(1) DEFAULT NULL,
  `name` text,
  `material_id` varchar(20) DEFAULT NULL,
  `rot` tinyint(1) DEFAULT NULL,
  `ptt` varchar(1) DEFAULT NULL,
  `ptt_time` int(11) DEFAULT NULL,
  `owner` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`service_event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `soundcards`
--

CREATE TABLE IF NOT EXISTS `soundcards` (
  `soundcard` varchar(80) NOT NULL,
  `name` varchar(20) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`soundcard`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Information for driving sound cards';

-- --------------------------------------------------------

--
-- Table structure for table `source`
--

CREATE TABLE IF NOT EXISTS `source` (
  `id` varchar(10) NOT NULL COMMENT 'short name of source',
  `enabled` tinyint(1) DEFAULT '1',
  `role` varchar(8) DEFAULT 'CAPTURE',
  `pharos_index` int(11) DEFAULT NULL,
  `tab_index` int(11) DEFAULT NULL,
  `owner` varchar(64) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `tab_index` (`tab_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `source2device`
--

CREATE TABLE IF NOT EXISTS `source2device` (
  `id` varchar(16) NOT NULL COMMENT 'the source or listener id',
  `idx` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `device` varchar(64) NOT NULL COMMENT 'reference to the physical device',
  `pcm` varchar(64) NOT NULL COMMENT 'alsa pcm on the physical device',
  `tab_index` int(11) NOT NULL,
  PRIMARY KEY (`id`,`idx`),
  KEY `device` (`device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `source2device_tabs`
--

CREATE TABLE IF NOT EXISTS `source2device_tabs` (
  `tab_index` int(11) NOT NULL AUTO_INCREMENT,
  `tab_text` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`tab_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `source_tabs`
--

CREATE TABLE IF NOT EXISTS `source_tabs` (
  `tab_index` int(11) NOT NULL AUTO_INCREMENT,
  `tab_text` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`tab_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `listener`
--
ALTER TABLE `listener`
  ADD CONSTRAINT `listener_ibfk_1` FOREIGN KEY (`tab_index`) REFERENCES `listener_tabs` (`tab_index`);

--
-- Constraints for table `listener2device`
--
ALTER TABLE `listener2device`
  ADD CONSTRAINT `listener2device_ibfk_1` FOREIGN KEY (`id`) REFERENCES `listener` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listener2device_ibfk_2` FOREIGN KEY (`device`) REFERENCES `physicaldevices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listener_active_schedule`
--
ALTER TABLE `listener_active_schedule`
  ADD CONSTRAINT `listener_active_schedule_ibfk_2` FOREIGN KEY (`listener`) REFERENCES `listener` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listener_active_schedule_ibfk_1` FOREIGN KEY (`service`) REFERENCES `service` (`service`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listener_event`
--
ALTER TABLE `listener_event`
  ADD CONSTRAINT `listener_event_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `listener_active_schedule` (`listener_event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`tab_index`) REFERENCES `services_tabs` (`tab_index`);

--
-- Constraints for table `service_active_schedule`
--
ALTER TABLE `service_active_schedule`
  ADD CONSTRAINT `service_active_schedule_ibfk_3` FOREIGN KEY (`material_id`) REFERENCES `material` (`material_id`),
  ADD CONSTRAINT `service_active_schedule_ibfk_4` FOREIGN KEY (`source`) REFERENCES `source` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `service_active_schedule_ibfk_5` FOREIGN KEY (`service`) REFERENCES `service` (`service`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service_event`
--
ALTER TABLE `service_event`
  ADD CONSTRAINT `service_event_ibfk_1` FOREIGN KEY (`id`) REFERENCES `service_active_schedule` (`service_event_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `source`
--
ALTER TABLE `source`
  ADD CONSTRAINT `source_ibfk_1` FOREIGN KEY (`tab_index`) REFERENCES `source_tabs` (`tab_index`);

--
-- Constraints for table `source2device`
--
ALTER TABLE `source2device`
  ADD CONSTRAINT `source2device_ibfk_1` FOREIGN KEY (`id`) REFERENCES `source` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `source2device_ibfk_2` FOREIGN KEY (`device`) REFERENCES `physicaldevices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
