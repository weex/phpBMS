<?php
require_once("../../include/session.php");

$pageTitle = "Manual List Sync";

$phpbms->showMenu = false;

$phpbms->cssIncludes[] = "pages/print.css";
$phpbms->cssIncludes[] = "pages/mailchimp/manual_sync.css";

$phpbms->jsIncludes[] = "modules/mailchimp/javascript/manual_list_sync.js";

//$phpbms->topJS[] = $tablePrinter->showJavaScriptArray();

//if($tablePrinter->openwindows) $phpbms->bottomJS[] = $tablePrinter->openwindows;

include("header.php");
?>

<div class="bodyline" id="mainbody">
	<h1><?php echo $pageTitle ?><a name="top"></a></h1>
    <button id="sync" class="Buttons"><span>sync</span></button>
    <span id="resultPic"></span>
    <textarea rows="10" cols="80" class="results" id="resultText" name="results"></textarea>
</div>
<?php include("footer.php")?>