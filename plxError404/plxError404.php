<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
include(dirname(__FILE__).'/lib/class.plx.error404.php');

class plxError404 extends plxPlugin {

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
		
		$this->error404 = new error404();
		
		# ajout du hook
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('plxShowError404', 'plxShowError404');
	}

	public function onActivate() {	
		$plxMotor = plxMotor::getInstance();
		$style = $plxMotor->aConf['style'];
		if (!file_exists(PLX_ROOT.'themes/'.$style.'/erreur.php.bak')) {
			rename(PLX_ROOT.'themes/'.$style.'/erreur.php', PLX_ROOT.'themes/'.$style.'/erreur.php.bak');
			copy(PLX_PLUGINS.'plxError404/lib/erreur.php', PLX_ROOT.'themes/'.$style.'/erreur.php');
		}
	}
	
	public function onDeactivate() {	
		$plxMotor = plxMotor::getInstance();
		$style = $plxMotor->aConf['style'];
		if (file_exists(PLX_ROOT.'themes/'.$style.'/erreur.php.bak')) {
			unlink(PLX_ROOT.'themes/'.$style.'/erreur.php');
			rename(PLX_ROOT.'themes/'.$style.'/erreur.php.bak', PLX_ROOT.'themes/'.$style.'/erreur.php');
		}
	}
	
	/**
	 * Méthode qui ajoute le fichier css et js dans le fichier header.php du thème
	 **/
	public function ThemeEndHead() {
		echo "\n\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxError404/css/plxError404.css" media="screen" />'."\n";
	}	
	
	/**
	 * Méthode pour l'affichage la page d'erreur 404
	 *
	 * @author	DPFPIC
	 **/
	public function plxShowError404() {
	
		$plxMotor = plxMotor::getInstance();
		// informations concernant votre site
		$adminemail = plxPlugin::getParam('to'); //le mail du webmaster
		$website = rtrim(plxUtils::getRacine(), '/'); //l'url du site
		$websitename = $plxMotor->aConf['title']; // le nom du site
		$output_p = '';
		$output_bonux = '';	

		$output_p  = '<div id="error-404" class="error-center">';	
		if (plxPlugin::getParam('person') == 0) {
			$output_p .= '<h2 class="error-404">';
			$output_p .= '<span>Erreur</span>';
			$output_p .= '</h2>';
			$output_p .= '<h1 class="error-404">';
			$output_p .= '<span>404</span>';
			$output_p .= '</h1>';
		} else {
			$output_p .= str_replace('../../', plxUtils::getRacine(), plxPlugin::getParam('warningMsg'));
		}
	
		// on commence à composer notre futur paragraphe
		$output_p .= '<p>'.$this->getlang('L_E4_OUTPUT_1').' ';

		// si le visiteur a tapé l'adresse manuellement
		if (!isset($_SERVER['HTTP_REFERER'])) {
			$output_p .= $this->getlang('L_E4_OUTPUT_2').' ';
			$output_bonux = $this->getlang('L_E4_OUTPUT_3'); //optionnel

		// si le visiteur a cliqué sur un lien (referer)
		} elseif (isset($_SERVER['HTTP_REFERER'])) {
			$output_p .=  $this->getlang('L_E4_OUTPUT_4').' ';

			$failuremess = $this->getlang('L_E4_OUTPUT_5').' ('.$_SERVER ['REMOTE_ADDR'].') '.$this->getlang('L_E4_OUTPUT_5B').' '.$website.$_SERVER['REQUEST_URI'].' '.$this->getlang('L_E4_OUTPUT_6').'.'."\n";
			$failuremess .= "Il venait de l'url : ".$_SERVER['HTTP_REFERER'];
	
			if (plxPlugin::getParam('supervision') == 1) {	
				//si le mail est bien envoyé
				if(plxUtils::sendMail($websitename,$adminemail,$adminemail,$this->getlang('L_E4_OUTPUT_7').' ? : '.$website.$_SERVER['REQUEST_URI'],$failuremess,'text')) { 
					$output_bonux .= $this->getlang('L_E4_OUTPUT_8').'.';
			
				//s'il ne l'est pas
				} else { 
					$output_bonux .= $this->getlang('L_E4_OUTPUT_9').'<br />';
					$output_bonux .= $this->getlang('L_E4_OUTPUT_10').' :<br />';
					$output_bonux .= '<code>'.$this->getlang('L_E4_OUTPUT_11').' : '.$_SERVER['HTTP_REFERER'].'<br />';
					$output_bonux .= $this->getlang('L_E4_OUTPUT_12').' : '.$website.$_SERVER['REQUEST_URI'].'</code>';
				}
			}
		}

		// on termine notre paragraphe, 
		// il s'agit de la partie qui apparait de toute manière
		$output_p .= ': <br /><code class="url">'.$website.$_SERVER['REQUEST_URI'].'</code><br />'; 
		$output_p .= ' '.$this->getlang('L_E4_OUTPUT_13').'.<br />'; 
		$output_p .= $output_bonux.'<br />'; 
		$output_p .= $this->getlang('L_E4_OUTPUT_14').'.</p>'; 
		$output_p .= '<a href="javascript:history.go(-1)">'.$this->getlang('L_E4_OUTPUT_15').'</a> '.$this->getlang('L_E4_OUTPUT_16').' <a href="'.plxUtils::getRacine().'">'.$this->getlang('L_E4_OUTPUT_17').'</a>';
	    $output_p .= '</div>';
	
		// affichons le texte
		echo $output_p;
	}
}
?>