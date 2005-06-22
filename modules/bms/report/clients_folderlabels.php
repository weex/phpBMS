<?PHP
	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder="  ORDER BY concat(lastname,firstname,company)";

	$maxrows=10;
	$maxcolumns=3;
	$topstart=1/2;
	$leftstart=3/16;
	$columnmargin=1/8;
	$labelheight=1;
	$labelwidth=2+(5/8);
	
	$reportquerystatement="select firstname,lastname,company,city,state from clients ";
						
	$border_debug=0;

	function printLabel($pdf,$therecord,$thex,$they,$border_debug){
		//offset left by 1/8" and top by 1/16th
		$pdf->SetXY($thex+(1/8),$they+1/16);
		$pdf->SetFont("Arial","B",12);
		if($therecord["lastname"]){
			$thename=$therecord["lastname"].", ".$therecord["firstname"];
			if($therecord["company"]) $thename.="\n".$therecord["company"];
		} else {
			$thename=$therecord["company"];		
		}
		$pdf->MultiCell(2.25,.2,$thename,$border_debug,2,"L");
		$pdf->SetFont("Arial","",9);
		$pdf->SetX($thex+(1/8));
		$pdf->Cell(2.25,.13,$therecord["city"].", ".$therecord["state"],$border_debug,2,"L");
		
		return $pdf;
	}

	require_once("../../../include/session.php");
	require_once("../../../fpdf/fpdf.php");
	require("../../../report/general_labels.php");
?>