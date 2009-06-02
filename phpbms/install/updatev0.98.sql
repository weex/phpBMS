--tablecustomfields CREATE--
CREATE TABLE tablecustomfields (
  `id` int(11) NOT NULL auto_increment,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL default '',
  `field` varchar(8) NOT NULL default '',
  `format` varchar(32),
  `generator` TEXT,
  `required` TINYINT(4) NOT NULL default 0,
  `displayorder` int(11) NOT NULL default 0,
  `roleid` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `tabledef` (`tabledefid`)
) ENGINE=INNODB;
--end tablecustomfields CREATE--
--userpreferences CREATE--
CREATE TABLE `userpreferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` TEXT,
  PRIMARY KEY  (`id`),
  KEY `thename` (`name`)
) ENGINE=INNODB;
--end userpreferences CREATE--
--widgets CREATE--
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
  PRIMARY KEY  (`id`),
  KEY `uniqueid` (`uuid`)
) ENGINE=INNODB;
--end widgets CREATE--

--attachments ALTER--
ALTER TABLE `attachments` ENGINE=INNODB;
ALTER TABLE `attachments`
    MODIFY `fileid` VARCHAR(64) NOT NULL,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `recordid` VARCHAR(64);
--end attachemnts ALTER--
--choices ALTER--
ALTER TABLE `choices` ENGINE=INNODB;
--end choices ALTER--
--files ALTER--
ALTER TABLE `files` ENGINE=INNODB;
ALTER TABLE `files`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0,
    MODIFY `roleid` varchar(64);
--end files ALTER--
--log ALTER--
ALTER TABLE `log` ENGINE=INNODB;
ALTER TABLE `log`
    MODIFY `userid` VARCHAR(64);
--end log ALTER--
--menu ALTER--
ALTER TABLE `menu` ENGINE=INNODB;
ALTER TABLE `menu`
    ADD COlUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY COLUMN `parentid` varchar(64) NOT NULL DEFAULT '0',
    MODIFY `roleid` varchar(64);
--end menu ALTER--
--modules ALTER--
ALTER TABLE `modules` ENGINE=INNODB;
ALTER TABLE `modules`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`;
--end modules ALTER--
--notes ALTER--
ALTER TABLE `notes` ENGINE=INNODB;
ALTER TABLE `notes`
    MODIFY `type` CHAR(2) NOT NULL DEFAULT 'NT',
    MODIFY `assignedtoid` varchar(64),
    MODIFY `attachedid` varchar(64),
    MODIFY `attachedtabledefid` varchar(64),
    MODIFY `parentid` varchar(64),
    MODIFY `assignedbyid` varchar(64),
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end notes ALTER--
--relationships ALTER--
ALTER TABLE `relationships` ENGINE=INNODB;
ALTER TABLE `relationships`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY `fromtableid` VARCHAR(64) NOT NULL,
    MODIFY `totableid` VARCHAR(64) NOT NULL;
--end relationships ALTER--
--reports ALTER--
ALTER TABLE `reports` ENGINE=INNODB;
ALTER TABLE `reports`
    MODIFY `tabledefid` varchar(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64),
    MODIFY `reportfile` VARCHAR(128) NOT NULL,
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`;
--end reports ALTER--
--roles ALTER--
ALTER TABLE `roles` ENGINE=INNODB;
ALTER TABLE `roles`
    MODIFY COLUMN `inactive` TINYINT(4) NOT NULL DEFAULT 0,
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end roles ALTER--
--rolestousers ALTER--
ALTER TABLE `rolestousers` ENGINE=INNODB;
ALTER TABLE `rolestousers`
    MODIFY `userid` varchar(64),
    MODIFY `roleid` varchar(64);
--end rolestousers ALTER--
--scheduler ALTER--
ALTER TABLE `scheduler` ENGINE=INNODB;
ALTER TABLE `scheduler`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`;
--end scheduler ALTER--
--settings ALTER--
ALTER TABLE `settings` ENGINE=INNODB;
--end settings ALTER--
--smartsearches ALTER--
ALTER TABLE `smartsearches` ENGINE=INNODB;
ALTER TABLE `smartsearches`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY `moduleid` VARCHAR(64),
    MODIFY `tabledefid` VARCHAR(64);
--end smartsearches ALTER--
--tablecolumns ALTER--
ALTER TABLE `tablecolumns` ENGINE=INNODB;
ALTER TABLE `tablecolumns`
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64) NOT NULL DEFAULT '';
--end tablecolumns ALTER--
--tabledefs ALTER--
ALTER TABLE `tabledefs` ENGINE=INNODB;
ALTER TABLE `tabledefs`
    MODIFY COLUMN `defaultwhereclause` TEXT DEFAULT NULL,
    MODIFY COLUMN `defaultsortorder` TEXT,
    MODIFY `searchroleid` VARCHAR(64),
    ADD COLUMN `importfile` VARCHAR(128) DEFAULT NULL AFTER `addroleid`,
    ADD COLUMN `importroleid` VARCHAR(64) NOT NULL DEFAULT 'Admin' AFTER `importfile`,
    ADD COLUMN `canpost` tinyint(4) NOT NULL default '0' AFTER `deletebutton`,
    ADD COLUMN `hascustomfields` tinyint(4) NOT NULL default '0' AFTER `canpost`,
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `prefix` VARCHAR(4) AFTER `displayname`;
--end tabledefs ALTER--
--tablefindoptions ALTER--
ALTER TABLE `tablefindoptions` ENGINE=INNODB;
ALTER TABLE `tablefindoptions`
    MODIFY COLUMN `search` TEXT NOT NULL,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64) NOT NULL DEFAULT '';
--end tablefindoptions ALTER--
--tablegroupings ALTER--
ALTER TABLE `tablegroupings` ENGINE=INNODB;
ALTER TABLE `tablegroupings`
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64) NOT NULL DEFAULT '';
--end tablegroupings ALTER--
--tableoptions ALTER--
ALTER TABLE `tableoptions` ENGINE=INNODB;
ALTER TABLE `tableoptions`
    ADD COLUMN `needselect` BOOLEAN NOT NULL DEFAULT 1 AFTER `option`,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64) NOT NULL DEFAULT '';
--tableoptions ALTER--
--tablesearchablefields ALTER--
ALTER TABLE `tablesearchablefields` ENGINE=INNODB;
ALTER TABLE `tablesearchablefields`
    MODIFY COLUMN `field` TEXT NOT NULL,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL;
--end tablesearchablefields ALTER--
--tabs ALTER--
ALTER TABLE `tabs` ENGINE=INNODB;
ALTER TABLE `tabs`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY `roleid` VARCHAR(64);
--end tabs ALTER--
--users ALTER--
ALTER TABLE `users` ENGINE=INNODB;
ALTER TABLE `users`
    ADD COLUMN `lastip` VARCHAR(45) NOT NULL DEFAULT '' AFTER `lastname`,
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end users ALTER--
--usersearches ALTER--
ALTER TABLE `usersearches` ENGINE=INNODB;
ALTER TABLE `usersearches`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `userid` VARCHAR(64) NOT NULL;
--end usersearches ALTER--

--menu INSERT--
DELETE FROM `menu`;
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', 'Tools', '', '0', '3', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:d9e0eaa6-26b3-fcfb-f1b5-ee0eef8a857a', 'Notes', 'search.php?id=12', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '30', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:03e984b6-d7ac-def2-a4f5-662003e94bfd', 'Tasks', 'search.php?id=23', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '40', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:c4871074-90e9-c9bb-bcf9-b69ca0c30e8b', 'Events', 'search.php?id=24', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '50', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:2bcd88e6-703f-128c-7f18-1aad44fb46fb', 'Snapshot', 'modules/base/snapshot.php', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '10', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', 'System', '', '0', '10', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:e44cf976-658a-50d7-4a8f-b575713e3964', 'Configuration', 'modules/base/adminsettings.php', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '10', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:cf16add3-b02a-bd9b-b3c7-3fe9d0d2e0ba', 'Users', 'search.php?id=9', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '40', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:d727dda4-6ac5-dd23-992b-7cf64cd96620', 'Roles', 'search.php?id=200', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '50', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:afddfee1-5ab7-2064-204f-816e9df929ac', '----', 'N/A', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '15', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:9c845b2d-7383-4182-1bf5-fe9b770f1d63', 'Menu', 'search.php?id=19', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '50', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:ef5853a0-3b57-06e5-a8d4-31bfbdb207b5', 'Files', 'search.php?id=26', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '910', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:1f72cd68-1e5a-e718-3b38-8671da9b0a1d', 'Saved Searchs/Sorts', 'search.php?id=17', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '930', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:00ddbccd-2761-3347-22ee-1adce9696b66', '----', 'N/A', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '45', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:8825339e-76a8-b51a-fdce-7b409451962c', 'Reports', 'search.php?id=16', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '70', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:2dea83ff-2927-0859-ab97-530ee76e7bb8', 'Relationships', 'search.php?id=10', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '60', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:76f8b6cd-f42d-0823-3e12-5cbe39f7fbdb', 'Table Definitions', 'search.php?id=11', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '940', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:f1780935-8018-d240-8e74-f8fde4f8e1bb', 'Modules', 'search.php?id=21', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '60', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:e0a2cc66-9b44-f0cb-84a7-45eb3307298f', 'My Account', 'modules/base/myaccount.php', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '20', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:b63e3218-0a12-3e51-88b7-8af400a74a7e', 'Scheduler', 'search.php?id=201', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '32', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:5f875b57-f499-2307-6d57-61ba49b72e82', 'System Log', 'search.php?id=202', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '20', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:bd2181f5-b938-011b-7e44-81728310bdf5', 'Smart Searches', 'search.php?id=204', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '80', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:f8392545-41f4-d39a-da7e-9116c9a35502', 'Tabs', 'search.php?id=203', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '100', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:3620bdc0-edaa-ad59-8ac5-193f855a9584', 'Log Out', 'logout.php', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '10', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:2da14499-f301-9b18-e384-e0e73f06509e', 'Help', '', '0', '200', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:113b56da-3722-6518-4c6a-7804d7ed0d19', 'About phpBMS', 'javascript:menu.showHelp()', 'menu:2da14499-f301-9b18-e384-e0e73f06509e', '0', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:08a6bb60-4557-b7d2-f2ba-09d828a1d9b2', 'Snapshot Widgets', 'search.php?id=205', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '90', 1, 1, NOW(), NOW(), 'Admin');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:30bc9743-3530-7705-283a-d740b19238cf', '----', 'N/A', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '20', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:13e03413-2f08-9b48-98a2-9bb83e4d15a1', '----', 'N/A', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '900', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:83d23ec3-ad10-09e1-8c80-72de0c4747f9', '----', 'N/A', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '920', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', 'Account', '', '0', '5', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:e8401ebb-c369-304f-053d-8195988e7faf', '----', 'N/A', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '30', 1, 1, NOW(), NOW(), 'Admin');
--end menu INSERT--
--modules UPDATE--
UPDATE `modules` SET `uuid`='mod:29873ee8-c12a-e3f6-9010-4cd24174ffd7' WHERE `id`='1';
--end modules UPDATE--
--reports UPDATE--
UPDATE `reports` SET
    `uuid`='rpt:37cee478-b57e-2d53-d951-baf3937ba9e0'
WHERE
    `name`='Raw Table Print';
UPDATE `reports` SET
    `uuid`='rpt:dac75fb9-91d2-cb1e-9213-9fab6d32f4c8',
    `description` = 'This report will generate a comma-delimited text file. Values are encapsulated in quotes, and the first line lists the field names.'
WHERE
    `name`='Raw Table Export';
UPDATE `reports` SET `uuid`='rpt:a6999cc3-59bb-6af3-460e-d5d791afb842' WHERE `name`='Note Summary';
UPDATE `reports` SET `uuid`='rpt:2944b204-5967-348a-8679-6835f45f0d79' WHERE `name`='SQL Export';
UPDATE `reports` SET `uuid`='rpt:37a299d1-d795-ad83-4b47-0778c16a381c' WHERE `name`='Support Tables SQL Export';
--end reports UPDATE--
--scheduler INSERT--
INSERT INTO `scheduler` (`uuid`, `name`, `job`, `crontab`, `lastrun`, `startdatetime`, `enddatetime`, `description`, `inactive`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('schd:fb52e7fb-bb49-7f5f-89e1-002b2785f085', 'Clean Import Files', './scheduler_delete_tempimport.php', '30::*::*::*::*', '2009-05-28 12:30:02', '2009-05-07 17:27:13', NULL, 'This will delete any temporary import files that are present (for whatever reason) after 30 minutes of their creation.', '0', 1, NOW(), 1, NOW());
INSERT INTO `scheduler` (`uuid`, `name`, `job`, `crontab`, `lastrun`, `startdatetime`, `enddatetime`, `description`, `inactive`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('schd:d1c247de-9811-d37f-ad94-a8472dc1bc9c', 'Remove Excess System Log Records', './scheduler_delete_logs.php', '*::24::*::*::*', NULL, '2009-03-31 12:00:00', NULL, 'This script will trim the system log when there are more than 2000 records present at the time of its calling (default will be every 24 hours).', '0', 1, NOW(), 1, NOW());
--end scheduler INSERT--
--smartsearches UPDATE--
UPDATE `smartsearches` SET `uuid`='smrt:ccc73fa4-6176-fad4-fbb1-5186d0edbdd1' WHERE `id`='2';
UPDATE `smartsearches` SET `uuid`='smrt:855406d5-659d-c907-74a1-acfd3802fd73' WHERE `id`='5';
UPDATE `smartsearches` SET `uuid`='smrt:ed5b1d7f-b0fe-2088-f17c-47bfbe1ace25' WHERE `id`='9';
--end smartsearches UPDATE--
--tablecolumns INSERT--
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('205', 'widget', 'concat(\'[b]\', widgets.title, \'[/b][br]\', widgets.uuid)', 'left', '', '0', 'widgets.title', '0', '100%', 'bbcode', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('205', 'role', 'IF(widgets.roleid != 0, IF(widgets.roleid != -100, roles.name, \'Administrator\'), \'EVERYONE\')', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('205', 'file', 'widgets.file', 'left', '', '1', '', '0', '', NULL, '0');
DELETE FROM `tablecolumns` WHERE `tabledefid` = '19';
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('19', 'link', 'menu.link', 'left', '', '1', '', '1', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('19', 'access', 'if(menu.roleid=0,\'EVERYONE\',if(menu.roleid=-100,\'Administrators\',roles.name))', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('19', 'Item', 'IF(menu.parentid = \'0\', CONCAT(\'[b]\', menu.name,\' [/b]\'), menu.name)', 'left', '', '0', '', '0', '100%', 'bbcode', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('19', 'display order', 'menu.displayorder', 'right', '', '3', '', '0', '', NULL, '0');
--end tablecolumns INSERT--
--tabledefs INSERT--
INSERT INTO `tabledefs` (`id`, `uuid`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('205', 'tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'Snapshot Widgets', 'system', '1', 'widgets', '((widgets INNER JOIN modules ON widgets.moduleid = modules.id) LEFT JOIN roles ON widgets.roleid = roles.id) ', 'modules/base/widgets_addedit.php', '-100', 'modules/base/widgets_addedit.php', '-100', NULL, '-100', '-100', '-100', '-100', 'delete', '0', '0', 'widgets.id != -1', 'widgets.title', NULL, NULL, NULL, 1, NOW(), 1, NOW());
DELETE FROM `tabledefs` WHERE `id` = '19';
INSERT INTO `tabledefs` (`id`, `uuid`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('19', 'tbld:83187e3d-101e-a8a5-037f-31e9800fed2d', 'Menu', 'system', '1', 'menu', '((menu LEFT JOIN menu as parentmenu on menu.parentid=parentmenu.id) LEFT JOIN roles on menu.roleid=roles.id)', 'modules/base/menu_addedit.php', '-100', 'modules/base/menu_addedit.php', '-100', NULL, '-100', '-100', '-100', '-100', 'delete', '0', '0', 'menu.id!=0', 'if(parentmenu.name is null,menu.displayorder,parentmenu.displayorder+(menu.displayorder+1)/10000)', '', '', '', 1, NOW(), 1, NOW());
--end tabledefs INSERT--
--tabledefs UPDATE--
UPDATE `tabledefs` SET `hascustomfields` = 1 WHERE `id` IN(12, 9, 26, 200);
UPDATE `tabledefs` SET
    `uuid` = 'tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb',
    `prefix` = 'usr'
WHERE
    `id`='9';
UPDATE `tabledefs` SET
    `uuid`='tbld:8d19c73c-42fb-d829-3681-d20b4dbe43b9',
    `prefix` = 'rln'
WHERE
    `id`='10';
UPDATE `tabledefs` SET
    `uuid`='tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3',
    `prefix` = 'tbld'
WHERE
    `id`='11';
UPDATE `tabledefs` SET
    `uuid`='tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1',
    `prefix` = 'note'
WHERE
    `id`='12';
UPDATE `tabledefs` SET
    `uuid`='tbld:d595ef42-db9d-2233-1b9b-11dfd0db9cbb',
    `prefix` = 'rpt'
WHERE
    `id`='16';
UPDATE `tabledefs` SET
    `uuid`='tbld:e251524a-2da4-a0c9-8725-d3d0412d8f4a',
    `prefix` = 'sss'
WHERE
    `id`='17';
UPDATE `tabledefs` SET
    `uuid`='tbld:ea159d67-5e89-5b7f-f5a0-c740e147cd73',
    `prefix` = 'mod'
WHERE
    `id`='21';
UPDATE `tabledefs` SET `uuid`='tbld:2bc3e683-81f9-694a-9550-a0c7263057de' WHERE `id`='23';
UPDATE `tabledefs` SET `uuid`='tbld:0fcca651-6c34-c74d-ac04-2d88f602dd71' WHERE `id`='24';
UPDATE `tabledefs` SET
    `uuid`='tbld:80b4f38d-b957-bced-c0a0-ed08a0db6475',
    `prefix` = 'file'
WHERE
    `id`='26';
UPDATE `tabledefs` SET `uuid`='tbld:edb8c896-7ce3-cafe-1d58-5aefbcd5f3d7' WHERE `id`='27';
UPDATE `tabledefs` SET
    `uuid`='tbld:87b9fe06-afe5-d9c6-0fa0-4a0f2ec4ee8a',
    `prefix` = 'role'
WHERE
    `id`='200';
UPDATE `tabledefs` SET
    `uuid`='tbld:83de284b-ef79-3567-145c-30ca38b40796',
    `prefix` = 'schd'
WHERE
    `id`='201';
UPDATE `tabledefs` SET `uuid`='tbld:3f71ab66-1f84-d68b-e2a3-3ee3bb0ec667' WHERE `id`='202';
UPDATE `tabledefs` SET
    `uuid`='tbld:7e75af48-6f70-d157-f440-69a8e7f59d38',
    `prefix` = 'tab'
WHERE
    `id`='203';
UPDATE `tabledefs` SET
    `uuid`='tbld:29925e0a-c825-0067-8882-db4b57866a96',
    `prefix` = 'smsr'
WHERE
    `id`='204';
UPDATE `tabledefs` SET `prefix` = 'wdgt' WHERE id = 205;
UPDATE `tabledefs` SET `prefix` = 'menu' WHERE id = 19;
--end tabledefs UPDATE--
--tablefindoptions INSERST--
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('205', 'All Records', 'widgets.id!=-1', '0', '0');
--end tablefindoptions INSERT--
--tablefindoptions UPDATE--
UPDATE `tablefindoptions` SET `search` = 'notes.type=\'TS\' AND notes.private=0' WHERE `tabledefid` = 23 AND `name` = 'Public Tasks';
UPDATE `tablefindoptions` SET `search` = 'notes.type=\'TS\' and notes.assignedbyid={{$_SESSION[\'userinfo\'][\'id\']}} and notes.completed=0' WHERE `tabledefid` = 23 AND `name` = 'Uncomplete Tasks Assigned By Me';
--end tablefindoptions UPDATE--
--tablegroupings INSERT--
INSERT INTO `tablegroupings` (`tabledefid`, `field`, `displayorder`, `ascending`, `name`, `roleid`) VALUES ('205', 'modules.name', '1', '1', 'Module', '0');
INSERT INTO `tablegroupings` (`tabledefid`, `field`, `displayorder`, `ascending`, `name`, `roleid`) VALUES ('205', 'widgets.type', '2', '1', 'Area', '0');
DELETE FROM `tablegroupings` WHERE `tabledefid` = '19';
INSERT INTO `tablegroupings` (`tabledefid`, `field`, `displayorder`, `ascending`, `name`, `roleid`) VALUES ('19', 'if(menu.parentid=\'0\',concat( lpad(menu.displayorder,3,\"0\"), \" - \" ,menu.name )  , concat( lpad(parentmenu.displayorder,3,\"0\") , \" - \",parentmenu.name))', '1', '1', '', '0');
--end tablegroupings INSERT--
--tableoptions INSERT--
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('9', 'import', '1', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('10', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('11', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('12', 'import', '1', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('16', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('17', 'import', '1', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('19', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('21', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('23', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('24', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('26', 'import', '1', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('27', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('200', 'import', '1', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('201', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('202', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('203', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('204', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('205', 'new', '1', '0', '0', '0', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('205', 'edit', '1', '1', '0', '0', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('205', 'printex', '1', '0', '0', '0', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('205', 'select', '1', '0', '0', '0', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('205', 'import', '1', '0', '0', '-100', '0');
--end tableoptions INSERT--
--tableoptions UPDATE--
UPDATE `tableoptions` SET `needselect` = 0 WHERE `name` IN('massEmail','new','printex','select') AND `tabledefid` IN (9,10,11,12,16,17,19,21,23,24,26,27,200,201,202,203,204);
--end tableoptions UPDATE--
--tablesearchablefields INSERT--
INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`) VALUES ('205', 'widgets.id', 'id', '1', 'field');
--end tablesearchablefields INSERT--
--tabs INSERT--
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:2ebf956d-5e39-c7d5-16b7-501b64685a5a', 'custom fields', 'tabledefs entry', 'modules/base/tabledefs_custom.php', '60', '0', '-100', NULL, NULL, 1, NOW(), 1, NOW());
--end tabs INSERT--
--tabs UPDATE--
UPDATE `tabs` SET `uuid`='tab:fdf064e0-f2d9-6c67-b64f-449e72e859b9' WHERE `id`='1';
UPDATE `tabs` SET `uuid`='tab:b1011143-1d47-520e-5879-3953a4f5055b' WHERE `id`='2';
UPDATE `tabs` SET `uuid`='tab:c5bdaf10-062c-fb3a-f40f-ddce821fd579' WHERE `id`='3';
UPDATE `tabs` SET `uuid`='tab:276dacd4-4a37-d979-aeda-a7982f632559' WHERE `id`='4';
UPDATE `tabs` SET `uuid`='tab:22d08e82-5047-4150-6de7-49e89149f56b' WHERE `id`='5';
UPDATE `tabs` SET `uuid`='tab:c111eaf5-692b-9c7d-1d46-1bacb6703361' WHERE `id`='100';
--end tabs UPDATE--
--users UPDATE--
UPDATE `users` SET `uuid`='usr:5c196e01-193a-8952-fee7-29b4e5e6a0b0' WHERE `id`='1';
UPDATE `users` SET `uuid`='usr:cb67a60b-a264-735c-6189-49a7c883af0b' WHERE `id`='2';
UPDATE `users` SET `uuid`='usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75' WHERE `id`='3';
--end users UPDATE--
--widgets INSERT--
INSERT INTO `widgets` (`uuid`, `type`, `title`, `file`, `roleid`, `moduleid`, `default`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('wdgt:a1aec114-954b-37c1-0474-7d4e851c728c', 'little', 'Workload', 'widgets/workload/class.php', '0', '1', '1', 1, NOW(), 1, NOW());
INSERT INTO `widgets` (`uuid`, `type`, `title`, `file`, `roleid`, `moduleid`, `default`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('wdgt:13d228d3-bbee-e7d2-6571-83a568688e3d', 'big', 'Events', 'widgets/events/class.php', '0', '1', '1', 1, NOW(), 1, NOW());
INSERT INTO `widgets` (`uuid`, `type`, `title`, `file`, `roleid`, `moduleid`, `default`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('wdgt:bc323640-6497-cb6f-5897-029af7dcb3c9', 'little', 'System Statistics', 'widgets/systemstats/class.php', '-100', '1', '0', 1, NOW(), 1, NOW());
--end widgets INSERT--
