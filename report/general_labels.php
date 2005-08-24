<?php

	// The label report requires the following variables to be set before creating the PDF
	//maxrows
	//maxcolumns
	//columnmargin
	//labelheight
	//labelwidth
	//top_start
	//left_start
	//border debug
	//reportquerystatement	
	if(!isset($border_debug)) $border_debug=0;
	
	//Also, it requires the definition of the function printLabel($pdf,$therecord,$thex,$they,$border_debug) which prints the actual 
	//contectens of the label
	
	if(isset($_POST["command"])) {
	
		if(!is_numeric($_POST["skiplabels"])) $_POST["skiplabels"]=0;
		if ($_POST["skiplabels"]>$maxrows*$maxcolumns) $_POST["skiplabels"]=0;
			
		
	
		//Generate the invoice Query
		$reportquerystatement.=$_SESSION["printing"]["whereclause"].$sortorder;
		$thequery=mysql_query($reportquerystatement,$dblink);
		if(!$thequery) die("No records, or invlaid SQL statement:<BR>".$reportquerystatement);
		//===================================================================================================
		// Generating PDF File.
		//===================================================================================================
		
		//define the documents and margins
		$pdf=new FPDF("P","in","Letter");
		$pdf->Open();
		$pdf->SetMargins(0,0);
	
		$pdf->AddPage();	
		
		$thex=$leftstart;
		$they=$topstart;
		$rowcount=1;
		$totalcount=1;
		$column=1;
		//this first while skips records

		while($totalcount<=$_POST["skiplabels"]){
			if($rowcount>$maxrows) {
			   $column++;
			   $they=$topstart;
			   $thex+=$labelwidth+$columnmargin;
			   $rowcount=1;
			}
			$they+=$labelheight;
			$rowcount++;
			$totalcount++;
		}
		
		while($therecord=mysql_fetch_array($thequery)) {	
			if($rowcount>$maxrows) {
			   $column++;
			   $they=$topstart;
			   $thex+=$labelwidth+$columnmargin;
			   $rowcount=1;
			}
			if($column>$maxcolumns){
				$pdf->AddPage();				
				$thex=$leftstart;
				$they=$topstart;
				$rowcount=1;
				$column=1;
			}
			$pdf=printLabel($pdf,$therecord,$thex,$they,$border_debug);
			
			$they+=$labelheight;
			$rowcount++;
		}// end fetch_array while loop
	
		
		if($border_debug==1){
			$pdf->Output();
		}
		else {
			//write the frickin thing! Need to write to a temp file and then you know...
			chdir("../../../report");
			$file=basename(tempnam(getcwd(),'tmp'));
			chmod($file,0664);		
			rename($file,$file.'.pdf');
			$file.='.pdf';
		
			// write to file and then output
			$pdf->Output($file);
			echo "<HTML><SCRIPT>document.location='../../../report/".$file."';</SCRIPT></HTML>";
		}
	} else {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title>Label Options</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="bodyline" style="width:550px;">
	<div class="searchtitle">Label Options</div>
	
	<form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="print">
		<div class="box">
			skip first labels<br>
			<input name="skiplabels" value="0" size="3" maxlength="3">
		</div>
		<div align="right" class="recordbottom">
			<input name="command" type="submit" class="Buttons" id="print" value="print" style="width:75px;margin-right:3px;">
			<input name="cancel" type="button" class="Buttons" id="cancel" value="canel" style="width:75px;" onClick="window.close();">	 
		</div>
   </form>
</div>
</body>
</html>
<?php }//end if ?>