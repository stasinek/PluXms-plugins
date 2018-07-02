<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# récuperation d'une instance de plxShow
$plxShow = plxShow::getInstance();
$plxShow->plxMotor->plxCapcha = new plxCapcha();
$plxPlugin = $plxShow->plxMotor->plxPlugins->getInstance('plxContact');

$error=false;
$success=false;

$captcha = $plxPlugin->getParam('captcha')=='' ? '1' : $plxPlugin->getParam('captcha');

if(!empty($_POST)) {
	$name=plxUtils::unSlash($_POST['name']);
	$mail=plxUtils::unSlash($_POST['mail']);
	$content=plxUtils::unSlash($_POST['content']);
	if(trim($name)=='')
		$error = $plxPlugin->getLang('L_ERR_NAME');
	elseif(!plxUtils::checkMail($mail))
		$error = $plxPlugin->getLang('L_ERR_EMAIL');
	elseif(trim($content)=='')
		$error = $plxPlugin->getLang('L_ERR_CONTENT');
	elseif($captcha AND $_POST['rep2'] != sha1($_POST['rep']))
		$error = $plxPlugin->getLang('L_ERR_ANTISPAM');
	if(!$error) {
		if(plxUtils::sendMail($name,$mail,$plxPlugin->getParam('email'),$plxPlugin->getParam('subject'),$content,'text',$plxPlugin->getParam('email_cc'),$plxPlugin->getParam('email_bcc')))
			$success = $plxPlugin->getParam('thankyou');
		else
			$error = $plxPlugin->getLang('L_ERR_SENDMAIL');
	}
} else {
	$name='';
	$mail='';
	$content='';
}

?>

<div id="form_contact">
	<?php if($error): ?>
	<p class="contact_error"><?php echo $error ?></p>
	<?php endif; ?>
	<?php if($success): ?>
	<p class="contact_success"><?php echo plxUtils::strCheck($success) ?></p>
	<?php else: ?>
	<?php if($plxPlugin->getParam('mnuText')): ?>
	<div class="text_contact">
	<?php echo $plxPlugin->getParam('mnuText') ?>
	</div>
	<?php endif; ?>
	<form action="#form" method="post">
		<fieldset>
		<p><label for="name"><?php $plxPlugin->lang('L_FORM_NAME') ?>&nbsp;:</label>
		<input id="name" name="name" type="text" size="30" value="<?php echo plxUtils::strCheck($name) ?>" maxlength="30"/></p>
		<p><label for="mail"><?php $plxPlugin->lang('L_FORM_MAIL') ?>&nbsp;:</label>
		<input id="mail" name="mail" type="text" size="30" value="<?php echo plxUtils::strCheck($mail) ?>"/></p>
		<p><label for="message"><?php $plxPlugin->lang('L_FORM_CONTENT') ?>&nbsp;:</label></p>
		<textarea id="message" name="content" cols="80" rows="12"><?php echo plxUtils::strCheck($content) ?></textarea>
		<?php if($captcha): ?>
		<p><label for="rep"><strong><?php $plxPlugin->lang('L_FORM_ANTISPAM') ?></strong>&nbsp;:</label></p>
		<p><?php echo $plxShow->capchaQ() ?>&nbsp;:&nbsp;<input id="rep" name="rep" type="text" size="10"/>
		<input name="rep2" type="hidden" value="<?php echo $plxShow->capchaR() ?>" /></p>
		<?php endif; ?>
		<p>
		<input type="reset" name="reset" value="<?php $plxPlugin->lang('L_FORM_BTN_RESET') ?>" />
		<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_BTN_SEND') ?>" />
		</p>
		</fieldset>
	</form>
	<?php endif; ?>
</div>
