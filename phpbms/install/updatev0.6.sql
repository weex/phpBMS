ALTER TABLE choices DROP selected;
ALTER TABLE notes CHANGE followup assignedtodate date;
ALTER TABLE notes ADD assignedtotime time;
ALTER TABLE notes CHANGE `type` `type` varchar(2);
ALTER TABLE notes CHANGE importance importance int not null default 0;
ALTER TABLE notes ADD parentid int;
ALTER TABLE notes ADD startdate date;
ALTER TABLE notes ADD starttime time;
ALTER TABLE notes ADD enddate date;
ALTER TABLE notes ADD endtime time;
ALTER TABLE notes ADD completed tinyint not null default 0;
UPDATE notes SET completed=beenread;
ALTER TABLE notes DROP beenread;
ALTER TABLE notes ADD private tinyint not null default 0;
ALTER TABLE notes ADD status varchar(64);
ALTER TABLE notes ADD completeddate date;
ALTER TABLE notes ADD location varchar(128);
ALTER TABLE notes ADD category varchar(128);
ALTER TABLE notes ADD assignedbyid int not null default 0;
ALTER TABLE notes ADD repeattype varchar(20);
ALTER TABLE notes ADD repeat tinyint not null default 0;
ALTER TABLE notes ADD repeatuntildate date;
ALTER TABLE notes ADD repeattimes int not null default 0;
ALTER TABLE notes ADD repeatfrequency smallint default 1;
ALTER TABLE notes ADD repeatdays CHAR(7);
UPDATE notes set type="NT",importance=0;

ALTER TABLE tablefindoptions ADD accesslevel int not null default 0;

ALTER TABLE reports ADD accesslevel int not null default 0;

ALTER TABLE usersearches ADD accesslevel int not null default 0;

UPDATE menu SET accesslevel=10 WHERE accesslevel=0;

ALTER TABLE tableoptions ADD accesslevel int not null default 0;

ALTER TABLE tablecolumns ADD `format` ENUM("date","time","currency","boolean","datetime");

DELETE FROM choices;
DELETE FROM menu;
DELETE FROM reports;
DELETE FROM tablecolumns;
DELETE FROM tablecolumns;
DELETE FROM tabledefs;
DELETE FROM tablefindoptions;
DELETE FROM tableoptions;
DELETE FROM tablesearchablefields;