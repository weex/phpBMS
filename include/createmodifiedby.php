<div class="box" style="clear:both;"><div style="float:right;padding:0px;margin:0px;"><br /><?php showSaveCancel(2)?></div>
<table border=0 cellspacing=0 cellpadding=0>
<tr>
<td class=small>
created by<br>
<input name="createdby" type="hidden" value="<?php $therecord["createdby"] ?>" />
<input name="createdbydisplay" type="text" value="<?PHP echo $createdby?>" size="20" maxlength="164" readonly="true" class="uneditable" style="width:135px;margin-bottom:2px;" tabindex="-1" /><br />
<input name="creationdate" type="text" value="<?PHP echo $therecord["creationdate"] ?>" size="20" maxlength="164" readonly="true" class="uneditable" style="width:135px;margin-bottom:2px;" tabindex="-1" />
</td>
<td class=small style="padding-left:3px;">
modified by<br>
<input name="modifiedby" type="hidden" value="<?php $therecord["modifiedby"] ?>" />
<input name="modifiedbydisplay" type="text" value="<?PHP echo $modifiedby?>" size="20" maxlength="164" readonly="true" class="uneditable" style="width:135px;margin-bottom:2px;" tabindex="-1" /><br />
<input name="modifieddate" type="text" value="<?PHP echo $therecord["modifieddate"] ?>" size="20" maxlength="164" readonly="true" class="uneditable" style="width:135px;margin-bottom:2px;" tabindex="-1" />
</td>
</tr>
</table><input id="cancelclick" name="cancelclick" type="hidden" value="0" /></div>
