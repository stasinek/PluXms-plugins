<?php
if(!defined('PLX_ROOT')) {
	header('Content-Type: text/plain; charset=utf-8');
	readfile('hymne-a-la-beaute.txt');
	exit;
}

/**
 * Plugin myContact
 * @author: Bazooka07
 * from plxMyContact written by	Stephane F
 * @lastdate: 2018-02-25
 * */
class kzContact extends plxPlugin {

	const PREFIX_CONTACT = 'form';

	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accéder à la page config.php du plugin
		self::setConfigProfil(PROFIL_ADMIN);

		# déclaration des hooks
		if(function_exists('mail')) {
			self::addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
			self::addHook('plxMotorDemarrageBegin', 'plxMotorDemarrageBegin');
			self::addHook('plxShowConstruct', 'plxShowConstruct');
			self::addHook('plxShowPageTitle', 'plxShowPageTitle');
			self::addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
			self::addHook('SitemapStatics', 'SitemapStatics');
		}

	}

	/**
	 * Envoie par courriel le POST du formulaire de contact avec
	 * les renseignements sur l'expéditeur fournis par le serveur.
	 * */
	public function sendMessage($inputs) {

		$error=false;
		$capcha = $_SESSION['capcha'];
		if (!empty(self::getParam('capcha')) and (empty($capcha) or ($capcha != sha1($inputs['rep'])))) {
			# Erreur de vérification capcha
			return self::getLang('L_ERR_ANTISPAM');
		} elseif(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$error = parent::getLang('L_ERR_CONTEXT');
		} else {
			# envoi des courriels
			$body = '';
			foreach($inputs as $field=>$value) {
				$caption = strtoupper(parent::getLang('L_'.strtoupper($field))). ':';
				$caption .= (preg_match('@(?:message|subject)$@', $field)) ? "\n" : ' ';
				$body .= $caption.$value."\n";
			}
			$body .= str_repeat('-', 40)."\n";
			foreach(explode(' ', 'HTTP_REFERER REMOTE_ADDR REMOTE_HOST HTTP_USER_AGENT HTTP_ACCEPT_LANGUAGE HTTP_ACCEPT HTTP_ACCEPT_ENCODING') as $field) {
				if(!empty($_SERVER[$field])) {
					$caption = ucfirst(str_replace('_', '-', str_replace('HTTP_', '', $field)));
					$value = filter_input(INPUT_SERVER, $fullname, FILTER_SANITIZE_STRING);
					$body .= "$caption: $value\n";
				}
			}
			if (!empty(self::getParam('envoiSepare'))) {
				$recipients = array_merge(
					explode(',', self::getParam('email')),
					explode(',', self::getParam('email_cc'))
				);
				if (count($recipients) > 1)
					$body .= "\n\n".self::getLang('L_SENT_TO').":\n".implode(', ', $recipients);
				$succes = false;
				foreach(array_merge($recipients, explode(',', self::getParam('email_cc'))) as $to) {
					if (plxUtils::sendMail(
							$name, $mail, trim($to),
							self::getParam('subject'), $body
							)) {
						$success = true;
					}
				}
				if(empty($success))
					return self::getLang('L_ERR_SENDMAIL');
			} else {
				if (!plxUtils::sendMail(
						$name, $mail, self::getParam('email'),
						self::getParam('subject'), $body, 'text',
						self::getParam('email_cc'), self::getParam('email_bcc')
					))
					return self::getLang('L_ERR_SENDMAIL');
			}
		}
		return false; # Pas d'erreur
	}

	/* ==================== Hooks ====================== */

	public function plxMotorPreChauffageBegin() {
		# Le traitement ne s'effectuera que pour l'affichage du formulaire de contact
		$code = <<< 'CODE'
<?php
if(!empty($this->get) and $this->get == '##FORM##') {
	$this->mode = '##MODE##';
	$this->cible = str_repeat('../', substr_count($this->aConf['racine_statiques'], '/')).$this->aConf['racine_plugins'].'##PREFIX##';
	$this->template = '##TEMPLATE##';
	return true;
}
?>
CODE;
		$replaces = array(
			'##FORM##'		=> self::getParam('content'),
			'##MODE##'		=> self::getParam('content'),
			'##PREFIX##'	=> __CLASS__.'/'.self::PREFIX_CONTACT,
			'##TEMPLATE##'	=> self::getParam('template')=='' ? 'static.php' : self::getParam('template')
		);
		echo str_replace(array_keys($replaces), array_values($replaces), $code);
	}

	public function plxMotorDemarrageBegin() {
		$code = <<< 'CODE'
<?php
if($this->mode == '##MODE##') { return true; }
?>
CODE;
		echo str_replace('##MODE##', self::getParam('content'), $code);
	}

	// infos sur la page statique
	public function plxShowConstruct() {

		# dans plxShow::staticContent()
		# $file = PLX_ROOT.$this->plxMotor->aConf['racine_statiques'].$this->plxMotor->cible;
		# $file .= '.'.$this->plxMotor->aStats[ $this->plxMotor->cible ]['url'].'.php';
		# Le traitement ne s'effectuera que pour l'affichage du formulaire de contact
		$code  = <<< 'CODE'
<?php
if($this->plxMotor->mode=='##FORM##') {
	$statique = array(
		$this->plxMotor->cible=>array(
			'name'		=> '##NAME##',
			'url'		=> '##FORM##',
			'active'	=> 1,
			'menu'		=> '',
			'readable'	=> 1
		)
	);
	$this->plxMotor->aStats = array_merge($this->plxMotor->aStats, $statique);
}
?>
CODE;
		# infos sur la page statique
		$replaces = array(
			'##NAME##'	=> addslashes(self::getParam('mnuName')),
			'##FORM##'	=> self::getParam('content')
		);
		echo str_replace(array_keys($replaces), array_values($replaces), $code);
	}

	/**
	 * Affiche une option pour le formulaire de contact dans le menu de navigation.
	 * */
	public function plxShowStaticListEnd() {

		if(!empty(self::getParam('mnuDisplay'))) {
			# ajout au menu pour accéder à la page de contact
			$code = <<< 'CODE'
<?php
	$href = $this->plxMotor->urlRewrite('?##FORM##');
	$class = ($this->plxMotor->mode=='##FORM##') ? 'active':'noactive';
	$pattern = <<< PATTERN
<li><a href="$href" class="$class">##CAPTION##</a></li>
PATTERN;
#	array_splice($menus, '##ENTRY##', POSITION, 0, $pattern);
	array_splice($menus, POSITION, 0, $pattern);
?>
CODE;
			$replaces = array(
				'##FORM##'		=> self::getParam('content'),
				'POSITION'		=> intval(self::getParam('mnuPos')) - 1,
				'##GROUP##'		=> self::getParam('mnuGroup'),
				'##ENTRY##'		=> (!empty($group)) ? "['$group']" : '',
				'##CAPTION##'	=> addslashes(self::getParam('mnuName'))
			);
			# echo str_replace(array_keys($replaces), array_values($replaces), $code);
			$code = str_replace(array_keys($replaces), array_values($replaces), $code);
			echo $code;
		}
	}

	/**
	 * Affiche le titre de la page pour le formulaire de contact.
	 * */
	public function plxShowPageTitle() {
		$code = <<< 'CODE'
<?php
if($this->plxMotor->mode == '##FORM##') {
	echo '##TITLE##'.plxUtils::strCheck($this->plxMotor->aConf['title']);
	return true;
}
?>
CODE;
		$title = self::getParam('title_htmltag');
		if (empty($title)) { $title = self::getParam('mnuName'); }
		if (!empty($title)) { $title .= ' - '; }
		$replaces = array(
			'##FORM##'	=> self::getParam('content'),
			'##TITLE##'	=> $title
		);
		echo str_replace(array_keys($replaces), array_values($replaces), $code);
	}

	public function SitemapStatics() {
		if(!empty(self::getParam('sitemap'))) {
			global $plxMotor;
			$location = $plxMotor->urlRewrite('?'.self::getParam('content'));
?>
		<url>
			<loc><?php echo $location; ?></loc>
			<changefreq>monthly</changefreq>
			<priority>0.8</priority>
		</url>
<?php
		}
	}

}
?>