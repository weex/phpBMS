<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
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
if(class_exists("phpbmsTable")){
	class discounts extends phpbmsTable{

		function getDefaults(){

			$therecord = parent::getDefaults();

			$therecord["type"]="percent";

			return $therecord;
		}


		function verifyVariables($variables){

			//table's default is fine
			if(isset($variables["type"])){

				switch($variables["type"]){

					case "percent":
					case "amount":
					break;

					default:
						$this->verifyErrors[] = "The value of the `type` field is invalid.
							It must be either 'percent' or 'amount'.";
					break;

				}//end switch

			}//end if

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function prepareVariables($variables){

			if($variables["type"] == "percent")
				$variables["value"] = ((real) str_replace("%","",$variables["percentvalue"]));
			else
				$variables["value"] =  currencyToNumber($variables["amountvalue"]);

			return $variables;
		}


		function getTotals($id=0){

			$returnArray["Invoice"]["total"]=0;
			$returnArray["Invoice"]["sum"]=0;
			$returnArray["Order"]["total"]=0;
			$returnArray["Order"]["sum"]=0;

			if($id>0){
				$querystatement="SELECT invoices.type,count(invoices.id) as total,sum(discountamount) as sum
								FROM discounts inner join invoices on discounts.id=invoices.discountid
								WHERE discounts.id=".((int) $id)." and (invoices.type=\"Order\" or invoices.type=\"Invoice\") GROUP BY invoices.type";
				$queryresult = $this->db->query($querystatement);

				while($therecord=$this->db->fetchArray($queryresult)){
					$returnArray[$therecord["type"]]["total"]=$therecord["total"];
					$returnArray[$therecord["type"]]["sum"]=$therecord["sum"];
				}

			}

			return $returnArray;

		}//end function

	}//end class
}//end if
?>