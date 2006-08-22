CREATE TABLE YDCMStatistics_downloads (
  download_is INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  date DATETIME NULL,
  filename VARCHAR(255) NULL,
  PRIMARY KEY(download_is)
);

CREATE TABLE YDCMLanguages (
  language_id VARCHAR(255) NOT NULL,
  name VARCHAR(50) NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 0,
  visitors_default TINYINT(1) NOT NULL DEFAULT 0,
  admin_default TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY(language_id)
);

INSERT INTO `ydcmlanguages` (`language_id`,`name`,`active`,`visitors_default`,`admin_default`) VALUES ('en','English',1,1,1);
INSERT INTO `ydcmlanguages` (`language_id`,`name`,`active`,`visitors_default`,`admin_default`) VALUES ('pt','Português',1,0,0);


CREATE TABLE YDCMStatistics_searches (
  search_id INTEGER(10) NOT NULL AUTO_INCREMENT,
  date DATETIME NOT NULL,
  word VARCHAR(255) NOT NULL,
  PRIMARY KEY(search_id)
);

CREATE TABLE YDCMTree (
  content_id INTEGER(10) NOT NULL AUTO_INCREMENT,
  parent_id INTEGER UNSIGNED NULL,
  nleft INTEGER NOT NULL DEFAULT 0,
  nright INTEGER NOT NULL DEFAULT 0,
  nlevel INTEGER NOT NULL DEFAULT 0,
  position INTEGER(10) NULL DEFAULT 1,
  type VARCHAR(20) NOT NULL,
  reference VARCHAR(100) NOT NULL,
  state TINYINT(1) NOT NULL,
  access TINYINT(2) NOT NULL DEFAULT 1,
  searcheable TINYINT(1) NOT NULL,
  published_date_start DATETIME NOT NULL,
  published_date_end DATETIME NOT NULL,
  candrag TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  candrop TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY(content_id)
);

INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (1,NULL,1,22,1,1,'YDCMRoot','',7,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (2,1,2,3,2,1,'YDCMRootmenu','menu Code Paste',1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (3,1,4,5,2,1,'YDCMRootmenu','menu Documentation',1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (4,1,6,13,2,1,'YDCMRootmenu','menu Books',1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (5,4,7,8,3,1,'YDCMPage','Apache',1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (6,4,9,10,3,1,'YDCMPage','PostegreSQL',1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (7,4,11,12,3,1,'YDCMPage','mysql',0,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (8,1,14,21,2,1,'YDCMRootmenu','links',1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (9,8,15,16,3,1,'YDCMPage','databases',1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (10,8,17,18,3,1,'YDCMPage','generators',1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
INSERT INTO `ydcmtree` (`content_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`position`,`type`,`reference`,`state`,`access`,`searcheable`,`published_date_start`,`published_date_end`,`candrag`,`candrop`) VALUES (11,8,19,20,3,1,'YDCMPage','portals',1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);


CREATE TABLE YDCMUsers (
  user_id INTEGER(10) NOT NULL AUTO_INCREMENT,
  parent_id INTEGER UNSIGNED NOT NULL,
  type  VARCHAR(100) NOT NULL DEFAULT 'YDCMVisitor',
  nleft INTEGER NOT NULL,
  nright INTEGER NOT NULL,
  nlevel INTEGER NOT NULL,
  position INTEGER UNSIGNED NULL DEFAULT 1,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(100) NOT NULL,
  name VARCHAR(255) NULL,
  email VARCHAR(255) NULL,
  other TEXT NULL,
  language_id VARCHAR(255) NOT NULL,
  template VARCHAR(255) NULL DEFAULT 'default',
  state TINYINT(1) NOT NULL,
  login_start DATETIME NOT NULL,
  login_end DATETIME NOT NULL,
  login_counter INTEGER(11) NULL,
  login_last DATETIME NOT NULL,
  login_current DATETIME NOT NULL,
  created_user INTEGER(20) NULL,
  created_date DATETIME NOT NULL,
  PRIMARY KEY(user_id),
  FOREIGN KEY(language_id)
    REFERENCES YDCMLanguages(language_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (1,0,1,24,1,'','',NULL,NULL,NULL,'en',0,'0000-00-00 00:00:00','0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00','YDCMRoot',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (2,1,2,21,2,'','',NULL,NULL,NULL,'en',1,'0000-00-00 00:00:00','0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00','YDCMUseradministrators',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (3,1,22,23,2,'','',NULL,NULL,NULL,'en',0,'0000-00-00 00:00:00','0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00','YDCMUservisitors',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (100,2,3,20,3,'admin','21232f297a57a5a743894a0e4a801fc3','Admin','email@host.com','other info','en',1,'1970-01-01 00:00:00','1970-01-01 00:00:00',40,'2006-08-15 16:34:08','2006-08-15 16:35:51',0,'0000-00-00 00:00:00','YDCMUseradministrator',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (101,100,4,11,4,'francisco','b5d5f67b30809413156655abdda382a3','Francisco Azevedo','','','en',1,'2001-01-01 00:00:00','2004-05-08 05:05:06',19,'2006-08-08 16:26:39','2006-08-08 17:18:19',2,'0000-00-00 00:00:00','YDCMUseradministrator',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (102,101,5,6,5,'pieter','','Pieter','','aasdasdasd','en',1,'2001-01-01 00:00:00','2001-01-01 00:00:00',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',100,'0000-00-00 00:00:00','YDCMUseradministrator',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (103,101,7,8,5,'david','','David','','','en',1,'2006-08-08 16:27:35','2006-08-08 16:27:35',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',101,'2006-08-08 16:28:01','YDCMUseradministrator',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (104,100,12,17,4,'pedro','','Pedro Cerqueira','','xpto','en',1,'2006-08-08 16:29:51','2006-08-08 16:29:51',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',100,'2006-08-08 16:30:06','YDCMUseradministrator',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (105,104,13,14,5,'limpeza','','Limpeza','','asfdasdasdasd','en',1,'2006-08-08 16:32:11','2006-08-08 16:32:11',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',100,'2006-08-08 16:32:26','YDCMUseradministrator',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (108,104,15,16,5,'meireles','','Meireles','','meireles','en',1,'2006-08-08 17:14:44','2006-08-08 17:14:44',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',100,'2006-08-08 17:15:09','YDCMUseradministrator',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (111,101,9,10,5,'marc','','Marc','','Vassoutinha','en',1,'2006-08-09 15:57:27','2006-12-09 15:57:27',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',100,'2006-08-09 15:57:49','YDCMUseradministrator',1,'default');
INSERT INTO `ydcmusers` (`user_id`,`parent_id`,`nleft`,`nright`,`nlevel`,`username`,`password`,`name`,`email`,`other`,`language_id`,`state`,`login_start`,`login_end`,`login_counter`,`login_last`,`login_current`,`created_user`,`created_date`,`type`,`position`,`template`) VALUES (113,100,18,19,4,'extra','','Extra user','','user OKuser OKuser OK','en',1,'2006-08-09 16:13:57','2006-08-09 16:13:57',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',100,'2006-08-09 16:14:20','YDCMUseradministrator',1,'default');


CREATE TABLE YDCMPermissions (
  permission_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INTEGER(10) NOT NULL,
  object VARCHAR(255) NOT NULL,
  object_action VARCHAR(255) NOT NULL,
  PRIMARY KEY(permission_id),
  FOREIGN KEY(user_id)
    REFERENCES YDCMUsers(user_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

INSERT INTO `ydcmpermissions` (`permission_id`,`object`,`object_action`,`user_id`) VALUES (117,'YDCMRootmenu','delete',100);
INSERT INTO `ydcmpermissions` (`permission_id`,`object`,`object_action`,`user_id`) VALUES (118,'YDCMPage','delete',100);
INSERT INTO `ydcmpermissions` (`permission_id`,`object`,`object_action`,`user_id`) VALUES (119,'YDCMPage','create',100);
INSERT INTO `ydcmpermissions` (`permission_id`,`object`,`object_action`,`user_id`) VALUES (120,'YDCMPage','delete',101);
INSERT INTO `ydcmpermissions` (`permission_id`,`object`,`object_action`,`user_id`) VALUES (121,'YDCMPage','create',101);


CREATE TABLE YDCMAudit (
  YDCMAudit_id INTEGER NOT NULL AUTO_INCREMENT,
  permission_id INTEGER UNSIGNED NOT NULL,
  permission_result VARCHAR(255) NULL,
  date DATE NOT NULL,
  date_time DATETIME NOT NULL,
  browser VARCHAR(20) NULL,
  platform VARCHAR(20) NULL,
  url VARCHAR(255) NULL,
  hits INTEGER UNSIGNED NULL,
  PRIMARY KEY(YDCMAudit_id),
  FOREIGN KEY(permission_id)
    REFERENCES YDCMPermissions(permission_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE YDCMLocks (
  lock_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  content_id INTEGER(10) NOT NULL,
  user_id INTEGER(10) NOT NULL,
  datetime  DATETIME NOT NULL,
  PRIMARY KEY(lock_id),
  FOREIGN KEY(user_id)
    REFERENCES YDCMUsers(user_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(content_id)
    REFERENCES YDCMTree(content_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE YDCMComp (
  component_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INTEGER(10) NOT NULL,
  content_id INTEGER(10) NOT NULL,
  language_id VARCHAR(255) NULL,
  title VARCHAR(255) NULL,
  version_default TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  version_date DATETIME NOT NULL,
  PRIMARY KEY(component_id),
  FOREIGN KEY(content_id)
    REFERENCES YDCMTree(content_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(language_id)
    REFERENCES YDCMLanguages(language_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(user_id)
    REFERENCES YDCMUsers(user_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (1,5,'en','Apache friends',1,'0000-00-00 00:00:00',100);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (2,6,'en','PostGre en',1,'0000-00-00 00:00:00',100);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (3,6,'pt','PostGre pt',1,'0000-00-00 00:00:00',101);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (4,7,'en','mySql howto',1,'2000-01-01 00:00:00',101);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (5,7,'pt','ajuda mySql',1,'0000-00-00 00:00:00',100);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (6,9,'en','Databases we use',1,'0000-00-00 00:00:00',100);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (7,9,'pt','Bases de dados',1,'0000-00-00 00:00:00',100);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (8,10,'en','Generators help',1,'2000-01-01 00:00:00',101);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (9,10,'pt','Ajuda sobre geradores',1,'2002-02-02 00:00:00',101);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (10,11,'en','Portals',1,'0000-00-00 00:00:00',101);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (11,11,'pt','Portais',1,'0000-00-00 00:00:00',101);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (12,8,'en','Menu Links',1,'0000-00-00 00:00:00',101);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (13,8,'pt','O meu menu de atalhos',1,'0000-00-00 00:00:00',100);
INSERT INTO `ydcmcomp` (`component_id`,`content_id`,`language_id`,`title`,`version_default`,`version_date`,`user_id`) VALUES (14,10,'en','Gener',0,'3002-02-02 00:00:00',100);


CREATE TABLE YDCMPage (
  component_id INTEGER UNSIGNED NOT NULL,
  current_version TINYINT(1) NULL,
  html TEXT NULL,
  xhtml TEXT NULL,
  template_pack TINYINT(1) UNSIGNED NOT NULL,
  template VARCHAR(255) NULL,
  metatags TINYINT(1) NOT NULL,
  description TEXT NULL,
  keywords TEXT NULL DEFAULT '',
  FOREIGN KEY(component_id)
    REFERENCES YDCMComp(component_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (1,1,'Apache html all lang','Apache xhtml all lang',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (2,1,'PostgreSQL en html','PostgreSQL en xhtml',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (3,1,'PostgreSQL pt html','PostgreSQL pt xhtml',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (4,1,'mySQL en html','mySQL en xhtml',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (5,1,'mySQL pt html','mySQL pt xhtml',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (6,1,'Databases html','Databases xhtml',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (7,1,'Bases de dados html','Bases de dados xhtml',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (8,1,'Generators html','Generators xhtml',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (9,1,'Geradores html','Geradores xhtml',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (10,1,'Portals html','Portals xhtml',0,'',0,'','');
INSERT INTO `ydcmpage` (`component_id`,`current_version`,`html`,`xhtml`,`template_pack`,`template`,`metatags`,`description`,`keywords`) VALUES (11,1,'Portais html','Portais xhtml',0,'',0,'','');


CREATE TABLE YDCMGuestbook_posts (
  post_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  component_id INTEGER UNSIGNED NOT NULL,
  date DATETIME NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NULL,
  message TEXT NOT NULL,
  active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  website VARCHAR(255) NULL,
  ip VARCHAR(15) NULL,
  PRIMARY KEY(post_id),
  FOREIGN KEY(component_id)
    REFERENCES YDCMComp(component_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE YDCMLink (
  component_id INTEGER UNSIGNED NOT NULL,
  url VARCHAR(255) NOT NULL,
  num_visits INTEGER UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY(component_id)
    REFERENCES YDCMComp(component_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE YDCMHelpdesk_posts (
  post_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  component_id INTEGER UNSIGNED NOT NULL,
  state_id VARCHAR(255) NULL,
  urgency_id VARCHAR(255) NULL,
  user_id INTEGER(10) NOT NULL,
  subject TEXT NULL,
  text TEXT NULL,
  creation_date DATETIME NOT NULL,
  reportedby_user TINYINT(1) UNSIGNED NULL,
  reportedby_local VARCHAR(255) NULL,
  reportedby_date DATETIME NOT NULL,
  reportedby_type VARCHAR(255) NULL,
  assignedto_user TINYINT(1) UNSIGNED NULL,
  assignedto_local VARCHAR(255) NULL,
  assignedto_date DATETIME NOT NULL,
  assignedto_type VARCHAR(255) NULL,
  PRIMARY KEY(post_id),
  FOREIGN KEY(user_id)
    REFERENCES YDCMUsers(user_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(component_id)
    REFERENCES YDCMComp(component_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

INSERT INTO `ydcmhelpdesk_posts` (`post_id`,`component_id`,`state_id`,`urgency_id`,`user_id`,`subject`,`text`,`creation_date`,`reportedby_user`,`reportedby_local`,`reportedby_date`,`reportedby_type`,`assignedto_user`,`assignedto_local`,`assignedto_date`,`assignedto_type`) VALUES (1,1,'1','1',1,'','','2006-07-05 15:50:33',NULL,NULL,'0000-00-00 00:00:00',NULL,0,'','2006-07-05 15:50:33','0');
INSERT INTO `ydcmhelpdesk_posts` (`post_id`,`component_id`,`state_id`,`urgency_id`,`user_id`,`subject`,`text`,`creation_date`,`reportedby_user`,`reportedby_local`,`reportedby_date`,`reportedby_type`,`assignedto_user`,`assignedto_local`,`assignedto_date`,`assignedto_type`) VALUES (2,1,'1','1',1,'sub','text','2006-07-05 15:50:33',NULL,NULL,'0000-00-00 00:00:00',NULL,0,'ass local','2006-07-05 15:50:33','0');
INSERT INTO `ydcmhelpdesk_posts` (`post_id`,`component_id`,`state_id`,`urgency_id`,`user_id`,`subject`,`text`,`creation_date`,`reportedby_user`,`reportedby_local`,`reportedby_date`,`reportedby_type`,`assignedto_user`,`assignedto_local`,`assignedto_date`,`assignedto_type`) VALUES (3,1,'1','1',1,'sub','text','2006-07-05 15:50:33',0,'rep local','2006-07-05 15:50:33','0',0,'ass local','2006-07-05 15:50:33','0');
INSERT INTO `ydcmhelpdesk_posts` (`post_id`,`component_id`,`state_id`,`urgency_id`,`user_id`,`subject`,`text`,`creation_date`,`reportedby_user`,`reportedby_local`,`reportedby_date`,`reportedby_type`,`assignedto_user`,`assignedto_local`,`assignedto_date`,`assignedto_type`) VALUES (4,1,'1','1',1,'sub','text','2006-07-05 15:50:33',0,'rep local','2006-07-05 15:50:33','0',0,'ass local','2006-07-05 15:50:33','0');


CREATE TABLE YDCMHelpdesk_response (
  response_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INTEGER(10) NOT NULL,
  post_id INTEGER UNSIGNED NOT NULL,
  date DATETIME NULL,
  description TEXT NULL,
  PRIMARY KEY(response_id),
  FOREIGN KEY(post_id)
    REFERENCES YDCMHelpdesk_posts(post_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(user_id)
    REFERENCES YDCMUsers(user_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);


