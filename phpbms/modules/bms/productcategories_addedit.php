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

    include("../../include/session.php");
    include("include/tables.php");
    include("include/fields.php");
    include("include/productcategories.php");

    $thetable = new productcategories($db, 7);
    $therecord = $thetable->processAddEditPage();

    if(isset($therecord["phpbmsStatus"]))
        $statusmessage = $therecord["phpbmsStatus"];

    $pageTitle="Product Category";

    $phpbms->cssIncludes[] = "pages/productcategories.css";

        //Form Elements
        //==============================================================
        $theform = new phpbmsForm();

        $theinput = new inputCheckbox("inactive",$therecord["inactive"]);
        $theform->addField($theinput);

        $theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,128);
        $theform->addField($theinput);

        $theinput = new inputField("displayorder",$therecord["displayorder"],"display order",true,NULL,10,10);
        $theform->addField($theinput);

        $theinput = new inputField("webdisplayname",$therecord["webdisplayname"],"web display name");
        $theform->addField($theinput);

        $theinput = new inputTextarea("description", $therecord["description"]);
        $theform->addField($theinput);

        $theinput = new inputCheckbox("webenabled",$therecord["webenabled"],"web enabled");
        $theform->addField($theinput);

        $thetable->getCustomFieldInfo();
        $theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
        $theform->jsMerge();
        //==============================================================
        //End Form Elements

    include("header.php");

?><div class="bodyline">
    <?php $theform->startForm($pageTitle);?>


    <fieldset id="fsAttributes">
            <legend>attributes</legend>

            <p><?php $theform->showField("inactive")?></p>

            <p><?php $thetable->showParentsSelect($therecord["id"], $therecord["parentid"]); ?></p>

            <p><?php $theform->showField("displayorder")?></p>

    </fieldset>

    <div id="leftDiv">

        <fieldset>
            <legend>name</legend>

            <p class="big"><?php $theform->showField("name")?></p>

        </fieldset>

        <fieldset>
            <legend>web</legend>

            <p><?php $theform->showField("webenabled")?></p>

            <p><?php $theform->showField("webdisplayname")?></p>

            <p><?php $theform->showField("description")?>

        </fieldset>

        <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

    </div>

    <?php
            $theform->showGeneralInfo($phpbms,$therecord);
            $theform->endForm();
    ?>
</div>
<?php include("footer.php");?>
