<?php
// This function writes to the settings.php file
// any settings you want saved
//=========================================
function write_settings($settings) {
	$settingsfile = fopen("../../settings.php","r") or die ("Couldn't open Settings File");
	//create an array of all lines
	while( !feof($settingsfile)) {
		$newfile[]=fgets($settingsfile,1024);
	}
	fclose($settingsfile);
	
	$newfile[]="\n";
	foreach($settings as $thesetting) {
		$infile=false;
		//next loop through the file, and if the setting is their, replace it
		for($i=0;$i<count($newfile);$i++){
			if (strpos(("D".$newfile[$i]),$thesetting["name"])==1) {
				$tabnumber=intval(5-strlen($thesetting["name"])/8);
				$newfile[$i]=$thesetting["name"].str_repeat(chr(9),$tabnumber)."= \"".str_replace(chr(10),"\\n",$thesetting["value"])."\"\n";
				$infile=true;
				break;
			}
		}
		if(!$infile) {
			$tabnumber=intval(5-strlen($thesetting["name"])/8);
			$newfile[]=$thesetting["name"].str_repeat(chr(9),$tabnumber)."= \"".str_replace(chr(10),"\\n",$thesetting["value"])."\"\n";
		}
	}
		
	if(end($newfile)=="\n") array_pop($newfile);
	//now write the new file
	$settingsfile = fopen("../../settings.php","w") or die ("Couldn't open Settings File");
	for($i=0;$i<count($newfile);$i++){
		 fwrite($settingsfile,$newfile[$i],1024);
	}
	fclose($settingsfile);
}//end function




//process commands
if (isset($_POST["command"])) {
	
	$writesettings=Array();
	if ($_SESSION["application_name"]!=$sapplication_name){
		$writesettings[]=Array(
			"name"=>"application_name",
			"value"=>$sapplication_name
		);		
		$_SESSION["application_name"]=$sapplication_name;
	}
	
	if ($_SESSION["encryption_seed"]!=$sencryption_seed){
		$writesettings[]=Array(
			"name"=>"encryption_seed",
			"value"=>$sencryption_seed
		);		
		$_SESSION["encryption_seed"]=$sencryption_seed;
	}

	if ($_SESSION["record_limit"]!=$srecord_limit){
		$writesettings[]=Array(
			"name"=>"record_limit",
			"value"=>$srecord_limit
		);		
		$_SESSION["record_limit"]=$srecord_limit;
	}

	if ($_SESSION["mysql_server"]!=$smysql_server){
		$writesettings[]=Array(
			"name"=>"mysql_server",
			"value"=>$smysql_server
		);		
		$_SESSION["mysql_server"]=$smysql_server;
	}

	if ($_SESSION["mysql_database"]!=$smysql_database){
		$writesettings[]=Array(
			"name"=>"mysql_database",
			"value"=>$smysql_database
		);		
		$_SESSION["mysql_database"]=$smysql_database;
	}

	if ($_SESSION["mysql_user"]!=$smysql_user){
		$writesettings[]=Array(
			"name"=>"mysql_user",
			"value"=>$smysql_user
		);		
		$_SESSION["mysql_user"]=$smysql_user;
	}

	if ($_SESSION["mysql_userpass"]!=$smysql_userpass){
		$writesettings[]=Array(
			"name"=>"mysql_userpass",
			"value"=>$smysql_userpass
		);		
		$_SESSION["mysql_userpass"]=$smysql_userpass;
	}


	if ($_SESSION["company_name"]!=$scompany_name){
		$writesettings[]=Array(
			"name"=>"company_name",
			"value"=>$scompany_name
		);		
		$_SESSION["company_name"]=$scompany_name;
	}

	if ($_SESSION["default_load_page"]!=$sdefault_load_page){
		$writesettings[]=Array(
			"name"=>"default_load_page",
			"value"=>$sdefault_load_page
		);		
		$_SESSION["default_load_page"]=$sdefault_load_page;
	}
	

	if ($_SESSION["company_address"]!=$scompany_address){
		$writesettings[]=Array(
			"name"=>"company_address",
			"value"=>$scompany_address
		);		
		$_SESSION["company_address"]=$scompany_address;
	}

	if ($_SESSION["company_csz"]!=$scompany_csz){
		$writesettings[]=Array(
			"name"=>"company_csz",
			"value"=>$scompany_csz
		);		
		$_SESSION["company_csz"]=$scompany_csz;
	}

	if ($_SESSION["company_phone"]!=$scompany_phone){
		$writesettings[]=Array(
			"name"=>"company_phone",
			"value"=>$scompany_phone
		);		
		$_SESSION["company_phone"]=$scompany_phone;
	}


	if ($_SESSION["stylesheet"]!=$sstylesheet){
		$writesettings[]=Array(
			"name"=>"stylesheet",
			"value"=>$sstylesheet
		);		
		$_SESSION["stylesheet"]=$sstylesheet;
	}

			$thequerystatement="SELECT name FROM modules WHERE name!=\"base\" ORDER BY name";
			$modulequery=mysql_query($thequerystatement,$dblink);
			
			while($modulerecord=mysql_fetch_array($modulequery)){
				include "../".$modulerecord["name"]."/include/adminsettings_include.php";
			}//end while 


	// if changes, process settings
	if(count($writesettings)>0) { write_settings($writesettings);}
	
	// deal with logo graphic.
	if($printedlogo!="none"){
		copy($printedlogo,$_SERVER["DOCUMENT_ROOT"]."/report/logo.png");
	}
}

?>