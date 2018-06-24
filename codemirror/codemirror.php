<?php
if(!defined('PLX_ROOT')) exit;

// http://codemirror.net
// https://github.com/emmetio/codemirror-plugin

class codemirror extends plxPlugin {

	const CM_LIBRARY_CDN = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/';
	const CM_VERSION = '5.39.0'; // only requested for cdn

	const CM_ADDONS_STYLESHEETS =
		'addon/dialog/dialog addon/display/fullscreen '.
		'addon/fold/foldgutter addon/hint/show-hint addon/lint/lint '.
		'addon/hint/show-hint '.
		'addon/scroll/simplescrollbars addon/search/matchesonscrollbar';

	public $mode = false;
	public $local_path = false;
	public $cm_url = false; // sans slash final !
	public $profils = array(
		PROFIL_ADMIN		=> 1,
		PROFIL_MANAGER		=> 2,
		PROFIL_MODERATOR	=> 4,
		PROFIL_EDITOR		=> 8,
		PROFIL_WRITER		=> 16
	);

	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		if(defined('PLX_ADMIN')) {
			# droits pour accéder à la page config.php du plugin
			$this->setConfigProfil(PROFIL_ADMIN);

			$scriptname = basename(strtolower($_SERVER['SCRIPT_NAME']),'.php');
			if($scriptname == 'user') {
				$scriptname = 'profil';
			}
			if(
				(
					($scriptname == 'parametres_plugin') and
					!empty($_REQUEST['p']) and
					($_REQUEST['p'] == __CLASS__)
				) or
				(
					!empty($this->getParam($scriptname)) and
					(
						($scriptname != 'article') or
						$this->__article_enabled()
					)
				)
			) {
				$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
				$this->addHook('AdminFootEndBody', 'AdminFootEndBody');

				$this->mode = $scriptname;
			}

			// $this->addHook('plxMotorConstruct', 'plxMotorConstruct');
			$this->addHook('AdminPluginCss', 'AdminPluginCss');

			// Le plugin peut être appelé directement par Tinymce
			$this->plx_plugin_root = PLX_PLUGINS.__CLASS__.'/';
			$this->__set_links();
		}
	}

	public function saveParams() {
		parent::saveParams();
		if(empty($this->local_path) xor !empty($this->getParam('cdn'))) {
			$this->__set_links();
			$this->onActivate(true);
		}
	}

	private function __article_enabled() {
		return !empty($this->getParam('article') & $this->profils[$_SESSION['profil']]);
	}

	private function __set_links() {
		$folders = glob(__DIR__.'/codemirror*/lib/codemirror*.js');
		if(empty($this->getParam('cdn')) and !empty($folders)) {
			if(DIRECTORY_SEPARATOR == '\\') {
				/* Hack against Window$ */
				$d = str_replace('\\', '/', __DIR__);
				$f = str_replace('\\', '/', $folders[count($folders)  - 1]);
				$folder = preg_replace('@^'.$d.'/(codemirror[^/]*)/lib/codemirror\.js$@', "$1", $f);
			} else {
				$folder = preg_replace('@^'.__DIR__.'/(codemirror[^/]*)/lib/codemirror\.js$@', "$1", $folders[count($folders)  - 1]);
			}
			$this->local_path = __DIR__.'/'.$folder; // sans slash final !
			if(!empty($this->plx_plugin_root)) {
				$this->cm_url =	$this->plx_plugin_root.$folder;
			}
		} else {
			$this->local_path = false;
			$this->cm_url = $this::CM_LIBRARY_CDN.$this::CM_VERSION;
		}
	}

	/**
	 * Récupère l'Url absolue pour le dossier du plugin.
	 * */
	public function get_context($plxMotor) {
		// $this->plx_plugin_root = $plxMotor->racine.$plxMotor->aConf['racine_plugins'].__CLASS__.'/';
		$this->plx_plugin_root = PLX_PLUGINS.__CLASS__.'/';
		$this->__set_links();
	}

	public function updateLang() {
		// Pour le panneau de config. $this->aLang is protected.
		// Impossible d'utiliser le hook AdminPrepend par les fichiers de langue ne sont pas encore chargés
		$this->aLang['L_ARTICLE']				= L_MENU_ARTICLES;
		$this->aLang['L_STATIQUE']				= L_MENU_STATICS;
		$this->aLang['L_COMMENT']				= L_MENU_COMMENTS;
		$this->aLang['L_CATEGORIE']				= L_MENU_CATEGORIES;
		$this->aLang['L_PARAMETRES_PLUGINCSS']	= L_MENU_CONFIG_PLUGINS;
		$this->aLang['L_PARAMETRES_EDITTPL']	= L_THEMES;
	}

	/**
	 * Compile les feuilles de styles de Codemirror lorsque le plugin est activé, hormis les feuilles des themes.
	 *
	 * Called by PlxPlugins::saveConfig() or $this::saveParams().
	 * */
	public function OnActivate($saveParams=false) {
		global $plxAdmin;

		$now = date('Y-m-d H:i');
		$warning = "Don't change manually - build on $now";
		$first_tag = "/* ---------- codemirror.css ----------- */";

		if(!$saveParams) {
			$this->__set_links();
		}

		if(empty($this->getParam('cdn'))) {
			$folder = substr(preg_replace('@^'.__DIR__.'@', '', $this->local_path), 1);
			$content = <<< LOCAL
/*
	$warning
    Reading from $folder folder.
*/

$first_tag

LOCAL;
			$content .= file_get_contents($this->local_path.'/lib/codemirror.css');

			$content .= file_get_contents(__DIR__.'/css/admin.css');

			foreach(explode(' ', $this::CM_ADDONS_STYLESHEETS) as $sheet) {
				$content .= "\n/* --- $sheet.css --- */\n";
				$content .= file_get_contents($this->local_path."/$sheet.css");
			}
		} else {
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_FOLLOWLOCATION	=> true,
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_USERAGENT		=> 'curl/'.curl_version()['version'],
				CURLOPT_HEADER			=> false
			));
			$base_url = $this::CM_LIBRARY_CDN.$this::CM_VERSION.'/';

			$content = <<< REMOTE
/*
	$warning
	Download from $base_url
*/

$first_tag

REMOTE;
			curl_setopt($ch, CURLOPT_URL,$base_url.'codemirror.css');
			$content .= curl_exec($ch);
			$content .= file_get_contents(__DIR__.'/css/admin.css');

			foreach(explode(' ', $this::CM_ADDONS_STYLESHEETS) as $sheet) {
				curl_setopt($ch, CURLOPT_URL, $base_url."$sheet.css");
				$content .= "\n/* --- $sheet.css --- */\n";
				$content .= curl_exec($ch);
			}
			curl_close($ch);
		}

		// Pas de feuilles de style pour les modes utilisés.

		$filename = PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.__CLASS__.'.admin.css';
		if(
			!plxUtils::write(trim($content), $filename) or
			!$plxAdmin->plxPlugins->cssCache('admin')) {
			plxMsg::Error(L_SAVE_FILE_ERROR);
		} elseif($saveParams) {
			# génération du cache css des plugins
			$plxAdmin->plxPlugins->cssCache('admin');
		}
	}

	/**
	 * Crée des listes déroulantes (<select>) des thémes et des raccourci-claviers pour le panneau de configuration.
	 * */
	public function print_select($name, $value, $ext='css') {
?>
		<select id="id_<?php echo $name; ?>" name="<?php echo $name; ?>">
			<option value=""><?php $this->lang('L_DEFAULT'); ?></option>
<?php
		if(!empty($this->local_path)) {
			foreach(glob("{$this->local_path}/".strtolower($name)."/*$ext") as $filename) {
				$buf = basename($filename, ".$ext");
				$caption = ucfirst($buf);
				$selected = ($buf == $value) ? ' selected' : '';
				echo <<< OPTION
			<option value="$buf"$selected>$caption</option>

OPTION;
			}
		} else {
			if(!empty($value)) {
				$caption = ucfirst($value);
				echo <<< OPTION
			<option value="$value" selected>$caption</option>

OPTION;
			}
		}
?>
		</select>
<?php
	}

	/**
	 * Charge la feuille de style CSS nécessaire à Codemirror pour le thème sélectionné.
	 * */
	public function AdminTopEndHead() {

		$theme = $this->getParam('theme');
		if(!empty($this->getParam('theme'))) {
			// $href = $this->cm_url()."/theme/$theme.min.css";
			$href = $this->cm_url."/theme/$theme.css";
?>
	<link type="text/css" rel="<?php if($this->mode == 'parametres_plugin') echo 'alternative '; ?>stylesheet" title ="<?php echo $theme; ?>" href="<?php echo $href; ?>" />
<?php
		}
	}

	/**
	 * Précise à Codemirror le langage (mode) à utiliser.
	 * */
	private function __cm_mode() {
		switch($this->mode) {
			case 'parametres_plugin':
			case 'parametres_plugincss' :
				return 'css';
				break;
			case 'parametres_edittpl':
				global $tpl;
				if(pathinfo($tpl, PATHINFO_EXTENSION) == 'css') {
					return 'css';
					break;
				}
			default:
				return 'application/x-httpd-php';
		}

	}

	/**
	 * Génére la liste des options de Codemirror en fonction du choix utilisateur.
	 * */
	private function __print_options() {
		$options = array(
			'mode' => $this->__cm_mode(),
		);

		# checkbox inputs
		foreach(explode(' ', 'lineNumbers lint matchTags emmet') as $option) {
			if(!empty($this->getParam($option))) {
				$options[$option] = true;
			}
		}

		foreach(explode(' ', 'keyMap theme') as $param) {
			$value = $this->getParam($param);
			if(!empty($value)) {
				$options[$param] = $value;
			}
		}

		echo json_encode(
			$options,
			JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE + JSON_FORCE_OBJECT
		);
	}

	/**
	 * Liste des paramètres pour la librairie requirejs.
	 * */
	private function __print_require_config() {
		$config = array(
			'baseUrl'		=> $this->cm_url,
			'waitSeconds'	=> 15,
			'paths'			=> array(
				'emmet'	=> $this->plx_plugin_root.'emmet/emmet-codemirror-plugin.js'
			),
			'shim'			=> array(
				'emmet'	=> array(
					'exports'	=> 'Emmet'
				)
			)
		);


		if(empty($this->local_path)) {
			// Les librairies sont téléchargées depuis Internet
			$config['paths'] =  array(
				'lib/codemirror' => 'codemirror'
			);
		}

		echo json_encode(
			$config,
			JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE + JSON_FORCE_OBJECT
		);
	}

	/* ------------------------ Hooks start here ---------------------------- */

	public function plxMotorConstruct() {
		echo '<?php $this->plxPlugins->aPlugins["'.__CLASS__.'"]->get_context($this); ?>';
	}

	/**
	 * Charge les scripts Javascript nécessaires pour les plugins (addons) et modes de Codemirror.
	 * Définit la configuration de ce dernier. Détecte si le bundle main.js pour Codemirror existe.
	 * */
	public function AdminFootEndBody() {
		$data_main = $this->plx_plugin_root;
		// check if library is optimized with r.js
		$data_main .= (empty($this->getParam('cdn')) and file_exists(__DIR__.'/main.js')) ? 'main.js' : 'app.js';
?>
	<div class="cm-help-content">
<?php readfile(__DIR__.'/lang/'.$this->default_lang.'-help-content.html'); ?>
	</div>
	<script
		type="text/javascript"
		src="<?php echo $this->plx_plugin_root; ?>require.js"
		data-main="<?php echo $data_main; ?>"
		data-cm-options='<?php $this->__print_options(); ?>'
		data-require-config='<?php echo $this->__print_require_config(); ?>'
	></script>
<?php
		if(
			// ($this->mode != 'parametres_plugin') and
			!empty($this->getParam('lint'))
		) {
?>
	<script src="<?php echo $this->plx_plugin_root; ?>csslint.js" defer></script>
<?php
		}
	}

}
?>
