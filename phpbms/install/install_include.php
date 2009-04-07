<?php
	class installer{

		var $db;

		function installer($db){

			$this->db = $db;

		}//end function init


		function createTables($sqlfile){

			$sqlstatement="";
			$thereturn = "";
			$line = 1;

			$filePointer = @ fopen($sqlfile,"r");

			if(!$filePointer)
				return "Could not open SQL file: ".$sqlfile;

			while(!feof($filePointer)) {

				$createstatement .= @ fgets($filePointer,1024);

				if(strpos($createstatement,";")){

					$this->db->query(trim($createstatement));

					if($this->db->error)
						return "Error creating tables on line ".$line.": ".$this->db->error." SQL Statement: ".trim($createstatement);

					$createstatement = "";

				}//end if;

				$line++;

			}//end while

			return true;

		}//end function createTables


		function processSQLfile($filename){

			$thefile = @ fopen($filename,"r");
			$line = 1;
			$thereturn = "";

			if(!$thefile)
				return "Could not open the SQL file ".$filename;

			while(!feof($thefile)) {

				$sqlstatement .= @ trim(fgets($thefile));

				// we look for the ; at the end of the line
				// If it is not there, we keep adding to the sql statement
				if(strrpos($sqlstatement,";") == strlen($sqlstatement)-1){

					$this->db->query(trim($sqlstatement));
					if($this->db->error)
						$thereturn .= "Error processing SQL file '".$filename."' on line ".$line.": ".$this->db->error." - SQL Statement: ".$sqlstatement."\n";

					$sqlstatement = "";

				}//end if;

				$line++;

			}//end while

			if($thereturn)
				return $thereturn;
			else
				return true;

		}//end function

	}//end class



	class modules{

		var $list = array();

		// this loads the modules in to an array
		// type looks for either update, to make sure the
		// module folder has an install folder, and inside the install
		// folder is an install.php or update .php (depending on type)
		// and that it has a version.php file;
		function modules($type = "install"){

			$currdirectory = @getcwd();

			$thedir= @ opendir("../modules/");

			$modules = array();

			//this helps build the modules array
			// each included modules version.php should add to the
			// array
			while($entry = readdir($thedir)){

				if($entry != "." && $entry != ".." && $entry != "base" && $entry != "sample" && is_dir("../modules/".$entry)){

					if(file_exists("../modules/".$entry."/install/".$type.".php") && file_exists("../modules/".$entry."/version.php")){

						include("../modules/".$entry."/version.php");

					}//endif

				}//endif

			}//end if

			@ chdir ($currdirectory);

			$this->list = $modules;

		}//end function init


		function displpayJS(){

			echo 'modules = Array();'."\n";

			foreach($this->list as $name=>$module)
				if(is_array($module) && $name != "base"){

					echo 'modules["'.$name.'"] = Array()'."\n";
					foreach($module as $key=>$value)
						echo 'modules["'.$name.'"]["'.$key.'"] = "'.$value.'";'."\n";
				}//endif


		}//end function displayJS


		function displayInstallTable(){

			?>
			<table id="moduleTable" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th>module</th>
						<th>version</th>
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
							<p class="notes"><strong>Requirements:</strong> <?php echo $module["requirements"]?></p>
						</td>
						<td><?php echo $module["version"] ?></td>
						<td class="moduleInstall">
							<button class="Buttons moduleButtons" id="moduleButton<?php echo $key ?>">Install Module</button>
							<p><span class="" id="Results<?php echo $key?>"></span></p>
						</td>
					</tr>
					<?php

				}//end if

			}//end foreach

			?></tbody></table><?php

		}//end function displayInstallTable

	}//end class



	class installUpdateBase{

		var $db;
		var $phpbmsSession;

		function returnJSON($success, $details, $extras = null){

			$thereturn["success"] = $success;
			$thereturn["details"] = $details;

			if($extras)
				$thereturn["extras"] = $extras;

			return json_encode($thereturn);

		}//endfunction returnJSON

	}//end class installUpdateBase


?>
