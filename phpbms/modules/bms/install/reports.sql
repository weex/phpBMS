INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:bac1d6eb-b2bb-9aa0-77c0-ff7f9046ca75', 'Invoice', 'PDF Report', '3', '100', '0', 'modules/bms/report/invoices_pdfinvoice.php', 'This report will gerneate and display an invoice in PDF format (which can be printed or saved).  The PDF file will contain one page per invoice.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:a34dd4b5-6942-2b14-4a58-74345dce48de', 'Work Order', 'PDF Report', '3', '100', '20', 'modules/bms/report/invoices_pdfworkorder.php', 'This report will gerneate and display a work order  in PDF format (which can be printed or saved).  The PDF file will contain one page per work order.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:34a69580-6fbc-d04c-ed3e-f0e497a9a9b2', 'Packing List', 'PDF Report', '3', '100', '0', 'modules/bms/report/invoices_pdfpackinglist.php', 'This report will gerneate and display an invoice packing list in PDF format (which can be printed or saved).  The PDF file will contain one page per invoice packing list.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:07f58303-d6e9-a032-01ad-0097d59b3c04', 'Labels - Folder', 'PDF Report', '2', '50', '0', 'modules/bms/report/clients_folderlabels.php', 'Avery 5160 or compatible (3x10) Instructor Folder labels. \r\n\r\n **MAKE SURE when printing the pdf file, to TURN OFF the option \"shrink oversized pages to paper size\".**', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:030e7d95-4542-b37c-3cac-a18ff5f4b8ff', 'Labels - Mailing', 'PDF Report', '2', '50', '0', 'modules/bms/report/clients_mailinglabels.php', 'Avery 5160 or compatible (3x10) Instructor Folder labels. \r\n\r\n **MAKE SURE when printing the pdf file, to TURN OFF the option \"shrink oversized pages to paper size\".**', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:c4a34fa0-16b5-dd85-cf63-5c8b543bb9c3', 'Labels - Shipping', 'PDF Report', '2', '50', '0', 'modules/bms/report/clients_shippinglabels.php', 'Avery 5160 or compatible (3x10) Instructor Folder labels. \r\n\r\n **MAKE SURE when printing the pdf file, to TURN OFF the option \"shrink oversized pages to paper size\".**', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:a502aa38-4ae8-9aa7-2795-4a05a4814637', 'Labels - Shipping', 'PDF Report', '3', '60', '0', 'modules/bms/report/invoices_shippinglabels.php', 'Avery 5160 or compatible (3x10) Instructor Folder labels. \r\n\r\n **MAKE SURE when printing the pdf file, to TURN OFF the option \"shrink oversized pages to paper size\".**', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:e3ef15d4-1bf5-36a1-cc05-ee44025ad619', 'Totals - Custom', 'report', '3', '50', '30', 'modules/bms/report/invoices_totals.php', 'Creater your own custom invoice totaling report, specify groupings, totals, averages and whether to display summary, invoice, and invoice detail information.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:5ec9b1fb-f3c6-26c7-f1bc-bec4ac8448fd', 'Totals - Amt. w/  Invoices', 'report', '3', '50', '30', 'modules/bms/report/invoices_totals_amtwinv.php', 'Basic totals report. Shows invoice total, subtotal and amount due fields  and displaying indidivdual invoice information.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:960ec744-4955-81d1-8170-e56251603e4b', 'Totals - Amt. w/ Invoices + Line Items', 'report', '3', '50', '30', 'modules/bms/report/invoices_totals_amtwinvlineitems.php', 'Basic totals report. Shows invoice total, subtotal and amount due fields  and displaying indidivdual invoice and line item information.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:47407854-cb7f-f1e3-bc44-1b3979eae9b0', 'Totals - Grouped by Acct. Manager', 'report', '3', '50', '30', 'modules/bms/report/invoices_totals_acctmngers.php', 'Totals report grouping by client account manager', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:05de9afc-c82c-ed66-9403-184aa3f07a1c', 'Totals - Grouped by Shipping Method', 'report', '3', '50', '30', 'modules/bms/report/invoices_totals_shippingmethod.php', 'Totals report including shipping ammount grouped by shipping method', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:60c71b67-5cf1-6d1b-6d96-a4dfe9bbd651', 'Totals - Grouped by Payment Method', 'report', '3', '50', '30', 'modules/bms/report/invoices_totals_payment.php', 'Totals report grouped by payment method.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:7a7672ef-f11c-9a6a-5640-708c50cadd29', 'Totals - Grouped by Invoice Lead Source', 'report', '3', '50', '30', 'modules/bms/report/invoices_totals_leadsource.php', 'Totals - Grouped by invoice lead source', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:49c0907c-9253-4fcb-0717-37952dd0ef4e', 'Quote', 'PDF Report', '3', '100', '20', 'modules/bms/report/invoices_pdfquote.php', 'PDF report for quote.  Does not include amount due.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:a278af28-9c34-da2e-d81b-4caa36dfa29f', 'Sales History', 'report', '4', '100', '30', 'modules/bms/report/products_saleshistory.php', 'Sales History for product including costs, average price and quantities.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:1908b03c-cacc-f03a-6d22-21fdef123f65', 'Purchase History', 'report', '2', '10', '0', 'modules/bms/report/clients_purchasehistory.php', 'Client purchase history', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:858702da-1b85-3a62-c20f-6b1593140a64', 'Totals - Custom', 'report', '5', '50', '30', 'modules/bms/report/lineitems_totals.php', 'Creat your own custom line item  totaling report, specify groupings, totals, averages and whether to display summary, invoice, and invoice detail information.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:28cf69cb-60de-bbed-df15-ea98842b6924', 'Totals - Product Categories', 'report', '5', '50', '30', 'modules/bms/report/lineitems_totals_productcategories.php', 'Totals report grouped first by product category and then by product.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:274d3dfa-ec52-74d2-630f-0c432a6e1ea5', 'Totals - Product', 'report', '5', '50', '30', 'modules/bms/report/lineitems_totals_products.php', 'Totals report grouped by product displaying line items', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:68b6258a-6902-f705-19f5-d2707bd78b35', 'Totals - Lead Source', 'report', '5', '50', '30', 'modules/bms/report/lineitems_totals_leadsource.php', 'Totals grouped by invoice lead source and product', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:b552c34f-64b9-5a89-15b3-c5d717644b81', 'Client Notes Summary', 'PDF Report', '2', '10', '0', 'modules/bms/report/clients_notesummary.php', 'Print all notes associated with he client and any notes associated with client invoices.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:aca42dbe-68c9-e966-c174-ed938e9b880a', 'Totals - Tax', 'report', '3', '50', '30', 'modules/bms/report/invoices_totals_tax.php', 'Tax Totals', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:e54cee32-b3c9-82cc-50c8-14848ece8e90', 'Receipt', 'PDF Report', '304', '10', '80', 'modules/bms/report/receipts_pdf.php', 'PDF print out of receipt for processing or client records', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:0df82ecf-5f05-56bd-18c3-e7cb27c0cf8a', 'Client Statements', 'PDF Report', '303', '10', '80', 'modules/bms/report/aritems_clientstatement.php', 'Client AR statement balances and activity.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:e25bdb7a-93be-b1d6-a292-cdec89c0c9fc', 'Summary', 'report', '303', '10', '80', 'modules/bms/report/aritems_summary.php', 'Items grouped and totaled by clients, with grand totals.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:916f34d8-0997-162c-4350-d93c3d283241', 'Payment Type Totals', 'report', '304', '10', '80', 'modules/bms/report/receipts_pttotals.php', 'Totals grouped by payment method.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('reports:4851c350-4343-4dc3-4b7b-74c287de011b', 'Incoming Cash Flow', 'report', '3', '55', '50', 'modules/bms/report/incoming_cashflow.php', 'This report shows total incoming monies for a time period from both posted sales orders AND posted receipts. It can be grouped by week, month, quarter and year.\r\n\r\nThis report runs is unaffected by selected records, search or sort parameters.  It requires input of it\'s own start and end dates.', 1, NOW(), 1, NOW());