CREATE TABLE clientemailprojects (
  id int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  name varchar(64) default '',
  userid int(11) NOT NULL default '0',
  emailto varchar(9) NOT NULL default '',
  emailfrom varchar(128) default '',
  subject varchar(128) default '',
  body text,
  lastrun timestamp(14) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=INNODB;

CREATE TABLE `clients` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  `firstname` varchar(64) NOT NULL default '',
  `lastname` varchar(64) NOT NULL default '',
  `company` varchar(128) NOT NULL default '',
  `type` enum('prospect','client') NOT NULL default 'prospect',
  `becameclient` date default NULL,
  `inactive` tinyint(4) NOT NULL default '0',
  `category` varchar(128) default NULL,
  `homephone` varchar(25) default NULL,
  `workphone` varchar(25) default NULL,
  `mobilephone` varchar(25) default NULL,
  `fax` varchar(25) default NULL,
  `otherphone` varchar(25) default NULL,
  `email` varchar(128) default NULL,
  `webaddress` varchar(128) default NULL,
  `taxid` VARCHAR(64) default NULL,
  `salesmanagerid` int(11) default NULL,
  `leadsource` varchar(64) default NULL,
  `address1` varchar(128) default NULL,
  `address2` varchar(128) default NULL,
  `city` varchar(64) default NULL,
  `state` varchar(20) default NULL,
  `postalcode` varchar(15) default NULL,
  `country` varchar(64) default '',
  `comments` text,
  `paymentmethodid` int(10) unsigned default '0',
  `shippingmethodid` int(10) unsigned default '0',
  `discountid` int(10) unsigned default '0',
  `taxareaid` int(11) default '0',
  `username` varchar(32) default NULL,
  `password` varchar(32) default NULL,
  `hascredit` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `creditlimit` DOUBLE,
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp NOT NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY (`id`),
  KEY `notin` (`inactive`),
  KEY `thefirstname` (`firstname`),
  KEY `created` (`creationdate`),
  KEY `thelastname` (`lastname`),
  KEY `thecompany` (`company`),
  KEY `thetype` (`type`)
) ENGINE=INNODB;

CREATE TABLE discounts (
  id int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  name varchar(128) default '',
  inactive tinyint(1) NOT NULL default '0',
  type enum('percent','amount') NOT NULL default 'percent',
  value double NOT NULL default '0',
  description text,
  createdby int(11) default NULL,
  modifiedby int(11) default NULL,
  modifieddate timestamp(14) NOT NULL,
  creationdate datetime default NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY  (id)
) ENGINE=INNODB;

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  `clientid` int(11) NOT NULL default '0',
  `type` enum('Quote','Order','Invoice','VOID') default NULL,
  `postingsessionid` int(11) default NULL,
  `statusid` int(10) unsigned default NULL,
  `statusdate` date default NULL,
  `readytopost` tinyint(3) unsigned NOT NULL default '0',
  `assignedtoid` int(10) unsigned default NULL,
  `ponumber` varchar(64) default NULL,
  `orderdate` date default NULL,
  `invoicedate` date default NULL,
  `requireddate` date default NULL,
  `leadsource` varchar(64) default NULL,
  `address1` varchar(128) default NULL,
  `address2` varchar(128) default NULL,
  `city` varchar(64) default NULL,
  `state` varchar(5) default NULL,
  `postalcode` varchar(15) default NULL,
  `country` varchar(64) default '',
  `weborder` tinyint(1) default '0',
  `webconfirmationno` varchar(64) default '',
  `discountid` int(11) NOT NULL default '0',
  `discountamount` double NOT NULL default '0',
  `totaltni` double default '0',
  `taxareaid` int(11) default '0',
  `taxpercentage` double default NULL,
  `totaltaxable` double default '0',
  `tax` double default '0',
  `shippingmethodid` int(10) unsigned default NULL,
  `totalweight` double default '0',
  `trackingno` varchar(64) default NULL,
  `shipping` double default '0',
  `totalcost` double default '0',
  `totalti` double default '0',
  `amountpaid` double default '0',
  `paymentmethodid` int(10) unsigned default NULL,
  `ccexpiration` varchar(10) default NULL,
  `ccnumber` varchar(64) default NULL,
  `ccverification` varchar(4) default '',
  `bankname` varchar(64) default NULL,
  `checkno` varchar(32) default NULL,
  `routingnumber` int(10) unsigned default NULL,
  `accountnumber` int(10) unsigned default NULL,
  `transactionid` varchar(64) default NULL,
  `printedinstructions` text,
  `specialinstructions` text,
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `shiptoname` varchar(128) default NULL,
  `shiptoaddress1` varchar(128) default NULL,
  `shiptoaddress2` varchar(128) default NULL,
  `shiptocity` varchar(64) default NULL,
  `shiptostate` varchar(20) default NULL,
  `shiptopostalcode` varchar(15) default NULL,
  `shiptocountry` varchar(64) default NULL,
  `billingaddressid` int(11) default NULL,
  `shiptoaddressid` int(11) default NULL,
  `shiptosameasbilling` tinyint(3) unsigned NOT NULL default '0',
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  UNIQUE KEY `theid` (`id`),
  KEY `client` (`clientid`)
)  ENGINE=INNODB AUTO_INCREMENT=1000 PACK_KEYS=0;

CREATE TABLE lineitems (
  id int(11) NOT NULL auto_increment,
  invoiceid int(11) NOT NULL default '0',
  `displayorder` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  productid int(11) default NULL,
  quantity double default NULL,
  unitcost double default NULL,
  unitprice double default NULL,
  unitweight double default NULL,
  memo text,
  taxable tinyint(4) NOT NULL default '1',
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifiedby int(11) default NULL,
  modifieddate timestamp(14) NOT NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY (id),
  KEY invoice (invoiceid),
  KEY product (productid)
) ENGINE=INNODB;

CREATE TABLE prerequisites (
  childid int(11) NOT NULL default '0',
  id int(11) NOT NULL auto_increment,
  parentid int(11) NOT NULL default '0',
  PRIMARY KEY (id),
  KEY child (childid),
  KEY parent (parentid)
) ENGINE=INNODB;

CREATE TABLE `postingsessions` (
  `id` int(11) NOT NULL auto_increment,
  `sessiondate` datetime NOT NULL default '0000-00-00 00:00:00',
  `source` varchar(64) NOT NULL default '',
  `recordsposted` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=INNODB;

CREATE TABLE productcategories (
  `id` int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) default NULL,
  `parentid` varchar(64) NOT NULL DEFAULT '',
  `displayorder` int(11) NOT NULL DEFAULT 0,
  `inactive` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `description` text,
  `webenabled` tinyint(1) NOT NULL default 0,
  `webdisplayname` varchar(64) default '',
  `createdby` int(11) NOT NULL default 0,
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp(14) NOT NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY(`id`)
) ENGINE=INNODB;

CREATE TABLE products (
  `categoryid` varchar(64) NOT NULL default '',
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  description varchar(255) default NULL,
  id int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  isoversized tinyint(4) NOT NULL default '0',
  isprepackaged tinyint(4) NOT NULL default '0',
  packagesperitem double default NULL,
  modifiedby int(11) default NULL,
  modifieddate timestamp(14) NOT NULL,
  partname varchar(128) default NULL,
  partnumber varchar(32) NOT NULL default '',
  status varchar(32) NOT NULL default 'In Stock',
  unitcost double default '0',
  unitofmeasure varchar(64) default NULL,
  unitprice double default '0',
  weight double default NULL,
  webenabled tinyint(1) NOT NULL default '0',
  keywords varchar(128) default NULL,
  thumbnail mediumblob,
  thumbnailmime varchar(128) default NULL,
  picture mediumblob,
  picturemime varchar(128) default NULL,
  webdescription text,
  inactive tinyint(4) NOT NULL default '0',
  type enum('Inventory','Non-Inventory','Service','Kit','Assembly') NOT NULL default 'Inventory',
  taxable tinyint(4) NOT NULL default '1',
  memo text,
  upc varchar(128) default NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  UNIQUE KEY theid (id),
  UNIQUE KEY thpartnum (partnumber),
  KEY status (status)
) ENGINE=INNODB;

CREATE TABLE tax (
  id int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  name varchar(64) default NULL,
  percentage double NOT NULL default '0',
  `inactive` tinyint(4) unsigned NOT NULL default '0',
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifiedby int(11) default NULL,
  modifieddate timestamp(14) NOT NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  UNIQUE KEY theid (id)
) ENGINE=INNODB;

CREATE TABLE `shippingmethods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT 0,
  `priority` int(11) NOT NULL DEFAULT 0,
  `canestimate` tinyint(4) NOT NULL DEFAULT 0,
  `estimationscript` VARCHAR(128),
  createdby int(11) NOT NULL default '0',
  `creationdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  PRIMARY KEY(`id`)
) ENGINE=INNODB;

CREATE TABLE `paymentmethods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL default '',
  `type` enum('draft','charge','receivable') default NULL,
  `priority` int(8) NOT NULL default '0',
  `inactive` tinyint(1) NOT NULL default '0',
  `onlineprocess` tinyint(1) NOT NULL default '0',
  `processscript` varchar(128) default '',
  `createdby` int(11) default NULL,
  `creationdate` datetime default NULL,
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp NOT NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY  (`id`)
) ENGINE=INNODB;

CREATE TABLE `invoicestatuses` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` VARCHAR(128),
  `setreadytopost` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `invoicedefault` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `defaultassignedtoid` INTEGER UNSIGNED,
  `inactive` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `priority` INTEGER UNSIGNED NOT NULL DEFAULT 0,
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
  PRIMARY KEY(`id`)
) ENGINE=INNODB;

CREATE TABLE `invoicestatushistory` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoicedefault` INTEGER UNSIGNED,
  `invoiceid` INTEGER UNSIGNED,
  `invoicestatusid` INTEGER UNSIGNED,
  `statusdate` DATE,
  `assignedtoid` INTEGER UNSIGNED,
  PRIMARY KEY(`id`)
) ENGINE=INNODB;

CREATE TABLE `aritems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` int(10) unsigned NOT NULL,
  `type` ENUM('invoice','credit','service charge') NOT NULL,
  `status` ENUM('open','closed') NOT NULL,
  `itemdate` DATE NOT NULL,
  `relatedid` int(10) unsigned default NULL,
  `amount` double NOT NULL default '0',
  `paid` double NOT NULL default '0',
  `aged1` tinyint(3) unsigned NOT NULL default '0',
  `aged2` tinyint(3) unsigned NOT NULL default '0',
  `aged3` tinyint(3) unsigned NOT NULL default '0',
  `title` varchar(255) default NULL,
  `posted` tinyint(3) unsigned NOT NULL default '0',
  `createdby` int(10) unsigned NOT NULL,
  `creationdate` datetime NOT NULL,
  `modifiedby` int(10) unsigned default NULL,
  `modifieddate` timestamp NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB;

CREATE TABLE `receipts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `clientid` int(10) unsigned NOT NULL,
  `amount` double NOT NULL default '0',
  `receiptdate` date NOT NULL,
  `status` enum('open','collected') NOT NULL default 'open',
  `readytopost` tinyint(3) unsigned NOT NULL default '0',
  `posted` tinyint(3) unsigned NOT NULL default '0',
  `postingsessionid` int(11) default NULL,
  `paymentmethodid` int(10) NOT NULL default '0',
  `ccnumber` varchar(64) default NULL,
  `ccexpiration` varchar(10) default NULL,
  `ccverification` varchar(4) default NULL,
  `bankname` varchar(64) default NULL,
  `checkno` varchar(32) default NULL,
  `routingnumber` int(10) unsigned default NULL,
  `accountnumber` int(10) unsigned default NULL,
  `transactionid` varchar(64) default NULL,
  `paymentother` varchar(128) default NULL,
  `memo` text,
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(10) unsigned default NULL,
  `modifieddate` timestamp NOT NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY  (`id`)
) ENGINE=INNODB;

CREATE TABLE `receiptitems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `receiptid` int(10) unsigned NOT NULL,
  `aritemid` int(10) unsigned NOT NULL,
  `applied` double NOT NULL default '0',
  `discount` double NOT NULL default '0',
  `taxadjustment` double NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB;

CREATE TABLE `addresses` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `title` VARCHAR(128),
  `shiptoname` VARCHAR(128),
  `address1` VARCHAR(128),
  `address2` VARCHAR(128),
  `city` VARCHAR(64),
  `state` VARCHAR(20),
  `postalcode` VARCHAR(15),
  `country` VARCHAR(64),
  `phone` VARCHAR(25),
  `email` VARCHAR(128),
  `notes` TEXT,
  `createdby` INTEGER UNSIGNED NOT NULL,
  `creationdate` DATETIME NOT NULL,
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
  PRIMARY KEY(`id`)
) ENGINE=INNODB;

CREATE TABLE `addresstorecord` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `tabledefid` INTEGER UNSIGNED NOT NULL,
  `recordid` INTEGER UNSIGNED NOT NULL,
  `addressid` INTEGER UNSIGNED NOT NULL,
  `defaultshipto` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `primary` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `createdby` INTEGER UNSIGNED NOT NULL,
  `creationdate` DATETIME NOT NULL,
  `modifiedby` INTEGER UNSIGNED,
  `modifieddate` TIMESTAMP,
  PRIMARY KEY(`id`)
) ENGINE=INNODB;

CREATE TABLE `productstoproductcategories` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `productuuid` varchar(64) NOT NULL,
  `productcategoryuuid` varchar(64) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=INNODB;