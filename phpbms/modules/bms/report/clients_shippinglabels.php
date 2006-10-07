<?PHP
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/

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
	
	$reportquerystatement="select firstname,lastname,company,shiptoaddress1,shiptoaddress2,
						shiptocity,shiptostate,shiptopostalcode,shiptocountry 
						FROM clients ";
						
	$border_debug=0;

	function printLabel($pdf,$therecord,$thex,$they,$border_debug){
		//offset lef tby 1/8" and top by 1/16th
		$pdf->SetXY($thex+(1/8),$they+1/16);
		$pdf->SetFont("Arial","B",9);
		if($therecord["lastname"]){
			$thename=$therecord["lastname"].", ".$therecord["firstname"];
			if($therecord["company"]) $thename.="\n".$therecord["company"];
		} else {
			$thename=$therecord["company"];		
		}
		$pdf->MultiCell(2.25,.135,$thename,$border_debug,2,"L");
		$pdf->SetFont("Arial","",8);
		$pdf->SetX($thex+(1/8));
		$pdf->Cell(2.25,.12,$therecord["shiptoaddress1"],$border_debug,2,"L");
		if($therecord["shiptoaddress2"]) $pdf->Cell(2.25,.12,$therecord["shiptoaddress2"],$border_debug,2,"L");
		$pdf->Cell(2.25,.12,$therecord["shiptocity"].", ".$therecord["shiptostate"]." ".$therecord["shiptopostalcode"],$border_debug,2,"L");
		if($therecord["shiptocountry"]) $pdf->Cell(2.25,.12,$therecord["shiptocountry"],$border_debug,2,"L");
		
		return $pdf;
	}

	session_cache_limiter('private');
	require_once("../../../include/session.php");
	require_once("../../../fpdf/fpdf.php");
	require("../../../report/general_labels.php");
?>