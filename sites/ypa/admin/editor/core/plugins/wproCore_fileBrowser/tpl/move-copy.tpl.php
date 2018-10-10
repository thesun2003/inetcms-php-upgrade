<?php if (!defined('IN_WPRO')) exit; ?>
<input type="hidden" name="srcFolderID" value="<?php echo htmlspecialchars($srcFolderID) ?>" />
<input type="hidden" name="srcFolderPath" value="<?php echo htmlspecialchars($srcFolderPath) ?>" />

<input type="hidden" name="destFolderID" value="<?php echo htmlspecialchars($destFolderID) ?>" />
<input type="hidden" name="destFolderPath" value="<?php echo htmlspecialchars($destFolderPath) ?>" />

<input type="hidden" name="requiredPermissions" value="<?php echo htmlspecialchars($requiredPermissions) ?>" />

<input type="hidden" name="files" value="" />

<input type="hidden" name="moveCopyID" value="<?php echo htmlspecialchars($moveCopyID) ?>" />

<script type="text/javascript">
initMoveCopy();
</script>

<p><?php echo $langEngine->get('wproCore_fileBrowser', 'selectDestinationFolder') ?></p>

<!-- outlook bar -->
<div class="leftColumn">

<iframe class="outlookBar inset" id="outlookFrame" name="outlookFrame" src="dialog.php?dialog=wproCore_fileBrowser&action=outlook&amp;filesOnly=true&amp;requiredPermissions=<?php echo htmlspecialchars($requiredPermissions) ?>&amp;current=<?php echo htmlspecialchars($destFolderID); ?>&amp;mode=<?php echo htmlspecialchars($srcFolderType); ?><?php echo ($EDITOR->appendToQueryStrings ? '&amp;'.$EDITOR->appendToQueryStrings : '') ?>&amp;<?php echo htmlspecialchars($wpsname); ?>=<?php echo htmlspecialchars($wpsid); ?><?php echo strip_tags(defined('SID') ? '&amp;'.SID : ''); ?>#<?php echo htmlspecialchars($srcFolderID); ?>" frameborder="0"></iframe>
</div>
<!-- end outlook bar -->

<div class="rightColumn insetWhite">

<?php 

function buildFolderArray(&$UI, &$pNode, $folders) {
	global $EDITOR;
	$i=0;
	foreach($folders as $folder) {
		$node = & $UI->createNode();

		$node->id = $folder['path'];
		$node->caption = ($folder['name']==$EDITOR->thumbnailFolderName)?$EDITOR->thumbnailFolderDisplayName:$folder['name'];
		$node->isFolder = true;
		$node->caption_onclick = 'function (node) {selectFolder(\''.addslashes($folder['path']).'\');}';
		if (!empty($folder['children'])) {
			buildFolderArray($UI, $node, $folder['children']);
		}
		$pNode->appendChild($node);
		$i++;
	}
}

$UI = $this->createUITree();
$UI->width = 327;
$UI->height = 280;


buildFolderArray($UI, $UI, $folders);


$UI->display();

?>

</div>

<label><input type="checkbox" name="overwrite" value="true"<?php if ($overwrite) :?> checked="checked"<?php endif ?> /> <?php echo $langEngine->get('wproCore_fileBrowser', 'overwrite') ?></label><br />

<label><input type="checkbox" name="goToDest" value="true"<?php if ($goToDest) :?> checked="checked"<?php endif ?> /> <?php echo $langEngine->get('wproCore_fileBrowser', 'goToDestination') ?></label>