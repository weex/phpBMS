ALTER TABLE clients CHANGE homephone homephone varchar(32);
ALTER TABLE clients CHANGE workphone workphone varchar(32);
ALTER TABLE clients CHANGE otherphone otherphone varchar(32);
ALTER TABLE clients CHANGE mobilephone mobilephone varchar(32);
ALTER TABLE clients CHANGE fax fax varchar(32);

ALTER TABLE clients ADD  `taxareaid` int(11) default '0';

ALTER TABLE products ADD upc VARCHAR(128);