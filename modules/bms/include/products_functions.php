<?PHP
//format tabs for products.
function product_tabs($selected="none",$id=0) {
	global $dblink;
	
	$querystatement="select id from notes where 
						attachedtabledefid=4 and attachedid=".$id;
	$thequery=mysql_query($querystatement,$dblink);
	$thequery? $numrows=mysql_num_rows($thequery): $numrows=0;

	$thetabs=array(
		array(
			"name"=>"General",
			"href"=>($id)?"products_addedit.php?id=".$id:"/clients_addedit.php"
		),
		array(
			"name"=>"Prerequisites",
			"href"=>(($id)?"products_prereq.php?id=".$id:"N/A"),
			"disabled"=>(($id)?false:true)
		)
	);
	
	if($_SESSION["userinfo"]["accesslevel"]>90){
		array_push($thetabs,array(
			"name"=>"Sales History",
			"href"=>(($id)?"products_saleshistory.php?id=".$id:"N/A"),
			"disabled"=>(($id)?false:true)
		));}
		array_push($thetabs,array(
			"name"=>"Notes/Messages",
			"href"=>(($id)?"products_notes.php?refid=".$id:"N/A"),
			"disabled"=>(($id)?false:true),
			"notify"=>($numrows?true:false)
		));
	create_tabs($thetabs,$selected);
}
?>