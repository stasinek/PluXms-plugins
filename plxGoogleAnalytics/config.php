<?php if(!defined('PLX_ROOT')) exit; ?>

<?php
# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	$plxPlugin->setParam('accountId', $_POST['accountId'], 'string');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxGoogleAnalytics');
	exit;
}
?>
<style>
form.inline-form label {
	width: 250px;
}
</style>
<form class="inline-form" action="parametres_plugin.php?p=plxGoogleAnalytics" method="post">
	<fieldset>
		<p>
			<label for="accountId"><?php $plxPlugin->lang('L_ACCOUNT_ID'); ?> : </label>
			<input type="text" name="accountId" value="<?php echo plxUtils::strCheck($plxPlugin->getParam('accountId')) ?>" />
			&nbsp;ex:&nbsp;UA-12345678-1
		</p>
		<p class="in-action-bar">   
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE'); ?>" />
		</p>
	</fieldset>
</form>
