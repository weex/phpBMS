ALTER TABLE `clients` ALTER COLUMN `state` varchar(20) default NULL; 
ALTER TABLE `clients` ALTER COLUMN `shiptostate` varchar(20) default NULL; 
ALTER TABLE `invoices` ALTER COLUMN `state` varchar(20) default NULL; 
