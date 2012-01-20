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
    include("include/users.php");

    $thetable = new users($db, "tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb");
    $therecord = $thetable->processAddEditPage();

    if(isset($therecord["phpbmsStatus"]))
        $statusmessage = $therecord["phpbmsStatus"];

    $pageTitle="User";

    $phpbms->cssIncludes[] = "pages/users.css";
    $phpbms->jsIncludes[] = "modules/base/javascript/users.js";

        //Form Elements
        //==============================================================
        $theform = new phpbmsForm();
        $theform->onsubmit="return submitForm(this);";

        $disabled = false;
        if($therecord["portalaccess"])
            $disabled = true;

        $theinput = new inputCheckbox("adminBox",$therecord["admin"],"administrator", $disabled);
        $theform->addField($theinput);

        $theinput = new inputCheckbox("revoked",$therecord["revoked"],"access revoked");
        $theform->addField($theinput);

        $theinput = new inputCheckbox("portalaccess",$therecord["portalaccess"],"portal access");
        $theform->addField($theinput);

        $theinput = new inputField("firstname",$therecord["firstname"],"first name",true,NULL,32,64);
        $theinput->setAttribute("class","important");
        $theform->addField($theinput);

        $theinput = new inputField("lastname",$therecord["lastname"],"last name",false,NULL,32,64);
        $theinput->setAttribute("class","important");
        $theform->addField($theinput);

        $theinput = new inputField("login",$therecord["login"],"log in name",true,NULL,32,64);
        $theinput->setAttribute("class","important");
        $theform->addField($theinput);

        $theinput = new inputField("phone",$therecord["phone"],"phone/extension",false,"phone",32,64);
        $theform->addField($theinput);
        
        $theinput = new inputField("email",$therecord["email"],"e-mail address",false,"email",32,64);
        $theinput->setAttribute("title","Enter the email address to send site mail from. Will also be used as part of your contact details.");
        $theform->addField($theinput);
        
        if(!$therecord["id"]) $therecord["sendmail"] = "/usr/sbin/sendmail -bs"; // Set default
        $theinput = new inputField("sendmail",$therecord["sendmail"],"sendmail path",false,null,32,255);
        $theinput->setAttribute("title","Enter the path to the sendmail program directory on the host server. Defaults to: '/usr/sbin/sendmail -bs'");
        $theform->addField($theinput);

        if(!$therecord["id"]) $therecord["smtphost"] = "localhost"; // Set default
        $theinput = new inputField("smtphost",$therecord["smtphost"],"SMTP host",false,null,32,255);
        $theinput->setAttribute("title","Enter the name of the SMTP host. Defaults to: 'localhost'");
        $theform->addField($theinput);

        if(!$therecord["id"]) $therecord["smtpport"] = 25; // Set default
        $theinput = new inputField("smtpport",$therecord["smtpport"],"SMTP port",false,"integer",10,10);
        $theinput->setAttribute("title","Enter the port number of your SMTP server. Use 25 for most unsecure servers and 465 for most secure servers. Defaults to: 25");
        $theform->addField($theinput);
        
        $theinput = new inputField("smtpuser",$therecord["smtpuser"],"SMTP username",false,NULL,32,255);
        $theinput->setAttribute("title","Enter the username for access to the SMTP host.");
        $theform->addField($theinput);

        $theinput = new inputField("smtppass",$therecord["smtppass"],"SMTP password",false,"password",32,255);
        $theinput->setAttribute("title","Enter the password for the SMTP host.");
        $theform->addField($theinput);

        $theinput = new inputField("lastip", $therecord["lastip"], "last log in IP");
        $theinput->setAttribute("readonly", "readonly");
        $theinput->setAttribute("class", "uneditable");
        $theform->addField($theinput);

        $theinput = new inputChoiceList($db,"department",$therecord["department"],"department");
        $theform->addField($theinput);

        $thetable->getCustomFieldInfo();
        $theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
        $theform->jsMerge();
        //==============================================================
        //End Form Elements

    include("header.php");

?><div class="bodyline">
    <?php $theform->startForm($pageTitle)?>
    <fieldset id="fsAttributes">
        <legend>attributes</legend>

        <p>
            <input type="hidden" id="admin" name="admin" value="<?php echo $therecord["admin"]?>" />
            <?php $theform->showField("adminBox");?>
        </p>

        <p><?php $theform->showField("revoked");?></p>

        <p><?php $theform->showField("portalaccess");?></p>

        <p class="notes">
            user accounts marked as portal access cannot login to phpBMS, but are used by external applications
            when creating/modifying information from outside the application for recording purposes.
        </p>
    </fieldset>

    <div id="leftSideDiv">
        <fieldset id="fsName">
            <legend>name</legend>

            <p id="firstnameP" class="big"><?php $theform->showField("firstname");?></p>

            <p class="big"><?php $theform->showField("lastname");?></p>

        </fieldset>

        <fieldset>
            <legend>log in</legend>

            <p class="big"><?php $theform->showField("login");?></p>

            <p>
                <label for="lastlogin" >last log in</label><br />
                <input id="lastlogin" name="lastlogin" type="text" value="<?php echo formatFromSQLDateTime($therecord["lastlogin"]); ?>" size="32" maxlength="64" readonly="readonly" class="uneditable"  />
            </p>

            <p><?php $theform->showField("lastip"); ?></p>

            <p>
                <label for="password">set new password</label><br />
                <input id="password" name="password" type="password" size="32" maxlength="32" />
            </p>

            <p>
                <label for="password2">confirm new password</label><br />
                <input id="password2" name="password2" type="password" size="32" maxlength="32" />
            </p>
        </fieldset>

        <fieldset>
            <legend>contact / user information</legend>

            <p><?php $theform->showField("email");?></p>

            <p><?php $theform->showField("phone");?></p>

            <p><?php $theform->showField("department");?></p>

            <p>
                <label for="employeenumber">employee number</label><br />
                <input type="text" id="employeenumber" name="employeenumber" value="<?php echo htmlQuotes($therecord["employeenumber"]) ?>" size="32" maxlength="32" />
            </p>
        </fieldset>
        
        <fieldset>
            <legend>E-Mail Settings</legend>
            
                <p><?php $theform->showField("email")?></p>

                <p><label for="mailer">select the mailer to use</label><br />
                <select id="mailer" name="mailer" style="width: 80px;" title="Select which mailer for the delivery of site email.  Defaults to: 'PHP Mail'">
                    <option value="mail" <?php if($therecord["mailer"] == "mail")  echo "selected=\"selected\"";?> >PHP Mail</option>
                    <option value="sendmail" <?php if($therecord["mailer"] == "sendmail")  echo "selected=\"selected\"";?> >Sendmail</option>
                    <option value="smtp" <?php if($therecord["mailer"] == "smtp")  echo "selected=\"selected\"";?> >SMTP</option>
                </select></p>

                <fieldset style="width: 250px;">
                    <legend>Sendmail Settings</legend>
                        <p class="notes"><strong>Note:</strong> Only used if mailer is set to use Sendmail</p>
                    
                        <p><?php $theform->showField("sendmail")?></p>
                </fieldset>
                
                <fieldset style="width: 250px;">
                    <legend>SMTP Settings</legend>
                        <p class="notes"><strong>Note:</strong> Only used if mailer is set to use SMTP</p>
        
                        <p><?php $theform->showField("smtphost")?></p>

                        <p><?php $theform->showField("smtpport")?></p>

                        <p><label for="smtpauth" title="Select Yes if your SMTP Host requires SMTP Authentication.  Defaults to: 'No'">SMTP authentication</label><br />
                        <input type="radio" id="smtpauth0" name="smtpauth" value="1" <?php if($therecord["smtpauth"] == 1)  echo "checked=\"checked\"";?> /><label for="smtpauth0">Yes</label>
                        <input type="radio" id="smtpauth1" name="smtpauth" value="0" <?php if($therecord["smtpauth"] == 0)  echo "checked=\"checked\"";?> /><label for="smtpauth1">No</label></p>

                        <p><?php $theform->showField("smtpuser")?></p>

                        <p><?php $theform->showField("smtppass")?></p>

                        <p><label for="smtpsecure" >SMTP security</label><br />
                        <select id="smtpsecure" name="smtpsecure" style="width: 80px;" title="Select the security model that your SMTP server uses.  Defaults to: 'None'">
                            <option value="none" <?php if($therecord["smtpsecure"] == "none")  echo "selected=\"selected\"";?> >None</option>
                            <option value="ssl" <?php if($therecord["smtpsecure"] == "ssl")  echo "selected=\"selected\"";?> >SSL</option>
                            <option value="tls" <?php if($therecord["smtpsecure"] == "tls")  echo "selected=\"selected\"";?> >TLS</option>
                        </select></p>

                </fieldset>
        </fieldset>

        <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

        <?php if($therecord["id"]){?>
        <fieldset>
            <legend>roles</legend>
            <input type="hidden" name="roleschanged" id="roleschanged" value="0" />
            <input type="hidden" name="newroles" id="newroles" value="" />
            <div class="fauxP">
            <div id="assignedrolesdiv">
                assigned roles<br />
                <select id="assignedroles" size="10" multiple="multiple">
                    <?php $thetable->displayRoles($therecord["uuid"], "assigned")?>
                </select>
            </div>
            <div id="rolebuttonsdiv">
                <p>
                    <button type="button" class="Buttons" onclick="moveRole('availableroles','assignedroles')">&lt; add role</button>
                </p>
                <p>
                    <button type="button" class="Buttons" onclick="moveRole('assignedroles','availableroles')">remove role &gt;</button>
                </p>
            </div>
            <div id="availablerolesdiv">
                available roles<br />
                <select id="availableroles" size="10" multiple="multiple">
                    <?php $thetable->displayRoles($therecord["uuid"],"available")?>
                </select>
            </div>
            </div>
        </fieldset>
        <?php }?>
    </div>

    <?php
        $theform->showGeneralInfo($phpbms,$therecord);
        $theform->endForm();
    ?>
</div>
<?php include("footer.php");?>
