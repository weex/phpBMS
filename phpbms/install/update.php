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
				$line=NULL;
				$key=NULL;
				$value=NULL;
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
			}
			if(!isset($variables["mysql_pconnect"]))
			$variables["mysql_pconnect"]="true";
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
		if($vars["mysql_pconnect"]=="true")
			$dblink = @  mysql_pconnect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);
		else
			$dblink = @  mysql_connect($vars["mysql_server"],$vars["mysql_user"],$vars["mysql_userpass"]);
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>update phpBMS</title>
<link href="../common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css" />
<link href="../common/stylesheet/mozilla/pages/install.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../common/javascript/common.js" type="text/javascript"></script>
<script language="JavaScript" src="../common/javascript/moo/prototype.lite.js" type="text/javascript" ></script>
<script language="JavaScript" src="../common/javascript/moo/moo.fx.js" type="text/javascript" ></script>
<script language="JavaScript" src="../common/javascript/moo/moo.fx.pack.js" type="text/javascript" ></script>

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
	
	function changeModules(){
		var themodule=getObjectFromID("modules");
		var moduleButton= getObjectFromID("updatemodule");
		
		if(themodule.value==0)
			moduleButton.disabled="disabled";
		else
			moduleButton.disabled=false;
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
	<h1>phpBMS v<?php echo $version ?> Update</h1>
	<h2>Before updating</h2>
		<p>Backup all of your data and program files before running any update.</p>
		<p>
			By downloading and decompressing this update, you may have already replaced script files 
			from the previous version of phpBMS. If you have decompressed these files to a separate 
			directory and have made custom changes directly to the system we recommend backing up 
			those files before continuing. 
		</p>
		<p>For the latest information about phpBMS check the <a href="http://www.phpbms.org">phpBMS Project web site</a>.</p>

		<h2>Updating the base system</h2>
		
		<h3>Step 1 - Verify administrative privileges</h3>
		<p>
	Only users with administrative rights can run the update.</p>
		<p>			name<br />
			<input name="name" type="text" id="username" size="32" maxlength="64" />
			<input name="name" type="hidden" id="version"  value="<?php echo $version ?>" />
    	</p>
		<p>
			password<br />
			<input name="password" type="password" id="password" size="32" maxlength="24"  />
		</p>
		
		<p>
			<input type="button" value="Verify" class="Buttons" onclick="runCommand('verifyLogin')" />
		</p>

		<h4>Verify Results</h4>
		<p>
			<textarea name="results" id="verifyLoginresults" class="results" cols="80" rows="2"></textarea>
		</p>

		<h3>Step 2 - Check for Updates </h3>
		<p>
			<input type="button" value="Check Availability" class="Buttons" onclick="runCommand('checkBaseUpdate')" />
		</p>
		<h4>Availability Results</h4>
		<p>
			<textarea name="results" id="checkBaseUpdateresults" class="results" cols="80" rows="2"></textarea>
		</p>

		<h3 >Step 3 - Update the Base Module (phpBMS core)</h3>
		<p>
			<input type="button" value="Update Core" class="Buttons" onclick="runCommand('updateBaseVersion')" />
		</p>
		<h3>Core Update Results</h3>
		<p>
			<textarea name="results" id="updateBaseVersionresults" class="results" cols="80" rows="8"></textarea>
		</p>
	
	<h2>Updating Installed modules</h2>
		<h3>Step 4 - Update Additional Modules</h3>
		<p>Select a module to update</p>
		<p>
			<select id="modules" name="modules" onchange="changeModules()">
				<option value="0">Select a module to update...</option>
				<?php showModules()?>
			</select>
			<input type="button" id="updatemodule" name="updatemodule" value="update" class="Buttons" onclick="runModuleUpdate()" disabled="disabled"/>
        </p>
		<h4>Module Update Results</h4>
		<p>
			<textarea name="results" id="moduleresults" class="results" cols="80" rows="8"></textarea>
		</p>
			
		
	<h2>Complete the update</h2>
		<p>Make sure you clear your <strong>browser cache</strong> (temporary internet files). Some updates may require the resetting of session files.</p>
		<p><input type="button" id="login" name="login" value="Log In" class="Buttons" onclick="document.location='../'" /></p>

</div>
<p class="tiny" align="center"> $Rev$ |  $LastChangedDate$
</p>
</body>
</html>
