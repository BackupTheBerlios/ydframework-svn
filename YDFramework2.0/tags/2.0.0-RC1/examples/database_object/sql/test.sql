#
# Database : `test`
# 

# --------------------------------------------------------

#
# Table structure for table `address`
#

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `user_id` int(11) NOT NULL default '0',
  `address` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `state` varchar(255) default NULL,
  `country` varchar(255) default NULL,
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Table structure for table `groups`
#

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `date` int(8) default NULL,
  `updated` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Table structure for table `users`
#

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `admin` tinyint(1) NOT NULL default '0',
  `birthday` int(8) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Table structure for table `users_groups`
#

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `joined` int(8) default NULL,
  `active` tinyint(1) NOT NULL default '0'
) TYPE=MyISAM;

