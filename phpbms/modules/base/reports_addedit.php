<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");
	include("include/reports.php");

	$thetable = new reports($db, "tbld:d595ef42-db9d-2233-1b9b-11dfd0db9cbb");
	$therecord = $thetable->processAddEditPage();

        if($therecord["id"]){

            $reportSettings = new reportSettings($db, $therecord["uuid"]);
            $reportSettings->get();

        }//endif

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Report";

	$phpbms->cssIncludes[] = "pages/base/reports.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/reports.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm(NULL, "post", "record", NULL);
                $theform->id = "record";

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("displayorder",$therecord["displayorder"],"display order",true,NULL,10,10);
		$theform->addField($theinput);

		$theinput = new inputBasicList("type",$therecord["type"],array("Report"=>"report","PDF Report"=>"PDF Report","Export"=>"export"));
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"roleid",$therecord["roleid"],"access (role)");
		$theform->addField($theinput);

		if($therecord["id"]){

                    $theinput = new inputField("reportfile",$therecord["reportfile"],"report file",true,NULL,64,128);
                    $theinput->setAttribute("readonly","readonly");
                    $theinput->setAttribute("class","uneditable");

                    $theform->addField($theinput);

                }//endif

                $theinput = new inputTextarea("description", $therecord["description"], NULL, false, 3, 48);
                $theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
<?php $theform->startForm($pageTitle) ?>
	<fieldset id="fsAttributes">
		<legend>Attributes</legend>

                <p><?php $theform->showField("type")?></p>

		<p>
			<label for="tabledefid">report table</label><br />
			<?php $thetable->displayTables("tabledefid",$therecord["tabledefid"]);?><br />
		</p>

		<p>
			<?php $theform->showField("displayorder"); ?><br />
		</p>

		<p><?php $theform->showField("roleid")?></p>

	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend>details</legend>

			<p class="big"><?php $theform->showField("name"); ?></p>


			<p>
                        <?php
                            if($therecord["id"])
                                $theform->showField("reportfile");
                            else
                                $thetable->displayRerportFiles();
                        ?>
                        </p>

			<p class="big"><?php $theform->showField("description"); ?></p>

		</fieldset>

                <fieldset>
                    <legend>settings</legend>
                    <?php if(!$therecord["id"]) {
                        ?><p class="notes">Report settings are available after initial record creation.</p><?php
                    } else {
                      ?>


                        <textarea class="hiddenTextAreas" id="rsUpdates" name="rsUpdates" rows="1" cols="5"></textarea>
                        <textarea class="hiddenTextAreas" id="rsDelList" name="rsDelList" rows="1" cols="5"></textarea>
                        <textarea class="hiddenTextAreas" id="rsAdds"    name="rsAdds" rows="1" cols="5"></textarea>
                        <table class="querytable simple" id="settingsTable" cellspacing="0" cellpadding="0" border="0" summary="report settings">
                            <thead>
                                <tr>
                                    <th align="right">name</th>
                                    <th>value</th>
                                    <th>type</th>
                                    <th width="100%">description</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id="rsTbody">
                                <tr id="addNewRow">
                                    <td>
                                        <input type="text" id="rsAddName"/>
                                    </td>
                                    <td>
                                        <input type="text" id="rsAddValue" size="32"/>
                                    </td>
                                    <td>string</td>
                                    <td>user added setting</td>
                                    <td>
                                        <button title="Add Setting" class="graphicButtons buttonPlus" id="rsButtonAdd" type="button"><span>+</span></button>
                                    </td>
                                </tr>
                                <?php
                                    $reportSettings->display();
                                ?>
                                <tr class="queryfooter" id="rsFooterTr">
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                      <?php
                    }//endif
                    ?>
                </fieldset>

	        <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
                $theform->endForm();
	?>
</div>
<?php include("footer.php");?>
