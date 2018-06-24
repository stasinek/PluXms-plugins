<?php
if (! defined('PLX_ROOT')) exit;

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if (! empty($_POST)) {
	foreach($plxPlugin->useful_scripts as $field) {
		$value = (isset($_POST[$field])) ? 1 : 0;
		$plxPlugin->setParam($field, $value, 'numeric');
	}
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p='.$plugin);
	exit;
} ?>
	<h2><?php echo(L_PLUGINS_CONFIG.': '.$plxPlugin->getInfo('title')); ?></h2>
	<p><i><?php echo $plxPlugin->getInfo('description'); ?></i></p>
	<p>Activer le plugin pour les rubriques suivantes :</p>
	<form id="form_<?php echo $plugin; ?>" method="post">
		<div>
<?php
$labels = array(
	'article'		=>L_MENU_ARTICLES,
	'statique'		=>L_MENU_STATICS,
	'comment'		=>L_MENU_COMMENTS,
	'comment_new'	=>L_COMMENT_ANSWER_TITLE,
	'categorie'		=>L_MENU_CATEGORIES,
	'profil'		=>L_MENU_CONFIG_USERS,
	'user'			=>L_MENU_PROFIL
);
foreach ($plxPlugin->useful_scripts as $field) {
	$label = $labels[$field];
	$value = $plxPlugin->getParam($field);
	$checked = ($value > 0) ? ' checked' : '';
	echo <<< FIELD
		<p>
			<label for="id_{$field}">{$label}</label>
			<input type="checkbox" id="id_{$field}" name="{$field}"{$checked} />
		</p>

FIELD;
}
?>
		</div>
		<p>
			<label>&nbsp;</label>
			<?php echo plxToken::getTokenPostMethod()."\n"; ?>
			<input type="submit" value="<?php echo L_ARTICLE_UPDATE_BUTTON; ?>">
		</p>
	</form>
