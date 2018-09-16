<?php
class plxPiwik extends plxPlugin {
	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accéder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);		
		
		# déclaration du hook
		$this->addHook('ThemeEndBody', 'ThemeEndBody');
	}
	
	public function ThemeEndBody(){
		echo $this->getParam('trackcode');
	}
	
}
?>
