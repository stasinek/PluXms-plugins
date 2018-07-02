<?php
/**
 * Plugin zoombox
 *
 * @package	PLX
 * @version	1.0
 * @date	18/07/2011
 * @author	Stephane F.
 * @author	Maguire Cyril
 **/
class zoombox extends plxPlugin {

	/**
	 * Constructeur de la classe zoombox
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Maguire Cyril
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# Déclarations des hooks
		$this->addHook('ThemeEndHead', 'addZoomboxCss');
		$this->addHook('ThemeEndBody', 'addZoombox');
		$this->addHook('AdminTopEndHead', 'addZoomboxCss');		
		$this->addHook('AdminTopEndBody', 'addZoombox');		
	}
	
	/**
	 * Méthode qui ajoute le fichier css de Zoombox
	 *
	 * @return	stdio
	 * @author	Maguire Cyril
	 **/	
	public function addZoomboxCss() {
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'zoombox/zoombox.css" />'."\n";
	}
	/**
	 * Méthode qui ajoute le fichier javascript de Zoombox
	 *
	 * @return	stdio
	 * @author	Maguire Cyril
	 **/	
	public function addZoombox() {
		echo "\t".'<script type="text/javascript">
				/* <![CDATA[ */
				!window.jQuery && document.write(\'<script  type="text/javascript" src="'.PLX_PLUGINS.'zoombox/jquery-1.7.1.min.js"><\/script>\');
				/* !]]> */
			</script>'."\n";
		echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'zoombox/zoombox.js"></script>'."\n";
		echo "\t".'<script type="text/javascript" >'."\n";
		    echo "\t\t".'jQuery(function($){'."\n";
		    echo "\t\t\t".'$(\'a.zoombox\').zoombox();'."\n";
		    echo "\t\t\t".'// You can also use specific options'."\n";
		    echo "\t\t\t".'$(\'a.zoombox\').zoombox({'."\n";
		    echo "\t\t\t\t".'theme       : \'darkprettyphoto\',        //available themes : zoombox, lightbox, prettyphoto, darkprettyphoto, simple'."\n";
		    echo "\t\t\t\t".'opacity     : 0.8,              // Black overlay opacity'."\n";
		    echo "\t\t\t\t".'duration    : 800,              // Animation duration'."\n";
		    echo "\t\t\t\t".'animation   : true,             // Do we have to animate the box ?'."\n";
		    echo "\t\t\t\t".'width       : 1000,              // Default width'."\n";
		    echo "\t\t\t\t".'height      : 600,              // Default height'."\n";
		    echo "\t\t\t\t".'gallery     : true,             // Allow gallery thumb view'."\n";
		    echo "\t\t\t\t".'autoplay : false                // Autoplay for video'."\n";
		    echo "\t\t\t".'});'."\n";
		    echo "\t\t".'});'."\n";
		    echo "\t".'</script>'."\n";
	}

}
?>