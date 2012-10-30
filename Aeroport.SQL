drop database if exists aeroport;

create database if not exists aeroport;
use aeroport;


# -----------------------------------------------------------------------------
#       table : pays
# -----------------------------------------------------------------------------

create table if not exists pays
 (
   code_pays integer not null  ,
   nom_pays varchar(255) not null  , 
   alpha2 char(2) null  ,
   alpha3 char(3) null  ,
   primary key (code_pays) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;

# -----------------------------------------------------------------------------
#       table : ville
# -----------------------------------------------------------------------------

create table if not exists ville
 (
   code_ville varchar(255) not null  ,
   nom_ville varchar(255) not null  ,
   code_pays integer not null  ,
   code_postal varchar(255) null  , 
   primary key (code_ville) ,
   Foreign key (code_pays) references pays (code_pays) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : aeroport
# -----------------------------------------------------------------------------

create table if not exists aeroport
 (
   id_aeroport char(3) not null  ,
   nom varchar(255) not null  ,
   code_ville varchar(255) not null  ,   
   longueur_piste bigint(4) not null  , 
   primary key (id_aeroport) ,
   Foreign key (code_ville) references ville (code_ville) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : pilote
# -----------------------------------------------------------------------------

create table if not exists pilote
 (
   id_pilote integer not null auto_increment ,
   nom varchar(255) not null  ,
   prenom varchar(255) not null  ,
   email varchar(255) not null  ,
   password varchar(255) not null  ,
   code_ville varchar(255) not null  ,
   adresse varchar(255) not null  ,
   telephone integer(10) null  ,
   disponibilite bool not null default 1, 
   primary key (id_pilote) ,
   Foreign key (code_ville) references ville (code_ville)
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;



# -----------------------------------------------------------------------------
#       table : type_avion
# -----------------------------------------------------------------------------

create table if not exists type_avion
 (
   id_type_avion integer not null auto_increment,
   libelle varchar(255) not null  ,
   rayon_action integer(10) not null  ,
   longueur_decollage integer(4) not null  ,
   longueur_atterissage integer(4) not null   , 
   primary key (id_type_avion) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;

# -----------------------------------------------------------------------------
#       table : jour_semaine
# -----------------------------------------------------------------------------

create table if not exists jour_semaine
 (
   numero_jour integer not null  ,
   libelle varchar(255) not null  , 
   primary key (numero_jour) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : avion
# -----------------------------------------------------------------------------

create table if not exists avion
 (
   id_avion varchar(255) not null  ,
   id_type_avion integer not null  ,
   nb_places integer(3) not null  ,
   total_heure_vol integer(6) not null default 0,
   nb_heures_gd_revision integer(6) not null default 0,
   disponibilite_avion bool not null default 0 , 
   primary key (id_avion) ,
   foreign key (id_type_avion) references type_avion (id_type_avion)
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;

# -----------------------------------------------------------------------------
#       table : ligne
# -----------------------------------------------------------------------------

create table if not exists ligne
 (
   numero_ligne integer not null auto_increment ,
   id_aeroport_origine char(3) not null  ,
   id_aeroport_depart char(3) not null  ,
   id_aeroport_arrivee char(3) not null  ,
   heure_depart time not null  , 
   heure_arrivee time not null  ,
   distance integer not null  ,
   primary key (numero_ligne) ,
   Foreign key (id_aeroport_origine) references aeroport (id_aeroport) ,
   Foreign key (id_aeroport_depart) references aeroport (id_aeroport) ,
   Foreign key (id_aeroport_arrivee) references aeroport (id_aeroport)
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;

# -----------------------------------------------------------------------------
#       table : vol
# -----------------------------------------------------------------------------

create table if not exists vol
 (
   id_vol integer not null ,
   numero_ligne integer not null  ,
   id_aeroport_depart_effectif char(3) not null  ,
   id_aeroport_arrivee_effectif char(3) not null  ,
   date_depart date not null  ,
   date_arrivee date not null  , 
   id_avion varchar(255) null  ,
   id_pilote integer null  ,
   id_copilote integer null  ,
   heure_arrivee_effective time null  ,
   tarif decimal(13,2) null ,
   primary key (numero_ligne,id_vol) ,
   Foreign key (numero_ligne) references ligne (numero_ligne) ON DELETE CASCADE ,
   Foreign Key (id_pilote) references pilote (id_pilote),
   Foreign Key (id_aeroport_arrivee_effectif) references aeroport (id_aeroport) ,
   Foreign Key (id_avion) references avion (id_avion),
   Foreign key (id_copilote) references pilote (id_pilote),
   Foreign key (id_aeroport_depart_effectif) references aeroport (id_aeroport) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;



# -----------------------------------------------------------------------------
#       table : type_remarque
# -----------------------------------------------------------------------------

create table if not exists type_remarque
 (
   id_type_remarque integer not null auto_increment ,
   libelle_type_remarque varchar(255) not null  , 
   primary key (id_type_remarque) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : service
# -----------------------------------------------------------------------------

create table if not exists service
 (
   id_service integer not null auto_increment ,
   libelle_service varchar(255) not null , 
   primary key (id_service)
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : remarque
# -----------------------------------------------------------------------------

create table if not exists remarque
 (
   id_remarque integer not null auto_increment ,
   libelle_remarque varchar(255) not null  , 
   id_type_remarque integer not null  ,
   id_service integer not null ,
   id_vol integer not null  ,
   numero_ligne integer not null  ,
   primary key (id_remarque) ,
   Foreign key (numero_ligne,id_vol) references vol (numero_ligne,id_vol) ,
   Foreign key (id_type_remarque) references type_remarque (id_type_remarque) ,
   Foreign key (id_service) references service (id_service) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;

# -----------------------------------------------------------------------------
#       table : utilisateur
# -----------------------------------------------------------------------------

create table if not exists utilisateur
 (
   login varchar(255) not null  ,
   id_service integer not null  ,
   nom varchar(255) not null  ,
   prenom varchar(255) not null  ,
   email varchar(255) not null  ,
   password varchar(255) not null  ,
   code_ville varchar(255) not null  ,
   adresse varchar(255) not null  ,
   telephone integer(10) null  ,
   date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
   primary key (login),
   Foreign Key (code_ville) references ville (code_ville),
   Foreign Key (id_service) references service (id_service)
 ) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;



# -----------------------------------------------------------------------------
#       table : maintenance
# -----------------------------------------------------------------------------

create table if not exists maintenance
 (
   id_maintenance integer not null auto_increment ,
   id_avion varchar(255) not null  ,
   date_prevue date not null  ,
   duree_prevue time not null  , 
   primary key (id_maintenance) ,
   Foreign key (id_avion) references avion (id_avion)
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : intervention
# -----------------------------------------------------------------------------

create table if not exists intervention
 (
   id_intervention integer not null auto_increment ,
   id_maintenance integer not null  ,
   login varchar(255) not null  ,
   date_effective date not null  ,
   duree_effective time not null  ,
   commentaire varchar(255) null ,
   primary key (id_intervention) ,
   Foreign key (id_maintenance) references maintenance (id_maintenance) ON DELETE CASCADE ,
   Foreign key (login) references utilisateur(login) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : desservirVilleAeroport
# -----------------------------------------------------------------------------

create table if not exists desservirVilleAeroport
 (
   code_ville varchar(255) not null  ,
   id_aeroport char(3) not null  , 
   primary key (code_ville,id_aeroport) ,
   Foreign key (code_ville) references ville (code_ville) ,
   foreign key (id_aeroport) references aeroport (id_aeroport)
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : astreinte
# -----------------------------------------------------------------------------

create table if not exists astreinte
 (
   id_aeroport char(3) not null  ,
   id_pilote integer not null  ,
   date_astreinte timestamp not null  , 
   primary key (id_aeroport,id_pilote,date_astreinte) ,
   Foreign key (id_aeroport) references aeroport (id_aeroport) ,
   Foreign key (id_pilote) references pilote (id_pilote)
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : periodicite alias contenir
# -----------------------------------------------------------------------------

create table if not exists periodicite
 (
   numero_ligne integer not null ,
   numero_jour integer not null  , 
   primary key (numero_ligne,numero_jour) ,
   Foreign key (numero_ligne) references ligne (numero_ligne) ON DELETE CASCADE ,
   Foreign key (numero_jour) references jour_semaine (numero_jour)
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : etreBreveter
# -----------------------------------------------------------------------------

create table if not exists etreBreveter
 (
   id_pilote integer not null  ,
   id_type_avion integer not null  ,
   date timestamp not null   , 
   primary key (id_pilote,id_type_avion) ,
   Foreign key (id_pilote) references pilote (id_pilote) ,
   Foreign key (id_type_avion) references type_avion (id_type_avion)
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;

# -----------------------------------------------------------------------------
#       table : client
# -----------------------------------------------------------------------------

create table if not exists client
 (
   id_client integer not null auto_increment ,
   nom varchar(255) not null  ,
   prenom varchar(255) not null  ,
   email varchar(255) not null  ,
   password varchar(255) not null  ,
   code_ville varchar(255) not null  ,
   adresse varchar(255) not null  ,
   telephone integer(10) null  ,
   date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
   primary key (id_client) ,
   Foreign key (code_ville) references ville (code_ville) 
 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;


# -----------------------------------------------------------------------------
#       table : reservation
# -----------------------------------------------------------------------------

create table if not exists reservation
 (
   id_reservation integer not null auto_increment ,
   id_client integer not null  ,
   numero_ligne integer not null  ,
   id_vol integer not null  , 
   nbreservation integer(3) not null , 
   date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
   primary key (id_reservation) ,
   Foreign key (id_client) references client (id_client) ,
   Foreign key (numero_ligne,id_vol) references vol (numero_ligne,id_vol) ON DELETE CASCADE 

 ) 
 ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER DATABASE aeroport charset=utf8;

# -----------------------------------------------------------------------------
#       Insertion Données
# -----------------------------------------------------------------------------

INSERT INTO `jour_semaine` (`numero_jour`, `libelle`) VALUES
(1, 'Lundi'),
(2, 'Mardi'),
(3, 'Mercredi'),
(4, 'Jeudi'),
(5, 'Vendredi'),
(6, 'Samedi'),
(7, 'Dimanche');

INSERT INTO `pays` (`code_pays`, `nom_pays`, `alpha2`, `alpha3`) VALUES
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
  
INSERT INTO `type_avion` (`id_type_avion`, `libelle`, `rayon_action`, `longueur_decollage`, `longueur_atterissage`) VALUES
(1, 'A380-800', 15200, 2750, 3000),
(2, 'A340-600', 14600, 2300, 2500),
(3, 'A319', 6650, 2000, 2300),
(4, 'A340-300', 13700, 2700, 2900),
(5, '787-8', 15000, 3000, 3300),
(6, '777-200LR', 17445, 2950, 3200);

INSERT INTO `avion` (`id_avion`, `id_type_avion`, `nb_places`, `total_heure_vol`, `nb_heures_gd_revision`, `disponibilite_avion`) VALUES
('1', 1, 525, 15, 0, 1),
('10', 2, 380, 254, 0, 1),
('2', 2, 295, 150, 0, 1),
('3', 3, 124, 3512, 0, 1),
('4', 4, 295, 250, 0, 1),
('5', 5, 237, 600, 0, 0),
('6', 6, 301, 810, 0, 1),
('7', 3, 124, 570, 0, 0),
('8', 2, 380, 30, 0, 1),
('9', 1, 525, 2000, 0, 0);

INSERT INTO `ville` (`code_ville`, `nom_ville`, `code_pays`, `code_postal`) VALUES
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

INSERT INTO `client` (`id_client`, `nom`, `prenom`, `email`, `password`, `code_ville`, `adresse`, `telephone`, `date`) VALUES
(1, 'Franquatin', 'Paulette', 'paulette.franquatin@gmail.com', 'paulou', '2', '12bis rue du jeu de paume', NULL, '2012-10-22 21:01:07'),
(2, 'Mojou', 'Bertrand', 'bertrand.mojou@gmail.com', 'bertrand', '7', '415 place de rien', NULL, '2012-10-22 21:01:07');

INSERT INTO `aeroport` (`id_aeroport`, `nom`, `code_ville`, `longueur_piste`) VALUES
('CDG', 'Aéroport Charles-de-Gaulle', '1', 4200),
('GIG', 'Aéroport international Galeão', '10', 4000),
('JFK', 'Aéroport international John-F.-Kennedy', '9', 4442),
('LHR', 'Aéroport de Londres Heathrow', '4', 3902),
('LYS', 'Aéroport Lyon-Saint-Exupéry', '3', 4000),
('MAN', 'Aéroport international de Manchester', '5', 3048),
('MRS', 'Aéroport Marseille Provence', '2', 3500),
('MUC', 'Aéroport international Franz-Josef-Strauss', '6', 4000),
('PEK', 'Beijing-Capital', '8', 3800),
('SXF', 'Aéroport international de Berlin-Schönefeld', '7', 3000);

INSERT INTO `desservirVilleAeroport` (`code_ville`, `id_aeroport`) VALUES
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

INSERT INTO `etreBreveter` (`id_pilote`, `id_type_avion`, `date`) VALUES
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

INSERT INTO `service` (`id_service`, `libelle_service`) VALUES
(1, 'Direction stratégique'),
(2, 'Service commercial'),
(3, 'Service maintenance'),
(4, 'Direction des ressources humaines'),
(5, 'Service planning'),
(6, 'Service d''exploitation'),
(7, 'Service de logistique commerciale');

INSERT INTO `type_remarque` (`id_type_remarque`, `libelle_type_remarque`) VALUES
(1, 'Cuisine'),
(2, 'Matériel spécifique'),
(3, 'Traitement médical'),
(4, 'Prise en charge'),
(5, 'Autre');

INSERT INTO `utilisateur` (`login`, `id_service`, `nom`, `prenom`, `email`, `password`, `code_ville`, `adresse`, `telephone`, `date`) VALUES
('Annette', 2, 'Duhamel', 'Annette', 'annette.duhamel@gmail.com', 'annie', '2', '156 Rue du Général de Gaulle', 125659748, '2012-10-22 20:42:55'),
('Blanche', 4, 'François', 'Blanche', 'blanche.francois@gmail.com', 'blanou', '3', '24 place rouge', 415623514, '2012-10-22 20:42:55'),
('Gérard', 3, 'Hanconinna', 'Gérard', 'Gerard.hanconnina@gmail.com', 'gege', '2', '25 Rue pétain', 126548795, '2012-10-22 20:42:55'),
('Jean-michel', 1, 'Durand', 'Jean-michel', 'jeanmichel.durand@gmail.com', 'jean-mi', '1', '95 Place de l''étoile', 562154987, '2012-10-22 20:42:55'),
('Kévin', 5, 'Kitvikosky', 'Kévin', 'kevin.kitvikosky', 'keke', '5', '653 Main Street', 555365125, '2012-10-22 20:42:55');

INSERT INTO `astreinte` (`id_aeroport`, `id_pilote`, `date_astreinte`) VALUES
('CDG', 4, '2012-10-22 20:58:01'),
('CDG', 6, '2012-10-22 20:58:41');

INSERT INTO `ligne` (`numero_ligne`, `id_aeroport_origine`, `id_aeroport_depart`, `id_aeroport_arrivee`, `heure_depart`, `heure_arrivee`, `distance`) VALUES
(17, 'CDG', 'CDG', 'GIG', '10:12:23', '10:12:26', 1100),
(16, 'CDG', 'CDG', 'GIG', '10:13:18', '10:13:20', 15000),
(18, 'CDG', 'CDG', 'MRS', '15:00:00', '18:00:00', 2000),
(19, 'LYS', 'LYS', 'PEK', '08:00:00', '18:00:00', 350),
(20, 'JFK', 'JFK', 'MRS', '08:00:00', '15:00:00', 2500),
(21, 'MAN', 'MAN', 'MUC', '15:00:00', '19:00:00', 16000),
(22, 'PEK', 'PEK', 'LYS', '08:00:00', '15:00:00', 1500),
(23, 'GIG', 'GIG', 'SXF', '06:00:00', '15:00:00', 13687),
(24, 'MAN', 'MAN', 'CDG', '09:00:00', '12:00:00', 1564),
(25, 'LHR', 'LHR', 'MUC', '02:00:00', '06:00:00', 12354),
(26, 'MUC', 'MUC', 'JFK', '05:00:00', '10:00:00', 1547);

INSERT INTO `periodicite` (`numero_ligne`, `numero_jour`) VALUES
(20, 1),
(21, 1),
(23, 1),
(24, 1),
(18, 2),
(20, 2),
(22, 2),
(25, 2),
(26, 2),
(23, 3),
(25, 3),
(18, 4),
(19, 4),
(21, 4),
(22, 4),
(24, 4),
(25, 4),
(20, 5),
(23, 5),
(24, 5),
(26, 5),
(19, 6),
(22, 6),
(21, 7),
(23, 7),
(26, 7);

INSERT INTO `vol` (`id_vol`, `numero_ligne`, `id_aeroport_depart_effectif`, `id_aeroport_arrivee_effectif`, `date_depart`, `date_arrivee`, `id_avion`, `id_pilote`, `id_copilote`, `heure_arrivee_effective`, `tarif`) VALUES
(1, 18, 'CDG', 'MRS', '2012-10-09', '2012-10-09', '2', 4, 3, '15:00:00', '150.00'),
(1, 19, 'LYS', 'PEK', '2012-10-24', '2012-10-25', '2', 7, 2, '18:00:00', '3000.00');

