INSERT INTO `scheduler` (`uuid`, `name`, `job`, `crontab`, `lastrun`, `startdatetime`, `enddatetime`, `description`, `inactive`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('schd:ba7f18b4-3489-a970-bf4c-ed7e3053dabf', 'Recurr Invoices', '../recurringinvoices/scheduler_recurr.php', '0::0::*::*::*', '2009-05-26 00:00:01', '0000-00-00 00:00:00', NULL, 'This job recurrs any invoices with repeat options.', '0', 1, NOW(), 1, NOW());