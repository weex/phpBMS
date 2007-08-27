ALTER TABLE `tax` MODIFY COLUMN `inactive` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `shippingmethods` MODIFY COLUMN `inactive` TINYINT(4) NOT NULL DEFAULT 0, MODIFY COLUMN `priority` INTEGER NOT NULL DEFAULT 0, MODIFY COLUMN `canestimate` TINYINT(4) NOT NULL DEFAULT 0, MODIFY COLUMN `createdby` INTEGER UNSIGNED NOT NULL DEFAULT 0, MODIFY COLUMN `creationdate` DATETIME NOT NULL DEFAULT '0000-00-00';
INSERT INTO `menu` (`name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('email projects','search.php?id=22',200,100,1,1,NOW(),NOW(),-100);
INSERT INTO `settings` (`name`, `value`) VALUES ('default_payment','0');
INSERT INTO `settings` (`name`, `value`) VALUES ('default_shipping','0');
INSERT INTO `settings` (`name`, `value`) VALUES ('default_discount','0');
INSERT INTO `settings` (`name`, `value`) VALUES ('default_taxarea','0');
INSERT INTO `reports` (`name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('223', 'Totals - Tax', 'report', '3', '50', '30', 'modules/bms/reports/invoices_totals_tax.php', 'Tax Totals', 1, NOW(), 1, NOW());