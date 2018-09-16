<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {

	# sauvegarde des paramètres
	$plxPlugin->setParam('timeout', $_POST['timeout'], 'numeric');
	$plxPlugin->saveParams();

	# redirection sur la page de configuration du plugin
	header('Location: parametres_plugin.php?p=plxRescueData');
	exit;
}

# initialisation des paramètres par défaut
$params = $plxPlugin->getParams();
$timeout = empty($params['timeout']) ? '30' : $params['timeout']['value'];

?>

<form action="parametres_plugin.php?p=plxRescueData" method="post" id="form_autosave">
	<fieldset>
		<p class="field"><label for="timeout"><?php $plxPlugin->lang('TIMEOUT') ?></label></p>
		<?php plxUtils::printInput('timeout', $timeout, 'text', '2-2'); ?>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="save" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>

