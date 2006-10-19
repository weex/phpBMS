<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<script language="javascript" type="text/javascript" src="common/javascript/common.js"></script>
<script language="javascript" type="text/javascript" src="common/javascript/moo/prototype.lite.js"></script>
<script language="javascript" type="text/javascript" src="common/javascript/moo/moo.fx.js"></script>
<script language="javascript" type="text/javascript">
function doFade(){
	var thediv=getObjectFromID("fader");
	var fade = new fx.Opacity(thediv,{duration:1000});
fade.custom(0,1);
window.status="DURRRR";
}

</script>
</head>

<body>
<a href="" onclick="doFade();return false;">Click here</a><br />
<div id="fader" style="background:black;color:white">
	THIS IS A TEST.
</div>
</body>
</html>
