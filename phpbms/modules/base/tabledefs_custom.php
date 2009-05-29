<?php
/*
 $Rev: 498 $ | $LastChangedBy: nate $
 $LastChangedDate: 2009-04-16 13:00:58 -0600 (Thu, 16 Apr 2009) $
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

	include("include/tabledefs_custom.php");

        //Make sure table definition id is set
	if(!isset($_GET["id"]))
            $error = new appError(300,"Passed variable not set (id)");

        $customFields = new customFields($db, ((int) $_GET["id"]));

	$pageTitle="Custom Fields: ".formatVariable($customFields->tableinfo["displayname"]);

        if(isset($_POST["custom1name"]))
            $statusmessage = $customFields->process($_POST);

	$phpbms->cssIncludes[] = "pages/base/tablecustom.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/tablecustom.js";

		//Form Elements
		//==============================================================
                $theform = $customFields->prepFields();

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

	$phpbms->showTabs("tabledefs entry", "tab:2ebf956d-5e39-c7d5-16b7-501b64685a5a", ((int) $_GET["id"]))?><div class="bodyline">
	<h1 id="pageTitle"><span><?php echo $pageTitle?></span></h1>

        <?php if(!$customFields->tableinfo["hascustomfields"]) {?>
            <form>
            <p id="noCustom">Table is not set up with custom fields.</p>
            </form>
        <?php } else { ?>
            <form action="<?php echo str_replace("&", "&amp;", $_SERVER["REQUEST_URI"]) ?>" method="post" name="record" id="record">

                <p id="topSaveP"><button type="button" class="Buttons saveButtons" accesskey="s">save</button></p>

                <?php $customFields->showFields($theform); ?>

                <p id="bottomSaveP"><button type="button" class="Buttons saveButtons">save</button></p>

            </form>
        <?php }//endif has customfields ?>

</div>
<?php include("footer.php")?>
