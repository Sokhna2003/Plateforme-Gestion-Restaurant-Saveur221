-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 11 sep. 2026 à 18:38
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `restaurant_saveur221`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--
CREATE DATABASE IF NOT EXISTS restaurantsaveur221_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE restaurantsaveur221_db;
CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `note` int(11) NOT NULL CHECK (`note` between 1 and 5),
  `commentaire` text DEFAULT NULL,
  `date_avis` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `client_id`, `commande_id`, `note`, `commentaire`, `date_avis`) VALUES
(19, 1, 4, 5, 'Le repas était excellent. Je commanderai à nouveau sans hésiter !', '2026-09-11 13:47:39');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_ajout` datetime NOT NULL DEFAULT current_timestamp(),
  `supprime_le` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `image`, `date_ajout`, `supprime_le`) VALUES
(1, 'Plats', 'Plats principaux du restaurant', NULL, '2026-09-09 18:50:55', NULL),
(2, 'Boissons', 'Boissons fraîches et chaudes', NULL, '2026-09-09 18:50:55', NULL),
(3, 'Desserts', 'Desserts et pâtisseries', 'https://res.cloudinary.com/dhevj2qfc/image/upload/v1789035150/saveur221/categories/kxdxylrrqzavoz3kzk95.jpg', '2026-09-09 18:50:55', NULL),
(4, 'Sandwichs', 'Sandwichs et paninis', NULL, '2026-09-09 18:50:55', NULL),
(7, 'Pannet', NULL, 'https://res.cloudinary.com/dhevj2qfc/image/upload/v1788995168/saveur221/categories/v7qwotftgvdc8uadk5sp.jpg', '2026-09-09 19:23:30', NULL),
(21, 'Grillades & Rôtisserie', 'Découvrez nos viandes et poissons grillés au feu de bois, marinés aux épices locales et servis avec leurs accompagnements au choix', 'https://res.cloudinary.com/dhevj2qfc/image/upload/v1788994126/sr6mtll50sdmkalvyvqj.jpg', '2026-09-09 22:48:41', NULL),
(23, 'Fast-Food & Burgers', 'Des burgers généreux, sandwichs et wraps préparés à la minute pour vos pauses gourmandes et vos repas sur le pouce', 'https://res.cloudinary.com/dhevj2qfc/image/upload/v1788995268/saveur221/categories/pezugxnptsnjlj4qcrmd.jpg', '2026-09-09 23:07:43', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `email`, `telephone`, `adresse`, `mot_de_passe`, `date_inscription`) VALUES
(1, 'Diallo', 'Fatou', 'fatou.diallo@test.sn', '771234567', 'Dakar, Sénégal', '$2y$10$/SVZ1/JjepoV8buo9shAhOrmHrOxDn.7o3nvgZe9O8ndoSin0plq6', '2026-08-30 20:27:36'),
(2, 'Ndiaye', 'Moussa', 'moussa.ndiaye@test.sn', '781234567', 'Thiès, Sénégal', '$2y$10$/SVZ1/JjepoV8buo9shAhOrmHrOxDn.7o3nvgZe9O8ndoSin0plq6', '2026-08-30 20:27:36'),
(3, 'Sow', 'Awa', 'awa.sow@test.sn', '761234567', 'Rufisque, Sénégal', '$2y$10$/SVZ1/JjepoV8buo9shAhOrmHrOxDn.7o3nvgZe9O8ndoSin0plq6', '2026-08-30 20:27:36');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `date_commande` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` enum('EN_ATTENTE','EN_PREPARATION','PRETE','RETIREE','ANNULEE') NOT NULL DEFAULT 'EN_ATTENTE',
  `montant_total` decimal(10,2) NOT NULL DEFAULT 0.00 CHECK (`montant_total` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `client_id`, `date_commande`, `statut`, `montant_total`) VALUES
(1, 1, '2026-08-30 20:32:50', 'EN_PREPARATION', 7500.00),
(2, 2, '2026-08-30 20:34:06', 'ANNULEE', 3000.00),
(3, 3, '2026-08-30 20:34:59', 'PRETE', 3000.00),
(4, 1, '2026-08-30 20:35:45', 'RETIREE', 3200.00);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_commandes`
--

CREATE TABLE `ligne_commandes` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL CHECK (`quantite` > 0),
  `prix_unitaire` decimal(10,2) NOT NULL CHECK (`prix_unitaire` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ligne_commandes`
--

INSERT INTO `ligne_commandes` (`id`, `commande_id`, `produit_id`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 1, 2, 3500.00),
(2, 1, 5, 1, 500.00),
(3, 2, 2, 1, 3000.00),
(4, 3, 8, 3, 1000.00),
(5, 4, 3, 1, 3200.00);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL CHECK (`montant` > 0),
  `date_paiement` datetime NOT NULL DEFAULT current_timestamp(),
  `mode_paiement` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `commande_id`, `montant`, `date_paiement`, `mode_paiement`) VALUES
(12, 4, 3200.00, '2026-09-10 17:09:37', 'Wave');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL CHECK (`prix` >= 0),
  `quantite_stock` int(11) NOT NULL DEFAULT 0 CHECK (`quantite_stock` >= 0),
  `seuil_alerte` int(11) NOT NULL DEFAULT 5,
  `categorie_id` int(11) NOT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `date_ajout` datetime DEFAULT NULL,
  `supprime_le` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `libelle`, `description`, `prix`, `quantite_stock`, `seuil_alerte`, `categorie_id`, `disponible`, `image`, `date_ajout`, `supprime_le`) VALUES
(1, 'Thieboudienne', 'Riz au poisson, sauce tomate, légumes', 3500.00, 40, 5, 1, 1, NULL, '2026-09-09 23:53:05', NULL),
(2, 'Yassa Poulet', 'Poulet mariné au citron et oignons', 3000.00, 15, 5, 1, 1, NULL, '2026-09-09 23:53:05', NULL),
(3, 'Mafé', 'Viande sauce arachide', 3200.00, 3, 5, 1, 1, NULL, '2026-09-09 23:53:05', NULL),
(4, 'Poisson braisé', 'Poisson grillé accompagné de frites', 4000.00, 0, 5, 1, 0, NULL, '2026-09-09 23:53:05', NULL),
(5, 'Bissap', 'Jus d\'hibiscus glacé', 500.00, 30, 10, 2, 1, NULL, '2026-09-09 23:53:05', NULL),
(6, 'Bouye', 'Jus de pain de singe', 500.00, 24, 10, 2, 1, 'https://res.cloudinary.com/dhevj2qfc/image/upload/v1789004074/saveur221/produits/dcvges317azhtpsknjnt.jpg', '2026-09-09 23:53:05', NULL),
(7, 'Coca-Cola', 'Canette 33cl', 600.00, 10, 10, 2, 1, NULL, '2026-09-09 23:53:05', NULL),
(8, 'Thiakry', 'Dessert au mil et lait caillé', 1000.00, 12, 5, 3, 1, NULL, '2026-09-09 23:53:05', NULL),
(9, 'Salade de fruits', 'Fruits frais de saison', 1500.00, 0, 5, 3, 0, 'https://res.cloudinary.com/dhevj2qfc/image/upload/v1789002221/saveur221/produits/bqmejuobfwea64axadui.jpg', '2026-09-09 23:53:05', NULL),
(10, 'Chawarma poulet', 'Chawarma viande de poulet, sauce blanche', 2000.00, 10, 3, 1, 1, '', '2026-09-09 23:53:05', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `libelle`) VALUES
(1, 'ADMIN'),
(2, 'GERANT');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role_id`, `actif`, `date_creation`) VALUES
(1, 'Diop', 'Awa', 'admin@saveur221.sn', '$2y$10$2IIESCMTsLfaBRgz.XE3i.1fsfjC.2iPMjGyZX97bRRovx33gH8pG', 1, 1, '2026-08-29 17:25:31'),
(2, 'Faye', 'Ibrahima', 'ibrahima.faye@saveur221.sn', '$2y$10$aVBIoWUWzYRNfjeVinan7e9QDyNKzvWdwiFGKPAQyKI/IKdgA/HYy', 2, 1, '2026-08-30 21:03:44'),
(3, 'Sarr', 'Modou', 'modou.sarr@saveur221.sn', '$2y$10$aVBIoWUWzYRNfjeVinan7e9QDyNKzvWdwiFGKPAQyKI/IKdgA/HYy', 2, 0, '2026-08-30 21:10:10');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `commande_id` (`commande_id`),
  ADD KEY `fk_avis_client` (`client_id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_commande_client` (`client_id`);

--
-- Index pour la table `ligne_commandes`
--
ALTER TABLE `ligne_commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ligne_commande` (`commande_id`),
  ADD KEY `fk_ligne_produit` (`produit_id`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_paiement_commande` (`commande_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produit_categorie` (`categorie_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_utilisateur_role` (`role_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `ligne_commandes`
--
ALTER TABLE `ligne_commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `fk_avis_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `fk_avis_commande` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`);

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `fk_commande_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Contraintes pour la table `ligne_commandes`
--
ALTER TABLE `ligne_commandes`
  ADD CONSTRAINT `fk_ligne_commande` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ligne_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `fk_paiement_commande` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `fk_produit_categorie` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`);

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `fk_utilisateur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

