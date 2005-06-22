<div class="recordbottom"><div style="float:right;padding:0px;margin:0px;"><br><?php include"savecancel.php"?></div>
<table border=0 cellspacing=0 cellpadding=0>
<tr>
<td class=small>
created by<br>
<input name="createdby" type="hidden" value="<?php $therecord["createdby"] ?>">
<input name="createdbydisplay" type="text" value="<?PHP echo $createdby?>" size="20" maxlength="164" readonly="true" class="uneditable" style="width:135px;margin-bottom:2px;"><br>
<input name="creationdate" type="text" value="<?PHP echo $therecord["creationdate"] ?>" size="20" maxlength="164" readonly="true" class="uneditable" style="width:135px;margin-bottom:2px;">  
</td>
<td class=small style="padding-left:3px;">
modified by<br>
<input name="modifiedby" type="hidden" value="<?php $therecord["modifiedby"] ?>">
<input name="modifiedbydisplay" type="text" value="<?PHP echo $modifiedby?>" size="20" maxlength="164" readonly="true" class="uneditable" style="width:135px;margin-bottom:2px;"><br>
<input name="modifieddate" type="text" value="<?PHP echo $therecord["modifieddate"] ?>" size="20" maxlength="164" readonly="true" class="uneditable" style="width:135px;margin-bottom:2px;">
</td>
</tr>
</table><input name="cancelclick" type="hidden" value="0"></div>