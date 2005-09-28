<?PHP
	include("include/session.php");
	
	$mytruecountstatement="SELECT count(*) as thecount".strstr(substr($_SESSION["thequerystatement"],0,strpos($_SESSION["thequerystatement"]," ORDER BY"))," FROM ");
	$queryresult=mysql_query($mytruecountstatement,$dblink) or die (mysql_error()." ".$mytruecountstatement);
	$therecord=mysql_fetch_array($queryresult);
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Choose...</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="<?PHP echo $_SERVER["PHP_SELF"] ?>" method="post" name="thechoice">
 <table width="100%" border="0" cellpadding="2" cellspacing="0" class="bodyline">
  <tr>
   <td><strong>Record Count:</strong> <?php echo $therecord["thecount"];?></td>
  </tr>
   <td align="right" nowrap>&nbsp;</td>
  </tr>
  <tr>
   <td align="right" nowrap>
		<input name="Button" type="button" class="Buttons" value="done" onClick="window.close()" style="width:75px">
   </td>
  </tr>
  
  
  
 </table>
</form>
</body>
</html>
