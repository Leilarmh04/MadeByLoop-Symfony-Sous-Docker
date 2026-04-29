-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : mer. 29 avr. 2026 à 13:59
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
-- Structure de la table `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `items` json NOT NULL,
  `updated_at` datetime NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cart`
--

INSERT INTO `cart` (`id`, `items`, `updated_at`, `user_id`) VALUES
(1, '[{\"quantity\": 2, \"productId\": 9}, {\"quantity\": 2, \"productId\": 10}]', '2026-04-29 07:48:43', 15),
(2, '[{\"quantity\": 1, \"productId\": 11}]', '2026-04-28 09:39:00', 14),
(3, '[{\"quantity\": 1, \"productId\": 9}]', '2026-04-29 08:42:46', 16);

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`, `slug`) VALUES
(1, 'Pièces uniques', 'pieces-uniques'),
(2, 'Accessoires', 'accessoires'),
(3, 'Décoration', 'decoration'),
(4, 'Bébés & enfants', 'bebes-enfants'),
(5, 'Mode & vêtements', 'mode-vetements'),
(6, 'Cadeaux', 'cadeaux');

-- --------------------------------------------------------

--
-- Structure de la table `conversation`
--

CREATE TABLE `conversation` (
  `id` int NOT NULL,
  `created_at` datetime NOT NULL,
  `buyer_id` int NOT NULL,
  `seller_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conversation`
--

INSERT INTO `conversation` (`id`, `created_at`, `buyer_id`, `seller_id`) VALUES
(1, '2026-04-22 10:31:11', 15, 14),
(2, '2026-04-29 08:38:19', 16, 14);

-- --------------------------------------------------------

--
-- Structure de la table `conversation_product`
--

CREATE TABLE `conversation_product` (
  `conversation_id` int NOT NULL,
  `product_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conversation_product`
--

INSERT INTO `conversation_product` (`conversation_id`, `product_id`) VALUES
(2, 11);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260302092120', '2026-03-02 09:29:28', 363),
('DoctrineMigrations\\Version20260304092857', '2026-03-04 09:30:26', 396),
('DoctrineMigrations\\Version20260330074939', '2026-03-30 07:51:04', 281),
('DoctrineMigrations\\Version20260330091205', '2026-03-30 10:20:58', 374),
('DoctrineMigrations\\Version20260413125432', '2026-04-13 13:01:09', 328),
('DoctrineMigrations\\Version20260413131109', '2026-04-13 13:18:20', 529),
('DoctrineMigrations\\Version20260413131724', NULL, NULL),
('DoctrineMigrations\\Version20260413133252', '2026-04-13 14:13:14', 892),
('DoctrineMigrations\\Version20260413145022', '2026-04-13 14:51:37', 1016),
('DoctrineMigrations\\Version20260414082511', NULL, NULL),
('DoctrineMigrations\\Version20260414082706', NULL, NULL),
('DoctrineMigrations\\Version20260414090602', NULL, NULL),
('DoctrineMigrations\\Version20260414101548', '2026-04-14 10:16:45', 1221),
('DoctrineMigrations\\Version20260414103241', '2026-04-14 10:33:10', 778),
('DoctrineMigrations\\Version20260414140130', '2026-04-14 14:02:40', 451),
('DoctrineMigrations\\Version20260415135414', '2026-04-15 13:56:02', 312),
('DoctrineMigrations\\Version20260421111025', '2026-04-21 11:17:49', 884),
('DoctrineMigrations\\Version20260421111715', NULL, NULL),
('DoctrineMigrations\\Version20260422141113', NULL, NULL),
('DoctrineMigrations\\Version20260424160000', '2026-04-24 12:31:25', 220),
('DoctrineMigrations\\Version20260428100000', '2026-04-27 10:23:36', 646),
('DoctrineMigrations\\Version20260428110000', '2026-04-27 14:30:16', 511);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_at` datetime NOT NULL,
  `is_read` tinyint NOT NULL,
  `conversation_id` int NOT NULL,
  `sender_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `content`, `sent_at`, `is_read`, `conversation_id`, `sender_id`) VALUES
(1, 'salam ca va mek?', '2026-04-22 10:32:00', 1, 1, 15),
(2, 'salam ca va mek?', '2026-04-22 10:32:03', 1, 1, 15),
(3, 'c\'est trop bien ca marche hehe', '2026-04-22 10:33:02', 1, 1, 15),
(4, 'hehehehehehehehe', '2026-04-22 14:40:13', 1, 1, 14),
(5, 'trop moche', '2026-04-29 08:38:34', 0, 2, 16),
(6, 'trop moche', '2026-04-29 08:38:37', 0, 2, 16),
(7, 'trop moche', '2026-04-29 08:38:37', 0, 2, 16),
(8, 'trop moche', '2026-04-29 08:38:38', 0, 2, 16),
(9, '15$ c bien', '2026-04-29 08:39:03', 0, 2, 16);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

CREATE TABLE `order` (
  `id` int NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` int NOT NULL,
  `items` json NOT NULL,
  `created_at` datetime NOT NULL,
  `buyer_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payment`
--

CREATE TABLE `payment` (
  `id` int NOT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `custom_order_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

CREATE TABLE `product` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `price` double NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `seller_id` int NOT NULL,
  `category_id` int NOT NULL,
  `sizes` json DEFAULT NULL,
  `colors` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `price`, `image`, `created_at`, `seller_id`, `category_id`, `sizes`, `colors`) VALUES
(5, 'bonnet', 'Bonnet en crochet fait main, doux et confortable, idéal pour garder la tête au chaud avec style. Chaque pièce est réalisée avec soin, ce qui la rend unique et agréable à porter au quotidien.', 50, '69e618f798488.webp', '2026-04-20 12:15:51', 14, 5, NULL, NULL),
(7, 'je ne sais pas', 'fjznfjnhzd;fjb<;wjdf;ljwdf;lùjb<ùdncjsldnflkzEJLzejlzlefljzNELFNLZENFKZLNLKEMQ/ZELZnnklefnlkz ;vjbùldncfùd', 100.5, 'gant-crochet-69e886e65fef0.webp', '2026-04-22 08:29:26', 14, 1, NULL, NULL),
(9, 'Woo Do-hwan', 'sdslkdkmjdkcnpijzefijsdlkjskdhfuoZEFOhfkjhqoh%%H%DOHD%H', 846, 'la-traque-dans-le-sang-69e888839cb02.webp', '2026-04-22 08:36:19', 14, 2, NULL, NULL),
(10, 'clary', 'ehbdfjhnslfhioshrdufoihfiiodkpdoihjedkflfnkjiuheodkfjeidhuuidokfjjjhjqzhpuihuoipihefjkjpjesk', 4546, 'shadow-hunter-clary-69e889166e786.webp', '2026-04-22 08:38:46', 14, 6, NULL, NULL),
(11, 's(sthugfy-(ury', 'ergqetyhsrthvyjtysxtyjs(èufgxsty', 5498, 'veste-crochet-69e889dadde53.webp', '2026-04-22 08:42:02', 14, 1, NULL, NULL),
(12, 'saliha B.', 'fgvfdcdrcfvgbhnj,hngtfredrfghjk;,jnhgbvfdsxxdcfvghj,nbvgcfdsxedrfgtyhujnbvcxdswdrftgyhujkjnbvcdxsdrftyhu', 85, 'Capture-d-ecran-2025-09-22-151235-69f08c7b2a764.png', '2026-04-28 10:26:18', 14, 5, '[\"M\", \"L\"]', '[\"Beige\", \"Nude\", \"Marron\"]'),
(13, 'dzddd', 'dddddd', 15, 'Capture-d-ecran-2025-08-04-193910-69f1c32e34d3d.png', '2026-04-29 08:37:02', 16, 6, '[\"Taille unique\"]', '[\"Beige\"]');

-- --------------------------------------------------------

--
-- Structure de la table `review`
--

CREATE TABLE `review` (
  `id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seller_role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` longtext COLLATE utf8mb4_unicode_ci,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `username`, `seller_role`, `bio`, `profile_picture`) VALUES
(1, 'rmhleila@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$OaLopk3K7l.oGruHVO3W5uaIMz6EvBJEBkAh2zTgwRrMn3hRVwjY6', 'Moi', 'both', NULL, NULL),
(12, 'leila.ramouhi@outlook.fr', '[\"ROLE_USER\"]', '$2y$13$CKWI740K13218p5zfgMNLOiJmwaJAw6qkxbTWSvJPnRW3uwwqvpye', 'hello', 'seller', NULL, NULL),
(13, 'alyiha@jfr.org', '[\"ROLE_USER\"]', '$2y$13$bke1GIDPvVVVZx8XS7Vz5eFsV1WU90AG0ltYxGUuqUZLqsYC5F4jm', 'Alyiha', 'both', NULL, NULL),
(14, 'tintin@milou.fr', '[\"ROLE_USER\"]', '$2y$13$cwIgnotfXhPFtVB1zS2UFu6QyL5GWRPyqDUU0LgGSf.8j7TwsUAxG', 'tintin95', 'seller', 'djcnsjdfnjsndlcs', NULL),
(15, 'saliha@lepoles.com', '[\"ROLE_USER\"]', '$2y$13$7GgKPsDcQnZb.j8AO6cMBOGyDTJMwxhoPCr5H18EDfl0xpEh0YcVG', 'ludovic', 'buyer', 'helllllllooooo', '69f0acbf5ae9b.png'),
(16, 'sinan@gmail.com', '[\"ROLE_USER\"]', '$2y$13$zZPZANF33vJmalJJpvV0wOo1sCg9y8WJQi6gomE5lrKMBJfzLasu6', 'sinan95', 'both', NULL, NULL),
(17, 'leila@gmail.com', '[\"ROLE_USER\"]', '$2y$13$5h0WX9/IqXMDc12a.C8GHe9lTQafIFUhoYKYoX3Ob43OovdIuHdkC', 'leila04', 'both', NULL, NULL),
(18, 'leilarmh@gmail.com', '[\"ROLE_USER\"]', '$2y$13$BR7iuPIyr.CXFbQ6RgqBAuiYuOT2wL8OgqCxtkfTj/cNgYM/OUANq', 'leila04', 'both', NULL, NULL),
(19, 'leila.moi@gmail.com', '[\"ROLE_USER\"]', '$2y$13$agxezd1O66GPEthvx1ng7.M7MSQ3Ar0R/f.8gbjCjjiBA1za1I9Be', 'leila95480', 'both', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `user_id` int NOT NULL,
  `product_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_favorites`
--

INSERT INTO `user_favorites` (`user_id`, `product_id`) VALUES
(14, 11),
(15, 9),
(15, 10);

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
-- Index pour la table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_BA388B7A76ED395` (`user_id`);

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `conversation`
--
ALTER TABLE `conversation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8A8E26E96C755722` (`buyer_id`),
  ADD KEY `IDX_8A8E26E98DE820D9` (`seller_id`);

--
-- Index pour la table `conversation_product`
--
ALTER TABLE `conversation_product`
  ADD PRIMARY KEY (`conversation_id`,`product_id`),
  ADD KEY `IDX_481C0AA79AC0396` (`conversation_id`),
  ADD KEY `IDX_481C0AA74584665A` (`product_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B6BD307F9AC0396` (`conversation_id`),
  ADD KEY `IDX_B6BD307FF624B39D` (`sender_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F52993986C755722` (`buyer_id`);

--
-- Index pour la table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_6D28840D684D8A5C` (`custom_order_id`);

--
-- Index pour la table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D34A04AD8DE820D9` (`seller_id`),
  ADD KEY `IDX_D34A04AD12469DE2` (`category_id`);

--
-- Index pour la table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_794381C64584665A` (`product_id`),
  ADD KEY `IDX_794381C6A76ED395` (`user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- Index pour la table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `IDX_E489ED11A76ED395` (`user_id`),
  ADD KEY `IDX_E489ED114584665A` (`product_id`);

--
-- Index pour la table `user_following`
--
ALTER TABLE `user_following`
  ADD PRIMARY KEY (`user_source`,`user_target`),
  ADD KEY `IDX_715F00073AD8644E` (`user_source`),
  ADD KEY `IDX_715F0007233D34C1` (`user_target`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `conversation`
--
ALTER TABLE `conversation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order`
--
ALTER TABLE `order`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product`
--
ALTER TABLE `product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `review`
--
ALTER TABLE `review`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `FK_BA388B7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `conversation`
--
ALTER TABLE `conversation`
  ADD CONSTRAINT `FK_8A8E26E96C755722` FOREIGN KEY (`buyer_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_8A8E26E98DE820D9` FOREIGN KEY (`seller_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `conversation_product`
--
ALTER TABLE `conversation_product`
  ADD CONSTRAINT `FK_481C0AA74584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_481C0AA79AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `FK_B6BD307F9AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`),
  ADD CONSTRAINT `FK_B6BD307FF624B39D` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `FK_F52993986C755722` FOREIGN KEY (`buyer_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `FK_6D28840D684D8A5C` FOREIGN KEY (`custom_order_id`) REFERENCES `order` (`id`);

--
-- Contraintes pour la table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_D34A04AD12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_D34A04AD8DE820D9` FOREIGN KEY (`seller_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `FK_794381C64584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_794381C6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `FK_E489ED114584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E489ED11A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

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
