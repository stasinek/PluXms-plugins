<?php

$pluginName = 'codemirror';
const PLX_ROOT = '../../../';
const PLX_CORE = PLX_ROOT.'core/';
const PLX_PLUGINS = '../../';

include(PLX_ROOT.'config.php');
include(PLX_CORE.'lib/config.php');

include(PLX_CORE.'lib/class.plx.plugins.php');
include(PLX_PLUGINS."$pluginName/$pluginName.php");

// remplace plxUtils::getLangs. Renvoie un tableau.
function getLangs() {
	return array_filter(
		array_map(
			function($item) { return basename($item); },
			glob(PLX_CORE.'lang/??', GLOB_ONLYDIR)
		),
		function($item) { return preg_match('@^[a-z]{2}@i', $item); }
	);
}

$langs = array(
	'default'	=> DEFAULT_LANG,
	'regexp'	=> '@^('.implode('|', getLangs()).')(?:_[A-Z]{2})?$@'
);

$lang = substr(
	filter_input(
		INPUT_GET,
		'lang',
		FILTER_VALIDATE_REGEXP,
		array('options'=>$langs)
	),
	0, 2
);
$plugin = new $pluginName($lang);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
	<meta name="robots" content="noindex, nofollow" />
	<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
	<title><?php $plugin->lang('L_CODE_EDITOR'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtolower(PLX_CHARSET) ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo PLX_CORE ?>admin/theme/plucss.css?ver=<?php echo PLX_VERSION ?>" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo PLX_CORE ?>admin/theme/theme.css?ver=<?php echo PLX_VERSION ?>" media="screen" />
	<link rel="icon" href="<?php echo PLX_CORE ?>admin/theme/images/favicon.png" />
<?php
$filename = PLX_PLUGINS.'admin.css';
if(file_exists($filename)) {
?>
	<link rel="stylesheet" type="text/css" href="<?php echo $filename; ?>" media="screen" />
<?php
}
$plugin->AdminTopEndHead();
?>
</head>
<body class="no-cm-statusbar">
<?php $plugin->AdminFootEndBody(); ?>
<script type="text/javascript">
	function mceFullscreen() {
		if(window.top.tinyMCE != null) {
			const windows = window.top.tinyMCE.activeEditor.windowManager.getWindows();
			windows[windows.length - 1].toggleFullscreen();
		}
	}
</script>
</body>
</html>