<?php

class plxAuth extends plxPlugin {
	public function __construct($default_lang) {
		parent::__construct($default_lang);

		$this->addHook('plxFormAuth', 'plxFormAuth');
		$this->addHook('plxMotorPreChauffageBegin', 'plxAuthLogout');
	}

	/* Formulaire d'authentification */
	public function plxFormAuth() {
		$this->plxMotor = plxMotor::getInstance();
		include_once(PLX_ROOT.'core/lib/class.plx.token.php');

		# Control du token du formulaire
		plxToken::validateFormToken($_POST);

		if($_SERVER['REQUEST_METHOD'] == 'POST') $_POST = plxUtils::unSlash($_POST);
		
		if(!empty($_POST['login']) AND !empty($_POST['password'])) {
			$connected = false;
			foreach($this->plxMotor->aUsers as $userid => $user) {
				if ($_POST['login'] == $user['login'] AND sha1($user['salt'].md5($_POST['password'])) == $user['password'] AND $user['active'] AND !$user['delete']) {					
					$_SESSION['user'] = $userid;
					$_SESSION['profil'] = $user['profil'];
					$_SESSION['hash'] = plxUtils::charAleatoire(10);
					$_SESSION['domain'] = getcwd().'/core/admin';
					$_SESSION['lang'] = $user['lang'];
					$connected = true;
				}
			}
		}			

		include(PLX_PLUGINS.'/plxAuth/form.auth.php');
	}
	
	public function plxAuthLogout() {
		if(plxUtils::getGets() AND preg_match('/^logout\/?/', plxUtils::getGets())) {
			$formtoken = $_SESSION['formtoken']; # sauvegarde du token du formulaire
			$_SESSION = array();
			session_destroy();
			session_start();
			$_SESSION['formtoken'] = $formtoken; # restauration du token du formulaire
			unset($formtoken);

			header('Location: index.php');
			exit;
		}		
	}
}
?>
