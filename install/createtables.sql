CREATE TABLE `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileid` varchar(64) NOT NULL,
  `tabledefid` varchar(64) NOT NULL,
  `recordid` varchar(64) NOT NULL,
  `createdby` int(11) DEFAULT '0',
  `creationdate` datetime DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT '0',
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `therecord` (`recordid`),
  KEY `thetable` (`tabledefid`),
  KEY `thefile` (`fileid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `listname` varchar(64) NOT NULL DEFAULT '',
  `thevalue` varchar(64) DEFAULT NULL,
  UNIQUE KEY `theid` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=0;

CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `description` text,
  `file` longblob,
  `type` varchar(100) DEFAULT '',
  `createdby` int(11) DEFAULT '0',
  `creationdate` datetime DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT '0',
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `roleid` varchar(64) DEFAULT NULL,
  `custom1` double DEFAULT NULL,
  `custom2` double DEFAULT NULL,
  `custom3` datetime DEFAULT NULL,
  `custom4` datetime DEFAULT NULL,
  `custom5` varchar(255) DEFAULT NULL,
  `custom6` varchar(255) DEFAULT NULL,
  `custom7` tinyint(1) NOT NULL,
  `custom8` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(25) DEFAULT NULL,
  `userid` varchar(64) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `value` text,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `link` varchar(128) NOT NULL DEFAULT '',
  `parentid` varchar(64) DEFAULT '',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `modifiedby` int(11) DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `roleid` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `displayname` varchar(128) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` text,
  `version` varchar(32) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `assignedtoid` varchar(64) DEFAULT NULL,
  `attachedid` varchar(64) DEFAULT NULL,
  `attachedtabledefid` varchar(64) DEFAULT NULL,
  `content` text,
  `assignedtodate` date DEFAULT NULL,
  `subject` varchar(128) DEFAULT NULL,
  `type` char(2) NOT NULL DEFAULT 'NT',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `importance` int(11) NOT NULL DEFAULT '0',
  `parentid` varchar(64) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `completed` tinyint(4) NOT NULL DEFAULT '0',
  `private` tinyint(4) NOT NULL DEFAULT '0',
  `status` varchar(64) DEFAULT NULL,
  `completeddate` date DEFAULT NULL,
  `location` varchar(128) DEFAULT NULL,
  `category` varchar(128) DEFAULT NULL,
  `assignedtotime` time DEFAULT NULL,
  `starttime` time DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  `assignedbyid` varchar(64) NOT NULL DEFAULT '0',
  `repeating` smallint(5) unsigned NOT NULL DEFAULT '0',
  `repeattype` enum('Daily','Weekly','Monthly','Yearly') DEFAULT NULL,
  `repeatuntil` date DEFAULT NULL,
  `repeatevery` int(10) unsigned NOT NULL DEFAULT '1',
  `repeattimes` int(10) unsigned DEFAULT NULL,
  `repeateachlist` varchar(128) DEFAULT NULL,
  `repeatontheday` int(10) unsigned DEFAULT NULL,
  `repeatontheweek` int(10) unsigned DEFAULT NULL,
  `firstrepeat` date DEFAULT NULL,
  `lastrepeat` date DEFAULT NULL,
  `timesrepeated` int(10) unsigned NOT NULL DEFAULT '0',
  `repeatname` varchar(255) DEFAULT NULL,
  `custom1` double DEFAULT NULL,
  `custom2` double DEFAULT NULL,
  `custom3` datetime DEFAULT NULL,
  `custom4` datetime DEFAULT NULL,
  `custom5` varchar(255) DEFAULT NULL,
  `custom6` varchar(255) DEFAULT NULL,
  `custom7` tinyint(1) NOT NULL,
  `custom8` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `tofield` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL DEFAULT '',
  `fromfield` varchar(32) NOT NULL DEFAULT '',
  `fromtableid` varchar(64) NOT NULL DEFAULT '',
  `totableid` varchar(64) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT '0',
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `inherint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `fromtableid` (`fromtableid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `tabledefid` varchar(64) NOT NULL,
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `roleid` varchar(64) DEFAULT NULL,
  `reportfile` varchar(128) NOT NULL,
  `description` text,
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reportsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reportuuid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `value` text,
  `type` varchar(32) NOT NULL DEFAULT 'string',
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `defaultvalue` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(10) unsigned DEFAULT NULL,
  `creationdate` datetime DEFAULT NULL,
  `modifiedby` int(10) unsigned DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `custom1` double DEFAULT NULL,
  `custom2` double DEFAULT NULL,
  `custom3` datetime DEFAULT NULL,
  `custom4` datetime DEFAULT NULL,
  `custom5` varchar(255) DEFAULT NULL,
  `custom6` varchar(255) DEFAULT NULL,
  `custom7` tinyint(1) NOT NULL,
  `custom8` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rolestousers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) NOT NULL,
  `roleid` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `roleid` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `scheduler` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `job` varchar(128) DEFAULT NULL,
  `pushrecordid` varchar(64) DEFAULT '',
  `crontab` varchar(64) DEFAULT NULL,
  `lastrun` datetime DEFAULT NULL,
  `startdatetime` datetime NOT NULL,
  `enddatetime` datetime DEFAULT NULL,
  `description` text,
  `inactive` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `createdby` int(10) unsigned DEFAULT NULL,
  `creationdate` datetime DEFAULT NULL,
  `modifiedby` int(10) unsigned DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `inactivated` (`inactive`),
  KEY `startdate` (`startdatetime`),
  KEY `enddate` (`enddatetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `smartsearches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `tabledefid` varchar(64) DEFAULT NULL,
  `moduleid` varchar(64) DEFAULT NULL,
  `createdby` int(10) unsigned NOT NULL,
  `creationdate` datetime NOT NULL,
  `modifiedby` int(10) unsigned DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tablecolumns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `column` text,
  `align` varchar(16) NOT NULL DEFAULT '',
  `footerquery` varchar(255) DEFAULT '',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `sortorder` varchar(128) DEFAULT '',
  `wrap` tinyint(1) NOT NULL DEFAULT '0',
  `size` varchar(16) NOT NULL DEFAULT '',
  `format` enum('date','time','currency','boolean','datetime','filelink','noencoding','bbcode','client','invoice') DEFAULT NULL,
  `roleid` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tabledef` (`tabledefid`),
  KEY `displayorder` (`displayorder`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tablecustomfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `field` varchar(8) NOT NULL DEFAULT '',
  `format` varchar(32) DEFAULT NULL,
  `generator` text,
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `roleid` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `tabledef` (`tabledefid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tabledefs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `displayname` varchar(64) DEFAULT NULL,
  `prefix` varchar(4) DEFAULT NULL,
  `type` varchar(16) NOT NULL DEFAULT 'table',
  `moduleid` varchar(64) NOT NULL,
  `maintable` varchar(64) NOT NULL DEFAULT '',
  `querytable` text,
  `editfile` varchar(128) DEFAULT NULL,
  `editroleid` varchar(64) DEFAULT NULL,
  `addfile` varchar(128) DEFAULT '',
  `addroleid` varchar(64) DEFAULT NULL,
  `importfile` varchar(128) DEFAULT NULL,
  `importroleid` varchar(64) DEFAULT 'Admin',
  `searchroleid` varchar(64) DEFAULT NULL,
  `advsearchroleid` varchar(64) DEFAULT 'Admin',
  `viewsqlroleid` varchar(64) DEFAULT 'Admin',
  `deletebutton` varchar(32) DEFAULT '',
  `canpost` tinyint(4) NOT NULL DEFAULT '0',
  `apiaccessible` tinyint(4) NOT NULL DEFAULT '0',
  `hascustomfields` tinyint(4) NOT NULL DEFAULT '0',
  `defaultwhereclause` text,
  `defaultsortorder` text,
  `defaultsearchtype` varchar(64) DEFAULT '',
  `defaultcriteriafindoptions` varchar(128) DEFAULT '',
  `defaultcriteriaselection` varchar(128) DEFAULT '',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tablefindoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `search` text NOT NULL,
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `roleid` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tabledef` (`tabledefid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tablegroupings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tabledefid` varchar(64) NOT NULL,
  `field` text NOT NULL,
  `displayorder` int(10) unsigned NOT NULL DEFAULT '0',
  `ascending` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) DEFAULT NULL,
  `roleid` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tableoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `option` varchar(128) NOT NULL DEFAULT '',
  `needselect` tinyint(1) NOT NULL DEFAULT '1',
  `othercommand` tinyint(1) NOT NULL DEFAULT '0',
  `roleid` varchar(64) DEFAULT NULL,
  `displayorder` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tabledef` (`tabledefid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tablesearchablefields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabledefid` varchar(64) NOT NULL,
  `field` text NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `type` varchar(16) NOT NULL DEFAULT 'field',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tabs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(45) NOT NULL,
  `tabgroup` varchar(45) NOT NULL,
  `location` varchar(128) DEFAULT NULL,
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `enableonnew` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `roleid` varchar(64) DEFAULT NULL,
  `tooltip` varchar(128) DEFAULT NULL,
  `notificationsql` text,
  `createdby` int(11) DEFAULT NULL,
  `creationdate` datetime DEFAULT NULL,
  `modifiedby` int(10) unsigned DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `userpreferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `login` varchar(64) NOT NULL DEFAULT '',
  `password` blob,
  `firstname` varchar(64) NOT NULL DEFAULT '',
  `lastname` varchar(64) NOT NULL DEFAULT '',
  `lastip` varchar(45) NOT NULL DEFAULT '',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `revoked` tinyint(1) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `modifiedby` int(11) DEFAULT '0',
  `lastlogin` datetime DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` varchar(128) DEFAULT '',
  `mailer` varchar(255) NOT NULL DEFAULT 'mail',
  `sendmail` varchar(255) NOT NULL DEFAULT '/usr/sbin/sendmail -bs',
  `smtphost` varchar(255) NOT NULL DEFAULT 'localhost',
  `smtpport` int(11) NOT NULL DEFAULT '25',
  `smtpauth` int(11) NOT NULL DEFAULT '0',
  `smtpuser` varchar(255) NOT NULL,
  `smtppass` varchar(255) NOT NULL,
  `smtpsecure` varchar(255) NOT NULL DEFAULT 'none',
  `phone` varchar(32) DEFAULT '',
  `department` varchar(128) DEFAULT '',
  `employeenumber` varchar(64) DEFAULT '',
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  `portalaccess` tinyint(4) NOT NULL DEFAULT '0',
  `custom1` double DEFAULT NULL,
  `custom2` double DEFAULT NULL,
  `custom3` datetime DEFAULT NULL,
  `custom4` datetime DEFAULT NULL,
  `custom5` varchar(255) DEFAULT NULL,
  `custom6` varchar(255) DEFAULT NULL,
  `custom7` tinyint(1) NOT NULL,
  `custom8` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `usersearches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(128) DEFAULT '',
  `sqlclause` text,
  `type` char(3) NOT NULL DEFAULT 'SCH',
  `roleid` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tabledefid` (`tabledefid`),
  KEY `thetype` (`type`),
  KEY `user` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `widgets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `type` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `file` varchar(255) NOT NULL,
  `roleid` varchar(64) DEFAULT NULL,
  `moduleid` varchar(64) DEFAULT NULL,
  `default` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) DEFAULT NULL,
  `creationdate` datetime DEFAULT NULL,
  `modifiedby` int(10) unsigned DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;