UPDATE tablecolumns SET `column`="notes.repeat+if(notes.parentid is null, 0,1)" WHERE id=124;
UPDATE tablecolumns SET `column`="notes.repeat",format="boolean" WHERE id=129;