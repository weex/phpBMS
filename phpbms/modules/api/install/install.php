<?php
// The install proceess is typically simple, but if you have some complex
// installation procedures not covered by the installModuleAjax (located in the
// main install/installajax.php file) You can override the class.
//
// Just make sure your class has an 'install' method, and spits back JSON paired
// results

// Always instanciate the class to the variable $theModule. include the
// encapsulating objects db and phpbmsSession variables as well
// as the path form the application to the modules's install folder
$theModule = new installModuleAjax($this->db, $this->phpbmsSession, "../modules/api/install/");

// by default the install class will try to run a createtables.sql file located
// in the modules install directory.  In addition, it will run individual
// SQL files that match table name entries in the tables array (defined below)
// that should also be located in the module's install folder.
$theModule->tables = array(
			"modules",
            "tablecolumns",
            "tabledefs",
            "tablefindoptions",
            "tableoptions",
            "tablesearchablefields"
			);

?>
