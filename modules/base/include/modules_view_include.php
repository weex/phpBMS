<?PHP
// These following functions and processing are similar for all pages
//========================================================================================
//========================================================================================

//set table id
$tableid=21;

function getRecords($id){
//========================================================================================
	global $dblink;
	
	$querystatement="SELECT
				id,name,displayname,version,description
				
				FROM modules
				WHERE id=".$id;		
	$thequery = mysql_query($querystatement,$dblink);
	$therecord = mysql_fetch_array($thequery);
	return $therecord;
}//end function


function setRecordDefaults(){
//========================================================================================
	return $therecord;	
}//end function



//==================================================================
// Process adding, editing, creating new, canceling or updating
//==================================================================
if(!isset($_POST["command"])){
	if(isset($_GET["id"])){
		//get record
		$therecord=getRecords($_GET["id"]);
	}
}
else
{
	switch($_POST["command"]){
		case "cancel":
			// if we needed to do any clean up (deleteing temp line items)
			$theid=$_POST["id"];
		break;
	}
	header("Location: ../../search.php?id=".$tableid."#".$theid);
}
?>