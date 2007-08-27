<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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
	//reload settings in latin1 (fpdf doesn't like utf)
	$phpbmsSession->loadSettings("latin1");
	//turn debug borders on to troubleshoot PDF creation (1 or 0)
	$border_debug=0;
	
	if(!$_SESSION["printing"]) $error = new appError(300,"Session Timeout Error");
	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder=" ORDER BY invoices.id";

	require_once("../../../fpdf/fpdf.php");
	require_once("../../../fpdf/mem_image.php");
	
	
	//Generate the invoice Query
	$querystatement="SELECT invoices.id, totalweight, totaltni, totalti, totalcost, invoices.taxareaid,
					shippingmethods.name as shippingmethod, paymentmethods.name as paymentmethod, checkno, bankname, invoices.ccnumber,
					invoices.ccexpiration, specialinstructions, printedinstructions, tax, shipping,
					clients.firstname, clients.lastname, clients.company,
					clients.email, clients.workphone, clients.homephone, clients.country, invoices.country as shiptocountry,
					clients.address1,clients.address2,clients.city,clients.state,clients.postalcode, 
					invoices.address1 as shiptoaddress1,invoices.address2 as shiptoaddress2,invoices.city as shiptocity, 
					invoices.state as shiptostate,invoices.postalcode as shiptopostalcode, amountpaid, trackingno,
					invoicedate,
					orderdate,
					invoices.totalti-invoices.amountpaid as amountdue,
					invoices.ponumber,invoices.discountamount,invoices.discountid,
					
					invoices.createdby, invoices.creationdate, 
					invoices.modifiedby, invoices.modifieddate
					FROM ((invoices INNER JOIN  clients ON invoices.clientid=clients.id) 
						LEFT JOIN paymentmethods ON paymentmethods.id=invoices.paymentmethodid)
						LEFT JOIN shippingmethods ON shippingmethods.id=invoices.shippingmethodid
					".$_SESSION["printing"]["whereclause"].$sortorder;
	$thequery=$db->query($querystatement);

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

	while($therecord=$db->fetchArray($thequery)) {
		$pdf->AddPage();	
		// Next we set the Title (invoice,work order,order,quote,packing list)
		$the_title="Invoice";
		
		$tempwidth=2.5;
		$tempheight=.25;
		$pdf->SetFont("Arial","B",16);
		$pdf->SetXY(-1*($tempwidth+$rightmargin),$topmargin);
		$pdf->Cell($tempwidth,$tempheight,$the_title,$border_debug,1,"R");
	
		//Next add the company info...
		$tempwidth=1;
		$cname=COMPANY_NAME;
		$caddress=COMPANY_ADDRESS."\n".COMPANY_CSZ."\n".COMPANY_PHONE;
		
		
		// Image from DB, so we need to retieve it and then add it to pdf
		// through the extended memImage function (instead of the image function, that wants a file, not data)
			$querystatement="SELECT file,upper(`type`)as `type` FROM files WHERE id=1";
			$pictureresult=$db->query($querystatement);
			if(!$pictureresult) $error = new appError(300,"Error Retrieving Logo Graphic");
			$thepicture=$db->fetchArray($pictureresult);
		
		if($thepicture["type"]=="IMAGE/JPEG"){
			$image = $thepicture["file"];
			$pdf->Image('var://image',$leftmargin,$topmargin,$tempwidth,0,"JPEG");	
		}
		elseif($thepicture["type"]=="IMAGE/PNG")
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

		$displayName="";
		if($therecord["company"]){
			$displayName.=$therecord["company"];
			if($therecord["lastname"])
				$displayName.=" (".$therecord["firstname"]." ".$therecord["lastname"].")";
		}else
			$displayName.=$therecord["firstname"]." ".$therecord["lastname"];
		
		$address=$therecord["address1"];
		if ($therecord["address2"]) $address.="\n".$therecord["address2"];
		$address.="\n".$therecord["city"].", ".$therecord["state"]."  ".$therecord["postalcode"];
		if($therecord["country"]) $address.=" ".$therecord["country"];
		
		$phoneemail="";
		if($therecord["workphone"] || $therecord["homephone"]){
			$phoneemail=$therecord["homephone"]." (H)";
			if($therecord["workphone"])
				$phoneemail=$therecord["workphone"]." (W)";
			$phoneemail.="\n";
		}
		if($therecord["email"])
			$phoneemail.=$therecord["email"];
		if($phoneemail)
			$address.="\n\n".$phoneemail;
		

		$pdf->SetFont("Arial","B",10);
		$pdf->Cell($tempwidth-.125,.16,$displayName,$border_debug,2,"L");
		$pdf->SetFont("Arial","B",9);
		$pdf->MultiCell($tempwidth-.125,.13,$address,$border_debug);
		
		//next the *Ship* To Box
		$pdf->Rect($leftmargin+$tempwidth+.25,$tempnext,$tempwidth,$tempheight);
		$pdf->SetXY($leftmargin+$tempwidth+.25+.0625,$tempnext+.0625);
		$pdf->SetFont("Arial","B",8);
		$pdf->Cell($tempwidth-.125,.15,"SHIP TO",$border_debug,2,"L");

		$address="";
		$address=$therecord["shiptoaddress1"];
		if ($therecord["shiptoaddress2"]) $address.="\n".$therecord["shiptoaddress2"];
		$address.="\n".$therecord["shiptocity"].", ".$therecord["shiptostate"]."  ".$therecord["shiptopostalcode"];
		if($therecord["shiptocountry"]) $address.=" ".$therecord["shiptocountry"];

		$pdf->SetFont("Arial","B",10);
		$pdf->Cell($tempwidth-.125,.16,$displayName,$border_debug,2,"L");
		$pdf->SetFont("Arial","B",9);
		$pdf->MultiCell($tempwidth-.125,.13,$address,$border_debug);

		//if they have a shipping method, print it.
		if ($therecord["shippingmethod"]){
			$tempheight2=.13;
			$pdf->SetXY($leftmargin+$tempwidth+.25+.0625,$tempnext+$tempheight-$tempheight2-.125);
			$pdf->SetFont("Arial","B",8);
			$shipping_text="Shipping Method: ";
			$pdf->Cell($pdf->GetStringWidth($shipping_text)+.0625,$tempheight2,$shipping_text,$border_debug,0,"R");
			$pdf->SetFont("Arial","",8);
			$pdf->Cell($pdf->GetStringWidth($therecord["shippingmethod"])+.0625,$tempheight2,$therecord["shippingmethod"],$border_debug,0,"L");
		} //end shipping method

		$tempnext+=$tempheight;
		

		
		//next the id,date,processedby (maybe paymentmethod)
		$tempnext+=.125;
		$tempheight=.4;
		$pdf->Rect($leftmargin,$tempnext,$paperwidth-$leftmargin-$rightmargin,$tempheight);
		$pdf->SetLineWidth(.01);
		$pdf->Line($leftmargin,$tempnext+$tempheight/2,$paperwidth-$rightmargin,$tempnext+$tempheight/2);
		
		$pdf->SetXY($leftmargin,$tempnext+.05);
		$pdf->SetFont("Arial","",8);
		$pdf->Cell(.75,.13,"Order ID",$border_debug,0,"L");
		$pdf->Cell(.75,.13,"Order Date",$border_debug,0,"L");
		$pdf->Cell(.75,.13,"Client PO",$border_debug,0,"L");
		$pdf->Cell(2.25,.13,"Processed by",$border_debug,0,"L");
		$pdf->SetX($paperwidth-$rightmargin-1.5);
		$pdf->Cell(1.5,.13,"Payment Method",$border_debug,0,"L");
	
		$pdf->SetXY($leftmargin,$tempnext+$tempheight/2+0.03);
		$pdf->Cell(.75,.13,$therecord["id"],$border_debug,0,"L");
		$pdf->Cell(.75,.13,formatFromSQLDate($therecord["orderdate"]),$border_debug,0,"L");
		$pdf->Cell(.75,.13,$therecord["ponumber"],$border_debug,0,"L");
		
		// THe last person who modified the record is the person who processed the order
		$getuserstatement="select firstname,lastname from users where id=".$therecord["modifiedby"];
		$userquery=$db->query($getuserstatement);
		$userrecord=$db->fetchArray($userquery);
		
		$pdf->Cell(2.25,.13,$userrecord["firstname"]." ".$userrecord["lastname"],$border_debug,0,"L");
		$pdf->SetX($paperwidth-$rightmargin-1.5);
		$pdf->Cell(1.5,.13,$therecord["paymentmethod"],$border_debug,0,"L");
	
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
		$taxablewidth=.3;
		$partnamewidth=$paperwidth-$leftmargin-$rightmargin-$partnumberwidth-$qtywidth-$unitpricewidth-$extendedwidth-$taxablewidth;

		$pdf->Cell($partnumberwidth,.14,"Part Number/Memo",$border_debug,0,"L");
		$pdf->Cell($partnamewidth,.14,"Part Name",$border_debug,0,"L");
		$pdf->Cell($taxablewidth,.14,"Tax",$border_debug,0,"C");
		$pdf->Cell($qtywidth,.14,"Qty",$border_debug,0,"C");
		$pdf->Cell($unitpricewidth,.14,"Unit Price",$border_debug,0,"R");
		$pdf->Cell($extendedwidth,.14,"Extended",$border_debug,0,"R");
		
		$tempnext2=$tempnext+$tempheight2+.06;
		// Get line items and loop through them
		$lineitemquery="SELECT products.partname,
						products.partnumber,
						lineitems.quantity,
						lineitems.unitprice,
						lineitems.quantity*lineitems.unitprice as extended,
						lineitems.taxable,
						lineitems.memo
						FROM lineitems LEFT JOIN products ON lineitems.productid=products.id 
						WHERE invoiceid=".$therecord["id"];
		$lineitems=$db->query($lineitemquery);
		if(!$lineitems) die("bad line item query: ".$lineitemquery);
	
		$pdf->SetXY($leftmargin,$tempnext2);
		$pdf->SetLineWidth(.01);		
		$pdf->SetDrawColor(200,200,200);		
		while($thelineitem = $db->fetchArray($lineitems)){
			
			$pdf->SetFont("Arial","",8);
			$pdf->Cell($partnumberwidth,.13,$thelineitem["partnumber"],$border_debug,0,"L");
			if(strlen($thelineitem["partname"])>85)
				$partname=substr($thelineitem["partname"],0,85)."...";
			else 
				$partname=$thelineitem["partname"];
			$pdf->Cell($partnamewidth,.13,$partname,$border_debug,0,"L");
			$thelineitem["taxable"]=booleanFormat($thelineitem["taxable"]);
			if($thelineitem["taxable"]=="&middot;")$thelineitem["taxable"]=" ";
			$pdf->Cell($taxablewidth,.13,$thelineitem["taxable"],$border_debug,0,"C");
			$pdf->Cell($qtywidth,.13,number_format($thelineitem["quantity"],2),$border_debug,0,"C");
			$pdf->Cell($unitpricewidth,.13,numberToCurrency($thelineitem["unitprice"]),$border_debug,0,"R");
			$pdf->Cell($extendedwidth,.13,numberToCurrency($thelineitem["extended"]),$border_debug,1,"R");
			$pdf->SetX($leftmargin+.125);
			$pdf->SetFont("Arial","i",8);
			$thelineitem["memo"].="\n";
			$pdf->MultiCell($paperwidth-$leftmargin-$rightmargin-.25,.13,$thelineitem["memo"],$border_debug);
	
			$pdf->SetX($leftmargin);
			$pdf->Line($leftmargin+.02,$pdf->GetY(),$paperwidth-$rightmargin-.02,$pdf->GetY());	
			
		}// end line item  while statement
		$pdf->SetDrawColor(0,0,0);		
		
		$tempnext+=$tempheight+.125;
		
		// Next, Special Instructions
		$instructions=$therecord["printedinstructions"];
		if($therecord["discountid"]!=0){
			$querystatement="SELECT description FROM discounts WHERE id=".$therecord["discountid"];
			$discountresult=$db->query($querystatement);

			$discountrecord=$db->fetchArray($discountresult);
			$instructions.="\n".$discountrecord["description"];
		}
		$tempheight=.75;
		$pdf->SetLineWidth(.02);		
		$pdf->Rect($leftmargin,$tempnext,$paperwidth-$leftmargin-$rightmargin,$tempheight);
		$pdf->SetFont("Arial","b",8);
		$pdf->SetXY($leftmargin,$tempnext+.02);
		$pdf->Cell(2,.15,"Special Instructions",$border_debug,2);
		$pdf->SetFont("Arial","",8);
		$pdf->MultiCell($paperwidth-$leftmargin-$rightmargin,.13,$instructions,$border_debug);
		$tempnext+=$tempheight+.125;
		
		// now totals...
		$tempheight=.625;
		$pdf->Rect($leftmargin,$tempnext,$paperwidth-$leftmargin-$rightmargin,$tempheight);
		$pdf->SetLineWidth(.01);		
		$pdf->Line($leftmargin,$tempnext+.2,$paperwidth-$rightmargin,$tempnext+.2);

		$taxwidth=.75;
		if($therecord["discountamount"]!=0)
			$discountwidth=.75;
		else
			$discountwidth=0;
		$shippingwidth=.75;
		$totalwidth=.75;
		$amountduewidth=1;
		$totaltniwidth=$paperwidth-$leftmargin-$rightmargin-$totalwidth-$taxwidth-$shippingwidth-$amountduewidth-$discountwidth-0.03;	

		$pdf->SetXY($leftmargin,$tempnext+.04);
		if($therecord["discountamount"]!=0)
			$pdf->Cell($discountwidth,.13,"Discount",$border_debug,0,"L");
		$pdf->Cell($totaltniwidth,.13,"Subtotal",$border_debug,0,"R");
		$pdf->Cell($taxwidth,.13,"Sales Tax",$border_debug,0,"R");
		$pdf->Cell($shippingwidth,.13,"Shipping",$border_debug,0,"R");
		$pdf->Cell($totalwidth,.13,"Total",$border_debug,0,"R");
		$pdf->Cell($amountduewidth,.13,"Amount Due",$border_debug,0,"R");
		
		$pdf->SetFont("Arial","B",10);
		$pdf->SetXY($leftmargin,$tempnext+.2+.03);
		if($therecord["discountamount"]!=0)
			$pdf->Cell($discountwidth,.15,numberToCurrency($therecord["discountamount"]),$border_debug,0,"L");
		$pdf->Cell($totaltniwidth,.15,numberToCurrency($therecord["totaltni"]),$border_debug,0,"R");
		$pdf->Cell($taxwidth,.15,numberToCurrency($therecord["tax"]),$border_debug,0,"R");
		$pdf->Cell($shippingwidth,.15,numberToCurrency($therecord["shipping"]),$border_debug,0,"R");
		$pdf->Cell($totalwidth,.15,numberToCurrency($therecord["totalti"]),$border_debug,0,"R");
		$pdf->Cell($amountduewidth,.15,numberToCurrency($therecord["amountdue"]),$border_debug,0,"R");
		
		// If a tax area is defined, print the tax information
		if($therecord["taxareaid"]) {
			$taxstatement="SELECT id, name, percentage FROM tax WHERE id=".$therecord["taxareaid"];
			$taxquery=$db->query($taxstatement);

			$taxrecord=$db->fetchArray($taxquery);
			$pdf->SetFont("Arial","",7);
			$pdf->SetXY($leftmargin,$tempnext+.2+.2);
			$pdf->Cell($totaltniwidth+$taxwidth+$discountwidth,.13,"(".$taxrecord["name"]." ".$taxrecord["percentage"]."%)",$border_debug,0,"R");
		}

		
		
	}// end fetch_array while loop
	
	$pdf->Output();
	exit();

?>