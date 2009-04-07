<?php
// The update proceess is typically simple, but if you have some complex
// update procedures (like the BMS module) not covered by the updateModuleAjax
// (located in the main install/installajax.php file) You can override the class.
//
// Just make sure your class has an 'update' method, and spits back JSON paired
// results

// Always instanciate the class to the variable $theModule. include the
// encapsulating objects db and phpbmsSession variables as well
// as the module name, and the path form the application to the modules's
// install folder
$theModule = new updateModuleAjax($this->db, $this->phpbmsSession, "recurringinvoices", "../modules/recurringinvoices/install/");

// Next define all the updates that are possible.  Each version
// corresponds to a sql file in the install folder named 'updatev[version].sql'
// that will be run while updating.
//
// List *all* the versions to update. The update process will ONLY run
// applicable upgrades, ignoring older versions not needed to upgrade.
$theModule->updateVersions = array(
					1.01,
					1.02,
					1.03
				);

?>
