<?php
// access level to admin area
if($_SESSION["userinfo"]["accesslevel"]<91)
	header("Location: noaccess.html");

//format tabs for user admin.
function tabledefs_tabs($selected="none",$id=0) {
	$thetabs=array(
		array(
			"name"=>"General",
			"href"=>($id)?"tabledefs_addedit.php?id=".$id:"/tabledefs_addedit.php"
		),
		array(
			"name"=>"Columns",
			"href"=>($id)?"tabledefs_columns.php?id=".$id:"/tabledefs_columns.php",
			"disabled"=>(($id)?false:true)
		),
		array(
			"name"=>"Options",
			"href"=>($id)?"tabledefs_options.php?id=".$id:"/tabledefs_options.php",
			"disabled"=>(($id)?false:true)
		),
		array(
			"name"=>"Quick Search",
			"href"=>($id)?"tabledefs_quicksearch.php?id=".$id:"/tabledefs_quicksearch.php",
			"disabled"=>(($id)?false:true)
		),
		array(
			"name"=>"Search Fields",
			"href"=>($id)?"tabledefs_searchfields.php?id=".$id:"/tabledefs_searchfields.php",
			"disabled"=>(($id)?false:true)
		)		
	);
	create_tabs($thetabs,$selected);
}//end function

?>