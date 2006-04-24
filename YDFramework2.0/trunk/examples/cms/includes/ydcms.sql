# MySQL-Front Dump 2.5
#
# Host: localhost   Database: ydcms
# --------------------------------------------------------
# Server version 4.0.20a-nt

USE ydcms;


#
# Table structure for table 'content'
#

DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `nleft` int(11) NOT NULL default '0',
  `nright` int(11) NOT NULL default '0',
  `nlevel` int(11) NOT NULL default '0',
  `created` int(11) NOT NULL default '0',
  `created_by` varchar(255) NOT NULL default '',
  `modified` int(11) NOT NULL default '0',
  `modified_by` varchar(255) NOT NULL default '',
  `content_type` varchar(255) NOT NULL default 'YDCmfContentNode',
  `can_delete` tinyint(1) NOT NULL default '1',
  `title_nl` longtext NOT NULL,
  `title_en` longtext NOT NULL,
  `title_fr` longtext NOT NULL,
  `property_0_nl` longtext,
  `property_0_en` longtext,
  `property_0_fr` longtext,
  `property_1_nl` longtext,
  `property_1_en` longtext,
  `property_1_fr` longtext,
  `property_2_nl` longtext,
  `property_2_en` longtext,
  `property_2_fr` longtext,
  `property_3_nl` longtext,
  `property_3_en` longtext,
  `property_3_fr` longtext,
  `property_4_nl` longtext,
  `property_4_en` longtext,
  `property_4_fr` longtext,
  `property_5_nl` longtext,
  `property_5_en` longtext,
  `property_5_fr` longtext,
  `property_6_nl` longtext,
  `property_6_en` longtext,
  `property_6_fr` longtext,
  `property_7_nl` longtext,
  `property_7_en` longtext,
  `property_7_fr` longtext,
  `property_8_nl` longtext,
  `property_8_en` longtext,
  `property_8_fr` longtext,
  `property_9_nl` longtext,
  `property_9_en` longtext,
  `property_9_fr` longtext,
  `property_10_nl` longtext,
  `property_10_en` longtext,
  `property_10_fr` longtext,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `content_name` (`name`),
  KEY `content_parent_id` (`parent_id`),
  KEY `content_nleft` (`nleft`),
  KEY `content_nright` (`nright`),
  KEY `content_nlevel` (`nlevel`),
  KEY `content_content_type` (`content_type`),
  KEY `content_can_delete` (`can_delete`)
) TYPE=MyISAM;



#
# Dumping data for table 'content'
#

INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("1", "0", "home", "1", "36", "1", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfcontentnode", "1", "Titel Nederlands", "Title English", "Titre Fran&ccedil;ais", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("2", "1", "home2", "2", "3", "2", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfhome", "1", "Titel2 Nederlands", "Title2 English", "Titre2 Fran&ccedil;ais", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("3", "1", "news", "4", "9", "2", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfhome", "1", "News Nederlands", "News English", "News Fran&ccedil;ais", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("4", "3", "news/20060417_krabrally_2006", "5", "6", "3", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfhome", "1", "News Krabrally 2006 Nederlands", "News Krabrally 2006 English", "News Krabrally 2006 Fran&ccedil;ais", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("5", "3", "news/20060418_krabrally_2007", "7", "8", "3", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfhome", "1", "News Krabrally 2007 Nederlands", "News Krabrally 2007 English", "News Krabrally 2007 Fran&ccedil;ais", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("6", "1", "photo", "10", "35", "2", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgallery", "1", "Photo Galleries", "Photo Galleries", "Photo Galleries", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("7", "6", "photo/krabrally2006", "11", "22", "3", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgalleryalbum", "1", "Krabrally 2006", "Krabrally 2006", "Krabrally 2006", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("8", "6", "photo/krabrally2007", "23", "34", "3", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgalleryalbum", "1", "Krabrally 2007", "Krabrally 2007", "Krabrally 2007", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("131", "7", "photo/krabrally2006/19990716-MVC-P-014F", "12", "13", "4", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-014F.JPG", "19990716-MVC-P-014F.JPG", "19990716-MVC-P-014F.JPG", "uploads/krabrally2006/19990716-MVC-P-014F.JPG", "uploads/krabrally2006/19990716-MVC-P-014F.JPG", "uploads/krabrally2006/19990716-MVC-P-014F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("132", "7", "photo/krabrally2006/19990716-MVC-P-015F", "14", "15", "4", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-015F.JPG", "19990716-MVC-P-015F.JPG", "19990716-MVC-P-015F.JPG", "uploads/krabrally2006/19990716-MVC-P-015F.JPG", "uploads/krabrally2006/19990716-MVC-P-015F.JPG", "uploads/krabrally2006/19990716-MVC-P-015F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("133", "7", "photo/krabrally2006/19990716-MVC-P-016F", "16", "17", "4", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-016F.JPG", "19990716-MVC-P-016F.JPG", "19990716-MVC-P-016F.JPG", "uploads/krabrally2006/19990716-MVC-P-016F.JPG", "uploads/krabrally2006/19990716-MVC-P-016F.JPG", "uploads/krabrally2006/19990716-MVC-P-016F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("134", "7", "photo/krabrally2006/19990716-MVC-P-017F", "18", "19", "4", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-017F.JPG", "19990716-MVC-P-017F.JPG", "19990716-MVC-P-017F.JPG", "uploads/krabrally2006/19990716-MVC-P-017F.JPG", "uploads/krabrally2006/19990716-MVC-P-017F.JPG", "uploads/krabrally2006/19990716-MVC-P-017F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("135", "7", "photo/krabrally2006/19990716-MVC-P-018F", "20", "21", "4", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-018F.JPG", "19990716-MVC-P-018F.JPG", "19990716-MVC-P-018F.JPG", "uploads/krabrally2006/19990716-MVC-P-018F.JPG", "uploads/krabrally2006/19990716-MVC-P-018F.JPG", "uploads/krabrally2006/19990716-MVC-P-018F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("136", "8", "photo/krabrally2007/19990716-MVC-P-019F", "24", "25", "4", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-019F.JPG", "19990716-MVC-P-019F.JPG", "19990716-MVC-P-019F.JPG", "uploads/krabrally2007/19990716-MVC-P-019F.JPG", "uploads/krabrally2007/19990716-MVC-P-019F.JPG", "uploads/krabrally2007/19990716-MVC-P-019F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("137", "8", "photo/krabrally2007/19990716-MVC-P-020F", "26", "27", "4", "1145389437", "pclaerho-lt.cem.creo.com", "1145389437", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-020F.JPG", "19990716-MVC-P-020F.JPG", "19990716-MVC-P-020F.JPG", "uploads/krabrally2007/19990716-MVC-P-020F.JPG", "uploads/krabrally2007/19990716-MVC-P-020F.JPG", "uploads/krabrally2007/19990716-MVC-P-020F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("138", "8", "photo/krabrally2007/19990716-MVC-P-021F", "28", "29", "4", "1145389438", "pclaerho-lt.cem.creo.com", "1145389438", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-021F.JPG", "19990716-MVC-P-021F.JPG", "19990716-MVC-P-021F.JPG", "uploads/krabrally2007/19990716-MVC-P-021F.JPG", "uploads/krabrally2007/19990716-MVC-P-021F.JPG", "uploads/krabrally2007/19990716-MVC-P-021F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("139", "8", "photo/krabrally2007/19990716-MVC-P-022F", "30", "31", "4", "1145389438", "pclaerho-lt.cem.creo.com", "1145389438", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-022F.JPG", "19990716-MVC-P-022F.JPG", "19990716-MVC-P-022F.JPG", "uploads/krabrally2007/19990716-MVC-P-022F.JPG", "uploads/krabrally2007/19990716-MVC-P-022F.JPG", "uploads/krabrally2007/19990716-MVC-P-022F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `content` (`id`, `parent_id`, `name`, `nleft`, `nright`, `nlevel`, `created`, `created_by`, `modified`, `modified_by`, `content_type`, `can_delete`, `title_nl`, `title_en`, `title_fr`, `property_0_nl`, `property_0_en`, `property_0_fr`, `property_1_nl`, `property_1_en`, `property_1_fr`, `property_2_nl`, `property_2_en`, `property_2_fr`, `property_3_nl`, `property_3_en`, `property_3_fr`, `property_4_nl`, `property_4_en`, `property_4_fr`, `property_5_nl`, `property_5_en`, `property_5_fr`, `property_6_nl`, `property_6_en`, `property_6_fr`, `property_7_nl`, `property_7_en`, `property_7_fr`, `property_8_nl`, `property_8_en`, `property_8_fr`, `property_9_nl`, `property_9_en`, `property_9_fr`, `property_10_nl`, `property_10_en`, `property_10_fr`) VALUES("140", "8", "photo/krabrally2007/19990716-MVC-P-023F", "32", "33", "4", "1145389438", "pclaerho-lt.cem.creo.com", "1145389438", "pclaerho-lt.cem.creo.com", "ydcmfgalleryitem", "1", "19990716-MVC-P-023F.JPG", "19990716-MVC-P-023F.JPG", "19990716-MVC-P-023F.JPG", "uploads/krabrally2007/19990716-MVC-P-023F.JPG", "uploads/krabrally2007/19990716-MVC-P-023F.JPG", "uploads/krabrally2007/19990716-MVC-P-023F.JPG", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
