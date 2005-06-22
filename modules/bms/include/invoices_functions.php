<?php
function invoice_tabs($selected="none",$id=0) {
	global $dblink;
	
	$thequerystatement="select id from notes where 
						attachedtabledefid=3 and attachedid=".$id;
	$thequery=mysql_query($thequerystatement,$dblink);
	$thequery? $numrows=mysql_num_rows($thequery): $numrows=0;

	$thetabs=array(
		array(
			"name"=>"General",
			"href"=>($id)?"invoices_addedit.php?id=".$id:"/invoices_addedit.php"
		),
		array(
			"name"=>"Notes/Messages",
			"href"=>(($id)?"invoices_notes.php?refid=".$id:"N/A"),
			"disabled"=>(($id)?false:true),
			"notify"=>($numrows?true:false)
		)
	);
	create_tabs($thetabs,$selected);
}
?>