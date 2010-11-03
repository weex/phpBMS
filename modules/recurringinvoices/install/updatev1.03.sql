ALTER TABLE `recurringinvoices` ENGINE=INNODB;
ALTER TABLE `recurringinvoices`
    MODIFY COLUMN `assignedtoid` VARCHAR(64),
    MODIFY COLUMN `statusid` VARCHAR(64) NOT NULL,
    MODIFY COLUMN `invoiceid` VARCHAR(64) NOT NULL,
    DROP COLUMN `includepaymentdetails`;
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:3434bf2d-1337-5cab-0a7a-25e04f1c6d8f', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:1f7541a0-7bbe-6b9f-e7c5-2db926557e53', 'Recurring Invoices', 'search.php?id=tbld%3A3434bf2d-1337-5cab-0a7a-25e04f1c6d8f', 'menu:8cf7d073-72b9-93db-6d07-14578e2a694f', '17', 1, 1, NOW(), NOW(), 'role:259ead9f-100b-55b5-508a-27e33a6216bf');
UPDATE `roles` SET `uuid`='role:ddbc37d3-c450-beba-b720-6cec50b55d82' WHERE `id`='400';
UPDATE `scheduler` SET `uuid`='schd:ba7f18b4-3489-a970-bf4c-ed7e3053dabf' WHERE `name`='Recurr Invoices';
UPDATE `tabledefs` SET
    `uuid`='tbld:3434bf2d-1337-5cab-0a7a-25e04f1c6d8f',
    `querytable` = '(recurringinvoices INNER JOIN invoices ON recurringinvoices.invoiceid = invoices.uuid) INNER JOIN clients ON invoices.clientid = clients.uuid'
WHERE
    `id`='400';

--UPDATE `tablecolumns` SET
--    `tabledefid` = 'tbld:3434bf2d-1337-5cab-0a7a-25e04f1c6d8f',
--    `roleid` = ''
--WHERE
--    `tabledefid` = '400';
--
--UPDATE `tablefindoptions` SET
--    `tabledefid` = 'tbld:3434bf2d-1337-5cab-0a7a-25e04f1c6d8f',
--    `roleid` = ''
--WHERE
--    `tabledefid` = '400';
--
--UPDATE `tableoptions` SET
--    `tabledefid` = 'tbld:3434bf2d-1337-5cab-0a7a-25e04f1c6d8f',
--    `roleid` = ''
--WHERE
--    `tabledefid` = '400'
--    AND
--    `roleid` = '0';
--
--UPDATE `tableoptions` SET
--    `tabledefid` = 'tbld:3434bf2d-1337-5cab-0a7a-25e04f1c6d8f',
--    `roleid` = 'Admin'
--WHERE
--    `tabledefid` = '400'
--    AND
--    `roleid` = '-100';
--
--UPDATE `tablesearchablefields` SET
--    `tabledefid` = 'tbld:3434bf2d-1337-5cab-0a7a-25e04f1c6d8f'
--WHERE
--    `tabledefid` = '400';
--
UPDATE `users` SET `uuid`='usr:170c4b04-b8fe-a528-fcea-5a213669f400' WHERE `id`='5';
UPDATE `tabs` SET
    `uuid`='tab:d303321e-7ff5-fe4b-29ec-fe3eb0305576'
WHERE
    `id`='500';
UPDATE `modules` SET
    `uuid`='mod:a243321f-9095-e25b-2750-f7e8328e3d4c'
WHERE
    `id`='200';