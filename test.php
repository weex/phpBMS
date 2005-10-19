<?php 
	phpinfo();
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>phpBMS - TEST</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script src="common/javascript/common.js"></script>
<script src="common/javascript/modal.js"></script>
<style>

#modalMask{
	position: absolute;
	z-index: 200;
	top: 0px;
	left: 0px;
	width: 100%;
	height: 100%;
	padding:0px;
	margin:0px;
	opacity: .4;
	filter: alpha(opacity=40);
	background-color: #AAAAAA;
}

#modalBox {
	position: absolute;
	z-index: 201;
	top: 0px;
	left: 0px;
	background-color:white;
	border: 2px solid black;
}
#modalTitle{
	background-color: #486CAE;
	color: #ffffff;
	font-weight: bold;
	font-size:12px;
	padding: 2px;
	padding-left:4px;
	border-bottom: 2px solid #000000;
	border-top: 1px solid #78A3F2;
	border-left: 1px solid #78A3F2;
	border-right: 1px solid #204095;
	z-index: 203;
}
#modalContent{
	padding:4px;
}
</style>
</head>
<body>
<?php 
	if(isset($_POST)){
		echo "<PRE>";
		var_dump($_POST);
		echo "</PRE>";
	}
?>
<style>
 FIELDSET{
border:1px blue inset;
padding:2px;
}
LEGEND{
font-family:Arial;
}
</style>
Test text<br/>
Test text<br/>
Test text<br/>
<form name="thereques" method="post" action="test.php">
<fieldset >
<legend>My Controls</legend>
<button name="reset" type="reset" value="oops" accesskey="o" onClick="alert('holy shnikeys');"><img src="http://www.google.com/intl/en/images/logo.gif" alt="oops" width="16" height=16 align="absmiddle"> reset</button>
<input type="button" onClick="alert('here - F')" accesskey="f" value="f-test" /><br />
<input type="button" onClick="alert('here - T')" accesskey="t" value="t-test" /><br />

<button type="button" name="command" id="savebutton" value="save" onClick="alert('test\nthis is a test alert.')">save record</button><br />
<button type="submit" name="command" id="cancelbutton" value="cancel">cancel record</button><br />

<select onChange="if(this.value!='')alert(this.value);">
	<option  value="">select...</option>
	<option accesskey="k" value="All">select all</option>
	<option accesskey="A" value="None">Select None</option>
</select>
</fieldset>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/> 
<fieldset accesskey="z">
<legend accesskey="y">My Controls</legend>
<input type="button" onClick="alert('here - F')" accesskey="f" value="f-test" /><br />
<input type="button" onClick="alert('here - T')" accesskey="t" value="t-test" /><br />
<label for="firstname">First Name</label><br />
<input type="text" id="firstname" tabindex=1>

</fieldset>
</form>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>
Test text<br/>


</body>
</html>
