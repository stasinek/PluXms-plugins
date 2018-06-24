<h3>Mon compte</h3>
<?php if(empty($_SESSION['user'])) {
	if($this->plxAdmin->aConf['urlrewriting']) {
		$url = plxUtils::getGets();
	} else {
		$url = '?'.plxUtils::getGets();
	}
	plxUtils::cleanHeaders();
?>

<form action="<?php echo $this->plxMotor->urlRewrite($url); ?>" method="post" id="form_auth">
	<fieldset>
		<?php (!empty($msg))?plxUtils::showMsg($msg, $error):''; ?>
		<label for="id_login"><?php echo $this->lang('L_AUTH_LOGIN_FIELD') ?></label>
		<?php plxUtils::printInput('login', (!empty($_POST['login']))?plxUtils::strCheck($_POST['login']):'', 'text', '18-255'); ?>

		<label for="id_password"><?php echo $this->lang('L_AUTH_PASSWORD_FIELD') ?></label>
		<?php plxUtils::printInput('password', '', 'password','18-255'); ?>

		<?php echo plxToken::getTokenPostMethod() ?>
		<p><input class="button submit" type="submit" value="<?php echo $this->lang('L_SUBMIT_BUTTON') ?>" /></p>
	</fieldset>
</form>
<?php } else { ?>
<ul>
	<li><a href="<?php echo $this->plxMotor->urlRewrite('core/admin/profil.php') ?>">Mon profil</a></li>
	<li><a href="<?php echo $this->plxMotor->urlRewrite('index.php?logout') ?>">Déconnexion</a></li>
</ul>
<?php } ?>
