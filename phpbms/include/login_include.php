<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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
	function verifyLogin($username,$password,$seed,$dblink){
		$thereturn="Login Failed";
		
		$querystatement="SELECT id, firstname, lastname, email, phone, department, employeenumber, admin
						FROM users 
						WHERE login=\"".mysql_real_escape_string($username)."\" and password=ENCODE(\"".mysql_real_escape_string($password)."\",\"".mysql_real_escape_string($seed)."\") and revoked=0 and portalaccess=0";
		$queryresult= @ mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Error verifing user record: ".$querystatement);
		if(mysql_num_rows($queryresult)){
			//We found a record that matches in the database
			// populate the session and go in
			$_SESSION["userinfo"]=mysql_fetch_array($queryresult);
			
			// Next get the users roles, and populate the session with them
			$_SESSION["userinfo"]["roles"][]=0;
			$querystatement="SELECT roleid FROM rolestousers WHERE userid=".$_SESSION["userinfo"]["id"];
			$rolesqueryresult=mysql_query($querystatement,$dblink);
			if(!$rolesqueryresult) reportError(310,"Error obtaining user roles.");
			while($rolerecord=mysql_fetch_array($rolesqueryresult))
				$_SESSION["userinfo"]["roles"][]=$rolerecord["roleid"];
			
			// set application location (web, not physical)
			$pathrev=strrev($_SERVER["PHP_SELF"]);
			$_SESSION["app_path"]=strrev(substr($pathrev,(strpos($pathrev,"/"))));

			$querystatement="UPDATE users SET modifieddate=modifieddate, lastlogin=Now() WHERE id = ".$_SESSION["userinfo"]["id"];
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult)
				reportError(300,"Error Updating login time.");

			$_SESSION["tableparams"]=array();
						
			goURL($_SESSION["default_load_page"]);
		} else 		
		return "Login Failed";
	}
	
	$failed="";
	if (isset($_POST["name"])) {
		$variables=addSlashesToArray($_POST);
		$failed=verifyLogin($variables["name"],$variables["password"],$_SESSION["encryption_seed"],$dblink);
	} else 
		$_POST["name"]="";
?>