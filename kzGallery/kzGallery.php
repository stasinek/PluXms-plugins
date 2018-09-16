<?php
if(!defined('PLX_ROOT')) { exit; }

/*
 * https://github.com/sachinchoolur/lightgallery.js (3845 ★)
 * https://github.com/sachinchoolur/lightGallery (3.1k ★)
 * https://github.com/dimsemenov/Magnific-Popup (10,108 ★)
 * https://fancyapps.com/fancybox/3/ (5.4k ★)
 * http://lokeshdhakar.com/projects/lightbox2/ (3.9k ★)
 * https://github.com/feimosi/baguetteBox.js (no Jquery 1.5k ★)
 * https://github.com/jackmoore/colorbox (4724 ★)
 * */

/**
 * Liste les dossiers contenant des vignettes d'images
 * */
class KzVignettes {
	const FILTRE = '/*.tb.{jpg,jpeg,png,gif}';
	public $dirs = array();

	public function __construct($root) {
		$this->root = $root;
		$this->offset = strlen($root);
		self::__getAlldirs($root);
		natsort($this->dirs);
	}

	private function __getAllDirs($currentFolder) {
		$currentFolder = rtrim($currentFolder, '/');
		$dirs = glob($currentFolder.'/*', GLOB_ONLYDIR);
		if(!empty($dirs)) {
			foreach($dirs as $dir1) {
				self::__getAllDirs($dir1);
			}
		}
		$pictures = glob($currentFolder.self::FILTRE, GLOB_BRACE);
		if(!empty($pictures) and !file_exists("$currentFolder/no-gallery.txt")) {
			$this->dirs[] = substr("$currentFolder/", $this->offset);
		}
	}

	public function select($name, $value=false, $class=false) {
		$options = implode("\n", array_map(
			function($aDir) use ($value) {
				$caption = (!empty($aDir)) ? $aDir : '.';
				$selected = ($value == $aDir) ? ' selected' : '';
				$level = substr_count($aDir, '/');
				return <<< OPTION
		<option class="level-$level" value="$aDir"$selected>/$caption</option>
OPTION;
			},
			$this->dirs
		));
		$className = (!empty($class)) ? ' class="'.$class.'"' : '';
		return <<< SELECT
	<select name="$name"$className>
$options
	</select>\n
SELECT;
	}

	public function hasVignettes($folder) {
		return (!empty($folder) and in_array($folder, $this->dirs));
	}
}

/**
 * Insère une ou plusieurs séries de photos miniatures dans le corps d'un article ou d'une page statique pour afficher un diaporama des photos en taille réelle.*
 *
 * @author	bazooka07
 * */
class kzGallery extends plxPlugin {

	const ART_GALLERY_FIELDNAME = 'gallery-folder';
	const GALLERY_PATTERN = '@(<div(?:\s+[^>]*)?\s+data-gallery="([^"]+)"(?:\s+[^>]*)?>)[^<]*(</div>)@';

	public function __construct($default_lang) {

		parent::__construct($default_lang);

		parent::addHook('plxMotorParseArticle', 'plxMotorParseArticle');

		if(defined('PLX_ADMIN')) {
			parent::setConfigProfil(PROFIL_ADMIN);
			parent::addHook('AdminArticleContent', 'AdminArticleContent');
			parent::addHook('AdminArticlePreview', 'AdminArticlePreview');
			parent::addHook('AdminEditArticleXml', 'AdminEditArticleXml');
			# Manage the famous copy-paste couple
			parent::addHook('AdminMediasFoot', 'AdminMediasFoot');
			parent::addHook('AdminArticleFoot', 'AdminArticleFoot');
		} else {
			parent::addHook('plxMotorDemarrageEnd', 'plxMotorDemarrageEnd'); # article
			parent::addHook('plxShowStaticContent', 'plxShowStaticContent'); # static page
			parent::addHook('ThemeEndHead', 'ThemeEndHead');
			parent::addHook('ThemeEndBody', 'ThemeEndBody');
		}

		parent::addHook('plxMotorConstruct', 'plxMotorConstruct');

		$this->replaces = array(
			'##CLASS##'		=> __CLASS__,
			'##FIELDNAME##'	=> self::ART_GALLERY_FIELDNAME,
			'##PATTERN##'	=> self::GALLERY_PATTERN,
			'##CAPTION##'	=> $this->getLang('ARTICLE_GALLERY_FOLDER'),
			'##HINT##'		=> self::getLang('ART_HINT'),
			'##PASTE##'		=> self::getLang('PASTE')
		);
	}

	public function callback($code) {
		echo
			"<?php\n".
			str_replace(
				array_keys($this->replaces),
				array_values($this->replaces),
				$code
			).
			"\n?>";
	}

	public function gallery_builder($folder, $root, $lightbox) {
		$exts = 'jpg|jpeg|png|gif';
		$thumbs_mask = '@\.tb\.(?:'.$exts.')$@';
		$exts_mask = '@\.('.$exts.')$@';
		$default_thumbnail = PLX_ROOT.'core/admin/theme/images/icon_plugin.png';
		# $lightbox = basename($folder);
		$content = array();
		$files = glob($root.$folder.'*.{jpg,jpeg,png,gif}', GLOB_BRACE);
		foreach($files as $filename) {
			if(!is_dir($filename) and !preg_match($thumbs_mask, $filename)) {
				$thumb_name = preg_replace($exts_mask, '.tb.$1',  $filename);
				if(!file_exists($thumb_name) and empty($this->getParam('thumbnail'))) {
					# thumbnail is required !
					break;
				}
				/*
				$src = (file_exists($thumb_name)) ? $thumb_name : $default_thumbnail;
				$imageSize = getImageSize($src);
				 * */
				if(file_exists($thumb_name)) {
					$src = $thumb_name;
					$imageSize = getImageSize($src)[3];
				} else {
					$src = $filename;
					# $imageSize = 'height="'.$this->height.'"';
					$imageSize = 'style="height: '.$this->height.'px;"';
				}
				$alt = preg_replace($exts_mask, '', basename($filename));
				if(defined('PLX_FEED') or !empty($this->getParam('title'))) {
					# No title
					if(!empty($this->getParam('link'))) {
						# No link to the original image
						$content[] = <<< CONTENT
	<img src="$src" alt="$alt" {$imageSize}>
CONTENT;
					} else {
						$content[] = <<< CONTENT
	<a href="$filename" data-lightbox="$lightbox"><img src="$src" alt="$alt" {$imageSize}></a>
CONTENT;
					}
				} else {
					if(!empty($this->getParam('link'))) {
						# No link to the original image
						$content[] = <<< CONTENT
	<figure>
		<img src="$src" alt="$alt" {$imageSize}>
		<figcaption>$alt</figcaption>
	</figure>
CONTENT;
					} else {
						$content[] = <<< CONTENT
	<figure>
		<a href="$filename" data-lightbox="$lightbox">
			<img src="$src" alt="$alt" {$imageSize}>
		</a>
		<figcaption>$alt</figcaption>
	</figure>
CONTENT;
					}
				}
			}
		}
		return implode("\n", $content);
	}

	public function galleries_insert($content, $root, $tag='') {
		return preg_replace_callback(
			self::GALLERY_PATTERN,
			function($matches) use($root, $tag) {
				$str1 = basename($matches[2]);
				$extra = (!empty($tag)) ? "$tag-$str1" : $str1;
				return $matches[1].$this->gallery_builder($matches[2], $root, $extra).$matches[3];
			},
			$content
		);
	}

	public function gallery_append($folder, $root, $tag='') {

		$innerHTML = $this->__gallery_builder($folder, $root, $tag);
		$className = __CLASS__;
		return <<< CONTENT
<div data-gallery="$folder" class="$className">
$innerHTML
</div>
CONTENT;
	}

/* ======================== Hooks ========================= */

	const ADMIN_ARTICLE_CONTENT_CODE = <<< 'ADMIN_ARTICLE_CONTENT_CODE'
$folder = (!empty($result['##FIELDNAME##'])) ? $result['##FIELDNAME##'] : ''; # new article ?
# $galleries = new plxMedias(PLX_ROOT.$plxAdmin->aConf['medias'], $folder);
# $galleriesSelect = str_replace('"folder"', '"##FIELDNAME##"', $galleries->contentFolder());
$galleries = new KzVignettes(PLX_ROOT.$plxAdmin->aConf['medias']);
$galleriesSelect = $galleries->select('##FIELDNAME##', $folder);
echo <<< SELECT
			<div class="grid">
				<div class="col sml-12">
					<label for="id_thumbnail">##CAPTION##&nbsp;:&nbsp;<a class="hint"><span>##HINT##</span></a></label>
					{$galleriesSelect}
				</div>
			</div>\n
SELECT;
ADMIN_ARTICLE_CONTENT_CODE;

	/**
	 * Add a select tag in the edit article page for selecting a folder of pictures.
	 * */
	public function AdminArticleContent() {

		self::callback(self::ADMIN_ARTICLE_CONTENT_CODE);
	}

	const ADMIN_ARTICLE_PREVIEW_CODE = <<< 'ADMIN_ARTICLE_PREVIEW_CODE'
# $root = PLX_ROOT.$plxAdmin->aConf['medias'];
$folder = filter_input(INPUT_POST, '##FIELDNAME##');
$art['##FIELDNAME##'] = $folder;
ADMIN_ARTICLE_PREVIEW_CODE;

	public function AdminArticlePreview() {
		self::callback(self::ADMIN_ARTICLE_PREVIEW_CODE);
	}

	const PLX_ADMIN_EDITARTICLE_XML_CODE = <<< 'PLX_ADMIN_EDITARTICLE_XML_CODE'
		if(!empty($content['##FIELDNAME##']) and $content['##FIELDNAME##'] != '.') {
			$value = rtrim(plxUtils::cdataCheck($content['##FIELDNAME##']), '/').'/';
		$xml .= <<< XML
	<##FIELDNAME##><![CDATA[$value]]></##FIELDNAME##>\n
XML;
		}
PLX_ADMIN_EDITARTICLE_XML_CODE;

	/**
	 * Add a field when saving an article in the xml file.
	 * */
	public function AdminEditArticleXml() {
		self::callback(self::PLX_ADMIN_EDITARTICLE_XML_CODE);
	}

	const PLXMOTOR_PARSEARTICLE_CODE = <<< 'PLXMOTOR_PARSEARTICLE_CODE'
$folder = (array_key_exists('##FIELDNAME##', $iTags)) ? $values[$iTags['##FIELDNAME##'][0]]['value'] : '';
$art['##FIELDNAME##'] = $folder;
if(defined('PLX_FEED') and empty(trim($art['chapo']))) {
	$root = PLX_ROOT.$this->aConf['medias'];
	if(!empty($folder)) {
		$art['content'] .=
			'<div data-gallery="'.$folder.'" class="$className">'.
			$this->plxPlugins->aPlugins['##CLASS##']->gallery_builder($folder, $root, '').
			'</div>';
	} elseif(!empty($art['content'])) {
		$art['content'] = $this->plxPlugins->aPlugins['##CLASS##']->galleries_insert(
			$art['content'],
			$root
		);
	}
}
PLXMOTOR_PARSEARTICLE_CODE;

	public function plxMotorParseArticle() {
		self::callback(self::PLXMOTOR_PARSEARTICLE_CODE);
	}

	const PLXMOTOR_DEMARRAGE_END_CODE = <<< 'PLXMOTOR_DEMARRAGE_END_CODE'
if(
	in_array($this->mode, array('article', 'home', 'tags', 'archives')) and
	!empty($this->plxRecord_arts->result)
) {
	# Building the gallery for the site side.
	$root = PLX_ROOT.$this->aConf['medias'];
	for($i=0, $iMax=count($this->plxRecord_arts->result); $i<$iMax; $i++) {
		if(
			$this->mode == 'article' or
			empty($this->plxRecord_arts->result[$i]['chapo'])
		) {
			$tag = 'post-'.ltrim($this->plxRecord_arts->result[$i]['numero'], '0');
			if(!empty($this->plxRecord_arts->result[$i]['##FIELDNAME##'])) {
				$folder = $this->plxRecord_arts->result[$i]['##FIELDNAME##'];
				$this->plxRecord_arts->result[$i]['content'] .=
					'<div data-gallery="'.$folder.'" class="$className">'.
					$this->plxPlugins->aPlugins['##CLASS##']->gallery_builder($folder, $root, $tag).
					'</div>';
			} elseif(!empty($this->plxRecord_arts->result[$i]['content'])) {
				$this->plxRecord_arts->result[$i]['content'] = $this->plxPlugins->aPlugins['##CLASS##']->galleries_insert(
					$this->plxRecord_arts->result[$i]['content'],
					$root,
					$tag
				);
			}
		}
	}
}
PLXMOTOR_DEMARRAGE_END_CODE;

	public function plxMotorDemarrageEnd() {
		self::callback(self::PLXMOTOR_DEMARRAGE_END_CODE);
	}

	const PLXSHOW_STATIC_CONTENT_CODE = <<< 'PLXSHOW_STATIC_CONTENT_CODE'
if(!empty($output)) {
	$output = $this->plxMotor->plxPlugins->aPlugins['##CLASS##']->galleries_insert(
		$output,
		PLX_ROOT.$this->plxMotor->aConf['medias']
	);
}
PLXSHOW_STATIC_CONTENT_CODE;

	/**
	 * Builds many galleries in static pages.
	 * */
	public function plxShowStaticContent() {
		self::callback(self::PLXSHOW_STATIC_CONTENT_CODE);
	}

	/**
	 * Add stylesheet for Lightbox if requested.
	 * */
	public function ThemeEndHead() {
		if(empty($this->getParam('lightbox2'))) {
			$href = "{$this->url}lightbox2/css/lightbox.min.css";
			echo <<< EOT
	<link rel="stylesheet" href="$href" />\n
EOT;
		}
	}

	/**
	 * Add Javascript for Lightbox if requested.
	 * */
	public function ThemeEndBody() {
		if(empty($this->getParam('lightbox2'))) {
			$root = "{$this->url}lightbox2/js/";
?>
	<script type="text/javascript">
		(function() {
			'use script';

			const src = (typeof 'jQuery' != 'undefined') ? 'lightbox-plus-jquery.min.js' : 'lightbox.min.js';
			const script = document.createElement('SCRIPT');
			script.type = 'text/javascript';
			script.src = '<?php echo $root; ?>' + src;
			document.head.appendChild(script);
		})();
	</script>
<?php
		}
	}

	const PLXMOTOR_CONSTRUCT_CODE = <<< 'PLXMOTOR_CONSTRUCT_CODE'
$this->plxPlugins->aPlugins['##CLASS##']->url = $this->racine.$this->aConf['racine_plugins'].'##CLASS##/';
$this->plxPlugins->aPlugins['##CLASS##']->height = $this->aConf['miniatures_h'];
PLXMOTOR_CONSTRUCT_CODE;

	/**
	 * Gets the url of the folder plugin.
	 * */
	public function plxMotorConstruct() {
		self::callback(self::PLXMOTOR_CONSTRUCT_CODE);
	}

	/**
	 * Add a button in the medias manager for copying the current folder.
	 * */
	public function AdminMediasFoot() {
?>
<script type="text/javascript">
	(function() {
		'use strict';

		const select = document.getElementById('folder');
		const submitBtn = document.querySelector('input[name="btn_changefolder"]');
		if(select != null && submitBtn != null) {
			var input = null;
			const parent = submitBtn.parentElement;
			const copyBtn = document.createElement('INPUT');
			copyBtn.type = 'button';
			copyBtn.id = '<?php echo __CLASS__; ?>-copy';
			copyBtn.value = "<?php $this->lang('COPY'); ?>";
			copyBtn.title = `<?php $this->lang('COPY_BTN_TITLE'); ?>`;
<?php
		$vignettesFolders = new KzVignettes(PLX_ROOT.$_SESSION['medias']);
		$disabled = $vignettesFolders->hasVignettes($_SESSION['folder']) ? '' : ' disabled';
		if($disabled) {
			echo <<< DISABLED
			copyBtn.disabled = true;
			parent.appendChild(copyBtn);\n
DISABLED;
		} else {
?>
			parent.appendChild(copyBtn);
			copyBtn.addEventListener('click', function(event) {
				if(typeof sessionStorage != 'undefined') {
					sessionStorage.setItem('<?php echo __CLASS__; ?>-folder', select.value);
				}

				if(input == null) {
					input = document.createElement('INPUT');
					input.type = 'text';
					input.className = '<?php echo __CLASS__; ?> outside';
					document.body.appendChild(input);
				}
				input.value = '<div class="<?php echo __CLASS__; ?>" data-gallery="' + select.value + '">' + "<?php $this->lang('GALLERY_NAME'); ?>".replace('#FOLDER#', select.value.replace(/\/$/, '')) + '</div>';
				input.select();
				document.execCommand('copy');
				copyBtn.classList.add('copied');
				input.value = '';

				event.preventDefault();
			});
			copyBtn.addEventListener('transitionend', function(event) {
				copyBtn.classList.remove('copied');
			});
<?php
		}
?>
		}
	})();
</script>
<?php
	}

	public function AdminArticleFoot() {
?>
<script type="text/javascript">
	(function() {
		'use strict';
		if(typeof sessionStorage != 'undefined') {
			const select = document.getElementById("<?php echo $this->replaces['##FIELDNAME##']; ?>");
			const myKey = '<?php echo __CLASS__; ?>-folder';
			const folder = sessionStorage.getItem(myKey);
			if(select != null && folder != null) {
				sessionStorage.removeItem(myKey);
				const pasteBtn = document.createElement('INPUT');
				pasteBtn.type = 'button';
				pasteBtn.value = "<?php $this->lang('PASTE'); ?>";
				select.parentElement.appendChild(pasteBtn);
				pasteBtn.addEventListener('click', function(event) {
					select.value = folder;
					event.preventDefault();
				});
			}
		}
	})();
</script>
<?php
	}

}
?>
