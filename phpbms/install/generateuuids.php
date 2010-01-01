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
    var $receiptList;
    var $invoiceList;


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

        //generate base uuids for tables
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


        //generate lists used elsewhere
        $this->moduleList = $this->generateUUIDList("modules");

        $bmsModulePresent = false;
        if(in_array("mod:0aa9cca0-7388-0eae-81b9-9935f9d127cc", $this->moduleList))
            $bmsModulePresent = true;

        //BMS updates
        if($bmsModulePresent){

            $this->createUUIDs("tbld:c9ff2c8c-ce1f-659a-9c55-31bca7cce70e"); //tax
            $this->createUUIDs("tbld:d6e4e1fb-4bfa-cb53-ab9c-1b3e7f907ae2"); //invoicestatuses
            $this->createUUIDs("tbld:380d4efa-a825-f377-6fa1-a030b8c58ffe"); //payment methods
            $this->createUUIDs("tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083"); //clients
            $this->createUUIDs("tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883"); //invoices
            $this->createUUIDs("tbld:455b8839-162b-3fcb-64b6-eeb946f873e1"); //discounts
            $this->createUUIDs("tbld:fa8a0ddc-87d3-a9e9-60b0-1bab374b2993"); //shipping methods
            $this->createUUIDs("tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34"); //products
            $this->createUUIDs("tbld:3342a3d4-c6a2-3a38-6576-419299859561"); //product categories
            $this->createUUIDs("tbld:27b99bda-7bec-b152-8397-a3b09c74cb23"); //addresses
            $this->createUUIDs("tbld:c595dbe7-6c77-1e02-5e81-c2e215736e9c"); //aritems
            $this->createUUIDs("tbld:43678406-be25-909b-c715-7e2afc7db601"); //receipts
            $this->createUUIDs("tbld:157b7707-5503-4161-4dcf-6811f8b0322f"); //client email projects

            $this->aritemList = $this->generateUUIDList("aritems");
            $this->receiptList = $this->generateUUIDList("receipts");
            $this->productsList = $this->generateUUIDList("products");
            $this->addressList = $this->generateUUIDList("addresses");
            $this->productcatList = $this->generateUUIDList("productcategories");
            $this->clientList = $this->generateUUIDList("clients");
            $this->statusList = $this->generateUUIDList("invoicestatuses");
            $this->discountList = $this->generateUUIDList("discounts");
            $this->discountList[0] = "";
            $this->taxList = $this->generateUUIDList("tax");
            $this->taxList[0] = "";
            $this->shippingList = $this->generateUUIDList("shippingmethods");
            $this->shippingList[0] = "";
            $this->paymentList = $this->generateUUIDList("paymentmethods");
            $this->paymentList[0] = "";
            $this->invoiceList = $this->generateUUIDList("invoices");
            $this->invoiceStatusList = $this->generateUUIDList("invoicestatuses");

        }//endif

        $this->tabledefList = $this->generateUUIDList("tabledefs");

        $this->userList = $this->generateUUIDList("users");
        $this->userList[0] ="";

        $this->roleList = $this->generateUUIDList("roles");
        $this->roleList[-100] = "Admin";
        $this->roleList[0] = "";

        $this->tabledefList = $this->generateUUIDList("tabledefs");
        $this->moduleList= $this->generateUUIDList("modules");
        $this->fileList= $this->generateUUIDList("files");

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
        $this->updateFields("attachments", array("fileid"=>$this->fileList));
        $this->updateFields("menu", array("parentid"=>$menuList, "roleid"=>$this->roleList));
        $this->updateFields("smartsearches", array("tabledefid"=>$this->tabledefList, "moduleid"=>$this->moduleList));
        $this->updateFields("tabs", array("roleid"=>$this->roleList));
        $this->updateFields("notes", array("assignedtoid"=>$this->userList, "assignedbyid"=>$this->userList, "attachedtabledefid", "parentid"=>$notesList));
        $this->updateFields("log", array("userid"=>$this->userList));

        //custom stuff
        $this->updateMenuLinks();

        //BMS updates
        if($bmsModulePresent){

            $this->updateFields("products", array("categoryid"=>$this->productcatList));

            $invoiceArray = array(
                                  "clientid"        =>$this->clientList,
                                  "statusid"        =>$this->statusList,
                                  "assignedtoid"    =>$this->userList,
                                  "discountid"      =>$this->discountList,
                                  "taxareaid"       =>$this->taxList,
                                  "shippingmethodid"=>$this->shippingList,
                                  "paymentmethodid" =>$this->paymentList
                                  );
            $this->updateFields("invoices", $invoiceArray);
            
            $clientArray = array(
                                "taxareaid"         =>$this->taxList,
                                "shippingmethodid"  =>$this->shippingList,
                                "paymentmethodid"   =>$this->paymentList,
                                "discountid"        =>$this->discountList,
                                "salesmanagerid"    =>$this->userList
                                );
            $this->updateFields("clients", $clientArray);

            $this->updateFields("lineitems", array("productid"=>$this->productsList));
            $this->updateFields("invoicestatuses", array("defaultassignedtoid"=>$this->userList));
            $this->updateFields("invoicestatushistory", array("invoiceid"=>$this->invoiceList, "invoicestatusid"=>$this->invoiceStatsList, "assignedtoid"=>$this->userList));
            $this->updateFields("addresstorecord", array("tabledefid"=>$this->tabledefList, "recordid"=>$this->clientList, "addressid"=>$this->addressList));
            $this->updateFields("receiptitems", array("receiptid"=>$this->receiptList, "aritemid"=>$this->aritemList));
            $this->updateFields("receipts", array("clientid"=>$this->clientList, "paymentmethodid"=>$this->paymentList));
            $this->updateFields("aritems", array("clientid"=>$this->clientList));
            $this->updateFields("prerequisites", array("parentid"=>$this->productList, "childid"=>$this->productList));
            $this->updateFields("clientemailprojects", array("userid"=>$this->userList));

            //we would have to run this if their were addresses associated with other records
            //$this->updateVariableUUIDs("addresstorecord", "tabledefid", "recordid");

            $this->updateAritems(); // need receipt list to work
            $this->updateBMSSettings();

        }//endif


        //Updates that need to be done after all the other updates
        // Notes attachedtoid
        // attachments
        // addresstorecord
        // AR??
        //
        // ======
        // This stuff probably won't be needed as they will be done during the update
        $this->updateVariableUUIDs("notes", "attachedtabledefid", "attachedid");
        $this->updateVariableUUIDs("attachments", "tabledefid", "recordid");


        //Finally add unique indexes to all uuid fields
        $this->addUUIDIndexes();

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
        
        $initialClause = "";
        
        $tablestatement = "
            SHOW FIELDS FROM
                `".$table."`
            WHERE
                `field` = 'stamp'
            OR
                `field` = 'modifieddate'
        ";
        
        $tableresult = $this->db->query($tablestatement);


        while($tablerecord = $this->db->fetchArray($tableresult))
            switch($tablerecord["Field"]){
                
                case "modifieddate":
                case "stamp":
                    $initialClause .= ", `".$tablerecord["Field"]."` = `".$tablerecord["Field"]."`";
                    break;
                
                default:
                    break;
            }//end switch
            
        while($therecord = $this->db->fetchArray($queryresult)){


            $updateClause = $initialClause;

            foreach($fields as $key=>$value)
                if(strpos($therecord[$key],":") === false)
                    $updateClause .= ", `".$key."` = '".$value[$therecord[$key]]."'";
            
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
                
                $this->db->query($updatestatement);

            }//endif

        }//endwhile

    }//end function updateField


    /**
      *  function creatUUIDs
      *  
      *  Gives randomly created uuids to a table with $tabledefuuid (keeping
      *  the prefix in the tabledef consistent).
      *  
      *  @param string $tabledefuuid
      */
    
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

            if(preg_match("/search.php\?id\=\d+/", $therecord["link"])){

                $id = substr($therecord["link"], 14);

                $updatestatement = "
                    UPDATE
                        `menu`
                    SET
                        `link` = 'search.php?id=".urlencode($this->tabledefList[$id])."'
                    WHERE
                        `id` = ".$therecord["id"];

                $this->db->query($updatestatement);

            }//end if

        }//endwhile


    }//end function updateMenuLinks


    /**
     * function updateAritems
     */

    function updateAritems() {

        $querystatment = "
            SELECT
                `type`,
                `relatedid`
            FROM
                `aritems`
            GROUP BY
                `type`,
                `relatedid`
        ";

        $queryresult = $this->db->query($querystatment);

        while($therecord = $this->db->fetchArray($queryresult)){

            switch($therecord["type"]){

                case "credit":
                    $oldRelatedid = $therecord["relatedid"];
                    $updatestatement = "
                        UPDATE
                            `aritems`
                        SET
                            `relatedid`='".$this->receiptList[$oldRelatedid]."'
                        WHERE
                            `relatedid` = '".$oldRelatedid."'
                            AND
                            `type`='credit'
                    ";

                    $this->db->query($updatestatement);
                    break;

                default:
                    $oldRelatedid = $therecord["relatedid"];

                    $updatestatement = "
                        UPDATE
                            `aritems`
                        SET
                            `relatedid`='".$this->invoiceList[$oldRelatedid]."'
                        WHERE
                            `relatedid` = '".$oldRelatedid."'
                            AND
                            `type`!='credit'
                    ";

                    $this->db->query($updatestatement);
                    break;

            }//end switch

        }//end if

    }//end method

    /**
     * Updates specific settings that reference ids
     *
     */
    function updateBMSSettings(){

        $querystatement = "
            SELECT
                *
            FROM
                `setings`
            WHERE
                `name` = 'default_payment'
                OR `name` = 'default_shipping'
                OR `name` = 'default_discount'
                OR `name` = 'default_taxarea'";

        $queryresult = $this->db->query($querystatement);

        while($therecord = $this->db->fetchArray($queryresult)){

            $updatestatement = "
                UPDATE
                    `settings`
                SET
                    `value` = '";
            if($therecord["value"] != 0)
                switch($therecord["name"]){

                    case "default_payment":
                        $updatestatement.=$this->paymentList[$therecord["value"]];
                        break;

                    case "default_shipping":
                        $updatestatement.=$this->shippingList[$therecord["value"]];
                        break;

                    case "default_discount":
                        $updatestatement.=$this->discountList[$therecord["value"]];
                        break;

                    case "default_taxarea":
                        $updatestatement.=$this->taxList[$therecord["value"]];
                        break;

                }//endswitch

            $updatestatement .= "'
                WHERE
                    `name` ='".$therecord["name"]."'";

            $this->db->query($updatestatement);

        }//endwhile

    }//end function updateBMSSettings

    /**
     * Updates records that have variable uuid record fields (such as notes attached to and addresses)
     *
     * @param string $tablename name of the table to update
     * @param string $tabledeffield name of the field that holds the tabledef uuid reference
     * @param string $recordfield name of the field that holds the record uuid
     */
    function updateVariableUUIDs($tablename, $tabledeffield, $recordfield){

        $querystatement = "
            SELECT
                `".$tablename."`.`id`,
                tabledefs.maintable,
                `".$tablename."`.`".$tabledeffield."`,
                `".$tablename."`.`".$recordfield."`
            FROM
                `".$tablename."` INNER JOIN tabledefs ON `".$tablename."`.`".$tabledeffield."` = tabledefs.uuid
            ORDER BY
                `".$tablename."`.`".$tabledeffield."`";

        $queryresult = $this->db->query($querystatement);

        $currentTabledefID = NULL;

        while($therecord = $this->db->fetchArray($queryresult)){

            if($currentTabledefID !== $therecord[$tabledeffield]){

                switch($therecord[$tabledeffield]){

                    case "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083": //clients:
                        $theList = $this->clientList;
                        break;

                    case "tbld:7a9e87ed-d165-c4a4-d9b9-0a4adc3c5a34": //products:
                        $theList = $this->productsList;
                        break;

                    case "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883": //invoices:
                        $theList = $this->invoiceList;
                        break;

                    //no list exists, so let's generate it
                    default:
                        $thelist = $this->generateUUIDList($therecord["maintable"]);

                }//endswitch

                $currentTabledefID = $therecord[$tabledeffield];

            }//endif

            $updatestatement = "
                UPDATE
                    `".$tablename."`
                SET
                    `".$recordfield."` = '".$theList[$therecord[$recordfield]]."'
                WHERE
                    `id` = ".$therecord["id"];

        }//endwhile

    }//end function updateVariableUUIDs


    function addUUIDIndexes(){

        $querystatement = "SHOW TABLES";

        $queryresult = $this->db->query($querystatement);

        while($therecord = $this->db->fetchArray($queryresult)){

            $tablename = array_pop($therecord);

            $querystatement = "DESCRIBE `".$tablename."`";

            $columnsresult = $this->db->query($querystatement);

            $hasUUID = false;

            while($columnrecord = $this->db->fetchArray($columnsresult)){

                if($columnrecord["Field"] == "uuid"){

                    $hasUUID = true;
                    break;

                }//endif

            }//endwhile

            if($hasUUID){

                $alterstatement = "ALTER TABLE `".$tablename."` ADD UNIQUE (`uuid`)";

                $this->db->query($alterstatement);

            }//endif

        }//endwhile

    }// end function addUUIDIndexes


}//end class updateAjax


// START PROCESSING
//==============================================================================

if(!isset($noProcess)){
    $genUUIDS = new generateUUIDS();
    echo $genUUIDS->process();
}//end if
