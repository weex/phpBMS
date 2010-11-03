<?php
/*
 $Rev: 267 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-14 13:08:27 -0600 (Tue, 14 Aug 2007) $
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
require_once("../../include/session.php");

$querystatement = "
    SELECT
        roleid
    FROM
        tableoptions
    WHERE
        name= 'massemail'
        AND tabledefid = 'tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083'
        ";

$queryresult = $db->query($querystatement);

$therecord = $db->fetchArray($queryresult);

if(!hasRights($therecord["roleid"]))
    goURL(APP_PATH."noaccess.php");

$pageTitle = "MailChimp List Sync";

$phpbms->showMenu = false;

$phpbms->cssIncludes[] = "pages/print.css";
$phpbms->cssIncludes[] = "pages/mailchimp/manual_sync.css";

$phpbms->jsIncludes[] = "modules/mailchimp/javascript/manual_list_sync.js";

include("header.php");
?>

<div class="bodyline" id="mainbody">
	<h1><?php echo $pageTitle ?><a name="top"></a></h1>
	<p>
		The sync process will pull changes made on the MailChimp side, and push
		local phpbms changes to MailChimp.
	</p>
	<p>
		<button id="sync" class="Buttons"><span>sync</span></button>
		<span id="resultPic"></span>
	</p>
	<p>
	<button type="button" class="graphicButtons buttonDown" id="showResults"><span>show results</span></button>
	</p>
    <div id="resultDiv">
		<p>
			<textarea readonly="readonly" rows="10" cols="98" class="results" id="resultText" name="results"></textarea>
		</p>
	</div>
	<p id="cancelP">
		<button type="button" class="Buttons" id="cancelButton" accesskey="x" title="access key='x'"><span id="cancelSpan">cancel</span></button>
	</p>
</div>
<?php include("footer.php")?>
