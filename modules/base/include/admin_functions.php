<?php

// access level to admin area
if($_SESSION["userinfo"]["accesslevel"]<91) header("Location: noaccess.html"); else{
	global $admintabs;
	$admintabs=array(
		array(
			"name"=>"General",
			"href"=>$_SESSION["app_path"]."modules/base/adminsettings.php"
		),
		array(
			"name"=>"Modules",
			"href"=>$_SESSION["app_path"]."search.php?id=21"
		),
		array(
			"name"=>"Nav. Menu",
			"href"=>$_SESSION["app_path"]."search.php?id=19"
		),
		array(
			"name"=>"Tables",
			"href"=>$_SESSION["app_path"]."search.php?id=16"
		),
		array(
			"name"=>"Users",
			"href"=>$_SESSION["app_path"]."search.php?id=9"
		)
	);

	global $tabletabs;
	$tabletabs=array(
		array(
			"name"=>"Reports",
			"href"=>$_SESSION["app_path"]."search.php?id=16"
		),
		array(
			"name"=>"Relationships",
			"href"=>$_SESSION["app_path"]."search.php?id=10"
		),
		array(
			"name"=>"Saved Searches/Sorts",
			"href"=>$_SESSION["app_path"]."search.php?id=17"
		),
		array(
			"name"=>"Table Definitions",
			"href"=>$_SESSION["app_path"]."search.php?id=11"
		)
	);
}

//format tabs for user admin.
function admin_tabs($selected="none") {
	global $admintabs;
	$_SESSION["admintab"]=$selected;
	create_tabs($admintabs,$selected);
}//end function

function admin_table_tabs($selected="none") {
	global $tabletabs;
	$_SESSION["tabletab"]=$selected;
	create_tabs($tabletabs,$selected);
}
?>