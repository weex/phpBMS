INSERT INTO `invoicestatuses` (`id`, `name`, `setreadytopost`, `invoicedefault`, `defaultassignedtoid`, `inactive`, `priority`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (1,'Open',0,1,NULL,0,0,1,NOW(),1,NOW());
INSERT INTO `invoicestatuses` (`id`, `name`, `setreadytopost`, `invoicedefault`, `defaultassignedtoid`, `inactive`, `priority`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (2,'Committed',0,0,NULL,0,10,1,NOW(),1,NOW());
INSERT INTO `invoicestatuses` (`id`, `name`, `setreadytopost`, `invoicedefault`, `defaultassignedtoid`, `inactive`, `priority`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (3,'Packed',0,0,NULL,0,20,1,NOW(),1,NOW());
INSERT INTO `invoicestatuses` (`id`, `name`, `setreadytopost`, `invoicedefault`, `defaultassignedtoid`, `inactive`, `priority`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES (4,'Shipped',1,0,NULL,0,30,1,NOW(),1,NOW());