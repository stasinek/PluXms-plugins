<?php
if(!defined('PLX_ADMIN') or !defined('PLX_ROOT')) { exit; }

plxToken::validateFormToken();

$folders = $plxPlugin->getFolders();

if(filter_has_var(INPUT_POST, 'download-folder')) {
	$value = filter_input(INPUT_POST, 'download-folder', FILTER_SANITIZE_STRING);
	if(!empty($value) and in_array($value, $folders)) {
		$plxPlugin->setParam('download-folder', $value, 'cdata');
		$plxPlugin->saveParams();
	}
	header("Location: parametres_plugin.php?p=$plugin");
	exit;
}

$param = 'download-folder';
$download_folder = $plxPlugin->getParam($param);
?>
<form id="form_<?php echo $plugin; ?>" class="inline-form" method="post">
	<div>
		<label for="id_<?php echo $param; ?>"><?php $plxPlugin->lang(strtoupper($param)); ?></label>
		<select id="id_<?php echo $param; ?>" name="<?php echo $param; ?>">
<?php
foreach($folders as $folder1) {
	$selected = ($folder1 == $download_folder) ? ' selected' : '';
	echo <<< OPTION
			<option value="$folder1"$selected>$folder1</option>\n
OPTION;
}
?>
		</select>
	</div>
	<div class="in-action-bar">
		<?php echo plxToken::getTokenPostMethod(); ?>
		<input type="submit" value="<?php $plxPlugin->lang('SAVE'); ?>"/>
	</div>
</form>
