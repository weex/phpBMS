ALTER TABLE `recurringinvoices` ENGINE=INNODB;
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('400', 'import', '0', '0', '0', '-100', '0');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:1f7541a0-7bbe-6b9f-e7c5-2db926557e53', 'Recurring Invoices', 'search.php?id=400', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '17', 1, 1, NOW(), NOW(), '30');
UPDATE `roles` SET `uuid`='role:ddbc37d3-c450-beba-b720-6cec50b55d82' WHERE `id`='400';
UPDATE `scheduler` SET `uuid`='schd:ba7f18b4-3489-a970-bf4c-ed7e3053dabf' WHERE `name`='Recurr Invoices';
UPDATE `tabledefs` SET `uuid`='tbld:3434bf2d-1337-5cab-0a7a-25e04f1c6d8f' WHERE `id`='400';
UPDATE `users` SET `uuid`='usr:170c4b04-b8fe-a528-fcea-5a213669f400' WHERE `id`='5';
UPDATE `tabs` SET `uuid`='tab:d303321e-7ff5-fe4b-29ec-fe3eb0305576' WHERE `id`='500';
UPDATE `modules` SET `uuid`='mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc' WHERE `id`='200';