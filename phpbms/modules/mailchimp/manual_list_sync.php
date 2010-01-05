<?php
require_once("../../include/session.php");

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