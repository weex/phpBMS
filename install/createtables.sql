CREATE TABLE choices (
  id int(11) NOT NULL auto_increment,
  listname varchar(64) NOT NULL default '',
  thevalue varchar(64) default NULL,
  UNIQUE KEY theid (id)
) TYPE=MyISAM PACK_KEYS=0;

CREATE TABLE menu (
  id int(11) NOT NULL auto_increment,
  name varchar(64) NOT NULL default '',
  link varchar(128) NOT NULL default '',
  parentid int(11) NOT NULL default '0',
  displayorder int(11) NOT NULL default '0',
  createdby int(11) NOT NULL default '0',
  modifiedby int(11) default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifieddate timestamp(14) NOT NULL,
  accesslevel int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE modules (
  id int(11) NOT NULL auto_increment,
  displayname varchar(128) NOT NULL default '',
  name varchar(64) NOT NULL default '',
  description text,
  version varchar(32) default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE notes (
  assignedtoid int(11) default NULL,
  attachedid int(11) default NULL,
  attachedtabledefid int(11) default NULL,
  content text,
  assignedtodate date default NULL,
  id int(11) NOT NULL auto_increment,
  subject varchar(128) default NULL,
  type char(2) default NULL,
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifiedby int(11) default NULL,
  modifieddate timestamp(14) NOT NULL,
  importance int(11) NOT NULL default '0',
  parentid int(11) default NULL,
  startdate date default NULL,
  enddate date default NULL,
  completed tinyint(4) NOT NULL default '0',
  private tinyint(4) NOT NULL default '0',
  status varchar(64) default NULL,
  completeddate date default NULL,
  location varchar(128) default NULL,
  category varchar(128) default NULL,
  repeattype varchar(20) default NULL,
  `repeat` tinyint(4) NOT NULL default '0',
  repeatuntildate date default NULL,
  repeatfrequency smallint(6) default NULL,
  repeatdays varchar(7) default NULL,
  assignedtotime time default NULL,
  starttime time default NULL,
  endtime time default NULL,
  repeattimes int(11) NOT NULL default '-1',
  assignedbyid int(11) NOT NULL default '0',
  UNIQUE KEY theid (id)
) TYPE=MyISAM PACK_KEYS=0;

CREATE TABLE relationships (
  id int(11) NOT NULL auto_increment,
  tofield varchar(32) NOT NULL default '',
  name varchar(128) NOT NULL default '',
  fromfield varchar(32) NOT NULL default '',
  fromtableid int(11) NOT NULL default '0',
  totableid int(11) NOT NULL default '0',
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifiedby int(11) default '0',
  modifieddate timestamp(14) NOT NULL,
  inherint tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY fromtable (fromtableid)
) TYPE=MyISAM PACK_KEYS=0;

CREATE TABLE reports (
  description text,
  id int(11) NOT NULL auto_increment,
  name varchar(64) default NULL,
  reportfile varchar(128) default NULL,
  type varchar(32) default NULL,
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifiedby int(11) default NULL,
  modifieddate timestamp(14) NOT NULL,
  tabledefid int(11) NOT NULL default '0',
  displayorder int(11) NOT NULL default '0',
  accesslevel int(11) NOT NULL default '0',
  UNIQUE KEY theid (id)
) TYPE=MyISAM PACK_KEYS=0;


CREATE TABLE tablecolumns (
  id int(11) NOT NULL auto_increment,
  tabledefid int(11) NOT NULL default '0',
  name varchar(64) NOT NULL default '',
  `column` varchar(255) NOT NULL default '',
  align varchar(16) NOT NULL default '',
  footerquery varchar(255) default '',
  displayorder int(11) NOT NULL default '0',
  sortorder varchar(128) default '',
  wrap tinyint(1) NOT NULL default '0',
  size varchar(16) NOT NULL default '',
  format enum('date','time','currency','boolean','datetime') default NULL,
  PRIMARY KEY  (id),
  KEY tabledef (tabledefid),
  KEY displayorder (displayorder)
) TYPE=MyISAM;

CREATE TABLE tabledefs (
  editfile varchar(128) default NULL,
  displayname varchar(64) default NULL,
  id int(11) NOT NULL auto_increment,
  maintable varchar(64) NOT NULL default '',
  createdby int(11) NOT NULL default '0',
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  modifiedby int(11) default NULL,
  modifieddate timestamp(14) NOT NULL,
  querytable varchar(255) NOT NULL default '',
  addfile varchar(100) default '',
  deletebutton varchar(32) default '',
  defaultwhereclause varchar(128) default '',
  defaultsortorder varchar(255) default '',
  defaultsearchtype varchar(64) default '',
  defaultcriteriafindoptions varchar(128) default '',
  defaultcriteriaselection varchar(128) default '',
  type varchar(16) NOT NULL default 'table',
  moduleid int(11) NOT NULL default '0',
  UNIQUE KEY theid (id)
) TYPE=MyISAM PACK_KEYS=0;


CREATE TABLE tablefindoptions (
  id int(11) NOT NULL auto_increment,
  tabledefid int(11) NOT NULL default '0',
  name varchar(64) NOT NULL default '',
  search varchar(255) NOT NULL default '',
  displayorder int(11) NOT NULL default '0',
  accesslevel int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY tabledef (tabledefid)
) TYPE=MyISAM;

CREATE TABLE tableoptions (
  id int(11) NOT NULL auto_increment,
  tabledefid int(11) NOT NULL default '0',
  name varchar(64) NOT NULL default '',
  `option` varchar(128) NOT NULL default '',
  othercommand tinyint(1) NOT NULL default '0',
  accesslevel int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY tabledef (tabledefid)
) TYPE=MyISAM;

CREATE TABLE tablesearchablefields (
  id int(11) NOT NULL auto_increment,
  tabledefid int(11) NOT NULL default '0',
  field varchar(255) NOT NULL default '',
  name varchar(64) NOT NULL default '',
  displayorder int(11) NOT NULL default '0',
  type varchar(16) NOT NULL default 'field',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE users (
  id int(11) NOT NULL auto_increment,
  login varchar(64) NOT NULL default '',
  password blob,
  firstname varchar(64) default NULL,
  lastname varchar(64) default NULL,
  creationdate datetime NOT NULL default '0000-00-00 00:00:00',
  revoked tinyint(1) NOT NULL default '0',
  accesslevel int(11) default '1',
  createdby int(11) NOT NULL default '0',
  modifiedby int(11) default '0',
  lastlogin datetime default NULL,
  modifieddate timestamp(14) NOT NULL,
  email varchar(128) default '',
  phone varchar(32) default '',
  department varchar(128) default '',
  employeenumber varchar(64) default '',
  PRIMARY KEY  (id),
  UNIQUE KEY theid (id)
) TYPE=MyISAM PACK_KEYS=0;

CREATE TABLE usersearches (
  id int(11) NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  tabledefid int(11) NOT NULL default '0',
  name varchar(128) default '',
  sqlclause text,
  type char(3) NOT NULL default 'SCH',
  accesslevel int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY tabledefid (tabledefid),
  KEY thetype (type),
  KEY user (userid)
) TYPE=MyISAM;