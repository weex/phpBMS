CREATE TABLE choices (
  id int(11) NOT NULL auto_increment,
  listname varchar(64) NOT NULL default '',
  thevalue varchar(64) default NULL,
  UNIQUE KEY theid (id)
) TYPE=MyISAM PACK_KEYS=0;

CREATE TABLE menu (
  id int(11) NOT NULL auto_increment,
  name varchar(64) NOT NULL default '',
  link varchar(128) NOT NULL default '',
  parentid int(11) NOT NULL default '0',
  displayorder int(11) NOT NULL default '0',
  createdby int(11) NOT NULL default '0',
  modifiedby int(11) default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifieddate timestamp(14) NOT NULL,
  roleid int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE modules (
  id int(11) NOT NULL auto_increment,
  displayname varchar(128) NOT NULL default '',
  name varchar(64) NOT NULL default '',
  description text,
  version varchar(32) default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE notes (
  assignedtoid int(11) default NULL,
  attachedid int(11) default NULL,
  attachedtabledefid int(11) default NULL,
  content text,
  assignedtodate date default NULL,
  id int(11) NOT NULL auto_increment,
  subject varchar(128) default NULL,
  type char(2) default NULL,
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifiedby int(11) default NULL,
  modifieddate timestamp(14) NOT NULL,
  importance int(11) NOT NULL default '0',
  parentid int(11) default NULL,
  startdate date default NULL,
  enddate date default NULL,
  completed tinyint(4) NOT NULL default '0',
  private tinyint(4) NOT NULL default '0',
  status varchar(64) default NULL,
  completeddate date default NULL,
  location varchar(128) default NULL,
  category varchar(128) default NULL,
  repeattype varchar(20) default NULL,
  `repeat` tinyint(4) NOT NULL default '0',
  repeatuntildate date default NULL,
  repeatfrequency smallint(6) default NULL,
  repeatdays varchar(7) default NULL,
  assignedtotime time default NULL,
  starttime time default NULL,
  endtime time default NULL,
  repeattimes int(11) NOT NULL default '-1',
  assignedbyid int(11) NOT NULL default '0',
  UNIQUE KEY theid (id)
) TYPE=MyISAM PACK_KEYS=0;

CREATE TABLE relationships (
  id int(11) NOT NULL auto_increment,
  tofield varchar(32) NOT NULL default '',
  name varchar(128) NOT NULL default '',
  fromfield varchar(32) NOT NULL default '',
  fromtableid int(11) NOT NULL default '0',
  totableid int(11) NOT NULL default '0',
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifiedby int(11) default '0',
  modifieddate timestamp(14) NOT NULL,
  inherint tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY fromtable (fromtableid)
) TYPE=MyISAM PACK_KEYS=0;

CREATE TABLE `reports` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) default NULL,
  `type` varchar(32) default NULL,
  `tabledefid` int(11) NOT NULL default '0',
  `displayorder` int(11) NOT NULL default '0',
  `roleid` int(11) NOT NULL default '0',
  `reportfile` varchar(128) default NULL,
  `description` text,
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `tablecolumns` (
  `id` int(11) NOT NULL auto_increment,
  `tabledefid` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `column` varchar(255) NOT NULL default '',
  `align` varchar(16) NOT NULL default '',
  `footerquery` varchar(255) default '',
  `displayorder` int(11) NOT NULL default '0',
  `sortorder` varchar(128) default '',
  `wrap` tinyint(1) NOT NULL default '0',
  `size` varchar(16) NOT NULL default '',
  `format` enum('date','time','currency','boolean','datetime','filelink','noencoding') default NULL,
  PRIMARY KEY  (`id`),
  KEY `tabledef` (`tabledefid`),
  KEY `displayorder` (`displayorder`)
) TYPE=MyISAM AUTO_INCREMENT=5000;

CREATE TABLE `tabledefs` (
  `id` int(11) NOT NULL,
  `displayname` varchar(64) default NULL,
  `type` varchar(16) NOT NULL default 'table',
  `moduleid` int(11) NOT NULL default '0',
  `maintable` varchar(64) NOT NULL default '',
  `querytable` varchar(255) NOT NULL default '',
  `editfile` varchar(128) default NULL,
  `editroleid` int(11) NOT NULL default '0',
  `addfile` varchar(100) default '',
  `addroleid` int(11) NOT NULL default '0',
  `searchroleid` int(11) NOT NULL default '0',
  `advsearchroleid` int(11) NOT NULL default '-100',
  `viewsqlroleid` int(11) NOT NULL default '-100',
  `deletebutton` varchar(32) default '',
  `defaultwhereclause` varchar(255) default NULL,
  `defaultsortorder` varchar(255) default '',
  `defaultsearchtype` varchar(64) default '',
  `defaultcriteriafindoptions` varchar(128) default '',
  `defaultcriteriaselection` varchar(128) default '',
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000; 

CREATE TABLE tablefindoptions (
  id int(11) NOT NULL auto_increment,
  tabledefid int(11) NOT NULL default '0',
  name varchar(64) NOT NULL default '',
  search varchar(255) NOT NULL default '',
  displayorder int(11) NOT NULL default '0',
  roleid int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY tabledef (tabledefid)
) TYPE=MyISAM AUTO_INCREMENT=2000; 

CREATE TABLE tableoptions (
  id int(11) NOT NULL auto_increment,
  tabledefid int(11) NOT NULL default '0',
  name varchar(64) NOT NULL default '',
  `option` varchar(128) NOT NULL default '',
  othercommand tinyint(1) NOT NULL default '0',
  roleid int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY tabledef (tabledefid)
) TYPE=MyISAM AUTO_INCREMENT=2000; 

CREATE TABLE tablesearchablefields (
  id int(11) NOT NULL auto_increment,
  tabledefid int(11) NOT NULL default '0',
  field varchar(255) NOT NULL default '',
  name varchar(64) NOT NULL default '',
  displayorder int(11) NOT NULL default '0',
  type varchar(16) NOT NULL default 'field',
  PRIMARY KEY  (id)
) TYPE=MyISAM AUTO_INCREMENT=2000;

CREATE TABLE users (
  id int(11) NOT NULL auto_increment,
  login varchar(64) NOT NULL default '',
  password blob,
  firstname varchar(64) default NULL,
  lastname varchar(64) default NULL,
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  revoked tinyint(1) NOT NULL default '0',
  createdby int(11) NOT NULL default '0',
  modifiedby int(11) default '0',
  lastlogin datetime default NULL,
  modifieddate timestamp(14) NOT NULL,
  email varchar(128) default '',
  phone varchar(32) default '',
  department varchar(128) default '',
  employeenumber varchar(64) default '',
  admin tinyint(4) NOT NULL default '0',
  portalaccess tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY theid (id)
) TYPE=MyISAM PACK_KEYS=0;

CREATE TABLE usersearches (
  id int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  tabledefid int(11) NOT NULL default '0',
  name varchar(128) default '',
  sqlclause text,
  type char(3) NOT NULL default 'SCH',
  roleid int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY tabledefid (tabledefid),
  KEY thetype (type),
  KEY user (userid)
) TYPE=MyISAM;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `value` varchar(255) default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1000;

CREATE TABLE `files` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `description` text,
  `file` longblob,
  `type` varchar(100) default '',
  `createdby` int(11) default '0',
  `creationdate` datetime default '0000-00-00 00:00:00',
  `modifiedby` int(11) default '0',
  `modifieddate` timestamp(14) NOT NULL,
  `roleid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=100; 

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL auto_increment,
  `fileid` int(11) NOT NULL default '0',
  `tabledefid` int(11) NOT NULL default '0',
  `recordid` int(11) NOT NULL default '0',
  `createdby` int(11) default '0',
  `creationdate` datetime default '0000-00-00 00:00:00',
  `modifiedby` int(11) default '0',
  `modifieddate` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `therecord` (`recordid`),
  KEY `thetable` (`tabledefid`),
  KEY `thefile` (`fileid`)
) TYPE=MyISAM; 

CREATE TABLE `roles` (
  `id` INTEGER UNSIGNED DEFAULT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `description` TEXT,
  `inactive` tinyint(4) NOT NULL,
  `createdby` INTEGER UNSIGNED,
  `creationdate` DATETIME,
  `modifiedby` INTEGER UNSIGNED,
  `modifieddate` TIMESTAMP,
  PRIMARY KEY(`id`)
) TYPE = MyISAM;

CREATE TABLE `rolestousers` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INTEGER UNSIGNED NOT NULL,
  `roleid` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(`id`)
) TYPE = MYISAM;