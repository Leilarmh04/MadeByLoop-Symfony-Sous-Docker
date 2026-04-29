-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : mer. 29 avr. 2026 à 13:17
-- Version du serveur : 8.0.45
-- Version de PHP : 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `MadeByLoop`
--

-- --------------------------------------------------------

--
-- Structure de la table `user_following`
--

CREATE TABLE `user_following` (
  `user_source` int NOT NULL,
  `user_target` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user_following`
--

INSERT INTO `user_following` (`user_source`, `user_target`) VALUES
(14, 15),
(15, 14);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `user_following`
--
ALTER TABLE `user_following`
  ADD PRIMARY KEY (`user_source`,`user_target`),
  ADD KEY `IDX_715F00073AD8644E` (`user_source`),
  ADD KEY `IDX_715F0007233D34C1` (`user_target`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `user_following`
--
ALTER TABLE `user_following`
  ADD CONSTRAINT `FK_715F0007233D34C1` FOREIGN KEY (`user_target`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_715F00073AD8644E` FOREIGN KEY (`user_source`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
