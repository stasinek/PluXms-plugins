<?php if(!defined('PLX_ROOT')) exit;

if(!empty($_POST)) {
	$plxPlugin->setParam('extensions', $_POST['extensions'], 'cdata');
	$plxPlugin->setParam('sort', $_POST['sort'], 'cdata');
	$plxPlugin->setParam('activeSeparateur', $_POST['activeSeparateur']);
	$plxPlugin->setParam('separateur', $_POST['separateur'], 'cdata');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=staticgaleries');
	exit;
}
?>

<h2><?php echo $plxPlugin->getInfo('title') ?></h2>

<form action="parametres_plugin.php?p=staticgaleries" method="post" id="staticgaleries">
	<fieldset class="withlabel">
		<p><?php echo $plxPlugin->getLang('L_EXTENSIONS') ?></p>
		<?php plxUtils::printInput('extensions', $plxPlugin->getParam('extensions'), 'text'); ?>

		<p><?php echo $plxPlugin->getLang('L_TSORT') ?></p>
		<?php plxUtils::printSelect('sort',
			array(
				'sort'=>$plxPlugin->getLang('L_SORT'), 
				'rsort'=>$plxPlugin->getLang('L_RSORT')),
			$plxPlugin->getParam('sort')); 
		?>

		<p><?php echo $plxPlugin->getLang('L_ACTIVE_ARIANE') ?></p>
		<?php plxUtils::printSelect('activeSeparateur', array('1'=>L_YES,'0'=>L_NO), $plxPlugin->getParam('activeSeparateur')); ?>


		<p><?php echo $plxPlugin->getLang('L_SEPARATEUR') ?></p>
		<?php plxUtils::printInput('separateur', $plxPlugin->getParam('separateur'), 'text'); ?>
	</fieldset>
	<input type="submit" name="submit" value="Enregistrer" />
</form>
