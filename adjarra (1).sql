-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mer. 21 mai 2025 à 18:25
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `adjarra`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `ID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`ID`, `Email`, `Password`) VALUES
(1, 'Admin@gmail.com', 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `circuits`
--

CREATE TABLE `circuits` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price_economy` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_comfort` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_premium` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_participants` int(11) NOT NULL DEFAULT 10,
  `duration` int(11) NOT NULL DEFAULT 1,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `guide_id` int(11) DEFAULT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `transporter_id` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `circuits`
--

INSERT INTO `circuits` (`id`, `name`, `description`, `price_economy`, `price_comfort`, `price_premium`, `max_participants`, `duration`, `start_date`, `end_date`, `guide_id`, `hotel_id`, `transporter_id`, `creation_date`, `status`) VALUES
(10, 'Sur les Traces du Passé Du Bénin', 'Sur les Traces du Passé Du Bénin', 10000.00, 30000.00, 50000.00, 10, 2, '2025-06-20', '2025-06-22', 13, 26, 5, '2025-05-19 12:07:00', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `circuit_sites`
--

CREATE TABLE `circuit_sites` (
  `circuit_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `circuit_sites`
--

INSERT INTO `circuit_sites` (`circuit_id`, `site_id`) VALUES
(1, 1),
(1, 2),
(2, 2),
(3, 2),
(3, 11),
(4, 1),
(4, 11),
(4, 12),
(5, 11),
(5, 12),
(6, 1),
(6, 2),
(6, 11),
(6, 12),
(7, 32),
(7, 37),
(7, 38),
(8, 28),
(8, 30),
(8, 31),
(8, 35),
(8, 38),
(9, 32),
(9, 37),
(9, 39),
(9, 42),
(10, 31),
(10, 32),
(10, 35),
(10, 36),
(10, 37),
(10, 39),
(10, 42);

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `picture_event` varchar(111) NOT NULL,
  `label_event` varchar(255) NOT NULL,
  `description_event` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `amount_event` double NOT NULL,
  `number_available_event` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `picture_event` varchar(111) NOT NULL,
  `label_event` varchar(255) NOT NULL,
  `description_event` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `amount_event` double NOT NULL,
  `number_available_event` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `picture_event`, `label_event`, `description_event`, `start_date`, `end_date`, `localisation`, `amount_event`, `number_available_event`) VALUES
(10, '68222b791fb6a.jpg', 'Vodoun days', 'VodounDays est un événement culturel et spirituel unique, dédié à la découverte et à la valorisation du patrimoine vaudou. Organisé autour du 10 janvier, journée nationale des religions endogènes au Bénin, ce festival rassemble cérémonies traditionnelles, danses sacrées, rituels, expositions d’art, et rencontres avec les dignitaires du culte. C’est une plongée authentique dans un univers mystique, vibrant de couleurs, de sons et de symboles, où spiritualité et culture ancestrale se rejoignent pour faire vivre les traditions du peuple béninois.', '2026-01-08 18:03:00', '2026-01-10 18:03:00', 'Ouidah, Bénin', 0, 1000),
(11, '68222cd3e01c5.jpeg', 'Festival International de Théâtre du Bénin (FITHEB)', 'Un des plus grands festivals de théâtre en Afrique francophone. Il a lieu tous les deux ans à Cotonou, Porto-Novo et dans d’autres villes.', '2025-06-20 18:13:00', '2025-06-22 18:14:00', 'Cotonou, Bénin', 1000, 100),
(13, '68222f3833deb.jpg', 'Urban Cult (Cotonou)', 'Festival de cultures urbaines (hip-hop, danse, graff, mode, rap).', '2025-06-26 18:22:00', '2025-06-27 18:22:00', 'Cotonou, Bénin', 1000, 100),
(14, '682230030d8a6.jpg', 'Festival des Masques de Dassa-Zoumè', 'Présentation spectaculaire des danses masquées traditionnelles du centre du Bénin.', '2025-06-28 18:27:00', '2025-06-29 18:27:00', 'Porto-novo, Bénin', 1000, 100),
(15, '682230fe883c3.jpeg', 'Festival Sô Ava (Ganvié)', 'Fête traditionnelle autour de la culture lacustre des Tofinous, avec danses sur pirogues, chants et rituels sur l’eau.', '2025-07-10 18:30:00', '2025-07-12 18:30:00', 'Sô Ava, Bénin', 1000, 100),
(17, '6823655c186d6.jpg', 'Porto Chill', 'Viens vivre une expérience unique à Porto- Novomusique ,à Porto-Novo ! Ambiance décontractée, musique, rires et bonne humeur sont au programme. Que tu sois là pour chiller entre potes , rencontrer du monde ou juste profiter d&#039;une ambiance zen , Porto Chill est l&#039;événementest l&#039;événement qu&#039;il te faut .\r\n\r\n', '2025-05-23 16:28:00', '2025-05-30 16:28:00', 'Porto-novo, Bénin', 15000, 100),
(18, '682504af8bc7a.png', 'WeLoveEya', 'Grands festivals de musique urbaine et afrobeat en Afrique de l&#039;Ouest, organisés chaque année à Cotonou, au Bénin. Lancé en 2022 par Lionel Talon, promoteur du Centre culturel et sportif Eya et fils du président béninois, l&#039;événement se déroule à la Place de l&#039;Amazone et attire des dizaines de milliers de festivaliers. ', '2025-11-14 21:58:00', '2025-11-16 21:58:00', 'Cotonou, Bénin', 10000, 1000);

-- --------------------------------------------------------

--
-- Structure de la table `guides`
--

CREATE TABLE `guides` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('homme','femme','autre') NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `experience_years` int(2) NOT NULL,
  `education` varchar(255) DEFAULT NULL,
  `languages` varchar(255) DEFAULT NULL,
  `specializations` varchar(255) DEFAULT NULL,
  `regions` varchar(255) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `guides`
--

INSERT INTO `guides` (`id`, `first_name`, `last_name`, `birth_date`, `gender`, `address`, `city`, `phone`, `email`, `id_number`, `license_number`, `experience_years`, `education`, `languages`, `specializations`, `regions`, `daily_rate`, `biography`, `profile_photo`, `documents`, `username`, `password`, `status`, `created_at`) VALUES
(13, 'Djidjoho Prince Junior', 'AGBODJOGBE', '2002-01-31', 'homme', 'Porto-Novo, Bénin', 'Porto-Novo', '0156184787', 'leaderagj2021@gmail.com', NULL, NULL, 5, NULL, 'Français,Fon', 'Histoire,Culture', 'Adjarra,Porto-Novo', 100000.00, NULL, 'uploads/guides/1747651851_junio.jpeg', NULL, 'dagbodjogbe35', '$2y$10$nq0mfQz9ZwAFR68V16ukuuOylyb4l1Dr.u6oxHDSWBojGUSzsuH4y', 'approved', '2025-05-19 10:50:51');

-- --------------------------------------------------------

--
-- Structure de la table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `hotel_name` varchar(255) NOT NULL,
  `creation_year` int(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `region` varchar(100) DEFAULT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_phone` varchar(50) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `hotel_category` varchar(50) DEFAULT NULL,
  `room_count` int(11) NOT NULL,
  `has_wifi` tinyint(1) DEFAULT 0,
  `has_parking` tinyint(1) DEFAULT 0,
  `has_pool` tinyint(1) DEFAULT 0,
  `has_restaurant` tinyint(1) DEFAULT 0,
  `has_ac` tinyint(1) DEFAULT 0,
  `has_conference` tinyint(1) DEFAULT 0,
  `price_range` varchar(50) NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `description` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo_files` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `hotels`
--

INSERT INTO `hotels` (`id`, `hotel_name`, `creation_year`, `address`, `city`, `region`, `contact_name`, `contact_phone`, `contact_email`, `website`, `hotel_category`, `room_count`, `has_wifi`, `has_parking`, `has_pool`, `has_restaurant`, `has_ac`, `has_conference`, `price_range`, `check_in_time`, `check_out_time`, `description`, `username`, `password`, `status`, `registration_date`, `photo_files`) VALUES
(26, 'Sofitel', 2024, 'Cotonou, Bénin', 'Cotonou', NULL, 'Fadyl BOURAIMA', '51851525', 'fadylbouraima4@gmail.com', NULL, '5 étoiles', 198, 1, 1, 1, 1, 1, 1, 'Luxe', NULL, NULL, 'L\'hôtel expose 150 œuvres d\'art d\'artistes béninois, transformant ses espaces en une véritable galerie d\'art. Il dispose également de neuf salles de réunion, dont Le Dôme , centre international de conférence rénové pouvant accueillir jusqu\'à 700 personnes.', 'sofitel80', '$2y$10$8HEsXORWs1TDrzV7QFLQKeYRoCzdVjjVNrFL9Wd0LUoqH0112FZx6', 'approved', '2025-05-21 12:38:12', '[\"uploads\\/hotels\\/photos\\/1747831092_sofi.jpg\"]');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `circuit_id` int(11) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `nombre_voyageurs` int(11) NOT NULL DEFAULT 1,
  `date_voyage` date NOT NULL,
  `type_forfait` enum('economy','comfort','premium') NOT NULL,
  `demandes_speciales` text DEFAULT NULL,
  `methode_paiement` varchar(50) NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `statut` enum('en_attente','confirmee','annulee') NOT NULL DEFAULT 'en_attente',
  `date_reservation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `circuit_id`, `prenom`, `nom`, `email`, `telephone`, `nombre_voyageurs`, `date_voyage`, `type_forfait`, `demandes_speciales`, `methode_paiement`, `prix_total`, `statut`, `date_reservation`) VALUES
(1, 1, 'spero', 'FAIHUN', 'sperofaihun00@gmail.com', '96829051', 1, '2025-04-24', 'economy', '', 'mobile_money', 12.00, 'confirmee', '2025-04-21 08:07:17'),
(2, 1, 'spero', 'FAIHUN', 's@gmail.com', '41890201', 1, '2025-04-27', 'economy', '', 'mobile_money', 12.00, 'confirmee', '2025-04-21 09:40:12'),
(3, 5, 'Djidjoho Prince Junior', 'AGBODJOGBE', 'leaderagj2021@gmail.com', '56184787', 1, '2025-05-16', 'economy', '', 'mobile_money', 50000.00, 'confirmee', '2025-05-10 14:14:51');

-- --------------------------------------------------------

--
-- Structure de la table `sites_touristiques`
--

CREATE TABLE `sites_touristiques` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `horaires` varchar(255) DEFAULT NULL,
  `categorie` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sites_touristiques`
--

INSERT INTO `sites_touristiques` (`id`, `nom`, `description`, `localisation`, `horaires`, `categorie`, `image`, `date_ajout`) VALUES
(28, 'Porte du non retour', 'Le temple des Pythons est un sanctuaire vaudou situé à Ouidah (Bénin), dans un lieu où l&#039;existence d&#039;un culte du serpent (Dangbé) – une forme particulière du vaudou – est attestée depuis la fin du XVIIe siècle. Ses pythons sacrés vivants constituent l&#039;une des attractions touristiques majeures de la ville.', 'Ouidah', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Site historique', '6821b53a5ba54.jpeg', '2025-05-12 08:45:46'),
(29, 'Porte du non retour', 'La Porte du non-retour est un arc mémorial en béton et bronze dans la ville de Ouidah, au Bénin. L&#039;arc, qui se trouve sur la plage érigé en 1995 à l&#039;initiative de l&#039;UNESCO. Elle commémore la déportation des millions de captifs mis en esclavage en direction des colonies d&#039;outre-Atlantique dans la traite négrière.', 'Ouidah', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Site historique', '6821b7593fc70.jpg', '2025-05-12 08:54:49'),
(30, 'Parc National de la pendjari', 'Le parc national de la Pendjari (PNP) est une aire protégée du Bénin, située à l&#039;extrême nord-ouest du pays, dans le département de l’Atacora, sur les communes de Tanguiéta, Matéri et Kérou, à la frontière du Burkina Faso. Il fait partie de la réserve de biosphère de l&#039;Unesco du complexe W-Arly-Pendjari (WAP). Il figure sur la liste des aires protégées du Bénin.', 'Tanguiéta, Bénin', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Parc', '6821b872bacc7.jpg', '2025-05-12 08:59:30'),
(31, 'Lac Nokoué', 'Le lac Nokoué est un lac situé dans le Sud du Bénin. Il mesure 20 km de long et 11 km de large, et couvre une superficie de 4 900 ha[1]. Le lac est partiellement alimenté par les fleuves Ouémé et Sô, qui drainent vers le lac les sédiments de la région.', 'Cotonou, Bénin', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Autre', '6821b9109484d.jpg', '2025-05-12 09:02:08'),
(32, 'Ganvié', 'Ganvié est une cité lacustre du sud du Bénin, située sur le lac Nokoué au nord de la métropole de Cotonou. Elle fait partie de la commune de Sô-Ava dans le département de l&#039;Atlantique[1]. Elle est surnommée la Venise de l&#039;Afrique[2].', 'Cotonou', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Autre', '6821b9a688ab2.jpg', '2025-05-12 09:04:38'),
(35, 'Etoile Rouge', 'La place de l&#039;Étoile rouge est un grand carrefour de la ville de Cotonou. L’époque du marxisme au Bénin est marquée par plusieurs monuments dont elle est le plus grand.', 'Cotonou, Bénin', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Site naturel', '6821bb6761146.jpeg', '2025-05-12 09:12:07'),
(36, 'Toffa', 'Toffa, né dans les années 1850 et mort en 1908, est un roi (« dă ») de Porto-Novo (« Hogbonou »). Son règne (1874-1908) fut marqué par une alternance d&#039;alliances et de conflits avec des voisins militairement puissants et expansionnistes, royaume de Dahomey, Angleterre et France. À sa mort, Porto-Novo est annexée par cette dernière et rattachée à la colonie du Dahomey.', 'Porto-novo, Bénin', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Site historique', '6821bc50060dd.jpg', '2025-05-12 09:16:00'),
(37, 'Bio Guéra', 'Bio Guéra, né en 1856 dans le village Gbaasi et mort en 1916, est un prince guerrier wassangari, un peuple du Nord Bénin. Il a mené plusieurs résistances contre la colonisation française au Bénin', 'Cotonou, Bénin', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Site historique', '6821bd41afa14.jpg', '2025-05-12 09:20:01'),
(38, 'Béhanzin', 'Béhanzin ou Behazin (Gbɛ̀hanzin en fon-gbe, anciennement transcrit Gbêhanzin ou Gbèhanzin, ou Gbèhin azi bô ayidjlè Ahossou Gbowelé), né en 1845 et mort en 1906 est un roi d&#039;Abomey. Fils du roi Glélé, il est d&#039;abord connu sous le nom d&#039;Ahokponu puis de prince Kondo à partir de 1875. Il est traditionnellement (si on exclut la reine Hangbè et Adandozan) le onzième roi d&#039;Abomey. Durant son règne, le royaume du Dahomey est défait, pour constituer la colonie du Dahomey, avec le rattachement de Porto-Novo du roi Toffa, son cousin et son ennemi.', 'Abomey, Bénin', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Site historique', '6821bdd7c9b87.jpg', '2025-05-12 09:22:31'),
(39, 'Amazone', 'Le Monument Amazone ou encore l&#039;Amazone (monument gigantesque) est une statue érigée en hommage aux Amazones du Dahomey et fabriquée en structure métallique avec une enveloppe en bronze, d&#039;une hauteur de 30 m et pesant 150 tonnes, posée sur un tertre. Elle est installée sur l&#039;esplanade des Amazones dans le 12è arrondissement de la ville de Cotonou dans le Sud du Bénin.', 'Cotonou, Bénin', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Monument', '6821be67da093.jpg', '2025-05-12 09:24:55'),
(42, 'Parc attraction ifè', 'Ce parc d’attraction donne une nouvelle dimension au boulevard lagunaire, en attendant la mise en place des institutions de la République dont les domaines mettent du temps à être viabilisés. ', 'Porto-novo, Bénin', 'Lun-Ven 24H/24 / Sam-Dim 24H/24', 'Parc', '6821c0da9e8d4.png', '2025-05-12 09:35:22'),
(43, 'Parc National de la pendjari', 'Le parc national de la Pendjari (PNP) est une aire protégée du Bénin, située à l&#039;extrême nord-ouest du pays, dans le département de l’Atacora, sur les communes de Tanguiéta, Matéri et Kérou, à la frontière du Burkina Faso. Il fait partie de la réserve de biosphère de l&#039;Unesco du complexe W-Arly-Pendjari (WAP). Il figure sur la liste des aires protégées du Bénin.', 'Tanguiéta, Bénin', 'Lun-Ven 9h-18h / Sam-Dim 10h-17h', 'Parc', '6821c24ca730e.jpg', '2025-05-12 09:41:32');

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut` enum('en_attente','complete','echouee') NOT NULL DEFAULT 'en_attente',
  `details` text DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`id`, `reference`, `montant`, `statut`, `details`, `date_creation`) VALUES
(1, 'BTB-1747757663-4275', 10000.00, 'en_attente', '{\"circuit_id\":10,\"customer\":\"Djidjoho Prince Junior AGBODJOGBE\",\"email\":\"leaderagj2021@gmail.com\",\"package\":\"economy\",\"travelers\":\"1\"}', '2025-05-20 16:14:23'),
(2, 'BTB-1747757692-2755', 30000.00, 'en_attente', '{\"circuit_id\":10,\"customer\":\"Djidjoho Prince Junior AGBODJOGBE\",\"email\":\"leaderagj2021@gmail.com\",\"package\":\"comfort\",\"travelers\":\"1\"}', '2025-05-20 16:14:52');

-- --------------------------------------------------------

--
-- Structure de la table `transporters`
--

CREATE TABLE `transporters` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('homme','femme','autre') NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `driver_license` varchar(50) DEFAULT NULL,
  `experience_years` int(2) NOT NULL,
  `vehicle_type` varchar(255) DEFAULT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL,
  `vehicle_year` int(4) DEFAULT NULL,
  `plate_number` varchar(50) DEFAULT NULL,
  `passenger_capacity` int(2) DEFAULT NULL,
  `languages` varchar(255) DEFAULT NULL,
  `services` varchar(255) DEFAULT NULL,
  `regions` varchar(255) DEFAULT NULL,
  `rate_per_km` decimal(10,2) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `vehicle_photos` text DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `transporters`
--

INSERT INTO `transporters` (`id`, `first_name`, `last_name`, `birth_date`, `gender`, `address`, `city`, `phone`, `email`, `id_number`, `driver_license`, `experience_years`, `vehicle_type`, `vehicle_model`, `vehicle_year`, `plate_number`, `passenger_capacity`, `languages`, `services`, `regions`, `rate_per_km`, `biography`, `profile_photo`, `vehicle_photos`, `documents`, `username`, `password`, `status`, `created_at`) VALUES
(2, 'zinsou', 'ADJOKE', '2025-04-21', 'homme', 'Porto Novo-Adjarra', 'Porto-Novo', '41890201', 'sperofaihun00@gmail.com', '1234545568', '2154646', 15, 'Berline', 'CCGHGCG', 2005, '8716589', 12, 'Français,Anglais,Fon,Yoruba', 'Transfert aéroport,Transport inter-villes,Visites touristiques,Événements spéciaux', 'Adjarra,Porto-Novo,Cotonou,Parakou', 50000.00, 'HFVUHIHIGUF UGUIGUG I', 'uploads/transporters/1745226703_team-1.jpg', '[\"uploads\\/transporters\\/vehicles\\/1745226704_about-2.jpg\"]', '[\"uploads\\/transporters\\/documents\\/1745226704_carousel-2.jpg\"]', 'zadjoke59', '$2y$10$6gmd6o5We.vvXLA6JbL1qemstwRt5vhZQCAfiE/nM.tCBVmtQUD5a', 'approved', '2025-04-21 09:11:44'),
(5, 'Fadyl', 'BOURAIMA', '2001-09-21', 'homme', 'Porto-Novo, Bénin', 'Porto-Novo', '51851525', 'fadylbouraima4@gmail.com', NULL, ' 123456789012', 5, 'Bus', 'Toyota', 2005, 'AB 1234 CD', 20, 'Français,Fon,Yoruba', 'Transport en ville,Transport inter-villes,Visites touristiques,Événements spéciaux', 'Adjarra,Porto-Novo,Cotonou,Parakou,Autre', 1000.00, NULL, 'uploads/transporters/1747652672_imag.jpg', '[\"uploads\\/transporters\\/vehicles\\/1747652672_transpo.png\"]', '[\"uploads\\/transporters\\/documents\\/1747652672_aaaaa.jpeg\"]', 'fbouraima14', '$2y$10$J68i0MJW3R63G1uphICWrOmby6cgduG8eCj3fn6Oa3/cuKNvgIqn.', 'approved', '2025-05-19 11:04:32');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(222) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_phone` int(11) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `confirm_password` varchar(255) NOT NULL,
  `first_loging` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `user_name`, `user_email`, `user_phone`, `user_password`, `confirm_password`, `first_loging`) VALUES
(1, 'prince djidjoho', 'leaderagj2021@gmail.com', 156184787, '$2y$10$Wy4e9qS1X9PI15tdapq1DekNA2WxlpChoKYnnoy408FZmcXRvk02y', '$2y$10$Wy4e9qS1X9PI15tdapq1DekNA2WxlpChoKYnnoy408FZmcXRvk02y', '1');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `circuits`
--
ALTER TABLE `circuits`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `circuit_sites`
--
ALTER TABLE `circuit_sites`
  ADD PRIMARY KEY (`circuit_id`,`site_id`);

--
-- Index pour la table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `sites_touristiques`
--
ALTER TABLE `sites_touristiques`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `transporters`
--
ALTER TABLE `transporters`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `circuits`
--
ALTER TABLE `circuits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `sites_touristiques`
--
ALTER TABLE `sites_touristiques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `transporters`
--
ALTER TABLE `transporters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
