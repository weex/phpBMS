<?php
/*
 $Rev: 427 $ | $LastChangedBy: nate $
 $LastChangedDate: 2008-08-13 12:09:00 -0600 (Wed, 13 Aug 2008) $
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
define("APP_DEBUG",false);
define("noStartup",true);

require("install_include.php");
require("../include/session.php");
require("../include/common_functions.php");


class generateUUIDS extends installUpdateBase{

    var $userList;
    var $roleList;
    var $tabledefList;
    var $moduleList;


    function process(){

        $this->phpbmsSession = new phpbmsSession;

        if($this->phpbmsSession->loadDBSettings(false)){

            @ include_once("include/db.php");

            $this->db = new db(false);
            $this->db->stopOnError = false;
            $this->db->showError = false;
            $this->db->logError = false;

        } else
            return $this->returnJSON(false, "Could not open session.php file");

        if(!$this->db->connect())
            return $this->returnJSON(false, "Could not connect to database ".$this->db->getError());

        if(!$this->db->selectSchema())
            return $this->returnJSON(false, "Could not open database schema '".MYSQL_DATABASE."'");

        //generate uuids for tables
        $this->createUUIDs("tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb"); //Users
        $this->createUUIDs("tbld:8d19c73c-42fb-d829-3681-d20b4dbe43b9"); //Relationships
        $this->createUUIDs("tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3"); //Table Definitions
        $this->createUUIDs("tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1"); //Notes
        $this->createUUIDs("tbld:d595ef42-db9d-2233-1b9b-11dfd0db9cbb"); //Reports
        $this->createUUIDs("tbld:e251524a-2da4-a0c9-8725-d3d0412d8f4a"); //Saved Searches/Sorts
        $this->createUUIDs("tbld:ea159d67-5e89-5b7f-f5a0-c740e147cd73"); //Installed Modules
        $this->createUUIDs("tbld:80b4f38d-b957-bced-c0a0-ed08a0db6475"); //Files
        $this->createUUIDs("tbld:87b9fe06-afe5-d9c6-0fa0-4a0f2ec4ee8a"); //Roles
        $this->createUUIDs("tbld:83de284b-ef79-3567-145c-30ca38b40796"); //Scheduler
        $this->createUUIDs("tbld:7e75af48-6f70-d157-f440-69a8e7f59d38"); //Tabs
        $this->createUUIDs("tbld:29925e0a-c825-0067-8882-db4b57866a96"); //Smart Searches
        $this->createUUIDs("tbld:83187e3d-101e-a8a5-037f-31e9800fed2d"); //Menu
        $this->createUUIDs("tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e"); //tax

        //generate lists used elsewhere
        $this->moduleList= $this->generateUUIDList("modules");

        $this->tabledefList = $this->generateUUIDList("tabledefs");

        //BMS updates
        if(in_array("mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc", $this->moduleList)){

            $this->createUUIDs("tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083"); //clients

        }//endif


        $this->userList = $this->generateUUIDList("users");
        $this->userList[0] ="";

        $this->roleList = $this->generateUUIDList("roles");
        $this->roleList[-100] = "Admin";
        $this->roleList[0] = "";

        $this->tabledefList = $this->generateUUIDList("tabledefs");
        //
        $this->moduleList= $this->generateUUIDList("modules");

        $menuList = $this->generateUUIDList("menu");
        $menuList[0] = "";

        $notesList = $this->generateUUIDList("notes");

        //function calls for all we have to do go here
        //======================================================================
        $this->updateFields("reports", array("roleid"=>$this->roleList, "tabledefid"=>$this->tabledefList));
        $this->updateFields("rolestousers", array("userid"=>$this->userList, "roleid"=>$this->roleList));
        $this->updateFields("tablecolumns", array("tabledefid"=>$this->tabledefList, "roleid"=>$this->roleList));
        $this->updateFields("tablefindoptions", array("tabledefid"=>$this->tabledefList, "roleid"=>$this->roleList));
        $this->updateFields("tablegroupings", array("tabledefid"=>$this->tabledefList, "roleid"=>$this->roleList));
        $this->updateFields("tableoptions", array("tabledefid"=>$this->tabledefList, "roleid"=>$this->roleList));
        $this->updateFields("tabledefs", array("moduleid"=>$this->moduleList, "editroleid"=>$this->roleList, "addroleid"=>$this->roleList, "searchroleid"=>$this->roleList, "advsearchroleid"=>$this->roleList, "viewsqlroleid"=>$this->roleList));
        $this->updateFields("tablesearchablefields", array("tabledefid"=>$this->tabledefList));
        $this->updateFields("widgets", array("roleid"=>$this->roleList, "moduleid"=>$this->moduleList));
        $this->updateFields("usersearches", array("tabledefid"=>$this->tabledefList, "userid"=>$this->userList, "roleid"=>$this->roleList));
        $this->updateFields("relationships", array("fromtableid"=>$this->tabledefList, "totableid"=>$this->tabledefList));

        $this->updateFields("files", array("roleid"=>$this->roleList));
        $this->updateFields("menu", array("parentid"=>$menuList, "roleid"=>$this->roleList));
        $this->updateFields("smartsearches", array("tabledefid"=>$this->tabledefList, "moduleid"=>$this->moduleList));
        $this->updateFields("tabs", array("roleid"=>$this->roleList));
        $this->updateFields("notes", array("assignedtoid"=>$this->userList, "assignedbyid"=>$this->userList, "attachedtabledefid", "parentid"=>$notesList));

        //custom stuff
        $this->updateMenuLinks();

        //BMS updates
        if(in_array("mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc", $this->moduleList)){

        }//endif


        //Updates that need to be done after all the other updates
        // Notes attachedtoid
        // attachments
        // addresstorecord
        // AR??
        //
        // ======
        // This stuff probably won't be needed as they will be done during the update
        //$this->updateFields("widgets", array("moduleid"=>$this->moduleList, "roleid"=>$this->roleList));

        return $this->returnJSON(true, "UUID's Generated");

    }//endfunction process


    function generateUUIDList($table, $whereclause = ""){

        $querystatement = "
            SELECT
                `id`,
                `uuid`
            FROM
                `".$table."`";

        if($whereclause)
            $querystatement .= "
                WHERE ".$whereclause;

        $queryresult = $this->db->query($querystatement);

        $list = array();
        while($therecord = $this->db->fetchArray($queryresult))
            $list[$therecord["id"]] = $therecord["uuid"];

        return $list;

    }//end function generateUUDIList

    /**
      *   function updateFields
      *   @param string $table
      *   @param array $fields An associative array (with fieldnames as keys)
      *   of arrays of ids to uuids (generated by $this->generateUUIDList).
      *   @example $this->updateFields("reports", array("roleid"=>$this->roleList, "tabledefid"=>$this->tabledefList));
      */
    function updateFields($table, $fields){

        $fieldClause ="`id` ";

        foreach($fields as $key=>$value)
            $fieldClause .= ", `".$key."`";

        $querystatement = "
            SELECT
                ".$fieldClause."
            FROM
                `".$table."`";
        $queryresult = $this->db->query($querystatement);

        while($therecord = $this->db->fetchArray($queryresult)){

            $updateClause = "";

            foreach($fields as $key=>$value)
                if(strpos($therecord[$key],":") === false)
                    $updateClause .= ", `$key` = '".$value[$therecord[$key]]."'";

            if($updateClause){

                $updateClause = substr($updateClause, 1);

                $updatestatement = "
                    UPDATE
                        `".$table."`
                    SET
                        $updateClause
                    WHERE
                        `id` = ".$therecord["id"]."
                ";

//echo $updatestatement."<br />";
                $this->db->query($updatestatement);

            }//endif

        }//endwhile

    }//end function updateField


    function createUUIDs($tabledefuuid){

        $querystatement  = "
            SELECT
                prefix,
                maintable
            FROM
                tabledefs
            WHERE
                uuid = '".$tabledefuuid."'";

        $queryresult = $this->db->query($querystatement);

        $therecord = $this->db->fetchArray($queryresult);

        $prefix = $therecord["prefix"].":";
        $tablename = $therecord["maintable"];

        $querystatement = "
            SELECT
                id,
                uuid
            FROM
                `".$tablename."`";

        $queryresult = $this->db->query($querystatement);

        while($therecord = $this->db->fetchArray($queryresult))
            if(strpos($therecord["uuid"], ":") === false){

                $updatestatement = "
                    UPDATE
                        `".$tablename."`
                    SET
                        uuid = '".uuid($prefix)."'
                    WHERE
                        id = ".$therecord["id"];

                $this->db->query($updatestatement);

            }//endif //endwhile

    }//end function createUUIDs


    function updateMenuLinks(){

        $querystatement = "
            SELECT
                `id`,
                `link`
            FROM
                `menu`
            WHERE
                `link` LIKE 'search.php?id=%'";

        $queryresult = $this->db->query($querystatement);

        while($therecord = $this->db->fetchArray($queryresult)){

            $id = substr($therecord["link"], 14);

            $updatestatement = "
                UPDATE
                    `menu`
                SET
                    `link` = 'search.php?id=".urlencode($this->tabledefList[$id])."'
                WHERE
                    `id` = ".$therecord["id"];

            $this->db->query($updatestatement);

        }//endwhile


    }//end function updateMenuLinks


}//end class updateAjax


// START PROCESSING
//==============================================================================

$genUUIDS = new generateUUIDS();

echo $genUUIDS->process();
