<?php
/*
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
		
	function displayTPBox(){	
?>

<table border="0" cellspacing=0 cellpadding=0 style="width:230px;">
	<tr>
		<td width="100%" class="tpHead">&nbsp;</td>
		<td class="tpHead"><button type="buttton" class="invisibleButtons" id="TPCancel" onClick="closeTPBox();"><img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-x.png" align="absmiddle" alt="x" width="16" height="16" border="0" /></button></td>		
	</tr>
</table>
<table border="0" cellspacing=0 cellpadding=0 class="tpHour" style="width:230px;">
	<tr>
		<td onClick="tpClickHour(0)">00</td>
		<td onClick="tpClickHour(1)">01</td>
		<td onClick="tpClickHour(2)">02</td>
		<td onClick="tpClickHour(3)">03</td>
		<td onClick="tpClickHour(4)">04</td>
		<td onClick="tpClickHour(5)">05</td>
		<td onClick="tpClickHour(6)">06</td>
		<td onClick="tpClickHour(7)">07</td>
		<td onClick="tpClickHour(8)">08</td>
		<td onClick="tpClickHour(9)">09</td>
		<td onClick="tpClickHour(10)">10</td>
		<td onClick="tpClickHour(11)">11</td>
	</tr>
	<tr>
		<td onClick="tpClickHour(12)">12</td>
		<td onClick="tpClickHour(13)">13</td>
		<td onClick="tpClickHour(14)">14</td>
		<td onClick="tpClickHour(15)">15</td>
		<td onClick="tpClickHour(16)">16</td>
		<td onClick="tpClickHour(17)">17</td>
		<td onClick="tpClickHour(18)">18</td>
		<td onClick="tpClickHour(19)">19</td>
		<td onClick="tpClickHour(20)">20</td>
		<td onClick="tpClickHour(21)">21</td>
		<td onClick="tpClickHour(22)">22</td>
		<td onClick="tpClickHour(23)">23</td>
	</tr>
</table>
<table border="0" cellspacing=0 cellpadding=0 class="tpMinute" id="tpMinuteLess" width="230px">
	<tr>
		<td onClick="tpClickMinute(this)">:00</td>
		<td onClick="tpClickMinute(this)">:05</td>
		<td onClick="tpClickMinute(this)">:10</td>
		<td onClick="tpClickMinute(this)">:15</td>
		<td onClick="tpClickMinute(this)">:20</td>
		<td onClick="tpClickMinute(this)">:25</td>
	</tr>
	<tr>
		<td onClick="tpClickMinute(this)">:30</td>
		<td onClick="tpClickMinute(this)">:35</td>
		<td onClick="tpClickMinute(this)">:40</td>
		<td onClick="tpClickMinute(this)">:45</td>
		<td onClick="tpClickMinute(this)">:50</td>		
		<td onClick="tpClickMinute(this)">:55</td>		
	</tr>
</table>
<table border="0" cellspacing=0 cellpadding=0 class="tpMinute" id="tpMinuteMore"  width="230px" style="display:none;">
	<tr>
	<?php 
		for($i=0;$i<60;$i++){
			if($i!=0 && ($i/6)==round($i/6,0))
				echo ("</TR><TR>");
			?><td onClick="tpClickMinute(this)">:<?php echo str_pad($i,2,"0",STR_PAD_LEFT)?></td><?php
		}
	?>
	</tr>
</table>

<div align=right>
<input name="TPmoreless" id="TPmoreless" type="button" value="more" class="smallButtons" onClick="switchMinutes(this)">
</div>
<?php	}//end function

	if(!isset($_GET["cm"])) 
		$_GET["cm"]="shw";
	
	if(!isset($_GET["st"]))
		$_GET["sd"]="12:00 AM";

	switch($_GET["cm"]){
		case "shw":
			displayTPBox();
		break;
	}
	
?>
