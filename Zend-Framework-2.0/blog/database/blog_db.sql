-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2015 at 09:52 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `blog_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE IF NOT EXISTS `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `sef` varchar(255) NOT NULL,
  `meta_description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `title`, `content`, `sef`, `meta_description`) VALUES
(1, 'Social Integration', 'Social Integration', 'social', 'Social Integration'),
(5, 'content', 'block', 'test', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `blog_category`
--

CREATE TABLE IF NOT EXISTS `blog_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `blog_category`
--

INSERT INTO `blog_category` (`id`, `blog_id`, `category_id`) VALUES
(5, 5, 1),
(8, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `root` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `root`, `lft`, `rgt`) VALUES
(1, 'Social', 1, 1, 6),
(2, 'fb integration', 1, 2, 3),
(3, 'gmail integration', 1, 4, 5),
(4, 'localtest', NULL, 1, 2),
(5, 'localtest', 5, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE IF NOT EXISTS `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) NOT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1002 ;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`id`, `name`, `country_id`, `status`) VALUES
(1, 'Kolkata', 2, 'active'),
(2, 'Siliguri', 2, 'active'),
(3, 'Delhi', 2, 'active'),
(4, 'Mumbai', 2, 'active'),
(5, 'Chennai', 2, 'active'),
(6, 'Bangalore', 2, 'active'),
(7, 'Hyderabad', 2, 'active'),
(9, 'Pune', 2, 'active'),
(10, 'Chandigarh', 2, 'active'),
(11, 'Jaipur', 2, 'active'),
(12, 'Thiruvananthapuram', 2, 'active'),
(13, 'Vadodara', 2, 'active'),
(14, 'Surat', 2, 'active'),
(15, 'Agra', 2, 'active'),
(16, 'Jamshedpur', 2, 'active'),
(17, 'Gurgaon', 2, 'active'),
(18, 'Bhubaneswar', 2, 'active'),
(19, 'Guwahati', 2, 'active'),
(20, 'Ranchi', 2, 'active'),
(21, 'Agartala', 2, 'active'),
(22, 'Varanasi', 2, 'active'),
(23, 'Allahabad', 2, 'active'),
(24, 'Lucknow', 2, 'active'),
(25, 'Jodhpur', 2, 'active'),
(26, 'Amritsar', 2, 'active'),
(27, 'Bhopal', 2, 'active'),
(28, 'Indore', 2, 'active'),
(29, 'Nagpur', 2, 'active'),
(30, 'Aurangabad', 2, 'active'),
(31, 'Patna', 2, 'active'),
(32, 'Dhanbad', 2, 'active'),
(33, 'Durgapur', 2, 'active'),
(34, 'Gandhinagar', 2, 'active'),
(35, 'Faridabad', 2, 'active'),
(37, 'Goa', 2, 'active'),
(38, 'Vijayawada', 2, 'active'),
(39, 'Visakhapatnam', 2, 'active'),
(40, 'Gwalior', 2, 'active'),
(45, 'Coimbatore', 2, 'active'),
(54, 'Ludhiana', 2, 'active'),
(57, 'Cuttack', 2, 'active'),
(58, 'Gaya', 2, 'active'),
(61, 'Jalandhar', 2, 'active'),
(62, 'Patiala', 2, 'active'),
(63, 'Kanpur', 2, 'active'),
(64, 'Raipur', 2, 'active'),
(65, 'Kharagpur', 2, 'active'),
(67, 'Meerut', 2, 'active'),
(68, 'Dehradun', 2, 'active'),
(69, 'Bathinda', 2, 'active'),
(70, 'Kota', 2, 'active'),
(71, 'Rajkot', 2, 'active'),
(72, 'Jhansi', 2, 'active'),
(73, 'Kolhapur', 2, 'active'),
(74, 'Burdwan', 2, 'active'),
(75, 'Madurai', 2, 'active'),
(76, 'Bharuch', 2, 'active'),
(77, 'Moradabad', 2, 'active'),
(78, 'Bikaner', 2, 'active'),
(79, 'Calicut', 2, 'active'),
(80, 'Noida', 2, 'active'),
(81, 'Secundrabad', 2, 'active'),
(84, 'Rourkela', 2, 'active'),
(86, 'Thiruchirappalli', 2, 'active'),
(88, 'Palakkad', 2, 'active'),
(91, 'Jammu', 2, 'active'),
(92, 'Outside India', 2, 'active'),
(95, 'Ghaziabad', 2, 'active'),
(96, 'Howrah', 2, 'active'),
(97, 'Navi Mumbai', 2, 'active'),
(98, 'Asansol', 2, 'active'),
(99, 'Mangalore', 2, 'active'),
(1001, 'krachi', 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'inactive',
  `currency_code` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `name`, `code`, `status`, `currency_code`) VALUES
(1, 'Pakistan', 'pk', 'active', 'RS'),
(2, 'India', 'in', 'active', 'Rs');

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'data_entry'),
(4, 'abc1');

-- --------------------------------------------------------

--
-- Table structure for table `group_resource`
--

CREATE TABLE IF NOT EXISTS `group_resource` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`resource_id`),
  KEY `group_id` (`group_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `link` varchar(50) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `state` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `display_name`, `password`, `state`) VALUES
(1, NULL, 'admin@blog.com', 'Admin', 'e10adc3949ba59abbe56e057f20f883e', 1),
(2, NULL, 'data.entry@mpt-schools.com', 'data entry', 'e10adc3949ba59abbe56e057f20f883e', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE IF NOT EXISTS `user_details` (
  `city_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_no` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `city_id` (`city_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`city_id`, `group_id`, `user_id`, `address`, `location`, `phone_no`) VALUES
(1, 1, 1, '', NULL, 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_category`
--
ALTER TABLE `blog_category`
  ADD CONSTRAINT `blog_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blog_category_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `city`
--
ALTER TABLE `city`
  ADD CONSTRAINT `city_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_resource`
--
ALTER TABLE `group_resource`
  ADD CONSTRAINT `group_resource_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_resource_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resource`
--
ALTER TABLE `resource`
  ADD CONSTRAINT `resource_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_details_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_details_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
