--postingsessions CREATE--
CREATE TABLE `postingsessions` (`id` int(11) NOT NULL auto_increment,`sessiondate` datetime NOT NULL default '0000-00-00 00:00:00',`source` varchar(64) NOT NULL default '',`recordsposted` int(11) NOT NULL default '0', `userid` int(11) NOT NULL default '0', UNIQUE KEY `theid` (`id`)) ENGINE=INNODB;
--endpostingsessions CREATE--
--productstoproductcategories CREATE--
CREATE TABLE `productstoproductcategories` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `productuuid` varchar(64) NOT NULL,
  `productcategoryuuid` varchar(64) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=INNODB;
--end productstoproductcategories CREATE--

--addresses ALTER--
ALTER TABLE `addresses` ENGINE=INNODB;
ALTER TABLE `addresses`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end addresses ALTER--
--addresstorecord ALTER--
ALTER TABLE `addresstorecord` ENGINE=INNODB;
ALTER TABLE `addresstorecord`
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `recordid` VARCHAR(64) NOT NULL,
    MODIFY `addressid` VARCHAR(64) NOT NULL,
    ADD INDEX (`tabledefid`),
    ADD INDEX (`recordid`),
    ADD INDEX (`addressid`);
--end addresstorecord ALTER--
--aritems ALTER--
ALTER TABLE `aritems` ENGINE=INNODB;
ALTER TABLE `aritems`
    ADD COLUMN `uuid` VARCHAR(64) NOT NULL,
    MODIFY `clientid` VARCHAR(64) NOT NULL,
    MODIFY `relatedid` VARCHAR(64);
--end aritems ALTER--
--attachements ALTER--
ALTER TABLE `attachments` ENGINE=INNODB;
--end attachements ALTER--
--clientemailprojects ALTER--
ALTER TABLE `clientemailprojects` ENGINE=INNODB;
ALTER TABLE `clientemailprojects`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY `userid` VARCHAR(64) NOT NULL;
--end clientemailprojects ALTER--
--clients ALTER--
ALTER TABLE `clients` ENGINE=INNODB;
ALTER TABLE `clients`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `canemail` tinyint(1) NOT NULL default '0' AFTER `email`,
    ADD COLUMN `taxid` VARCHAR(64) default NULL AFTER `webaddress`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0,
    MODIFY `salesmanagerid` VARCHAR(64),
    MODIFY `paymentmethodid` VARCHAR(64),
    MODIFY `shippingmethodid` VARCHAR(64),
    MODIFY `discountid` VARCHAR(64),
    MODIFY `taxareaid` VARCHAR(64);
--end clients ALTER--
--discounts ALTER--
ALTER TABLE `discounts` ENGINE=INNODB;
ALTER TABLE `discounts`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end discounts ALTER--
--invoices ALTER--
ALTER TABLE `invoices` ENGINE=INNODB;
ALTER TABLE `invoices`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `postingsessionid` int(11) default NULL,
    ADD COLUMN `iscreditmemo` tinyint(3) unsigned NOT NULL default '0' AFTER `type`,
    ADD COLUMN `cmuuid` varchar(64) default NULL AFTER `iscreditmemo`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0,
    MODIFY `clientid` VARCHAR(64) NOT NULL,
    MODIFY `statusid` VARCHAR(64),
    MODIFY `assignedtoid` VARCHAR(64),
    MODIFY `discountid` VARCHAR(64),
    MODIFY `taxareaid` VARCHAR(64),
    MODIFY `shippingmethodid` VARCHAR(64),
    MODIFY `paymentmethodid` VARCHAR(64),
    MODIFY `billingaddressid` VARCHAR(64),
    MODIFY `shiptoaddressid` VARCHAR(64),
    MODIFY `ccnumber` blob default NULL,
    MODIFY `ccexpiration` blob default NULL,
    MODIFY `ccverification` blob default NULL,
    MODIFY `accountnumber` blob default NULL,
    MODIFY `routingnumber` blob default NULL;
--end invoices ALTER--
--invoicestatuses ALTER--
ALTER TABLE `invoicestatuses` ENGINE=INNODB;
ALTER TABLE `invoicestatuses`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0,
    MODIFY `defaultassignedtoid` VARCHAR(64);
--end invoicestatuses ALTER--
--invoicestatushistory ALTER--
ALTER TABLE `invoicestatushistory` ENGINE=INNODB;
ALTER TABLE `invoicestatushistory`
    MODIFY `invoiceid` VARCHAR(64) NOT NULL,
    MODIFY `invoicestatusid` VARCHAR(64) NOT NULL,
    MODIFY `assignedtoid` VARCHAR(64);
--end invoicestatushistory ALTER--
--lineitems ALTER--
ALTER TABLE `lineitems` ENGINE=INNODB;
ALTER TABLE `lineitems`
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0,
    MODIFY `productid` VARCHAR(64);
--end lineitems ALTER--
--paymentmethods ALTER--
ALTER TABLE `paymentmethods` ENGINE=INNODB;
ALTER TABLE `paymentmethods`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end paymentmethods ALTER--
--prequisites ALTER--
ALTER TABLE `prerequisites` ENGINE=INNODB;
ALTER TABLE `prerequisites`
    MODIFY `childid` VARCHAR(64) NOT NULL,
    MODIFY `parentid` VARCHAR(64) NOT NULL;
--end prequisites ALTER--
--productcategories ALTER--
ALTER TABLE `productcategories` ENGINE=INNODB;
ALTER TABLE `productcategories`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0,
    ADD COLUMN `parentid` varchar(64) NOT NULL DEFAULT '' AFTER `name`,
    ADD COLUMN `displayorder` INT(11) NOT NULL DEFAULT 0 AFTER `parentid`;
--end productcategories ALTER--
--products ALTER--
ALTER TABLE `products` ENGINE=INNODB;
ALTER TABLE `products`
    MODIFY COLUMN `categoryid` varchar(64),
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end products ALTER--
--reciptitems ALTER--
ALTER TABLE `receiptitems` ENGINE=INNODB;
ALTER TABLE `receiptitems`
    MODIFY `receiptid` VARCHAR(64) NOT NULL,
    MODIFY `aritemid` VARCHAR(64) NOT NULL;
--end reciptitems ALTER--
--recipts ALTER--
ALTER TABLE `receipts` ENGINE=INNODB;
ALTER TABLE `receipts`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `postingsessionid` int(11) default NULL,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0,
    MODIFY `clientid` VARCHAR(64) NOT NULL,
    MODIFY `paymentmethodid` VARCHAR(64) NOT NULL,
    MODIFY `ccnumber` blob default NULL,
    MODIFY `ccexpiration` blob default NULL,
    MODIFY `ccverification` blob default NULL,
    MODIFY `accountnumber` blob default NULL,
    MODIFY `routingnumber` blob default NULL;
--end recipts ALTER--
--shippinmethods ALTER--
ALTER TABLE `shippingmethods` ENGINE=INNODB;
ALTER TABLE `shippingmethods`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end shippinmethods ALTER--
--tax ALTER--
ALTER TABLE `tax` ENGINE=INNODB;
ALTER TABLE `tax`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end tax ALTER--

--invoicestatuses UPDATE--
UPDATE `invoicestatuses` SET `uuid`='inst:2d08635f-5341-46f5-d2a3-b0ae0008b4d4' WHERE `id`='1';
UPDATE `invoicestatuses` SET `uuid`='inst:00b9767c-a8d5-358e-8d35-824c530d06eb' WHERE `id`='2';
UPDATE `invoicestatuses` SET `uuid`='inst:7b97bb4b-406f-1eb5-ce0c-e5ffa4946267' WHERE `id`='3';
UPDATE `invoicestatuses` SET `uuid`='inst:e8b5e6a7-5797-7901-6266-6adeedd15ec9' WHERE `id`='4';
--end invoicestatuses UPDATE--
--menu INSERT--
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:7261b6a1-6424-77a6-6a02-eada540ec092', 'Client', '', '', '0', 1, 1, NOW(), NOW(), 'role:de7e6679-8bb2-29ee-4883-2fcd756fb120');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:42d221f6-37ef-c755-e335-39d00e3fda58', 'Quick View', 'modules/bms/quickview.php', 'menu:7261b6a1-6424-77a6-6a02-eada540ec092', '0', 1, 1, NOW(), NOW(), 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:d080b1a3-6fac-ae6e-2457-b969a8d67996', 'Clients', 'search.php?id=tbld%3A6d290174-8b73-e199-fe6c-bcf3d4b61083', 'menu:7261b6a1-6424-77a6-6a02-eada540ec092', '10', 1, 1, NOW(), NOW(), 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:96809579-305c-19c3-042b-405e7763a000', 'Check for Duplicates', 'search.php?id=tbld%3Af993cf23-ad4a-047b-e920-d45fee1dc08e', 'menu:7261b6a1-6424-77a6-6a02-eada540ec092', '20', 1, 1, NOW(), NOW(), 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:8cf7d073-72b9-93db-6d07-14578e2a694f', 'Sales', '', '', '1', 1, 1, NOW(), NOW(), 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:652204a6-c0c3-e0b7-d0e4-919c3f3f64f9', 'Sales Orders', 'search.php?id=tbld%3A62fe599d-c18f-3674-9e54-b62c2d6b1883', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '10', 1, 1, NOW(), NOW(), 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:23960b0a-316e-0070-8d64-0834aa443187', 'Item Analysis', 'search.php?id=tbld%3A31423480-a9b0-f0ff-749e-b3b5e18ca93c', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '55', 1, 1, NOW(), NOW(), 'role:259ead9f-100b-55b5-508a-27e33a6216bf');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:7cc050a6-9742-ca54-3a19-3d08867e7cf7', 'Discounts', 'search.php?id=tbld%3A455b8839-162b-3fcb-64b6-eeb946f873e1', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '40', 1, 1, NOW(), NOW(), 'role:259ead9f-100b-55b5-508a-27e33a6216bf');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:e7bb833c-a3ac-0619-88d2-990ab53e63e6', 'Tax Areas', 'search.php?id=tbld%3Ac9ff2c8c-ce1f-659a-9c55-31bca7cce70e', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '70', 1, 1, NOW(), NOW(), 'role:8f5fb368-e7d9-5010-d8f6-b4a78adc0520');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:6f119c0c-241e-4dd8-42c9-c7321e9d5b53', 'Product', '', '', '2', 1, 1, NOW(), NOW(), 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:4d49690e-980b-c705-271e-376339dde62f', 'Products', 'search.php?id=tbld%3A7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', 'menu:6f119c0c-241e-4dd8-42c9-c7321e9d5b53', '0', 1, 1, NOW(), NOW(), 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:24861935-096d-ed0b-98ce-2273da3054c6', 'Product Categories', 'search.php?id=tbld%3A3342a3d4-c6a2-3a38-6576-419299859561', 'menu:6f119c0c-241e-4dd8-42c9-c7321e9d5b53', '1', 1, 1, NOW(), NOW(), 'role:259ead9f-100b-55b5-508a-27e33a6216bf');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:8f14c059-2ff4-0ca6-21ec-7204c3cddb20', 'Prerequisites', 'search.php?id=tbld%3A8179e105-5487-5173-d835-d9d510cc7f1b', 'menu:6f119c0c-241e-4dd8-42c9-c7321e9d5b53', '2', 1, 1, NOW(), NOW(), 'role:de7e6679-8bb2-29ee-4883-2fcd756fb120');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:9352a813-3c5a-efd4-71dc-a5b8bcac2e3d', 'Shipping Methods', 'search.php?id=tbld%3Afa8a0ddc-87d3-a9e9-60b0-1bab374b2993', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '80', 1, 1, NOW(), NOW(), 'role:259ead9f-100b-55b5-508a-27e33a6216bf');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:23c6a92e-932b-4861-1050-561b5f35f3a1', 'Payment Methods', 'search.php?id=tbld%3A380d4efa-a825-f377-6fa1-a030b8c58ffe', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '90', 1, 1, NOW(), NOW(), 'role:259ead9f-100b-55b5-508a-27e33a6216bf');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:48d55385-ce5e-53ca-34c2-1861da882442', '----', 'N/A', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '50', 1, 1, NOW(), NOW(), 'role:259ead9f-100b-55b5-508a-27e33a6216bf');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:96171e3f-2499-9e75-248e-45d637df9128', 'Invoice Statuses', 'search.php?id=tbld%3Ad6e4e1fb-4bfa-cb53-ab9c-1b3e7f907ae2', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '80', 1, 1, NOW(), NOW(), 'role:259ead9f-100b-55b5-508a-27e33a6216bf');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:f020a94f-f78f-c1f9-cd32-133089704403', 'Email Projects', 'search.php?id=tbld%3A157b7707-5503-4161-4dcf-6811f8b0322f', 'menu:7261b6a1-6424-77a6-6a02-eada540ec092', '100', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:abd768e4-10b4-607c-a760-ee58366437e6', 'AR Items', 'search.php?id=tbld%3Ac595dbe7-6c77-1e02-5e81-c2e215736e9c', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '20', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:279a030f-75b5-27bc-68fa-9f6597f785d8', 'Receipts', 'search.php?id=tbld%3A43678406-be25-909b-c715-7e2afc7db601', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '30', 1, 1, NOW(), NOW(), 'role:c9439c3c-499b-7bcc-ee14-fec5bfcf5fc2');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:94c0a95d-3244-107f-2c85-f4207e5cec94', '----', 'N/A', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '100', 1, 1, NOW(), NOW(), 'role:8f5fb368-e7d9-5010-d8f6-b4a78adc0520');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:1b9973a9-b1c7-8cf6-a5ea-ba041a19ac06', 'Post Records', 'modules/bms/post.php', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '110', 1, 1, NOW(), NOW(), 'role:8f5fb368-e7d9-5010-d8f6-b4a78adc0520');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:07b85ffa-178b-f20f-36db-c2ca99274124', 'Posting Sessions', 'search.php?id=tbld%3A97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '120', 1, 1, NOW(), NOW(), 'role:8f5fb368-e7d9-5010-d8f6-b4a78adc0520');
--end menu INSERT--
--moudule UPDATE--
UPDATE `modules` SET `uuid`='mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc' WHERE `id`='2';
--end module UPDATE--
--paymentmethods UPDATE--
UPDATE `paymentmethods` SET `uuid`='paym:5efd94ea-8e71-f5a2-d30a-b23e1ef54b26' WHERE `name`='Business Check';
UPDATE `paymentmethods` SET `uuid`='paym:1e3fb0cd-7a39-cbc5-6a46-3ff95c07f94d' WHERE `name`='Personal Check';
UPDATE `paymentmethods` SET `uuid`='paym:d1aabcd6-0999-03b8-20e1-1b940b58346e' WHERE `name`='Cashiers Check';
UPDATE `paymentmethods` SET `uuid`='paym:f193f243-29e6-ee37-e73b-d0808d1d22d2' WHERE `name`='VISA';
UPDATE `paymentmethods` SET `uuid`='paym:c9a4e2f4-2f46-761c-6bb0-6b01de0d0470' WHERE `name`='Mastercard';
UPDATE `paymentmethods` SET `uuid`='paym:085963a3-a30b-a4ee-887d-d199a0e1ec65' WHERE `name`='American Express';
UPDATE `paymentmethods` SET `uuid`='paym:9fd45f33-ba56-e9a9-e167-00c0ea2200ad' WHERE `name`='Cash';
UPDATE `paymentmethods` SET `uuid`='paym:c4d76cc7-9368-16a1-7cc9-443fe5479fb4' WHERE `name`='Net 30';
UPDATE `paymentmethods` SET `uuid`='paym:a911dad0-d393-e86d-b238-24aea7f6f797' WHERE `name`='Wire Transfer';
UPDATE `paymentmethods` SET `uuid`='paym:77411629-5911-f318-cadd-e6e8b14203e2' WHERE `name`='Money Order';
UPDATE `paymentmethods` SET `uuid`='paym:aa837c8a-8878-5339-bc5c-0004cd6e091c' WHERE `name`='VISA - Debit';
UPDATE `paymentmethods` SET `uuid`='paym:d2909465-4a20-9e88-5e2a-d4f85a57a547' WHERE `name`='Discover Card';
--end paymentmethods UPDATE--
--relationships INSERT--
DELETE FROM `relationships` WHERE `id`<= '32';
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:eb5f285a-da6b-73e4-c098-878f517863b9', 'uuid', 'sales managers', 'salesmanagerid', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 'tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:66443c52-6dd4-b339-aee5-d9794d076e1b', 'clientid', 'sales orders', 'uuid', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '1');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:758183a0-d5f1-eb19-8acc-2de7789895c9', 'uuid', 'clients', 'clientid', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:f8ee5a95-a4b1-f55c-d01f-c705863c60b7', 'uuid', 'tax areas', 'taxareaid', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:21988f3c-9c64-3b74-7ccc-1e71a2f82f2a', 'uuid', 'sales orders', 'invoiceid', 'tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:c6a0e198-c91d-0bdc-0d1d-23d4e2a59faf', 'uuid', 'products', 'productid', 'tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c', 'tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:1ddf2bf3-781e-21b1-9e1f-e424f725a5eb', 'uuid', 'parent products', 'parentid', 'tbld:8179e105-5487-5173-d835-d9d510cc7f1b', 'tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:6675ec95-d545-3804-5ec2-5dc07618ef06', 'uuid', 'child products', 'childid', 'tbld:8179e105-5487-5173-d835-d9d510cc7f1b', 'tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:b1371cef-370c-eb3e-6f52-079b9cd43b2d', 'categoryid', 'products', 'uuid', 'tbld:3342a3d4-c6a2-3a38-6576-419299859561', 'tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', 1, NOW(), 1, NOW(), '1');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:e70aa923-a782-68d4-b56e-0bc2f80c1ae6', 'productid', 'sales order line items', 'uuid', 'tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', 'tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c', 1, NOW(), 1, NOW(), '1');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:15066548-3671-e611-19ac-e278a5d324fc', 'parentid', 'prerequisites', 'uuid', 'tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', 'tbld:8179e105-5487-5173-d835-d9d510cc7f1b', 1, NOW(), 1, NOW(), '1');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:e48c9af6-babc-d43b-891c-cfc9b9329b71', 'taxareaid', 'sales orders', 'uuid', 'tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:518b11b2-226a-2332-ceb4-b4f847b6cfec', 'salesmanagerid', 'sales person for clients', 'uuid', 'tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:cd3a9dfd-6bbd-f9ca-ec56-3a7bc3f9d447', 'createdby', 'created clients', 'id', 'tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:613ce2e2-ea78-8f6d-0106-0a6efe3f2b61', 'modifiedby', 'modified clients', 'id', 'tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:e0e962f7-4309-b2cf-7532-a8b3090d7195', 'createdby', 'created sales orders', 'id', 'tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:7718de55-eb15-15e8-51bf-b6800a338189', 'modifiedby', 'modified invoices', 'id', 'tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:c497dcac-8b5b-8307-9d3c-5e321d4fd096', 'invoiceid', 'line items', 'id', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c', 1, NOW(), 1, NOW(), '1');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:636d0e78-2118-6ff6-467e-753f81504d2b', 'discountid', 'sales orders', 'uuid', 'tbld:455b8839-162b-3fcb-64b6-eeb946f873e1', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:59b07233-576d-0130-0694-5191de94fc87', 'uuid', 'discounts', 'discountid', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'tbld:455b8839-162b-3fcb-64b6-eeb946f873e1', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:06ff1e31-3b66-ccea-daf0-ab5bfb854ec5', 'uuid', 'clients', 'clientid', 'tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:16245f6f-c65e-5226-068a-2be01cf9e822', 'clientid', 'AR items', 'uuid', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 'tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c', 1, NOW(), 1, NOW(), '1');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:24a148ec-214c-4373-8205-49c61eea00cb', 'uuid', 'invoices', 'relatedid', 'tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:4854cafd-b826-156b-a7f2-ca3f42939e39', 'relatedid', 'AR items', 'uuid', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:887a9cf2-1207-27fb-d19e-d6aca96dccae', 'uuid', 'clients', 'clientid', 'tbld:43678406-be25-909b-c715-7e2afc7db601', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:cd96f32f-9285-50ea-a919-331b23453ed1', 'clientid', 'receipts', 'uuid', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 'tbld:43678406-be25-909b-c715-7e2afc7db601', 1, NOW(), 1, NOW(), '1');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:bbef27de-ade8-6f22-e87d-481614b0ecca', 'uuid', 'product categories', 'categoryid', 'tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', 'tbld:3342a3d4-c6a2-3a38-6576-419299859561', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:3d7d26c2-4d15-cbbf-ea78-0e1f342d62e5', 'shippingmethodid', 'sales orders', 'uuid', 'tbld:fa8a0ddc-87d3-a9e9-60b0-1bab374b2993', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:ca8384d2-cee1-35be-b9af-5e87bca450fc', 'paymentmethodid', 'sales orders', 'uuid', 'tbld:380d4efa-a825-f377-6fa1-a030b8c58ffe', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:cdbd96f9-7445-30d4-efc6-267b8ecb2dfa', 'paymentmethodid', 'receipts', 'uuid', 'tbld:380d4efa-a825-f377-6fa1-a030b8c58ffe', 'tbld:43678406-be25-909b-c715-7e2afc7db601', 1, NOW(), 1, NOW(), '1');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:caf9517c-4d66-8bd8-9ccb-b510be80fef9', 'uuid', 'receipts', 'relatedid', 'tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c', 'tbld:43678406-be25-909b-c715-7e2afc7db601', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:ab32b9a1-42ec-99ae-3e0f-492e199ec94b', 'assignedtoid', 'assigned to sales orders', 'uuid', 'tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:7b79b739-9cfb-8401-1d50-279c4f1e6219', 'id', 'posting sessions', 'postingsessionid', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:fede3e71-c563-8a70-e818-7b517b123883', 'id', 'posting sessions', 'postingsessionid', 'tbld:43678406-be25-909b-c715-7e2afc7db601', 'tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:b25f7c4f-678e-b2e7-21c0-9c28fa034df7', 'postingsessionid', 'sales orders', 'id', 'tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 1, NOW(), 1, NOW(), '0');
INSERT INTO `relationships` (`uuid`, `tofield`, `name`, `fromfield`, `fromtableid`, `totableid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`, `inherint`) VALUES ('rln:c003c109-b79b-eded-9acd-b251af8d92ec', 'postingsessionid', 'receipts', 'id', 'tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'tbld:43678406-be25-909b-c715-7e2afc7db601', 1, NOW(), 1, NOW(), '0');
--end relationships INSERT--
--reports DELETE/INSERT--
DELETE FROM `reports` WHERE `name` IN ('Receipt', 'Totals - Tax', 'report', 'Client Notes Summary', 'Totals - Lead Source', 'Totals - Product', 'Totals - Product Categories', 'Totals - Custom', 'Purchase History', 'Sales History', 'Quote', 'Totals - Grouped by Invoice Lead Source', 'Totals - Grouped by Payment Method', 'Totals - Grouped by Shipping Method', 'Totals - Grouped by Acct. Manager', 'Totals - Amt. w/ Invoices + Line Items', 'Totals - Amt. w/  Invoices', 'Labels - Shipping', 'Labels - Mailing', 'Labels - Folder', 'Packing List', 'Work Order', 'Invoice');
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:e3ef15d4-1bf5-36a1-cc05-ee44025ad619', 'Totals - Custom', 'report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/invoices_totals.php', 'Creater your own custom invoice totaling report, specify groupings, totals, averages and whether to display summary, invoice, and invoice detail information.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:a278af28-9c34-da2e-d81b-4caa36dfa29f', 'Sales History', 'report', 'tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', '100', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/products_saleshistory.php', 'Sales History for product including costs, average price and quantities.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:1908b03c-cacc-f03a-6d22-21fdef123f65', 'Purchase History', 'report', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', '10', '', 'modules/bms/report/clients_purchasehistory.php', 'Client purchase history', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:858702da-1b85-3a62-c20f-6b1593140a64', 'Totals - Custom', 'report', 'tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/lineitems_totals.php', 'Creat your own custom line item  totaling report, specify groupings, totals, averages and whether to display summary, invoice, and invoice detail information.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:0df82ecf-5f05-56bd-18c3-e7cb27c0cf8a', 'Client Statements', 'PDF Report', 'tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c', '10', 'role:c9439c3c-499b-7bcc-ee14-fec5bfcf5fc2', 'modules/bms/report/aritems_clientstatement.php', 'Client AR statement balances and activity.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:e25bdb7a-93be-b1d6-a292-cdec89c0c9fc', 'Summary', 'report', 'tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c', '10', 'role:c9439c3c-499b-7bcc-ee14-fec5bfcf5fc2', 'modules/bms/report/aritems_summary.php', 'Items grouped and totaled by clients, with grand totals.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:916f34d8-0997-162c-4350-d93c3d283241', 'Payment Type Totals', 'report', 'tbld:43678406-be25-909b-c715-7e2afc7db601', '10', 'role:c9439c3c-499b-7bcc-ee14-fec5bfcf5fc2', 'modules/bms/report/receipts_pttotals.php', 'Totals grouped by payment method.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:4851c350-4343-4dc3-4b7b-74c287de011b', 'Incoming Cash Flow', 'report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '55', 'role:8f5fb368-e7d9-5010-d8f6-b4a78adc0520', 'modules/bms/report/incoming_cashflow.php', 'This report shows total incoming monies for a time period from both posted sales orders AND posted receipts. It can be grouped by week, month, quarter and year.\r\n\r\nThis report runs is unaffected by selected records, search or sort parameters.  It requires input of its own start and end dates.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2', 'Labels - Folder', 'PDF Report', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', '50', NULL, 'report/general_labels.php', 'Avery 5160 or compatible (3x10) Instructor Folder labels. \r\n\r\n **MAKE SURE when printing the pdf file, to TURN OFF the option \"shrink oversized pages to paper size\".**', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840', 'Labels - Mailing', 'PDF Report', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', '50', NULL, 'report/general_labels.php', 'Avery 5160 or compatible (3x10) Instructor Folder labels. \r\n\r\n **MAKE SURE when printing the pdf file, to TURN OFF the option \"shrink oversized pages to paper size\".**', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a', 'Labels - Shipping', 'PDF Report', 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', '50', NULL, 'report/general_labels.php', 'Avery 5160 or compatible (3x10) Instructor Folder labels. \r\n\r\n **MAKE SURE when printing the pdf file, to TURN OFF the option \"shrink oversized pages to paper size\".**', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f', 'Labels - Shipping', 'PDF Report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '60', 'role:de7e6679-8bb2-29ee-4883-2fcd756fb120', 'report/general_labels.php', 'Avery 5160 or compatible (3x10) Instructor Folder labels. \r\n\r\n **MAKE SURE when printing the pdf file, to TURN OFF the option \"shrink oversized pages to paper size\".**', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c', 'Totals - Lead Source', 'report', 'tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/lineitems_totals.php', 'Totals grouped by invoice lead source and product', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:157ec22d-14c2-64cc-486a-fedc9cfe7d39', 'Totals - Products', 'report', 'tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/lineitems_totals.php', 'Totals report grouped by product displaying line items', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c', 'Totals - Product Categories', 'report', 'tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/lineitems_totals.php', 'Totals report grouped first by product category and then by product.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:b934c506-ee84-f260-283b-9cb07050b197', 'Receipt', 'PDF Report', 'tbld:43678406-be25-909b-c715-7e2afc7db601', '10', 'role:c9439c3c-499b-7bcc-ee14-fec5bfcf5fc2', 'modules/bms/report/receipts_pdf.php', 'PDF print out of receipt for processing or client records', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:5ff03ba4-a310-524e-f3a3-05b6cadf820a', 'Totals - Account Manager', 'report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/invoices_totals.php', 'Totals report grouping by client account manager', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:3e25c485-857d-1698-6c9b-03e3e8f4177b', 'Totals - Amount with Invoices', 'report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/invoices_totals.php', 'Basic totals report. Shows invoice total, subtotal and amount due fields and displaying individual invoice information.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:8a9cf2d6-d5f0-54ca-1b89-8207dfa4c348', 'Totals - Amounts with Invoices and Line Items', 'report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/invoices_totals.php', 'Basic totals report. Shows invoice total, subtotal and amount due fields and displaying individual invoice and line item information.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:3cb9ae81-0838-863e-0185-1f46224cbe00', 'Totals - Sales Order Lead Source', 'report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/invoices_totals.php', 'Totals - Grouped by invoice lead source', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:67dca680-14ba-d4e7-4926-b90c65495f48', 'Totals - Pament Methods', 'report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/invoices_totals.php', 'Totals report grouped by payment method.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:5dc66c67-1e98-7f85-6e89-5ab5d7795140', 'Totals - Shipping Methods', 'report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/invoices_totals.php', 'Totals report including shipping amount grouped by shipping method', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:d97334ef-0139-f178-fe36-c38e151cc60e', 'Totals - Tax', 'report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '50', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'modules/bms/report/invoices_totals.php', 'Sales order tax totals', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b', 'Invoice', 'PDF Report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '100', NULL, 'modules/bms/report/invoices_pdfinvoice.php', 'Printable/E-Mail Ready Invoice PDF', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73', 'Packing List', 'PDF Report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '100', NULL, 'modules/bms/report/invoices_pdfpackinglist.php', 'Packing (pick) List PDF', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca', 'Quote', 'PDF Report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '100', NULL, 'modules/bms/report/invoices_pdfquote.php', 'Quote PDF.  Does not include amount due.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37', 'Work Order', 'PDF Report', 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', '100', 'role:de7e6679-8bb2-29ee-4883-2fcd756fb120', 'modules/bms/report/invoices_pdfworkorder.php', 'Work Order PDF', 1, NOW(), 1, NOW());
--end reports DELETE/INSERT--
--reportsettings INSERT--
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','maxRows','10','int',1,'10','Number of label rows per page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','maxColumns','3','int',1,'3','Number of label columns per page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','startTop','0.5','real',1,'0.5','Top Margin of page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','startLeft','0.1875','real',1,'0.1875','Left Margin of page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','columnMargin','0.125','real',1,'0.125','Distance between columns');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','labelHeight','1','real',1,'1','Height of a single label');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','labelHeight','2.625','real',1,'2.625','Width of a single label');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','labelMarginLeft','0.0625','real',1,'0.0625','Distance from left between the start of an individual to the text being put on it');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','labelMarginTop','0.125','real',1,'0.125','Distance from top between start of an individual to the text being put on it');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','queryStatement','SELECT if(clients.lastname != \'\' || clients.lastname is not null, concat(clients.lastname, \', \', clients.firstname), \'\') as rowText1, clients.company as rowText2, concat(addresses.city, \', \', addresses.state) as rowText3 FROM ((clients INNER JOIN addresstorecord on clients.uuid = addresstorecord.recordid AND addresstorecord.tabledefid=\'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083\' AND addresstorecord.primary=1) INNER JOIN addresses ON  addresstorecord.addressid = addresses.uuid)','text',1,'SELECT \"no data in first row\" AS `rowText1`,  \"no data in second row\" AS `rowText2` FROM `reports`','SQL SELECT and FROM clauses defining the data to be retrieved.  Each line printed should be selected as a column in the format `rowText(X)`, where (X) is the line number it will be printed on');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','rowText1Font','Arial,B,9','string',0,'Arial,B,9','Comma separated list of FPF parameters defining font settings for the label text.  You can change the font on subsequent lines by adding an additional setting rowText(x)Font where (x) is the line number the font change occurs.');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:e3057c38-0f68-5d1d-a1b7-793183d951d2','rowText3Font','Arial,,8','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','maxRows','10','int',1,'10','Number of label rows per page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','maxColumns','3','int',1,'3','Number of label columns per page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','startTop','0.5','real',1,'0.5','Top Margin of page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','startLeft','0.1875','real',1,'0.1875','Left Margin of page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','columnMargin','0.125','real',1,'0.125','Distance between columns');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','labelHeight','1','real',1,'1','Height of a single label');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','labelHeight','2.625','real',1,'2.625','Width of a single label');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','labelMarginLeft','0.0625','real',1,'0.0625','Distance from left between the start of an individual to the text being put on it');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','labelMarginTop','0.125','real',1,'0.125','Distance from top between start of an individual to the text being put on it');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','queryStatement','SELECT clients.company AS rowText1, trim(concat(clients.firstname, \' \', clients.lastname)) AS rowText2, addresses.address1 AS rowText3, addresses.address2 AS rowText4, concat(addresses.city, \', \', addresses.state, \' \', addresses.postalcode) AS rowText5, addresses.country AS rowText6 FROM ((clients INNER JOIN addresstorecord on clients.uuid = addresstorecord.recordid AND addresstorecord.tabledefid=\'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083\' AND addresstorecord.primary=1) INNER JOIN addresses ON  addresstorecord.addressid = addresses.uuid)','text',1,'SELECT \"no data in first row\" AS `rowText1`,  \"no data in second row\" AS `rowText2` FROM `reports`','SQL SELECT and FROM clauses defining the data to be retrieved.  Each line printed should be selected as a column in the format `rowText(X)`, where (X) is the line number it will be printed on');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','rowText1Font','Arial,B,9','string',0,'Arial,B,9','Comma separated list of FPDF font parameters defining font settings for the label text.  You can change the font on subsequent lines by adding an additional setting rowText(x)Font where (x) is the line number the font change occurs.');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:1019eae2-50d4-e097-c73c-3748c2bd7840','rowText3Font','Arial,,8','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','maxRows','10','int',1,'10','Number of label rows per page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','maxColumns','3','int',1,'3','Number of label columns per page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','startTop','0.5','real',1,'0.5','Top Margin of page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','startLeft','0.1875','real',1,'0.1875','Left Margin of page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','columnMargin','0.125','real',1,'0.125','Distance between columns');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','labelHeight','1','real',1,'1','Height of a single label');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','labelHeight','2.625','real',1,'2.625','Width of a single label');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','labelMarginLeft','0.0625','real',1,'0.0625','Distance from left between the start of an individual to the text being put on it');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','labelMarginTop','0.125','real',1,'0.125','Distance from top between start of an individual to the text being put on it');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','queryStatement','SELECT clients.company AS rowText1, if(shiptoname != \'\' && shiptoname is not null, trim(shiptoname), trim(concat(clients.firstname, \' \', clients.lastname))) AS rowText2, addresses.address1 AS rowText3, addresses.address2 AS rowText4, concat(addresses.city, \', \', addresses.state, \' \', addresses.postalcode) AS rowText5, addresses.country AS rowText6 FROM ((clients INNER JOIN addresstorecord on clients.uuid = addresstorecord.recordid AND addresstorecord.tabledefid=\'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083\' AND addresstorecord.defaultshipto=1) INNER JOIN addresses ON  addresstorecord.addressid = addresses.uuid)','text',1,'SELECT \"no data in first row\" AS `rowText1`,  \"no data in second row\" AS `rowText2` FROM `reports`','SQL SELECT and FROM clauses defining the data to be retrieved.  Each line printed should be selected as a column in the format `rowText(X)`, where (X) is the line number it will be printed on');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','rowText1Font','Arial,B,9','string',0,'Arial,B,9','Comma separated list of FPDF font parameters defining font settings for the label text.  You can change the font on subsequent lines by adding an additional setting rowText(x)Font where (x) is the line number the font change occurs.');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:805fe2c4-a0d2-185a-5609-a444da07d61a','rowText3Font','Arial,,8','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','maxRows','10','int',1,'10','Number of label rows per page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','maxColumns','3','int',1,'3','Number of label columns per page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','startTop','0.5','real',1,'0.5','Top Margin of page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','startLeft','0.1875','real',1,'0.1875','Left Margin of page');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','columnMargin','0.125','real',1,'0.125','Distance between columns');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','labelHeight','1','real',1,'1','Height of a single label');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','labelHeight','2.625','real',1,'2.625','Width of a single label');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','labelMarginLeft','0.0625','real',1,'0.0625','Distance from left between the start of an individual to the text being put on it');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','labelMarginTop','0.125','real',1,'0.125','Distance from top between start of an individual to the text being put on it');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','queryStatement','SELECT clients.company AS rowText1, if(shiptosameasbilling = 0 && shiptoname != \'\' && shiptoname IS NOT NULL, shiptoname, trim(concat(clients.firstname, \' \', clients.lastname))) AS rowText2, if(shiptosameasbilling = 0, shiptoaddress1, invoices.address1) AS rowText3, if(shiptosameasbilling = 0, shiptoaddress2, invoices.address2) AS rowText4, if(shiptosameasbilling = 0, concat(shiptocity, \', \',shiptostate,\' \',shiptopostalcode), concat(invoices.city,\', \',invoices.state,\' \',invoices.postalcode)) AS rowText5, if(shiptosameasbilling = 0, shiptocountry, invoices.country) AS rowText6 FROM invoices INNER JOIN clients ON invoices.clientid=clients.uuid','text',1,'SELECT \"no data in first row\" AS `rowText1`,  \"no data in second row\" AS `rowText2` FROM `reports`','SQL SELECT and FROM clauses defining the data to be retrieved.  Each line printed should be selected as a column in the format `rowText(X)`, where (X) is the line number it will be printed on');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','rowText1Font','Arial,B,9','string',0,'Arial,B,9','Comma separated list of FPDF font parameters defining font settings for the label text.  You can change the font on subsequent lines by adding an additional setting rowText(x)Font where (x) is the line number the font change occurs.');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:7d461e1a-1bf3-b484-6b58-bf99126ad95f','rowText3Font','Arial,,8','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c','reportTitle','Totals Grouped by Invoice Lead Source and Product','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c','column1','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c','column2','4','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c','column4','8','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c','column5','5','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c','group1','14','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c','group2','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c','showWhat','totals','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:435d3c34-0ca3-5d40-3488-e5593869c20c','column3','9','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:157ec22d-14c2-64cc-486a-fedc9cfe7d39','reportTitle','Totals Grouped by Product','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:157ec22d-14c2-64cc-486a-fedc9cfe7d39','column1','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:157ec22d-14c2-64cc-486a-fedc9cfe7d39','column2','4','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:157ec22d-14c2-64cc-486a-fedc9cfe7d39','column3','9','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:157ec22d-14c2-64cc-486a-fedc9cfe7d39','column4','8','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:157ec22d-14c2-64cc-486a-fedc9cfe7d39','column5','5','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:157ec22d-14c2-64cc-486a-fedc9cfe7d39','group1','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:157ec22d-14c2-64cc-486a-fedc9cfe7d39','showWhat','lineitems','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c','reportTitle','Totals Grouped by Product Categories and Product','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c','column1','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c','column2','4','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c','column3','9','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c','column4','8','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c','column5','5','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c','group1','2','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c','group2','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b2402d8d-10e3-30b2-2c92-e6da9bd0093c','showWhat','totals','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b934c506-ee84-f260-283b-9cb07050b197','reportTitle','Receipt','string',1,'Receipts','Title printed in upper right hand of report.');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5ff03ba4-a310-524e-f3a3-05b6cadf820a','reportTitle','Totals Grouped by Client Account Manager','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5ff03ba4-a310-524e-f3a3-05b6cadf820a','showWhat','invoices','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5ff03ba4-a310-524e-f3a3-05b6cadf820a','column1','11','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5ff03ba4-a310-524e-f3a3-05b6cadf820a','column2','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5ff03ba4-a310-524e-f3a3-05b6cadf820a','column3','3','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5ff03ba4-a310-524e-f3a3-05b6cadf820a','group1','13','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5ff03ba4-a310-524e-f3a3-05b6cadf820a','group2','12','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3e25c485-857d-1698-6c9b-03e3e8f4177b','reportTitle','Subtotals, Totals, and Amount Due','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3e25c485-857d-1698-6c9b-03e3e8f4177b','showWhat','invoices','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3e25c485-857d-1698-6c9b-03e3e8f4177b','column1','11','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3e25c485-857d-1698-6c9b-03e3e8f4177b','column2','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3e25c485-857d-1698-6c9b-03e3e8f4177b','column3','3','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:8a9cf2d6-d5f0-54ca-1b89-8207dfa4c348','reportTitle','Subtotals, Totals, and Amount Due','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:8a9cf2d6-d5f0-54ca-1b89-8207dfa4c348','showWhat','lineitems','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:8a9cf2d6-d5f0-54ca-1b89-8207dfa4c348','column1','11','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:8a9cf2d6-d5f0-54ca-1b89-8207dfa4c348','column2','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:8a9cf2d6-d5f0-54ca-1b89-8207dfa4c348','column3','3','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3cb9ae81-0838-863e-0185-1f46224cbe00','reportTitle','Totals Grouped by Sales Order Lead Source','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3cb9ae81-0838-863e-0185-1f46224cbe00','showWhat','invoices','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3cb9ae81-0838-863e-0185-1f46224cbe00','column1','11','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3cb9ae81-0838-863e-0185-1f46224cbe00','column2','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3cb9ae81-0838-863e-0185-1f46224cbe00','column3','3','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:3cb9ae81-0838-863e-0185-1f46224cbe00','group1','15','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:67dca680-14ba-d4e7-4926-b90c65495f48','reportTitle','Totals Grouped by Payment Method','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:67dca680-14ba-d4e7-4926-b90c65495f48','showWhat','invoices','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:67dca680-14ba-d4e7-4926-b90c65495f48','column1','11','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:67dca680-14ba-d4e7-4926-b90c65495f48','column2','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:67dca680-14ba-d4e7-4926-b90c65495f48','column3','3','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:67dca680-14ba-d4e7-4926-b90c65495f48','group1','16','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5dc66c67-1e98-7f85-6e89-5ab5d7795140','reportTitle','Totals Grouped by Shipping Method','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5dc66c67-1e98-7f85-6e89-5ab5d7795140','showWhat','invoices','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5dc66c67-1e98-7f85-6e89-5ab5d7795140','column1','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5dc66c67-1e98-7f85-6e89-5ab5d7795140','coumn2','7','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:5dc66c67-1e98-7f85-6e89-5ab5d7795140','group1','17','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:d97334ef-0139-f178-fe36-c38e151cc60e','reportTitle','Tax Totals','string',0,'Report','Report Title');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:d97334ef-0139-f178-fe36-c38e151cc60e','showWhat','invoices','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:d97334ef-0139-f178-fe36-c38e151cc60e','column1','11','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:d97334ef-0139-f178-fe36-c38e151cc60e','column2','1','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:d97334ef-0139-f178-fe36-c38e151cc60e','column3','5','string',0,'',NULL);
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b','reportTitle','Invoice','string',1,'Invoice','Title printed on reports');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b','printLogo','1','bool',1,'1','Should the logo print (1 = yes, 0 = no)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b','printCompanyInfo','1','bool',1,'1','Should the top company information print (1 = yes, 0 = no)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b','leftTopBox','billto','string',1,'billto','Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b','leftTopBoxTitle','SOLD TO','string',1,'SOLD TO','Title of Left Top Header Box');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b','rightTopBox','shipto','string',1,'shipto','Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b','rightTopBoxTitle','SHIP TO','string',1,'SHIP TO','Title of Right Top Header Box');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b','templateFormatting','0','bool',1,'0','Should PDF remove lines and dark titles (1 = remove, 0 = keep)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:44b21461-6e67-c284-0ccf-36ab1af47c9b','templateUUID','','string',0,'','Optional UUID of file record for PDF to be used as background template');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73','reportTitle','Packing List','string',1,'Packing List','Title printed on reports');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73','printLogo','1','bool',1,'1','Should the logo print (1 = yes, 0 = no)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73','printCompanyInfo','1','bool',1,'1','Should the top company information print (1 = yes, 0 = no)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73','leftTopBox','billto','string',1,'billto','Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73','leftTopBoxTitle','SOLD TO','string',1,'SOLD TO','Title of Left Top Header Box');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73','rightTopBox','shipto','string',1,'shipto','Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73','rightTopBoxTitle','SHIP TO','string',1,'SHIP TO','Title of Right Top Header Box');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73','templateFormatting','0','bool',1,'0','Should PDF remove lines and dark titles (1 = remove, 0 = keep)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:6bdef1ac-2f74-4d20-411a-3e48531dae73','templateUUID','','string',0,'','Optional UUID of file record for PDF to be used as background template');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca','reportTitle','Quote','string',1,'Quote','Title printed on reports');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca','printLogo','1','bool',1,'1','Should the logo print (1 = yes, 0 = no)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca','printCompanyInfo','1','bool',1,'1','Should the top company information print (1 = yes, 0 = no)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca','leftTopBox','billto','string',1,'billto','Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca','leftTopBoxTitle','SOLD TO','string',1,'SOLD TO','Title of Left Top Header Box');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca','rightTopBox','shipto','string',1,'shipto','Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca','rightTopBoxTitle','SHIP TO','string',1,'SHIP TO','Title of Right Top Header Box');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca','templateFormatting','0','bool',1,'0','Should PDF remove lines and dark titles (1 = remove, 0 = keep)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:b683e2f0-e52b-4dd4-33e1-7566616893ca','templateUUID','','string',0,'','Optional UUID of file record for PDF to be used as background template');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37','reportTitle','Work Order','string',1,'Work Order','Title printed on reports');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37','printLogo','1','bool',1,'1','Should the logo print (1 = yes, 0 = no)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37','printCompanyInfo','1','bool',1,'1','Should the top company information print (1 = yes, 0 = no)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37','leftTopBox','billto','string',1,'billto','Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37','leftTopBoxTitle','SOLD TO','string',1,'SOLD TO','Title of Left Top Header Box');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37','rightTopBox','shipto','string',1,'shipto','Contents of Right Top Header Box (can be `billto`, `shipto`, `invoiceinfo`, `companyinfo`, `nowshow` or `blank`)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37','rightTopBoxTitle','SHIP TO','string',1,'SHIP TO','Title of Right Top Header Box');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37','templateFormatting','0','bool',1,'0','Should PDF remove lines and dark titles (1 = remove, 0 = keep)');
INSERT INTO `reportsettings` (reportuuid, name, value, type, required, defaultvalue, description)  VALUES ('rpt:30a56a65-8673-b3c0-cd50-6546968b4d37','templateUUID','','string',0,'','Optional UUID of file record for PDF to be used as background template');
--end reportsettings INSERT--
--role UPDATE--
UPDATE `roles` SET `uuid`='role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52' WHERE `id`='10';
UPDATE `roles` SET `uuid`='role:de7e6679-8bb2-29ee-4883-2fcd756fb120' WHERE `id`='20';
UPDATE `roles` SET `uuid`='role:259ead9f-100b-55b5-508a-27e33a6216bf' WHERE `id`='30';
UPDATE `roles` SET `uuid`='role:8f5fb368-e7d9-5010-d8f6-b4a78adc0520' WHERE `id`='50';
UPDATE `roles` SET `uuid`='role:c9439c3c-499b-7bcc-ee14-fec5bfcf5fc2' WHERE `id`='80';
--end role UPDATE--
--settings DELETE--
DELETE FROM `settings` WHERE `name`='clear_payment_on_invoice';
--end settings DELTE--
--settings INSERT--
INSERT INTO `settings` (`name`, `value`) VALUES ('company_taxid', '');
INSERT INTO `settings` (`name`, `value`) VALUES ('encrypt_payment_fields', '0');
INSERT INTO `settings` (`name`, `value`) VALUES ('encryption_key_path', '');
--end settings INSERT--
--shippingmethods UPDATE--
UPDATE `shippingmethods` SET `uuid`='shp:f34a3e10-e782-2675-f041-71f5c88f5aa9' WHERE `name`='FedEx Priority Overnight AM';
UPDATE `shippingmethods` SET `uuid`='shp:e2e43816-667a-fdf3-6bec-4456bcf8bef0' WHERE `name`='FedEx Standard Overnight';
UPDATE `shippingmethods` SET `uuid`='shp:0f07f7fd-0352-8df7-8294-a57e5e375808' WHERE `name`='UPS 2nd Day Air';
UPDATE `shippingmethods` SET `uuid`='shp:6ef11711-7335-3e90-cf27-df5ea23c1480' WHERE `name`='UPS 3 Day Select';
UPDATE `shippingmethods` SET `uuid`='shp:1a0c53bd-6754-7d9f-4bea-bad57628187a' WHERE `name`='UPS Ground (Com)';
UPDATE `shippingmethods` SET `uuid`='shp:9e0bad1f-0545-6b09-3900-4e5943629037' WHERE `name`='UPS Ground (Res)';
UPDATE `shippingmethods` SET `uuid`='shp:ba131229-fb3d-d328-91c8-323480831b03' WHERE `name`='UPS Next Day Air';
--end shippingmethods UPDATE--
--smartsearches INSERT--
INSERT INTO `smartsearches` (`uuid`, `name`, `fromclause`, `valuefield`, `displayfield`, `secondaryfield`, `classfield`, `searchfields`, `filterclause`, `rolefield`, `tabledefid`, `moduleid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('smrt:eed8c810-f9c8-077b-4b3e-9aef451f8057', 'Pick Product Category For Product', 'productcategories', 'productcategories.uuid', 'productcategories.name', '\'\'', '\'\'', 'productcategories.name', 'productcategories.inactive = 0', '\'\'', 'tbld:3342a3d4-c6a2-3a38-6576-419299859561', 'mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc', 1, NOW(), 1, NOW());
--end smartsearches INSERT--
--smartsearches UPDATE--
UPDATE `smartsearches` SET `uuid`='smrt:5cf171f7-2284-1492-62bb-872bc222eaef' WHERE `id`='1';

UPDATE
    `smartsearches`
SET
    `uuid`='smrt:1b16f1e8-edf2-332e-e61b-3759f7020d41',
    `valuefield`='`clients`.`uuid`',
    `fromclause` = '((`clients` INNER JOIN `addresstorecord` ON `clients`.`uuid` = `addresstorecord`.`recordid` AND `addresstorecord`.`tabledefid`=\'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083\' AND addresstorecord.primary=\'1\') INNER JOIN `addresses` ON  `addresstorecord`.`addressid` = `addresses`.`uuid`)'
WHERE
    `id`='3';

UPDATE
    `smartsearches`
SET
    `uuid`='smrt:5634f7fb-a0c8-7e10-4c96-8bb043e7f478',
    `valuefield` = '`products`.`uuid`'
WHERE
    `id`='4';

UPDATE `smartsearches` SET
    `uuid`='smrt:32f76377-1822-17f5-674c-118b678378d4',
    `valuefield` = 'clients.uuid',
    `fromclause` = '((`clients` INNER JOIN `addresstorecord` ON `clients`.`uuid` = `addresstorecord`.`recordid` AND `addresstorecord`.`tabledefid`=\'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083\' AND `addresstorecord`.`primary`=\'1\') INNER JOIN `addresses` ON  `addresstorecord`.`addressid` = `addresses`.`uuid`)'
WHERE
    `id`='6';

UPDATE
    `smartsearches`
SET
    `uuid`='smrt:a18ca9d4-58aa-7a47-faa7-1ad0ed5ba8c6',
    `valuefield`='`clients`.`uuid`'
WHERE
    `id`='7';

UPDATE
    `smartsearches`
SET
    `uuid`='smrt:3b48afbf-f18f-a18d-8aa8-f51f27008750',
    `valuefield`='`clients`.`uuid`'
WHERE
    `id`='8';
--end smartsearches UPDATE--
--tablecolumns INSERT--
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'id', 'postingsessions.id', 'left', '', '0', '', '0', '', NULL, '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'date', 'postingsessions.sessiondate', 'left', '', '1', '', '0', '', 'datetime', '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'source', 'postingsessions.source', 'left', '', '2', '', '0', '', NULL, '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'user', 'concat(users.firstname,\" \",users.lastname) ', 'left', '', '3', '', '1', '100%', NULL, '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'records', 'postingsessions.recordsposted', 'right', '', '4', '', '0', '', NULL, '');
DELETE FROM `tablecolumns` WHERE `tabledefid` = 7;
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561', 'name', 'if(productcategories.description, concat(\'[b]\', productcategories.name,\'[/b][br]\',productcategories.description), concat(\'[b]\', productcategories.name,\'[/b]\'))', 'left', '', '1', 'productcategories.name', '0', '100%', 'bbcode', '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561', 'parent category', 'if(parents.uuid, parents.name, \'No Parent\')', 'left', '', '2', '', '0', '', NULL, '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561', 'display order', 'productcategories.displayorder', 'right', '', '3', '', '0', '', NULL, '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561', 'web', 'productcategories.webenabled', 'center', '', '0', '', '0', '', 'boolean', '');
--end tablecolumns INSERT--
--tablecolumns UPDATE--
UPDATE `tablecolumns` SET `column`='IF(receipts.paymentmethodid = -1,concat( concat("Other... (", receipts.paymentother), ")"), paymentmethods.name)' WHERE `tabledefid` = '304' AND `name` = 'payment';
--end tablecolumns UPDATE--
--tabledefs INSERT--
INSERT INTO `tabledefs` (`uuid`, `displayname`, `prefix`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'Posting Sessions', NULL, 'table', 'mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc', 'postingsessions', '(postingsessions INNER JOIN users ON postingsessions.userid = users.id)', 'N/A', '', 'N/A', '', NULL, 'admin', 'role:8f5fb368-e7d9-5010-d8f6-b4a78adc0520', 'Admin', 'Admin', 'NA', '0', '0', 'YEAR(postingsessions.sessiondate) = YEAR(NOW()) AND MONTH(postingsessions.sessiondate) = MONTH(NOW())', 'postingsessions.sessiondate DESC', NULL, NULL, NULL, 1, NOW(), 1, NOW());
--end tabledefs INSERT--
--tabledefs UPDATE--
UPDATE `tabledefs` SET
    `uuid`='tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083',
    `importfile` = 'modules/bms/clients_import.php',
    `querytable` = '((clients INNER JOIN addresstorecord on clients.uuid = addresstorecord.recordid AND addresstorecord.tabledefid=\'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083\' AND addresstorecord.primary=1) INNER JOIN addresses ON  addresstorecord.addressid = addresses.uuid)',
    `hascustomfields` = '1',
    `prefix` = 'cli'
WHERE
    `id`='2';
UPDATE `tabledefs` SET
    `uuid`='tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883',
    `canpost`= '1',
    `hascustomfields` = '1',
    `prefix` = 'sord',
    `querytable` = '(((`invoices` INNER JOIN `clients` ON `invoices`.`clientid`=`clients`.`uuid`) INNER JOIN `invoicestatuses` ON `invoices`.`statusid`=`invoicestatuses`.`uuid`) LEFT JOIN `paymentmethods` ON `invoices`.`paymentmethodid` = `paymentmethods`.`uuid`)'
WHERE
    `id`='3';
UPDATE `tabledefs` SET
    `uuid`='tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34',
    `hascustomfields` = '1',
    `querytable`='(products left join productcategories on products.categoryid=productcategories.uuid)',
    `prefix` = 'prod'
WHERE
    `id`='4';
UPDATE `tabledefs` SET
    `uuid`='tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c',
    `hascustomfields` = '1',
    `querytable` = '((lineitems left join products on lineitems.productid=products.uuid) inner join invoices on lineitems.invoiceid=invoices.id)'
WHERE
    `id`='5';
UPDATE `tabledefs` SET
    `uuid`='tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e',
    `deletebutton` = 'inactivate',
    `hascustomfields` = '1',
    `prefix` = 'tax'
WHERE
    `id`='6';
UPDATE `tabledefs` SET
    `uuid` = 'tbld:3342a3d4-c6a2-3a38-6576-419299859561',
    `querytable` = '(productcategories LEFT JOIN productcategories AS `parents` ON productcategories.parentid = parents.uuid)',
    `prefix` = 'pcat'
WHERE
    `id`='7';
UPDATE `tabledefs` SET
    `uuid`='tbld:8179e105-5487-5173-d835-d9d510cc7f1b',
    `querytable` = '(prerequisites LEFT JOIN `products` ON prerequisites.parentid=products.uuid) LEFT JOiN products AS childproducts on prerequisites.childid=childproducts.uuid'
WHERE
    `id`='8';
UPDATE `tabledefs` SET
    `uuid`='tbld:f993cf23-ad4a-047b-e920-d45fee1dc08e',
    `querytable` = '((clients INNER JOIN addresstorecord on clients.uuid = addresstorecord.recordid AND addresstorecord.tabledefid=\'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083\' AND addresstorecord.primary=1) INNER JOIN addresses ON addresstorecord.addressid = addresses.uuid),((clients AS dclients INNER JOIN addresstorecord as daddresstorecord on dclients.uuid = daddresstorecord.recordid AND daddresstorecord.tabledefid=\'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083\' AND daddresstorecord.primary=1) INNER JOIN addresses AS daddresses ON daddresstorecord.addressid = daddresses.uuid)'
WHERE
    `id`='18';
UPDATE `tabledefs` SET
    `uuid`='tbld:157b7707-5503-4161-4dcf-6811f8b0322f',
    `querytable` = '`clientemailprojects` LEFT JOIN `users` ON `clientemailprojects`.`userid`=`users`.`uuid`'
WHERE
    `id`='22';
UPDATE `tabledefs` SET
    `uuid`='tbld:455b8839-162b-3fcb-64b6-eeb946f873e1',
    `defaultcriteriafindoptions` = 'Active Records',
    `defaultcriteriaselection` = 'name',
    `hascustomfields` = '1',
    `prefix` = 'dsct'
WHERE
    `id`='25';
UPDATE `tabledefs` SET
    `uuid`='tbld:fa8a0ddc-87d3-a9e9-60b0-1bab374b2993',
    `hascustomfields` = '1',
    `prefix` = 'smtd'
WHERE
    `id`='300';
UPDATE `tabledefs` SET
    `uuid`='tbld:380d4efa-a825-f377-6fa1-a030b8c58ffe',
    `hascustomfields` = '1',
    `prefix` = 'paym'
WHERE
    `id`='301';
UPDATE `tabledefs` SET
    `uuid`='tbld:d6e4e1fb-4bfa-cb53-ab9c-1b3e7f907ae2',
    `hascustomfields` = '1',
    `prefix` = 'inst'
WHERE
    `id`='302';
UPDATE `tabledefs` SET
    `uuid`='tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c',
    `prefix` = 'arit',
    `querytable` = '(`aritems` INNER JOIN `clients` ON `aritems`.`clientid` = `clients`.`uuid`)'
WHERE
    `id`='303';
UPDATE `tabledefs` SET
    `uuid`='tbld:43678406-be25-909b-c715-7e2afc7db601',
    `canpost` = '1',
    `hascustomfields` = '1',
    `prefix` = 'rcpt',
    `querytable` = '((`receipts` INNER JOIN `clients` ON `receipts`.`clientid` = `clients`.`uuid`) LEFT JOIN `paymentmethods` ON `receipts`.`paymentmethodid` = `paymentmethods`.`uuid`)'
WHERE
    `id`='304';
UPDATE `tabledefs` SET
    `uuid`='tbld:e3ce122f-7c43-cfca-fd32-11c663567a2a',
    `querytable`='((addresstorecord INNER JOIN addresses ON addresstorecord.addressid = addresses.uuid) INNER JOIN clients ON addresstorecord.recordid = clients.uuid)'
WHERE
    `id`='305';
UPDATE `tabledefs` SET
    `uuid`='tbld:27b99bda-7bec-b152-8397-a3b09c74cb23',
    `hascustomfields` = '1',
    `prefix` = 'addr',
    `querytable` = '((`addresstorecord` INNER JOIN `addresses` ON `addresstorecord`.`addressid` = `addresses`.`uuid`) INNER JOIN `clients` ON `addresstorecord`.`recordid` = `clients`.`uuid`)'
WHERE
    `id`='306';
--end tabledefs UPDATE--
--tablefindoptions INSERT--
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:455b8839-162b-3fcb-64b6-eeb946f873e1', 'Active Records', 'discounts.inactive=0', '0', '');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'All Records', 'postingsessions.id!=-1', '2', '');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'This Month\'s Sessions', 'YEAR(postingsessions.sessiondate) = YEAR(NOW()) AND MONTH(postingsessions.sessiondate) = MONTH(NOW())', '0', '');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'Last Month\'s Sessions', 'YEAR(postingsessions.sessiondate) = YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH)) AND MONTH(postingsessions.sessiondate) = MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH))', '1', '');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'Orders - Credit Memo', '`invoices`.`iscreditmemo` != \'0\' AND `invoices`.`type` = \'Order\'', '6', 'role:de7e6679-8bb2-29ee-4883-2fcd756fb120');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'Invoices - Credit Memo', '`invoices`.`iscreditmemo` != \'0\' AND `invoices`.`type` = \'Invoice\' ', '14', 'role:de7e6679-8bb2-29ee-4883-2fcd756fb120');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'All Credit Memos', '`invoices`.`iscreditmemo` != \'0\'', '17', 'role:259ead9f-100b-55b5-508a-27e33a6216bf');
--end tablefindoptions INSERT--
--tablefindoptions UPDATE--
UPDATE `tablefindoptions` SET `displayorder` = 0 WHERE `tabledefid` = 25 AND `name` = 'all records';
UPDATE `tablefindoptions` SET `search`= 'clients.firstname=dclients.firstname AND clients.lastname=dclients.lastname AND addresses.postalcode = daddresses.postalcode AND clients.lastname != \'\' AND clients.firstname != \'\' AND addresses.postalcode != \'\' AND clients.id<>dclients.ID' WHERE `name` = 'match names and postal code' AND `tabledefid` = '18';
UPDATE `tablefindoptions` SET `search`= 'addresses.address1=daddresses.address1 AND clients.id<>dclients.id' WHERE `name` = 'match addresses' AND `tabledefid` = '18';
UPDATE `tablefindoptions` SET `displayorder` = '1' WHERE `tabledefid` = '2' AND `name` = 'Orders';
UPDATE `tablefindoptions` SET `displayorder` = '2' WHERE `tabledefid` = '2' AND `name` = 'Orders - Today';
UPDATE `tablefindoptions` SET `displayorder` = '3' WHERE `tabledefid` = '2' AND `name` = 'Orders/Invoices - Unpaid';
UPDATE `tablefindoptions` SET `displayorder` = '4' WHERE `tabledefid` = '2' AND `name` = 'Orders - Ready To Post';
UPDATE `tablefindoptions` SET `displayorder` = '5' WHERE `tabledefid` = '2' AND `name` = 'Orders - No Payment';
UPDATE `tablefindoptions` SET `displayorder` = '7' WHERE `tabledefid` = '2' AND `name` = 'Invoices';
UPDATE `tablefindoptions` SET `displayorder` = '8' WHERE `tabledefid` = '2' AND `name` = 'Invoices - Today';
UPDATE `tablefindoptions` SET `displayorder` = '9' WHERE `tabledefid` = '2' AND `name` = 'Invoices - Yesterday';
UPDATE `tablefindoptions` SET `displayorder` = '10' WHERE `tabledefid` = '2' AND `name` = 'Invoices - This Week';
UPDATE `tablefindoptions` SET `displayorder` = '11' WHERE `tabledefid` = '2' AND `name` = 'Invoices - Last Week';
UPDATE `tablefindoptions` SET `displayorder` = '12' WHERE `tabledefid` = '2' AND `name` = 'Invoices - This Month';
UPDATE `tablefindoptions` SET `displayorder` = '13' WHERE `tabledefid` = '2' AND `name` = 'Invoices - Last Month';
UPDATE `tablefindoptions` SET `displayorder` = '15' WHERE `tabledefid` = '2' AND `name` = 'Quotes';
UPDATE `tablefindoptions` SET `displayorder` = '16' WHERE `tabledefid` = '2' AND `name` = 'Voided Records';
UPDATE `tablefindoptions` SET `displayorder` = '18' WHERE `tabledefid` = '2' AND `name` = 'All Records';
--end tablefindoptions UPDATE--
--tablegroupings UPDATE--
UPDATE `tablegroupings` SET `name` = 'concat(invoices.type ,\"s\",IF(`invoices`.`iscreditmemo`, \" - Credit Memo\", \"\"))' WHERE `name` = 'concat(invoices.type,"s")';
--end tablegroupings UPDATE--
--tableoptions DELETE--
DELETE FROM `tableoptions WHERE `name`='massEmail';
--end tableoptions DELETe
--tableoptions INSERT--
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:157b7707-5503-4161-4dcf-6811f8b0322f', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:27b99bda-7bec-b152-8397-a3b09c74cb23', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:31423480-a9b0-f0ff-749e-b3b5e18ca93c', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:380d4efa-a825-f377-6fa1-a030b8c58ffe', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:43678406-be25-909b-c715-7e2afc7db601', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:455b8839-162b-3fcb-64b6-eeb946f873e1', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883', 'create_credit_memo', 'create credit memo(s)', '1', '1', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', '60');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:8179e105-5487-5173-d835-d9d510cc7f1b', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:d6e4e1fb-4bfa-cb53-ab9c-1b3e7f907ae2', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:e3ce122f-7c43-cfca-fd32-11c663567a2a', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:f993cf23-ad4a-047b-e920-d45fee1dc08e', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:fa8a0ddc-87d3-a9e9-60b0-1bab374b2993', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'new', '0', '0', '0', '', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'edit', '0', '1', '0', '', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'printex', '1', '0', '0', '', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'select', '1', '0', '0', '', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'import', '0', '0', '0', 'Admin', '0');
--end tableoptions INSERT--
--tableoptions UPDATE--
UPDATE `tableoptions` SET `needselect` = 0 WHERE `tabledefid` = 2 AND `name` = 'massEmail';
UPDATE `tableoptions` SET `needselect` = 0 WHERE `tabledefid` = 303 AND `name` = 'run_aging';
--end tableoptions UPDATE--
--tablesearchablefields ALTER--
INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', 'postingsessions.id', 'id', '2', 'field');
INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`) VALUES ('tbld:97760a4f-1c1a-a108-d05f-5fc4ec59583c', '(users.lastname LIKE \"{{value}}%\" OR users.firstname LIKE \"{{value}}%\")', 'user', '1', 'whereclause');
--end tablesearchablefields ALTER--
--tabs UPDATE--
UPDATE `tabs` SET `uuid`='tab:becfca94-ae25-a42c-7909-247d5324e4b5' WHERE `id`='6';
UPDATE `tabs` SET `uuid`='tab:ef895fc0-bbea-9bf5-47ac-4913c6dace13' WHERE `id`='7';
UPDATE `tabs` SET `uuid`='tab:19e34181-65f0-bfcf-6e09-99d0575ebd74' WHERE `id`='8';
UPDATE `tabs` SET `uuid`='tab:d8e888af-d147-98ae-6849-a159a7c9daae' WHERE `id`='9';
UPDATE `tabs` SET `uuid`='tab:17346362-261b-4d1d-fa77-99e84cfd9b8a' WHERE `id`='10';
UPDATE `tabs` SET `uuid`='tab:9bfc7eea-5abb-f5d8-763f-f78fe499464d' WHERE `id`='11';
UPDATE `tabs` SET `uuid`='tab:cd09d4a1-7d32-e08a-bd6e-5850bc9af88e' WHERE `id`='12';
UPDATE `tabs` SET `uuid`='tab:4c853d8b-8895-a8c5-8ff6-1128e6e1a798' WHERE `id`='13';
UPDATE `tabs` SET `uuid`='tab:d62cf7eb-fd2a-948a-6279-8a61d02390ae' WHERE `id`='14';
UPDATE `tabs` SET `uuid`='tab:20276b44-9cfa-403e-4c2a-ac6f0987ae20' WHERE `id`='15';
UPDATE `tabs` SET `uuid`='tab:809d644e-fa40-5ad3-0426-3d84cf15b32e' WHERE `id`='16';
UPDATE `tabs` SET `uuid`='tab:23687374-5c14-04af-74ac-0f74342e1019' WHERE `id`='17';
UPDATE `tabs` SET `uuid`='tab:c4cbfabf-a00e-7b82-b411-0e442205360a' WHERE `id`='18';
UPDATE `tabs` SET `uuid`='tab:5a6ef814-2689-4e3b-2609-db43fb3cc001' WHERE `id`='300';
UPDATE `tabs` SET `uuid`='tab:625192d0-00e6-ae2c-5b8c-f433bbf6e546' WHERE `id`='303';
--end tabs UPDATE--
--usersearches UPDATE--
UPDATE `usersearches` SET `uuid`='sss:80d758f6-d96b-b2a7-1aba-1cddd2266c23' WHERE `id`='28';
UPDATE `usersearches` SET `uuid`='sss:b55e4aa0-4dde-52be-c60d-7faea7237fa6' WHERE `id`='29';
UPDATE `usersearches` SET `uuid`='sss:838153b2-a11a-8cd5-49e4-990b44bc83c3' WHERE `id`='30';
UPDATE `usersearches` SET `uuid`='sss:4d864de7-7502-1193-eb32-665cadc66661' WHERE `id`='31';
UPDATE `usersearches` SET `uuid`='sss:539ac44c-638a-a01a-d5d1-7e80803ab144' WHERE `id`='32';
UPDATE `usersearches` SET `uuid`='sss:eb14f8bd-5b4c-e8f8-c158-5e24a3cd5663' WHERE `id`='33';
UPDATE `usersearches` SET `uuid`='sss:a08d8603-cb8c-671d-fc72-15dfa500715d' WHERE `id`='34';
UPDATE `usersearches` SET `uuid`='sss:a79c03a5-acb6-228b-c53b-97abd7e00bb9' WHERE `id`='35';
UPDATE `usersearches` SET `uuid`='sss:315aed7f-ffff-3e16-b345-8b4420b4ad0f' WHERE `id`='36';
UPDATE `usersearches` SET `uuid`='sss:c6128941-56d1-5307-61aa-1b443441bbd7' WHERE `id`='37';
UPDATE `usersearches` SET `uuid`='sss:114f9c00-81a0-b2cf-c275-2bb665bf0370' WHERE `id`='38';
UPDATE `usersearches` SET `uuid`='sss:ca0a886c-8668-e233-a64a-44107f1e7baa' WHERE `id`='39';
UPDATE `usersearches` SET `uuid`='sss:4aa49326-37e8-7da6-4fff-689ca31d6543' WHERE `id`='40';
UPDATE `usersearches` SET `uuid`='sss:1ab1baea-5ed2-d6f8-28e2-7e1c6e67835b' WHERE `id`='41';
UPDATE `usersearches` SET `uuid`='sss:bd9877e7-bdf9-ea4a-fae8-9c1da5a4e83a' WHERE `id`='42';
UPDATE `usersearches` SET `uuid`='sss:2df0e7fc-42bc-edcb-deb9-9966f2491cd7' WHERE `id`='43';
UPDATE `usersearches` SET `uuid`='sss:4ce44288-db3f-e887-4f5c-1830171b5943' WHERE `id`='44';
UPDATE `usersearches` SET `uuid`='sss:497987b3-9ecd-b002-b3d1-14d3ee7b19b2' WHERE `id`='45';
UPDATE `usersearches` SET `uuid`='sss:3661c9eb-3018-9a68-f697-de35b10cbc50' WHERE `id`='46';
UPDATE `usersearches` SET `uuid`='sss:0f622991-46b7-c197-5c3c-abe668638d50' WHERE `id`='47';
UPDATE `usersearches` SET `uuid`='sss:3b58e5b3-6015-9214-4f74-8f4a4b3ca906' WHERE `id`='48';
UPDATE `usersearches` SET `uuid`='sss:464bd15b-a5ce-25f5-4178-ff7ef02a5ed2' WHERE `id`='49';
UPDATE `usersearches` SET
    `uuid`='sss:5b591200-0b48-dc0e-d88d-f165e32c490a',
    `userid`=''
WHERE
    `id`='70';
--end usersearches UPDATE--
--widgets INSERT--
INSERT INTO `widgets` (`uuid`, `type`, `title`, `file`, `roleid`, `moduleid`, `default`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('wdgt:c0a56726-d855-7443-66a2-7b84f443a84c', 'big', 'New Sales Orders', '../bms/widgets/recentsalesorders/class.php', 'role:de7e6679-8bb2-29ee-4883-2fcd756fb120', 'mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc', '1', 1, NOW(), 1, NOW());
INSERT INTO `widgets` (`uuid`, `type`, `title`, `file`, `roleid`, `moduleid`, `default`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('wdgt:26936c27-7b7c-07fc-1f35-97d2410688b5', 'big', 'New Clients', '../bms/widgets/recentclients/class.php', 'role:de7e6679-8bb2-29ee-4883-2fcd756fb120', 'mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc', '1', 1, NOW(), 1, NOW());
INSERT INTO `widgets` (`uuid`, `type`, `title`, `file`, `roleid`, `moduleid`, `default`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('wdgt:06a30e04-55ad-75da-7bd6-0c4203210ac8', 'little', 'Accounts Receivable Statistics', '../bms/widgets/arstats/class.php', 'role:c9439c3c-499b-7bcc-ee14-fec5bfcf5fc2', 'mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc', '1', 1, NOW(), 1, NOW());
--end widgets INSERT--
