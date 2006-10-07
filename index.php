<?php include("include/session.php");
/*
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
	if (isset($_POST["name"])) {
		$result = mysql_query("SELECT id,firstname,lastname,accesslevel,email,phone,department,employeenumber from users where login=\"".$_POST["name"]."\" and password=ENCODE(\"".$_POST["password"]."\",\"".$_SESSION["encryption_seed"]."\") and revoked=0 and accesslevel>=10;",$dblink);
		if (mysql_num_rows($result)){
		
			// login passed... set session parameters
			$_SESSION["userinfo"]= mysql_fetch_array($result);

			// set application location (web, not physical)
			$pathrev=strrev($_SERVER["PHP_SELF"]);
			$_SESSION["app_path"]=strrev(substr($pathrev,(strpos($pathrev,"/"))));
						
			//next update record's lastlogin time
			$result = mysql_query("UPDATE users set modifieddate=modifieddate, lastlogin=Now() where id = ".$_SESSION["userinfo"]["id"],$dblink);
			if  (!$result)die ("update users query failed:".mysql_error($dblink));			
						
			header("Location: ".$_SESSION["default_load_page"]);
			
			//register table settings
			session_register("tableparams");
		}
		else
		{
			// Login failed... send to index.php with parameter of no we should already be on the index page... so just set
			// the failed parameter
			$failed="Login Failed";
		}		
	}


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<script language="javascript">if(top!=self){top.location=self.location;}</script>
	<title><?PHP echo $_SESSION["application_name"]; ?> - Login Page</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="common/javascript/common.js"></script>
	<script language="javascript">
		function setMainFocus(){
			var focusField=getObjectFromID("username");
			focusField.focus();		
		}
	</script>
</head>

<body>
<form name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
 <div align="center">
 	<div style="width:250px;">
	<div class="bodyline" style="margin-top:100px;padding:0px;" align="left">
		<div align="right"  class="box" style="margin:1px;border-style:none; background-color:white;"><a href="http://kreotek.com/products/phpbms"><img src="common/image/logo.png" width="85" height="22" border="0"></a></div>
		<h2 style="text-transform:none;margin:4px;">
			<?PHP echo $_SESSION["application_name"];?>			
		<div class=tiny style="padding:0px;">Business Management Web Application</div>
		</h2>
		
		<label for="username">name<br />
    	<input name="name" type="text" id="username" size="25" maxlength="64" style="width:97%" />
		<script>setMainFocus();</script></label>
		<label for="password">password<br />
    	<input name="password" type="password" id="password" size="25" maxlength="24" style="width:97%" /></label>
		<div align=right style="padding-bottom:15px;"><input name="command" type="submit" class="Buttons" value="Log On" style="width:75px;" /></div>
		
		<?php if (isset($failed)) {?>
			<div class="standout" align="center"><?php echo $failed?></div>
		    <?php } ?>

		<div class="tiny" align="center">&middot;&nbsp;<a href="requirements.php">browser requirements</a>&nbsp;&middot;&nbsp;<a href="info.php">program info</a> &middot;</div>
	</div>
	</div>
 </div>
</form>
</body>
</html>
