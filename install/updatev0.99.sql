--tabledefs ALTER--
ALTER TABLE `tabledefs` CHANGE `addfile` `addfile` VARCHAR( 128 );
--end tabledefs ALTER--
--tabledefs UPDATE--
UPDATE `tabledefs` SET `editfile` = 'modules/base/notes_addedit.php?ty=EV&backurl=../../search.php?id=tbld%3A0fcca651-6c34-c74d-ac04-2d88f602dd71', `addfile` = 'modules/base/notes_addedit.php?ty=EV&backurl=../../search.php?id=tbld%3A0fcca651-6c34-c74d-ac04-2d88f602dd71', `modifieddate` = NOW( ) WHERE `tabledefs`.`id` =10 LIMIT 1 ;
UPDATE `tabledefs` SET `editfile` = 'modules/base/notes_addedit.php?ty=EV&backurl=../../search.php?id=tbld%3A2bc3e683-81f9-694a-9550-a0c7263057de', `addfile` = 'modules/base/notes_addedit.php?ty=EV&backurl=../../search.php?id=tbld%3A2bc3e683-81f9-694a-9550-a0c7263057de', `modifieddate` = NOW() WHERE `tabledefs`.`id` = 9 LIMIT 1;
--end tabledefs UPDATE--
--tablecolumns ALTER--
ALTER TABLE `tablecolumns` CHANGE `format` `format` ENUM( 'date', 'time', 'currency', 'boolean', 'datetime', 'filelink', 'noencoding', 'bbcode', 'client', 'invoice' );
--end tablecolumns ALTER--
--tableoptions UPDATE--
UPDATE `tableoptions` SET `name` = 'mark_asshipped' where `tabledefid` = 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883' and `name` = 'mark_ashipped';
UPDATE `tablecolumns` SET `format` = 'invoices' WHERE `tabledefid` = 'tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883' AND `name` = 'id';
--end tableoptions UPDATE--
--users ALTER--
ALTER TABLE `users` ADD `mailer` VARCHAR( 255 ) NOT NULL DEFAULT 'mail' AFTER `email` ,
ADD `sendmail` VARCHAR( 255 ) NOT NULL DEFAULT '/usr/sbin/sendmail -bs' AFTER `mailer` ,
ADD `smtphost` VARCHAR( 255 ) NOT NULL DEFAULT 'localhost' AFTER `sendmail` ,
ADD `smtpport` INT( 11 ) NOT NULL DEFAULT '25' AFTER `smtphost` ,
ADD `smtpauth` INT( 11 ) NOT NULL DEFAULT '0' AFTER `smtpport` ,
ADD `smtpuser` VARCHAR( 255 ) NOT NULL AFTER `smtpauth` ,
ADD `smtppass` VARCHAR( 255 ) NOT NULL AFTER `smtpuser` ,
ADD `smtpsecure` VARCHAR( 255 ) NOT NULL DEFAULT 'none' AFTER `smtppass` ;
--end users ALTER--
--settings ALTER--
ALTER TABLE `settings` CHANGE `value` `value` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
--end settings ALTER--
