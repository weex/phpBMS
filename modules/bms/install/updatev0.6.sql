ALTER TABLE clients ADD category varchar(128);
ALTER TABLE clients ADD becameclient date;
ALTER TABLE clients CHANGE type type ENUM("prospect","client") not null default "prospect";

ALTER TABLE products ADD keywords varchar(128);
ALTER TABLE products ADD thumbnail MEDIUMBLOB;
ALTER TABLE products ADD thumbnailmime varchar(128);
ALTER TABLE products ADD picture MEDIUMBLOB;
ALTER TABLE products ADD picturemime varchar(128);
ALTER TABLE products ADD webdescription text;
ALTER TABLE products ADD type ENUM("Inventory","Non-Inventory","Service","Kit","Assembly") not null default "Inventory";
ALTER TABLE invoices ADD taxpercentage double;
ALTER TABLE products ADD inactive tinyint not null default 0;
UPDATE products set inactive=1 WHERE Status="DISCONTINUED";
UPDATE products set status="Available" WHERE Status="DISCONTINUED";
ALTER TABLE products ADD taxable tinyint not null default 1;
ALTER TABLE products ADD memo text;

ALTER TABLE lineitems ADD taxable tinyint not null default 1;

DROP TABLE templineitems;

ALTER TABLE invoices ADD requireddate date default NULL;
ALTER TABLE invoices CHANGE status `type` ENUM("Quote","Order","Invoice","VOID") not null default "Order";
ALTER TABLE invoices ADD status ENUM("Open","Committed","Packed","Shipped") not null default "Open";
ALTER TABLE invoices ADD discountamount double not null default 0;
ALTER TABLE invoices ADD ponumber varchar(64) default '';
ALTER TABLE invoices ADD totaltaxable double not null default 0;
UPDATE invoices SET status="Shipped" WHERE shipped=1;
ALTER TABLE invoices DROP shipped;
ALTER TABLE invoices ADD discountid int not null default 0;
UPDATE invoices set totaltaxable=totaltni;
UPDATE invoices SET status="Shipped" WHERE `type`="Invoice";

CREATE TABLE `discounts` ( `id` int(11) NOT NULL auto_increment,`name` varchar(128) default '',`inactive` tinyint(1) NOT NULL default '0',`type` enum('percent','amount') NOT NULL default 'percent',`value` double NOT NULL default '0', `description` text, `createdby` int, `modifiedby` int, `modifieddate` timestamp,  `creationdate` datetime,  PRIMARY KEY  (`id`)) TYPE=MyISAM;