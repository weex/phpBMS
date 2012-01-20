--settings INSERT--
INSERT INTO `settings` (`name`, `value`) VALUES ('show_payment_instructions','0');
INSERT INTO `settings` (`name`, `value`) VALUES ('invoice_paymentinstruc','Enter your payment details here');
--end settings INSERT--
--addresses ALTER--
ALTER TABLE `addresses` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `addresses` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end addresses ALTER--
--clients ALTER--
ALTER TABLE `clients` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `clients` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end clients ALTER--
--discounts ALTER--
ALTER TABLE `discounts` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `discounts` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end discounts ALTER--
--invoices ALTER--
ALTER TABLE `invoices` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `invoices` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end invoices ALTER--
--invoicestatuses ALTER--
ALTER TABLE `invoicestatuses` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `invoicestatuses` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end invoicestatuses ALTER--
--lineitems ALTER--
ALTER TABLE `lineitems` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `lineitems` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end lineitems ALTER--
--paymentmethods ALTER--
ALTER TABLE `paymentmethods` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `paymentmethods` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end paymentmethods ALTER--
--productcategories ALTER--
ALTER TABLE `productcategories` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `productcategories` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end productcategories ALTER--
--products ALTER--
ALTER TABLE `products` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `products` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end products ALTER--
--receipts ALTER--
ALTER TABLE `receipts` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `receipts` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end receipts ALTER--
--shippingmethods ALTER--
ALTER TABLE `shippingmethods` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `shippingmethods` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end shippingmethods ALTER--
--tax ALTER--
ALTER TABLE `tax` CHANGE `custom7` `custom7` tinyint(1) NOT NULL;
ALTER TABLE `tax` CHANGE `custom8` `custom8` tinyint(1) NOT NULL;
--end tax ALTER--