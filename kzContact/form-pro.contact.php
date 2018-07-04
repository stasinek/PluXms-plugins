<?php
# Auteur Bazooka07
# 2014-02-12
if(!defined('PLX_ROOT')) exit;

define('ANTISPAM_REP', 'rep');

# le script s'execute à l'interieur d'une fonction de $plxShow, donc $this égal $plxShow
$plxPlugin = $this->plxMotor->plxPlugins->getInstance('kzContact');

$error=false;
$success=false;

if(!empty($_POST)) {
	$name=plxUtils::unSlash($_POST['name']);
	$mail=plxUtils::unSlash($_POST['mail']);
	$content=plxUtils::unSlash($_POST['content']);
	if(trim($name)=='')
		$error = $plxPlugin->getLang('L_ERR_NAME');
	elseif (!plxUtils::checkMail($mail))
		$error = $plxPlugin->getLang('L_ERR_EMAIL');
	elseif (trim($content)=='')
		$error = $plxPlugin->getLang('L_ERR_CONTENT');
	elseif (! $this->plxMotor->capchaOk($_POST[ANTISPAM_REP]))
		$error = $plxPlugin->getLang('L_ERR_ANTISPAM');
	if(!$error) {
		if (plxUtils::sendMail(
				$name, $mail, $plxPlugin->getParam('email'),
				$plxPlugin->getParam('subject'), $content, 'text',
				$plxPlugin->getParam('email_cc'), $plxPlugin->getParam('email_bcc')
				))
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

<!-- begin form.contact.php -->
			<div id="form_contact">
<?php if ($error) { ?>
				<p class="contact_error"><?php echo $error ?></p>
<?php } ?>
<?php if($success) { ?>
				<p class="contact_success"><?php echo str_replace('\n', '<br />',plxUtils::strCheck($success)); ?></p>
<?php } else { ?>
<?php if($plxPlugin->getParam('mnuText')) { ?>
				<div class="text_contact">
				<?php echo $plxPlugin->getParam('mnuText') ?>
				</div>
<?php } ?>
				<form method="post" onsubmit="return kzContact_onsubmit();">
					<fieldset>
						<h2>Contact professionnel</h2>
						<p class="one_line">
							<label for="name"><?php $plxPlugin->lang('L_FORM_NAME') ?></label>
							<input id="name" name="name" type="text" size="30" value="<?php echo plxUtils::strCheck($name) ?>" maxlength="30" />
						</p>
						<p class="one_line">
							<label for="mail"><?php $plxPlugin->lang('L_FORM_MAIL') ?></label>
							<input id="mail" name="mail" type="text" size="30" value="<?php echo plxUtils::strCheck($mail) ?>" />
						</p>
						<label for="message"><?php $plxPlugin->lang('L_FORM_CONTENT') ?></label>
						<textarea id="message" name="content" cols="60" rows="12"><?php echo plxUtils::strCheck($content) ?></textarea>
<?php if($this->plxMotor->aConf['capcha']) { ?>
						<div id="captcha-bloc">
							<!-- label for="id_rep"><?php echo L_ANTISPAM_WARNING; ?></label -->
							<?php $this->capchaQ(); ?><br />
							<input id="id_rep" name="<?php echo ANTISPAM_REP; ?>" type="text" size="6" maxlength="6" />
						</div>
<?php } ?>
						<div>
							<input type="submit" name="submit" />
							<input type="reset" name="reset" />
						</div>
						<hr style="clear: both; height: 0; visibility: hidden;"/>
					</fieldset>
				</form>
<?php } ?>
			</div>
<!-- end form.contact.php -->