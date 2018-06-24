<?php
/**
 * Classe plxContentBackup
 *
 * @version 1.1
 * @date	23/02/2010
 * @update 	03/09/2016 Pluxml 5.5
 * @author	François POTEAU
 **/
class plxContentBackup extends plxPlugin {
	
	
	/**
	 * Constructeur de la classe
	 *
	 * @return	null
	 * @author	François POTEAU  
	 **/	
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->setAdminProfil(PROFIL_ADMIN);
		
		# Ajouts des hooks
		$this->addHook('AdminIndexTop', 'AdminIndexTop');
		$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
		#$this->addHook('AdminTopMenus', 'AdminTopMenus');
	}

	/**
	 * Méthode pour le hook AdminIndexTop
	 *
	 * Vérifie la dernière sauvegarde et procède au backup si nécessaire.
	 *
	 * @return	void
	 * @author	François POTEAU 
	 **/
	public function AdminIndexTop() {
		if(!class_exists('zip')) {
			require_once('class/class.zip.php');
		}
		if(!class_exists('PHPMailerLite')) {
			require_once('class/class.phpmailer-lite.php');
		}
		require_once('class/class.archive.php');
		
		$archive = new archive($this->getParam('savedir'),$this->getParam('days'),$this->getParam('saved_dirs'));
		if($archive->check()) {
			$archive->zip();
			$archive->sendmail(
				$this->getParam('email'),
				$this->getParam('senderemail'),
				$this->getParam('sendername'),
				$this->getLang('L_TITLE'),
				$this->getLang('L_CONTENT')
			);
			echo '<p class="msg">'.$this->getLang('L_SUCCESS').' '.$this->getParam('email').'</p>';
		}
	}
	/**
	 * Méthode pour le hook AdminTopEndHead
	 *
	 * Ajout de la feuille de style (mise en forme de l'administration du plugin)
	 *
	 * @return	void
	 * @author	François POTEAU 
	 
	public function AdminTopEndHead() {
		echo '<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxContentBackup/styles.css" media="screen" />';
	}
	**/

}
?>