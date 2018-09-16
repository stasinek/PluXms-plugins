<?php
 /**
 * Plugin plxW3cssThemeSelect by nIQnutn
 * Update: 16 april 2018
 * Version: 1.0
 **/
class plxW3cssThemeSelect extends plxPlugin {
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
	}
}
?>
