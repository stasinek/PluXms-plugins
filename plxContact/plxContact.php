<?php
/**
 * Plugin plxContact
 * @author	Stephane F
 **/
class plxContact extends plxPlugin {
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

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		# déclaration des hooks
		$this->addHook('AdminTopBottom', 'AdminTopBottom');
		if(plxUtils::checkMail($this->getParam('email'))) {
			$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
			$this->addHook('plxShowConstruct', 'plxShowConstruct');
			$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
			$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
			$this->addHook('ThemeEndHead', 'ThemeEndHead');
			$this->addHook('SitemapStatics', 'SitemapStatics');
		}
	}
	/**
	 * Méthode de traitement du hook plxShowConstruct
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowConstruct() {

		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='contact') {";
		$string .= "\t\$array = array();";
		$string .= "\t\$array[\$this->plxMotor->cible] = array(
			'name'		=> \$plxAdmin->plxPlugins->aPlugins['plxContact']->getParam('mnuName'),
			'menu'		=> '',
			'url'		=> 'contact',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "\t\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats,\$array);";
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

		$template = $this->getParam('template')=='' ? 'static.php' : $this->getParam('template');

		$string = "
		if(\$this->get && preg_match('/^contact\/?/',\$this->get)) {
			\$this->mode = 'contact';
			\$this->cible = '../../plugins/plxContact/form';
			\$this->template = '".$template."';
			return true;
		}
		";
		echo '<?php '.$string.' ?>';
	}
	/**
	 * Méthode qui rensigne le titre de la page dans la balise html <title>
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowPageTitle() {

		$string = "
			if(\$this->plxMotor->mode=='contact') {
				echo '';
				echo plxUtils::strCheck(\$this->plxMotor->aConf['title'].' - '.\$plxAdmin->plxPlugins->aPlugins['plxContact']->getParam('mnuName'));
				return true;
			}";
		echo '<?php '.$string.' ?>';
	}
	/**
	 * Méthode de traitement du hook plxShowStaticListEnd
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowStaticListEnd() {
		# ajout du menu pour accèder à la page de contact

			if($this->getParam('mnuDisplay')) {
			$string = "
			\$class = \$this->plxMotor->mode=='".$this->getParam('url')."' ? 'active' : 'noactive';
			array_splice(\$menus,".($this->getParam('mnuPos')-1).",0,
				'<li><a class=\"menu-item static'.\$class.'\" href=\"\$this->plxMotor->urlRewrite('?".$this->getParam('url')."')\" title=\"".$this->getParam('mnuName')."\">'
				.'".$this->getParam('mnuName')."</a></li>'); 
			";
			echo '<?php '.$string.' ?>';
			}
	}
	/**
	 * Méthode qui ajoute le fichier css dans le fichier header.php du thème
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function ThemeEndHead() {

		$string = "
			echo '\t<link rel=\"stylesheet\" type=\"text/css\" href=\"'.PLX_PLUGINS.'plxContact/style.css\" media=\"screen\"/>\n';
			";
		echo '<?php '.$string.' ?>';
	}
	/**
	 * Méthode qui affiche un message si l'adresse email du contact n'est pas renseignée
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminTopBottom() {

		$string = "
		if('".$this->getParam('email')."'=='') {
			echo '<p class=\"warning\">Plugin Contact<br />".$this->getLang('L_ERR_EMAIL')."</p>\";
			plxMsg::Display();
		}";
		echo '<?php '.$string.' ?>';
	}
	/**
	 * Méthode qui référence la page de contact dans le sitemap
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function SitemapStatics() {
		$string = "
		echo '\n';
		echo '\t<url>\n';
		echo '\t\t<loc>".$plxMotor->urlRewrite('?'.$plxAdmin->plxPlugins->aPlugins['plxContact']->getParam('url'))."</loc>\n';
		echo '\t\t<changefreq>monthly</changefreq>\n';
		echo '\t\t<priority>0.8</priority>\n';
		echo '\t</url>\n';
		";
		echo '<?php '.$string.' ?>';
	}
}
?>
