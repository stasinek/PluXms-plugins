<?php

 /**
 * Plugin plxFontAwesome by nIQnutn
 * Update: 08 december 2017
 * Version: 0.5
 **/


class plxFontAwesome extends plxPlugin {

	/**
	 * Constructeur de la classe plxFontAwesome
	 *
	 * @author	 nIQnutn
	 **/
	 
	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# limite l'accès à l'écran d'administration du plugin
		$this-> setConfigProfil(PROFIL_ADMIN);	
		# limite l'accès à l'écran d'administration du plugin
		$this->setAdminProfil(PROFIL_WRITER,PROFIL_MANAGER,PROFIL_MODERATOR,PROFIL_EDITOR,PROFIL_ADMIN);	

		# déclaration des hooks
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('AdminTopEndHead', 'ThemeEndHead');
		

	}

	public function ThemeEndHead() {
		echo '<link rel="stylesheet" href="'.PLX_PLUGINS.'plxFontAwesome/fontawesome-free-5.0.0/css/fontawesome-all.css">';
	}
}
?>
