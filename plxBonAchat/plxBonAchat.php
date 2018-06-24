<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
 
include(dirname(__FILE__).'/lib/class.plx.bonachat.php'); 

class plxBonAchat extends plxPlugin {

	private $url = ''; # parametre de l'url pour accèder à la page de bonachat
	public $lang = '';

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

		$this->url = $this->getParam('url')=='' ? 'bonachat' : $this->getParam('url');

		# Autorisation d'acces à la configuration du plugin
		$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);

		# Autorisation d'accès à l'administration du plugin
		$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);

		$this->bonachat = new bonachat();		

		# Personnalisation du menu admin
		$this->setAdminMenu($this->getlang('L_BA_DEFAULT_MENU_NAME'),'',$this->getlang('L_BA_DEFAULT_MENU_NAME'));
		
		# déclaration des hooks
		$this->addHook('AdminTopEndHead', 'AdminTopEndHead');		
		$this->addHook('AdminTopBottom', 'AdminTopBottom');
		$this->addHook('ThemeEndHead', 'ThemeEndHead');		

		# Si le fichier de langue existe on peut mettre en place la partie visiteur
			if(plxUtils::checkMail($this->getParam('baEmail'))) {
				$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
				$this->addHook('plxShowConstruct', 'plxShowConstruct');
				$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
				$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
				$this->addHook('SitemapStatics', 'SitemapStatics');
			}
	}

	/**
	 * Méthode qui ajoute le fichier css et js dans le fichier header.php du thème
	 **/
	public function ThemeEndHead() {
		echo "\n\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxBonAchat/css/bonachat.css" media="screen" />'."\n";
	}
	
	/**
	 * Méthode qui charge le code css nécessaire pour l'affichage admin
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/	
	public function AdminTopEndHead() {
		if(basename($_SERVER['SCRIPT_NAME'])=='plugin.php') {
			echo '<link href="'.PLX_PLUGINS.$this->plug['name'].'/css/admin.css" rel="stylesheet" type="text/css" />'."\n";
		}
		echo "\n\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxBonAchat/js/jscolor/jscolor.js"></script>';
	}
	
	/**
	 * Méthode qui affiche un message si l'adresse email du contact n'est pas renseignée
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminTopBottom() {

		echo '<?php
		if($plxAdmin->plxPlugins->aPlugins["plxBonAchat"]->getParam("baEmail")=="") {
			echo "<p class=\"warning\">Plugin BonAchat<br />'.$this->getLang("L_BA_ERR_EMAIL").'</p>";
			plxMsg::Display();
		}
		?>';
	}

	/**
	 * Méthode de traitement du hook plxShowConstruct
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowConstruct() {

		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='".$this->url."') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".addslashes($this->getParam('baName'))."',
			'menu'		=> '',
			'url'		=> 'bonachat',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		echo "<?php ".$string." ?>";
	}

	/**
	 * Méthode de traitement du hook plxMotorPreChauffageBegin
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxMotorPreChauffageBegin() {
	
		$template = $this->getParam('template')==''?'static.php':$this->getParam('template');

		$string = "
		if(\$this->get && preg_match('/^".$this->url."\/?/',\$this->get)) {
			\$this->mode = '".$this->url."';
			\$prefix = str_repeat('../', substr_count(trim(PLX_ROOT.\$this->aConf['racine_statiques'], '/'), '/'));
			\$this->cible = \$prefix.'plugins/plxBonAchat/form';
			\$this->template = '".$template."';
			return true;
		}
		";

		echo "<?php ".$string." ?>";
	}

	/**
	 * Méthode de traitement du hook plxShowStaticListEnd
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowStaticListEnd() {

		# ajout du menu pour accèder à la page de contact
		if($this->getParam('baDisplay')) {
			echo "<?php \$status = \$this->plxMotor->mode=='".$this->url."'?'active':'noactive'; ?>";
			echo "<?php array_splice(\$menus, ".($this->getParam('baPos')-1).", 0, '<li class=\"static menu '.\$status.'\" id=\"static-contact\"><a href=\"'.\$this->plxMotor->urlRewrite('?".$this->lang.$this->url."').'\" title=\"".addslashes($this->getParam('baName'))."\">".addslashes($this->getParam('baName'))."</a></li>'); ?>";
		}

	}

	/**
	 * Méthode qui rensigne le titre de la page dans la balise html <title>
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowPageTitle() {

		echo '<?php
			if($this->plxMotor->mode == "'.$this->url.'") {
				$this->plxMotor->plxPlugins->aPlugins["plxBonAchat"]->lang("L_BA_PG_TITLE");
				return true;
			}
		?>';
	}

	/**
	 * Méthode qui référence la page de contact dans le sitemap
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function SitemapStatics() {
		echo '<?php
		echo "\n";
		echo "\t<url>\n";
		echo "\t\t<loc>".$plxMotor->urlRewrite("?'.$this->lang.$this->url.'")."</loc>\n";
		echo "\t\t<changefreq>monthly</changefreq>\n";
		echo "\t\t<priority>0.8</priority>\n";
		echo "\t</url>\n";
		?>';
	}

}
?>
