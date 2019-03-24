-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 24. Mrz 2019 um 13:45
-- Server-Version: 10.1.37-MariaDB
-- PHP-Version: 7.1.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `mystromDb`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Datalogger`
--

CREATE TABLE `Datalogger` (
  `dbId` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Measurements`
--

CREATE TABLE `Measurements` (
  `dbId` int(11) NOT NULL,
  `dbIdSeries` int(11) NOT NULL,
  `CurrentPower` double NOT NULL,
  `Energy` double NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deltaT` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Series`
--

CREATE TABLE `Series` (
  `dbId` int(11) NOT NULL,
  `dbIdDatalogger` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Startdatum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Datalogger`
--
ALTER TABLE `Datalogger`
  ADD PRIMARY KEY (`dbId`);

--
-- Indizes für die Tabelle `Measurements`
--
ALTER TABLE `Measurements`
  ADD PRIMARY KEY (`dbId`);

--
-- Indizes für die Tabelle `Series`
--
ALTER TABLE `Series`
  ADD PRIMARY KEY (`dbId`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Datalogger`
--
ALTER TABLE `Datalogger`
  MODIFY `dbId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Measurements`
--
ALTER TABLE `Measurements`
  MODIFY `dbId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Series`
--
ALTER TABLE `Series`
  MODIFY `dbId` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
