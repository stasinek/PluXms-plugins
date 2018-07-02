<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
if(!empty($_POST)) {
	$plxPlugin->setParam('trackcode', $_POST['trackcode'], 'cdata');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxPiwik');
	exit;
}
?>

<h2><?php $plxPlugin->lang('L_TITLE') ?></h2>
<p><?php $plxPlugin->lang('L_DESCRIPTION') ?></p>

<form action="parametres_plugin.php?p=plxPiwik" method="post">
	<?php $plxPlugin->lang('L_TRACKING_CODE') ?> : 
	<?php plxUtils::printArea('trackcode', plxUtils::strCheck($plxPlugin->getParam('trackcode')), '', 20); ?><br />
	<br />
	<input type="submit" name="submit" value="Enregistrer" />
</form>
