INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:becfca94-ae25-a42c-7909-247d5324e4b5', 'general', 'clients entry', 'modules/bms/clients_addedit.php', '10', '1', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', '', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:ef895fc0-bbea-9bf5-47ac-4913c6dace13', 'purchase history', 'clients entry', 'modules/bms/clients_purchasehistory.php', '20', '0', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', '', '', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:19e34181-65f0-bfcf-6e09-99d0575ebd74', 'attachments', 'clients entry', 'modules/bms/clients_attachments.php', '30', '0', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', 'SELECT count(attachments.id) AS theresult FROM attachments INNER JOIN clients ON attachments.recordid = clients.uuid WHERE clients.id = {{id}}', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:d8e888af-d147-98ae-6849-a159a7c9daae', 'notes/tasks/events', 'clients entry', 'modules/bms/clients_notes.php', '40', '0', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', 'SELECT count(notes.id) AS theresult FROM notes INNER JOIN clients ON notes.attachedid = clients.uuid WHERE clients.id ={{id}}', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:17346362-261b-4d1d-fa77-99e84cfd9b8a', 'general', 'products entry', 'modules/bms/products_addedit.php', '10', '1', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', '', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:9bfc7eea-5abb-f5d8-763f-f78fe499464d', 'prerequisites', 'products entry', 'modules/bms/products_prereq.php', '20', '0', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', 'SELECT count(id) FROM prerequisites WHERE childid={{id}}', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:cd09d4a1-7d32-e08a-bd6e-5850bc9af88e', 'sales history', 'products entry', 'modules/bms/products_saleshistory.php', '30', '0', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', '', '', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:4c853d8b-8895-a8c5-8ff6-1128e6e1a798', 'attachments', 'products entry', 'modules/bms/products_attachments.php', '40', '0', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', 'SELECT count(attachments.id) AS theresult FROM attachments INNER JOIN products ON attachments.recordid = products.uuid WHERE  products.id = {{id}}', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:d62cf7eb-fd2a-948a-6279-8a61d02390ae', 'notes/tasks/events', 'products entry', 'modules/bms/products_notes.php', '50', '0', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', 'SELECT count(notes.id) AS theresult FROM notes INNER JOIN products ON notes.attachedid = products.uuid WHERE products.id = {{id}}', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:20276b44-9cfa-403e-4c2a-ac6f0987ae20', 'general', 'invoices entry', 'modules/bms/invoices_addedit.php', '10', '1', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', '', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:809d644e-fa40-5ad3-0426-3d84cf15b32e', 'status history', 'invoices entry', 'modules/bms/invoices_statushistory.php', '20', '0', 'role:de7e6679-8bb2-29ee-4883-2fcd756fb120', '', '', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:23687374-5c14-04af-74ac-0f74342e1019', 'attachments', 'invoices entry', 'modules/bms/invoices_attachments.php', '30', '0', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', 'SELECT count(attachments.id) AS theresult FROM attachments INNER JOIN invoices ON attachments.recordid = invoices.uuid WHERE  invoices.id = {{id}}', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:c4cbfabf-a00e-7b82-b411-0e442205360a', 'notes/events/task', 'invoices entry', 'modules/bms/invoices_notes.php', '40', '0', 'role:3403a7e0-adb1-4d0b-3c6e-6d6bbe177d52', '', 'SELECT count(notes.id) AS theresult FROM notes INNER JOIN invoices ON notes.attachedid = invoices.uuid WHERE invoices.id ={{id}}', 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:5a6ef814-2689-4e3b-2609-db43fb3cc001', 'credit', 'clients entry', 'modules/bms/clients_credit.php', '15', '0', '', 'Credit Limits/History', NULL, 1, NOW(), 1, NOW());
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:625192d0-00e6-ae2c-5b8c-f433bbf6e546', 'addresses', 'clients entry', 'modules/bms/clients_addresses.php', '12', '0', '', NULL, NULL, 1, NOW(), 1, NOW());
