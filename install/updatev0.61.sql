UPDATE tablecolumns SET `column`="if(notes.starttime,concat(notes.startdate,\" \",notes.starttime),notes.startdate)" WHERE id=116;
UPDATE tablecolumns SET `column`="if(notes.endtime,concat(notes.enddate,\" \",notes.endtime),notes.enddate)" WHERE id=117;
UPDATE tablecolumns SET `column`="notes.repeat+if(notes.parentid is null, 0,1)" WHERE id=124;
UPDATE tablecolumns SET `column`="notes.repeat",format="boolean" WHERE id=129;

ALTER TABLE tabledefs CHANGE defaultwhereclause defaultwhereclause varchar(255);
ALTER TABLE tabledefs CHANGE `id` `id` int(11) NOT NULL auto_increment default '1000';

ALTER TABLE tablecolumns CHANGE `format` `format` enum('date','time','currency','boolean','datetime','filelink') default NULL,

UPDATE tabledefs SET defaultwhereclause="notes.type='EV' AND notes.createdby = {{$_SESSION['userinfo']['id']}} AND ((notes.startdate = year(notes.startdate)=year(curdate()) and week(notes.startdate)=week(curdate())) OR notes.`repeat`=1)" WHERE id=24;

UPDATE tablefindoptions SET search ="" WHERE id=
UPDATE tablefindoptions SET search ="notes.type='EV' AND notes.createdby = {{$_SESSION['userinfo']['id']}} AND ((notes.startdate = curdate()) OR notes.`repeat`=1)" WHERE id=88
UPDATE tablefindoptions SET search ="notes.type='EV' AND notes.createdby = {{$_SESSION['userinfo']['id']}} AND ((notes.startdate = date_sub(curdate(),INTERVAL 1 DAY)) OR notes.`repeat`=1)" WHERE id=89

CREATE TABLE `settings` (`id` int(11) NOT NULL auto_increment, `name` varchar(64) NOT NULL default '',`value` varchar(255) default '', PRIMARY KEY  (`id`)) TYPE=MyISAM; 

CREATE TABLE `files` (`id` int(11) NOT NULL auto_increment,`name` varchar(128) NOT NULL default '',`description` text,`file` longblob,`type` varchar(100) default '',`createdby` int(11) default '0',`creationdate` datetime default '0000-00-00 00:00:00',`modifiedby` int(11) default '0',`modifieddate` timestamp(14) NOT NULL,`accesslevel` int(11) NOT NULL default '0',PRIMARY KEY  (`id`)) TYPE=MyISAM; 

CREATE TABLE `attachments` (`id` int(11) NOT NULL auto_increment,`fileid` int(11) NOT NULL default '0',`tabledefid` int(11) NOT NULL default '0',`recordid` int(11) NOT NULL default '0',`createdby` int(11) default '0',`creationdate` datetime default '0000-00-00 00:00:00',`modifiedby` int(11) default '0',`modifieddate` timestamp(14) NOT NULL,PRIMARY KEY  (`id`),KEY `therecord` (`recordid`),KEY `thetable` (`tabledefid`),KEY `thefile` (`fileid`)) TYPE=MyISAM;

INSERT INTO tabledefs VALUES ('modules/base/files_addedit.php','Files',26,'files',2,'2005-11-17 21:44:07',2,20051117214436,'files','modules/base/files_addedit.php','delete','files.id=-1','files.name','search','','','table',1);
INSERT INTO tabledefs VALUES ('modules/base/files_addedit.php','Attachments',27,'attachments',2,'2005-11-22 11:38:15',2,20051122125549,'(attachments INNER JOIN files on attachments.fileid=files.id)','modules/base/files_addedit.php','delete','attachments.id!=0','attachments.creationdate DESC','search','','','table',1);
INSERT INTO tablefindoptions VALUES (108,26,'All Records','files.id!=0',0,-10);
INSERT INTO tableoptions VALUES (83,26,'new','1',0,-10);
INSERT INTO tableoptions VALUES (84,26,'select','1',0,-10);
INSERT INTO tableoptions VALUES (85,26,'edit','1',0,-10);
INSERT INTO tableoptions VALUES (86,27,'new','1',0,-10);
INSERT INTO tableoptions VALUES (87,27,'select','1',0,-10);
INSERT INTO tableoptions VALUES (88,27,'edit','1',0,-10);
INSERT INTO tablesearchablefields VALUES (96,26,'files.name','name',0,'field');
INSERT INTO tablecolumns VALUES (141,26,'id','files.id','left','',0,'',0,'',NULL);
INSERT INTO tablecolumns VALUES (142,26,'description','files.description','left','',2,'',1,'99%',NULL);
INSERT INTO tablecolumns VALUES (144,26,'file','files.name','left','',1,'',0,'',NULL);
INSERT INTO tablecolumns VALUES (151,26,'download','files.id','center','',3,'',0,'','filelink');
INSERT INTO tablecolumns VALUES (148,27,'attached','attachments.creationdate','left','',2,'',0,'','datetime');
INSERT INTO tablecolumns VALUES (147,27,'file','concat(\"<b>\",files.name,\"</b>\")','left','',0,'files.name',0,'',NULL);
INSERT INTO tablecolumns VALUES (150,27,'download','files.id','center','',3,'',0,'','filelink');
INSERT INTO tablecolumns VALUES (649,27,'description','files.description','left','',1,'',1,'100%',NULL);