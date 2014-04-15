-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 12, 2014 at 01:48 PM
-- Server version: 5.5.33
-- PHP Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `praticien`
--

-- --------------------------------------------------------

--
-- Table structure for table `wp_custom_categories_test`
--

DROP TABLE IF EXISTS `wp_custom_categories_test`;
CREATE TABLE IF NOT EXISTS `wp_custom_categories_test` (
  `term_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_de` varchar(255) NOT NULL,
  `name_it` varchar(255) NOT NULL,
  `terme_parent` int(11) NOT NULL,
  `rang` tinyint(5) NOT NULL,
  `general` varchar(255) NOT NULL,
  PRIMARY KEY (`term_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=259 ;

--
-- Dumping data for table `wp_custom_categories_test`
--

INSERT INTO `wp_custom_categories_test` (`term_id`, `name`, `name_de`, `name_it`, `terme_parent`, `rang`, `general`) VALUES
(174, 'Droit fondamental', 'Grundrecht', 'Diritto fondamentale', 8, 2, 'Droits fondamentaux'),
(175, 'Droit de cité et droit des étrangers', 'Bürgerrecht und Ausländerrecht', 'Cittadinanza e diritto degli stranieri', 12, 1, ''),
(176, 'Responsabilité de l''État', 'Staatshaftung', 'Responsabilità dello Stato', 6, 1, ''),
(177, 'Fonction publique', 'Öffentliches Dienstverhältnis', 'Pubblica amministrazione', 15, 5, ''),
(178, 'Droits politique', 'Politische Rechte', 'Diritto politici', 8, 3, 'Droits politiques'),
(179, 'Entraide et extradition', 'Rechtshilfe und Auslieferung', 'Assistenza giudiziaria e estradizione', 11, 5, ''),
(180, 'Instruction et formation professionnelle', 'Unterrichtswesen und Berufsausbildung', 'Istruzione e formazione professionale', 2, 1, ''),
(181, 'Art et culture', 'Kunst und Kultur', 'Arte e cultura', 23, 1, ''),
(182, 'Équilibre écologique', 'Ökologisches Gleichgewicht', 'Equilibrio ecologico', 15, 4, ''),
(183, 'Politique de sécurité et de promotion de la paix', 'Sicherheits- und Friedenspolitik', 'Politica di sicurezza e di pace', 15, 11, ''),
(184, 'Finances publiques & droit fiscal', 'Öffentliche Finanzen & Abgaberecht', 'Finanze pubbliche & diritto tributario', 10, 1, ''),
(185, 'Aménagement du territoire et droit public des constructions', 'Raumplanung und öffentliches Baurecht', 'Pianificazione territoriale e diritto pubblico edilizio', 15, 1, ''),
(186, 'Expropriation', 'Enteignung', 'Espropriazione', 15, 8, ''),
(187, 'Énergie & transport & poste et télécommunication, média', 'Energie & Verkehr & Post- und Fernmeldeverkehr, Medien', 'Energia & trasporto & poste e telecomunicazioni, mass media', 17, 1, ''),
(188, 'Énergie', 'Energie', 'Energia', 17, 0, ''),
(189, 'Construction des routes et circulation routière', 'Strassenbau und Strassenverkehr', 'Costruzioni stradali e circolazione stradale', 15, 2, ''),
(190, 'Transport (sans circulation routière)', 'Verkehr (ohne Strassenverkehr)', 'Trasporto (senza circolazione stradale)', 15, 3, ''),
(191, 'Poste et télécommunications', 'Post- und Fernmelde Verkehr', 'Posta e telecomunicazioni', 24, 1, ''),
(192, 'Média', 'Medien', 'Mass media', 4, 1, ''),
(193, 'Droit des assurances sociales', 'Sozialversicherungsrecht', 'Diritto delle assicurazioni sociali', 16, 8, ''),
(194, 'Assurance-vieillesse et survivants', 'Alters- und Hinterlassenenversicherung', 'Assicurazione per la vecchiaia e per i superstiti', 16, 7, ''),
(195, 'Assurance-invalidité', 'Invalidenversicherung', 'Assicurazione per l''invalidità', 16, 5, ''),
(196, 'Prestations complémentaires à l''AVS/AI', 'Ergänzungsleistung', 'Prestazione complementari', 16, 9, ''),
(197, 'Prévoyance professionnelle', 'Berufliche Vorsorge', 'Previdenza professionale', 16, 10, ''),
(198, 'Assurance maladie', 'Krankenversicherung', 'Assicurazione contro le malattie', 16, 6, ''),
(199, 'Assurance-accidents', 'Unfallversicherung', 'Assicurazione contro gli infortuni', 16, 3, ''),
(200, 'Assurance militaire', 'Militärversicherung', 'Assicurazione militare', 16, 1, ''),
(201, 'Régime allocations et pertes de gains', 'Erwerbersatzordnung', 'Indennità per perdita di guadagno', 16, 11, ''),
(202, 'Allocation familiale dans l''agriculture', 'Familienzulagen in der Landwirtschaft', 'Assegni familiari nell''agricoltura', 16, 12, ''),
(203, 'Assurance-chômage', 'Arbeitslosenversicherung', 'Assicurazione contro la disoccupazione', 16, 4, ''),
(204, 'Santé & sécurité sociale', 'Gesundheitswesen & soziale Sicherheit', 'Sanità & sicurezza sociale', 7, 1, ''),
(205, 'Économie', 'Wirtschaft', 'Economia', 1, 1, ''),
(206, 'Procédure', 'Verfahren', 'Procedura', 3, 0, ''),
(207, 'Procédure civile', 'Zivilprozess', 'Procedura civile', 3, 3, ''),
(208, 'Procédure pénale', 'Strafprozess', 'Procedura penale', 3, 2, ''),
(209, 'Procédure administrative', 'Verwaltungsverfahren', 'Procedura amministrativa', 3, 4, ''),
(210, 'Questions de compétences, garantie du juge du domicile et du juge naturel', 'Zuständigkeitsfragen, Garantie des Wohnsitzrichters und des verfassungsmässigen Richters', 'Quesiti di competenza, garanzia del foro del domicilio e del giudice costituzionale', 3, 16, ''),
(211, 'Exécution forcée', 'Zwangsvollstreckung', 'Esecuzione forzata', 3, 6, ''),
(212, 'Juridiction arbitrale', 'Schiedsgerichtsbarkeit', 'Giuridizione arbitrale', 20, 1, ''),
(213, 'Droit des poursuites et faillites', 'Schuldbetreibungs- und Konkursrecht', 'Diritto delle esecuzioni e del fallimento', 9, 1, ''),
(214, 'Droit privé', 'Privatrecht', 'Diritto privato', 14, 0, ''),
(215, 'Droit civil', 'Zivilrecht', 'Diritto civile', 14, 10, ''),
(216, 'Droit privé (en général)', 'Privatrecht (allgemein)', 'Diritto privato (in generale)', 14, 1, ''),
(217, 'Droit des personnes', 'Personenrecht', 'Diritto delle persone', 14, 2, ''),
(218, 'Droit de la famille', 'Familienrecht', 'Diritto di famiglia', 14, 3, ''),
(219, 'Droit des successions', 'Erbrecht', 'Diritto successorio', 14, 4, ''),
(220, 'Droits réels', 'Sachenrecht', 'Diritti reali', 14, 5, ''),
(221, 'Registre', 'Register', 'Registro', 21, 3, ''),
(222, 'Droit des obligations et droit commercial', 'Obligationenrecht und Handelsrecht', 'Diritto delle obbligazioni e diritto commerciale', 18, 1, ''),
(223, 'Droit des obligations (général)', 'Obligationenrecht (allgemein)', 'Diritto delle obbligazioni (generale)', 18, 0, ''),
(224, 'Droit des contrats', 'Vertragsrecht', 'Diritto contrattuale', 18, 7, ''),
(225, 'Droit des sociétés', 'Gesellschaftsrecht', 'Diritto delle società', 21, 2, ''),
(226, 'Papiers-valeurs', 'Wertpapierrecht', 'Cartavalore', 21, 1, ''),
(227, 'Assurance responsabilité civile', 'Haftpflichtrecht', 'Assicurazione responsabilità civile', 18, 4, 'Responsabilité civile'),
(228, 'Propriété intellectuelle, concurrence et cartels', 'Immaterialgüter-, Wettbewerbs- und Kartellrecht', 'Proprietà intelletuale, concorrenza e cartelli', 13, 1, ''),
(229, 'Droit pénal', 'Strafrecht', 'Diritto penale', 11, 0, ''),
(230, 'Droit pénal (en général)', 'Strafrecht (allgemein)', 'Diritto penale (in generale)', 11, 1, ''),
(231, 'Infractions', 'Straftaten', 'Infrazione', 11, 2, ''),
(232, 'Droit pénal administratif', 'Verwaltungsstrafrecht', 'Diritto penale amministrativo', 11, 6, ''),
(233, 'Exécution des peines et des mesures', 'Straf- und Massnahmenvollzug', 'Esecuzione delle pene e delle misure', 11, 4, ''),
(234, 'Procédures disciplinaires', 'Aufsichtsbeschwerden', 'Procedimento disciplinare', 3, 15, ''),
(235, 'Recours en matière de surveillance', 'Zuflucht über die Überwachung', 'Ricorso di sorveglianza', 15, 7, ''),
(244, 'Droit de l''avocat', 'Anwaltsgesetz', 'Avvocatura', 19, 1, ''),
(247, 'Général', 'Allgemeine', 'Generale', 0, 0, ''),
(248, 'Régime allocations et pertes de gain', 'Régime allocations et pertes de gain-allemand', 'Régime allocations et pertes de gain-italien', 0, 0, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
