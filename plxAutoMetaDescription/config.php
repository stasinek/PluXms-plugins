<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	$plxPlugin->setParam('nbwords', $_POST['nbwords'], 'numeric');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxAutoMetaDescription');
	exit;
}
$nbwords = $plxPlugin->getParam('nbwords')!='' ? $plxPlugin->getParam('nbwords') : 30;

?>

<form action="parametres_plugin.php?p=plxAutoMetaDescription" method="post" id="form_plxAutoMetaDescription">
	<fieldset>
		<?php $plxPlugin->lang('L_NBWORDS_HELP') ?>
		<p class="field"><label for="id_nbwords"><?php $plxPlugin->lang('L_NBWORDS') ?></label></p>
		<?php plxUtils::printInput('nbwords',$nbwords,'text','4-4') ?>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>