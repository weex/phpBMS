<?php 
	include("include/session.php");
	include("include/common_functions.php");
	include("include/fields.php");

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>phpBMS - TEST</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="common/stylesheet/mozilla/base.css" rel="stylesheet" type="text/css">
<script language="javascript" src="common/javascript/common.js"></script>
<script language="javascript" src="common/javascript/choicelist.js"></script>
<script language="javascript" src="common/javascript/fields.js"></script>
<script language="javascript" src="common/javascript/autofill.js"></script>
</head>
<body>
	<div>
		<?PHP autofill("clientid","",2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","clients.inactive!=1 AND clients.type=\"client\"",Array("size"=>"70","maxlength"=>"128","style"=>"","style"=>"font-weight:bold"),1,"The quote/order/invoice must have a client.") ?>
		<br><br>
		<?PHP autofill("partnumber","",4,"products.id","products.partnumber","products.partname","products.status=\"In Stock\"",Array("size"=>"11","maxlength"=>"32","style"=>"border-left-width:0px;"),false,"") ?>
	</div>
	<div>
		<?PHP choicelist("test2","","test"); ?>
	</div>
	<div align="right">
		<?PHP choicelist("test","","test"); ?>
	</div>
	
</body>
</html>