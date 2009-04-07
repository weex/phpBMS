INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('2', 'e-mail', 'clients.email', 'left', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('2', 'has credit', 'clients.hascredit', 'center', '', '1', '', '0', '', 'boolean', '80');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('2', 'name / location', 'CONCAT(\'[b]\',IF(clients.company != \'\', CONCAT(clients.company,IF(clients.lastname != \'\' OR clients.firstname != \'\', CONCAT(\' (\',if(clients.lastname != \'\', clients.lastname, \'{blank}\'),\', \',if(clients.firstname != \'\', clients.firstname, \'{blank}\'),\')\'), \'\')), IF(clients.lastname != \'\' OR clients.firstname != \'\', CONCAT(if(clients.lastname != \'\', clients.lastname, \'{blank}\'),\', \',if(clients.firstname != \'\', clients.firstname, \'{blank}\')), \'\')),\'[/b][br][space]\', IF(addresses.city != \'\' OR addresses.state !=\'\' OR addresses.postalcode != \'\', CONCAT(IF(addresses.city != \'\',addresses.city,\'\'),\', \',IF(addresses.state != \'\', addresses.state, \'\'),\' \',IF(addresses.postalcode != \'\', addresses.postalcode, \'\')),\'(no location)\'))', 'left', '', '2', 'concat(clients.company,clients.lastname,clients.firstname)', '0', '100%', 'bbcode', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('2', 'type', 'clients.type', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('2', 'phone', 'IF(clients.workphone != \'\' OR clients.homephone != \'\' OR clients.mobilephone != \'\' OR clients.otherphone != \'\',IF(clients.workphone != \'\', concat(clients.workphone, \' (w)\'), IF(clients.homephone != \'\', concat(clients.homephone, \' (h)\'), IF(clients.mobilephone != \'\', concat(clients.mobilephone, \' (m)\'), IF(clients.otherphone != \'\', concat(clients.otherphone, \' (o)\'), \'\')))) ,\'\')', 'left', '', '4', 'concat(clients.workphone, clients.homephone, clients.mobilephone,clients.otherphone)', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'id', 'invoices.id', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'type', 'invoices.type', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'status', 'invoicestatuses.name', 'left', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'date', 'if(invoices.type=\"Invoice\",invoices.invoicedate,invoices.orderdate)', 'left', '', '5', '', '0', '', 'date', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'client name / company', 'concat(\"<strong>\",if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company),\"</strong>\")', 'left', '', '6', NULL, '0', '100%', 'noencoding', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'total', 'if(invoices.type!=\"VOID\",invoices.totalti,\"-----\")', 'right', 'sum(if(invoices.type!=\"VOID\",invoices.totalti,0))', '8', 'if(invoices.type!=\"VOID\",invoices.totalti,0)', '0', '', 'currency', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'due', 'if(invoices.type!=\"VOID\",invoices.totalti-invoices.amountpaid,\"-----\")', 'right', 'sum(if(invoices.type!=\"VOID\",(invoices.totalti-invoices.amountpaid),0))', '9', 'if(invoices.type!=\"VOID\",invoices.totalti-invoices.amountpaid,0)', '0', '', 'currency', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'web', 'invoices.weborder', 'center', '', '4', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'RTP', 'invoices.readytopost', 'center', '', '1', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('3', 'payment', 'paymentmethods.name', 'left', '', '7', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('4', 'part number', 'products.partnumber', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('4', 'name', 'products.partname', 'left', '', '1', '', '1', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('4', 'status', 'products.status', 'left', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('4', 'unit price', 'products.unitprice', 'right', '', '4', '', '0', '', 'currency', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('4', 'type', 'products.type', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'id', 'lineitems.id', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'invoice id', 'lineitems.invoiceid', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'part #', 'products.partnumber', 'left', '', '4', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'name', 'products.partname', 'left', '', '5', '', '1', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'unit price', 'lineitems.unitprice', 'right', 'sum(lineitems.unitprice)', '6', 'lineitems.unitprice', '0', '', 'currency', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'qty', 'format(lineitems.quantity,2)', 'center', 'format(sum(lineitems.quantity),2)', '7', 'lineitemd.quantity', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'price ext.', 'lineitems.unitprice*lineitems.quantity', 'right', 'sum(lineitems.unitprice*lineitems.quantity)', '8', '', '0', '', 'currency', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'cost ext.', 'lineitems.unitcost*lineitems.quantity', 'right', 'sum(lineitems.unitcost*lineitems.quantity)', '9', '', '0', '', 'currency', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'total wt.', 'format((lineitems.unitweight*lineitems.quantity),2)', 'right', 'format(sum(lineitems.unitweight*lineitems.quantity),2)', '11', 'lineitems.unitweight*lineitems.quantity', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'type', 'invoices.type', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'date', 'if(invoices.type=\"Invoice\",invoices.invoicedate,invoices.orderdate)\r\n', 'left', '', '3', '', '1', '', 'date', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('5', 'tax', 'lineitems.taxable', 'center', '', '10', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('6', 'id', 'tax.id', 'center', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('6', 'name', 'concat(\"<strong>\",tax.name,\"</strong>\")', 'left', '', '1', NULL, '0', '', 'noencoding', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('6', 'percentage', 'concat(tax.percentage,\"%\")', 'left', '', '2', 'tax.percentage', '0', '98%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('7', 'id', 'productcategories.id', 'center', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('7', 'name', 'productcategories.name', 'left', '', '1', '', '0', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('7', 'date created', 'productcategories.creationdate', 'left', '', '2', 'productcategories.creationdate', '0', '', 'datetime', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('8', 'name', 'childproducts.partname', 'left', '', '1', '', '1', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('8', 'part number', 'childproducts.partnumber', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('18', 'id', 'clients.id', 'center', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('18', 'type', 'clients.type', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('18', 'name', 'IF(clients.company != \'\', CONCAT(clients.company,IF(clients.lastname != \'\' OR clients.firstname != \'\', CONCAT(\' (\',if(clients.lastname != \'\', clients.lastname, \'{blank}\'),\', \',if(clients.firstname != \'\', clients.firstname, \'{blank}\'),\')\'), \'\')), IF(clients.lastname != \'\' OR clients.firstname != \'\', CONCAT(if(clients.lastname != \'\', clients.lastname, \'{blank}\'),\', \',if(clients.firstname != \'\', clients.firstname, \'{blank}\')), \'\'))', 'left', '', '2', 'concat(clients.company,clients.lastname,clients.firstname)', '1', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('22', 'name', 'clientemailprojects.name', 'left', '', '0', '', '0', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('22', 'last run', 'clientemailprojects.lastrun', 'right', '', '1', '', '0', '', 'datetime', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('25', 'amount/percent', 'discounts.value', 'right', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('25', 'type', 'discounts.type', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('25', 'name', 'discounts.name', 'left', '', '0', '', '0', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('300', 'name', 'shippingmethods.name', 'left', '', '1', '', '0', '99%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('300', 'estimate', 'shippingmethods.canestimate', 'center', '', '2', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('300', 'priority', 'shippingmethods.priority', 'right', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('301', 'name', 'paymentmethods.name', 'left', '', '1', '', '0', '99%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('301', 'type', 'paymentmethods.type', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('301', 'id', 'paymentmethods.id', 'center', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('301', 'priority', 'paymentmethods.priority', 'center', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('301', 'online process', 'paymentmethods.onlineprocess', 'center', '', '4', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('302', 'name', 'invoicestatuses.name', 'left', '', '0', '', '1', '99%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('302', 'priority', 'invoicestatuses.priority', 'right', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('302', 'inactive', 'invoicestatuses.inactive', 'center', '', '2', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('303', 'type', 'aritems.type', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('303', 'date', 'aritems.itemdate', 'left', '', '3', '', '0', '', 'date', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('303', 'client', 'if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)', 'left', '', '4', '', '1', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('303', 'doc amt', 'aritems.amount', 'right', 'SUM(aritems.amount)', '5', '', '0', '', 'currency', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('303', 'amt due', 'aritems.amount-aritems.paid', 'right', 'SUM(aritems.amount-aritems.paid)', '6', '', '0', '', 'currency', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('303', 'doc ref', 'aritems.relatedid', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('303', 'status', 'aritems.status', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('304', 'id', 'receipts.id', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('304', 'client', 'if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)', 'left', '', '4', '', '1', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('304', 'RTP', 'receipts.readytopost', 'center', '', '1', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('304', 'amount', 'receipts.amount', 'right', 'sum(receipts.amount)', '6', '', '0', '', 'currency', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('304', 'payment', 'IF(receipts.paymentmethodid = -1,concat( concat(\"Other... (\", receipts.paymentother), \")\"), paymentmethods.name)', 'left', '', '5', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('304', 'date', 'receipts.receiptdate', 'left', '', '3', '', '0', '', 'date', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('304', 'status', 'receipts.status', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('305', 'address', 'concat(if(addresses.title != \'\',concat(\'[b]\',addresses.title,\'[/b][br]\'),\'\'),IF(addresses.address1!=\'\',addresses.address1,\'\'),if(addresses.address2 != \'\',concat(\'[br]\', addresses.address2),\'\'),if(addresses.city != \'\',concat(\'[br]\',addresses.city,\', \',if(addresses.state != \'\',addresses.state, \'\'),\' \',if(addresses.postalcode != \'\', addresses.postalcode, \'\')),\'\'),if(addresses.country != \'\',concat(\'[br]\',addresses.country),\'\'))', 'left', '', '0', '', '1', '100%', 'bbcode', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('305', 'default ship to', 'addresstorecord.defaultshipto', 'center', '', '4', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('305', 'ship to name', 'addresses.shiptoname', 'left', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('305', 'phone', 'addresses.phone', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('305', 'email', 'addresses.email', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('305', 'primary', 'addresstorecord.primary', 'center', '', '5', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('306', 'id', 'addresses.id', 'left', '', '0', '', '0', '', NULL, '0');