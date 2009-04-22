<?php
/*
 $Rev: 249 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-07-02 15:50:36 -0600 (Mon, 02 Jul 2007) $
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

class db{

    // We may want to do more than connect via mysql. Currently only MySQL code
    // is provided.  Some functions may offer a swtich on type, but others
    // are currently coded for MySQL only
    var $type="mysql";

    // mysql vars;
    var $db_link;
    var $hostname;
    var $schema;
    var $dbuser;
    var $dbpass;
    var $pconnect = true;

    var $showError = false;
    var $logError = true;
    var $stopOnError = true;
    var $errorFormat = "xhtml";

    var $error = NULL;

    function db($connect = true, $hostname = NULL, $schema = NULL, $user = NULL, $pass = NULL, $pconnect = NULL, $type = "mysql"){

        if($type!="mysql")
        $this->type=$type;

        switch($this->type){

            default:
            case "mysql":

                if(defined("MYSQL_SERVER"))
                    $this->hostname = MYSQL_SERVER;

                if($hostname!=NULL)
                    $this->hostname = $hostname;

                if(defined("MYSQL_DATABASE"))
                    $this->schema = MYSQL_DATABASE;

                if($schema!=NULL)
                    $this->schema = $schema;

                if(defined("MYSQL_USER"))
                    $this->dbuser = MYSQL_USER;

                if($schema!=NULL)
                    $this->dbuser = $user;

                if(defined("MYSQL_USERPASS"))
                    $this->dbpass = MYSQL_USERPASS;

                if($schema!=NULL)
                    $this->dbpass = $pass;

                if(defined("MYSQL_PCONNECT"))
                    $this->pconnect = MYSQL_PCONNECT;

                if($pconnect!=NULL)
                    $this->pconnect = $pconnect;

                break;

        }//end switch

        if($connect){
            if($this->connect()){

                if($this->selectSchema())
                    return $this->db_link;
                else
                    return false;

            } else
                return false;
        } else
            return true;

    }//end function init (db)


    function connect(){
        // This functions connects to the server.
        // It uses pconnect if the constant is set (via the settings.php file)

        if($this->pconnect)
            $this->db_link = @ mysql_pconnect($this->hostname,$this->dbuser,$this->dbpass);
        else
            $this->db_link = @ mysql_connect($this->hostname,$this->dbuser,$this->dbpass);

        if(!$this->db_link){

            $error = new appError(-400,"Could not connect to database server.\n\n".$this->getError(),"",$this->showError,$this->stopOnError,false,$this->errorFormat);
            return false;

        } else
            return $this->db_link;

    }//end function connect


    function selectSchema($schema=NULL){
        //Selects the database (schema) to use

        if($schema!=NULL)
            $this->schema=$schema;

        if(! @ mysql_select_db($this->schema,$this->db_link)){

            $error = new appError(-410,"Could not open schema ".$this->schema,"",$this->showError,$this->stopOnError,false,$this->errorFormat);
            return false;

        } else
            return true;

    }//end function selectSchema


    function query($sqlstatement){
        //issues a SQL query

        switch($this->type){

            case "mysql":
                if(!isset($this->db_link))
                    if(!$this->db())
                        die($this->error);

                $queryresult = @ mysql_query($sqlstatement,$this->db_link);

                if(!$queryresult){

                    $this->error = $this->getError($this->db_link);
                    $error = new appError(-420,$this->getError($this->db_link)."\n\nStatement: ".$sqlstatement,"",$this->showError,$this->stopOnError,$this->logError,$this->errorFormat);
                    return false;

                }//endif

                break;

        }//endswitch type

        //success, clear the error and return the query result pointer
        $this->error = NULL;
        return $queryresult;

    }//end function query


    function setEncoding($encoding = "utf8"){
        //set the database character encoding

        switch($this->type){

            case "mysql":
                @ mysql_query("SET NAMES ".$encoding, $this->db_link);
                break;

        }//endswitch

    }//end function setEncoding


    function getError($link = NULL){
        //retrieve the last error from the database server

        switch($this->type){

            case "mysql":
                if($link)
                    $thereturn = @ mysql_error($link);
                else
                    $thereturn = @ mysql_error();

                break;

        }//end switch --type--

        return $thereturn;

    }//end function getError


    function numRows($queryresult){
        //retrieve the number of rows of an issued query.

        switch($this->type){

            case "mysql":
                $numrows = @ mysql_num_rows($queryresult);

                if(!is_numeric($numrows)){

                    $error= new appError(-430,"","Could Not Retrieve Rows.","",$this->showError,$this->stopOnError,$this->logError,$this->errorFormat);
                    return false;

                }//end if

                break;

        }//end case

        $this->error=NULL;
        return $numrows;

    }//end function numRows


    function fetchArray($queryresult){
        //Fetches associative array of current row from query result

        switch($this->type){

            case "mysql":
                $row = @ mysql_fetch_assoc($queryresult);
                break;

        }//endswitch

            return $row;

    }//end function fetchArray


    function startTransaction(){
        // Start a transaction

        switch($this->type){

            case "mysql":
                $this->query("START TRANSACTION;");
                break;

        }//end switch

    }//end function startTransaction


    function commitTransaction(){
        // commits a started transaction

        switch($this->type){

            case "mysql":
                $this->query("COMMIT;");
                break;

        }//end switch

    }//end function commitTransaction


    function rollbackTransaction(){
        // roll back any changes in a transaction

        switch($this->type){

            case "mysql":
                $this->query("ROLLBACK;");
                break;

        }//end switch

    }//end function rollbackTransaction


    function seek($queryresult,$rownum){
        // moves the internal pointer of the current record
        // on a query result to a specific location

        switch($this->type){

            case "mysql":
                $thereturn=@ mysql_data_seek($queryresult,$rownum);
                break;

        }//endswitch

        return $thereturn;

    }//end function seek


    function numFields($queryresult){
        // return the number of fields a query result has

        switch($this->type){

            case "mysql":
                $thereturn=@ mysql_num_fields($queryresult);
                break;

        }//endswitch

        return $thereturn;

    }//end function numFields


    function fieldTable($queryresult,$offset){
        //return the name of the table that a specified field is in
        // pass a query result and a numerical field offset

        switch($this->type){

            case "mysql":
                $thereturn=@ mysql_field_table($queryresult,$offset);
                break;

        }//endswitch

        return $thereturn;

    }//end function fieldTable


    function fieldName($queryresult,$offset){
        //return the name of the field at a specified offset

        switch($this->type){

            case "mysql":
                $thereturn=@ mysql_field_name($queryresult,$offset);
                break;

        }//end case

        return $thereturn;

    }//end function fieldName


    function tableInfo($tablename){
        // returns a multi-dimensional array describing the fields in a
        // provided table name

        $thereturn = false;

        switch($this->type){

            case "mysql":
                $queryresult = @ mysql_list_fields($this->schema,$tablename);
                if($queryresult){

                    for($offset = 0; $offset < mysql_num_fields($queryresult); ++$offset){

                        $name = $this->fieldName($queryresult,$offset);
                        $thereturn[$name]["type"] = @ mysql_field_type($queryresult,$offset);
                        $thereturn[$name]["length"] = mysql_field_len($queryresult,$offset);
                        $thereturn[$name]["flags"] = mysql_field_flags($queryresult,$offset);

                    }//endfor

                }//endif

            break;

        }//end case

        return $thereturn;

    }//end function tableInfo


    function insertId(){
        //return the id of the last inserted record

        $thereturn = false;

        switch($this->type){

            case "mysql":
                $thereturn = @ mysql_insert_id($this->db_link);
                break;

        }//endswitch

        return $thereturn;

    }//end function insertId


    function affectedRows(){
        // return the number of affected rows, typically from an update
        // or a delete

        $thereturn = false;

        switch($this->type){

            case "mysql":
                $thereturn = @ mysql_affected_rows($this->db_link);
                break;

        }//endswitch

        return $thereturn;

    }//end function affectedRows


    function processSQLFile($fileName){
	// process a standard .sql file.  Should be able to handle
	// comments, and semicolon within quotes.  Returns an object with
	// statistics, and possible errors.
	// Will not work with double quoted variables, only single quoted.

	$filePointer = @ fopen($fileName, "r");

	$return = new stdClass();
	$return->numQueries = 0;
	$return->errors = array();

	$inParents = false;
	$sqlstatement = "";
	$lineNumber = 1;

	$this->showError = false;
	$this->stopOnError = false;

	if(!$filePointer){
	    //could not open file

	    $return->errors[] = "Could Not Open File: '".$fileName."'";
	    return $return;

	}//end if

	while($line = @ fgets($filePointer, 65536)){

	    // need to convert DOS or Mac line breaks
	    $line = ereg_replace("\r\n$", "\n", $line);
	    $line = ereg_replace("\r$", "\n", $line);

	    // ignore comment lines, but only if they are not in quotes
	    if(!$inParents){

		$skipline = false;
		if(trim($line) == "" || strpos($line, "#") === 0 || strpos($line, "--") === 0)
		    $skipline = true;

		if($skipline){

		    $lineNumber++;
		    continue;

		}//endif

	    }//endif

	    // remove double backslashes before we count quotes
	    $deslashedLine = str_replace("\\\\", "", $line);

	    // count single quotes and backslashed sing quotes so we can
	    // determine if any semicolons represent end of line
	    $parents = substr_count($deslashedLine, "'") - substr_count($deslashedLine, "\\'");
	    if ($parents%2!=0)
		$inParents = !$inParents;

	    $sqlstatement .= $line;

	    if (ereg(";$", trim($line)) && !$inParents) {
	    	// run the query.  If there is an error, log it and the
		// line number it started on

		$this->query($sqlstatement);

		if($this->error)
		    $return->errors[] = "Error Processing file '".$fileName."' on line ".$lineNumber.": ".$this->error."\n\n SQL Statement: ".$sqlstatement;

		$return->numQueries++;
		$sqlstatement = "";

	    }//end if

	    $lineNumber++;

	}//endwhile

	@ fclose($filePointer);

	return $return;

    }//end function processSQLFile

}//end db class
?>
