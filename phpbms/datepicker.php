<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
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
	require_once("include/session.php");
	require_once("include/common_functions.php");
			
	function displayBox($month,$year,$selectedDate){
		global $dblink;
		
		$thedate=mktime(0,0,0,$month,1,$year);
		$today=mktime(0,0,0);
		$todayArray=getdate($today);
		if($selectedDate!="0000-00-00"){
			$selDate=dateFromSQLDate($selectedDate);
			$tempDate=getdate($selDate);
			$displayLongDate=$tempDate["month"]." ".$tempDate["mday"].", ".$tempDate["year"];
		}
		else {
			$selDate=NULL;
			$displayLongDate="Click a Date";
		}		
?><script language="javascript">displayLongDate="<?php echo $displayLongDate ?>";</script>
	<?php 
	?><table class="dp" cellspacing="0" cellpadding="0" border=0>
	<tr>
		<td colspan=6 class="dpHead"><?php echo date("F, Y",$thedate)?></td>
		<td class="dpHead"><button type="button" class="graphicButtons buttonX" id="DPCancel" onclick="closeDPBox();"><span>x</span></button></td>
	</tr>
	<tr>
		<td class="dpButtons" onClick="loadMonth('<?php echo $_SESSION["app_path"]?>','<?php echo $month?>','<?php echo $year-1?>'<?php if($selDate) echo ",'".date("m/d/Y",$selDate)."'"?>)">&lt;&lt;</td>
		<td class="dpButtons" onClick="loadMonth('<?php echo $_SESSION["app_path"]?>','<?php if($month==1) echo "12"; else echo $month-1?>','<?php if($month==1) echo $year-1; else echo $year?>'<?php if($selDate) echo ",'".date("m/d/Y",$selDate)."'"?>)">&lt;</td>
		<td colspan=3 class="dpButtons" onClick="loadMonth('<?php echo $_SESSION["app_path"]?>','<?php echo date('m',$today)?>','<?php echo $todayArray["year"]?>'<?php if($selDate) echo ",'".date("m/d/Y",$selDate)."'"?>)">Today</td>
		<td class="dpButtons" onClick="loadMonth('<?php echo $_SESSION["app_path"]?>','<?php if($month==12) echo "1"; else echo $month+1?>','<?php if($month==12) echo $year+1; else echo $year;?>'<?php if($selDate) echo ",'".date("m/d/Y",$selDate)."'"?>)">&gt;</td>
		<td class="dpButtons" onClick="loadMonth('<?php echo $_SESSION["app_path"]?>','<?php echo $month?>','<?php echo $year+1?>'<?php if($selDate) echo ",'".date("m/d/Y",$selDate)."'"?>)">&gt;&gt;</td>
	</tr>
	<tr  class="dpDayNames">
		<td width="14.286%">S</td>
		<td width="14.286%">M</td>
		<td width="14.286%">T</td>
		<td width="14.286%">W</td>
		<td width="14.286%">R</td>
		<td width="14.286%">F</td>
		<td width="14.286%">S</td>
	</tr>
	<?php 
		$firstdate=getdate($thedate);
		$mydate=$firstdate;
		while($firstdate["month"]==$mydate["month"]){
			if($mydate["wday"]==0) echo "<TR class=\"dpWeek\">";
			if($firstdate==$mydate){
				// firstdate, so we may have to put in blanks
				echo "<TR class=\"dpWeek\">";
				for($i=0;$i<$mydate["wday"];$i++)
					echo "<TD>&nbsp;</TD>";
			}
			
			$dayclass="dpReg";
			if($thedate==$selDate) $dayclass="dpSelected";
			elseif($thedate==$today) $dayclass="dpToday";
			
			echo "<TD class=\"".$dayclass."\" onMouseOut=\"dpUnhighlightDay();\" onMouseOver=\"dpHighlightDay(".$mydate["year"].",".date("n",$thedate).",".$mydate["mday"].")\" onClick=\"dpClickDay(".$mydate["year"].",".date("n",$thedate).",".$mydate["mday"].")\">".$mydate["mday"]."</TD>";
			
			if($mydate["wday"]==6) echo "</tr>";
			$thedate=strtotime("tomorrow",$thedate);
			$mydate=getdate($thedate);
		}
		
		if($mydate["wday"]!=0){
			for($i=6;$i>=$mydate["wday"];$i--)
				echo "<TD>&nbsp;</TD>";
			echo "</TR";
		}
	?>
	<tr><td id="dpExp" class="dpExplanation" colspan="7"><?php echo $displayLongDate ?></td></tr>	
</table>
<?php	}//end function

	if(!isset($_GET["cm"])) 
		$_GET["cm"]="shw";
	
	if(!isset($_GET["sd"]))
		$_GET["sd"]="0000-00-00";

	switch($_GET["cm"]){
		case "shw":
			displayBox($_GET["m"],$_GET["y"],$_GET["sd"]);
		break;
	}
	
?>