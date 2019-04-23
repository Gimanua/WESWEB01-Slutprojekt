-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 23 apr 2019 kl 21:26
-- Serverversion: 10.1.35-MariaDB
-- PHP-version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `webchess`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `bannedips`
--

CREATE TABLE `bannedips` (
  `ip` varchar(128) NOT NULL,
  `until` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `bannedusers`
--

CREATE TABLE `bannedusers` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `userid` smallint(5) UNSIGNED NOT NULL,
  `until` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `challenges`
--

CREATE TABLE `challenges` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `senderuserid` smallint(5) UNSIGNED NOT NULL,
  `receiveruserid` smallint(5) UNSIGNED NOT NULL,
  `private` tinyint(1) NOT NULL,
  `sendtime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `games`
--

CREATE TABLE `games` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `pgn` mediumtext NOT NULL,
  `fen` varchar(128) NOT NULL,
  `imageurl` varchar(192) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `blackuserid` smallint(6) UNSIGNED NOT NULL,
  `whiteuserid` smallint(6) UNSIGNED NOT NULL,
  `averageelorating` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `games`
--

INSERT INTO `games` (`id`, `status`, `pgn`, `fen`, `imageurl`, `private`, `blackuserid`, `whiteuserid`, `averageelorating`) VALUES
(1, 0, 'Some PGN text for this game.', 'rnbqkbnr/ppp1pppp/8/3p4/3PP3/8/PPP2PPP/RNBQKBNR b KQkq -', 'http://www.fen-to-image.com/image/rnbqkbnr/ppp1pppp/8/3p4/3PP3/8/PPP2PPP/RNBQKBNR%20b%20KQkq%20-', 0, 1, 3, 1200),
(2, 0, 'Some PGN text describing the game.', 'r1bqkb1r/pppp1ppp/2n2n2/1B2p3/4P3/5N2/PPPP1PPP/RNBQK2R w KQkq -', 'http://www.fen-to-image.com/image/r1bqkb1r/pppp1ppp/2n2n2/1B2p3/4P3/5N2/PPPP1PPP/RNBQK2R%20w%20KQkq%20-', 0, 4, 5, 1948),
(3, 0, 'Some PGN text describing the game.', 'rnb1kbnr/ppp1pppp/8/3q4/8/2N5/PPPP1PPP/R1BQKBNR b KQkq -', 'http://www.fen-to-image.com/image/rnb1kbnr/ppp1pppp/8/3q4/8/2N5/PPPP1PPP/R1BQKBNR%20b%20KQkq%20-', 0, 2, 3, 1200),
(4, 0, 'Some PGN text describing the game.', 'rnbqkbnr/ppp2ppp/4p3/3p4/3PP3/8/PPP2PPP/RNBQKBNR w KQkq -', 'http://www.fen-to-image.com/image/rnbqkbnr/ppp2ppp/4p3/3p4/3PP3/8/PPP2PPP/RNBQKBNR%20w%20KQkq%20-', 0, 4, 3, 1532);

-- --------------------------------------------------------

--
-- Tabellstruktur `nonactivatedusers`
--

CREATE TABLE `nonactivatedusers` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL,
  `activationvalid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `token` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `nonactivatedusers`
--

INSERT INTO `nonactivatedusers` (`id`, `username`, `password`, `email`, `activationvalid`, `token`) VALUES
(3, 'Gimanua', '$2y$10$WW6/eACrJFUOqw3OJAcImOBLnDtUIqxWxOuEs0VohMmVd4Mtf1N9i', 'gimanua.dota2@gmail.com', '2019-04-23 19:28:36', '407e80009db34df8179205fde022f7e5');

-- --------------------------------------------------------

--
-- Tabellstruktur `savedgames`
--

CREATE TABLE `savedgames` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `userid` smallint(5) UNSIGNED NOT NULL,
  `gameid` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

CREATE TABLE `users` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(64) NOT NULL,
  `elorating` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `elorating`) VALUES
(1, 'Test', 'Test', 'test@test.se', 1200),
(2, 'Göran', '123', 'göran.go@a.b', 1200),
(3, 'Mange', '123', 'mamama@kk.lll', 1200),
(4, 'Janne', '123', 'jan.jan@unibet.com', 1865),
(5, 'Berit', '123', 'berit@koko.se', 2031);

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `bannedips`
--
ALTER TABLE `bannedips`
  ADD PRIMARY KEY (`ip`);

--
-- Index för tabell `bannedusers`
--
ALTER TABLE `bannedusers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_USERID` (`userid`) USING BTREE;

--
-- Index för tabell `challenges`
--
ALTER TABLE `challenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_SENDERUSERID` (`senderuserid`) USING BTREE,
  ADD KEY `IX_RECEIVERUSERID` (`receiveruserid`) USING BTREE;

--
-- Index för tabell `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_BLACKUSERID` (`blackuserid`) USING BTREE,
  ADD KEY `IX_WHITEUSERID` (`whiteuserid`) USING BTREE;

--
-- Index för tabell `nonactivatedusers`
--
ALTER TABLE `nonactivatedusers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IX_USERNAME` (`username`),
  ADD UNIQUE KEY `IX_EMAIL` (`email`),
  ADD UNIQUE KEY `IX_TOKEN` (`token`) USING BTREE;

--
-- Index för tabell `savedgames`
--
ALTER TABLE `savedgames`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_USERID` (`userid`) USING BTREE,
  ADD KEY `IX_GAMEID` (`gameid`) USING BTREE;

--
-- Index för tabell `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IX_USERNAME` (`username`),
  ADD UNIQUE KEY `IX_EMAIL` (`email`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `bannedusers`
--
ALTER TABLE `bannedusers`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `challenges`
--
ALTER TABLE `challenges`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `games`
--
ALTER TABLE `games`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT för tabell `nonactivatedusers`
--
ALTER TABLE `nonactivatedusers`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT för tabell `savedgames`
--
ALTER TABLE `savedgames`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `users`
--
ALTER TABLE `users`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `bannedusers`
--
ALTER TABLE `bannedusers`
  ADD CONSTRAINT `bannedusers_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`);

--
-- Restriktioner för tabell `challenges`
--
ALTER TABLE `challenges`
  ADD CONSTRAINT `challenges_ibfk_1` FOREIGN KEY (`senderuserid`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `challenges_ibfk_2` FOREIGN KEY (`receiveruserid`) REFERENCES `users` (`id`);

--
-- Restriktioner för tabell `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`blackuserid`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `games_ibfk_2` FOREIGN KEY (`whiteuserid`) REFERENCES `users` (`id`);

--
-- Restriktioner för tabell `savedgames`
--
ALTER TABLE `savedgames`
  ADD CONSTRAINT `savedgames_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `savedgames_ibfk_2` FOREIGN KEY (`gameid`) REFERENCES `games` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
