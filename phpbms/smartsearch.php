<?php
/*
 $Rev: 375 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2008-01-29 18:01:42 -0700 (Tue, 29 Jan 2008) $
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

include("include/session.php");

class smartSearch{

	var $totalcount = 0;

	function smartSearch($db, $sdbid){

		$this->db = $db;

		$this->getSearchParams($sdbid);

	}//end method init


	function getSearchParams($sdbid){

		$querystatement = "
			SELECT
				*
			FROM
				smartsearches
			WHERE
				id = ".((int) $sdbid);

		$this->searchParams = $this->db->fetchArray($this->db->query($querystatement));

	}//end method - getSearchParams


	function find($term, $offset=0){

		$term = trim(mysql_real_escape_string($term));

		// first we take the entered text and explode int by words
		$terms = explode(" ",$term);

		//next we take the list of fields to search and create an array
		$searchFields = explode(",", $this->searchParams["searchfields"]);

		$wheres="";
		foreach($terms as $value){

			// this series of foreachs builds a SQL OR clause to search
			// the search fields to match things that start with the term
			// or has words inside that start with the term.

			$wheres .="AND (";

			foreach($searchFields as $field)
				$wheres .= trim($field)." LIKE '".$value."%' OR ".trim($field)." LIKE '% ".$value."%'\nOR ";

			$wheres = substr($wheres,0,strlen($wheres)-3);
			$wheres .= ")";

		}//endforeach

		if($wheres){

			$finalsearch = "";
			foreach($searchFields as $field)
				$finalsearch .= trim($field)." LIKE '".$term."%'\nOR ";

			$finalsearch = substr($finalsearch,0,strlen($finalsearch)-3);

			$wheres = "AND ( (".$finalsearch.") OR (".substr($wheres,4)."))";

		}//endif - where

		$securityWhere = "";

		if($this->searchParams["rolefield"]){

			// If the rolefield is present, we need to make sure the rolefield
			// of each record matches the logged in users array of roles

			if ($_SESSION["userinfo"]["admin"] !=1 ){

				if(count($_SESSION["userinfo"]["roles"])>0){

                                        foreach($_SESSION["userinfo"]["roles"] as $role)
                                            $securityWhere .= ", '".$role."'";

					$securityWhere = " AND (".$this->searchParams["rolefield"]." IN ('', ".$securityWhere." ) OR ".$this->searchParams["rolefield"]." IS NULL)";
				} else
					$securityWhere = " AND (".$this->searchParams["rolefield"]." = '' OR ".$this->searchParams["rolefield"]." IS NULL)";

			}//endif admin

		}//endif rolefield

		$querystatement = "
			SELECT DISTINCT
				".$this->searchParams["displayfield"]." AS display,
				".$this->searchParams["valuefield"]." AS value,
				".$this->searchParams["secondaryfield"]." AS secondary,
				".$this->searchParams["classfield"]." AS classname
			FROM
				".$this->searchParams["fromclause"]."
			WHERE
				(".$this->subout($this->searchParams["filterclause"]).")
				".$securityWhere."
				".$wheres."
			ORDER BY
				".$this->searchParams["displayfield"]."
			LIMIT ".((int) $offset).", 8";


		//need to retireve count of all records so
		// the JS can know wheher to put the show more results on.
		$totalCountStatement = "
			SELECT
				COUNT(".$this->searchParams["displayfield"].") AS thecount
			FROM
				".$this->searchParams["fromclause"]."
			WHERE
				(".$this->searchParams["filterclause"].")
				".$securityWhere."
				".$wheres;

		$countrecord = $this->db->fetchArray($this->db->query($totalCountStatement));
		$this->totalcount = $countrecord["thecount"];

		return $this->db->query($querystatement);

	}//end method


	function display($result){
		// This function will spit out a JSON array of records

		$output = "{totalRecords: ".$this->totalcount.", resultRecords: [";

		while($therecord = $this->db->fetchArray($result)){

			$output .= "{display: '".str_replace("'", "\'", formatVariable($therecord["display"],"bbcode"))."',";
			$output .= "value: '".str_replace("'", "\'", formatVariable($therecord["value"]))."',";
			$output .= "secondary: '".str_replace("'", "\'", formatVariable($therecord["secondary"],"bbcode"))."',";
			$output .= "classname: '".str_replace("'", "\'", formatVariable($therecord["classname"]))."'},";

		}//endwhile

		if($output != "{totalRecords: ".$this->totalcount.", resultRecords: [")
			$output = substr($output, 0, strlen($output)-1);

		$output .= "] }";

		header("Content-type: text/plain");
		echo $output;

	}//end method - display


	// replace variables
	// strings with entrys like " {{$ENTRY}} "
	// get everything in the {{ }} evaluated
	function subout($string){

		while(strpos($string,"{{")){
			$start=strpos($string,"{{");
			$startsubout=$start+2;
			$endsubout=strpos($string,"}}");
			$end=$endsubout+2;
			$temp="";
			eval(stripslashes("\$temp=".substr($string,$startsubout,$endsubout-$startsubout).";"));
			$string=substr($string,0,$start).$temp.substr($string,$end);
		}

		return $string;

	}//end function

}//end class


//processing
//=========================================================================
if(isset($_GET["sdbid"]) && isset($_GET["t"])){

	$smartSearch = new smartSearch($db, $_GET["sdbid"]);

	if(!isset($_GET["o"]))
		$_GET["o"] = 0;

	$theresult = $smartSearch->find($_GET["t"],((int) $_GET["o"]));

	if(isset($theresult))
		$smartSearch->display($theresult);

}//end if
?>
