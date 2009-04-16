<?php
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
