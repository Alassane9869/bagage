-- ========================================================
-- DUMP BASE DE DONNÉES PRODUCTION : sc2vuxe8870_bagage
-- DIARRA TRANSPORT - GESTION DES BAGAGES
-- Généré pour import phpMyAdmin cPanel
-- ========================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


-- --------------------------------------------------------
-- Structure de la table `roles`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `permissions` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `login` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `agence` varchar(255) DEFAULT NULL,
  `statut` varchar(50) DEFAULT 'actif',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login_unique` (`login`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `societe`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `societe`;
CREATE TABLE `societe` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `raison_sociale` varchar(200) NOT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `pays` varchar(100) DEFAULT 'Mali',
  `site_web` varchar(255) DEFAULT NULL,
  `numero_rccm` varchar(100) DEFAULT NULL,
  `numero_ifu` varchar(100) DEFAULT NULL,
  `devise` varchar(10) DEFAULT 'FCFA',
  `fuseau_horaire` varchar(50) DEFAULT 'Africa/Bamako',
  `logo_path` varchar(255) DEFAULT 'logos/logo_societe_1782763691.png',
  `mentions_recu` text DEFAULT NULL,
  `guichet_principal` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `clients`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clients_telephone_index` (`telephone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `voyages`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `voyages`;
CREATE TABLE `voyages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ville_depart` varchar(100) NOT NULL,
  `ville_arrivee` varchar(100) NOT NULL,
  `date_depart` date NOT NULL,
  `heure_depart` varchar(20) DEFAULT NULL,
  `programme` varchar(100) DEFAULT NULL,
  `numero_bus` varchar(50) DEFAULT NULL,
  `statut_voyage` varchar(50) DEFAULT 'planifie',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voyages_date_depart_index` (`date_depart`),
  KEY `voyages_statut_voyage_index` (`statut_voyage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `bagages`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `bagages`;
CREATE TABLE `bagages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code_bagage` varchar(50) NOT NULL,
  `client_id` bigint(20) unsigned DEFAULT NULL,
  `voyage_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `nombre_colis` int(10) unsigned NOT NULL DEFAULT 1,
  `nature` varchar(100) DEFAULT NULL,
  `poids` decimal(8,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remise` decimal(10,2) NOT NULL DEFAULT 0.00,
  `numero_billet` varchar(100) DEFAULT NULL,
  `statut` enum('enregistre','embarque') NOT NULL DEFAULT 'enregistre',
  `date_enregistrement` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bagages_code_bagage_unique` (`code_bagage`),
  KEY `bagages_client_id_index` (`client_id`),
  KEY `bagages_voyage_id_index` (`voyage_id`),
  KEY `bagages_user_id_index` (`user_id`),
  KEY `bagages_statut_index` (`statut`),
  KEY `bagages_date_enregistrement_index` (`date_enregistrement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `colis`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `colis`;
CREATE TABLE `colis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bagage_id` bigint(20) unsigned NOT NULL,
  `numero_colis` varchar(100) NOT NULL,
  `poids` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `colis_bagage_id_index` (`bagage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `tickets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bagage_id` bigint(20) unsigned NOT NULL,
  `numero_ticket` varchar(100) NOT NULL,
  `qr_code` text DEFAULT NULL,
  `imprime_a` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_numero_ticket_unique` (`numero_ticket`),
  KEY `tickets_bagage_id_index` (`bagage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `suivis`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `suivis`;
CREATE TABLE `suivis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bagage_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `localisation` varchar(100) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `commentaire` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suivis_bagage_id_index` (`bagage_id`),
  KEY `suivis_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `caisses`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `caisses`;
CREATE TABLE `caisses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `date_caisse` date NOT NULL,
  `total_bagages` int(10) unsigned NOT NULL DEFAULT 0,
  `recettes` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remises` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `caisses_user_id_date_caisse_unique` (`user_id`,`date_caisse`),
  KEY `caisses_date_caisse_index` (`date_caisse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `login_histories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `login_histories`;
CREATE TABLE `login_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login_histories_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `activity_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `detail` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `cache`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `jobs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure de la table `migrations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- DONNÉES INITIALES (SEEDERS)
-- ========================================================

-- Données de `migrations`
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000001_create_roles_table', 1),
(5, '2026_01_01_000002_update_users_table', 1),
(6, '2026_01_01_000003_create_clients_table', 1),
(7, '2026_01_01_000004_create_voyages_table', 1),
(8, '2026_01_01_000005_create_bagages_table', 1),
(9, '2026_01_01_000006_create_colis_table', 1),
(10, '2026_01_01_000007_create_tickets_table', 1),
(11, '2026_01_01_000008_create_suivis_table', 1),
(12, '2026_01_01_000009_create_caisses_table', 1),
(13, '2026_01_01_000010_create_login_histories_table', 1),
(14, '2026_01_01_000011_create_activity_logs_table', 1),
(15, '2026_01_01_000012_create_societe_table', 1),
(16, '2026_06_14_000001_make_clients_nom_nullable', 1),
(17, '2026_06_25_000001_simplify_bagages_statut_enum', 1),
(18, '2026_06_25_000003_add_login_column_to_users', 1),
(19, '2026_06_26_000001_reassign_and_remove_obsolete_roles', 1),
(20, '2026_06_26_000002_add_fields_to_societe_table', 1);

-- Données de `roles`
INSERT INTO `roles` (`id`, `nom`, `slug`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'Administrateur', 'administrateur', '\"[\\\"*\\\"]\"', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(2, 'Agent Bagages', 'agent_bagages', '\"[\\\"bagages\\\",\\\"embarquement\\\",\\\"caisse\\\"]\"', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(3, 'Contrôleur', 'controleur', '\"[\\\"consultation\\\",\\\"suivi\\\"]\"', '2026-10-11 16:34:16', '2026-10-11 16:34:16');

-- Données de `users`
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `prenom`, `nom`, `telephone`, `role_id`, `agence`, `statut`, `login`) VALUES
(1, 'DIARRA ADMIN', 'admin@diarratransport.ml', NULL, '$2y$12$wpI8zsQxuIW0AQ.aAEnMYe1ojGaSpRswgVbasKUvPMfreWJoCz2hq', NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16', 'Diarra', 'Admin', NULL, 1, 'Siège', 'actif', 'admin'),
(2, 'YACOU DIAKITE', 'yacou.diakite@diarratransport.ml', NULL, '$2y$12$Zb3elmO9JZ9ABbglbU5fw.KHIk9D2PPtVj./tcHXwcpdlI7y56ZvS', NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16', 'Yacou', 'Diakite', NULL, 2, 'Bamako Central', 'actif', 'yacou.diakite');

-- Données de `societe`
INSERT INTO `societe` (`id`, `raison_sociale`, `telephone`, `email`, `adresse`, `guichet_principal`, `created_at`, `updated_at`, `ville`, `pays`, `site_web`, `numero_rccm`, `numero_ifu`, `devise`, `fuseau_horaire`, `logo_path`, `mentions_recu`) VALUES
(1, 'DIARRA TRANSPORT', '(223) 20802142', 'contact@diarratransport.ml', 'Près de la maternité Sogoniko – Bamako, Mali', '20802142', '2026-10-11 16:34:16', '2026-10-11 17:24:07', NULL, 'Mali', NULL, NULL, NULL, 'FCFA', 'Africa/Bamako', 'logos/logo_societe_1782763691.png', NULL);

-- Données de `clients`
INSERT INTO `clients` (`id`, `nom`, `prenom`, `telephone`, `adresse`, `numero_piece`, `created_at`, `updated_at`) VALUES
(1, 'Coulibaly', 'Mamadou', '+223 76 11 22 33', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(2, 'Traoré', 'Awa', '+223 65 44 55 66', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(3, 'Diallo', 'Oumar', '+223 79 77 88 99', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(4, 'Keïta', 'Mariam', '+223 66 12 34 56', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(5, 'Sanogo', 'Ibrahim', '+223 70 98 76 54', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(6, 'Cissé', 'Fatoumata', '+223 75 33 22 11', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(7, 'Bah', 'Boubacar', '+223 61 55 44 33', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(8, 'Sidibé', 'Aminata', '+223 72 66 77 88', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(9, 'Dembélé', 'Moussa', '+223 77 55 66 44', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(10, 'Fofana', 'Rokia', '+223 63 44 33 22', NULL, NULL, '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(11, 'Traore', 'Moussa', '+223 70 00 00 00', NULL, NULL, '2026-10-11 17:12:32', '2026-10-11 17:12:32'),
(12, 'Traore', 'Moussa', '+223 70 00 00 00', NULL, NULL, '2026-10-11 17:15:51', '2026-10-11 17:15:51'),
(13, 'Traore', 'Moussa', '+223 70 00 00 00', NULL, NULL, '2026-10-11 17:18:25', '2026-10-11 17:18:25'),
(14, 'Traore', 'Moussa', '+223 70 00 00 00', NULL, NULL, '2026-10-11 17:26:55', '2026-10-11 17:26:55');

-- Données de `voyages`
INSERT INTO `voyages` (`id`, `ville_depart`, `ville_arrivee`, `date_depart`, `heure_depart`, `programme`, `numero_bus`, `statut_voyage`, `created_at`, `updated_at`) VALUES
(1, 'Bamako', 'Kayes', '2026-06-03 00:00:00', '08:00', 'BAMAKO-KAYES 08H', 'BM-001', 'planifie', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(2, 'Bamako', 'Kayes', '2026-06-03 00:00:00', '10:00', 'BAMAKO-KAYES 10H', 'BM-002', 'planifie', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(3, 'Bamako', 'Dakar', '2026-06-03 00:00:00', '06:00', 'BAMAKO-DAKAR 06H', 'BM-003', 'planifie', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(4, 'Bamako', 'Sikasso', '2026-06-03 00:00:00', '07:00', 'BAMAKO-SIKASSO 07H', 'SK-001', 'planifie', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(5, 'Bamako', 'Abidjan', '2026-06-03 00:00:00', '08:00', 'BAMAKO-ABIDJAN 08H', 'AB-001', 'planifie', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(6, 'Bamako', 'Kayes', '2026-10-11 00:00:00', NULL, 'BAMAKO-KAYES 08H', NULL, 'planifie', '2026-10-11 17:12:32', '2026-10-11 17:12:32'),
(7, 'Bamako', 'Kayes', '2026-10-11 00:00:00', NULL, 'BAMAKO-KAYES 08H', NULL, 'planifie', '2026-10-11 17:15:51', '2026-10-11 17:15:51'),
(8, 'Bamako', 'Kayes', '2026-10-11 00:00:00', NULL, 'BAMAKO-KAYES 08H', NULL, 'planifie', '2026-10-11 17:18:25', '2026-10-11 17:18:25'),
(9, 'Bamako', 'Kayes', '2026-10-11 00:00:00', NULL, 'BAMAKO-KAYES 08H', NULL, 'planifie', '2026-10-11 17:26:55', '2026-10-11 17:26:55');

-- Données de `bagages`
INSERT INTO `bagages` (`id`, `code_bagage`, `client_id`, `voyage_id`, `user_id`, `nombre_colis`, `nature`, `poids`, `description`, `montant`, `remise`, `numero_billet`, `statut`, `date_enregistrement`, `created_at`, `updated_at`) VALUES
(1, 'BG-2026-00001', 1, 1, 2, 3, 'Sac', 11.5, 'Effets personnels', 22491, 0, '28190001/26', 'enregistre', '2026-10-10 11:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(2, 'BG-2026-00002', 2, 2, 2, 2, 'Carton', 19.5, 'Effets personnels', 6630, 0, '28190002/26', 'embarque', '2026-10-08 23:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(3, 'BG-2026-00003', 3, 3, 2, 2, 'Colis fragile', 27.5, 'Effets personnels', 10174, 2000, '28190003/26', 'enregistre', '2026-10-11 16:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(4, 'BG-2026-00004', 4, 4, 2, 2, 'Marchandise', 18.5, 'Effets personnels', 13964, 0, '28190004/26', 'embarque', '2026-10-11 02:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(5, 'BG-2026-00005', 5, 5, 2, 1, 'Valise', 24.5, 'Effets personnels', 7417, 0, '28190005/26', 'enregistre', '2026-10-10 13:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(6, 'BG-2026-00006', 6, 1, 2, 2, 'Sac', 28.5, 'Effets personnels', 10422, 2000, '28190006/26', 'enregistre', '2026-10-10 03:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(7, 'BG-2026-00007', 7, 2, 2, 2, 'Carton', 15.5, 'Effets personnels', 15580, 0, '28190007/26', 'embarque', '2026-10-10 04:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(8, 'BG-2026-00008', 8, 3, 2, 1, 'Colis fragile', 26.5, 'Effets personnels', 5325, 0, '28190008/26', 'enregistre', '2026-10-11 04:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(9, 'BG-2026-00009', 9, 4, 2, 2, 'Marchandise', 19.5, 'Effets personnels', 13788, 2000, '28190009/26', 'embarque', '2026-10-11 01:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(10, 'BG-2026-00010', 10, 5, 2, 2, 'Valise', 30.5, 'Effets personnels', 12784, 0, '28190010/26', 'enregistre', '2026-10-08 21:34:16', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(11, 'BG-2026-00011', 11, 6, 1, 2, 'Valise', 15.5, NULL, 5000, 500, NULL, 'enregistre', '2026-10-11 17:12:32', '2026-10-11 17:12:32', '2026-10-11 17:12:32'),
(12, 'BG-2026-00012', 12, 7, 1, 2, 'Valise', 15.5, NULL, 5000, 500, NULL, 'enregistre', '2026-10-11 17:15:51', '2026-10-11 17:15:51', '2026-10-11 17:15:51'),
(13, 'BG-2026-00013', 13, 8, 1, 2, 'Valise', 15.5, NULL, 5000, 500, NULL, 'enregistre', '2026-10-11 17:18:25', '2026-10-11 17:18:25', '2026-10-11 17:18:25'),
(14, 'BG-2026-00014', 14, 9, 1, 2, 'Valise', 15.5, NULL, 5000, 500, NULL, 'enregistre', '2026-10-11 17:26:55', '2026-10-11 17:26:55', '2026-10-11 17:26:55');

-- Données de `colis`
INSERT INTO `colis` (`id`, `bagage_id`, `numero_colis`, `created_at`, `updated_at`) VALUES
(1, 1, 'BG-2026-00001-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(2, 1, 'BG-2026-00001-C2', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(3, 1, 'BG-2026-00001-C3', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(4, 2, 'BG-2026-00002-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(5, 2, 'BG-2026-00002-C2', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(6, 3, 'BG-2026-00003-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(7, 3, 'BG-2026-00003-C2', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(8, 4, 'BG-2026-00004-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(9, 4, 'BG-2026-00004-C2', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(10, 5, 'BG-2026-00005-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(11, 6, 'BG-2026-00006-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(12, 6, 'BG-2026-00006-C2', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(13, 7, 'BG-2026-00007-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(14, 7, 'BG-2026-00007-C2', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(15, 8, 'BG-2026-00008-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(16, 9, 'BG-2026-00009-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(17, 9, 'BG-2026-00009-C2', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(18, 10, 'BG-2026-00010-C1', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(19, 10, 'BG-2026-00010-C2', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(20, 11, 'BG-2026-00011-C1', '2026-10-11 17:12:32', '2026-10-11 17:12:32'),
(21, 11, 'BG-2026-00011-C2', '2026-10-11 17:12:32', '2026-10-11 17:12:32'),
(22, 12, 'BG-2026-00012-C1', '2026-10-11 17:15:51', '2026-10-11 17:15:51'),
(23, 12, 'BG-2026-00012-C2', '2026-10-11 17:15:51', '2026-10-11 17:15:51'),
(24, 13, 'BG-2026-00013-C1', '2026-10-11 17:18:25', '2026-10-11 17:18:25'),
(25, 13, 'BG-2026-00013-C2', '2026-10-11 17:18:25', '2026-10-11 17:18:25'),
(26, 14, 'BG-2026-00014-C1', '2026-10-11 17:26:55', '2026-10-11 17:26:55'),
(27, 14, 'BG-2026-00014-C2', '2026-10-11 17:26:55', '2026-10-11 17:26:55');

-- Données de `suivis`
INSERT INTO `suivis` (`id`, `bagage_id`, `user_id`, `localisation`, `statut`, `commentaire`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(2, 2, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(3, 2, 2, 'Bamako – Bus BM-002', 'embarque', 'Chargé dans le bus', '2026-10-10 21:34:16', '2026-10-10 01:34:16'),
(4, 3, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(5, 4, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(6, 4, 2, 'Bamako – Bus SK-001', 'embarque', 'Chargé dans le bus', '2026-10-10 15:34:16', '2026-10-10 01:34:16'),
(7, 5, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(8, 6, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(9, 7, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(10, 7, 2, 'Bamako – Bus BM-002', 'embarque', 'Chargé dans le bus', '2026-10-11 07:34:16', '2026-10-10 23:34:16'),
(11, 8, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(12, 9, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(13, 9, 2, 'Bamako – Bus SK-001', 'embarque', 'Chargé dans le bus', '2026-10-10 06:34:16', '2026-10-10 10:34:16'),
(14, 10, 2, 'Bamako', 'enregistre', 'Enregistrement initial', '2026-10-11 16:34:16', '2026-10-11 16:34:16'),
(15, 11, 1, 'Bamako', 'enregistre', 'Bagage enregistré à l\'agence', '2026-10-11 17:12:32', '2026-10-11 17:12:32'),
(16, 12, 1, 'Bamako', 'enregistre', 'Bagage enregistré à l\'agence', '2026-10-11 17:15:51', '2026-10-11 17:15:51'),
(17, 13, 1, 'Bamako', 'enregistre', 'Bagage enregistré à l\'agence', '2026-10-11 17:18:25', '2026-10-11 17:18:25'),
(18, 14, 1, 'Bamako', 'enregistre', 'Bagage enregistré à l\'agence', '2026-10-11 17:26:55', '2026-10-11 17:26:55');

-- Données de `caisses`
INSERT INTO `caisses` (`id`, `user_id`, `date_caisse`, `total_bagages`, `recettes`, `remises`, `net`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-10-11 00:00:00', 4, 20000, 2000, 18000, '2026-10-11 17:12:32', '2026-10-11 17:26:55');

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
