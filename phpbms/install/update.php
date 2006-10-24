<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/
	function loadSettings() {	
		$settingsfile =  fopen("../settings.php","r");
		if($settingsfile){
			//loop through the settings file and load variables into the session 
			while( !feof($settingsfile)) {
				$line=fscanf($settingsfile,"%[^=]=%[^[]]",$key,$value);
				if ($line){
					$key=trim($key);
					$value=trim($value);
					if($key!="" and !strpos($key,"]")){	
						$startpos=strpos($value,"\"");
						$endpos=strrpos($value,"\"");
						if($endpos!=false)
							$value=substr($value,$startpos+1,$endpos-$startpos-1);
						$variables[$key]=$value;
					}
				}
				$line=NULL;
				$key=NULL;
				$value=NULL;
			}
			fclose($settingsfile);
			return $variables;
		} else return "Cannot open setting.php file";
	}

	
	function getNewVersion($dir="."){
		$file =  @ fopen($dir."/version.txt","r");
		$version=fgets($file,1024);
		@ fclose($file);
		return $version;
	}
	
	function showModules(){
		$vars=loadSettings();
		if(!is_array($vars)) {
			echo "<option>Could Not Open Settings File</option>";
			return false;
		}
		$dblink = @ mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);		
		@ mysql_select_db($vars["mysql_database"],$dblink);
		
		$querystatement="SELECT name,version FROM modules WHERE name!=\"base\" ";
		$queryresult=mysql_query($querystatement,$dblink);
		
		while($modulerecord=mysql_fetch_array($queryresult)){
			$newVersion=getNewVersion("../modules/".$modulerecord["name"]."/install");
			if($newVersion!=$modulerecord["version"])
				echo "<OPTION value=\"".$modulerecord["name"]."\">".$modulerecord["name"]." (".$modulerecord["version"]." -&gt; ".$newVersion.")</OPTION>\n";
		}
		
	}

	$version=getNewVersion();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>update phpBMS</title>
<link href="../common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css" />
<link href="../common/stylesheet/mozilla/pages/install.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../common/javascript/common.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">	
	function runCommand(command){
		var theURL="updatexml.php?command="+command;
		var adminName=getObjectFromID("username");
		var adminPass=getObjectFromID("password");
		var version=getObjectFromID("version");
		theURL+="&u="+encodeURIComponent(adminName.value);
		theURL+="&p="+encodeURIComponent(adminPass.value);
		theURL+="&v="+encodeURIComponent(version.value);

		var responseText= getObjectFromID(command+"results");
		loadXMLDoc(theURL,null,false);
		if(req.responseXML)
			response = req.responseXML.documentElement;
		else 
			alert(req.responseText);
		responseText.value+=response.firstChild.data+"\n";
	}
	
	function runModuleUpdate(){
		var themodule=getObjectFromID("modules");
		var responseText= getObjectFromID("moduleresults");
		if(themodule.value=="")
			alert("First, Select a module");
		else {
			var theURL="../modules/"+themodule.value+"/install/update.php";
			var adminName=getObjectFromID("username");
			var adminPass=getObjectFromID("password");
			theURL+="?u="+encodeURIComponent(adminName.value);
			theURL+="&p="+encodeURIComponent(adminPass.value);
			
			loadXMLDoc(theURL,null,false);
			if(req.responseXML)
				response = req.responseXML.documentElement;
			else alert(req.responseText);
			responseText.value+=response.firstChild.data+"\n";
		}
	}
</script>
</head>

<body>
<div class="bodyline" id="container">
	<h1>phpBMS v<?php echo $version ?> Update Instructions</h1>
	<p>&nbsp;</p>
	<h1>Before updating</h1>
		<p>
			It is always a good idea to backup all of your data and files before running any updates. 
			By downloading and decompressing this update, you may have already replaced script files 
			from the previous version of phpBMS. If you have decompressed these files to a separate 
			directory and have made custom changes directly to the system we recommend backing up 
			those files before continuing. 
		</p>
		<p>
			For the latest information about phpBMS, including help
			forums, mailing lists, wiki, and downloads, please check the <a href="http://www.phpbms.org">phpBMS Project
		web site</a>.</p>
		<p>&nbsp;</p>

		<h1>Updating the base system</h1>
		<div class="box">
		
	    	<h2 >Step 1 - Enter Administrator Log In</h2>
			<p>
				Only users with administrative rights can run the update procedure.<br />
				Enter the log in name of and administrator and verify both the database connection and the administrative log in.
			</p>
			<p>
				user name<br />
				<input name="name" type="text" id="username" size="32" maxlength="64" />
				<input name="name" type="hidden" id="version"  value="<?php echo $version ?>" />
			</p>
			<p>
				password<br />
				<input name="password" type="password" id="password" size="32" maxlength="24"  />
			</p>
			
			<p>
				<input type="button" value="Verify Log In" class="Buttons" onclick="runCommand('verifyLogin')" />
			</p>

    		<h3 >Administrative Login Results</h3>
    		<p>
    			<textarea name="results" id="verifyLoginresults" class="results" cols="80"rows="4"></textarea>
   			</p>

	</div>
	<div class="box" style="margin-bottom:10px;">
		<h2>Step 2 - Check for Updates </h2>
		<p>If the administrative login was successful, compare the unpacked version version to the version reported in the database.</p>
		<p>
			<input type="button" value="Check For Update" class="Buttons" onclick="runCommand('checkBaseUpdate')" />
		</p>
		<h3>Update Check Results</h3>
		<p>
			<textarea name="results" id="checkBaseUpdateresults" class="results" cols="80" rows="4"></textarea>
		</p>
	</div>

	<div class="box" style="clear:both;">
		<h2 >Step 3 - Update the Base Module</h2>
		<p>
			Click the &quot;Update Base Module&quot; button to run the core and base module update. 
		</p>
		<p>This may make changes to the database records and structure. </p>
		<p>
			<input type="button" value="Update Base Module" class="Buttons" onclick="runCommand('updateBaseVersion')" />
		</p>
		<h3>Base Module  Update Results</h3>
		<p>
			<textarea name="results" id="updateBaseVersionresults" class="results" cols="80" rows="10"></textarea>
		</p>
	</div>
	
	<p>&nbsp;</p>
	<h1>Updating Installed modules</h1>
	<div class="box">
		<h2>Step 4 - Update Additional Modules </h2>
		
		<p>
			Below is a list of additional installed modules that can be updated. To update a module, 
			select the module from the list, and click the &quot;update module &quot; button. 
		</p>
		<p> 
			Select a module to update <br />
			<select size="6" id="modules" name="modules">
				<?php showModules()?>
			</select>
        </p>
		<p>
			<input type="button" id="updatemodule" name="updatemodule" value="Update Module" class="Buttons" onclick="runModuleUpdate()" />
		</p>
		<h3>Results</h3>
		<p>
			<textarea name="results" id="moduleresults" class="results" cols="80" rows="11"></textarea>
		</p>
			
	</div>
		
	<p>&nbsp;</p>
	<h1>Complete the update</h1>
	<div class="box">
		<p><br />
			Before continuing to the login screen, you may want to clear your <strong>browser cache</strong> (temporary internet files), so that any new JavaScript and style sheet files will refresh properly. </p>
		<p>If phpBMS updated successfully, go to the log in screen.</p>
		<p><input type="button" id="login" name="login" value="Go to Log In Screen" class="Buttons" onclick="document.location='../'" /></p>
	</div>

</div></body>
</html>
