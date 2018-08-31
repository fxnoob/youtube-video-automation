-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 02, 2018 at 02:54 AM
-- Server version: 5.7.22
-- PHP Version: 7.1.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `youtube_news`
--
CREATE DATABASE IF NOT EXISTS `youtube_news` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `youtube_news`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dainik_bhasker`
--

CREATE TABLE `dainik_bhasker` (
  `pid` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `title` varchar(1000) DEFAULT NULL,
  `text_data` longtext,
  `tags` longtext,
  `photos` longtext,
  `scraped` int(11) NOT NULL DEFAULT '0',
  `processed` int(11) DEFAULT '0',
  `uploaded` int(11) DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `yt_id` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ndtv`
--

CREATE TABLE `ndtv` (
  `id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `title` varchar(500) NOT NULL,
  `text_data` longtext NOT NULL,
  `photos` text NOT NULL,
  `scraped` int(11) NOT NULL,
  `processed` int(11) NOT NULL,
  `uploaded` int(11) NOT NULL,
  `date_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `patrika`
--

CREATE TABLE `patrika` (
  `pid` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `title` varchar(1000) DEFAULT NULL,
  `text_data` longtext,
  `photos` longtext,
  `scraped` int(11) NOT NULL DEFAULT '0',
  `processed` int(11) DEFAULT '0',
  `uploaded` int(11) DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `portals`
--

CREATE TABLE `portals` (
  `id` int(11) NOT NULL,
  `url` text NOT NULL,
  `description` varchar(100) NOT NULL,
  `additional` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `y_tcredentials`
--

CREATE TABLE `y_tcredentials` (
  `id` int(11) NOT NULL,
  `yt_creds` longtext NOT NULL,
  `name` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dainik_bhasker`
--
ALTER TABLE `dainik_bhasker`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `ndtv`
--
ALTER TABLE `ndtv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patrika`
--
ALTER TABLE `patrika`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `portals`
--
ALTER TABLE `portals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `y_tcredentials`
--
ALTER TABLE `y_tcredentials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dainik_bhasker`
--
ALTER TABLE `dainik_bhasker`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ndtv`
--
ALTER TABLE `ndtv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patrika`
--
ALTER TABLE `patrika`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portals`
--
ALTER TABLE `portals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `y_tcredentials`
--
ALTER TABLE `y_tcredentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
