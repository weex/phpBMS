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
	if(!class_exists("phpbmsReport"))
		include("report_class.php");

	class generalTablePrint extends phpbmsReport {
	
		var $maintable = "";
		var $resultOutput = "";
		
		function generalTablePrint($db, $tabledefid){
		
			$this->tabledefid = ((int) $tabledefid);
			
			parent::phpbmsReport($db);

			$querystatement = "
				SELECT 
					maintable, displayname
				FROM 
					tabledefs
				WHERE 
					id=".((int) $tabledefid);
					
			$queryresult = $db->query($querystatement); 
			$therecord=$db->fetchArray($queryresult);
			
			$this->maintable = $therecord["maintable"];
			$this->displayname = $therecord["displayname"];
			
		}//end method

		
		function generate(){
		
			$querystatement = "
				SELECT 
					* 
				FROM 
					".$this->maintable;

			$querystatement = $this->assembleSQL($querystatement);
			
			$queryresult = $this->db->query($querystatement);

			$num_fields = $this->db->numFields($queryresult);

			ob_start();

			?>
			<div id="container">
				<h1><?php echo formatVariable($this->displayname)?></h1>
				<table id="results">
					<thead>
						<tr>
			<?php
			
			for($i=0;$i<$num_fields;$i++){
				
				?>
					<th <?php if($i == $num_fields-1) echo 'id="lastHeader"' ?>><?php echo $this->db->fieldName($queryresult, $i); ?></th>
					
				<?php 
			
			}//end for
			
			?>
						</tr>
					</thead>
					
					<tbody>
			<?php 
		
			while($therecord = $this->db->fetchArray($queryresult)){
				
				?><tr><?php 
				
				foreach($therecord as $value){
					
					?><td><?php echo formatVariable($value)?></td><?php
				
				}//end foreach

				?></tr><?php 

			}//endwhile

			?>
					</tbody>					
					
				</table>
			</div>
			<?php

			$this->reportOutput = ob_get_contents();
			ob_end_clean();			
		
		}//end method
		
		
		function show(){
		
			global $phpbms;
			$db = &$this->db;
			
			$phpbms->cssIncludes[] = "reports.css";
			$phpbms->cssIncludes[] = "pages/generaltableprint.css";
			
			$phpbms->showMenu = false;
			$phpbms->showFooter = false;
			
			include("header.php");
			
			echo $this->reportOutput;
			
			include("footer.php");

		}//end method
		
	}//end class
	
	
	//PROCESSING 
	//========================================================================
	
	if(!isset($noOutput)){

		session_cache_limiter('private');
	
		require("../include/session.php");
		if(!isset($_GET["tid"])) 
			$error = new appError(200,"URL variable missing: tid");
	
		$report = new generalTablePrint($db, $_GET["tid"]);
		$report->setupFromPrintScreen();
		$report->generate();	
		$report->show();	
		
	}//end if
	
?>