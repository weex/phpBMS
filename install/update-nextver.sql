/* Fixes bug 443 */
ALTER TABLE `tabledefs` CHANGE `addfile` `addfile` VARCHAR( 128 );
UPDATE `php_bms_db`.`tabledefs` SET `editfile` = 'modules/base/notes_addedit.php?ty=EV&backurl=../../search.php?id=tbld%3A0fcca651-6c34-c74d-ac04-2d88f602dd71', `addfile` = 'modules/base/notes_addedit.php?ty=EV&backurl=../../search.php?id=tbld%3A0fcca651-6c34-c74d-ac04-2d88f602dd71', `modifieddate` = NOW( ) WHERE `tabledefs`.`id` =10 LIMIT 1 ;
UPDATE `php_bms_db`.`tabledefs` SET `editfile` = 'modules/base/notes_addedit.php?ty=EV&backurl=../../search.php?id=tbld%3A2bc3e683-81f9-694a-9550-a0c7263057de', `addfile` = 'modules/base/notes_addedit.php?ty=EV&backurl=../../search.php?id=tbld%3A2bc3e683-81f9-694a-9550-a0c7263057de', `modifieddate` = NOW() WHERE `tabledefs`.`id` = 9 LIMIT 1;
