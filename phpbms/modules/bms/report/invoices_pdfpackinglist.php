<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
	session_cache_limiter('private');
	require_once("../../../include/session.php");
	require_once("../../../include/common_functions.php");
	//turn debug borders on to troubleshoot PDF creation (1 or 0)
	$border_debug=0;
	
	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder=" ORDER BY invoices.id";

	require("../../../fpdf/fpdf.php");
	require_once("../../../fpdf/mem_image.php");
	
	//Generate the invoice Query
	$querystatement="SELECT invoices.id, totalweight, totaltni, totalti, totalcost, invoices.taxareaid,
					shippingmethod, invoices.paymentmethod, checkno, bankname, invoices.ccnumber,
					invoices.ccexpiration, specialinstructions, printedinstructions, tax, shipping,
					clients.firstname, clients.lastname, clients.company,
					clients.address1,clients.address2,clients.city,clients.state,clients.postalcode,
					invoices.address1 as shiptoaddress1,invoices.address2 as shiptoaddress2,invoices.city as shiptocity,
					invoices.state as shiptostate,invoices.postalcode as shiptopostalcode, amountpaid, trackingno,
					date_Format(invoicedate,\"%c/%e/%Y\") as invoicedate,
					date_Format(orderdate,\"%c/%e/%Y\") as orderdate,
					date_Format(shippeddate,\"%c/%e/%Y\") as shippeddate,
					invoices.totalti-invoices.amountpaid as amountdue,
					invoices.ponumber,invoices.discountamount,invoices.discountid,
					
					invoices.createdby, date_Format(invoices.creationdate,\"%c/%e/%Y %T\") as creationdate, 
					invoices.modifiedby, date_Format(invoices.modifieddate,\"%c/%e/%Y %T\") as modifieddate
					FROM invoices INNER JOIN  clients ON invoices.clientid=clients.id ".$_SESSION["printing"]["whereclause"].$sortorder;
	$thequery=mysql_query($querystatement,$dblink);
	if(!$thequery) reportError(200,"Invlaid SQL statement: ".mysql_error($dblink)." -- ".$querystatement);
	//===================================================================================================
	// Generating PDF File.
	//===================================================================================================
	
	$leftmargin=.5;
	$rightmargin=.5;
	$topmargin=.75;
	$paperwidth=8.5;
	$paperlength=11;
	
	//define the documents and margins
	$pdf=new MEM_IMAGE("P","in","Letter");
	$pdf->SetMargins($leftmargin,$topmargin,$rightmargin);
	$pdf->Open();

	while($therecord=mysql_fetch_array($thequery)) {
		$pdf->AddPage();	
		// Next we set the Title (invoice,work order,order,quote,packing list)
		$the_title="Packing List";
		
		$tempwidth=2.5;
		$tempheight=.25;
		$pdf->SetFont("Arial","B",16);
		$pdf->SetXY(-1*($tempwidth+$rightmargin),$topmargin);
		$pdf->Cell($tempwidth,$tempheight,$the_title,$border_debug,1,"R");
	
		//Next add the company info...
		$tempwidth=1;
		$cname=$_SESSION["company_name"];
		$caddress=$_SESSION["company_address"]."\n".$_SESSION["company_csz"]."\n".$_SESSION["company_phone"];

		// Image from DB, so we need to retieve it and then add it to pdf
		// through the extended memImage function (instead of the image function, that wants a file, not data)
			$querystatement="SELECT file FROM files WHERE id=1";
			$pictureresult=mysql_query($querystatement,$dblink);
			if(!$pictureresult) reportError(300,"Error Retrieving Logo Graphic");
			$thepicture=mysql_fetch_array($pictureresult);
			
		$pdf->MemImage($thepicture["file"],$leftmargin,$topmargin,$tempwidth);	
		
		//next company name
		$pdf->SetXY($tempwidth+$leftmargin,$topmargin);
		$pdf->SetFont("Times","B",12);
		$pdf->Cell(4,$tempheight,$cname,$border_debug,2,"L");
	
		//and last, company address
		$tempnext=$tempheight+$topmargin;
		$tempheight=.13;
		$pdf->SetFont("Times","",8);
		$pdf->MultiCell(4,$tempheight,$caddress,$border_debug);
		$tempnext+=($tempheight*4);
	
	
	
	
		//next the Bill To Box
		$tempnext+=.25;
		$tempheight=1.25;
		$tempwidth=($paperwidth-$leftmargin-$rightmargin)/2-.125;
		$pdf->SetLineWidth(.02);
		$pdf->Rect($leftmargin,$tempnext,$tempwidth,$tempheight);
		$pdf->SetXY($leftmargin+.0625,$tempnext+.0625);
		$pdf->SetFont("Arial","B",8);
		$pdf->Cell($tempwidth-.125,.15,"SOLD TO",$border_debug,2,"L");

		$address="";
		if ($therecord["company"]) $address.=$therecord["company"]."\n";
		$address=$address.$therecord["firstname"]." ".$therecord["lastname"]."\n";
		$address=$address.$therecord["address1"]."\n";
		if ($therecord["address2"]) $address.=$therecord["address2"]."\n";
		$address=$address.$therecord["city"].", ".$therecord["state"]."  ".$therecord["postalcode"]."\n";

		$pdf->SetFont("Arial","B",10);
		$pdf->MultiCell($tempwidth-.125,.13,$address,$border_debug);
		
		//next the *Ship* To Box
		$pdf->Rect($leftmargin+$tempwidth+.25,$tempnext,$tempwidth,$tempheight);
		$pdf->SetXY($leftmargin+$tempwidth+.25+.0625,$tempnext+.0625);
		$pdf->SetFont("Arial","B",8);
		$pdf->Cell($tempwidth-.125,.15,"SHIP TO",$border_debug,2,"L");

		$address="";
		if ($therecord["company"]) $address.=$therecord["company"]."\n";
		$address=$address.$therecord["firstname"]." ".$therecord["lastname"]."\n";
		$address=$address.$therecord["shiptoaddress1"]."\n";
		if ($therecord["shiptoaddress2"]) $address=$address.$therecord["shiptoaddress2"]."\n";
		$address=$address.$therecord["shiptocity"].", ".$therecord["shiptostate"]."  ".$therecord["shiptopostalcode"]."\n";

		$pdf->SetFont("Arial","B",10);
		$pdf->MultiCell($tempwidth-.125,.13,$address,$border_debug);

		$tempnext+=$tempheight;
		

		
		//next the id,date,processedby (maybe paymentmethod)
		$tempnext+=.125;
		$tempheight=.4;
		$pdf->Rect($leftmargin,$tempnext,$paperwidth-$leftmargin-$rightmargin,$tempheight);
		$pdf->SetLineWidth(.01);
		$pdf->Line($leftmargin,$tempnext+$tempheight/2,$paperwidth-$rightmargin,$tempnext+$tempheight/2);
		
		$pdf->SetXY($leftmargin,$tempnext+.05);
		$pdf->SetFont("Arial","",8);
		$pdf->Cell(1,.13,"Order ID",$border_debug,0,"L");
		$pdf->Cell(1,.13,"Order Date",$border_debug,0,"L");
		$pdf->Cell(1,.13,"Client PO",$border_debug,0,"L");
	
		$pdf->SetXY($leftmargin,$tempnext+$tempheight/2+0.03);
		$pdf->Cell(1,.13,$therecord["id"],$border_debug,0,"L");
		$pdf->Cell(1,.13,$therecord["orderdate"],$border_debug,0,"L");
		$pdf->Cell(1,.13,$therecord["ponumber"],$border_debug,0,"L");
			
		$tempnext+=$tempheight+.125;
	
	
		//next construct the line item box (think about having too many line items? how to handle)
		$tempheight=5;
		$pdf->SetLineWidth(.02);		
		$pdf->Rect($leftmargin,$tempnext,$paperwidth-$leftmargin-$rightmargin,$tempheight);
		$tempheight2=.2;
		$pdf->Line($leftmargin,$tempnext+$tempheight2,$paperwidth-$rightmargin,$tempnext+$tempheight2);	
		$pdf->SetXY($leftmargin,$tempnext+.03);

		$partnumberwidth=1.1;
		$qtywidth=.5;
		$unitpricewidth=.6;
		$extendedwidth=.6;
		$partnamewidth=$paperwidth-$leftmargin-$rightmargin-$partnumberwidth-$qtywidth-$unitpricewidth-$extendedwidth;

		$pdf->Cell($partnumberwidth,.14,"Part Number/Memo",$border_debug,0,"L");
		$pdf->Cell($partnamewidth,.14,"Part Name",$border_debug,0,"L");
		$pdf->Cell($qtywidth,.14,"Unit Wt.",$border_debug,0,"C");
		$pdf->Cell($unitpricewidth,.14,"Qty",$border_debug,0,"C");
		$pdf->Cell($extendedwidth,.14,"Wt. Ext.",$border_debug,0,"R");
		
		$tempnext2=$tempnext+$tempheight2+.06;
		// Get line items and loop through them
		$lineitemquery="SELECT products.partname,
						products.partnumber,
						lineitems.quantity,
						lineitems.unitprice,
						lineitems.quantity*lineitems.unitprice as extended,
						lineitems.taxable,
						lineitems.memo,
						products.isprepackaged,
						products.isoversized,
						lineitems.unitweight,
						products.packagesperitem
						FROM lineitems LEFT JOIN products ON lineitems.productid=products.id 
						WHERE invoiceid=".$therecord["id"];
		$lineitems=mysql_query($lineitemquery,$dblink);
		if(!$lineitems) reportError(300,"bad line item query: <br />".mysql_error($dblink)."<br /><br />".$lineitemquery);
	
		$pdf->SetXY($leftmargin,$tempnext2);
		$pdf->SetLineWidth(.01);		
		$pdf->SetDrawColor(200,200,200);
		$total_boxes=0;				
		while($thelineitem = mysql_fetch_array($lineitems)){
			$partnumber=$thelineitem["partnumber"];
			if($thelineitem["isprepackaged"]) $partnumber.="*";
			if($thelineitem["isoversized"]) $partnumber.="+";
			
			$pdf->SetFont("Arial","",8);
			$pdf->Cell($partnumberwidth,.13,$partnumber,$border_debug,0,"L");
			if(strlen($thelineitem["partname"])>90)
				$partname=substr($thelineitem["partname"],0,90)."...";
			else 
				$partname=$thelineitem["partname"];
			$pdf->Cell($partnamewidth,.13,$partname,$border_debug,0,"L");
			$pdf->Cell($qtywidth,.13,number_format($thelineitem["unitweight"],2),$border_debug,0,"C");
			$pdf->Cell($qtywidth,.13,number_format($thelineitem["quantity"],2),$border_debug,0,"C");
			$pdf->Cell($extendedwidth,.13,number_format($thelineitem["extended"],2),$border_debug,1,"R");
			$pdf->SetX($leftmargin+.125);
			$pdf->SetFont("Arial","i",8);
			$thelineitem["memo"].="\n";
			$pdf->MultiCell($paperwidth-$leftmargin-$rightmargin-.25,.13,$thelineitem["memo"],$border_debug);
	
			$pdf->SetX($leftmargin);
			$pdf->Line($leftmargin+.02,$pdf->GetY(),$paperwidth-$rightmargin-.02,$pdf->GetY());	

			if($thelineitem["isprepackaged"]){
				$total_boxes+=$thelineitem["quantity"];
			} else {
				$total_boxes+=$thelineitem["quantity"]*$thelineitem["packagesperitem"];
			}
			
		}// end line item  while statement
		$total_boxes=ceil($total_boxes);
		$pdf->SetDrawColor(0,0,0);		
		
		$tempnext+=$tempheight+.125;
		//next guide for oversized and prepackaged
		$pdf->SetFont("Arial","bi",8);
		$pdf->SetXY($leftmargin,$tempnext-.125-.13);
		$pdf->Cell($paperwidth-$leftmargin-$rightmargin,.13,"* prepackaged items.   + oversized items",$border_debug,2);
		
		
		// Next, Special Instructions
		$tempheight=.75;
		$pdf->SetLineWidth(.02);		
		$pdf->Rect($leftmargin,$tempnext,$paperwidth-$leftmargin-$rightmargin,$tempheight);
		$pdf->SetFont("Arial","b",8);
		$pdf->SetXY($leftmargin,$tempnext+.02);
		$pdf->Cell(2,.15,"Special Instructions",$border_debug,2);
		$pdf->SetFont("Arial","",8);
		$pdf->MultiCell($paperwidth-$leftmargin-$rightmargin,.13,$therecord["specialinstructions"],$border_debug);
		$tempnext+=$tempheight+.125;		


		// now totals...
		$tempheight=.5;
		$pdf->Rect($leftmargin,$tempnext,$paperwidth-$leftmargin-$rightmargin,$tempheight);
		$pdf->SetLineWidth(.01);		
		$pdf->Line($leftmargin,$tempnext+.2,$paperwidth-$rightmargin,$tempnext+.2);

		$shippingmethodwidth=2.5;
		$totalweightwidth=1;
		$shippingwidth=1.5;
		$estimatedboxeswidth=$paperwidth-$leftmargin-$rightmargin-$shippingwidth-$totalweightwidth-$shippingmethodwidth-.03;

		$pdf->SetXY($leftmargin,$tempnext+.04);
		$pdf->Cell($shippingmethodwidth,.13,"Shipping Method",$border_debug,0,"L");
		$pdf->Cell($estimatedboxeswidth,.13,"Estimated Boxes",$border_debug,0,"C");
		$pdf->Cell($totalweightwidth,.13,"Total Weight",$border_debug,0,"C");
		$pdf->Cell($shippingwidth,.13,"Shipping",$border_debug,0,"R");
		
		$pdf->SetFont("Arial","B",10);
		$pdf->SetXY($leftmargin,$tempnext+.2+.03);
		$pdf->Cell($shippingmethodwidth,.15,$therecord["shippingmethod"],$border_debug,0,"L");
		$pdf->Cell($estimatedboxeswidth,.15,$total_boxes,$border_debug,0,"C");
		$pdf->Cell($totalweightwidth,.15,number_format($therecord["totalweight"],2),$border_debug,0,"C");
		$pdf->Cell($shippingwidth,.15,currencyFormat($therecord["shipping"]),$border_debug,0,"R");
		
	}// end fetch_array while loop

	$pdf->Output();
	exit();

?>