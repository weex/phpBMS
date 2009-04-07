INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('9', 'log in name', 'users.login', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('9', 'name', 'concat(users.firstname,\" \",users.lastname)', 'left', '', '0', '', '0', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('9', 'last login', 'users.lastlogin', 'left', '', '3', '', '0', '', 'datetime', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('9', 'revoked', 'users.revoked', 'center', '', '2', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('10', 'name', 'relationships.name', 'left', '', '0', '', '0', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('10', 'to', 'concat(totable.maintable, \'.\', relationships.tofield)', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('10', 'from', 'concat(fromtable.maintable, \'.\', relationships.fromfield)', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('10', 'inherent', 'relationships.inherint', 'center', '', '3', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('11', 'id', 'tabledefs.id', 'center', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('11', 'main table', 'tabledefs.maintable', 'left', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('11', 'display', 'tabledefs.displayname', 'left', '', '1', '', '0', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('11', 'type', 'tabledefs.type', 'center', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('12', 'done', 'notes.completed', 'center', '', '1', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('12', 'title', 'notes.subject', 'left', '', '0', '', '1', '99%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('16', 'id', 'reports.id', 'center', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('16', 'name', 'reports.name', 'left', '', '1', '', '0', '100%', 'noencoding', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('16', 'type', 'reports.type', 'left', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('16', 'order', 'reports.displayorder', 'center', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('17', 'id', 'usersearches.id', 'center', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('17', 'name', 'usersearches.name', 'left', '', '1', '', '0', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('17', 'user', 'if(usersearches.userid=0,\"<b>global</b>\",concat(users.lastname,\", \",users.firstname))', 'left', '', '2', NULL, '0', '', 'noencoding', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('19', 'id', 'menu.id', 'center', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('19', 'link', 'menu.link', 'left', '', '2', '', '1', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('19', 'access', 'if(menu.roleid=0,\'EVERYONE\',if(menu.roleid=-100,\'Administrators\',roles.name))', 'left', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('19', 'Item', 'menu.name', 'left', '', '1', '', '0', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('21', 'id', 'modules.id', 'center', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('21', 'name', 'modules.displayname', 'left', '', '1', '', '1', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('21', 'version', 'modules.version', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('23', 'complete', 'notes.completed', 'center', '', '1', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('23', 'title', 'notes.subject', 'left', '', '2', '', '1', '65%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('23', 'repeat', 'notes.repeatname', 'left', '', '3', '', '1', '30%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('23', 'due date', 'if(notes.endtime is not null,concat(notes.enddate,\" \",notes.endtime),notes.enddate)', 'left', '', '0', '', '0', '', 'datetime', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('24', 'start', 'if(notes.starttime,concat(notes.startdate,\" \",notes.starttime),notes.startdate)', 'left', '', '0', 'concat(notes.startdate,\" \",notes.starttime)', '0', '', 'datetime', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('24', 'end', 'if(notes.endtime,concat(notes.enddate,\" \",notes.endtime),notes.enddate)', 'left', '', '1', 'concat(notes.enddate,\" \",notes.endtime)', '0', '', 'datetime', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('24', 'title', 'notes.subject', 'left', '', '2', '', '1', '65%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('24', 'status', 'notes.status', 'left', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('24', 'repeating', 'notes.repeatname', 'left', '', '5', '', '1', '30%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('24', 'location', 'notes.location', 'left', '', '4', '', '1', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('26', 'id', 'files.id', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('26', 'description', 'files.description', 'left', '', '2', '', '1', '99%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('26', 'file', 'files.name', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('26', 'download', 'files.id', 'center', '', '3', '', '0', '', 'filelink', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('27', 'attached', 'attachments.creationdate', 'left', '', '2', '', '0', '', 'datetime', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('27', 'file', 'concat(\"<b>\",files.name,\"</b>\")', 'left', '', '0', NULL, '0', '', 'noencoding', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('27', 'download', 'files.id', 'center', '', '3', '', '0', '', 'filelink', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('27', 'description', 'files.description', 'left', '', '1', '', '1', '100%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('200', 'name', 'roles.name', 'left', '', '1', '', '0', '99%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('200', 'inactive', 'roles.inactive', 'center', '', '2', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('200', 'id', 'roles.id', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('201', 'name', 'scheduler.name', 'left', '', '0', '', '0', '95%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('201', 'cron interval', 'scheduler.crontab', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('201', 'inactive', 'scheduler.inactive', 'center', '', '2', '', '0', '', 'boolean', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('202', 'time', 'log.stamp', 'left', '', '1', '', '0', '', 'datetime', '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('202', 'id', 'log.id', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('202', 'type', 'log.type', 'left', '', '2', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('202', 'value', 'log.value', 'left', '', '3', '', '1', '90%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('202', 'user', 'concat(users.firstname,\" \",users.lastname)', 'left', '', '4', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('202', 'ip address', 'log.ip', 'left', '', '5', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('203', 'name', 'tabs.name', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('203', 'location', 'tabs.location', 'left', '', '2', '', '1', '95%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('203', 'display order', 'tabs.displayorder', 'center', '', '3', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('203', 'id', 'tabs.id', 'left', '', '0', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('203', 'access', 'if(tabs.roleid=0,\'EVERYONE\',if(tabs.roleid=-100,\'Administrators\',roles.name))', 'left', '', '4', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('204', 'name', 'smartsearches.name', 'left', '', '0', '', '1', '99%', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('204', 'table', 'tabledefs.displayname', 'left', '', '1', '', '0', '', NULL, '0');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('204', 'module', 'modules.displayname', 'left', '', '2', '', '0', '', NULL, '0');