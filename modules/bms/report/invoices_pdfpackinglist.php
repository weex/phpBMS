<?PHP
	require("../../../include/session.php");
	//turn debug borders on to troubleshoot PDF creation (1 or 0)
	$border_debug=0;
	
	if($_SESSION["printing"]["sortorder"])
		$sortorder=$_SESSION["printing"]["sortorder"];
	else
		$sortorder=" ORDER BY invoices.id";

	require("../../../fpdf/fpdf.php");
	
	//Generate the invoice Query
	$thequerystatement="select invoices.id, totalweight, totaltni, totalti, totalcost, taxareaid,
					shippingmethod, invoices.paymentmethod, checkno, bankname, invoices.ccnumber,
					invoices.ccexpiration, invoices.specialinstructions, invoices.printedinstructions, 
					invoices.tax, invoices.shipping, invoices.ccverification,
					clients.firstname, clients.lastname, clients.company,
					clients.address1,clients.address2,clients.city,clients.state,clients.postalcode,
					invoices.address1 as shiptoaddress1,invoices.address2 as shiptoaddress2,invoices.city as shiptocity,
					invoices.state as shiptostate,invoices.postalcode as shiptopostalcode, amountpaid, shipped, trackingno,
					date_Format(invoicedate,\"%c/%e/%Y\") as invoicedate,
					date_Format(orderdate,\"%c/%e/%Y\") as orderdate,
					date_Format(shippeddate,\"%c/%e/%Y\") as shippeddate,
					invoices.totalti-invoices.amountpaid as amountdue,
					
					invoices.createdby, date_Format(invoices.creationdate,\"%c/%e/%Y %T\") as creationdate, 
					invoices.modifiedby, date_Format(invoices.modifieddate,\"%c/%e/%Y %T\") as modifieddate
					from invoices inner join clients on invoices.clientid=clients.id ".$_SESSION["printing"]["whereclause"].$sortorder;
	$thequery=mysql_query($thequerystatement,$dblink);
	if(!$thequery) die("No records, or invlaid SQL statement:<BR>".$thequerystatement);
	//===================================================================================================
	// Generating PDF File.
	//===================================================================================================
	
	$leftmargin=.5;
	$rightmargin=.5;
	$topmargin=.75;
	$paperwidth=8.5;
	$paperlength=11;
	
	//define the documents and margins
	$pdf=new FPDF("P","in","Letter");
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
		$pdf->Image("../../../report/logo.png",$leftmargin,$topmargin,$tempwidth);
		
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
	
		$pdf->SetXY($leftmargin,$tempnext+$tempheight/2+0.03);
		$pdf->Cell(1,.13,$therecord["id"],$border_debug,0,"L");
		$pdf->Cell(1,.13,$therecord["orderdate"],$border_debug,0,"L");
			
		$tempnext+=$tempheight+.125;
	
	
		//next construct the line item box (think about having too many line items? how to handle)
		$tempheight=5;
		$pdf->SetLineWidth(.02);		
		$pdf->Rect($leftmargin,$tempnext,$paperwidth-$leftmargin-$rightmargin,$tempheight);
		$tempheight2=.2;
		$pdf->Line($leftmargin,$tempnext+$tempheight2,$paperwidth-$rightmargin,$tempnext+$tempheight2);	
		$pdf->SetXY($leftmargin,$tempnext+.03);

		$partnumberwidth=1.2;
		$qtywidth=1;
		$unitpricewidth=1;
		$extendedwidth=1;
		$partnamewidth=$paperwidth-$leftmargin-$rightmargin-$partnumberwidth-$qtywidth-$unitpricewidth-$extendedwidth;

		$pdf->Cell($partnumberwidth,.14,"Part Number/Memo",$border_debug,0,"L");
		$pdf->Cell($partnamewidth,.14,"Part Name",$border_debug,0,"L");
		$pdf->Cell($qtywidth,.14,"Unit Wt.",$border_debug,0,"C");
		$pdf->Cell($unitpricewidth,.14,"Qty",$border_debug,0,"C");
		$pdf->Cell($extendedwidth,.14,"Wt. Ext.",$border_debug,0,"R");
		
		$tempnext2=$tempnext+$tempheight2+.06;
		// Get line items and loop through them
		$lineitemquery="select products.partname,products.partnumber,lineitems.quantity,
						isprepackaged,isoversized,packagesperitem,
						lineitems.unitweight,lineitems.quantity*lineitems.unitweight as extended,memo
						from lineitems left join products on lineitems.productid=products.id where invoiceid=".$therecord["id"];
		$lineitems=mysql_query($lineitemquery,$dblink);
		if(!$lineitems) die("bad line item query: ".$lineitemquery);
	
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
			$pdf->Cell($partnamewidth,.13,$thelineitem["partname"],$border_debug,0,"L");
			$pdf->Cell($qtywidth,.13,number_format($thelineitem["unitweight"],2),$border_debug,0,"C");
			$pdf->Cell($qtywidth,.13,number_format($thelineitem["quantity"],2),$border_debug,0,"C");
			$pdf->Cell($extendedwidth,.13,number_format($thelineitem["extended"],2),$border_debug,1,"R");
			$pdf->SetX($leftmargin+.25);
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
		$pdf->Cell($shippingwidth,.15,"\$".number_format($therecord["shipping"],2),$border_debug,0,"R");
		
	}// end fetch_array while loop

	if($border_debug==1){
		$pdf->Output();
	}
	else {
		//write the frickin thing! Need to write to a temp file and then you know...
		chdir("../../../report");
		$file=basename(tempnam(getcwd(),'tmp'));
		chmod($file,744);		
		rename($file,$file.'.pdf');
		$file.='.pdf';
	
		// write to file and then output
		$pdf->Output($file);
		echo "<HTML><SCRIPT>document.location='../../../report/".$file."';</SCRIPT></HTML>";
	}
?>