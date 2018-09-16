<?php
/**
 * Plugin plxSlimbox2
 *
 **/
class plxSlimbox2 extends plxPlugin {

	/**
	 * Constructeur de la classe plxSlimbox2
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

		# Déclarations des hooks
		$this->addHook('ThemeEndHead', 'addCSS');
		$this->addHook('ThemeEndBody', 'addJS');
	}

	/**
	 * Méthode qui ajoute le fichier css de slimbox2
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function addCSS() {
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxSlimbox2/slimbox2/css/slimbox2.css" />'."\n";
	}
	/**
	 * Méthode qui ajoute le fichier javascript de slimbox2
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function addJS() {

		echo "\n".'
		<script type="text/javascript">
		/* <![CDATA[ */
		!window.jQuery && document.write(\'<script  type="text/javascript" src="'.PLX_PLUGINS.'plxSlimbox2/jquery-1.8.2.min.js"><\/script>\');
		/* !]]> */
		</script>
		<script type="text/javascript" src="'.PLX_PLUGINS.'plxSlimbox2/slimbox2/js/slimbox2.js"></script>
		';
	}

}
?>