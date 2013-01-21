-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Lun 21 Janvier 2013 à 09:56
-- Version du serveur: 5.5.28
-- Version de PHP: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

drop database if exists aeroport;

create database if not exists aeroport;
use aeroport;

--
-- Base de données: `aeroport`
--

-- --------------------------------------------------------

--
-- Structure de la table `AdministrateurUser`
--

CREATE TABLE IF NOT EXISTS `AdministrateurUser` (
  `id_admin` int(2) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `AdministrateurUser`
--

INSERT INTO `AdministrateurUser` (`id_admin`, `login`, `password`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

--
-- Structure de la table `AdresseClient`
--

CREATE TABLE IF NOT EXISTS `AdresseClient` (
  `id_adresse` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `pays` varchar(255) NOT NULL,
  `code_postal` varchar(5) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `defaut` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_adresse`,`id_client`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `AdresseClient`
--

INSERT INTO `AdresseClient` (`id_adresse`, `id_client`, `pays`, `code_postal`, `ville`, `adresse`, `defaut`) VALUES
(1, 1, 'dfsdfds', '54578', 'testse', 'trets', 1),
(2, 1, 'France', '53140', 'pre en pail', 'le bioragz', 0);

-- --------------------------------------------------------

--
-- Structure de la table `aeroport`
--

CREATE TABLE IF NOT EXISTS `aeroport` (
  `id_aeroport` char(3) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `code_ville` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `longueur_piste` bigint(4) NOT NULL,
  PRIMARY KEY (`id_aeroport`),
  KEY `code_ville` (`code_ville`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `aeroport`
--

INSERT INTO `aeroport` (`id_aeroport`, `nom`, `code_ville`, `adresse`, `longueur_piste`) VALUES
('CDG', 'Aéroport Charles-de-Gaulle', '1', '1 impasse de Gaulle', 4200),
('GIG', 'Aéroport international Galeão', '10', '2 rue de la paix', 4000),
('JFK', 'Aéroport international John-F.-Kennedy', '9', '2 rue de la paix', 4442),
('LHR', 'Aéroport de Londres Heathrow', '4', '2 rue de la paix', 3902),
('LYS', 'Aéroport Lyon-Saint-Exupéry', '3', '2 rue de la paix', 4000),
('MAN', 'Aéroport international de Manchester', '5', '2 rue de la paix', 3048),
('MRS', 'Aéroport Marseille Provence', '2', '2 rue de la paix', 3500),
('MUC', 'Aéroport international Franz-Josef-Strauss', '6', '2 rue de la paix', 4000),
('PEK', 'Beijing-Capital', '8', '2 rue de la paix', 3800),
('SXF', 'Aéroport international de Berlin-Schönefeld', '7', '2 rue de la paix', 3000);

-- --------------------------------------------------------

--
-- Structure de la table `astreinte`
--

CREATE TABLE IF NOT EXISTS `astreinte` (
  `id_aeroport` char(3) NOT NULL,
  `id_pilote` int(11) NOT NULL,
  `date_astreinte` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_aeroport`,`id_pilote`,`date_astreinte`),
  KEY `id_pilote` (`id_pilote`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `astreinte`
--

INSERT INTO `astreinte` (`id_aeroport`, `id_pilote`, `date_astreinte`) VALUES
('CDG', 1, '2012-11-07 22:00:00'),
('CDG', 1, '2012-11-19 23:00:00'),
('CDG', 1, '2012-12-14 23:00:00'),
('LYS', 1, '2012-11-04 22:00:00'),
('CDG', 2, '2012-11-05 22:00:00'),
('CDG', 2, '2012-11-19 23:00:00'),
('CDG', 2, '2012-12-14 23:00:00'),
('LYS', 2, '2012-11-04 22:00:00'),
('CDG', 3, '2012-11-23 23:00:00'),
('MAN', 3, '2012-11-04 22:00:00'),
('CDG', 4, '2012-10-22 16:58:01'),
('MAN', 4, '2012-11-04 22:00:00'),
('CDG', 5, '2012-11-05 22:00:00'),
('CDG', 5, '2012-11-07 22:00:00'),
('CDG', 6, '2012-10-22 16:58:41'),
('CDG', 7, '2012-11-21 23:00:00'),
('CDG', 7, '2012-11-23 23:00:00'),
('CDG', 10, '2012-11-21 23:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `avion`
--

CREATE TABLE IF NOT EXISTS `avion` (
  `id_avion` int(4) NOT NULL AUTO_INCREMENT,
  `id_type_avion` int(11) NOT NULL,
  `nb_places` int(3) NOT NULL,
  `total_heure_vol` int(6) NOT NULL DEFAULT '0',
  `nb_heures_gd_revision` int(6) NOT NULL DEFAULT '0',
  `disponibilite_avion` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_avion`),
  KEY `id_type_avion` (`id_type_avion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `avion`
--

INSERT INTO `avion` (`id_avion`, `id_type_avion`, `nb_places`, `total_heure_vol`, `nb_heures_gd_revision`, `disponibilite_avion`) VALUES
(1, 1, 5251, 15, 5, 1),
(2, 2, 295, 150, 90, 1),
(3, 3, 124, 3512, 0, 1),
(4, 4, 295, 250, 0, 1),
(5, 5, 237, 600, 0, 1),
(6, 6, 301, 810, 0, 1),
(7, 3, 124, 570, 0, 0),
(8, 2, 380, 30, 0, 0),
(9, 1, 525, 2000, 0, 1),
(10, 2, 380, 254, 0, 1),
(11, 1, 7857, 78, 54, 1),
(12, 1, 7857, 78, 547, 1);

-- --------------------------------------------------------

--
-- Structure de la table `Categorie`
--

CREATE TABLE IF NOT EXISTS `Categorie` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `CategorieProduit`
--

CREATE TABLE IF NOT EXISTS `CategorieProduit` (
  `id_categorie` int(6) NOT NULL,
  `id_produit` int(6) NOT NULL,
  PRIMARY KEY (`id_categorie`,`id_produit`),
  KEY `id_categorie` (`id_categorie`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Client`
--

CREATE TABLE IF NOT EXISTS `Client` (
  `id_client` int(6) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_client`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `Client`
--

INSERT INTO `Client` (`id_client`, `nom`, `prenom`, `mail`, `login`, `password`, `date`) VALUES
(1, 'Cottereau', 'Johnny', 'johnny.cottereau@gmail.com', 'johnny', 'ab4f63f9ac65152575886860dde480a1', '2013-01-20 19:47:58');

-- --------------------------------------------------------

--
-- Structure de la table `Commande`
--

CREATE TABLE IF NOT EXISTS `Commande` (
  `id_commande` int(6) NOT NULL AUTO_INCREMENT,
  `id_client` int(6) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `montant` float NOT NULL,
  `Islivre` tinyint(1) NOT NULL,
  `commentaire` varchar(255) DEFAULT NULL,
  `id_transport` int(11) NOT NULL,
  `id_paiement` int(11) NOT NULL,
  PRIMARY KEY (`id_commande`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `CommandeProduit`
--

CREATE TABLE IF NOT EXISTS `CommandeProduit` (
  `id_produit` int(6) NOT NULL,
  `id_commande` int(6) NOT NULL,
  `quantite` int(11) NOT NULL,
  PRIMARY KEY (`id_produit`,`id_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `desservir_ville_aeroport`
--

CREATE TABLE IF NOT EXISTS `desservir_ville_aeroport` (
  `code_ville` varchar(255) NOT NULL,
  `id_aeroport` char(3) NOT NULL,
  PRIMARY KEY (`code_ville`,`id_aeroport`),
  KEY `id_aeroport` (`id_aeroport`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `desservir_ville_aeroport`
--

INSERT INTO `desservir_ville_aeroport` (`code_ville`, `id_aeroport`) VALUES
('3', 'CDG'),
('7', 'CDG'),
('8', 'CDG'),
('1', 'GIG'),
('2', 'GIG'),
('7', 'GIG'),
('1', 'JFK'),
('10', 'JFK'),
('2', 'JFK'),
('3', 'JFK'),
('4', 'JFK'),
('5', 'JFK'),
('6', 'JFK'),
('6', 'LHR'),
('1', 'LYS'),
('10', 'LYS'),
('6', 'LYS'),
('9', 'LYS'),
('7', 'MAN'),
('9', 'MAN'),
('10', 'MRS'),
('4', 'MRS'),
('5', 'MRS'),
('8', 'MRS'),
('9', 'MRS'),
('2', 'MUC'),
('4', 'PEK'),
('8', 'PEK'),
('3', 'SXF'),
('8', 'SXF');

-- --------------------------------------------------------

--
-- Structure de la table `etre_breveter`
--

CREATE TABLE IF NOT EXISTS `etre_breveter` (
  `id_pilote` int(11) NOT NULL,
  `id_type_avion` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pilote`,`id_type_avion`),
  KEY `id_type_avion` (`id_type_avion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `etre_breveter`
--

INSERT INTO `etre_breveter` (`id_pilote`, `id_type_avion`, `date`) VALUES
(1, 1, '2012-10-22 21:08:50'),
(1, 2, '2012-10-22 21:08:50'),
(1, 4, '2012-10-22 21:08:50'),
(2, 1, '2012-10-22 21:08:50'),
(2, 2, '2012-10-22 21:08:50'),
(2, 3, '2012-10-22 21:08:50'),
(3, 1, '2012-10-22 21:08:50'),
(3, 2, '2012-10-22 21:08:50'),
(3, 4, '2012-10-22 21:08:50'),
(3, 6, '2012-10-22 21:08:50'),
(4, 5, '2012-10-22 21:08:50'),
(4, 6, '2012-10-22 21:08:50'),
(5, 5, '2012-10-22 21:08:50'),
(5, 6, '2012-10-22 21:08:50'),
(6, 2, '2012-10-22 21:08:50'),
(6, 3, '2012-10-22 21:08:50'),
(7, 2, '2012-10-22 21:08:50'),
(7, 3, '2012-10-22 21:08:50'),
(8, 2, '2012-10-22 21:08:50'),
(8, 4, '2012-10-22 21:08:50'),
(9, 4, '2012-10-22 21:08:50'),
(9, 6, '2012-10-22 21:08:50'),
(10, 3, '2012-10-22 21:08:50'),
(10, 4, '2012-10-22 21:08:50');

-- --------------------------------------------------------

--
-- Structure de la table `intervention`
--

CREATE TABLE IF NOT EXISTS `intervention` (
  `id_intervention` int(11) NOT NULL AUTO_INCREMENT,
  `id_maintenance` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `date_effective` date NOT NULL,
  `duree_effective` time NOT NULL,
  `commentaire` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_intervention`),
  KEY `id_maintenance` (`id_maintenance`),
  KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `jour_semaine`
--

CREATE TABLE IF NOT EXISTS `jour_semaine` (
  `numero_jour` int(11) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  PRIMARY KEY (`numero_jour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `jour_semaine`
--

INSERT INTO `jour_semaine` (`numero_jour`, `libelle`) VALUES
(1, 'Lundi'),
(2, 'Mardi'),
(3, 'Mercredi'),
(4, 'Jeudi'),
(5, 'Vendredi'),
(6, 'Samedi'),
(7, 'Dimanche');

-- --------------------------------------------------------

--
-- Structure de la table `ligne`
--

CREATE TABLE IF NOT EXISTS `ligne` (
  `numero_ligne` int(11) NOT NULL AUTO_INCREMENT,
  `id_aeroport_origine` char(3) NOT NULL,
  `id_aeroport_depart` char(3) NOT NULL,
  `id_aeroport_arrivee` char(3) NOT NULL,
  `heure_depart` time NOT NULL,
  `heure_arrivee` time NOT NULL,
  `tarif` decimal(13,2) DEFAULT NULL,
  `distance` int(11) NOT NULL,
  PRIMARY KEY (`numero_ligne`),
  KEY `id_aeroport_origine` (`id_aeroport_origine`),
  KEY `id_aeroport_depart` (`id_aeroport_depart`),
  KEY `id_aeroport_arrivee` (`id_aeroport_arrivee`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65 ;

--
-- Contenu de la table `ligne`
--

INSERT INTO `ligne` (`numero_ligne`, `id_aeroport_origine`, `id_aeroport_depart`, `id_aeroport_arrivee`, `heure_depart`, `heure_arrivee`, `tarif`, `distance`) VALUES
(18, 'PEK', 'CDG', 'MRS', '15:00:00', '18:00:00', 5.00, 654),
(19, 'LYS', 'LYS', 'PEK', '08:00:00', '18:00:00', 5.00, 350),
(20, 'JFK', 'JFK', 'MRS', '08:00:00', '15:00:00', 5.00, 2500),
(21, 'MAN', 'MAN', 'MUC', '15:00:00', '19:00:00', 5.00, 16000),
(22, 'PEK', 'PEK', 'LYS', '08:00:00', '15:00:00', 5.00, 1500),
(23, 'GIG', 'GIG', 'SXF', '06:00:00', '15:00:00', 5.00, 13687),
(24, 'MAN', 'MAN', 'CDG', '09:00:00', '12:00:00', 5.00, 1564),
(25, 'LHR', 'LHR', 'MUC', '02:00:00', '06:00:00', 5.00, 919),
(26, 'MUC', 'MUC', 'JFK', '05:00:00', '10:00:00', 5.00, 1547),
(27, 'CDG', 'CDG', 'LYS', '12:13:47', '12:13:50', 785.00, 388),
(30, 'MRS', 'LYS', 'CDG', '02:26:15', '02:26:18', 165.00, 400597),
(31, 'CDG', 'MRS', 'LYS', '02:35:03', '02:35:05', 47.00, 274770),
(32, 'CDG', 'LYS', 'MRS', '05:00:00', '02:36:23', 150.00, 274770),
(33, 'CDG', 'MRS', 'LYS', '05:00:00', '00:26:00', 4145.00, 274770),
(34, 'MRS', 'MRS', 'CDG', '08:00:00', '00:10:00', 150.00, 665225),
(35, 'MRS', 'MRS', 'LYS', '12:54:28', '12:54:30', 456.00, 274770),
(36, 'LYS', 'MRS', 'LYS', '12:56:56', '12:56:57', 456.00, 274770),
(37, 'CDG', 'MRS', 'LYS', '14:45:35', '14:45:36', 145.00, 274770),
(38, 'CDG', 'MRS', 'LYS', '05:00:00', '08:00:00', 145.00, 274770),
(39, 'LYS', 'CDG', 'LYS', '05:00:00', '00:07:00', 150.00, 400597),
(40, 'CDG', 'CDG', 'CDG', '12:00:00', '10:00:00', 15.00, 0),
(41, 'MRS', 'MRS', 'MRS', '12:18:12', '15:12:15', 154.00, 0),
(42, 'CDG', 'SXF', 'PEK', '05:00:00', '02:00:00', 45.00, 7365807),
(43, 'PEK', 'SXF', 'MUC', '02:59:11', '02:10:12', 150.00, 504838),
(44, 'CDG', 'MRS', 'LYS', '04:16:00', '04:02:00', 4588.00, 274770),
(45, 'CDG', 'MRS', 'LYS', '03:01:34', '03:01:36', 456.00, 274770),
(46, 'CDG', 'LYS', 'LYS', '05:00:00', '12:00:00', 154.00, 0),
(47, 'CDG', 'MRS', 'LYS', '08:00:00', '16:00:00', 45.00, 274770),
(48, 'CDG', 'MRS', 'LYS', '08:00:00', '16:00:00', 45.00, 274770),
(49, 'CDG', 'CDG', 'MRS', '03:07:12', '03:07:13', 150.00, 665225),
(50, 'MRS', 'LYS', 'CDG', '03:08:20', '03:08:23', 456.00, 400597),
(51, 'CDG', 'CDG', 'LYS', '01:30:24', '02:00:00', 150.00, 400),
(52, 'MRS', 'LYS', 'CDG', '04:00:00', '03:33:04', 456.00, 400),
(53, 'MUC', 'PEK', 'MAN', '04:00:00', '17:07:00', 154.00, 8097),
(54, 'MRS', 'MRS', 'MRS', '03:36:19', '03:36:20', 456.00, 0),
(55, 'MRS', 'MRS', 'LYS', '03:37:01', '03:37:02', 45.00, 274),
(58, 'MRS', 'LYS', 'MRS', '03:38:16', '03:38:22', 456.00, 274),
(59, 'MRS', 'LYS', 'MRS', '03:38:47', '03:38:48', 45.00, 274),
(60, 'LYS', 'CDG', 'MRS', '03:00:00', '08:00:00', 456.00, 665),
(61, 'MRS', 'SXF', 'PEK', '09:00:00', '13:00:00', 154.00, 7365),
(62, 'MRS', 'LYS', 'MRS', '19:33:53', '19:33:55', 785.00, 274),
(63, 'SXF', 'PEK', 'MAN', '06:00:00', '10:00:00', 859.00, 8097),
(64, 'PEK', 'SXF', 'MAN', '16:00:00', '18:00:00', 15.00, 1052);

-- --------------------------------------------------------

--
-- Structure de la table `maintenance`
--

CREATE TABLE IF NOT EXISTS `maintenance` (
  `id_maintenance` int(11) NOT NULL AUTO_INCREMENT,
  `id_avion` int(4) NOT NULL,
  `date_prevue` date NOT NULL,
  `fin_prevue` date NOT NULL,
  PRIMARY KEY (`id_maintenance`),
  KEY `id_avion` (`id_avion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Contenu de la table `maintenance`
--

INSERT INTO `maintenance` (`id_maintenance`, `id_avion`, `date_prevue`, `fin_prevue`) VALUES
(21, 8, '2013-01-14', '2013-01-24'),
(29, 7, '2013-01-08', '2013-01-18');

-- --------------------------------------------------------

--
-- Structure de la table `Paiement`
--

CREATE TABLE IF NOT EXISTS `Paiement` (
  `id_paiement` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  PRIMARY KEY (`id_paiement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `Parametre`
--

CREATE TABLE IF NOT EXISTS `Parametre` (
  `id_parametre` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nbProduits` int(11) NOT NULL,
  `nbElements` int(11) NOT NULL,
  `site` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `Parametre`
--

INSERT INTO `Parametre` (`id_parametre`, `email`, `password`, `nbProduits`, `nbElements`, `site`) VALUES
(1, 'johnny.cottereau@insset.fr', 'insset2013', 15, 12, 'Insset Airlines');

-- --------------------------------------------------------

--
-- Structure de la table `pays`
--

CREATE TABLE IF NOT EXISTS `pays` (
  `code_pays` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `alpha2` char(2) DEFAULT NULL,
  `alpha3` char(3) DEFAULT NULL,
  PRIMARY KEY (`code_pays`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `pays`
--

INSERT INTO `pays` (`code_pays`, `nom`, `alpha2`, `alpha3`) VALUES
(4, 'Afghanistan', 'AF', 'AFG'),
(8, 'Albanie', 'AL', 'ALB'),
(10, 'Antarctique', 'AQ', 'ATA'),
(12, 'Algérie', 'DZ', 'DZA'),
(16, 'Samoa Américaines', 'AS', 'ASM'),
(20, 'Andorre', 'AD', 'AND'),
(24, 'Angola', 'AO', 'AGO'),
(28, 'Antigua-et-Barbuda', 'AG', 'ATG'),
(31, 'Azerbaïdjan', 'AZ', 'AZE'),
(32, 'Argentine', 'AR', 'ARG'),
(36, 'Australie', 'AU', 'AUS'),
(40, 'Autriche', 'AT', 'AUT'),
(44, 'Bahamas', 'BS', 'BHS'),
(48, 'Bahreïn', 'BH', 'BHR'),
(50, 'Bangladesh', 'BD', 'BGD'),
(51, 'Arménie', 'AM', 'ARM'),
(52, 'Barbade', 'BB', 'BRB'),
(56, 'Belgique', 'BE', 'BEL'),
(60, 'Bermudes', 'BM', 'BMU'),
(64, 'Bhoutan', 'BT', 'BTN'),
(68, 'Bolivie', 'BO', 'BOL'),
(70, 'Bosnie-Herzégovine', 'BA', 'BIH'),
(72, 'Botswana', 'BW', 'BWA'),
(74, 'Île Bouvet', 'BV', 'BVT'),
(76, 'Brésil', 'BR', 'BRA'),
(84, 'Belize', 'BZ', 'BLZ'),
(86, 'Territoire Britannique de l''Océan Indien', 'IO', 'IOT'),
(90, 'Îles Salomon', 'SB', 'SLB'),
(92, 'Îles Vierges Britanniques', 'VG', 'VGB'),
(96, 'Brunéi Darussalam', 'BN', 'BRN'),
(100, 'Bulgarie', 'BG', 'BGR'),
(104, 'Myanmar', 'MM', 'MMR'),
(108, 'Burundi', 'BI', 'BDI'),
(112, 'Bélarus', 'BY', 'BLR'),
(116, 'Cambodge', 'KH', 'KHM'),
(120, 'Cameroun', 'CM', 'CMR'),
(124, 'Canada', 'CA', 'CAN'),
(132, 'Cap-vert', 'CV', 'CPV'),
(136, 'Îles Caïmanes', 'KY', 'CYM'),
(140, 'République Centrafricaine', 'CF', 'CAF'),
(144, 'Sri Lanka', 'LK', 'LKA'),
(148, 'Tchad', 'TD', 'TCD'),
(152, 'Chili', 'CL', 'CHL'),
(156, 'Chine', 'CN', 'CHN'),
(158, 'Taïwan', 'TW', 'TWN'),
(162, 'Île Christmas', 'CX', 'CXR'),
(166, 'Îles Cocos (Keeling)', 'CC', 'CCK'),
(170, 'Colombie', 'CO', 'COL'),
(174, 'Comores', 'KM', 'COM'),
(175, 'Mayotte', 'YT', 'MYT'),
(178, 'République du Congo', 'CG', 'COG'),
(180, 'République Démocratique du Congo', 'CD', 'COD'),
(184, 'Îles Cook', 'CK', 'COK'),
(188, 'Costa Rica', 'CR', 'CRI'),
(191, 'Croatie', 'HR', 'HRV'),
(192, 'Cuba', 'CU', 'CUB'),
(196, 'Chypre', 'CY', 'CYP'),
(203, 'République Tchèque', 'CZ', 'CZE'),
(204, 'Bénin', 'BJ', 'BEN'),
(208, 'Danemark', 'DK', 'DNK'),
(212, 'Dominique', 'DM', 'DMA'),
(214, 'République Dominicaine', 'DO', 'DOM'),
(218, 'Équateur', 'EC', 'ECU'),
(222, 'El Salvador', 'SV', 'SLV'),
(226, 'Guinée Équatoriale', 'GQ', 'GNQ'),
(231, 'Éthiopie', 'ET', 'ETH'),
(232, 'Érythrée', 'ER', 'ERI'),
(233, 'Estonie', 'EE', 'EST'),
(234, 'Îles Féroé', 'FO', 'FRO'),
(238, 'Îles (malvinas) Falkland', 'FK', 'FLK'),
(239, 'Géorgie du Sud et les Îles Sandwich du Sud', 'GS', 'SGS'),
(242, 'Fidji', 'FJ', 'FJI'),
(246, 'Finlande', 'FI', 'FIN'),
(248, 'Îles Åland', 'AX', 'ALA'),
(250, 'France', 'FR', 'FRA'),
(254, 'Guyane Française', 'GF', 'GUF'),
(258, 'Polynésie Française', 'PF', 'PYF'),
(260, 'Terres Australes Françaises', 'TF', 'ATF'),
(262, 'Djibouti', 'DJ', 'DJI'),
(266, 'Gabon', 'GA', 'GAB'),
(268, 'Géorgie', 'GE', 'GEO'),
(270, 'Gambie', 'GM', 'GMB'),
(275, 'Territoire Palestinien Occupé', 'PS', 'PSE'),
(276, 'Allemagne', 'DE', 'DEU'),
(288, 'Ghana', 'GH', 'GHA'),
(292, 'Gibraltar', 'GI', 'GIB'),
(296, 'Kiribati', 'KI', 'KIR'),
(300, 'Grèce', 'GR', 'GRC'),
(304, 'Groenland', 'GL', 'GRL'),
(308, 'Grenade', 'GD', 'GRD'),
(312, 'Guadeloupe', 'GP', 'GLP'),
(316, 'Guam', 'GU', 'GUM'),
(320, 'Guatemala', 'GT', 'GTM'),
(324, 'Guinée', 'GN', 'GIN'),
(328, 'Guyana', 'GY', 'GUY'),
(332, 'Haïti', 'HT', 'HTI'),
(334, 'Îles Heard et Mcdonald', 'HM', 'HMD'),
(336, 'Saint-Siège (état de la Cité du Vatican)', 'VA', 'VAT'),
(340, 'Honduras', 'HN', 'HND'),
(344, 'Hong-Kong', 'HK', 'HKG'),
(348, 'Hongrie', 'HU', 'HUN'),
(352, 'Islande', 'IS', 'ISL'),
(356, 'Inde', 'IN', 'IND'),
(360, 'Indonésie', 'ID', 'IDN'),
(364, 'République Islamique d''Iran', 'IR', 'IRN'),
(368, 'Iraq', 'IQ', 'IRQ'),
(372, 'Irlande', 'IE', 'IRL'),
(376, 'Israël', 'IL', 'ISR'),
(380, 'Italie', 'IT', 'ITA'),
(384, 'Côte d''Ivoire', 'CI', 'CIV'),
(388, 'Jamaïque', 'JM', 'JAM'),
(392, 'Japon', 'JP', 'JPN'),
(398, 'Kazakhstan', 'KZ', 'KAZ'),
(400, 'Jordanie', 'JO', 'JOR'),
(404, 'Kenya', 'KE', 'KEN'),
(408, 'République Populaire Démocratique de Corée', 'KP', 'PRK'),
(410, 'République de Corée', 'KR', 'KOR'),
(414, 'Koweït', 'KW', 'KWT'),
(417, 'Kirghizistan', 'KG', 'KGZ'),
(418, 'République Démocratique Populaire Lao', 'LA', 'LAO'),
(422, 'Liban', 'LB', 'LBN'),
(426, 'Lesotho', 'LS', 'LSO'),
(428, 'Lettonie', 'LV', 'LVA'),
(430, 'Libéria', 'LR', 'LBR'),
(434, 'Jamahiriya Arabe Libyenne', 'LY', 'LBY'),
(438, 'Liechtenstein', 'LI', 'LIE'),
(440, 'Lituanie', 'LT', 'LTU'),
(442, 'Luxembourg', 'LU', 'LUX'),
(446, 'Macao', 'MO', 'MAC'),
(450, 'Madagascar', 'MG', 'MDG'),
(454, 'Malawi', 'MW', 'MWI'),
(458, 'Malaisie', 'MY', 'MYS'),
(462, 'Maldives', 'MV', 'MDV'),
(466, 'Mali', 'ML', 'MLI'),
(470, 'Malte', 'MT', 'MLT'),
(474, 'Martinique', 'MQ', 'MTQ'),
(478, 'Mauritanie', 'MR', 'MRT'),
(480, 'Maurice', 'MU', 'MUS'),
(484, 'Mexique', 'MX', 'MEX'),
(492, 'Monaco', 'MC', 'MCO'),
(496, 'Mongolie', 'MN', 'MNG'),
(498, 'République de Moldova', 'MD', 'MDA'),
(500, 'Montserrat', 'MS', 'MSR'),
(504, 'Maroc', 'MA', 'MAR'),
(508, 'Mozambique', 'MZ', 'MOZ'),
(512, 'Oman', 'OM', 'OMN'),
(516, 'Namibie', 'NA', 'NAM'),
(520, 'Nauru', 'NR', 'NRU'),
(524, 'Népal', 'NP', 'NPL'),
(528, 'Pays-Bas', 'NL', 'NLD'),
(530, 'Antilles Néerlandaises', 'AN', 'ANT'),
(533, 'Aruba', 'AW', 'ABW'),
(540, 'Nouvelle-Calédonie', 'NC', 'NCL'),
(548, 'Vanuatu', 'VU', 'VUT'),
(554, 'Nouvelle-Zélande', 'NZ', 'NZL'),
(558, 'Nicaragua', 'NI', 'NIC'),
(562, 'Niger', 'NE', 'NER'),
(566, 'Nigéria', 'NG', 'NGA'),
(570, 'Niué', 'NU', 'NIU'),
(574, 'Île Norfolk', 'NF', 'NFK'),
(578, 'Norvège', 'NO', 'NOR'),
(580, 'Îles Mariannes du Nord', 'MP', 'MNP'),
(581, 'Îles Mineures Éloignées des États-Unis', 'UM', 'UMI'),
(583, 'États Fédérés de Micronésie', 'FM', 'FSM'),
(584, 'Îles Marshall', 'MH', 'MHL'),
(585, 'Palaos', 'PW', 'PLW'),
(586, 'Pakistan', 'PK', 'PAK'),
(591, 'Panama', 'PA', 'PAN'),
(598, 'Papouasie-Nouvelle-Guinée', 'PG', 'PNG'),
(600, 'Paraguay', 'PY', 'PRY'),
(604, 'Pérou', 'PE', 'PER'),
(608, 'Philippines', 'PH', 'PHL'),
(612, 'Pitcairn', 'PN', 'PCN'),
(616, 'Pologne', 'PL', 'POL'),
(620, 'Portugal', 'PT', 'PRT'),
(624, 'Guinée-Bissau', 'GW', 'GNB'),
(626, 'Timor-Leste', 'TL', 'TLS'),
(630, 'Porto Rico', 'PR', 'PRI'),
(634, 'Qatar', 'QA', 'QAT'),
(638, 'Réunion', 'RE', 'REU'),
(642, 'Roumanie', 'RO', 'ROU'),
(643, 'Fédération de Russie', 'RU', 'RUS'),
(646, 'Rwanda', 'RW', 'RWA'),
(654, 'Sainte-Hélène', 'SH', 'SHN'),
(659, 'Saint-Kitts-et-Nevis', 'KN', 'KNA'),
(660, 'Anguilla', 'AI', 'AIA'),
(662, 'Sainte-Lucie', 'LC', 'LCA'),
(666, 'Saint-Pierre-et-Miquelon', 'PM', 'SPM'),
(670, 'Saint-Vincent-et-les Grenadines', 'VC', 'VCT'),
(674, 'Saint-Marin', 'SM', 'SMR'),
(678, 'Sao Tomé-et-Principe', 'ST', 'STP'),
(682, 'Arabie Saoudite', 'SA', 'SAU'),
(686, 'Sénégal', 'SN', 'SEN'),
(690, 'Seychelles', 'SC', 'SYC'),
(694, 'Sierra Leone', 'SL', 'SLE'),
(702, 'Singapour', 'SG', 'SGP'),
(703, 'Slovaquie', 'SK', 'SVK'),
(704, 'Viet Nam', 'VN', 'VNM'),
(705, 'Slovénie', 'SI', 'SVN'),
(706, 'Somalie', 'SO', 'SOM'),
(710, 'Afrique du Sud', 'ZA', 'ZAF'),
(716, 'Zimbabwe', 'ZW', 'ZWE'),
(724, 'Espagne', 'ES', 'ESP'),
(732, 'Sahara Occidental', 'EH', 'ESH'),
(736, 'Soudan', 'SD', 'SDN'),
(740, 'Suriname', 'SR', 'SUR'),
(744, 'Svalbard et Île Jan Mayen', 'SJ', 'SJM'),
(748, 'Swaziland', 'SZ', 'SWZ'),
(752, 'Suède', 'SE', 'SWE'),
(756, 'Suisse', 'CH', 'CHE'),
(760, 'République Arabe Syrienne', 'SY', 'SYR'),
(762, 'Tadjikistan', 'TJ', 'TJK'),
(764, 'Thaïlande', 'TH', 'THA'),
(768, 'Togo', 'TG', 'TGO'),
(772, 'Tokelau', 'TK', 'TKL'),
(776, 'Tonga', 'TO', 'TON'),
(780, 'Trinité-et-Tobago', 'TT', 'TTO'),
(784, 'Émirats Arabes Unis', 'AE', 'ARE'),
(788, 'Tunisie', 'TN', 'TUN'),
(792, 'Turquie', 'TR', 'TUR'),
(795, 'Turkménistan', 'TM', 'TKM'),
(796, 'Îles Turks et Caïques', 'TC', 'TCA'),
(798, 'Tuvalu', 'TV', 'TUV'),
(800, 'Ouganda', 'UG', 'UGA'),
(804, 'Ukraine', 'UA', 'UKR'),
(807, 'L''ex-République Yougoslave de Macédoine', 'MK', 'MKD'),
(818, 'Égypte', 'EG', 'EGY'),
(826, 'Royaume-Uni', 'GB', 'GBR'),
(833, 'Île de Man', 'IM', 'IMN'),
(834, 'République-Unie de Tanzanie', 'TZ', 'TZA'),
(840, 'États-Unis', 'US', 'USA'),
(850, 'Îles Vierges des États-Unis', 'VI', 'VIR'),
(854, 'Burkina Faso', 'BF', 'BFA'),
(858, 'Uruguay', 'UY', 'URY'),
(860, 'Ouzbékistan', 'UZ', 'UZB'),
(862, 'Venezuela', 'VE', 'VEN'),
(876, 'Wallis et Futuna', 'WF', 'WLF'),
(882, 'Samoa', 'WS', 'WSM'),
(887, 'Yémen', 'YE', 'YEM'),
(891, 'Serbie-et-Monténégro', 'CS', 'SCG'),
(894, 'Zambie', 'ZM', 'ZMB');

-- --------------------------------------------------------

--
-- Structure de la table `periodicite`
--

CREATE TABLE IF NOT EXISTS `periodicite` (
  `numero_ligne` int(11) NOT NULL,
  `numero_jour` int(11) NOT NULL,
  PRIMARY KEY (`numero_ligne`,`numero_jour`),
  KEY `numero_jour` (`numero_jour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `periodicite`
--

INSERT INTO `periodicite` (`numero_ligne`, `numero_jour`) VALUES
(20, 1),
(21, 1),
(23, 1),
(24, 1),
(32, 1),
(42, 1),
(43, 1),
(45, 1),
(46, 1),
(49, 1),
(58, 1),
(59, 1),
(20, 2),
(25, 2),
(26, 2),
(47, 2),
(48, 2),
(54, 2),
(55, 2),
(61, 2),
(23, 3),
(25, 3),
(59, 3),
(19, 4),
(21, 4),
(24, 4),
(25, 4),
(27, 4),
(49, 4),
(52, 4),
(58, 4),
(20, 5),
(23, 5),
(24, 5),
(26, 5),
(47, 5),
(48, 5),
(54, 5),
(55, 5),
(61, 5),
(19, 6),
(32, 6),
(59, 6),
(21, 7),
(23, 7),
(26, 7),
(27, 7),
(49, 7),
(58, 7);

-- --------------------------------------------------------

--
-- Structure de la table `pilote`
--

CREATE TABLE IF NOT EXISTS `pilote` (
  `id_pilote` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `code_ville` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `telephone` int(10) DEFAULT NULL,
  `disponibilite` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_pilote`),
  KEY `code_ville` (`code_ville`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `pilote`
--

INSERT INTO `pilote` (`id_pilote`, `nom`, `prenom`, `email`, `password`, `code_ville`, `adresse`, `telephone`, `disponibilite`) VALUES
(1, 'Simoret', 'Marc', 'marc.simoret@gmail.com', 'marco', '5', '154 rue de moncuq', NULL, 1),
(2, 'Balouche', 'Paul', 'paul.balouche@gmail.com', 'paulo', '3', '562 place de la république', NULL, 1),
(3, 'Vergoza', 'Paulo', 'paulo.vergoza@gmail.com', 'paulo', '10', '153 de la plaza centrale', NULL, 1),
(4, 'Jupon', 'Daniel', 'daniel.jupon@gmail.com', 'daniel', '6', '156 schrule das', NULL, 0),
(5, 'Kolibri', 'Gazelle', 'gazelle.kolibri', 'gazou', '8', '561 chinatown', NULL, 1),
(6, 'Peret', 'Franck', 'franck.perget@gmail.com', 'francky', '6', '6154 london park', NULL, 0),
(7, 'Vasseur', 'Corentin', 'corentin.vasseur@gmail.com', 'corentinou', '2', '456 rue de voltaire', NULL, 1),
(8, 'McClane', 'John', 'john.mcclane@gmail.com', 'yipikai', '9', '456 Madison Square Garden', NULL, 1),
(9, 'Hancouille', 'John', 'john.hancouille@gmail.com', 'hancock', '8', '415 de la cité interdite', NULL, 0),
(10, 'Belhamy', 'Edgard', 'edgard.balhamy@gmail.com', 'edgard', '2', '45 rue des riches', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `Produit`
--

CREATE TABLE IF NOT EXISTS `Produit` (
  `id_produit` int(6) NOT NULL AUTO_INCREMENT,
  `designation` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `descriptionBreve` varchar(255) DEFAULT NULL,
  `prix` float NOT NULL,
  `quantite` int(6) NOT NULL,
  `photo1` varchar(255) DEFAULT NULL,
  `photo2` varchar(255) DEFAULT NULL,
  `photo3` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actif` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_produit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `Produit`
--

INSERT INTO `Produit` (`id_produit`, `designation`, `description`, `descriptionBreve`, `prix`, `quantite`, `photo1`, `photo2`, `photo3`, `date`, `actif`) VALUES
(1, 'Produit de test', 'test', 'test', 90, 51, '1_1.jpeg', '1_2.jpeg', '1_3.jpeg', '2013-01-20 23:18:01', 1);

-- --------------------------------------------------------

--
-- Structure de la table `remarque`
--

CREATE TABLE IF NOT EXISTS `remarque` (
  `id_remarque` int(11) NOT NULL AUTO_INCREMENT,
  `libelle_remarque` varchar(255) NOT NULL,
  `id_type_remarque` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `id_vol` int(11) NOT NULL,
  `numero_ligne` int(11) NOT NULL,
  PRIMARY KEY (`id_remarque`),
  KEY `numero_ligne` (`numero_ligne`,`id_vol`),
  KEY `id_type_remarque` (`id_type_remarque`),
  KEY `id_service` (`id_service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE IF NOT EXISTS `reservation` (
  `id_reservation` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) NOT NULL,
  `numero_ligne` int(11) NOT NULL,
  `id_vol` int(11) NOT NULL,
  `nbreservation` int(3) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reservation`),
  KEY `id_client` (`id_client`),
  KEY `numero_ligne` (`numero_ligne`,`id_vol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE IF NOT EXISTS `service` (
  `id_service` int(11) NOT NULL AUTO_INCREMENT,
  `libelle_service` varchar(255) NOT NULL,
  PRIMARY KEY (`id_service`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `service`
--

INSERT INTO `service` (`id_service`, `libelle_service`) VALUES
(1, 'Direction stratégique'),
(2, 'Service commercial'),
(3, 'Service maintenance'),
(4, 'Direction des ressources humaines'),
(5, 'Service planning'),
(6, 'Service d''exploitation'),
(7, 'Service de logistique commerciale'),
(8, 'Service d''équipage'),
(9, 'Aucun Service');

-- --------------------------------------------------------

--
-- Structure de la table `SousCategorie`
--

CREATE TABLE IF NOT EXISTS `SousCategorie` (
  `id_souscategorie` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  PRIMARY KEY (`id_souscategorie`),
  KEY `id_categorie` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `SousCategorieProduit`
--

CREATE TABLE IF NOT EXISTS `SousCategorieProduit` (
  `id_souscategorie` int(6) NOT NULL,
  `id_produit` int(6) NOT NULL,
  PRIMARY KEY (`id_souscategorie`,`id_produit`),
  KEY `id_souscategorie` (`id_souscategorie`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Transport`
--

CREATE TABLE IF NOT EXISTS `Transport` (
  `id_transport` int(5) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `temps` int(11) NOT NULL,
  `prix` float NOT NULL,
  `photo` varchar(255) NOT NULL,
  PRIMARY KEY (`id_transport`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `Transport`
--

INSERT INTO `Transport` (`id_transport`, `libelle`, `temps`, `prix`, `photo`) VALUES
(1, 'UPS Standard', 48, 15.56, 'ups.png'),
(2, ' Chronopost International ', 24, 12.6, 'chronopost.png'),
(3, ' Colissimo Suivi ', 48, 10.9, 'colissimo.png'),
(4, ' France Express ', 24, 10.59, 'france_express.png');

-- --------------------------------------------------------

--
-- Structure de la table `type_avion`
--

CREATE TABLE IF NOT EXISTS `type_avion` (
  `id_type_avion` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `rayon_action` int(10) NOT NULL,
  `longueur_decollage` int(4) NOT NULL,
  `longueur_atterrissage` int(4) NOT NULL,
  PRIMARY KEY (`id_type_avion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `type_avion`
--

INSERT INTO `type_avion` (`id_type_avion`, `libelle`, `rayon_action`, `longueur_decollage`, `longueur_atterrissage`) VALUES
(1, 'A380-800s', 152001, 27501, 30001),
(2, 'A340-600', 14600, 2300, 2500),
(3, 'A319', 6650, 2000, 2300),
(4, 'A340-300', 13700, 2700, 2900),
(5, '787-8', 15000, 3000, 3300),
(6, '777-200LR', 17445, 2950, 3200);

-- --------------------------------------------------------

--
-- Structure de la table `type_remarque`
--

CREATE TABLE IF NOT EXISTS `type_remarque` (
  `id_type_remarque` int(11) NOT NULL AUTO_INCREMENT,
  `libelle_type_remarque` varchar(255) NOT NULL,
  PRIMARY KEY (`id_type_remarque`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `type_remarque`
--

INSERT INTO `type_remarque` (`id_type_remarque`, `libelle_type_remarque`) VALUES
(1, 'Cuisine'),
(2, 'Matériel spécifique'),
(3, 'Traitement médical'),
(4, 'Prise en charge'),
(5, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE IF NOT EXISTS `utilisateur` (
  `login` varchar(255) NOT NULL,
  `id_service` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `code_ville` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `telephone` int(10) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user_pilote` integer null,
  `id_user` integer not null,
  PRIMARY KEY (`login`),
  KEY `code_ville` (`code_ville`),
  KEY `id_service` (`id_service`),
  KEY `id_user_pilote` (`id_user_pilote`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `utilisateur`
--

INSERT INTO `utilisateur` (`login`, `id_service`, `nom`, `prenom`, `email`, `password`, `code_ville`, `adresse`, `telephone`, `date`, `id_user_pilote`, `id_user`) VALUES
('annette.duhamel@gmail.com', 2, 'Duhamel', 'Annette', 'annette.duhamel@gmail.com', 'annie', '2', '156 Rue du Général de Gaulle', 125659748, '2012-10-22 20:42:55', null, 1),
('blanche.francois@gmail.com', 4, 'François', 'Blanche', 'blanche.francois@gmail.com', 'blanou', '3', '24 place rouge', 415623514, '2012-10-22 20:42:55', null, 2),
('gerard.hanconnina@gmail.com', 3, 'Hanconinna', 'Gérard', 'gerard.hanconnina@gmail.com', 'gege', '2', '25 Rue pétain', 126548795, '2012-10-22 20:42:55', null, 3),
('jeanmichel.durand@gmail.com', 1, 'Durand', 'Jean-michel', 'jeanmichel.durand@gmail.com', 'jean-mi', '1', '95 Place de l''étoile', 562154987, '2012-10-22 20:42:55', null, 4),
('kevin.kitvikosky@gmail.com', 5, 'Kitvikosky', 'Kévin', 'kevin.kitvikosky@gmail.com', 'keke', '5', '653 Main Street', 555365125, '2012-10-22 20:42:55', null, 5),
('marc.simoret@gmail.com', 8, 'Simoret', 'Marc', 'marc.simoret@gmail.com', 'marco', '5', '154 rue de moncuq', NULL, '2012-10-22 20:42:55', 1, 6),
('paul.balouche@gmail.com', 8, 'Balouche', 'Paul', 'paul.balouche@gmail.com', 'paulo', '3', '562 place de la république', NULL, '2012-10-22 20:42:55', 2, 7),
('paulo.vergoza@gmail.com', 8, 'Vergoza', 'Paulo', 'paulo.vergoza@gmail.com', 'paulo', '10', '153 de la plaza centrale', NULL, '2012-10-22 20:42:55', 3, 8),
('daniel.jupon@gmail.com', 8, 'Jupon', 'Daniel', 'daniel.jupon@gmail.com', 'daniel', '6', '156 schrule das', NULL, '2012-10-22 20:42:55', 4, 9),
('gazelle.kolibri@gmail.com', 8, 'Kolibri', 'Gazelle', 'gazelle.kolibri@gmail.com', 'gazou', '8', '561 chinatown', NULL, '2012-10-22 20:42:55', 5, 10),
('franck.perget@gmail.com', 8, 'Peret', 'Franck', 'franck.perget@gmail.com', 'francky', '6', '6154 london park', NULL, '2012-10-22 20:42:55', 6, 11),
('corentin.vasseur@gmail.com', 8, 'Vasseur', 'Corentin', 'corentin.vasseur@gmail.com', 'corentinou', '2', '456 rue de voltaire', NULL, '2012-10-22 20:42:55', 7, 12),
('john.mcclane@gmail.com', 8, 'McClane', 'John', 'john.mcclane@gmail.com', 'yipikai', '9', '456 Madison Square Garden', NULL, '2012-10-22 20:42:55', 8, 13),
('john.hancouille@gmail.com', 8, 'Hancouille', 'John', 'john.hancouille@gmail.com', 'hancock', '8', '415 de la cité interdite', NULL, '2012-10-22 20:42:55', 9, 14),
('edgard.balhamy@gmail.com', 8, 'Belhamy', 'Edgard', 'edgard.balhamy@gmail.com', 'edgard', '2', '45 rue des riches', NULL, '2012-10-22 20:42:55', 10, 15);

-- --------------------------------------------------------

--
-- Structure de la table `ville`
--

CREATE TABLE IF NOT EXISTS `ville` (
  `code_ville` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `code_pays` int(11) NOT NULL,
  `code_postal` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`code_ville`),
  KEY `code_pays` (`code_pays`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `ville`
--

INSERT INTO `ville` (`code_ville`, `nom`, `code_pays`, `code_postal`) VALUES
('1', 'Paris', 250, '75000'),
('10', 'Rio de Janeiro', 76, NULL),
('2', 'Marseille', 250, '13000'),
('3', 'Lyon', 250, '69000'),
('4', 'Londres', 826, NULL),
('5', 'Manchester', 826, NULL),
('6', 'Munich', 276, NULL),
('7', 'Berlin', 276, NULL),
('8', 'Pékin', 156, NULL),
('9', 'New-York', 840, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `vol`
--

CREATE TABLE IF NOT EXISTS `vol` (
  `id_vol` int(11) NOT NULL,
  `numero_ligne` int(11) NOT NULL,
  `id_aeroport_depart_effectif` char(3) NOT NULL,
  `id_aeroport_arrivee_effectif` char(3) NOT NULL,
  `date_depart` date NOT NULL,
  `date_arrivee` date NOT NULL,
  `id_avion` int(4) DEFAULT NULL,
  `id_pilote` int(11) DEFAULT NULL,
  `id_copilote` int(11) DEFAULT NULL,
  `heure_arrivee_effective` time DEFAULT NULL,
  `tarif_effectif` decimal(13,2) DEFAULT NULL,
  PRIMARY KEY (`numero_ligne`,`id_vol`),
  KEY `id_pilote` (`id_pilote`),
  KEY `id_aeroport_arrivee_effectif` (`id_aeroport_arrivee_effectif`),
  KEY `id_avion` (`id_avion`),
  KEY `id_copilote` (`id_copilote`),
  KEY `id_aeroport_depart_effectif` (`id_aeroport_depart_effectif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `vol`
--

INSERT INTO `vol` (`id_vol`, `numero_ligne`, `id_aeroport_depart_effectif`, `id_aeroport_arrivee_effectif`, `date_depart`, `date_arrivee`, `id_avion`, `id_pilote`, `id_copilote`, `heure_arrivee_effective`, `tarif_effectif`) VALUES
(1, 18, 'CDG', 'MRS', '2012-11-07', '2012-11-08', NULL, NULL, NULL, NULL, 0.00),
(1, 19, 'LYS', 'PEK', '2012-10-09', '2012-10-09', 2, 7, 2, '18:00:00', 5.00),
(2, 19, 'CDG', 'CDG', '2013-01-22', '2013-01-23', NULL, NULL, NULL, NULL, NULL),
(1, 30, 'LYS', 'CDG', '0000-00-00', '0000-00-00', NULL, NULL, NULL, NULL, 145.00),
(1, 31, 'MRS', 'LYS', '2012-11-26', '2012-11-20', NULL, NULL, NULL, NULL, 0.00),
(1, 33, 'MRS', 'LYS', '2012-11-19', '2012-11-26', NULL, NULL, NULL, NULL, 0.00),
(1, 34, 'MRS', 'CDG', '2012-11-27', '2012-11-28', NULL, NULL, NULL, NULL, 0.00),
(1, 35, 'MRS', 'LYS', '2012-11-14', '2012-11-12', NULL, NULL, NULL, NULL, 0.00),
(1, 36, 'MRS', 'LYS', '2012-11-26', '2012-11-25', NULL, NULL, NULL, NULL, 0.00),
(1, 37, 'MRS', 'LYS', '2012-11-27', '2012-11-28', NULL, NULL, NULL, NULL, 0.00),
(1, 38, 'MRS', 'LYS', '2012-11-14', '2012-11-15', NULL, NULL, NULL, NULL, 0.00),
(1, 39, 'CDG', 'LYS', '2012-11-06', '2012-11-07', NULL, NULL, NULL, NULL, 0.00),
(1, 40, 'CDG', 'CDG', '2012-11-06', '2012-11-06', NULL, NULL, NULL, NULL, 0.00),
(1, 41, 'MRS', 'MRS', '2012-11-07', '2012-11-07', NULL, NULL, NULL, NULL, 0.00),
(1, 44, 'MRS', 'LYS', '2012-11-07', '2012-11-08', NULL, NULL, NULL, NULL, 0.00),
(1, 50, 'LYS', 'CDG', '2012-11-12', '2012-11-12', NULL, NULL, NULL, NULL, 0.00),
(1, 51, 'CDG', 'LYS', '2012-11-04', '2012-11-04', NULL, NULL, NULL, NULL, 0.00),
(1, 53, 'PEK', 'MAN', '2012-11-05', '2012-11-05', NULL, NULL, NULL, NULL, 150.00),
(1, 60, 'CDG', 'MRS', '2012-11-05', '2012-11-14', NULL, NULL, NULL, NULL, 0.00),
(1, 62, 'LYS', 'MRS', '2012-11-13', '2012-11-13', NULL, NULL, NULL, NULL, 5.00),
(1, 63, 'PEK', 'MAN', '2012-11-13', '2012-11-15', 12, NULL, NULL, NULL, 0.00),
(1, 64, 'SXF', 'MAN', '2012-11-01', '2012-11-01', 12, NULL, NULL, NULL, 0.00);

--
-- Contraintes pour les tables exportées
--
ALTER DATABASE aeroport charset=utf8;
--
-- Contraintes pour la table `aeroport`
--
ALTER TABLE `aeroport`
  ADD CONSTRAINT `aeroport_ibfk_1` FOREIGN KEY (`code_ville`) REFERENCES `ville` (`code_ville`);

--
-- Contraintes pour la table `astreinte`
--
ALTER TABLE `astreinte`
  ADD CONSTRAINT `astreinte_ibfk_1` FOREIGN KEY (`id_aeroport`) REFERENCES `aeroport` (`id_aeroport`),
  ADD CONSTRAINT `astreinte_ibfk_2` FOREIGN KEY (`id_pilote`) REFERENCES `pilote` (`id_pilote`);

--
-- Contraintes pour la table `avion`
--
ALTER TABLE `avion`
  ADD CONSTRAINT `avion_ibfk_1` FOREIGN KEY (`id_type_avion`) REFERENCES `type_avion` (`id_type_avion`);

--
-- Contraintes pour la table `desservir_ville_aeroport`
--
ALTER TABLE `desservir_ville_aeroport`
  ADD CONSTRAINT `desservir_ville_aeroport_ibfk_1` FOREIGN KEY (`code_ville`) REFERENCES `ville` (`code_ville`),
  ADD CONSTRAINT `desservir_ville_aeroport_ibfk_2` FOREIGN KEY (`id_aeroport`) REFERENCES `aeroport` (`id_aeroport`);

--
-- Contraintes pour la table `etre_breveter`
--
ALTER TABLE `etre_breveter`
  ADD CONSTRAINT `etre_breveter_ibfk_1` FOREIGN KEY (`id_pilote`) REFERENCES `pilote` (`id_pilote`),
  ADD CONSTRAINT `etre_breveter_ibfk_2` FOREIGN KEY (`id_type_avion`) REFERENCES `type_avion` (`id_type_avion`);

--
-- Contraintes pour la table `intervention`
--
ALTER TABLE `intervention`
  ADD CONSTRAINT `intervention_ibfk_1` FOREIGN KEY (`id_maintenance`) REFERENCES `maintenance` (`id_maintenance`) ON DELETE CASCADE,
  ADD CONSTRAINT `intervention_ibfk_2` FOREIGN KEY (`login`) REFERENCES `utilisateur` (`login`);

--
-- Contraintes pour la table `ligne`
--
ALTER TABLE `ligne`
  ADD CONSTRAINT `ligne_ibfk_1` FOREIGN KEY (`id_aeroport_origine`) REFERENCES `aeroport` (`id_aeroport`),
  ADD CONSTRAINT `ligne_ibfk_2` FOREIGN KEY (`id_aeroport_depart`) REFERENCES `aeroport` (`id_aeroport`),
  ADD CONSTRAINT `ligne_ibfk_3` FOREIGN KEY (`id_aeroport_arrivee`) REFERENCES `aeroport` (`id_aeroport`);

--
-- Contraintes pour la table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`id_avion`) REFERENCES `avion` (`id_avion`);

--
-- Contraintes pour la table `periodicite`
--
ALTER TABLE `periodicite`
  ADD CONSTRAINT `periodicite_ibfk_1` FOREIGN KEY (`numero_ligne`) REFERENCES `ligne` (`numero_ligne`) ON DELETE CASCADE,
  ADD CONSTRAINT `periodicite_ibfk_2` FOREIGN KEY (`numero_jour`) REFERENCES `jour_semaine` (`numero_jour`);

--
-- Contraintes pour la table `pilote`
--
ALTER TABLE `pilote`
  ADD CONSTRAINT `pilote_ibfk_1` FOREIGN KEY (`code_ville`) REFERENCES `ville` (`code_ville`);

--
-- Contraintes pour la table `remarque`
--
ALTER TABLE `remarque`
  ADD CONSTRAINT `remarque_ibfk_1` FOREIGN KEY (`numero_ligne`, `id_vol`) REFERENCES `vol` (`numero_ligne`, `id_vol`),
  ADD CONSTRAINT `remarque_ibfk_2` FOREIGN KEY (`id_type_remarque`) REFERENCES `type_remarque` (`id_type_remarque`),
  ADD CONSTRAINT `remarque_ibfk_3` FOREIGN KEY (`id_service`) REFERENCES `service` (`id_service`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`numero_ligne`, `id_vol`) REFERENCES `vol` (`numero_ligne`, `id_vol`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`code_ville`) REFERENCES `ville` (`code_ville`),
  ADD CONSTRAINT `utilisateur_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `service` (`id_service`),
  ADD CONSTRAINT `utilisateur_ibfk_3` FOREIGN KEY (`id_user_pilote`) REFERENCES `pilote` (`id_pilote`);

--
-- Contraintes pour la table `ville`
--
ALTER TABLE `ville`
  ADD CONSTRAINT `ville_ibfk_1` FOREIGN KEY (`code_pays`) REFERENCES `pays` (`code_pays`);

--
-- Contraintes pour la table `vol`
--
ALTER TABLE `vol`
  ADD CONSTRAINT `vol_ibfk_1` FOREIGN KEY (`numero_ligne`) REFERENCES `ligne` (`numero_ligne`) ON DELETE CASCADE,
  ADD CONSTRAINT `vol_ibfk_2` FOREIGN KEY (`id_pilote`) REFERENCES `pilote` (`id_pilote`),
  ADD CONSTRAINT `vol_ibfk_3` FOREIGN KEY (`id_aeroport_arrivee_effectif`) REFERENCES `aeroport` (`id_aeroport`),
  ADD CONSTRAINT `vol_ibfk_4` FOREIGN KEY (`id_avion`) REFERENCES `avion` (`id_avion`),
  ADD CONSTRAINT `vol_ibfk_5` FOREIGN KEY (`id_copilote`) REFERENCES `pilote` (`id_pilote`),
  ADD CONSTRAINT `vol_ibfk_6` FOREIGN KEY (`id_aeroport_depart_effectif`) REFERENCES `aeroport` (`id_aeroport`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
