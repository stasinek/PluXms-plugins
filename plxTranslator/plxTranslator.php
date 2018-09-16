<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
include(dirname(__FILE__).'/lib/class.plx.translator.php');

class plxTranslator extends plxPlugin {

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accéder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		
		$this->translator = new translator();
		
		# ajout du hook
		$this->addHook('plxShowTranslator', 'plxShowTranslator');
	}

	/**
	 * Méthode pour l'affichage de Google Translate
	 *
	 * @author	DPFPIC
	 **/
	public function plxShowTranslator() {
	
		$plxMotor = plxMotor::getInstance();
		$lang = $plxMotor->aConf['default_lang'];		
		$string = '<style type="text/css">.goog-te-combo {max-width:200px;}
		.goog-te-gadget {text-align:'.plxPlugin::getParam('alignInput').';}
		</style>
		<div id="translator">
			<div id="google_translate_element"></div>';
		$string .= '
			<script type="text/javascript">
			function googleTranslateElementInit() {
				new google.translate.TranslateElement({
				pageLanguage: "'.$lang.'"
				}, "google_translate_element");
			}
			</script>
			<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
		</div>';

		echo $string;
	}
}
?>