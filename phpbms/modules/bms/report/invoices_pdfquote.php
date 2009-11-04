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
if(!isset($_SESSION["userinfo"]["id"])){

	//IE needs caching to be set to private in order to display PDFS
	session_cache_limiter('private');

	//set encoding to latin1 (fpdf doesnt like utf8)
	$sqlEncoding = "latin1";
	require_once("../../../include/session.php");

}//end if

if(!class_exists("invoicePDF"))
	include("invoices_pdf_class.php");

class  quotePDF extends invoicePDF{

	var $title = "Quote";

	function quotePDF($db, $orientation='P', $unit='mm', $format='Letter'){

		$this->invoicePDF($db, $orientation, $unit, $format);

	}//end method

	function initialize(){
		parent::initialize();

		unset($this->totalsinfo[5]);

	}//end method

}//end class

//PROCESSING
//=============================================================================
if(!isset($noOutput)){

	$report = new quotePDF($db, 'P', 'in', 'Letter');
	$report->showShipNameInShipTo = false;

	$report->setupFromPrintScreen();
	$report->generate();
	
	$filename = "Quote";
	if($report->count === 1){
		
		if($report->invoicerecord["company"])
			$filename .= "_".$report->invoicerecord["company"];
		
		$filename .= "_".$report->invoicerecord["id"];
		
	}elseif((int)$report->count)
		$filename .= "_Multiple";
	
	$filename .= ".pdf";
	
	$report->output('screen', $filename);

}//end if


?>