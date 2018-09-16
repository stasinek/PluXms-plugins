<?php
/**
 *
 * Plugin	plxProtectEmails
 * @author	Stephane F
 *
 **/

class plxProtectEmails extends plxPlugin {

	/**
	 * Constructeur de la classe plxProtectEmails
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @authors	Stephane F - Francis
	 **/
	public function __construct($default_lang) {
		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		# si affichage des articles coté visiteurs: protection des emails contre le spam
		if(!defined('PLX_ADMIN')) {
			$this->addHook('plxMotorParseArticle', 'protectEmailsArticles');
			$this->addHook('plxShowStaticContent', 'protectEmailsStatics');
			$this->addHook('plxShowStaticInclude', 'plxShowEmailsStaticInclude');
		}
	}

	/**
	 * Méthode qui encode une chaine de caractères en hexadécimal
	 *
	 * @parm	s		chaine de caractères à encoder
	 * @return	string	chaine de caractères encodée en hexadécimal
	 * @author	Stephane F
	 **/
	public static function encodeBin2Hex($s) {
		$encode = '';
		for ($i = 0; $i < strlen($s); $i++) {
			$encode .= '%' . bin2hex($s[$i]);
		}
		return $encode;
	}

	/**
	 * Méthode qui protège les adresses emails contre le spam
	 *
	 * @parm	txt		chaine de caractères à protéger
	 * @return	string	chaine de caractères avec les adresses emails protégées
	 * @author	Stephane F, Francis
	 **/
	public static function protectEmails($txt) {

		if(preg_match_all('/<a.+href=[\'"]mailto:([\._a-zA-Z0-9-@]+)((\?.*)?)[\'"][^>]*>([\._a-zA-Z0-9-@]+)<\/a>/i', $txt, $matches)) {
			foreach($matches[0] as $k => $v) {
				$string = plxProtectEmails::encodeBin2Hex('document.write(\''.$matches[0][$k].'\')');
				$txt = str_replace($matches[0][$k], '<script>eval(unescape(\''.$string.'\'))</script>' , $txt);
			}
		}
		$s = preg_replace('/<input(\s+[^>]*)?>/i', '', $txt);
		$s = preg_replace('/<textarea(\s+[^>]*)?>.*?<\/textarea(\s+[^>]*)?>/i', '', $s);
		$s = preg_replace('/<(code|pre)(\b.*)<\/(code|pre)>/is', '', $s);
		if(preg_match_all('/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i', $s, $matches)) {
			foreach($matches[0] as $k => $v) {
				$string = plxProtectEmails::encodeBin2Hex('document.write(\''.$matches[0][$k].'\')');
				$txt = str_replace($matches[0][$k], '<script>eval(unescape(\''.$string.'\'))</script>' , $txt);
			}
		}
		return $txt;
	}

	/**
	 * Méthode qui protège les adresses emails contre le spam dans les articles
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function protectEmailsArticles() {
		echo '<?php
			$art["chapo"] = plxProtectEmails::protectEmails($art["chapo"]);
			$art["content"] = plxProtectEmails::protectEmails($art["content"]);
		?>';
	}

	/**
	 * Méthode qui protège les adresses emails contre le spam dans les pages statiques
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function protectEmailsStatics() {
		echo '<?php
			$output = plxProtectEmails::protectEmails($output);
		?>';
	}

	/**
	 * Méthode qui protège les adresses emails à partir de la méthode plxShow::staticInclude()
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowEmailsStaticInclude() {
		echo '<?php
			$plxGlob_stats = plxGlob::getInstance(PLX_ROOT.$this->plxMotor->aConf["racine_statiques"]);
			if($files = $plxGlob_stats->query("/^".str_pad($id,3,"0",STR_PAD_LEFT).".[a-z0-9-]+.php$/")) {
				ob_start();
				include(PLX_ROOT.$this->plxMotor->aConf["racine_statiques"].$files[0]);
				echo plxProtectEmails::protectEmails(ob_get_clean());
			}
			return true;
		?>';
	}

}
?>
