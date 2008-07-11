ALTER TABLE `clients` ADD COLUMN `taxid` VARCHAR(64) default NULL AFTER `webaddress`;
INSERT INTO `settings` (`name`, `value`) VALUES ('company_taxid', '');