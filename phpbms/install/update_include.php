<?php

define("APP_DEBUG",false);
define("noStartup",true);

require("../include/session.php");

	class updater{

		// database object
		var $db;

		// module list
		var $list;

		var $phpbmsSession;


		function updater(){

			$this->phpbmsSession = new phpbmsSession;

			if($this->phpbmsSession->loadDBSettings(false)){

				@ include_once("include/db.php");

				$this->db = new db(false);
				$this->db->stopOnError = false;
				$this->db->showError = false;
				$this->db->logError = false;

			} else
				$error = new appError(-300,"","",true,true,false);

			if(!$this->db->connect())
				$error = new appError(-400,"Could not connect to database server.\n\n".$this->db->getError(),"Database Error",true,true,false);

			if(!$this->db->selectSchema())
				$error = new appError(-410,"Could not open schema ".$this->db->schema,"Database Error", true, true, false);

		}//end function init


		function buildList(){

			$thedir = @ opendir("../modules/");

			$modules = array();

			//this helps build the modules array
			// each included modules version.php should add to the
			// array
			while($entry = readdir($thedir)){

				if($entry != "." && $entry != ".." && $entry != "base" && $entry != "sample" && is_dir("../modules/".$entry)){

					if(file_exists("../modules/".$entry."/install/update.php") && file_exists("../modules/".$entry."/version.php")){

						include("../modules/".$entry."/version.php");

					}//endif

				}//endif

			}//end if

			//Next we add the base version in
			include("../phpbmsversion.php");

			//go retrieve current versions
			foreach($modules as $key=>$value)
				$modules[$key]["currentversion"] = $this->getCurrentVersion($key);

			$this->list = $modules;

		}//end function buildList


		function getMySQLVersion(){

			$querystatement = "SELECT VERSION() AS ver";
			$queryresult = $this->db->query($querystatement);
			if($this->db->error)
				$error = new appError(-425,"Could not retrieve mysql verson. ","Database Error", true, true, false);

			$therecord = $this->db->fetchArray($queryresult);

			return $therecord["ver"];

		}//endif


		function getCurrentVersion($module){

			$querystatement = "
				SELECT
					version
				FROM
					modules
				WHERE
					name = '".$module."'";

			$queryresult = $this->db->query($querystatement);
			if($this->db->error)
				$error = new appError(-600,"Could not retrieve current version information for ".$module.": ".$this->db->error,"Cannot load module information",true,true,false);

			if($this->db->numRows($queryresult)){

				$therecord = $this->db->fetchArray($queryresult);

				return floatval($therecord["version"]);

			} else
				return 0;

		}//end function getCurrentVersion


		function checkBaseUpdate(){

			return ($this->list["base"]["version"] == $this->list["base"]["currentversion"]);

		}//end function showBaseUpdate


		function showModulesUpdate(){

			?>
			<table id="moduleTable" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th>module</th>
						<th>file version</th>
						<th>database version</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
			<?php

			ksort($this->list);

			foreach($this->list as $key=>$module){

				if($key != "base"){

					?>
					<tr>
						<td>
							<h3><strong><?php echo $module["name"]?></strong></h3>
							<p><?php echo $module["description"]?></p>
							<p class="notes"><strong>Requirements:</strong><?php echo $module["requirements"]?></p>
						</td>
						<td><?php echo $module["version"] ?></td>
						<td><?php echo $module["currentversion"] ?></td>

						<td class="moduleInstall">
							<?php
								if($module["version"] != $module["currentversion"]){
									if($module["currentversion"] == 0) {
							?>
								Not Installed
							<?php
									} else {
							?>

								<button class="Buttons moduleButtons" id="moduleButton<?php echo $key ?>">Update Module</button>
								<p><span class="" id="Results<?php echo $key?>"></span></p>

							<?php }} else {?>
								Versions Match<br />
								Update Unnecessary
							<?php }//endif version!=currentversion ?>
						</td>
					</tr>
					<?php

				}//end if

			}//end foreach

			?></tbody></table><?php

		}//end function showModuleUpdate

	}//end class
?>
