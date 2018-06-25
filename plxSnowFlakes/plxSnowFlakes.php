<?php
/**
 * Plugin plxSnowFlakes
 *
 * @version	1.0
 * @date	06/11/2011
 * @author	Stephane F
 **/
class plxSnowFlakes extends plxPlugin {

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

        # appel du constructeur de la classe plxPlugin (obligatoire)
        parent::__construct($default_lang);

		# déclaration des hooks
        $this->addHook('ThemeEndHead', 'ThemeEndHead');
    }

	/**
	 * Méthode qui charge le javascript dans la partie <head> du site
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function ThemeEndHead() {
		echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxSnowFlakes/snowflakes.js"></script>'."\n";
	}
}
?>