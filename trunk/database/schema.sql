-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 25, 2009 at 03:03 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6-1+lenny3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
 
--
-- Database: 'sif'
--

-- --------------------------------------------------------

--
-- Table structure for table 'adminlog'
--

CREATE TABLE IF NOT EXISTS adminlog (
  who varchar(20) collate utf8_bin NOT NULL,
  what varchar(80) collate utf8_bin NOT NULL,
  `when` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  notes varchar(255) collate utf8_bin default NULL,
  `row` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`row`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Things Administators have done' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'adminqueue'
--

CREATE TABLE IF NOT EXISTS adminqueue (
  seq int(11) NOT NULL auto_increment,
  url varchar(255) collate utf8_bin NOT NULL,
  created timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (seq)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Things Administators need to do' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'listener'
--

CREATE TABLE IF NOT EXISTS listener (
  listener varchar(10) character set latin1 NOT NULL,
  schedule_listener varchar(10) character set latin1 default NULL,
  listener_long_name text character set latin1,
  enabled tinyint(1) default '1',
  locked tinyint(1) default '1',
  current_service varchar(10) collate utf8_bin NOT NULL default 'OFF',
  default_service varchar(10) collate utf8_bin NOT NULL,
  auto_service tinyint(4) NOT NULL,
  role varchar(8) character set latin1 default 'OUTPUT',
  pharos_index int(11) default NULL,
  vlc_hostname varchar(64) character set latin1 default NULL,
  icon text character set latin1,
  tab_index int(11) default NULL,
  owner varchar(64) character set latin1 default NULL,
  notes text character set latin1,
  PRIMARY KEY  (listener)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'listener_active_schedule'
--

CREATE TABLE IF NOT EXISTS listener_active_schedule (
  listener_event_id bigint(20) NOT NULL auto_increment,
  listener varchar(10) character set latin1 default NULL,
  service varchar(10) character set latin1 default NULL,
  first_date date default NULL,
  last_date date default NULL,
  days varchar(7) character set latin1 default NULL,
  start_time time default NULL,
  duration time default NULL,
  start_mode varchar(1) character set latin1 default NULL,
  `name` text character set latin1,
  owner varchar(64) character set latin1 default NULL,
  PRIMARY KEY  (listener_event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'listener_planning_schedule'
--

CREATE TABLE IF NOT EXISTS listener_planning_schedule (
  listener_event_id bigint(20) NOT NULL auto_increment,
  listener varchar(10) character set latin1 default NULL,
  service varchar(10) character set latin1 default NULL,
  first_date date default NULL,
  last_date date default NULL,
  days varchar(7) character set latin1 default NULL,
  start_time time default NULL,
  duration time default NULL,
  start_mode varchar(1) character set latin1 default NULL,
  `name` text character set latin1,
  owner varchar(64) character set latin1 default NULL,
  PRIMARY KEY  (listener_event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'listener_tabs'
--

CREATE TABLE IF NOT EXISTS listener_tabs (
  tab_index int(11) NOT NULL auto_increment,
  tab_text varchar(20) character set latin1 default NULL,
  enabled tinyint(1) default NULL,
  PRIMARY KEY  (tab_index)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table 'logicaldevices'
--

CREATE TABLE IF NOT EXISTS logicaldevices (
  hostname varchar(64) character set latin1 NOT NULL,
  macaddress varchar(17) character set latin1 default NULL,
  ipv4 varchar(15) collate utf8_bin NOT NULL,
  PRIMARY KEY  (hostname)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'material'
--

CREATE TABLE IF NOT EXISTS material (
  material_id varchar(20) collate utf8_bin NOT NULL,
  duration time NOT NULL,
  delete_after date NOT NULL,
  title varchar(256) collate utf8_bin NOT NULL,
  `file` varchar(256) collate utf8_bin NOT NULL,
  material_type varchar(20) collate utf8_bin NOT NULL,
  owner varchar(64) collate utf8_bin NOT NULL,
  client_ref varchar(512) collate utf8_bin NOT NULL,
  tx_date date NOT NULL,
  PRIMARY KEY  (material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'physicaldevicecharacteristics'
--

CREATE TABLE IF NOT EXISTS physicaldevicecharacteristics (
  `type` varchar(20) character set latin1 NOT NULL,
  `name` varchar(20) collate utf8_bin NOT NULL,
  `value` varchar(255) character set latin1 NOT NULL,
  PRIMARY KEY  (`type`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'physicaldevices'
--

CREATE TABLE IF NOT EXISTS physicaldevices (
  macaddress varchar(17) character set latin1 NOT NULL default '',
  location varchar(255) character set latin1 default NULL,
  `type` varchar(20) collate utf8_bin NOT NULL,
  PRIMARY KEY  (macaddress)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'physicaldevicetypes'
--

CREATE TABLE IF NOT EXISTS physicaldevicetypes (
  `type` varchar(20) collate utf8_bin NOT NULL,
  description varchar(80) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Named device types';

-- --------------------------------------------------------

--
-- Table structure for table 'redundancy'
--

CREATE TABLE IF NOT EXISTS redundancy (
  redundancy_text varchar(10) collate utf8_bin NOT NULL,
  redundancy_type varchar(8) collate utf8_bin NOT NULL default 'SOURCE',
  main varchar(10) collate utf8_bin NOT NULL,
  reserve varchar(10) collate utf8_bin NOT NULL,
  tab_index int(11) NOT NULL,
  PRIMARY KEY  (redundancy_text)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'redundancy_tabs'
--

CREATE TABLE IF NOT EXISTS redundancy_tabs (
  tab_index int(11) NOT NULL auto_increment,
  tab_text varchar(20) character set latin1 default NULL,
  enabled tinyint(1) default NULL,
  PRIMARY KEY  (tab_index)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table 'service'
--

CREATE TABLE IF NOT EXISTS service (
  service varchar(10) character set latin1 NOT NULL,
  service_long_name text character set latin1,
  multicast_id int(11) NOT NULL auto_increment,
  enabled tinyint(1) default '1',
  locked tinyint(1) default NULL,
  current_source varchar(10) character set latin1 default 'OFF',
  icon text character set latin1,
  tab_index int(11) default NULL,
  owner varchar(64) character set latin1 default NULL,
  notes text character set latin1,
  pharos_index int(11) NOT NULL,
  PRIMARY KEY  (multicast_id,service),
  UNIQUE KEY service (service)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table 'services_tabs'
--

CREATE TABLE IF NOT EXISTS services_tabs (
  tab_index int(11) NOT NULL auto_increment,
  tab_text varchar(20) character set latin1 default NULL,
  enabled tinyint(1) default NULL,
  PRIMARY KEY  (tab_index)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table 'service_active_schedule'
--

CREATE TABLE IF NOT EXISTS service_active_schedule (
  service_event_id bigint(20) NOT NULL auto_increment,
  service varchar(10) character set latin1 default NULL,
  `source` varchar(10) character set latin1 default NULL,
  first_date date default NULL,
  last_date date default NULL,
  days varchar(7) character set latin1 default NULL,
  start_time time default NULL,
  duration time default NULL,
  start_mode varchar(1) character set latin1 default NULL,
  `name` text character set latin1,
  material_id text character set latin1,
  rot tinyint(1) default NULL,
  ptt varchar(1) character set latin1 default NULL,
  ptt_time int(11) default NULL,
  owner varchar(64) character set latin1 default NULL,
  PRIMARY KEY  (service_event_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table 'service_planning_schedule'
--

CREATE TABLE IF NOT EXISTS service_planning_schedule (
  service_event_id bigint(20) NOT NULL auto_increment,
  service varchar(10) character set latin1 default NULL,
  `source` varchar(10) character set latin1 default NULL,
  first_date date default NULL,
  last_date date default NULL,
  days varchar(7) character set latin1 default NULL,
  start_time time default NULL,
  duration time default NULL,
  start_mode varchar(1) character set latin1 default NULL,
  `name` text character set latin1,
  material_id text character set latin1,
  rot tinyint(1) default NULL,
  ptt varchar(1) character set latin1 default NULL,
  ptt_time int(11) default NULL,
  owner varchar(64) character set latin1 default NULL,
  PRIMARY KEY  (service_event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'soundcards'
--

CREATE TABLE IF NOT EXISTS soundcards (
  soundcard varchar(80) collate utf8_bin NOT NULL,
  `name` varchar(20) collate utf8_bin NOT NULL,
  `value` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (soundcard,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Information for driving sound cards';

-- --------------------------------------------------------

--
-- Table structure for table 'source'
--

CREATE TABLE IF NOT EXISTS `source` (
  `source` varchar(10) character set latin1 NOT NULL,
  schedule_source varchar(10) character set latin1 default NULL,
  source_long_name text character set latin1,
  enabled tinyint(1) default '1',
  active tinyint(1) default '1',
  role varchar(8) character set latin1 default 'CAPTURE',
  pharos_index int(11) default NULL,
  vlc_hostname varchar(64) character set latin1 default NULL,
  device varchar(64) collate utf8_bin NOT NULL,
  port varchar(64) collate utf8_bin NOT NULL,
  icon text character set latin1,
  tab_index int(11) default NULL,
  owner varchar(64) character set latin1 default NULL,
  notes text character set latin1,
  PRIMARY KEY  (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table 'source_tabs'
--

CREATE TABLE IF NOT EXISTS source_tabs (
  tab_index int(11) NOT NULL auto_increment,
  tab_text varchar(20) character set latin1 default NULL,
  enabled tinyint(1) default '1',
  PRIMARY KEY  (tab_index)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;
