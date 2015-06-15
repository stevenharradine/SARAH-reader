-- phpMyAdmin SQL Dump
-- version 4.2.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 14, 2015 at 10:14 PM
-- Server version: 5.5.43-0ubuntu0.14.10.1
-- PHP Version: 5.5.12-2ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sarah`
--

-- --------------------------------------------------------

--
-- Table structure for table `reader_cache`
--

CREATE TABLE IF NOT EXISTS `reader_cache` (
`ITEM_ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `FEED_ID` int(11) NOT NULL,
  `feed_name` text NOT NULL,
  `label` text NOT NULL,
  `url` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  `favorite` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=553099 ;

-- --------------------------------------------------------

--
-- Table structure for table `reader_feeds`
--

CREATE TABLE IF NOT EXISTS `reader_feeds` (
`FEED_ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `name` text NOT NULL,
  `label` text NOT NULL,
  `rss` text NOT NULL,
  `isDisabled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reader_cache`
--
ALTER TABLE `reader_cache`
 ADD PRIMARY KEY (`ITEM_ID`);

--
-- Indexes for table `reader_feeds`
--
ALTER TABLE `reader_feeds`
 ADD PRIMARY KEY (`FEED_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reader_cache`
--
ALTER TABLE `reader_cache`
MODIFY `ITEM_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=553099;
--
-- AUTO_INCREMENT for table `reader_feeds`
--
ALTER TABLE `reader_feeds`
MODIFY `FEED_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=69;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
