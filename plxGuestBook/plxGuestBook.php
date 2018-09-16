<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
 
include(dirname(__FILE__).'/lib/class.plx.guestbook.php'); 
 
class plxGuestBook extends plxPlugin {

	/**
	 * Constructeur de la classe GuestBook
	 *
	 * @param	default_lang	langue par d�faut utilis�e par PluXml
	 * @return	null
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# Autorisation d'acces � la configuration du plugin
		$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);

		# Autorisation d'acc�s � l'administration du plugin
		$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);

		$this->guestbook = new guestbook();		

		# Personnalisation du menu admin
		$this->setAdminMenu($this->getlang('L_GB_DEFAULT_MENU_NAME').$this->guestbook->notificationgb(),'',$this->getlang('L_GB_DEFAULT_MENU_NAME'));
		#$this->setAdminMenu($this->getlang('L_GB_DEFAULT_MENU_NAME'),'',$this->getlang('L_GB_DEFAULT_MENU_NAME'));
		
		# d�claration des hooks
		$this->addHook('AdminTopBottom', 'AdminTopBottom');
		if(plxUtils::checkMail($this->getParam('email'))) {
			$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
			$this->addHook('plxShowConstruct', 'plxShowConstruct');
			$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
			$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
			$this->addHook('ThemeEndHead', 'ThemeEndHead');
			$this->addHook('SitemapStatics', 'SitemapStatics');
		}
	}

	/**
	 * M�thode de traitement du hook plxShowConstruct
	 **/
	public function plxShowConstruct() {
		#'name'		=> '".$this->getParam('mnuName')."',
	
		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='guestbook') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuName')."',
			'menu'		=> '',
			'url'		=> 'guestbook',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		echo "<?php ".$string." ?>";
	}
	
	/**
	 * M�thode appel�e � l'activation du plugin pour cr�er le r�pertoire gbook
	 *
	 * @author	Stephane F
	 **/
	public function onActivate() {
		# V�rification de l'existence du dossier /plugins/gbook
		if(!is_dir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/gbook')) {
			@mkdir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/gbook',0755,true);

			# Protection du r�pertoire gbook
			plxUtils::write('', PLX_ROOT.PLX_CONFIG_PATH.'plugins/gbook/index.html');
			if (!file_exists(PLX_ROOT.PLX_CONFIG_PATH.'plugins/gbook/.htaccess')){
				file_put_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/gbook/.htaccess',"<Files *>
	Order allow,deny
	Deny from all
</Files>      
      ");
    }			
		}
	}	
	/**
	 * M�thode de traitement du hook plxMotorPreChauffageBegin
	 **/
	public function plxMotorPreChauffageBegin() {

		$template = $this->getParam('template')==''?'static.php':$this->getParam('template');

		$string = "
		if(\$this->get && preg_match('/^guestbook\/?/',\$this->get)) {
			\$this->mode = 'guestbook';
			\$this->cible = '../.".PLX_PLUGINS."plxGuestBook/form';
			\$this->template = '".$template."';
			return true;
		}
		";

		echo "<?php ".$string." ?>";
	}	

	/**
	 * M�thode de traitement du hook plxShowStaticListEnd
	 **/
	public function plxShowStaticListEnd() {

		# ajout du menu pour acc�der � la page de guestbook
		if($this->getParam('mnuDisplay')) {
			echo "<?php \$class = \$this->plxMotor->mode=='guestbook'?'active':'noactive'; ?>";
			echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, '<li><a class=\"static '.\$class.'\" href=\"'.\$this->plxMotor->urlRewrite('?guestbook').'\" title=\"".$this->getParam('mnuName')."\">".$this->getParam('mnuName')."</a></li>'); ?>";
		}

	}

	/**
	 * M�thode qui ajoute le fichier css et js dans le fichier header.php du th�me
	 **/
	public function ThemeEndHead() {
		echo "\n\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxGuestBook/css/guestbook.css" media="screen" />'."\n";
		echo "\n\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxGuestBook/js/nospam.js"></script>'."\n";
	}

	/**
	 * M�thode qui rensigne le titre de la page dans la balise html <title>
	 **/
	public function plxShowPageTitle() {
		echo '<?php
			if($this->plxMotor->mode == "guestbook") {
				echo plxUtils::strCheck("'.stripslashes($this->getParam('mnuName')).' - ".$this->plxMotor->aConf["title"]);
				return true;
			}
		?>';
	}	

	/**
	 * M�thode qui r�f�rence la page de guestbook dans le sitemap
	 **/
	public function SitemapStatics() {
		echo '<?php
		echo "\n";
		echo "\t<url>\n";
		echo "\t\t<loc>".$plxMotor->urlRewrite("?guestbook")."</loc>\n";
		echo "\t\t<changefreq>monthly</changefreq>\n";
		echo "\t\t<priority>0.8</priority>\n";
		echo "\t</url>\n";
		?>';
	}

	/**
	 * M�thode qui affiche un message si l'adresse email du guestbook n'est pas renseign�e
	 **/
	public function AdminTopBottom() {

		echo '<?php
		if($plxAdmin->plxPlugins->aPlugins["plxGuestBook"]->getParam("email")=="") {
			echo "<p class=\"warning\">Plugin GuestBook<br />'.$this->getLang("L_GB_ERR_EMAIL").'</p>";
			plxMsg::Display();
		}
		?>';
	}	
	
}
	
?>
