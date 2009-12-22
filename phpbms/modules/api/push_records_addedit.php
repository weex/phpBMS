<?php
/*
 $Rev: 643 $ | $LastChangedBy: nate $
 $LastChangedDate: 2009-09-02 14:00:56 -0600 (Wed, 02 Sep 2009) $
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

	include("../../include/session.php");
	include("include/fields.php");
	include("include/tables.php");
    include("include/pushrecords.php");

	$thetable = new pushrecords($db,"tbld:73adc80f-7f0e-e340-937e-41194c5bda29");
	$therecord = $thetable->processAddEditPage();


	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

    $phpbms->jsIncludes[] = "modules/api/javascript/pushrecords.js";
    $phpbms->cssIncludes[] = "pages/api/pushrecords.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm(NULL, "post", "record", NULL);
        $theform->id = $theform->name;

        /**
          *  Attributes
          */
        
        $theinput = new inputDataTableList($db, "originuuid", $therecord["originuuid"], "`tabledefs`",
                                           "`uuid`", "`displayname`", "", "`displayname` ASC",
                                           false, "origin table definition");
        $theform->addField($theinput);

        $theinput = new inputDataTableList($db, "destuuid", $therecord["destuuid"], "`tabledefs`",
                                           "`uuid`", "`displayname`", "", "`displayname` ASC",
                                           false, "destination table definition");
        $theform->addField($theinput);

        $theinput = new inputCheckbox("usecustomdestuuid", $therecord["usecustomdestuuid"], "override destination tabledef uuid");
        $theform->addField($theinput);

        $theinput = new inputField("customdestuuid", $therecord["customdestuuid"], "custom destination tabledef uuid");
        $theform->addField($theinput);

        $thelist = array("Insert / Replace" => "insert", "Update" => "update", "Custom..." => "custom");
        $theinput = new inputBasicList("apicommand", $therecord["apicommand"], $thelist, "command");
        $theform->addField($theinput);

        $theinput = new inputField("customcommand", $therecord["customcommand"], "custom command");
        $theform->addField($theinput);
        
        /**
          *  API Options 
          */
        
        $theinput = new inputCheckbox("keepdestid", $therecord["keepdestid"], "keep destination record id");
        $theform->addField($theinput);
        
        $theinput = new inputCheckbox("useuuid", $therecord["useuuid"], "uuid id field is primary");
        $theform->addField($theinput);
        
        $list = array(
            "SQL"=>"SQL",
            "English, US" => "English, US",
            "English, UK" => "English, UK",
            "Dutch, NL" => "Dutch, NL"
        );
        $theinput = new inputBasicList("dateformat", $therecord["dateformat"], $list, "data date format");
        $theform->addField($theinput);
        
        $list = array(
            "24 Hour"=>"24 Hour",
            "12 Hour" => "12 Hour"
        );
        $theinput = new inputBasicList("timeformat", $therecord["timeformat"], $list, "data time format");
        $theform->addField($theinput);
        
        $theinput = new inputTextarea("extraoptions", $therecord["extraoptions"], "additonal options");
        $theform->addField($theinput);
        

		/**
          *  Script Information
          */
        
        $theinput = new inputField("name",$therecord["name"], "name",true,NULL,28,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

        $thelist = array("All" => "all", "Selected Records" => "select", "Saved Search" => "custom");
        $theinput = new inputBasicList("whereselection", $therecord["whereselection"], $thelist, "which records");
        $theform->addField($theinput);
        
        $theinput = new inputDataTableList($db, "customwhere", $therecord["customwhere"], "usersearches", "uuid", "name",
                                           "`tabledefid`='".mysql_real_escape_string($therecord["originuuid"])."' AND `type` = 'SCH'", "name ASC", true,
                                           "Saved Search", true, "");
        $theform->addField($theinput);

        /**
          *  Server Information
          */
        
        $thelist = array("POST" => "POST", "GET" => "GET");
        $theinput = new inputBasicList("httpformat", $therecord["httpformat"], $thelist, "http format");
        $theform->addField($theinput);

        $thelist = array("JSON" => "json");
        $theinput = new inputBasicList("dataformat", $therecord["dataformat"], $thelist, "api data format");
        $theform->addField($theinput);

        $theinput = new inputCheckbox("ssl", $therecord["ssl"], "ssl");
        $theform->addField($theinput);

        $theinput = new inputField("server", $therecord["server"], "server", true);
        $theform->addField($theinput);

        $theinput = new inputField("destscript",$therecord["destscript"], "script path from the server", true);
		$theform->addField($theinput);

        $theinput = new inputField("port", $therecord["port"], "port (if different from 80 or 443)", false, "integer", 12, 12);
        $theform->addField($theinput);

        $theinput = new inputField("apiusername",$therecord["apiusername"], "api username", true);
		$theform->addField($theinput);

        $theinput = new inputField("apipassword",$therecord["apipassword"], "api password", false);
		$theform->addField($theinput);

        $thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements


	$pageTitle = "Push Record";

	include("header.php");
?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

    <div id="fsAttributes">
        <fieldset>
            <legend>attributes</legend>
                <p><?php $theform->showfield("originuuid"); ?></p>
                
                <p>
                    <?php $theform->showfield("usecustomdestuuid"); ?>
                </p>
                <p>
                    <span id="destuuidspan">
                        <?php $theform->showfield("destuuid"); ?>
                    </span>
                    <span id="customdestuuidspan">
                        <?php $theform->showfield("customdestuuid"); ?>
                    </span>
                </p>
                
                <p><?php $theform->showfield("apicommand"); ?></p>
                <p>
                    <span id="customcommandspan">
                        <?php $theform->showfield("customcommand"); ?>
                    </span>
                </p>
        </fieldset>
    </div>
	<div id="nameDiv">
		<fieldset>
			<legend>Script Information &amp; Options</legend>

			<p class="big"><?php $theform->showField("name"); ?></p>

            <p>
                <?php $theform->showfield("whereselection"); ?>
                <span id="customwherespan">
                    <br/>
                <?php $theform->showfield("customwhere"); ?>
                </span>
            </p>
            <p><button type="button" class="graphicButtons buttonDown" id="showoptions"><span>advanced options</span></button></p>
            <div id="moreoptions">
                <p class="notes">
                    Option defaults are recommended for phpbms to phpbms pushes.
                </p>
                <p><?php $theform->showField("keepdestid"); ?></p>
                <p><?php $theform->showField("useuuid"); ?></p>
                <p><?php $theform->showField("dateformat"); ?></p>
                <p><?php $theform->showField("timeformat"); ?></p>
                <p>
                    <span class="notes">
                        Extra options can be added in a json object format of
                        option:value. Example: { "var0":"value0", "var1":"value1",...}
                    </span>
                    <br/>
                    <?php $theform->showField("extraoptions"); ?>
                </p>
            </div>
		</fieldset>
        <fieldset>
            <legend>Server Information</legend>
            
            <p>
                <span class="notes">
                    POST http format is hightly recommended, especially for
                    large pushes and for bms to bms pushes.
                </span>
                <br/>
                <?php $theform->showfield("httpformat"); ?>
                <?php $theform->showfield("ssl"); ?>
            </p>
            <p id="portP"><?php $theform->showfield("port"); ?></p>
            
            <p><?php $theform->showfield("server"); ?></p>
            <p><?php $theform->showField("destscript"); ?></p>
            
            <p><?php $theform->showfield("apiusername"); ?></p>
            <p><?php $theform->showfield("apipassword"); ?></p>
            
            
            <p><?php $theform->showfield("dataformat"); ?></p>

        </fieldset>
                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>
	</div>
	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
