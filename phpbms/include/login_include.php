<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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
class login{

	var $db;

	function login($db){

		$this->db = $db;

	}//end function init


	function verify($username, $password){

		$querystatement = "
			SELECT
				id,
				uuid,
				firstname,
				lastname,
				email,
				phone,
				department,
				employeenumber,
				admin
			FROM
				users
			WHERE
				login = '".mysql_real_escape_string($username)."'
				AND password = ENCODE('".mysql_real_escape_string($password)."','".mysql_real_escape_string(ENCRYPTION_SEED)."')
				AND revoked = 0
				AND portalaccess = 0";

		$queryresult = $this->db->query($querystatement);

		if($this->db->numRows($queryresult)){

			//We found a record that matches in the database
			// populate the session and go in
			$_SESSION["userinfo"] = $this->db->fetchArray($queryresult);

			// Next get the users roles, and populate the session with them
			$_SESSION["userinfo"]["roles"] = array();
			$querystatement = "
				SELECT
					roleid
				FROM
					rolestousers
				WHERE userid = '".$_SESSION["userinfo"]["uuid"]."'";

			$rolesqueryresult = $this->db->query($querystatement);

			while($rolerecord = $this->db->fetchArray($rolesqueryresult))
				$_SESSION["userinfo"]["roles"][]=$rolerecord["roleid"];

			//Retrieve and Setup User Preferences
			$_SESSION["userinfo"]["prefs"] = array();

			$querystatement = "
				SELECT
					`name`,
					`value`
				FROM
					`userpreferences`
				WHERE
					`userid` = ".$_SESSION["userinfo"]["id"];

			$queryresult = $this->db->query($querystatement);

			while($prefsrecord = $this->db->fetchArray($queryresult))
				$_SESSION["userinfo"]["prefs"][$prefsrecord["name"]] = $prefsrecord["value"];

			//update lastlogin
			$ip = $_SERVER["REMOTE_ADDR"];

			$updatestatement = "
				UPDATE
					users
				SET
					modifieddate = modifieddate,
					lastlogin = Now(),
					`lastip` = '".$ip."'
				WHERE
					id = ".$_SESSION["userinfo"]["id"];

			$this->db->query($updatestatement);

			$_SESSION["tableparams"] = array();

			goURL(DEFAULT_LOAD_PAGE);

		} else 	{

			//log login attempt
			$log = new phpbmsLog("Login attempt failed for user '".$username."'", "SECURITY");

			return "Login Failed";

		}//endif numrows


	}//end function verify

}//end class
