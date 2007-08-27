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
	function verifyLogin($username,$password,$db){
		$thereturn = "Login Failed";
		
		$querystatement = "SELECT id, firstname, lastname, email, phone, department, employeenumber, admin
						FROM users 
						WHERE login=\"".mysql_real_escape_string($username)."\" 
						AND password=ENCODE(\"".mysql_real_escape_string($password)."\",\"".mysql_real_escape_string(ENCRYPTION_SEED)."\") 
						AND revoked=0 AND portalaccess=0";
						
		$queryresult = $db->query($querystatement);
				
		if($db->numRows($queryresult)){
			
			//We found a record that matches in the database
			// populate the session and go in
			$_SESSION["userinfo"]=$db->fetchArray($queryresult);
			
			// Next get the users roles, and populate the session with them
			$_SESSION["userinfo"]["roles"][]=0;
			$querystatement = "SELECT roleid FROM rolestousers WHERE userid=".$_SESSION["userinfo"]["id"];
			$rolesqueryresult = $db->query($querystatement);
			
			while($rolerecord=$db->fetchArray($rolesqueryresult))
				$_SESSION["userinfo"]["roles"][]=$rolerecord["roleid"];
			
		
			$querystatement = "UPDATE users SET modifieddate=modifieddate, lastlogin=Now() WHERE id = ".$_SESSION["userinfo"]["id"];
			$updateresult = $db->query($querystatement);

			$_SESSION["tableparams"]=array();
						
			goURL(DEFAULT_LOAD_PAGE);
		} else 		
		return "Login Failed";
	}


// Start Code
//=================================================================================================================
	
	$failed="";
	if (isset($_POST["name"])) {
		$variables=addSlashesToArray($_POST);
		$failed=verifyLogin($variables["name"],$variables["password"],$db);
	} else 
		$_POST["name"]="";
?>