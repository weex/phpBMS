<?php 
class recurringinvoice{
	function recurringinvoice($db,$invoiceid){
		$this->db = $db;
		$this->invoiceid = ((int) $invoiceid);
		
		$this->weekArray = array("First"=>"1", "Second"=>"2", "Third"=>"3", "Fourth"=>"4", "Last"=>"5");
		
		$this->dayOfWeekArray = array();
		$this->dayOfWeekArray[nl_langinfo(constant("DAY_1"))] = 7;
		for($i=1; $i<=6; $i++)
			$this->dayOfWeekArray[nl_langinfo( constant("DAY_".($i+1)) )] = $i;
	}
	
	
	function delete(){
		$querystatement = "DELETE FROM recurringinvoices WHERE invoiceid =".$this->invoiceid;
		$this->db->query($querystatement);
	}
	
	
	function getRecord(){
		$querystatement = "SELECT * FROM recurringinvoices WHERE invoiceid = ".$this->invoiceid;
		$queryresult = $this->db->query($querystatement);
		if($this->db->numRows($queryresult))
			$therecord = $this->db->fetchArray($queryresult);
		else
			$therecord = $this->getDefaults();
		
		return $therecord;
	}
	
	
	function getDefaults(){
	
		$therecord["id"] = NULL;
		$therecord["type"] = "Daily";
		$therecord["until"] = dateToString(mktime(),"SQL");;
		$therecord["every"] =1;
		$therecord["times"] = 1;
		$therecord["eachlist"] = NULL;
		$therecord["ontheweek"] = NULL;
		$therecord["ontheday"] = NULL;
		
		$therecord["includepaymenttype"] = 0;
		$therecord["includepaymentdetails"] = 0;		
		$therecord["statusid"] = $this->getDefaultStatus();
		$therecord["assignedtoid"] = 0;
	
		$therecord["name"] = "";
		$therecord["firstrepeat"] = NULL;
		$therecord["lastrepeat"] = NULL;
		$therecord["timesrepeated"] = 0;

		$therecord["notificationroleid"] = 0;

		return $therecord;
	}
	
	
	function insert($variables){

		$querystatement = "INSERT INTO recurringinvoices (invoiceid, `type`, `every`, `eachlist`, `ontheday`, `ontheweek`,   							
							`times`,`until`, `name`,includepaymenttype, includepaymentdetails, statusid, assignedtoid,notificationroleid) VALUES (";

		$thename="Every ";
		
		$querystatement .= $this->invoiceid.", ";
		$querystatement .= "'".$variables["type"]."', ";
		$querystatement .= ((int) $variables["every"]).", ";
		switch($variables["type"]){
			case "Daily":
				if($variables["every"] != 1)
					$thename = $variables["every"]." days";
				else
					$thename = " day ";
				
				$querystatement .= "NULL, NULL, NULL, ";
			break;

			case "Weekly":
				if($variables["every"] != 1)
					$thename .= $variables["every"]." weeks on";
				else
					$thename .= "week on";
					
				foreach(explode("::",$variables["eachlist"]) as $dayNum){
					$tempday = ($dayNum != 7)?($dayNum+1):(1);
					$thename .=" ".nl_langinfo(constant("ABDAY_".$tempday)).", ";
				}
				$thename = substr($thename,0,strlen($thename)-2);

				if(strpos($thename,",") != false)
					$thename = strrev(preg_replace("/,/","dna ",strrev($thename),1));
				
				$querystatement .= "'".$variables["eachlist"]."', NULL, NULL, ";
			break;

			case "Monthly":			
				if($variables["every"] != 1)
					$thename .= $variables["every"]." months";
				else
					$thename .= "month";

				$thename .= " on the";
				if($variables["monthlyWhat"] == 1){
				
					foreach(explode("::",$variables["eachlist"]) as $dayNum)
						$thename .=" ".ordinal($dayNum).", ";

					$thename = substr($thename,0,strlen($thename)-2);

					if(strpos($thename,",") != false)
						$thename = strrev(preg_replace("/,/","dna ",strrev($thename),1));

				
					$querystatement .= "'".$variables["eachlist"]."', NULL, NULL, ";
				} else {
					foreach($this->weekArray as $key=>$value)
						if($value == $variables["monthlyontheweek"])
							$thename .= " ".strtolower($key);

					foreach($this->dayOfWeekArray as $key=>$value)
						if($value == $variables["monthlyontheday"])
							$thename .= " ".$key;
						
					$querystatement .= "NULL, ".((int) $variables["monthlyontheday"]).", ".((int) $variables["monthlyontheweek"]).", ";
				}
			break;
			
			case "Yearly":
				if($variables["every"] > 1)
					$thename .= $variables["every"]." years";
				else
					$thename .= "year";
				
				$thename .= " in";
				
				foreach(explode("::",$variables["eachlist"]) as $monthNum)
					$thename .=" ".nl_langinfo(constant("MON_".$monthNum)).", ";
					
				$thename = substr($thename,0,strlen($thename)-2);
				if(strpos($thename,",") != false)
					$thename = strrev(preg_replace("/,/","dna ",strrev($thename),1));
					
				$querystatement .= "'".$variables["eachlist"]."', ";

				if(isset($variables["yearlyOnThe"])){
					$thename .= " on the";
					foreach($this->weekArray as $key=>$value)
						if($value == $variables["yearlyontheweek"])
							$thename .= " ".strtolower($key);
					
					foreach($this->dayOfWeekArray as $key=>$value)
						if($value == $variables["yearlyontheday"])
							$thename .= " ".$key;

					$querystatement .= ((int) $variables["yearlyontheday"]).", ";
					$querystatement .= ((int) $variables["yearlyontheweek"]).", ";
				} else
					$querystatement .= "NULL, NULL, ";				
			break;
		}

		switch($variables["end"]){
			case "never":
				$querystatement .= "NULL, NULL, ";
				break;
				
			case "after":
				$thename .= " for ".$variables["times"];
				
				$querystatement .= ((int) $variables["times"]).", NULL, ";
				break;
				
			case "on date":
				$thename .= " until ".$variables["until"];
				$querystatement .= "NULL, '".sqlDateFromString($variables["until"])."', ";
				break;
		}
		$thename .= ".";
		
		$querystatement .= "'".mysql_real_escape_string($thename)."', ";
		
		if(!isset($variables["includepaymenttype"])) $variables["includepaymenttype"] = 0;
		$querystatement .= ((int) $variables["includepaymenttype"]).", ";
		
		if(!isset($variables["includepaymentdetails"])) $variables["includepaymentdetails"] = 0;
		$querystatement .= ((int) $variables["includepaymentdetails"]).", ";

		$querystatement .= ((int) $variables["statusid"]).", ";

		$querystatement .= ((int) $variables["assignedtoid"]).", ";
				
		$querystatement .= ((int) $variables["notificationroleid"]);
		
		$querystatement .= ")";

		$this->db->query($querystatement);
	}//end method
	
	
	function process(){
		if(isset($_POST["command"])){
			switch($_POST["command"]){
				case "update":
					$this->delete();
					if(isset($_POST["recurr"])){
						$this->insert(addSlashesToArray($_POST));
						$therecord = $this->getRecord();
						$therecord["statusMessage"] = "recurrence saved";
					} else {
						$therecord = $this->getDefaults();
						$therecord["statusMessage"] = "recurrence removed";
					}// endif
					break;
					
				case "cancel":
					break;
			
			}// endswitch
		} else {
			$therecord = $this->getRecord();
		}
		
		return $therecord;
		
	}//end method
	
	
	function showWeeklyOptions($therecord,$invoiceDate){
		if($therecord["type"] == "Weekly")
			$daysSelected = explode("::",$therecord["eachlist"]);
		else
			$daysSelected = array(strftime("%u",$invoiceDate));
			
		$daysAvailable = array(7,1,2,3,4,5,6);
		
		foreach($daysAvailable as $dayNum){
			$tempday = ($dayNum != 7)?($dayNum+1):(1);
			?><button id="dayOption<?php echo $dayNum?>" class="<?php 
			
			if(in_array($dayNum,$daysSelected))
				echo "pressed"; 
			
			?>Buttons" type="button" value="<?php echo $dayNum?>" onclick="daySelect(this)"><?php 
			
			echo nl_langinfo(constant("ABDAY_".$tempday));
			
			?></button><?php
		}
			
		
	}
	
	function showMonthlyOptions($therecord,$invoiceDate){
		if($therecord["type"] == "Monthly" && $therecord["eachlist"])
			$daysSelected = explode("::",$therecord["eachlist"]);
		else
			$daysSelected = array(strftime("%e",$invoiceDate));
			
		
		for($dayNum = 1; $dayNum <= 31; $dayNum++){
			?><button id="monthDayOption<?php echo $dayNum?>" class="<?php 
			
			if(in_array($dayNum,$daysSelected))
				echo "pressed"; 
			
			?>Buttons monthDays" type="button" value="<?php echo $dayNum?>" onclick="monthDaySelect(this)" <?php 
			
				if($therecord["ontheday"])
					echo 'disabled="disabled"';
			
			?>><?php 
			
			echo $dayNum;
			
			?></button><?php
			if(($dayNum % 7) == 0) echo "<br />";
		}			
		
	}//end method

	
	function showYearlyOptions($therecord,$invoiceDate){
		if($therecord["type"] == "Yearly")
			$monthsSelected = explode("::",$therecord["eachlist"]);
		else
			$monthsSelected = array(date("n",$invoiceDate));
			
		for($monthNum = 1; $monthNum <=12; $monthNum++){
			?><button id="yearlyMonthOption<?php echo $monthNum?>" class="<?php 
			
			if(in_array($monthNum,$monthsSelected))
				echo "pressed"; 
			
			?>Buttons yearlyMonths" type="button" value="<?php echo $monthNum?>" onclick="yearlyMonthSelect(this)"><?php 
			
			echo nl_langinfo(constant("ABMON_".$monthNum));
			
			?></button><?php
			if(($monthNum % 4) == 0) echo "<br />";
		}
	}
	
	
	function getDefaultStatus(){
			
		$querystatement="SELECT id FROM invoicestatuses WHERE invoicedefault=1";
		$queryresult=$this->db->query($querystatement);

		$therecord=$this->db->fetchArray($queryresult);
		
		return $therecord["id"];
	}
	

	function showStatusDropDown($statusid){
		$querystatement="SELECT invoicestatuses.id,invoicestatuses.name,invoicestatuses.invoicedefault,users.firstname,users.lastname FROM 
						(invoicestatuses LEFT JOIN users ON invoicestatuses.defaultassignedtoid=users.id)WHERE invoicestatuses.inactive=0
						ORDER BY invoicestatuses.priority,invoicestatuses.name";
		$queryresult=$this->db->query($querystatement);

		?><label for="statusid">status</label><br />
		<select id="statusid" name="statusid">
			<?php
			$options="";
			while($therecord=$this->db->fetchArray($queryresult)){
				if($therecord["firstname"]!="" || $therecord["lastname"]!="")
					$options["s".$therecord["id"]]=trim($therecord["firstname"]." ".$therecord["lastname"]);
				?><option value="<?php echo $therecord["id"]?>" <?php if($statusid==$therecord["id"]) echo "selected=\"selected\""?>><?php echo $therecord["name"]?></option><?php
			}
			?>
		</select><?php
	}//end method
	
	
	function showRolesDropDown($selectedRoleID){
		$querystatement = "SELECT id,name FROM roles WHERE inactive = 0 ORDER BY id";

		$queryresult=$this->db->query($querystatement);
		
		?><label for="notificationroleid">Send a notification e-mail to users with role</label><br />
		<select id="notificationroleid" name="notificationroleid">
			<option value="0" <?php if($selectedRoleID == 0) echo 'selected="selected"'?>>do not send notification</option>
			<?php 
				while($therecord=$this->db->fetchArray($queryresult)){
					?><option value="<?php echo $therecord["id"]?>" <?php if($selectedRoleID == $therecord["id"]) echo 'selected="selected"'?>><?php echo $therecord["name"]?></option><?php 
				}				
			?>
			<option value="-100" <?php if($selectedRoleID == -100) echo 'selected="selected"'?>>Administrators</option>
		</select><?php
	
	}// end method
	
}//end class
?>