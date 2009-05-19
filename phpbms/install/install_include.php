<?php
	class installer{

		var $db;

		function installer($db){

			$this->db = $db;

		}//end function init


		function processSQLfile($sqlfile){

			$return = "";

			$fileReturn = $this->db->processSQLFile($sqlfile);

			if(count($fileReturn->errors)){

				foreach($fileReturn->errors as $error)
					$return = "\n\n".$error;

				$return = substr($return, 2);

				return $return;

			} else
				return true;

		}//end function processSQLfile

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
                                                if($key!="requirements")
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

                        if(isset($this->list["bms"]))
                                $this->_displayModuleLine("bms", $this->list["bms"], "bmsModule");

			foreach($this->list as $key=>$module)
				if($key != "base" && $key != "bms")
                                        $this->_displayModuleLine($key, $module);


			?></tbody></table><?php

		}//end function displayInstallTable


                function _displayModuleLine($name, $module, $class = null){

                        ?>
                        <tr <?php if($class)  echo 'class="'.$class.'"';?>>
                                <td>
                                        <h3><strong><?php echo $module["name"]?></strong></h3>
                                        <p><?php echo $module["description"]?></p>
                                        <p class="notes"><strong>Requirements:</strong> <?php echo $module["requirements"]?></p>
                                </td>
                                <td><?php echo $module["version"] ?></td>
                                <td class="moduleInstall">
                                        <button class="Buttons moduleButtons" id="moduleButton<?php echo $name ?>">Install Module</button>
                                        <p><span class="" id="Results<?php echo $name?>"></span></p>
                                </td>
                        </tr>
                        <?php

                }//end

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
