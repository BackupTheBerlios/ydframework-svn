

INSERT INTO `ydcmconfiguration_files` VALUES ('audio','mp3, wav, ogg, mid');
INSERT INTO `ydcmconfiguration_files` VALUES ('compress','zip, rar, gz, tgz');
INSERT INTO `ydcmconfiguration_files` VALUES ('image','jpeg, gif, png, ico, psd, bmp');
INSERT INTO `ydcmconfiguration_files` VALUES ('maxfilesize_mb','5');
INSERT INTO `ydcmconfiguration_files` VALUES ('other','swf');
INSERT INTO `ydcmconfiguration_files` VALUES ('text','htm, html, xml, js, css, doc, pdf, xls, ppt, txt');
INSERT INTO `ydcmconfiguration_files` VALUES ('video','mpg, mpeg, avi, wmv');





INSERT INTO `ydcmlanguages` VALUES ('all','All',1,0);
INSERT INTO `ydcmlanguages` VALUES ('en','English',1,1);
INSERT INTO `ydcmlanguages` VALUES ('pt','Português',1,0);




INSERT INTO `ydcmtree` VALUES (1,0,1,22,1,1,'YDCMRoot','',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (2,1,2,3,2,1,'YDCMRootmenu','menu Code Paste',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (3,1,4,5,2,1,'YDCMRootmenu','menu Documentation',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (4,1,6,13,2,1,'YDCMRootmenu','menu Books',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (5,4,7,8,3,1,'YDCMPage','Apache',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (6,4,9,10,3,1,'YDCMPage','PostegreSQL',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (7,4,11,12,3,1,'YDCMPage','mysql',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (8,1,14,21,2,1,'YDCMRootmenu','links',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (9,8,15,16,3,1,'YDCMPage','databases',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (10,8,17,18,3,1,'YDCMPage','generators',1,1,1,0,0);
INSERT INTO `ydcmtree` VALUES (11,8,19,20,3,1,'YDCMPage','portals',1,1,1,0,0);


INSERT INTO `ydcmpage` VALUES (1,  5, 'en', 1, 'Apache in english', '', '', 0, '', 0, '', '');
INSERT INTO `ydcmpage` VALUES (2,  6, 'all', 1, 'PostgreSQL in all lang', '', '', 0, '', 0, '', '');
INSERT INTO `ydcmpage` VALUES (3,  7, 'pt', 1, 'mySQL in PT', '', '', 0, '', 0, '', '');
INSERT INTO `ydcmpage` VALUES (4,  9, 'en', 1, 'Our databases', '', '', 0, '', 0, '', '');
INSERT INTO `ydcmpage` VALUES (5, 10, 'en', 1, 'Generators', '', '', 0, '', 0, '', '');
INSERT INTO `ydcmpage` VALUES (6, 11, 'all', 1, 'Portals for all', '', '', 0, '', 0, '', '');

INSERT INTO `ydcmrootmenu` VALUES (1, 2, 'all', 'my menu paste');
INSERT INTO `ydcmrootmenu` VALUES (2, 3, 'en',  'documentation in EN');
INSERT INTO `ydcmrootmenu` VALUES (3, 4, 'pt',  'PT books menu');
INSERT INTO `ydcmrootmenu` VALUES (4, 8, 'all', 'links menu');


INSERT INTO `ydcmusers` VALUES (2,0,0,0,0,'admin','21232f297a57a5a743894a0e4a801fc3','name','email@test.com','a','en',1,NULL,NULL,7,'2006-04-23 17:02:27','2006-04-23 17:02:34',0,'0000-00-00 00:00:00');



