<?php
if(!defined('PLX_ROOT')) exit('Fuck !');

class kzDownload extends plxPlugin {

	const CLASS_HTML_MASK = '@class="([^"]+)"@';
	const MARK = 'data-download';
	const WEEK_DURATION = 604800; // 3600 * 24 * 7;
	const STATS_PERIODE = 7948800; // = 3600 * 24 * (31 + 30 + 31) ~ 3 mois
	const WEEKS_MAX = 6; /* for admin.php */
	const DATE_FORMAT = 'y\WW';

	public $mediasRoot;

	public function __construct($lang) {
		parent::__construct($lang);
		parent::addHook('plxMotorConstruct', 'plxMotorConstruct');
		$configFilename = $this->plug['parameters.xml'];
		if(!defined('PLX_ADMIN')) {
			if(file_exists($configFilename)) {
				parent::addHook('ThemeEndBody', 'ThemeEndBody');
				parent::addHook('plxMotorSendDownload', 'plxMotorSendDownload');
				// Hack against PluXml : absolute path is required for saving from the front end.
				$this->plug['parameters.xml'] = realpath($configFilename);
			}
		} else {
			parent::setConfigProfil(PROFIL_ADMIN);
			parent::setAdminProfil(PROFIL_ADMIN);
			parent::setAdminMenu(parent::getLang('MENU_NAME'), 0, parent::getLang('MENU_TITLE'));
			if(file_exists($configFilename)) {
				parent::addHook('AdminEditArticle', 'AdminEdit');
				parent::addHook('AdminEditStatique', 'AdminEdit');
			}
		}
	}

	// Fork of plxPlugin::saveParams() from PluXml
	public function saveParams() {

		# Début du fichier XML
		$charset = PLX_CHARSET;
		$xml = <<< XML_BEGIN
<?xml version='1.0' encoding="$charset"?>
<document>\n
XML_BEGIN;
		foreach($this->aParams as $name=>$param) {
			$type = $param['type'];
			switch($type) {
				case 'numeric':
					$value = intval($param['value']);
					$xml .= <<< XML
	<parameter name="$name" type="$type">$value</parameter>\n
XML;
					break;
				case 'string':
					$value = plxUtils::cdataCheck(plxUtils::strCheck($param['value']));
					$xml .= <<< XML
	<parameter name="$name" type="$type">$value</parameter>\n
XML;
					break;
				case 'cdata':
					$value = plxUtils::cdataCheck($param['value']);
					$xml .= <<< XML
	<parameter name="$name" type="$type"><![CDATA[$value]]></parameter>\n
XML;
					break;
			}
		}
		$xml .= <<< XML_END
</document>\n
XML_END;

		# On écrit le fichier
		if(plxUtils::write($xml,$this->plug['parameters.xml'])) {
			# suppression ancien fichier parameters.xml s'il existe encore (5.1.7+)
			if(file_exists($this->plug['dir'].$this->plug['name'].'/parameters.xml'))
				unlink($this->plug['dir'].$this->plug['name'].'/parameters.xml');
			return (class_exists('plxMsg')) ? plxMsg::Info(L_SAVE_SUCCESSFUL) : true;
		}
		else
			return (class_exists('plxMsg')) ? plxMsg::Error(L_SAVE_ERR.' '.$this->plug['parameters.xml']) : false;
	}

	private function __byteConvert($bytes) {
	    if ($bytes == 0) { return "0.00&nbsp;"; }

	    $s = array('&nbsp;', 'K', 'M', 'G', 'T', 'P');
	    $e = floor(log($bytes, 1024));

	    return round($bytes/pow(1024, $e), 2).$s[$e];
	}

	private function __build_gallery($folder) {
		$ext_filename = __DIR__ .'/mime-types/extensions.json';
		$substitutes_filename = __DIR__ . '/icons/substitutes.txt';

		$default_mime_types = array(
			'application/octet-stream',
			'text/plain'
		);
		$folder = trim($folder, '/').'/';
		$root = $this->mediasRoot.$folder;
		$glob = plxGlob::getInstance($root);
		if ($files = $glob->query('@[\w-]+\.\w+$@')) {
			sort($files);

			$description_filename = $root.'.htaccess';
			$descriptions = array();
			if(file_exists($description_filename)) {
			    if(preg_match_all(
					'@^AddDescription\s+"([^"]+)"\s+(.+)$@m',
					file_get_contents($description_filename),
					$matches,
					PREG_SET_ORDER)
				) {
			        foreach($matches as $capture) {
			            $descriptions[$capture[2]] = $capture[1];
			        }
			    }
			}

			$contentRows = array();
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$extensions = (function_exists('json_decode') and file_exists($ext_filename));
			$substitutes = true;
			$prefix = (!empty($this->urlRewriting)) ? 'download/' : 'index.php?download/';
			foreach($files as $filename) {
				$path1 = $root.$filename;
				$href = $prefix.plxEncrypt::encryptId(substr($path1,  $this->offsetRoot));
				$mime_type = finfo_file($finfo, $path1);
				$ext = pathinfo($path1,  PATHINFO_EXTENSION);
				if(
					$ext != 'txt' and
					in_array($mime_type, $default_mime_types) and
					$extensions !== false
				) {
					if($extensions === true) {
						$extensions = json_decode(file_get_contents($ext_filename), true);
					}
					if(array_key_exists($ext, $extensions)) {
						$mime_type = $extensions[$ext];
					}
				}
				$icon_name = str_replace('/', '-', $mime_type);
				$src = "/icons/$icon_name.png";
				if(!file_exists(__DIR__ .$src)) {
					$src = "/icons/$ext.png";
					if(!file_exists(__DIR__ .$src)) {
						if($substitutes === true) {
							// voir pour une correspondance avec un autre mime-type
							if(file_exists($substitutes_filename)) {
								$content = file($substitutes_filename);
								$substitutes = array();
								foreach($content as $line) {
									list($key, $value) = explode("\t", $line, 2);
									if($key != trim($value)) {
										$substitutes[$key] = trim($value);
									}
								}
							} else {
								echo "\n<!--\nfile not found: $substitutes_filename\n-->\n";
								$substitutes = false;
							}
						}
						if(
							is_array($substitutes) and
							array_key_exists($icon_name, $substitutes)
						) {
							$src = "/icons/{$substitutes[$icon_name]}.png";
						} else {
							$parts = explode('/', $mime_type);
							$src = "/icons/{$parts[0]}-x-generic.png";
						}
					}
				}
				$src = PLX_PLUGINS.'/'. __CLASS__ ."$src";
		        $time = date('Y-m-d H:i', filemtime($path1));
		        $icon = '&nbsp;';
		        $size = self::__byteConvert(filesize($path1));
		        $description = (array_key_exists($filename, $descriptions)) ? $descriptions[$filename] : '';
				$contentRows[] = <<< TR
   	     <tr>
    	        <td><img src="$src" width="32" height="32" alt="$ext" title="$mime_type" /></td><td><a href="$href">$filename</a></td><td>$time</td><td>$size</td><td>$description</td>
        </tr>
TR;
			}
			finfo_close($finfo);
			$rows = implode("\n", $contentRows);
		} else {
			// No file
			$caption = $this->getLang('NO_FILE');
			$rows = <<< NO_FILENAME
			<tr colspan="5"><td>$caption</td></tr>\n
NO_FILENAME;
		}

		$titles = array();
		foreach(explode(' ', 'FILENAME DATE SIZE DESCRIPTION') as $t) {
			$titles[] = $this->getLang($t);
		}
		$colGlue = <<< COL_GLUE
</th>
				<th>
COL_GLUE;
		$colTitles = implode($colGlue, $titles);
		$className = __CLASS__ . '-filelist';
		return <<< TABLE
	<div class="scrollable-table"><table class="$className">
		<thead>
			<tr>
				<th></th>
				<th>
$colTitles
				</th>
			</tr>
		</thead><tbody>
$rows
		</tbody>
	</table></div>\n
TABLE;
	}

	public function replace($matches) {
		if(!empty($matches[2])) {
			$prefix = (!empty($this->urlRewriting)) ? 'download/' : 'index.php?download/';
			$href = $prefix.plxEncrypt::encryptId(substr($matches[2], $this->offsetRoot));
			$result = $matches[1].$href.$matches[3];
			if(preg_match(self::CLASS_HTML_MASK, $result)) {
				return preg_replace_callback(
					self::CLASS_HTML_MASK,
					function($matches) {
						$classList = explode(' ', $matches[1]);
						if(in_array('download', $classList)) {
							return $matches[0];
						} else {
							$classList[] = 'download';
							return 'class="'.implode(' ', $classList).'"';
						}
					},
					$result
				);
			} else {
				return str_replace('<a', '<a class="download"', $result);
			}
		} elseif(!empty($matches[5])) {
			return $matches[4].self::__build_gallery($matches[5]);
		}
	}

	private function __getFolders($root) {
		$folders = glob("$root*", GLOB_ONLYDIR);
		if(!empty($folders)) {
			foreach($folders as $folder) {
				$this->folders[] = substr($folder, $this->offsetRoot);
				self::__getFolders("$folder/");
			}
		}
	}

	public function getFolders() {
		$this->folders = array('/');
		$root = PLX_ROOT.$this->mediasRoot.$this->mediasUser;
		$this->offsetRoot = strlen($root) - 1;
		self::__getFolders($root);
		sort($this->folders);
		return $this->folders;
	}

	public function stats($filename) {
		# ckeck if file_exists !

		if(empty($filename)) { return; }

		$datas = trim($this->getParam('stats'));
		if(!empty($datas)) {
			$stats = (function_exists('json_decode')) ? json_decode($datas, true) : $stats = unserialize($datas);
		} else {
			$stats = array();
		}
		$week = date(self::DATE_FORMAT);
		if(!empty($stats[$filename])) {
			if(!array_key_exists($week, $stats[$filename]['weeks'])) {
				$stats[$filename]['weeks'][$week] = 1;
			} else {
				$stats[$filename]['weeks'][$week]++;
			}
			$stats[$filename]['cumul']++;

			// Drop outdated weeks for this filename
			$from = date(self::DATE_FORMAT, time() - self::STATS_PERIODE);
			foreach(array_keys($stats[$filename]['weeks']) as $week1) {
				if($week1 < $from) {
					unset($stats[$filename]['weeks'][$week1]);
				}
			}
			krsort($stats[$filename]['weeks']);
		} else {
			$stats[$filename] = array(
				'weeks'		=> array($week => 1),
				'cumul'		=> 1,
				'published'	=> date('Y-m-d')
			);
		}

		$this->setParam(
			'stats',
			(function_exists('json_encode') ? "\n".json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n" : serialize($stats)),
			'cdata'
		);
		$this->saveParams();
	}

	public function deleteStats($inputName) {
		$datas = trim($this->getParam('stats'));
		if(empty($datas)) { return; }

		$filelist = filter_input(INPUT_POST, $inputName, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
		$stats = (function_exists('json_decode')) ? json_decode($datas, true) : $stats = unserialize($datas);
		$updated = false;
		foreach($filelist as $filename) {
			if(array_key_exists($filename, $stats)) {
				unset($stats[$filename]);
				$updated = true;
			}
		}
		if($updated) {
			$this->setParam(
				'stats',
				(function_exists('json_encode') ? "\n".json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n" : serialize($stats)),
				'cdata'
			);
			$this->saveParams();
		}
	}

/* ========== Hooks =============== */

	public function plxMotorConstruct() {
		$code = <<< 'CODE'
<?php
	$this->plxPlugins->aPlugins['##CLASS##']->mediasRoot = $this->aConf['medias'];
	$this->plxPlugins->aPlugins['##CLASS##']->mediasUser = (
		!empty($this->aConf['userfolders']) and
		!empty($_SESSION['user']) and
		$_SESSION['profil'] == PROFIL_WRITER
	) ? "${_SESSION['user']}/" : '';
	$this->plxPlugins->aPlugins['##CLASS##']->urlRewriting = !empty($this->aConf['urlrewriting']);
?>
CODE;
		echo str_replace('##CLASS##', __CLASS__, $code);
	}

	public function ThemeEndBody() {
		global $output;

		// href for encrypting in an <a> tag with a data-download attribute
		$mark = self::MARK;
		/* (<a\b[^>]*\s+href=")([^"]*)("(?:\s+$mark\b)(?:\s+\w+[^>]*)?>) */
		$anchor_mask = <<< ANCHOR_MASK
(<a\b[^>]*\shref=")([^"]+)(".*\s$mark\b[^>]*)
ANCHOR_MASK;
		// filelist for downloading. The folder is set in the data-download attribute.
		$mark = self::MARK;
		$div_mask = <<< DIV_MASK
(<div(?:\s+\w+[^>]*)?\s+$mark="([^"]+)"(?:\s+[^>]*)?>)(?:[^<]*)
DIV_MASK;
		$mask = "@(?:{$anchor_mask}|{$div_mask})@";

		if(preg_match($mask, $output)) {
			$this->offsetRoot = strlen($this->mediasRoot);
			$output = preg_replace_callback($mask, array($this, 'replace'), $output);
		}
	}

	/**
	 * For counting each downloading file.
	 * */
	public function plxMotorSendDownload() {
		$code = <<< 'CODE'
<?php
$this->plxPlugins->aPlugins['##CLASS##']->stats(
	substr(
		$file,
		strlen(PLX_ROOT.$this->aConf['medias'])
	)
);
?>
CODE;
		echo str_replace('##CLASS##', __CLASS__, $code);
	}

	/**
	 * When editing an article or a static page, adds "data-download" attribute to a <a> tag which links to a file for downloading.
	 * */
	public function plxAdminEdit() {
		$code = <<< 'CODE'
<?php
if(!empty(trim($content['content']))) {
$content['content'] = preg_replace('#MASK#','\1 #MARK#', $content['content']);
	if(array_key_exists('artId', $content)) {
		return false;
	}
}
?>\n
CODE;
		$path1 = $this->mediasRoot.ltrim($this->getParam('download-folder'), '/');
		$mark = self::MARK;
		$anchor_mask = <<< ANCHOR_MASK
@(<a\b[^>]*\s+href="{$path1}[^"]*")(?!\s+{$mark}\b)@
ANCHOR_MASK;
		$replaces = array(
			'#MASK#' => $anchor_mask,
			'#MARK#' => $mark
		);
		echo str_replace(array_keys($replaces), array_values($replaces), $code);
	}

}
?>
