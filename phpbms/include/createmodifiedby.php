<div class="box" id="createmodifiedby">
	<div id="savecancel2"><?php showSaveCancel(2)?></div>
	<div id="createdbydiv" class="small">
		<input name="createdby" type="hidden" value="<?php $therecord["createdby"] ?>" />
		created by<br />		
		<div><input name="createdbydisplay" type="text" value="<?php echo $createdby?>" size="20" maxlength="164" readonly="true" class="uneditable fieldCreateModify" tabindex="0" /></div>
		<div><input name="creationdate" type="text" value="<?php echo formatFromSQLDatetime($therecord["creationdate"]) ?>" size="20" maxlength="164" readonly="true" class="uneditable fieldCreateModify" tabindex="0" /></div>
	</div>
	<div class="small">
		<input name="modifiedby" type="hidden" value="<?php $therecord["modifiedby"] ?>" /><input id="cancelclick" name="cancelclick" type="hidden" value="0" />
		modified by<br/>
		<div><input name="modifiedbydisplay" type="text" value="<?php echo $modifiedby?>" size="20" maxlength="164" readonly="true" class="uneditable fieldCreateModify" tabindex="0" /></div>
		<div><input name="modifieddate" type="text" value="<?php echo formatFromSQLDatetime($therecord["modifieddate"]) ?>" size="20" maxlength="164" readonly="true" class="uneditable fieldCreateModify" tabindex="0" /></div>			
	</div>	
</div>
