-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 18 Juillet 2008 à 21:32
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.4-2ubuntu5.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `atomik`
--

-- --------------------------------------------------------

--
-- Structure de la table `atomik_pages`
--

CREATE TABLE IF NOT EXISTS `atomik_pages` (
  `id` int(11) NOT NULL auto_increment,
  `filename` varchar(255) NOT NULL,
  `version` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `atomik_pages_fields`
--

CREATE TABLE IF NOT EXISTS `atomik_pages_fields` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `version` int(11) NOT NULL,
  `lang` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `atomik_pages_versions`
--

CREATE TABLE IF NOT EXISTS `atomik_pages_versions` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
