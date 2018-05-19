-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 19 Mai 2018 à 16:40
-- Version du serveur :  5.7.11
-- Version de PHP :  7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `oauth_test`
--

-- --------------------------------------------------------

--
-- Structure de la table `testimonies`
--

CREATE TABLE `testimonies` (
  `id` int(11) NOT NULL,
  `title` varchar(80) NOT NULL,
  `description` varchar(255) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `longitude` float NOT NULL,
  `latitude` float NOT NULL,
  `annee` datetime DEFAULT CURRENT_TIMESTAMP,
  `validated` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `testimonies`
--

INSERT INTO `testimonies` (`id`, `title`, `description`, `id_user`, `url`, `longitude`, `latitude`, `annee`, `validated`) VALUES
(32, 'TÃ©moignage 2017', 'Oui', 38, 'http://localhost/img.png', 43.2854, 5.45358, '2017-06-14 00:00:00', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(20) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `name`, `password`, `email`, `role`, `created`) VALUES
(4, 'Claire', 'Claires', 'Fredon', 'Claire', '$2y$10$GQFSOEzj/IhqMfdA7hb.n.a6k9ThxFhV4Cqrcstgp6B0x8WbURUT6', 'claire@fredon.fr', 'ADMIN', '2018-05-04 19:03:15');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `testimonies`
--
ALTER TABLE `testimonies`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `testimonies`
--
ALTER TABLE `testimonies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
