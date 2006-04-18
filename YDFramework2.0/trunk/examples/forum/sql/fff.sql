-- phpMyAdmin SQL Dump
-- version 2.6.4-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 29, 2005 at 10:11 AM
-- Server version: 4.0.15
-- PHP Version: 4.3.10
-- 
-- Database: `fff_forum`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `fff_forums`
-- 

CREATE TABLE `fff_forums` (
  `idForum` int(11) NOT NULL auto_increment,
  `nameForum` varchar(255) NOT NULL default '',
  `order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idForum`)
) TYPE=MyISAM AUTO_INCREMENT=394 ;

-- 
-- Dumping data for table `fff_forums`
-- 

INSERT INTO `fff_forums` VALUES (1, 'Java frameworks', 5);
INSERT INTO `fff_forums` VALUES (2, 'PHP 4 & 5 Frameworks', 14);

-- --------------------------------------------------------

-- 
-- Table structure for table `fff_groups`
-- 

CREATE TABLE `fff_groups` (
  `idGroup` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `rightsLevel` int(11) NOT NULL default '1',
  PRIMARY KEY  (`idGroup`),
  UNIQUE KEY `nameGroup` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `fff_groups`
-- 

INSERT INTO `fff_groups` VALUES (1, 'users', 1);
INSERT INTO `fff_groups` VALUES (2, 'moderators', 3);
INSERT INTO `fff_groups` VALUES (3, 'administrators', 5);
INSERT INTO `fff_groups` VALUES (4, 'inactive users', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `fff_groups_users`
-- 

CREATE TABLE `fff_groups_users` (
  `idGroup` int(11) NOT NULL default '0',
  `idUser` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idGroup`,`idUser`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `fff_groups_users`
-- 

INSERT INTO `fff_groups_users` VALUES (1, 0);
INSERT INTO `fff_groups_users` VALUES (1, 4);
INSERT INTO `fff_groups_users` VALUES (1, 5);
INSERT INTO `fff_groups_users` VALUES (1, 6);
INSERT INTO `fff_groups_users` VALUES (3, 3);
INSERT INTO `fff_groups_users` VALUES (4, 16);

-- --------------------------------------------------------

-- 
-- Table structure for table `fff_posts`
-- 

CREATE TABLE `fff_posts` (
  `idPost` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `content` longtext NOT NULL,
  `idAuthor` int(11) NOT NULL default '0',
  `datePost` datetime NOT NULL default '0000-00-00 00:00:00',
  `dateAnswer` datetime NOT NULL default '0000-00-00 00:00:00',
  `ipAuthor` varchar(15) NOT NULL default '',
  `idEditor` int(11) default NULL,
  `dateEdited` datetime default NULL,
  `ipEditor` varchar(15) default NULL,
  `idPostParent` int(11) default NULL,
  `nbViews` int(11) NOT NULL default '0',
  `nbAnswers` int(11) NOT NULL default '0',
  `idForum` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idPost`),
  FULLTEXT KEY `title` (`title`,`content`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `fff_posts`
-- 

INSERT INTO `fff_posts` VALUES (1, 'PHPMVC', 'Qu''en pensez vous ?', 4, '2005-10-15 15:29:28', '2005-10-15 15:29:28', '192.168.0.66', NULL, NULL, NULL, NULL, 4, 0, 2);
INSERT INTO `fff_posts` VALUES (2, 'YellowDuck', 'Enorme non ?', 4, '2005-10-15 15:30:15', '2005-10-15 15:30:15', '192.168.0.66', NULL, NULL, NULL, NULL, 5, 0, 2);
INSERT INTO `fff_posts` VALUES (3, 'des utilisateurs de Struts ?', 'dans le coin ?', 4, '2005-10-15 15:30:57', '2005-10-15 15:30:57', '192.168.0.66', NULL, NULL, NULL, NULL, 3, 0, 1);
INSERT INTO `fff_posts` VALUES (4, 'Mojavi', 'Moi j''aime pas, et vous ?', 4, '2005-10-15 15:32:31', '2005-10-15 15:32:31', '192.168.0.66', NULL, NULL, NULL, NULL, 4, 0, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `fff_users`
-- 

CREATE TABLE `fff_users` (
  `idUser` int(11) NOT NULL auto_increment,
  `login` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`idUser`),
  UNIQUE KEY `nameUser` (`login`)
) TYPE=MyISAM AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `fff_users`
-- 


INSERT INTO `fff_users` VALUES (3, 'root', '63a9f0ea7bb98050796b649e85481845', 'root');
INSERT INTO `fff_users` VALUES (4, 'user1', '24c9e15e52afc47c225b757e7bee1f9d', 'Compte Test User 1\r\nModérateur');
INSERT INTO `fff_users` VALUES (5, 'user2', '7e58d63b60197ceb55a1c487989a3720', 'Compte Test User 2');
INSERT INTO `fff_users` VALUES (16, 'user3', '92877af70a45fd6a2ed7fe81e1236b78', 'Utilisateur inactif');
