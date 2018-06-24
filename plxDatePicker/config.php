<?php

if (!defined('PLX_ROOT')) { exit; }

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if (!empty($_POST)) {
	$oldTheme = $plxPlugin->getParam('theme');
	foreach ($plxPlugin->params as $field=>$type1) {
		if ($type1 === 'numeric') {
			$value = intval($_POST[$field]);
		} else {
			$value = $_POST[$field];
		}
		$plxPlugin->setParam($field, $value, $type1);
		}
	$plxPlugin->saveParams();
	if ($_POST['theme'] !=  $oldTheme) {
		$plxPlugin->setCss($_POST['theme']);
	}
	header('Location: parametres_plugin.php?p='.$plugin);
	exit;
}

$themes = array();
foreach (glob(dirname(__FILE__).'/datepick/*.css') as $v) {
	$w = basename($v, '.datepick.css');
	if (substr($w, 0, 2) != 'ui') {
		$themes[$w] = ucfirst($w);
	}
}

$items = array('theme'=>$themes);
?>

<h2><?php echo($plxPlugin->getInfo('title')); ?></h2>

<form id="form_<?php echo $plugin; ?>" method="post">
<?php
echo "\t".plxToken::getTokenPostMethod()."\n";
foreach($plxPlugin->params as $field=>$type) {
	$value = $plxPlugin->getParam($field);
	if (empty($value)) {
		$value = $plxPlugin->default_values[$field];
	} ?>
	<p>
		<label for="id_<?php echo $field; ?>"><?php $plxPlugin->lang('L_JQUERY_'.strtoupper($field)); ?></label>
<?php plxUtils::printSelect($field, $items[$field], $value); ?>
	</p>
<?php
}
?>
	<p>
		<input type="submit" value="<?php echo L_ARTICLE_UPDATE_BUTTON; ?>"/>
	</p>
</form>
<iframe id="myFrame" src="" allowfullscreen="false" width="250" height="220" frameborder="0"></iframe>
