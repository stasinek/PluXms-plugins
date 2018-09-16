<?php
if(!defined('PLX_ROOT')) { exit; }

$fields = explode(' ', 'lightbox2 link title thumbnail');

plxToken::validateFormToken();

if(!empty($_POST)) {
	foreach($fields as $field) {
		if(!empty($_POST[$field]) and $_POST[$field] == '1') {
			$plxPlugin->setParam($field, 1, 'numeric');
		} else {
			$plxPlugin->delParam($field);
		}
	}
	$plxPlugin->saveParams();
	header("Location: parametres_plugin.php?p=$plugin");
	exit;
}

$yes_no = array('0'=>L_YES,'1'=>L_NO);
?>
<form id="form_<?php echo $plugin; ?>"method="post" class="inline-form">
	<div class="in-action-bar">
		<?php echo plxToken::getTokenPostMethod(); ?>
		<input type="submit" value="<?php echo L_ARTICLE_UPDATE_BUTTON; ?>">
	</div>
<?php
foreach($fields as $field) {
	$caption = $plxPlugin->getLang(strtoupper($field).'_CONFIG');
	$value = (!empty($plxPlugin->getParam($field))) ? '1' : '0';
	echo <<< FIELD_START
	<div>
		<label for="id_$field">$caption</label>
FIELD_START;
	plxUtils::printSelect($field, $yes_no, $value);
	echo <<< FIELD_ENDS
	</div>\n
FIELD_ENDS;
}
?>
</form>
