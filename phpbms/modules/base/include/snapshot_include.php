<?php

class snapshot{

    var $db;
    var $preferences;
    var $widgets = array();

    function snapshot($db){

        $this->db = $db;

        if(!isset($_SESSION["userinfo"]["prefs"]["snapshot"]))
            $this->setPreferenceDefaults();
        else
            $this->preferences = json_decode($_SESSION["userinfo"]["prefs"]["snapshot"]);

    }//end function init


    function setPreferenceDefaults(){
        // If the user does not have any snapshot preferences loaded from
        // login, we should get the defaults from the widgets table and
        // construct/save the preferences.


        //build roles modifier.  For admins, they can see everything
        // but for everyone else we need to make an IN statement
        $rolemodifier = "";

        if(!$_SESSION["userinfo"]["admin"]){

            $rolemodifier = "''";
            foreach($_SESSION["userinfo"]["roles"] as $role)
                $rolemodifier .= ", '".$role."'";

            $rolemodifier = "AND `roleid` IN(".$rolemodifier.") OR roleid IS NULL";

        }//endif

        $querystatement = "
            SELECT
                `uuid`,
                `type`
            FROM
                `widgets`
            WHERE
                `default` = 1
                ".$rolemodifier."
            ORDER BY
                `type`,
                `moduleid`";

        //grab the defaults from the widgets table
        $queryresult = $this->db->query($querystatement);

        $big = array();
        $little = array();

        //construct the object that will be converted to json
        $preference = new stdClass();
        $preference->bigArea = array();
        $preference->littleArea = array();
        $preference->orientation = "littleRight";

        // add the default widget's uuid to the appropriate
        // area array
        while($therecord = $this->db->fetchArray($queryresult))
            if($therecord["type"] == "little")
                $preference->littleArea[] = $therecord["uuid"];
            else
                $preference->bigArea[] = $therecord["uuid"];

        $preference->littleArea = array_reverse($preference->littleArea);
        $preference->bigArea = array_reverse($preference->bigArea);

        $this->preferences = $preference;

        $json = json_encode($preference);

        //set the session's json
        $_SESSION["userinfo"]["prefs"]["snapshot"] = $json;

        //insert the preference record
        $insertstatement = "
            INSERT INTO
                `userpreferences`
                (`userid`, `name`, `value`)
            VALUES (
                ".$_SESSION["userinfo"]["id"].",
                'snapshot',
                '".mysql_real_escape_string($json)."'
            )";

        $this->db->query($insertstatement);

    }//end function setPreferenceDefaults


    function updatePreferences(){
        // takes the objects preferences, encodes them in JSON and then saves
        // them both in the current session and the userpreferences table for
        // that user

        $json = json_encode($this->preferences);

        $_SESSION["userinfo"]["prefs"]["snapshot"] = $json;

        $updatestatement = "
            UPDATE
                `userpreferences`
            SET
                `value` = '".mysql_real_escape_string($json)."'
            WHERE
                `name` = 'snapshot'
                AND `userid` = ".$_SESSION["userinfo"]["id"];

        $this->db->query($updatestatement);

    }//end function updatePreferences


    function getWidgets(){
        // This function loads each of the widgets into the objects widgets array
        // by first looking up the widget in the database, and then loading the
        // corrsponding widget file and implementing the class that corresponds
        // to the uuid of that widget

        if(count($this->preferences->bigArea) || count($this->preferences->littleArea)){

            if(count($this->preferences->bigArea))
                $bigArea = "IN('".implode($this->preferences->bigArea, "','")."')";
            else
                $bigArea = "= 'N/A'";

            if(count($this->preferences->littleArea))
                $littleArea = "IN('".implode($this->preferences->littleArea, "','")."')";
            else
                $littleArea = "= 'N/A'";

            $querystatement = "
                SELECT
                    `uuid`,
                    `file`
                FROM
                    `widgets`
                WHERE
                    `uuid` ".$bigArea."
                    OR `uuid` ".$littleArea;

            $queryresult = $this->db->query($querystatement);

            while($therecord = $this->db->fetchArray($queryresult)){

                //load the widgets class definition file
                if(! @ include($therecord["file"]))
                    $error = new appError(-800,"Could not include widget file: ".$therecord["file"], "Widget load failed.");

                // class name of widget should be the uuid (minus the : and - chars)
                // so let's check for it ant then instanciate it in the widgets array
                $className = str_replace("-", "", str_replace(":", "", $therecord["uuid"]));
                if(class_exists($className))
                    $this->widgets[$therecord["uuid"]] = new $className($this->db);

            }//endwhile fetch record

        }//endif big/littleareas

    }//end function getWidgets


    function merge($existingArray, $type = "jsIncludes"){
        // This function merges any js or CSS files form any widgets with
        // and then returns the new array

        foreach($this->widgets as $widget)
            $existingArray = array_merge($existingArray, $widget->$type);

        return $existingArray;

    }//end function merge


    function process($variables){

        $variables = addSlashesToArray($variables);

        switch($variables["cmd"]){

            case "remove":

                $pos = array_search($variables["uuid"], $this->preferences->bigArea);
                if($pos !== false)
                    array_splice($this->preferences->bigArea, $pos, 1);
                else {
                    $pos = array_search($variables["uuid"], $this->preferences->littleArea);
                    if($pos !== false)
                        array_splice($this->preferences->littleArea, $pos, 1);
                }//endif

                $this->updatePreferences();

                break;

            case "add":

                if($variables["afterwidget"] == "first")
                    $this->preferences->$variables["area"] = array_merge(array($variables["widget"]), $this->preferences->$variables["area"]);
                else {

                    $newArray = array();
                    while(count($this->preferences->$variables["area"])){

                        $item = array_pop($this->preferences->$variables["area"]);

                        if($item == $variables["afterwidget"])
                            $newArray[] = $variables["widget"];

                        $newArray[] = $item;

                    }//endwhile

                    $this->preferences->$variables["area"] = array_reverse($newArray);

                }//end if

                $this->updatePreferences();
                break;

        }//endswitch

    }//end function process


    function displayWidgets(){
        //Creates the two column, and places the widgets

        ?>
        <div id="widgetContainer" class="<?php echo $this->preferences->orientation ?>">

            <div id="bigArea" class="<?php echo $this->preferences->orientation ?>">
                <?php

                    $this->displayConfigureSection("bigArea");

                    foreach($this->preferences->bigArea as $uuid)
                        $this->widgets[$uuid]->display();

                ?>
            </div>

            <div id="littleArea" class="<?php echo $this->preferences->orientation ?>">
                <?php

                    $this->displayConfigureSection("littleArea");

                    foreach($this->preferences->littleArea as $uuid)
                        $this->widgets[$uuid]->display();

                ?>
            </div>

            <div id= "clearer">&nbsp;</div>
        </div>
        <?php
    }//end function display


    function displayConfigureSection($area){

        if(count($this->preferences->$area))
            $areaWhere = "NOT IN('".implode($this->preferences->$area, "','")."')";
        else
            $areaWhere = "!= 'N/A'";

        if(!$_SESSION["userinfo"]["admin"])
            $rolewhere = "AND (`roleid` IN ('".implode($_SESSION["userinfo"]["roles"])."') OR `roleid` ='' OR `roleid` IS NULL)";
        else
            $rolewhere = "";

        $querystatement = "
            SELECT
                `widgets`.`uuid`,
                `widgets`.`title`,
                `modules`.`name` AS module
            FROM
                `widgets` INNER JOIN `modules` ON `widgets`.`moduleid` = `modules`.`uuid`
            WHERE
                `widgets`.`type` = '".str_replace("Area", "", $area)."'
                AND `widgets`.`uuid` ".$areaWhere."
                ".$rolewhere."
            ORDER BY
                `widgets`.`moduleid`,
                `widgets`.`title`";

        $widgetresult = $this->db->query($querystatement);

        ?>
            <div class="configure">
                <button id="<?php echo $area ?>Configure" class="graphicButtons buttonPlus configureButtons" type="button">add widget...</button>
                <div id="<?php echo $area ?>ConfigureDropdown">
                    <form action="snapshot.php" onsubmit="return false;" method="post">
                        <input type="hidden" name="cmd"  value="add" />
                        <input type="hidden" name="area" value="<?php echo $area?>" />
                        <p>
                            <label for="<?php echo $area?>AddWidget">widget</label><br />
                            <select name="widget" id="<?php echo $area?>AddWidget">
                                <?php

                                    if(!$this->db->numRows($widgetresult)){

                                        ?><option value="">no widgets available</option><?php

                                    } else {

                                        $module = "";

                                        while($therecord = $this->db->fetchArray($widgetresult)){

                                            if($module == ""){

                                                $module = $therecord["module"];
                                                ?><optgroup label="<?php echo formatVariable($module) ?>"><?php

                                            }//endif

                                            if($module != $therecord["module"]){

                                                $module = $therecord["module"];

                                                ?></optgroup>
                                                <optgroup label="<?php echo formatVariable($module) ?>"><?php

                                            }//endif

                                            ?><option value="<?php echo formatVariable($therecord["uuid"])?>">
                                                <?php echo formatVariable($therecord["title"])?>
                                            </option><?php

                                        }//endwhile

                                        ?></optgroup><?php

                                    }//endif widgets available

                                ?>
                            </select>
                        </p>
                        <p>
                            <label for="<?php echo $area?>AfterWidget">after</label><br />
                            <select name="afterwidget" id="<?php echo $area?>AfterWidget">
                                <?php
                                    $count = 0;

                                    foreach($this->widgets as $widget){

                                            if($widget->type == str_replace("Area", "", $area)){

                                                ?><option value="<?php echo $widget->uuid ?>"><?php echo formatVariable($widget->title) ?></option><?php
                                                $count++;

                                            }//end if

                                    }//end foreach

                                    if(!$count) {
                                        //no widgets beind displayed
                                        ?><option value="first">place first</option><?php

                                    }//endif
                                ?>
                            </select>
                        </p>
                        <p>
                            <button type="button" class="Buttons widgetAddButtons" id="<?php echo $area ?>AddButton">add</button>
                            <button type="button" class="Buttons widgetCancelButtons" id="<?php echo $area ?>CancelButton">cancel</button>
                        </p>
                    </form>
                </div>
            </div>
        <?php

    }//end function display


    function displaySystemMessages(){
        //shows system messages, but only if they exist

        $querystatement = "
            SELECT
                    notes.id,
                    notes.subject,
                    notes.content,
                    concat(users.firstname,' ',users.lastname) AS createdby,
                    notes.creationdate
            FROM
                    notes INNER JOIN users ON notes.createdby=users.id
            WHERE
                    type='SM'
            ORDER BY
                    importance DESC,
                    notes.creationdate";

        $queryresult = $this->db->query($querystatement);

        if($this->db->numRows($queryresult)){ ?>

        <div class="box" id="systemMessageContainer">
            <h2>System Messages</h2>
            <?php while($therecord = $this->db->fetchArray($queryresult)) {

                $therecord["content"] = str_replace("\n","<br />",htmlQuotes($therecord["content"]));

            ?>
            <h3 class="systemMessageLinks"><?php echo htmlQuotes($therecord["subject"])?> <span>[ <?php echo htmlQuotes(formatFromSQLDateTime($therecord["creationdate"]))?> <?php echo htmlQuotes($therecord["createdby"])?>]</span></h3>
            <div class="systemMessages">
                <p><?php echo $therecord["content"]?></p>
            </div>
            <?php }//end while ?>
        </div>
        <?php }//endif

    }//end method showSystemMessages


}//end class snapshot


// Widget Base Class
//==============================================================================

class widget{

    var $db;
    var $uuid;
    var $type = "big";
    var $title = "widget";
    var $jsIncludes = array();
    var $cssIncludes = array();

    function widget($db){

        $this->db = $db;

    }//end function init


    function display(){

        ?>
        <div class="box widgets" id="<?php echo $this->uuid; ?>">
            <div class="widgetOptions" id="<?php echo $this->uuid; ?>Options">
                <button type="button" id="<?php echo $this->uuid; ?>RemoveButton" class="graphicButtons buttonMinus widgetRemoves" title="remove widget"><span>remove widget</span></button>
            </div>
            <h2 class="widgetTitles"><?php echo formatVariable($this->title)?></h2>

            <?php $this->displayMiddle(); ?>

        </div>
        <?php

    }//end function display


    function displayMiddle(){

    }//end function displayMiddle


}//end class widget

?>
