<?php
/**
 * Plugin plxSearch
 * @author	Stephane F
 **/
class plxSearch extends plxPlugin {

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
		if($this->getParam('savesearch'))
			$this->setAdminProfil(PROFIL_ADMIN);

		# déclaration des hooks
		$this->addHook('plxShowConstruct', 'plxShowConstruct');
		$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
		$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
		$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
		$this->addHook('SearchForm', 'form');
		$this->addHook('SitemapStatics', 'SitemapStatics');
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
	}
	/**
	 * Méthode de traitement du hook plxShowConstruct
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowConstruct() {
		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='".$this->getParam('url')."') {";
		$string .= "\$array = array();";
		$string .= "\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuName')."',
			'menu'		=> '',
			'url'		=> 'search',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
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
		if(\$this->get && preg_match('/^".$this->getParam('url')."\/?/',\$this->get)) {
			\$this->mode = '".$this->getParam('url')."';
			\$this->cible = '../../plugins/plxSearch/form';
			\$this->template = '".$template."';
			return true;
		}
		";
		echo "<?php ".$string." ?>";
	}
	/**
	 * Méthode qui renseigne le titre de la page dans la balise html <title>
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowPageTitle() {
		$string = "
			if(\$this->plxMotor->mode =='".$this->getParam('url')."') {
				echo plxUtils::strCheck(\$this->plxMotor->aConf['title']).' - '.plxUtils::strCheck('".$this->getLang('L_PAGE_TITLE')."');
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

		# ajout du menu pour accèder à la page de recherche
		if($this->getParam('mnuDisplay')) {
			
			$string  = "\$class = \$this->plxMotor->mode=='".$this->getParam('url')."' ? 'active' : 'noactive';
				array_splice(\$menus,".($this->getParam('mnuPos')-1).",0,
					'<li><a class=\"menu-item static '.\$class.'\" href=\"".$this->plxMotor->urlRewrite('?'.$this->getParam('url'))."\">'
					".$this->getParam('mnuName')."</a></li>');
				";
			echo "<?php ".$string." ?>";
		}
	}
	/**
	 * Méthode statique qui affiche le formulaire de recherche
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public static function form($title=false) {

		# récuperation d'une instance de plxMotor
		$plxMotor = plxMotor::getInstance();
		$plxPlugin = $plxMotor->plxPlugins->getInstance('plxSearch');
		$searchword = '';
		if(!empty($_POST['searchfield'])) {
			$searchword = plxUtils::strCheck(plxUtils::unSlash($_POST['searchfield']));
		} ?>
	<div class="searchform">
	<form action="<?php echo $plxMotor->urlRewrite('?'.$plxPlugin->getParam('url')); ?>" method="post">
		<?php if($title) : ?>
		<p class="searchtitle"><?php $plxPlugin->lang('L_FORM_SEARCHFIELD') ?>&nbsp;:</p>
		<?php endif; ?>
		<p class="searchfields">
		<input type="text" class="searchfield" name="searchfield" value="<?php echo $searchword ?>" />
		<input type="submit" class="searchbutton" name="searchbutton" value="<?php echo $plxPlugin->getParam('frmLibButton') ?>" />
		</p>
	</form>
	</div><?php
	}
	/**
	 * Méthode qui ajoute le fichier css dans le fichier header.php du thème
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function ThemeEndHead() {
		$string = "
			echo '\t<link rel=\"stylesheet\" type=\"text/css\" href=\"'.PLX_PLUGINS.'plxSearch/style.css\" media=\"screen\" />'.PHP_EOL;
			";
		echo "<?php ".$string." ?>";
	}
	/**
	 * Méthode qui référence la page de recherche dans le sitemap
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function SitemapStatics() {
		$string = "
		echo '\n';
		echo '\t<url>\n';
		echo '\t\t<loc>".$plxMotor->urlRewrite('?'.$plxAdmin->plxPlugins->aPlugins['plxSearch']->getParam('url'))."</loc>\n';
		echo '\t\t<changefreq>monthly</changefreq>\n';
		echo '\t\t<priority>0.8</priority>\n';
		echo '\t</url>\n';
		";
		echo "<?php ".$string." ?>";
	}
}
?>