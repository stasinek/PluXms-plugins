<?php
/**
 * Plugin plxMyZoombox
 *
 **/
class plxMyZoombox extends plxPlugin {

	/**
	 * Constructeur de la classe plxMyZoombox
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Maguire Cyril
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accéder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		# Déclarations des hooks
		$this->addHook('ThemeEndHead', 'addZoomboxCss');
		$this->addHook('ThemeEndBody', 'addZoombox');
		$this->addHook('AdminTopEndHead', 'addZoomboxCss');
		$this->addHook('AdminTopEndBody', 'addZoombox');
	}

	/**
	 * Méthode qui initialise le fichier de paramètres à l'activation du plugin
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function onActivate() {
		if(!is_file($this->plug['parameters.xml'])) {
			$this->setParam('theme', 'zoombox', 'string');
			$this->setParam('opacity', '0.8', 'string');
			$this->setParam('duration', 800, 'numeric');
			$this->setParam('animation', 'true', 'string');
			$this->setParam('width', 1000, 'numeric');
			$this->setParam('height', 800, 'numeric');
			$this->setParam('gallery', 'true', 'string');
			$this->setParam('autoplay', 'false', 'string');
			$this->saveParams();
		}
	}

	/**
	 * Méthode qui ajoute le fichier css de Zoombox
	 *
	 * @return	stdio
	 * @author	Maguire Cyril
	 **/
	public function addZoomboxCss() {
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxMyZoombox/zoombox/zoombox.css" />'."\n";
	}
	/**
	 * Méthode qui ajoute le fichier javascript de Zoombox
	 *
	 * @return	stdio
	 * @author	Maguire Cyril
	 **/
	public function addZoombox() {
		echo "\n".'
		<script type="text/javascript">
		/* <![CDATA[ */
		!window.jQuery && document.write(\'<script  type="text/javascript" src="'.PLX_PLUGINS.'plxMyZoombox/zoombox/jquery-1.8.2.min.js"><\/script>\');
		/* !]]> */
		</script>
		<script type="text/javascript" src="'.PLX_PLUGINS.'plxMyZoombox/zoombox/zoombox.js"></script>
		<script type="text/javascript">
			jQuery(function($){
				$(\'a.zoombox\').zoombox();
				// You can also use specific options
				$(\'a.zoombox\').zoombox({
					theme		: \''.$this->getParam('theme').'\',	// available themes : zoombox, lightbox, prettyphoto, darkprettyphoto, simple
					opacity		: '.$this->getParam('opacity').',	// Black overlay opacity
					duration	: '.$this->getParam('duration').',	// Animation duration
					animation	: '.$this->getParam('animation').',	// Do we have to animate the box ?
					width		: '.$this->getParam('width').',		// Default width
					height		: '.$this->getParam('height').',	// Default height
					gallery		: '.$this->getParam('gallery').',	// Allow gallery thumb view
					autoplay	: '.$this->getParam('autoplay').'	// Autoplay for video
				})
		    })
		</script>';

	}

}
?>