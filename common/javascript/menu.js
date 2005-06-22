function changePass(base){
	dialogWindow=window.open(base+"changepassword.php","changepassword","resize=yes,status=no,scrollbars=no,width=375,height=220,modal=yes");
	return true;
}
var supported = (document.getElementById || document.all);

if (supported)
{
	document.write("<STYLE TYPE=\"text/css\">");
	document.write(".submenuitems {display: none}");
	document.write("</STYLE>");

	var max = 7; // # of menus
	var shown = new Array();
	for (var i=1;i<=max;i++)
	{
		shown[i+1] = false;
	}
}

function expand(i)
{
	shown[i] = (shown[i]) ? false : true;
	current = (shown[i]) ? 'block' : 'none';


	if (document.getElementById) {
		document.getElementById('sub'+i).style.display = current;
		var oldImage = document.getElementById('right' + i);
		base=oldImage.src.substring(0,oldImage.src.indexOf("common/image"));
		if (shown[i]) {
			oldImage.src=base+'common/image/down_arrow.gif';
		} else {
			oldImage.src=base+'common/image/left_arrow.gif';
		}
	} else if (document.all) {
		document.all['sub'+i].style.display = current;
		var oldImage ="document.right" + i;
		base=oldImage.src.substring(0,oldImage.src.indexOf("common/image"));
		if (shown[i]) {
			oldImage.src=base+'common/image/down_arrow.gif';
		} else {
			oldImage.src=base+'common/image/left_arrow.gif';
		}
	}
	return false;
}
