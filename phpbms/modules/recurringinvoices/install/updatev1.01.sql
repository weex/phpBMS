INSERT INTO `menu` (`name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('Recurring Invoices','search.php?id=400',204,17,1,1,NOW(),NOW(),30);
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `othercommand`, `roleid`, `displayorder`) VALUES ('400', 'edit', '1', '0', '0', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `othercommand`, `roleid`, `displayorder`) VALUES ('400', 'new', '0', '0', '0', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `othercommand`, `roleid`, `displayorder`) VALUES ('400', 'select', '1', '0', '0', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `othercommand`, `roleid`, `displayorder`) VALUES ('400', 'printex', '0', '0', '0', '0');