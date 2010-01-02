<?php
/*
 $Rev: 311 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-10-02 19:51:27 -0600 (Tue, 02 Oct 2007) $
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
    //Quick Links
    class wdgtbc3236406497cb6f5897029af7dcb3c9 extends widget{

        var $uuid ="wdgt:bc323640-6497-cb6f-5897-029af7dcb3c9";
        var $type = "little";
        var $title = "System Statistics";
        var $cssIncludes = array('widgets/base/quicklinks.css');

	var $stats = array();

        function displayMiddle(){

	    //Grabbing Database Size
	    $querystatement = "
		SELECT
		    SUM( data_length + index_length) / 1024 / 1024 AS sizedb
		FROM
		    information_schema.TABLES
		WHERE
		    table_schema ='".MYSQL_DATABASE."'";

	    $queryresult = $this->db->query($querystatement);
	    if($this->db->numRows($queryresult)){

		$therecord = $this->db->fetchArray($queryresult);
		$stats["database size"] = round($therecord["sizedb"], 2)." MB";

	    }//end if

	    //Modules Loaded
	    $querystatement = "
		SELECT
		    COUNT(id) AS thecount
		FROM
		    modules";

	    $queryresult = $this->db->query($querystatement);
	    if($this->db->numRows($queryresult)){

		$therecord = $this->db->fetchArray($queryresult);
		$stats["modules loaded"] = $therecord["thecount"];

	    }//end if

	    //Active Users
	    $querystatement = "
		SELECT
		    COUNT(id) AS thecount
		FROM
		    users
		WHERE
		    revoked = 0
		    AND portalaccess = 0";

	    $queryresult = $this->db->query($querystatement);
	    if($this->db->numRows($queryresult)){

		$therecord = $this->db->fetchArray($queryresult);
		$stats["active users"] = $therecord["thecount"];

	    }//end if

	    //Errors In System Log
	    $querystatement = "
		SELECT
		    COUNT(id) AS thecount
		FROM
		    log
		WHERE
		    `type` = 'error'";

	    $queryresult = $this->db->query($querystatement);
	    if($this->db->numRows($queryresult)){

		$therecord = $this->db->fetchArray($queryresult);
		$stats["logged errors"] = $therecord["thecount"];

	    }//end if


	    foreach($stats as $statName => $statValue){
	    ?>
		<p><?php echo formatVariable($statName) ?>: <?php echo formatVariable($statValue) ?></p>
	    <?php
	    }

        }//end function showMiddle

    }//end class workload
?>
