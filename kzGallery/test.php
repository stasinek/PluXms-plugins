<?php
class MyObject {

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
				$selected = ($value == $aDir) ? ' selected' : '';
				$level = substr_count($aDir, '/');
				return <<< OPTION
		<option class="level-$level" value="$aDir"$selected>$aDir</option>
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
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">

<head>
	<title>Test picturesFolders</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
</head>
<body>
<?php
const ROOT = '../../echecs/medias/';
$myObject = new MyObject(ROOT);
echo $myObject->select('medias');
var_dump($myObject);
?>
</body>
</html>
