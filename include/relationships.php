<?php

//Perform the relationship lookup to construct join clause
function perform_relationship($relationshipid,$theids){
	global $dblink;
	$_SESSION["passedjoinclause"]="";
	$_SESSION["passedjoinwhere"]="";
	
	$querystatement="SELECT 
		fromtable.maintable as fromtable, relationships.totableid, totable.maintable as totable, 
		relationships.tofield, relationships.fromfield, relationships.inherint
		FROM
		(relationships inner join tabledefs as fromtable on relationships.fromtableid=fromtable.id) 
		inner join tabledefs as totable on relationships.totableid=totable.id	
		WHERE relationships.id=".$relationshipid;
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,"Error Retrieving Eelationship");
	$therelationship=mysql_fetch_array($queryresult);
	
	//if the relationship is inherent (already exists due to the display nature of the table)
	// then the join clause is not needed
	if($therelationship["inherint"]=="0"){
		// build the join clause
		$_SESSION["passedjoinclause"]=" INNER JOIN ".$therelationship["fromtable"]." on ".$therelationship["totable"].".";
		$_SESSION["passedjoinclause"].=$therelationship["tofield"]."=".$therelationship["fromtable"].".".$therelationship["fromfield"];
	}
	
	// make the passed where clause for passed id's	 this will pass the saved where clause.
	foreach($theids as $theid){
		$_SESSION["passedjoinwhere"].=" or ".$therelationship["fromtable"].".id=".$theid;
	}
	$_SESSION["passedjoinwhere"]=substr($_SESSION["passedjoinwhere"],3);

	return "search.php?id=".$therelationship["totableid"];
}
?>