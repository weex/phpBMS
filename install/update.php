<?php 
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
		$version=fgets($file);
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

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>update phpBMS</title>
<link href="../common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css">
<style>
H1{ font-size:24px;}
H2{ font-size:20px;}
h3{ font-size:18px; border-bottom:1px solid #666666; padding:2px; margin-left:10px; color:#0B63A2; padding-left:80px; text-indent: -80px;}
h4{ font-size:14px; border-bottom:1px solid #666666; padding:2px; margin-left:25px; color:#0B63A2;}
</style>
<script language="JavaScript" src="../common/javascript/common.js"></script>
<script language="javascript">	
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
<div class="bodyline" style="padding:4px;">
	<h1>phpBMS v<?php echo $version ?> Update Instructions</h1>
	<h2>Before updating</h2>
	<div style="margin-left:10px;">
	It is always a good idea to backup all of your data before running any updates. By downloading and decompressing this update, you may have already replaced the scropt files from th e the previous version of phpBMS. If you have decompressed these files to a separate directory and have made custom changes directly to the system we recommend backing up those files before continuing. 
	</div>
	<div style="margin-left:10px;">
	Check the <a href="./changelog.txt">changelog.txt</a> file for a list of all changes that have taken place
	</div>
	<div style="margin-left:10px;">
	For the latest information and source code, including links to the up to date SVN project please check the <a href="http://sourceforge.net/projects/phpbms/">Sourceforge project page
    </a>	</div>
	
	<h2>Updating the base system</h2>
	<div class="box">
    	<div style="float:right;width:40%;padding-top:0"><br>
    		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
    		<div>
    			<textarea name="results" id="verifyLoginresults" style="width:100%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="11"></textarea>
   			</div>
   		</div>
		
		<div style="margin-right:42%">
	    	<h3 >Step 1 - Check Database Connection and Enter Administrator Log In Information </h3>
			<div style="margin-left:10px;">Only users with administrative rights can run the update procedure.  Enter the log in name of and administrator and click the verify
	both the database connection and the administrative log in.
				<p>
					name<br>
					<input name="name" type="text" id="username" size="32" maxlength="64">
					<input name="name" type="hidden" id="version"  value="<?php echo $version ?>">
				</p>
				<p>
					password<br>
                    <input name="password" type="password" id="password" size="32" maxlength="24" >
</p>
			</div>
			
			<div>
				<input type="button" value="Verify Log In" class="Buttons" onClick="runCommand('verifyLogin')">
			</div>
   		</div>
	</div>
	<div class="box">
    	<div style="float:right;width:40%;padding-top:0">
    		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
    		<div>
    			<textarea name="results" id="checkBaseUpdateresults" style="width:100%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="3"></textarea>
    		</div>
   		</div>
		<div style="margin-right:42%">
		    	<h3 >Step 2 - Check The Base Module for Updates </h3>
    			<div style="margin-left:10px;">
	    		 If the administrative login was successful, compare this version to the installed version.</div>
    			<div>
    				<input type="button" value="Check For Update" class="Buttons" onClick="runCommand('checkBaseUpdate')">
	   			</div>
   		</div>
	</div>

<div class="box">
    	<div style="float:right;width:40%;padding-top:0">
    		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
    		<div>
    			<textarea name="results" id="updateBaseVersionresults" style="width:100%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="3"></textarea>
   			</div>
   		</div>
		<div style="margin-right:42%">
	    	<h3 >Step 3 - Run the base update </h3>
			<div style="margin-left:10px;">
			If phpBMS needs to be updated, click the &quot;Update Base Module&quot; button to update the phpBMS core. This may make changes to the database records and design. </div>
			<div>
				<input type="button" value="Update Base Module" class="Buttons" onClick="runCommand('updateBaseVersion')">
			</div>
		</div>
	</div>
	
	<h2>Updating Installed modules</h2>
    <div class="box">
	<div style="float:right;width:40%;padding-top:0">
		<h3 style="margin-left:0;margin-bottom:1px;">Results</h3>
		<div>
			<textarea name="results" id="moduleresults" style="width:100%;font-family:'Courier New', Courier, mono;font-size:11px;" rows="12"></textarea>
		</div>
	</div><h3 style="margin-right:42%">Step 4 - Update Additional Modules </h3>
	<div style="margin-left:10px;margin-right:42%">
		<div>
			<div>Below is a list of additional installed modules that can be updated. To update a module, select the module from the list, and click the &quot;update module &quot; button. </div>
			<div> Select a module to update <br>
                	<select size="6" id="modules" name="modules" style="width:400px;font-size:12px;" >
                		<?php showModules()?>
                	</select>
            	</div>
			<div style="width:400px;" align="right">
            	<input type="button" id="updatemodule" name="updatemodule" value="Update Module" class="Buttons" onClick="runModuleUpdate()">
            </div>
		</div>
	</div>
		
	</div>	
	<h2>Complete the update</h2>
	<div>If phpBMS and any applicable modules updated successfully, go to the log in screen.</div>
	<div><input type="button" id="login" name="login" value="Go to Log In Screen" class="Buttons" onClick="document.location='../'"></div>

</div></body>
</html>
