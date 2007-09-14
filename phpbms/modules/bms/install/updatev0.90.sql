ALTER TABLE `clients` MODIFY COLUMN `state` varchar(20) default NULL; 
ALTER TABLE `clients` MODIFY COLUMN `shiptostate` varchar(20) default NULL; 
ALTER TABLE `invoices` MODIFY COLUMN `state` varchar(20) default NULL; 
