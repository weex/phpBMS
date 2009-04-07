<?php
/*
 $Rev: 176 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2006-12-27 18:40:19 -0700 (Wed, 27 Dec 2006) $
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
if(class_exists("phpbmsTable")){
	class schedulers extends phpbmsTable{

		var $availableRoleIDs = array();

		function getDefaults(){
			$therecord = parent::getDefaults();

			$therecord["crontab"]="*::*::*::*::*";

			$therecord["min"]="*";
			$therecord["hrs"]="*";
			$therecord["date"]="*";
			$therecord["mo"]="*";
			$therecord["day"]="*";

			$therecord["startdate"] = dateToString(mktime(),"SQL");
			$therecord["starttime"] = sqlTimeFromString(timeToString(time()));

			$therecord["enddate"]="";
			$therecord["endtime"]="";

			return $therecord;

		}//end method


		function getRecord($id = 0){
			$therecord = parent::getRecord($id);

			$datearray=explode(" ",$therecord["startdatetime"]);
			$therecord["startdate"]=$datearray[0];
			if(isset($datearray[1])) $therecord["starttime"]=$datearray[1]; else $therecord["starttime"]="";

			$datearray=explode(" ",$therecord["enddatetime"]);
			$therecord["enddate"]=$datearray[0];
			if(isset($datearray[1])) $therecord["endtime"]=$datearray[1]; else $therecord["endtime"]="";

			$cronarray=explode("::",$therecord["crontab"]);
			if(isset($cronarray[0])) $therecord["min"]=$cronarray[0]; else $therecord["min"]="*";
			if(isset($cronarray[1])) $therecord["hrs"]=$cronarray[1]; else $therecord["hrs"]="*";
			if(isset($cronarray[2])) $therecord["date"]=$cronarray[2]; else $therecord["date"]="*";
			if(isset($cronarray[3])) $therecord["mo"]=$cronarray[3]; else $therecord["mo"]="*";
			if(isset($cronarray[4])) $therecord["day"]=$cronarray[4]; else $therecord["day"]="*";

			$therecord["lastrun"]=formatFromSQLDatetime($therecord["lastrun"]);

			return $therecord;

		}//end method


		function verifyVariables($variables){

			//must have the file that has the job that will
			//be run.
			if(isset($variables["job"])){
				if(!$variables["job"] === "" || $variables["job"] === NULL)
					$this->verifyErrors[] = "The `job` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `job` field must be set.";

			//checks to see if crontab is in the (somewhat) right format
			if(isset($variables["crontab"])){
				$explode = explode("::", $variables["crontab"]);
				if(count($explode) != 5)
					$this->verifyErrors[] = "The `crontab` field is not of the proper form.  There must be four pairs of '::' in the field's value.";
			}//end if

			return parent::verifyVariables($variables);

		}//end method


		function prepareVariables($variables){

			$temparray[0]=$variables["min"];
			$temparray[1]=$variables["hrs"];
			$temparray[2]=$variables["date"];
			$temparray[3]=$variables["mo"];
			$temparray[4]=$variables["day"];
			$variables["crontab"]=implode("::",$temparray);

			if($variables["startdate"]){
				$variables["startdatetime"] = $variables["startdate"];
				if($variables["starttime"])
					$variables["startdatetime"] .= " ".$variables["starttime"];
			}else
				$variables["startdatetime"] = NULL;


			if($variables["enddate"]){
				$variables["enddatetime"] = $variables["enddate"];
				if($variables["endtime"])
					$variables["enddatetime"].=" ".$variables["endtime"];
			}else
				$variables["enddatetime"] = NULL;


			return $variables;
		}

	}//end class
}//end if
?>