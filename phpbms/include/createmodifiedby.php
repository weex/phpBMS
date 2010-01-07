<?php if(isset($db) && isset($therecord)){?>
<div id="createmodifiedby" >
	<div id="savecancel2"><?php showSaveCancel(2)?></div>
	<table>
		<tr id="cmFirstRow">
			<td class="cmTitles">
				<input name="createdby" type="hidden" value="<?php $therecord["createdby"] ?>" />
				<input name="creationdate" type="hidden" value="<?php echo formatFromSQLDatetime($therecord["creationdate"]) ?>"/>
				created
			</td>
			<td><?php echo htmlQuotes($phpbms->getUserName($therecord["createdby"]))?></td>
			<td><?php echo formatFromSQLDatetime($therecord["creationdate"]) ?></td>
		</tr>
		<tr>
			<td class="cmTitles">
				<input name="modifiedby" type="hidden" value="<?php $therecord["modifiedby"] ?>" />
				<input id="cancelclick" name="cancelclick" type="hidden" value="0" />
				<input name="modifieddate" type="hidden" value="<?php echo formatFromSQLDatetime($therecord["modifieddate"]) ?>"/>
				modified
			</td>
			<td><?php echo htmlQuotes($phpbms->getUserName($therecord["modifiedby"]))?></td>
			<td><?php echo formatFromSQLDatetime($therecord["modifieddate"]) ?></td>
		</tr>
	</table>
</div>
<?php }//endif ?>
