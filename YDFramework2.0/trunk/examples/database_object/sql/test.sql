
-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `admin` tinyint(1) NOT NULL default '0',
  `birthday` int(8) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `users_groups`
-- 

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `joined` int(8) default NULL,
  `active` tinyint(1) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `groups`
-- 

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phones`
-- 

DROP TABLE IF EXISTS `phones`;
CREATE TABLE `phones` (
  `user_id` int(11) NOT NULL default '0',
  `number` varchar(30) NOT NULL default '',
  `ord` int(3) NOT NULL default '0'
) TYPE=MyISAM;