INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('2', 'Clients', 'table', '2', 'clients', '((clients INNER JOIN addresstorecord on clients.id = addresstorecord.recordid AND addresstorecord.tabledefid=2 AND addresstorecord.primary=1) INNER JOIN addresses ON  addresstorecord.addressid = addresses.id)', 'modules/bms/clients_addedit.php', '0', 'modules/bms/clients_addedit.php', '0', '0', '-100', '-100', 'inactivate', 'clients.id=-100', 'clients.company, clients.lastname, clients.firstname', NULL, NULL, NULL, 1, NOW(), 1, NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (3,'Sales Orders','table',2,'invoices','(((invoices INNER JOIN clients ON invoices.clientid=clients.id) INNER JOIN invoicestatuses ON invoices.statusid=invoicestatuses.id) LEFT JOIN paymentmethods ON invoices.paymentmethodid = paymentmethods.id)','modules/bms/invoices_addedit.php',0,'modules/bms/invoices_addedit.php',0,0,-100,-100,'void','invoices.type+0<3 OR (invoices.type = \'Invoice\' AND invoices.totalti != invoices.amountpaid)','invoices.id','','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (4,'Products','table',2,'products','(products inner join productcategories on products.categoryid=productcategories.id)','modules/bms/products_addedit.php',0,'modules/bms/products_addedit.php',0,0,-100,-100,'inactivate','products.id =-100','products.partnumber','','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (5,'Line Items','table',2,'lineitems','((lineitems left join products on lineitems.productid=products.id) inner join invoices on lineitems.invoiceid=invoices.id)','N/A',0,'N/A',0,0,-100,-100,'NA','lineitems.id =-1','lineitems.invoiceid,lineitems.id','','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (6,'Tax Areas','table',2,'tax','tax','modules/bms/tax_addedit.php',0,'modules/bms/tax_addedit.php',0,0,-100,-100,'inactive','tax.inactive=0','tax.name','','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (7,'Product Categories','table',2,'productcategories','productcategories','modules/bms/productcategories_addedit.php',0,'modules/bms/productcategories_addedit.php',0,0,-100,-100,'delete','productcategories.id !=0','productcategories.name','','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (8,'Prerequisites','table',2,'prerequisites','(prerequisites left join products on prerequisites.parentid=products.id) left join products as childproducts on prerequisites.childid=childproducts.id','N/A',0,'N/A',0,0,-100,-100,'NA','prerequisites.id =-1','products.id','','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('18', 'Client Duplicates', 'view', '2', 'clients', '((clients INNER JOIN addresstorecord on clients.id = addresstorecord.recordid AND addresstorecord.tabledefid=2 AND addresstorecord.primary=1) INNER JOIN addresses ON addresstorecord.addressid = addresses.id),((clients AS dclients INNER JOIN addresstorecord as daddresstorecord on dclients.id = daddresstorecord.recordid AND daddresstorecord.tabledefid=2 AND daddresstorecord.primary=1) INNER JOIN addresses AS daddresses ON daddresstorecord.addressid = daddresses.id)', 'N/A', '0', 'N/A', '0', '0', '-100', '-100', 'NA', 'clients.id = -100', 'clients.company, clients.lastname, clients.firstname', NULL, NULL, NULL, 1, NOW(), 1, NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (22,'Client E-mail Projects','system',2,'clientemailprojects','clientemailprojects left join users on clientemailprojects.userid=users.id','modules/bms/clientemailprojects_edit.php',0,'NA',0,0,-100,-100,'delete','clientemailprojects.id !=-1','clientemailprojects.name','search','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (25,'Discounts','table',2,'discounts','discounts','modules/bms/discounts_addedit.php',0,'modules/bms/discounts_addedit.php',0,0,-100,-100,'inactivate','discounts.id!=0','discounts.id','search','all records','name',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (300,'Shipping Methods','table',2,'shippingmethods','shippingmethods','modules/bms/shippingmethods_addedit.php',30,'modules/bms/shippingmethods_addedit.php',30,30,30,-100,'inactivate','shippingmethods.inactive!=0','shippingmethods.priority, shippingmethods.name','','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (301,'Payment Methods','table',2,'paymentmethods','paymentmethods','modules/bms/paymentmethods_addedit.php',30,'modules/bms/paymentmethods_addedit.php',30,30,30,-100,'inactivate','paymentmethods.inactive=0','paymentmethods.priority,paymentmethods.name','','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (302,'Invoice Statuses','table',2,'invoicestatuses','invoicestatuses','modules/bms/invoicestatuses_addedit.php',30,'modules/bms/invoicestatuses_addedit.php',30,30,-100,-100,'inactivate','invoicestatuses.inactive=0','invoicestatuses.priority,invoicestatuses.name','','','',1,NOW(),1,NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('303', 'AR Items', 'table', '2', 'aritems', '(aritems INNER JOIN clients ON aritems.clientid = clients.id)', 'modules/bms/aritems_view.php', '0', 'NA', '0', '0', '-100', '-100', 'NA', 'aritems.posted=1 AND aritems.status = \'open\'', 'aritems.itemdate, aritems.status, aritems.id', NULL, NULL, NULL, 1, NOW(), 1, NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('304', 'Receipts', 'table', '2', 'receipts', '((receipts INNER JOIN clients ON receipts.clientid = clients.id) LEFT JOIN paymentmethods ON receipts.paymentmethodid = paymentmethods.id)', 'modules/bms/receipts_addedit.php', '80', 'modules/bms/receipts_addedit.php', '80', '80', '-100', '-100', 'delete', 'receipts.posted=0', 'receipts.receiptdate', NULL, NULL, NULL, 1, NOW(), 1, NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('305', 'Client Addresses', 'view', '2', 'addresstorecord', '((addresstorecord INNER JOIN addresses ON addresstorecord.addressid = addresses.id) INNER JOIN clients ON addresstorecord.recordid = clients.id)', 'modules/bms/addresses_addedit.php', '0', 'modules/bms/addresses_addedit.php', '0', '0', '-100', '-100', 'remove', 'addresstorecord.tabledefid=2 AND addresstorecord.id = -100', 'addresstorecord.primary DESC,  addresstorecord.defaultshipto DESC,  addresses.title,  addresses.address1', NULL, NULL, NULL, 1, NOW(), 1, NOW());
INSERT INTO `tabledefs` (`id`, `displayname`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('306', 'Addresses', 'table', '2', 'addresses', 'addresses', 'modules/bms/addresses_addedit.php', '0', 'modules/bms/addresses_addedit.php', '0', '0', '-100', '-100', 'delete', 'addresses.id != -100', 'address.title', NULL, NULL, NULL, 1, NOW(), 1, NOW());