<?php

if (!defined('PLX_ROOT')) exit;

/*
 * Plugin pour récupérer mot de passe
 * */

// some id(s) elements in the form to play with
define('ID1', 'lostpassword-mail');
define('ID2', 'lostpassword-flag');
define('ID_EMAIL', 'email');
define('ID_PASSWORD', 'password'); // look for value in core/admin/auth.php script

class lostPassword extends plxPlugin {

	private $proceed;
	private $my_msg;
	private $to;

	public function __construct($default_lang) {
		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accéder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		$this->proceed = false;

		// called in core/admin/auth.php
		$this->addHook('AdminAuthPrepend', 'AdminAuthPrepend');
		$this->addHook('AdminAuthEndHead', 'AdminAuthEndHead');
		$this->addHook('AdminAuthTop', 'AdminAuthTop');
		$this->addHook('AdminAuth', 'AdminAuth');

		// called in core/admin/top.php
		// $this->addHook('AdminTopEndHead', 'AdminEndHead');
	}

	private function pluginRoot() {
		global $plxAdmin;
		
		return $plxAdmin->racine.$plxAdmin->aConf['racine_plugins'].__CLASS__.'/';
	}

	// Proceed $_POST array
	// send new password by email if necessary
	public function AdminAuthPrepend() {
		// don't send any output before html header
		global $plxAdmin;

		if (! empty($_POST[ID2])) {
			$this->proceed = true;
			$this->my_msg = $this->aLang['L_PASSWD_BAD_LOGIN_MAIL'];
			if(!empty($_POST['login']) and !empty($_POST[ID_EMAIL])) {
				foreach($plxAdmin->aUsers as $userid => $user) {
					if ($_POST['login']==$user['login'] AND $_POST[ID_EMAIL]==$user['email'] AND $user['active'] AND !$user['delete']) {
						$this->my_msg = $this->aLang['L_PASSWD_SENT_OK'];
						if (plxUtils::testMail(false)) {
							// create new password
							$new_password = plxUtils::charAleatoire();
							// send email
							$name = $this->getParam('name');
							$from = $this->getParam('from');
							$to = $user['email'];
							$subject = str_replace('#TITLE', $plxAdmin->aConf['title'], $this->getParam('subject'));
							$body = str_replace(
								array('#LOGIN', '#PASSWORD', '#SITE', '#IP'),
								array($_POST['login'], $new_password, $plxAdmin->racine, $_SERVER['REMOTE_ADDR']),
								$this->getParam('body'));
							$cc = false;
							$bcc = $this->getParam('bcc');
							if (false) { # for debugging
								$fp = fopen('../../data/'.__CLASS__.'.log', 'a+');
								fwrite($fp, "Date: ".date('c')."\n");
								fwrite($fp, "Name: $name\n");
								fwrite($fp, "From: $from\n");
								fwrite($fp, "To: $to\n");
								fwrite($fp, "Subject: $subject\n");
								fwrite($fp, "Body:\n $body\n");
								fwrite($fp, "Cc: $cc\n");
								fwrite($fp, "Bcc: $bcc\n");
								fwrite($fp, "---- EOT ----\n");
								fclose($fp);
							}
							// new password is sending by E-mail
							// attention : LWS en mode hébergeur mutualisé n'accepte pas les chaamps $cc et $bcc
							if (plxUtils::sendMail($name, $from, $to, $subject, $body, 'text', $cc, $bcc)) {
								// store $new_password
								$salt = $user['salt'];
								$plxAdmin->aUsers[$userid]['password'] = sha1($salt.md5($new_password));
								$result = $plxAdmin->editUsers(null, true);
							}
							else
								$this->my_msg = $this->aLang['L_PASSWD_SENT_ABORTED'];
						} else {
							$this->my_msg = $this->aLang['L_MAIL_NOT_AVAILABLE'];
						}
						break;
					}
				}
			}
		}
	}

	// Add styleSheet for core/admin/parameter_plugin.php script
	public function AdminAuthEndHead() {
		global $plxAdmin;
		
		$plugin_path = '../../'.$plxAdmin->aConf['racine_plugins'];
		$filename =  $this->pluginRoot().__CLASS__; # without extension ?>
<!-- AdminAuthEndHead and AdminTopEndHead Hooks for <?php echo(__CLASS__); ?> -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $filename; ?>.css" />
		<script type="text/javascript" src="<?php echo $filename; ?>.js" ></script>
<?php
	}

	// Update message to display if needed
	public function AdminAuthTop() { ?>
<!-- AdminAuthTop Hook for <?php echo(__CLASS__); ?> -->
<?php
		global $msg, $error;

		if ($this->proceed) {
			$msg = $this->my_msg;
			$error = 'error';
		}
	}

	//  Update the form in core/admin/auth.php script
	// Display capcha if $plxAdmin->plxCapcha not null
	public function AdminAuth() {
		global $plxAdmin;

		$value = (empty($_POST[ID_EMAIL])) ? '' : $_POST[ID_EMAIL]; ?>
<!-- AdminAuth Hook for <?php echo(__CLASS__); ?> -->
			<div id="lostpassword">
				<p>
					<a href="#" onclick="lostpassword_onclick('<?php echo ID1."', '".ID2."', 'id_".ID_EMAIL."', 'id_".ID_PASSWORD."', '".$this->aLang['L_PASSWD_ERROR']; ?>');">
						<?php echo($this->aLang['L_PASSWD_LOST']); ?> ?
					</a>
				</p>
				<input type="hidden" id="<?php echo ID2; ?>" name="<?php echo ID2; ?>" value="" />
				<div id="<?php echo(ID1); ?>">
					<label for="id_email"><?php echo L_USER_MAIL ?>&nbsp;:</label>
					<?php plxutils::printinput(ID_EMAIL, $value, 'text', '30-255') ?>
				</div>
			</div>
<?php
	}
}
?>
