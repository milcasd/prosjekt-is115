-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 04. Des, 2023 12:01 PM
-- Tjener-versjon: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jobquest`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `arbeidsgiver`
--

CREATE TABLE `arbeidsgiver` (
  `UID` int(11) NOT NULL,
  `bedriftsnavn` varchar(255) NOT NULL,
  `epost` varchar(255) NOT NULL,
  `orgnr` int(9) NOT NULL,
  `sted` varchar(255) NOT NULL,
  `zip` int(4) NOT NULL,
  `passord` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `arbeidsgiver`
--

INSERT INTO `arbeidsgiver` (`UID`, `bedriftsnavn`, `epost`, `orgnr`, `sted`, `zip`, `passord`) VALUES
(6, 'Bedrift', 'test@arbeidsgiver.no', 123456789, 'Kristiansand', 4099, '$2y$10$UpB/fq/VyH6.e4VAZ8Ici.piKcuArQEuUApMNj2yYNcAiNLCi2PFq');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `brukere`
--

CREATE TABLE `brukere` (
  `UID` int(11) NOT NULL,
  `brukernavn` varchar(200) NOT NULL,
  `fnavn` varchar(200) NOT NULL,
  `enavn` varchar(200) NOT NULL,
  `epost` varchar(200) NOT NULL,
  `tlf` int(8) NOT NULL,
  `passord` varchar(250) NOT NULL,
  `sted` varchar(100) NOT NULL,
  `zip` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `brukere`
--

INSERT INTO `brukere` (`UID`, `brukernavn`, `fnavn`, `enavn`, `epost`, `tlf`, `passord`, `sted`, `zip`) VALUES
(4, 'jodo', 'John', 'Doe', 'johndoe@uia.no', 12345678, '$2y$10$sCiPrTJ6DM1PIdlTqzedhOWWuHFYCRp30s5maZOakmk15Bth41QTO', 'Kristiansand', 4600);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `jobber`
--

CREATE TABLE `jobber` (
  `UID` int(11) NOT NULL,
  `stillingstittel` varchar(255) NOT NULL,
  `beskrivelse` text NOT NULL,
  `sted` varchar(255) NOT NULL,
  `publiseringsdato` date NOT NULL DEFAULT current_timestamp(),
  `frist` date NOT NULL,
  `bilde` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `jobbsoknad`
--

CREATE TABLE `jobbsoknad` (
  `UID` int(11) NOT NULL,
  `fnavn` varchar(255) NOT NULL,
  `enavn` varchar(255) NOT NULL,
  `tlf` int(8) NOT NULL,
  `epost` varchar(255) NOT NULL,
  `utdanning` varchar(2000) NOT NULL,
  `erfaring` varchar(2000) NOT NULL,
  `soknadstekst` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `meldinger`
--

CREATE TABLE `meldinger` (
  `UID` int(6) NOT NULL,
  `sender` varchar(30) NOT NULL,
  `mottaker` varchar(30) NOT NULL,
  `melding` text NOT NULL,
  `tid` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `meldinger`
--

INSERT INTO `meldinger` (`UID`, `sender`, `mottaker`, `melding`, `tid`) VALUES
(1, 'Kongsberg Maritim ', 'John', 'Hei! Vi er interessert i et intervju når det passer for deg? ', '2023-12-01 13:08:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arbeidsgiver`
--
ALTER TABLE `arbeidsgiver`
  ADD PRIMARY KEY (`UID`);

--
-- Indexes for table `brukere`
--
ALTER TABLE `brukere`
  ADD PRIMARY KEY (`UID`);

--
-- Indexes for table `jobber`
--
ALTER TABLE `jobber`
  ADD PRIMARY KEY (`UID`);

--
-- Indexes for table `jobbsoknad`
--
ALTER TABLE `jobbsoknad`
  ADD PRIMARY KEY (`UID`);

--
-- Indexes for table `meldinger`
--
ALTER TABLE `meldinger`
  ADD PRIMARY KEY (`UID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arbeidsgiver`
--
ALTER TABLE `arbeidsgiver`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `brukere`
--
ALTER TABLE `brukere`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobber`
--
ALTER TABLE `jobber`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `jobbsoknad`
--
ALTER TABLE `jobbsoknad`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `meldinger`
--
ALTER TABLE `meldinger`
  MODIFY `UID` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
