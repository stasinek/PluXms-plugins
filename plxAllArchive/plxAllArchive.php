<?php
/**
 * Plugin plxAllArchive
 *
 * @author	Stephane F
 **/
class plxAllArchive extends plxPlugin {

	private $url = ''; # parametre de l'url pour accèder à la page des archives
	public $lang = '';

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

		# gestion du multilingue plxMultiLingue
		if(preg_match('/([a-z]{2})\/(.*)/i', plxUtils::getGets(), $capture)) {
				$this->lang = $capture[1].'/';
		}

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		$this->url = $this->getParam('url')=='' ? 'allarchive' : $this->getParam('url');
				
		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		# déclaration des hooks
		$this->addHook('AdminTopBottom', 'AdminTopBottom');
		$this->addHook('AdminTopEndHead', 'AdminTopEndHead');

		# Si le fichier de langue existe on peut mettre en place la partie visiteur
		if(file_exists(PLX_PLUGINS.$this->plug['name'].'/lang/'.$default_lang.'.php')) {
			$this->addHook('plxShowConstruct', 'plxShowConstruct');
			$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
			$this->addHook('plxMotorDemarrageBegin', 'plxMotorDemarrageBegin');
			$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
			$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
			$this->addHook('SitemapStatics', 'SitemapStatics');
			$this->addHook('AllArchive', 'AllArchive');
		}

	}

	/**
	 * Méthode qui charge le code css nécessaire à la gestion de onglet dans l'écran de configuration du plugin
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminTopEndHead() {
		if(basename($_SERVER['SCRIPT_NAME'])=='parametres_plugin.php') {
			echo '<link href="'.PLX_PLUGINS.$this->plug['name'].'/tabs/style.css" rel="stylesheet" type="text/css" />'."\n";
		}
	}

	/**
	 * Méthode qui affiche un message si le plugin n'a pas la langue du site dans sa traduction
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminTopBottom() {

		echo '<?php
		$file = PLX_PLUGINS."'.$this->plug['name'].'/lang/".$plxAdmin->aConf["default_lang"].".php";
		if(!file_exists($file)) {
			echo "<p class=\"warning\">Plugin AllArchive<br />".sprintf("'.$this->getLang('L_LANG_UNAVAILABLE').'", $file)."</p>";
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
			'name'		=> '".$this->getParam('mnuName_'.$this->default_lang)."',
			'menu'		=> '',
			'url'		=> 'allarchive',
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
		if(\$this->get && preg_match('/^".$this->url."/',\$this->get)) {
			\$this->mode = '".$this->url."';
			\$prefix = str_repeat('../', substr_count(trim(PLX_ROOT.\$this->aConf['racine_statiques'], '/'), '/'));
			\$this->cible = \$prefix.'plugins/plxAllArchive/static';
			\$this->template = '".$template."';
			return true;
		}
		";

		echo "<?php ".$string." ?>";
	}

	/**
	 * Méthode de traitement du hook plxMotorDemarrageBegin
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxMotorDemarrageBegin() {
		echo "<?php
			if(preg_match('/plxAllArchive/', \$this->cible))
				return true;
		?>";
	}

	/**
	 * Méthode de traitement du hook plxShowStaticListEnd
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowStaticListEnd() {

		$name = $this->getParam('mnuName_'.$this->default_lang);
		if(trim($name)==='') $name = get_class($this);

		# ajout du menu pour accèder à la page de toutes les archives
		if($this->getParam('mnuDisplay')) {
			echo "<?php \$status = \$this->plxMotor->mode=='".$this->url."'?'active':'noactive'; ?>";
			echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, '<li class=\"static menu '.\$status.'\" id=\"static-archives\"><a href=\"'.\$this->plxMotor->urlRewrite('?".$this->lang.$this->url."').'\" title=\"".$this->getParam('mnuName_'.$this->default_lang)."\">".$this->getParam('mnuName_'.$this->default_lang)."</a></li>'); ?>";
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
				$this->plxMotor->plxPlugins->aPlugins["plxAllArchive"]->lang("L_PAGE_TITLE");
				return true;
			}
		?>';
	}

	/**
	 * Méthode qui référence la page des archives dans le sitemap
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

	/**
	 * Méthode qui permet d'afficher un lien pour accèder à la liste des archives
	 *
	 * @param	params		string ou array
							- si array:
								- array[0] = type d'affichage (by_year, by_category, by_author, by_title)
								- array[1] = tri par date ('asc', 'desc')
								- array[2] = format d'affichage (variables : #archives_status, #archives_url, #archives_name)
							- si string:
								format d'affichage (variables : #archives_status, #archives_url, #archives_name)
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AllArchive($params='') {

		$default_format = '<a href=\"#archives_url\" class=\"#archives_status\" title=\"#archives_name\">#archives_name</a>';

		if(is_array($params)) {
			$sortby = plxUtils::getValue($params[0]);
			$sort = plxUtils::getValue($params[1]);
			$format = plxUtils::getValue($params[2]);
		} else {
			$sortby=$sort='';
			$format = $params;
		}
		$format = empty($format) ? $default_format : str_replace('"', '\"', $format);

		$url='?'.$this->url;
		if(!empty($sortby)) {
			$url.='/'.(empty($sort)?'desc':$sort);
			$url.='_'.$sortby;
		}

		echo '<?php
		$name = str_replace("#archives_url", $plxMotor->urlRewrite("'.$url.'"), "'.$format.'");
		$name = str_replace("#archives_name", "'.$this->getParam('mnuName_'.$this->default_lang).'", $name);
		if ($plxShow->plxMotor->get AND preg_match("/^'.$this->url.'/", $plxShow->plxMotor->get))
			$name = str_replace("#archives_status", "active", $name);
		else
			$name = str_replace("#archives_status", "noactive", $name);
		echo $name;
		?>';
	}
}
?>