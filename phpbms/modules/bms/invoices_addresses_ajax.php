<?php
/*
 $Rev: 375 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2008-01-29 18:01:42 -0700 (Tue, 29 Jan 2008) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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

include("../../include/session.php");

class clientAddresses{

	function clientAddresses($db){

		$this->db = $db;

	}//end method - init


	function getList($uuid){

		$querystatement = "
			SELECT
				addresses.*,
				addresstorecord.primary,
				addresstorecord.defaultshipto
			FROM
				addresstorecord INNER JOIN addresses ON addresstorecord.addressid = addresses.uuid
			WHERE
				addresstorecord.tabledefid = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083'
				AND addresstorecord.recordid = '".mysql_real_escape_string($uuid)."'";

		return $this->db->query($querystatement);

	}//end method - get


	function displayList($queryresult){

		?><div class="fauxP">
			client addresses<br />
			<div id="LAPickDiv">
			<?php

			if($this->db->numRows($queryresult)){

				while($therecord = $this->db->fetchArray($queryresult)){

					$content = "";
					if($therecord["title"])
						$content .= "<strong>".htmlQuotes($therecord["title"])."</strong><br/>";

					$content .= htmlQuotes($therecord["address1"]);

					if($therecord["address2"])
						$content .= "<br/>".htmlQuotes($therecord["address2"]);

					$content .= "<br/>".htmlQuotes($therecord["city"]);

					if($therecord["state"])
						$content .= ", ".htmlQuotes($therecord["state"]);

					if($therecord["postalcode"])
						$content .= " ".htmlQuotes($therecord["postalcode"]);

					if($therecord["country"])
						$content .= "<br/>".htmlQuotes($therecord["country"]);

					?><a href="#" id="LA-<?php echo $therecord["uuid"]?>" class="LAPickAs"><?php echo $content?></a><?php

				}//endwhile - therecord

			} else {

				?><p><em>no records found</em></p><?php

			}//endif numrows

			?>
			</div>
		</div>

		<p align="right">
			<button type="button" class="disabledButtons addressButtons" id="LALoadButton">load</button>
			<button type="button" class="Buttons addressButtons" id="LACancelButton">cancel</button>
		</p>
		<?php

	}//end method - display

	function showSingle($uuid){

		$querystatement = "
			SELECT
				*
			FROM
				addresses
			WHERE
				uuid ='".mysql_real_escape_string($uuid)."'";

		$therecord = $this->db->fetchArray($this->db->query($querystatement));

		$output = "{";

		foreach($therecord as $key=>$value)
			$output .= $key .": '".str_replace("'","\\'",htmlQuotes($value))."' ,";

		$output = substr($output, 0, -1);
		$output .= "}";

		echo $output;

	}//end method - showSingle

}//end class


//processing
//=========================================================================
if(isset($_GET["w"]) && isset($_GET["id"])){

	$addresses = new clientAddresses($db);

	switch($_GET["w"]){

		case "s":
			//single address record
			$addresses->showSingle($_GET["id"]);
			break;

		case "l":
			//list of addresses for client
			$addressResult = $addresses->getList($_GET["id"]);

			$addresses->displayList($addressResult);

			break;

	}//endswitch - get w

}//end if
?>
