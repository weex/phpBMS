<?php include("include/session.php");

	if (isset($_POST["name"])) {
		$result = mysql_query("SELECT id,firstname,lastname,accesslevel,email,phone,department,employeenumber from users where login=\"".$_POST["name"]."\" and password=ENCODE(\"".$_POST["password"]."\",\"".$_SESSION["encryption_seed"]."\") and revoked=0 and accesslevel>9;",$dblink);
		if (mysql_num_rows($result)){
		
			// login passed... set session parameters
			$_SESSION["userinfo"]= mysql_fetch_array($result);

			// set application location (web, not physical)
			$pathrev=strrev($_SERVER["PHP_SELF"]);
			$_SESSION["app_path"]=strrev(substr($pathrev,(strpos($pathrev,"/"))));
						
			//next update record's lastlogin time
			$result = mysql_query("UPDATE users set modifieddate=modifieddate, lastlogin=Now() where id = ".$_SESSION["userinfo"]["id"],$dblink);
			if  (!$result)die ("update users query failed:".mysql_error());			
			
			//anytime anyone logs in, clean temp PDF files older than an hour
			include("include/common_functions.php");
			clean_pdf_reports("report/");
			
			header("Location: ".$_SESSION["default_load_page"]);
			
			//register table settings
			session_register("tableparams");
		}
		else
		{
			// Login failed... send to index.php with parameter of no we should already be on the index page... so just set
			// the failed parameter
			$failed="** Login Failed **";
		}		
	}


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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
 	<div style="width:210px;">
	<div class="bodyline" style="margin-top:100px;border:1px solid black;" align="left">
		<div class=large style="padding-bottom:0px;"><strong><?PHP echo $_SESSION["application_name"];?></strong></div>
		<div class=tiny style="padding-top:0px;">Business Management Web Application</div>

		<div style="padding-top:15px">name<br>
    	<input name="name" type="text" id="username" size="25" maxlength="64" style="width:100%">
		<script>setMainFocus();</script>
		</div>
		<div>password<br>
    	<input name="password" type="password" id="password" size="25" maxlength="24" style="width:100%"></div>
		<div align=right style="padding-bottom:15px;"><input name="command" type="submit" class="Buttons" value="Log On" style="width:75px;"></div>
		
		<?php if (isset($failed)) {?>
			<div class="standout" align="center"><strong><?php echo $failed?></strong></div>
		    <?php } ?>

		<div class="tiny" align="center">&middot;&nbsp;<a href="requirements.html">browser requirements</a>&nbsp;&middot;&nbsp;<a href="info.html">program info</a> &middot;</div>
	</div>
	</div>
 </div>
</form>
</body>
</html>