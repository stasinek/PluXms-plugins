<?php
/**
 * Plugin plxGitHub
 *
 **/
class plxGitHub extends plxPlugin {
	/**
	 * Constructeur de la classe jquery
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		# Déclarations des hooks
		$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
		$this->addHook('plxShowConstruct', 'plxShowConstruct');
		$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
		$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('ThemeEndBody', 'ThemeEndBody');
		$this->addHook('SitemapStatics', 'SitemapStatics');
	}

	/**
	 * Méthode de traitement du hook plxShowConstruct
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowConstruct() {

		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='github') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuName')."',
			'menu'		=> '',
			'url'		=> 'github',
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
		if(\$this->get && preg_match('/^github\/?/',\$this->get)) {
			\$this->mode = 'github';
			\$this->cible = '../../plugins/plxGitHub/static';
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

		# ajout du menu pour accèder à la page de github
		if($this->getParam('mnuDisplay')) {
			echo "<?php \$class = \$this->plxMotor->mode=='github'?'active':'noactive'; ?>";
			echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, '<li><a class=\"menu-item static '.\$class.'\" href=\"'.\$this->plxMotor->urlRewrite('?github').'\">".$this->getParam('mnuName')."</a></li>'); ?>";
		}

	}

	/**
	 * Méthode qui ajoute les déclarations dans la partie head du site coté visiteurs
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function ThemeEndHead() {
		if($this->getParam('jquery')=='' OR $this->getParam('jquery')=='1')
		if ($this->plxMotor->mode=="github") {
		echo '<script type="text/javascript" src="'.PLX_PLUGINS.'plxGitHub/js/jquery.min.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.PLX_PLUGINS.'plxGitHub/js/repo.min.js"></script>'."\n";
		echo '<style type="text/css">#repo ul {margin: 0 !important}</style>'."\n";
		}
	}

	/**
	 * Méthode qui ajoute le fichier css dans le fichier header.php du thème
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function ThemeEndBody() {

		$branch = $this->getParam('githubBranch')==''?'':',user: "'.$this->getParam('githubBranch').'"';

		echo '<script type="text/javascript">'."\n";
		echo 'jQuery(function($){
			$("#repo").repo({ user: "'.$this->getParam('gihubUser').'", name: "'.$this->getParam('githubRepo').'" '.$branch.'});
		});
		';
		echo '</script>'."\n";
	}


	/**
	 * Méthode qui rensigne le titre de la page dans la balise html <title>
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowPageTitle() {
		echo '<?php
			if($this->plxMotor->mode == "github") {
				echo plxUtils::strCheck($this->plxMotor->aConf["title"]." - '.$this->getParam('mnuName').'");
				return true;
			}
		?>';
	}

	/**
	 * Méthode qui référence la page de github dans le sitemap
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function SitemapStatics() {
		echo '<?php
		echo "\n";
		echo "\t<url>\n";
		echo "\t\t<loc>".$plxMotor->urlRewrite("?github")."</loc>\n";
		echo "\t\t<changefreq>monthly</changefreq>\n";
		echo "\t\t<priority>0.8</priority>\n";
		echo "\t</url>\n";
		?>';
	}

}
?>