<?php
//format tabs for clients.
function client_tabs($selected="none",$id=0) {
	global $dblink;
	
	$querystatement="select id from notes where 
						attachedtabledefid=2 and attachedid=".$id;
	$thequery=mysql_query($querystatement,$dblink);
	$thequery? $numrows=mysql_num_rows($thequery): $numrows=0;

	$thetabs=array(
		array(
			"name"=>"General",
			"href"=>($id)?"clients_addedit.php?id=".$id:"/clients_addedit.php"
		),
		array(
			"name"=>"Purchase History",
			"href"=>(($id)?"clients_purchasehistory.php?id=".$id:"N/A"),
			"disabled"=>(($id)?false:true)
		),
		array(
			"name"=>"Notes",
			"href"=>(($id)?"clients_notes.php?refid=".$id:"N/A"),
			"disabled"=>(($id)?false:true),
			"notify"=>($numrows?true:false)
		)
	);
	create_tabs($thetabs,$selected);
}
?>