CREATE TABLE `tablegroupings` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `tabledefid` VARCHAR(64) NOT NULL,
  `field` TEXT NOT NULL,
  `displayorder` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `ascending` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `name` VARCHAR(64),
  `roleid` VARCHAR(64),
  PRIMARY KEY(`id`)
) ENGINE=INNODB;

CREATE TABLE `log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(25),
  `userid` VARCHAR(64),
  `ip` VARCHAR(45),
  `value` TEXT,
  `stamp` TIMESTAMP,
  PRIMARY KEY(`id`)
) ENGINE=INNODB;

CREATE TABLE choices (
  id int(11) NOT NULL auto_increment,
  listname varchar(64) NOT NULL default '',
  thevalue varchar(64) default NULL,
  UNIQUE KEY theid (id)
) ENGINE=INNODB  PACK_KEYS=0;

CREATE TABLE menu (
  id int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  name varchar(64) NOT NULL default '',
  link varchar(128) NOT NULL default '',
  parentid varchar(64) default '',
  displayorder int(11) NOT NULL default '0',
  createdby int(11) NOT NULL default '0',
  modifiedby int(11) default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifieddate timestamp(14) NOT NULL,
  `roleid` VARCHAR(64),
  PRIMARY KEY  (id),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB ;

CREATE TABLE modules (
  id int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  displayname varchar(128) NOT NULL default '',
  name varchar(64) NOT NULL default '',
  description text,
  version varchar(32) default '',
  PRIMARY KEY  (id),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB ;

CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `assignedtoid` varchar(64) default NULL,
  `attachedid` varchar(64) default NULL,
  `attachedtabledefid` varchar(64) default NULL,
  `content` text,
  `assignedtodate` date default NULL,
  `subject` varchar(128) default NULL,
  `type` char(2) NOT NULL default 'NT',
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp NOT NULL,
  `importance` int(11) NOT NULL default '0',
  `parentid` varchar(64) default NULL,
  `startdate` date default NULL,
  `enddate` date default NULL,
  `completed` tinyint(4) NOT NULL default '0',
  `private` tinyint(4) NOT NULL default '0',
  `status` varchar(64) default NULL,
  `completeddate` date default NULL,
  `location` varchar(128) default NULL,
  `category` varchar(128) default NULL,
  `assignedtotime` time default NULL,
  `starttime` time default NULL,
  `endtime` time default NULL,
  `assignedbyid` varchar(64) NOT NULL default '0',
  `repeating` smallint(5) unsigned NOT NULL default '0',
  `repeattype` enum('Daily','Weekly','Monthly','Yearly') default NULL,
  `repeatuntil` date default NULL,
  `repeatevery` int(10) unsigned NOT NULL default '1',
  `repeattimes` int(10) unsigned default NULL,
  `repeateachlist` varchar(128) default NULL,
  `repeatontheday` int(10) unsigned default NULL,
  `repeatontheweek` int(10) unsigned default NULL,
  `firstrepeat` date default NULL,
  `lastrepeat` date default NULL,
  `timesrepeated` int(10) unsigned NOT NULL default '0',
  `repeatname` varchar(255) default NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY (`id`),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB;

CREATE TABLE relationships (
  id int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  tofield varchar(32) NOT NULL default '',
  name varchar(128) NOT NULL default '',
  fromfield varchar(32) NOT NULL default '',
  fromtableid VARCHAR(64) NOT NULL default '',
  totableid VARCHAR(64) NOT NULL default '0',
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifiedby int(11) default '0',
  modifieddate timestamp(14) NOT NULL,
  inherint tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY (`uuid`),
  KEY(`fromtableid`)
) ENGINE=INNODB;

CREATE TABLE `reports` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) default NULL,
  `type` varchar(32) default NULL,
  `tabledefid` varchar(64) NOT NULL,
  `displayorder` int(11) NOT NULL default '0',
  `roleid` VARCHAR(64),
  `reportfile` varchar(128) NOT NULL,
  `description` text,
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp,
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB;

CREATE TABLE `tablecolumns` (
  `id` int(11) NOT NULL auto_increment,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL default '',
  `column` text,
  `align` varchar(16) NOT NULL default '',
  `footerquery` varchar(255) default '',
  `displayorder` int(11) NOT NULL default '0',
  `sortorder` varchar(128) default '',
  `wrap` tinyint(1) NOT NULL default '0',
  `size` varchar(16) NOT NULL default '',
  `format` enum('date','time','currency','boolean','datetime','filelink','noencoding','bbcode') default NULL,
  `roleid` VARCHAR(64),
  PRIMARY KEY  (`id`),
  KEY `tabledef` (`tabledefid`),
  KEY `displayorder` (`displayorder`)
) ENGINE=INNODB;

CREATE TABLE `tabledefs` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  `displayname` varchar(64) default NULL,
  `prefix` varchar(4) default NULL,
  `type` varchar(16) NOT NULL default 'table',
  `moduleid` varchar(64) NOT NULL,
  `maintable` varchar(64) NOT NULL default '',
  `querytable` text,
  `editfile` varchar(128) default NULL,
  `editroleid` varchar(64),
  `addfile` varchar(100) default '',
  `addroleid` varchar(64),
  `importfile` VARCHAR(128) DEFAULT NULL,
  `importroleid` VARCHAR(64) default 'Admin',
  `searchroleid` varchar(64),
  `advsearchroleid` varchar(64) default 'Admin',
  `viewsqlroleid` varchar(64) default 'Admin',
  `deletebutton` varchar(32) default '',
  `canpost` tinyint(4) NOT NULL default '0',
  `apiaccessible` tinyint(4) NOT NULL default '0',
  `hascustomfields` tinyint(4) NOT NULL default '0',
  `defaultwhereclause` text,
  `defaultsortorder` text,
  `defaultsearchtype` varchar(64) default '',
  `defaultcriteriafindoptions` varchar(128) default '',
  `defaultcriteriaselection` varchar(128) default '',
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB;

CREATE TABLE tablefindoptions (
  id int(11) NOT NULL auto_increment,
  tabledefid varchar(64) NOT NULL,
  name varchar(64) NOT NULL default '',
  search text NOT NULL,
  displayorder int(11) NOT NULL default '0',
  roleid varchar(64),
  PRIMARY KEY  (id),
  KEY tabledef (tabledefid)
) ENGINE=INNODB;

CREATE TABLE tableoptions (
  id int(11) NOT NULL auto_increment,
  tabledefid varchar(64) NOT NULL,
  name varchar(64) NOT NULL default '',
  `option` varchar(128) NOT NULL default '',
  `needselect` BOOLEAN NOT NULL DEFAULT 1,
  othercommand tinyint(1) NOT NULL default '0',
  roleid varchar(64),
  `displayorder` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY  (id),
  KEY tabledef (tabledefid)
) ENGINE=INNODB;

CREATE TABLE tablesearchablefields (
  id int(11) NOT NULL auto_increment,
  tabledefid VARCHAR(64) NOT NULL,
  field text NOT NULL,
  name varchar(64) NOT NULL default '',
  displayorder int(11) NOT NULL default '0',
  type varchar(16) NOT NULL default 'field',
  PRIMARY KEY  (id)
) ENGINE=INNODB ;

CREATE TABLE tablecustomfields (
  `id` int(11) NOT NULL auto_increment,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL default '',
  `field` varchar(8) NOT NULL default '',
  `format` varchar(32),
  `generator` TEXT,
  `required` TINYINT(4) NOT NULL default 0,
  `displayorder` int(11) NOT NULL default 0,
  `roleid` VARCHAR(64) default '',
  PRIMARY KEY  (`id`),
  KEY `tabledef` (`tabledefid`)
) ENGINE=INNODB;

CREATE TABLE users (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  login varchar(64) NOT NULL default '',
  password blob,
  firstname varchar(64) NOT NULL default '',
  lastname varchar(64) NOT NULL default '',
  `lastip` VARCHAR(45) NOT NULL DEFAULT '',
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
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY (`id`),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB;

CREATE TABLE usersearches (
  id int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  userid varchar(64) NOT NULL,
  tabledefid varchar(64) NOT NULL,
  name varchar(128) default '',
  sqlclause text,
  type char(3) NOT NULL default 'SCH',
  roleid varchar(64),
  PRIMARY KEY  (id),
  KEY tabledefid (tabledefid),
  KEY thetype (type),
  KEY user (userid)
) ENGINE=INNODB;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `value` varchar(255) default '',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB  AUTO_INCREMENT=1000;

CREATE TABLE `files` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL default '',
  `description` text,
  `file` longblob,
  `type` varchar(100) default '',
  `createdby` int(11) default '0',
  `creationdate` datetime default '0000-00-00 00:00:00',
  `modifiedby` int(11) default '0',
  `modifieddate` timestamp(14) NOT NULL,
  `roleid` VARCHAR(64),
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB;

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL auto_increment,
  `fileid` VARCHAR(64) NOT NULL,
  `tabledefid` VARCHAR(64) NOT NULL,
  `recordid` VARCHAR(64) NOT NULL,
  `createdby` int(11) default '0',
  `creationdate` datetime default '0000-00-00 00:00:00',
  `modifiedby` int(11) default '0',
  `modifieddate` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `therecord` (`recordid`),
  KEY `thetable` (`tabledefid`),
  KEY `thefile` (`fileid`)
) ENGINE=INNODB;

CREATE TABLE `roles` (
  `id` INTEGER UNSIGNED DEFAULT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` VARCHAR(64) NOT NULL,
  `description` TEXT,
  `inactive` tinyint(4) NOT NULL DEFAULT 0,
  `createdby` INTEGER UNSIGNED,
  `creationdate` DATETIME,
  `modifiedby` INTEGER UNSIGNED,
  `modifieddate` TIMESTAMP,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY(`id`),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB;

CREATE TABLE `rolestousers` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` VARCHAR(64) NOT NULL,
  `roleid` VARCHAR(64) NOT NULL,
  PRIMARY KEY(`id`),
  KEY (`userid`),
  KEY (`roleid`)
) ENGINE=INNODB;

CREATE TABLE `scheduler` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(45) default NULL,
  `job` varchar(128) default NULL,
  `pushrecordid` varchar(64) default '',
  `crontab` varchar(64) default NULL,
  `lastrun` datetime default NULL,
  `startdatetime` datetime NOT NULL,
  `enddatetime` datetime default NULL,
  `description` text,
  `inactive` tinyint(3) unsigned NOT NULL default '0',
  `createdby` int(10) unsigned default NULL,
  `creationdate` datetime default NULL,
  `modifiedby` int(10) unsigned default NULL,
  `modifieddate` TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`uuid`),
  KEY `inactivated` (`inactive`),
  KEY `startdate` (`startdatetime`),
  KEY `enddate` (`enddatetime`)
) ENGINE=INNODB;

CREATE TABLE `tabs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(45) NOT NULL,
  `tabgroup` varchar(45) NOT NULL,
  `location` varchar(128) default NULL,
  `displayorder` int(11) NOT NULL default '0',
  `enableonnew` tinyint(3) unsigned NOT NULL default '0',
  `roleid` VARCHAR(64),
  `tooltip` varchar(128) default NULL,
  `notificationsql` text,
  `createdby` int(11) default NULL,
  `creationdate` datetime default NULL,
  `modifiedby` int(10) unsigned default NULL,
  `modifieddate` timestamp,
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB;

CREATE TABLE `smartsearches` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `fromclause` text NOT NULL,
  `valuefield` varchar(255) NOT NULL,
  `displayfield` text NOT NULL,
  `secondaryfield` text NOT NULL,
  `classfield` text NOT NULL,
  `searchfields` text NOT NULL,
  `filterclause` text NOT NULL,
  `rolefield` text,
  `tabledefid` VARCHAR(64),
  `moduleid` VARCHAR(64),
  `createdby` int(10) unsigned NOT NULL,
  `creationdate` datetime NOT NULL,
  `modifiedby` int(10) unsigned default NULL,
  `modifieddate` timestamp,
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`uuid`)
) ENGINE=INNODB;

CREATE TABLE `widgets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `type` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `file` varchar(255) NOT NULL,
  `roleid` VARCHAR(64),
  `moduleid` VARCHAR(64),
  `default` tinyint(4) NOT NULL default '0',
  `createdby` int(11) default NULL,
  `creationdate` datetime default NULL,
  `modifiedby` int(10) unsigned default NULL,
  `modifieddate` timestamp,
  PRIMARY KEY(`id`),
  UNIQUE KEY(`uuid`)
) ENGINE=INNODB;

CREATE TABLE `userpreferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` TEXT,
  PRIMARY KEY  (`id`),
  KEY(`userid`),
  KEY(`name`)
) ENGINE=INNODB;
