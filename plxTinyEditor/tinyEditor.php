<?php
/**
 * Plugin smallEditor
 * Fork de plxEditor
 *
 * @package	PLX
 * @author	J.P. Pourrez, Stephane F
 **/
class tinyEditor extends plxPlugin {

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Stephane F
	 **/
	public $useful_scripts =  array('article', 'statique', 'comment', 'comment_new', 'categorie', 'profil', 'user');

	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		# Déclarations des hooks
		$scriptname = basename($_SERVER['SCRIPT_NAME'], '.php');
		if (in_array($scriptname, $this->useful_scripts)) {
			$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
			$this->addHook('AdminFootEndBody', 'AdminFootEndBody');
			$this->addHook('plxAdminEditArticle', 'plxAdminEditArticle');
			$this->addHook('AdminArticleTop', 'AdminArticleTop');
		} else if ($scriptname == 'medias') {
			$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
			$this->addHook('AdminMediasFoot', 'AdminMediasFoot');
		}

	}

	private function pluginRoot() {
		global $plxAdmin;
		return $plxAdmin->racine.$plxAdmin->aConf['racine_plugins'].__CLASS__.'/';
	}

	private function enable() {
		$scriptname = basename($_SERVER['SCRIPT_NAME'], '.php');
		return (($scriptname == 'medias') or (intval($this->getParam($scriptname)) > 0));
	}

	/**
	 * Méthode qui convertit les liens absolus en liens relatifs pour les images et les documents
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxAdminEditArticle() {
		if ($this->enable()) {
			// $chapo and $content are not global, but inside plxAdmin::editArticle($content, $id)
			global $plxAdmin;
			$url_base = $plxAdmin->racine;
			$code = <<< CODE
<?php
foreach(array('chapo', 'content') as \$k) {
	\$content[\$k] = str_replace('="$url_base', '="', \$content[\$k]);
}
?>
CODE;
			echo $code;
		} else {
			return false;
		}
	}

	/**
	 * Méthode qui convertit les liens relatifs en liens absolus pour les images et les documents
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminArticleTop() {
		if ($this->enable()) {
			global $plxAdmin, $chapo, $content;
			$chapo = plxUtils::rel2abs($plxAdmin->racine, $chapo);
			$content = plxUtils::rel2abs($plxAdmin->racine, $content);
		}
	}

	/**
	 * Méthode du hook AdminTopEndHead
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminTopEndHead() {
		if ($this->enable()) { ?>
	<script type="text/javascript" src="<?php echo $this->pluginRoot().__CLASS__.'/'.__CLASS__.'.js'; ?>"></script>
<?php
		}
	}

	/**
	 * Méthode du hook AdminFootEndBody
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminFootEndBody() {
		if ($this->enable()) {
			global $plxAdmin; ?>
<script type="text/javascript">
<!--
	options = { <?php
		$buf = $this->loadLang(dirname(__FILE__).'/lang/'.$this->default_lang.'.php');
		$start = strlen('L_'.__CLASS__.'_');
		foreach($buf as $key=>$value) {
			echo substr($key, $start).": '".addslashes($value)."',\n";
		}
?>
		urlBase:		'<?php echo $plxAdmin->racine; ?>',
		mediasManager:	'<?php echo $plxAdmin->racine; ?>core/admin/medias.php',
		smiliesPath:	'<?php echo $this->pluginRoot().__CLASS__; ?>/smilies/'
	};
	for (i in options) {
		TINYEDITOR.settings[i] = options[i];
	}
	var textAreas = document.querySelectorAll('textarea[name]');
	for (i=0, iMax = textAreas.length; i<iMax; i++) {
		var
			elm = textAreas[i],
			name = elm.name;
			TINYEDITOR.editors[name] = new TINYEDITOR.editor.create(name);
	}
	window.addEventListener('load', function(event) {
		var item = sessionStorage.getItem('TINYEDITOR');
		if (item) {
			var context = JSON.parse(item);
			for (ed in TINYEDITOR.editors) {
				if (context[ed]) {
					if (TINYEDITOR.editors[ed].viewFullscreen != context[ed].viewFullscreen) {
						TINYEDITOR.editors[ed].toggleFullscreen(null);
					}
					var ifr = E$('id_'+ed+'-iframe');
					if (ifr) {
						ifr.style.height = context[ed].iframe;
					}
				}
			}
		}
	});
// -->
</script>
<?php	}
	}

	// called by medias.php
	public function AdminMediasFoot() {
		if ($this->enable()) { ?>
	<script type="text/javascript"> <!-- tinyEditor -->
		<!--
		mediasSet('#medias-table tbody');
		// -->
	</script>
<?php	}
	}
}
?>
