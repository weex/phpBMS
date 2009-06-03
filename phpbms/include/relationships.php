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
class relationship{

    var $db;
    var $id;

    /**
     * Initializes object
     *
     * @param object $db database object
     * @param integer $id relationship id
     */
    function relationship($db, $id){

        $this->db = $db;
        $this->id = ((int) $id);

    }//end function init


    /**
     * create's and runs the  relationship
     *
     * @param string $theids comma separated list of record ids
     *
     * @return string returns the URL of the new search screen to go to.
     */
    function execute($theids){

	$_SESSION["passedjoinclause"] = "";
	$_SESSION["passedjoinwhere"] = "";

	$querystatement = "
            SELECT
		fromtable.maintable AS fromtable,
                relationships.totableid,
                totable.maintable AS totable,
		relationships.tofield,
                relationships.fromfield,
                relationships.inherint
            FROM
		(relationships INNER JOIN tabledefs AS fromtable ON relationships.fromtableid = fromtable.uuid)
		INNER JOIN tabledefs AS totable ON relationships.totableid = totable.uuid
	    WHERE
                relationships.id=".$this->id;

        $queryresult = $this->db->query($querystatement);

        $therecord = $this->db->fetchArray($queryresult);

	/*
         if the relationship is inherent (already exists due to the display
         nature of the table) then the join clause is not needed
        */
        if($therecord["inherint"] == 0){

            $_SESSION["passedjoinclause"] = "
                INNER JOIN ".$therecord["fromtable"]." ON ".$therecord["totable"].".".$therecord["tofield"]." = ".$therecord["fromtable"].".".$therecord["fromfield"];

        }//end if

	/*
          make the passed where clause for passed id's this will pass the
          saved where clause.
        */
	foreach($theids as $theid)
            $_SESSION["passedjoinwhere"] .= " OR ".$therecord["fromtable"].".id = ".$theid;

	$_SESSION["passedjoinwhere"] = substr($_SESSION["passedjoinwhere"], 3);

	return "search.php?id=".urlencode($therecord["totableid"]);

    }//end function execute

}//end class relationship
?>
