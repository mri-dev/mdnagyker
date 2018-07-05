--
-- Table structure for table `nagyker_downloadable_images`
--
CREATE TABLE `nagyker_downloadable_images` (
  `ID` int(11) NOT NULL,
  `hashkey` varchar(50) NOT NULL COMMENT 'md5( nagyker _ nagyker_id )',
  `nagyker` smallint(2) NOT NULL,
  `nagyker_id` varchar(50) DEFAULT NULL,
  `gyarto_id` varchar(50) DEFAULT NULL,
  `kep` varchar(250) NOT NULL,
  `downloaded` tinyint(1) NOT NULL DEFAULT '0',
  `cannot_be_downloaded` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nagyker_downloadable_images`
--
ALTER TABLE `nagyker_downloadable_images`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `hashkey` (`hashkey`),
  ADD KEY `nagyker` (`nagyker`),
  ADD KEY `kep` (`kep`),
  ADD KEY `downloaded` (`downloaded`),
  ADD KEY `cannot_be_downloaded` (`cannot_be_downloaded`),
  ADD KEY `gyarto_id` (`gyarto_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nagyker_downloadable_images`
--
ALTER TABLE `nagyker_downloadable_images`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 19, 2018 at 03:01 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `goldonli_fishing`
--

-- --------------------------------------------------------

--
-- Table structure for table `xml_origins`
--

CREATE TABLE `xml_origins` (
  `ID` smallint(6) NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `url` text,
  `download_progress` tinyint(1) NOT NULL DEFAULT '0',
  `typeof` enum('xml','csv') NOT NULL,
  `last_download` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `xml_origins_column_xref`
--

CREATE TABLE `xml_origins_column_xref` (
  `ID` int(11) NOT NULL,
  `origin_id` smallint(6) NOT NULL,
  `rootkey` varchar(50) NOT NULL,
  `prod_id` varchar(200) DEFAULT NULL,
  `termek_nev` varchar(200) DEFAULT NULL,
  `termek_leiras` varchar(200) DEFAULT NULL,
  `termek_leiras2` varchar(200) DEFAULT NULL,
  `nagyker_ar_netto` varchar(200) DEFAULT NULL,
  `nagyker_ar_netto_akcios` varchar(200) DEFAULT NULL,
  `kisker_ar_netto` varchar(200) DEFAULT NULL,
  `kisker_ar_netto_akcios` varchar(200) DEFAULT NULL,
  `termek_keszlet` varchar(200) DEFAULT NULL,
  `termek_kep_urls` varchar(200) NOT NULL,
  `ean_code` varchar(200) DEFAULT NULL,
  `marka_nev` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `xml_temp_products`
--

CREATE TABLE `xml_temp_products` (
  `ID` int(11) NOT NULL,
  `hashkey` varchar(50) NOT NULL,
  `origin_id` smallint(6) NOT NULL,
  `prod_id` varchar(250) NOT NULL,
  `last_updated` datetime DEFAULT NULL,
  `termek_nev` varchar(250) DEFAULT NULL,
  `termek_leiras` text,
  `termek_leiras2` text,
  `nagyker_ar_netto` float DEFAULT NULL,
  `kisker_ar_netto` float DEFAULT NULL,
  `termek_keszlet` mediumint(9) DEFAULT NULL,
  `termek_kep_urls` text,
  `ean_code` varchar(30) DEFAULT NULL,
  `marka_nev` varchar(50) DEFAULT NULL,
  `kisker_ar_netto_akcios` float DEFAULT NULL,
  `nagyker_ar_netto_akcios` float DEFAULT NULL,
  `io` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `xml_origins`
--
ALTER TABLE `xml_origins`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `download_progress` (`download_progress`),
  ADD KEY `typeof` (`typeof`);

--
-- Indexes for table `xml_origins_column_xref`
--
ALTER TABLE `xml_origins_column_xref`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `origin_id` (`origin_id`);

--
-- Indexes for table `xml_temp_products`
--
ALTER TABLE `xml_temp_products`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `hashkey` (`hashkey`),
  ADD KEY `origin_id` (`origin_id`),
  ADD KEY `prod_id` (`prod_id`),
  ADD KEY `ean_code` (`ean_code`),
  ADD KEY `marka_nev` (`marka_nev`),
  ADD KEY `io` (`io`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `xml_origins`
--
ALTER TABLE `xml_origins`
  MODIFY `ID` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `xml_origins_column_xref`
--
ALTER TABLE `xml_origins_column_xref`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `xml_temp_products`
--
ALTER TABLE `xml_temp_products`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27433;
COMMIT;
