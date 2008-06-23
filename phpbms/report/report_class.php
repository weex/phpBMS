<?php
/*
 $Rev: 290 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 18:15:00 -0600 (Mon, 27 Aug 2007) $
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

	class phpbmsReport{
		
		var $db;
		var $whereclause ="";
		var $sortorder = "";
		var $reportOutput = "";
		var $groupBy = "";
		
		function phpBMSReport($db){
		
			$this->db = $db;
		
		}//end method

		
		function setupFromPrintScreen(){
		
			if(isset($_SESSION["printing"]["sortorder"]))
				$this->sortorder = $_SESSION["printing"]["sortorder"];
				
			if(isset($_SESSION["printing"]["whereclause"]))
				$this->whereclause = $_SESSION["printing"]["whereclause"];
			
			//backwards compatibility
			if(strpos($this->whereclause, "where ") === 0)
				$this->whereclause = substr($this->whereclause, 6);			
		
		}//end method

		
		function assembleSQL($querystatement){
			
			if($this->whereclause)
				$querystatement .= "
					WHERE
						".$this->whereclause;
			
			if($this->groupBy)
				$querystatement .= "
					GROUP BY
						".$this->groupBy;			
			
			if($this->sortorder)
				$querystatement .= "
					ORDER BY
						".$this->sortorder;

			return $querystatement;
			
		}//end method
		
		function _showNoRecords(){
			
			?>
			<h1 id="noRecord">No Records</h1>
			<p>No valid records for this report.</p>
			<?php
			
		}//end method --_showNoRecords--
	
	}//end class

?>