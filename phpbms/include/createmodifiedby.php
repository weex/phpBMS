<div class="box" id="createmodifiedby">
	<div id="savecancel2"><?php showSaveCancel(2)?></div>
	<div id="createdbydiv" class="small">
		<input name="createdby" type="hidden" value="<?php $therecord["createdby"] ?>" />
		created by<br />		
		<div><input name="createdbydisplay" type="text" value="<?PHP echo $createdby?>" size="20" maxlength="164" readonly="true" class="uneditable fieldCreateModify" tabindex="0" /></div>
		<div><input name="creationdate" type="text" value="<?PHP echo formatDateTime($therecord["creationdate"]) ?>" size="20" maxlength="164" readonly="true" class="uneditable fieldCreateModify" tabindex="0" /></div>
	</div>
	<div class="small">
		<input name="modifiedby" type="hidden" value="<?php $therecord["modifiedby"] ?>" /><input id="cancelclick" name="cancelclick" type="hidden" value="0" />
		modified by<br/>
		<div><input name="modifiedbydisplay" type="text" value="<?PHP echo $modifiedby?>" size="20" maxlength="164" readonly="true" class="uneditable fieldCreateModify" tabindex="0" /></div>
		<div><input name="modifieddate" type="text" value="<?PHP echo formatTimestamp($therecord["modifieddate"],true) ?>" size="20" maxlength="164" readonly="true" class="uneditable fieldCreateModify" tabindex="0" /></div>			
	</div>	
</div>
