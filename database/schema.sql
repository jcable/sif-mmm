-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 06, 2010 at 05:17 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

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
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `adminlog`;
CREATE TABLE IF NOT EXISTS `adminlog` (
  `who` varchar(20) NOT NULL,
  `what` varchar(80) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` varchar(255) DEFAULT NULL,
  `row` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`row`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Things Administators have done' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `adminqueue`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `adminqueue`;
CREATE TABLE IF NOT EXISTS `adminqueue` (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(80) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`seq`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Things Administators need to do' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `listener`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `listener`;
CREATE TABLE IF NOT EXISTS `listener` (
  `id` varchar(10) NOT NULL COMMENT 'short name of listener',
  `long_name` text,
  `enabled` tinyint(1) DEFAULT '1',
  `locked` tinyint(1) DEFAULT '1',
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
-- Creation: Jan 06, 2010 at 03:28 PM
--

DROP TABLE IF EXISTS `listener2device`;
CREATE TABLE IF NOT EXISTS `listener2device` (
  `id` varchar(10) NOT NULL COMMENT 'the source or listener id',
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
-- Creation: Jan 06, 2010 at 04:43 PM
--

DROP TABLE IF EXISTS `listener2device_tabs`;
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
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `listener_active_schedule`;
CREATE TABLE IF NOT EXISTS `listener_active_schedule` (
  `listener_event_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `listener` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `service` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `first_date` date DEFAULT NULL,
  `last_date` date DEFAULT NULL,
  `days` varchar(7) CHARACTER SET ascii DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `start_mode` varchar(1) CHARACTER SET ascii DEFAULT NULL,
  `name` text CHARACTER SET utf8,
  `owner` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`listener_event_id`),
  KEY `listener` (`listener`),
  KEY `service` (`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `listener_planning_schedule`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `listener_planning_schedule`;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `listener_tabs`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `listener_tabs`;
CREATE TABLE IF NOT EXISTS `listener_tabs` (
  `tab_index` int(11) NOT NULL AUTO_INCREMENT,
  `tab_text` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`tab_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `material`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `material`;
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
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `physicaldevicecharacteristics`;
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
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `physicaldevices`;
CREATE TABLE IF NOT EXISTS `physicaldevices` (
  `id` varchar(10) NOT NULL COMMENT 'friendly name',
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
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `physicaldevicetypes`;
CREATE TABLE IF NOT EXISTS `physicaldevicetypes` (
  `type` varchar(20) NOT NULL,
  `description` varchar(80) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Named device types';

-- --------------------------------------------------------

--
-- Table structure for table `service`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `service` varchar(10) CHARACTER SET utf8 NOT NULL,
  `service_long_name` text CHARACTER SET utf8,
  `enabled` tinyint(1) DEFAULT '1',
  `locked` tinyint(1) DEFAULT NULL,
  `tab_index` int(11) DEFAULT NULL,
  `owner` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `notes` text CHARACTER SET utf8,
  `pharos_index` int(11) NOT NULL,
  PRIMARY KEY (`service`),
  UNIQUE KEY `service` (`service`),
  KEY `tab_index` (`tab_index`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `services_tabs`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `services_tabs`;
CREATE TABLE IF NOT EXISTS `services_tabs` (
  `tab_index` int(11) NOT NULL AUTO_INCREMENT,
  `tab_text` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`tab_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `service_active_schedule`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `service_active_schedule`;
CREATE TABLE IF NOT EXISTS `service_active_schedule` (
  `service_event_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `source` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `first_date` date DEFAULT NULL,
  `last_date` date DEFAULT NULL,
  `days` varchar(7) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `start_mode` varchar(1) DEFAULT NULL,
  `name` text,
  `material_id` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `rot` tinyint(1) DEFAULT NULL,
  `ptt` varchar(1) DEFAULT NULL,
  `ptt_time` int(11) DEFAULT NULL,
  `owner` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`service_event_id`),
  KEY `i_service` (`service`),
  KEY `i_source` (`source`),
  KEY `i_material` (`material_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `service_planning_schedule`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `service_planning_schedule`;
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
  `material_id` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `rot` tinyint(1) DEFAULT NULL,
  `ptt` varchar(1) DEFAULT NULL,
  `ptt_time` int(11) DEFAULT NULL,
  `owner` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`service_event_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `soundcards`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `soundcards`;
CREATE TABLE IF NOT EXISTS `soundcards` (
  `soundcard` varchar(80) NOT NULL,
  `name` varchar(20) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`soundcard`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Information for driving sound cards';

-- --------------------------------------------------------

--
-- Table structure for table `source`
--
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `source`;
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
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `source2device`;
CREATE TABLE IF NOT EXISTS `source2device` (
  `id` varchar(10) NOT NULL COMMENT 'the source or listener id',
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
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `source2device_tabs`;
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
-- Creation: Jan 06, 2010 at 10:23 AM
--

DROP TABLE IF EXISTS `source_tabs`;
CREATE TABLE IF NOT EXISTS `source_tabs` (
  `tab_index` int(11) NOT NULL AUTO_INCREMENT,
  `tab_text` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`tab_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

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
  ADD CONSTRAINT `listener2device_ibfk_2` FOREIGN KEY (`device`) REFERENCES `physicaldevices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listener2device_ibfk_1` FOREIGN KEY (`id`) REFERENCES `listener` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listener_active_schedule`
--
ALTER TABLE `listener_active_schedule`
  ADD CONSTRAINT `listener_active_schedule_ibfk_2` FOREIGN KEY (`listener`) REFERENCES `listener` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listener_active_schedule_ibfk_1` FOREIGN KEY (`service`) REFERENCES `service` (`service`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`tab_index`) REFERENCES `services_tabs` (`tab_index`);

--
-- Constraints for table `service_active_schedule`
--
ALTER TABLE `service_active_schedule`
  ADD CONSTRAINT `service_active_schedule_ibfk_5` FOREIGN KEY (`service`) REFERENCES `service` (`service`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `service_active_schedule_ibfk_3` FOREIGN KEY (`material_id`) REFERENCES `material` (`material_id`),
  ADD CONSTRAINT `service_active_schedule_ibfk_4` FOREIGN KEY (`source`) REFERENCES `source` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `source`
--
ALTER TABLE `source`
  ADD CONSTRAINT `source_ibfk_1` FOREIGN KEY (`tab_index`) REFERENCES `source_tabs` (`tab_index`);

--
-- Constraints for table `source2device`
--
ALTER TABLE `source2device`
  ADD CONSTRAINT `source2device_ibfk_2` FOREIGN KEY (`device`) REFERENCES `physicaldevices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `source2device_ibfk_1` FOREIGN KEY (`id`) REFERENCES `source` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
