<?php if (!defined('IN_WPRO')) exit; 
require(WPRO_DIR.'conf/defaultValues/wproCore_fileBrowser.inc.php');
?>
<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'preview')?></legend>

<div id="<?php echo $prefix ?>preview" align="center" style="height:240px; width:303px; overflow:auto;" class="previewFrame"><img id="<?php echo $prefix ?>imagepreview" src="core/images/spacer.gif"border="0" title="" alt=""></div>

</fieldset>


<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'properties')?></legend>
<?php 

$t = $this->createUI2ColTable();
$t->width = 'small';
	$s = $this->createHTMLSelect();
	$s->attributes = array('name'=>$prefix.'widthUnits','onchange' => 'FB.embedPlugins["images"].updatePreview("'.$prefix.'");');
	$s->options = array(''=>$langEngine->get('core', 'pixels'),'%'=>$langEngine->get('core', 'percent'));
	$s->selected='px';
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'width'), 'w'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'width',
	'accesskey' => 'w',
	'onchange' => 'FB.embedPlugins["images"].updatePreview("'.$prefix.'");',
)).$s->fetch(), $prefix.'width');
	$s = $this->createHTMLSelect();
	$s->attributes = array('name'=>$prefix.'heightUnits','onchange' => 'FB.embedPlugins["images"].updatePreview("'.$prefix.'");');
	$s->options = array(''=>$langEngine->get('core', 'pixels'),'%'=>$langEngine->get('core', 'percent'));
	$s->selected='px';
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'height'), 'h'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'height',
	'accesskey' => 'h',
	'onchange' => 'FB.embedPlugins["images"].updatePreview("'.$prefix.'");',
)).$s->fetch(), $prefix.'height');
$t->addRow('&nbsp;', '<a href="javascript:undefined" onclick="FB.embedPlugins[\'images\'].resetDimensions()">'.$langEngine->get('wproCore_fileBrowser', 'resetDimensions').'</a>');

$t->display();
?>
</fieldset>