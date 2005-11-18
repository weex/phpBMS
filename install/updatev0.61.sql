UPDATE tablecolumns SET column="if(notes.starttime,concat(notes.startdate,\" \",notes.starttime),notes.startdate)" WHERE id=116;
UPDATE tablecolumns SET column="if(notes.endtime,concat(notes.enddate,\" \",notes.endtime),notes.enddate)" WHERE id=117;
UPDATE tablecolumns SET column="notes`.repeat`+if(notes.parentid is null, 0,1)" WHERE id=124;
UPDATE tablecolumns SET column="notes.`repeat`",format="boolean" WHERE id=129;

ALTER TABLE tabledefs CHANGE defaultwhereclause defaultwhereclause varchar(255);

UPDATE tabledefs SET defaultwhereclause="notes.type='EV' AND notes.createdby = {{$_SESSION['userinfo']['id']}} AND ((notes.startdate = year(notes.startdate)=year(curdate()) and week(notes.startdate)=week(curdate())) OR notes.`repeat`=1)" WHERE id=24;

UPDATE tablefindoptions SET search ="" WHERE id=
UPDATE tablefindoptions SET search ="notes.type='EV' AND notes.createdby = {{$_SESSION['userinfo']['id']}} AND ((notes.startdate = curdate()) OR notes.`repeat`=1)" WHERE id=88
UPDATE tablefindoptions SET search ="notes.type='EV' AND notes.createdby = {{$_SESSION['userinfo']['id']}} AND ((notes.startdate = date_sub(curdate(),INTERVAL 1 DAY)) OR notes.`repeat`=1)" WHERE id=89

CREATE TABLE `settings` (`id` int(11) NOT NULL auto_increment, `name` varchar(64) NOT NULL default '',`value` varchar(255) default '', PRIMARY KEY  (`id`)) TYPE=MyISAM; 

CREATE TABLE `files` (`id` int(11) NOT NULL auto_increment,`name` varchar(128) NOT NULL default '',`accesslevel` int(11) NOT NULL default '0',`servename` varchar(64) NOT NULL default '',`file` longblob,`type` varchar(100) default '',`createdby` int(11) default '0',`creationdate` datetime default '0000-00-00 00:00:00',`modifiedby` int(11) default '0',`modifieddate` timestamp(14) NOT NULL,PRIMARY KEY  (`id`)) TYPE=MyISAM; 

CREATE TABLE `filetorecord` (`id` int(11) NOT NULL auto_increment,`fileid` int(11) NOT NULL default '0',`tabledefid` int(11) NOT NULL default '0',`recordid` int(11) NOT NULL default '0',`createdby` int(11) default '0',`creationdate` datetime default '0000-00-00 00:00:00',`modifiedby` int(11) default '0',`modifieddate` timestamp(14) NOT NULL,PRIMARY KEY  (`id`),KEY `therecord` (`recordid`),KEY `thetable` (`tabledefid`),KEY `thefile` (`fileid`)) TYPE=MyISAM;