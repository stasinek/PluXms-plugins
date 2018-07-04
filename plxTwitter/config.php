<?php

if (!defined('PLX_ROOT')) exit;

# Control du token du formulaire
plxToken::validateFormToken($_POST);

$params = array(
	'consumer_key'=>'string', 'consumer_secret'=>'string',
	'access_token_key'=>'string', 'access_token_secret'=>'string',
	'tags'=>'string', 'author'=>'boolean');

if (!empty($_POST)) {
	foreach ($params as $field=>$type) {
		if ($type == 'boolean') {
			$value = (isset($_POST[$field])) ? 1 : 0;
			$plxPlugin->setParam($field, $value, 'numeric');
		}
		else {
			if (isset($_POST[$field])) {
				$value = $_POST[$field];
				$plxPlugin->setParam($field, $value, $type);
			}
		}
	}
	$plxPlugin->saveParams();
	if (! isset($_POST['tweet']) and ! isset($_POST['credits']) and ! isset($_POST['timeline']) and ! isset($_POST['config'])) {
		header('Location: '.$_SERVER['REQUEST_URI']);
		exit;
	}
}

$plxPlugin->init_config();

if (!array_key_exists($plugin, $plxAdmin->plxPlugins->aPlugins) and isset($plxPlugin)) {
	// $plugin is disabled but we needs to call some Hook in /core/admin/top.php ('AdminTopEndHead', ...)
	$plxPlugins = $plxAdmin->plxPlugins;
	$plxPlugins->aPlugins[$plugin] = $plxPlugin;
	$plxPlugins->aHooks = array_merge_recursive($plxPlugins->aHooks, $plxPlugin->getHooks());
}

?>
	<h2><?php echo(L_PLUGINS_CONFIG.': '.$plxPlugin->getInfo('title')); ?></h2>
	<p><i><?php echo $plxPlugin->getInfo('description'); ?></i></p>
	<p><a href="http://dev.twitter.com/apps" target="_blank">Register your application</a></p>
	<script type="text/javascript">
		function valid() {
			var tags = document.getElementById('tags');
			return true;
		}
	</script>
	<form id="form_<?php echo $plugin; ?>" method="post" onsubmit="return valid();">
<?php
foreach ($params as $field=>$type) {
	$value = plxUtils::strCheck($plxPlugin->getParam($field));
	switch ($field) {
		case 'tags' :
			$title = 'mots séparés par des espaces. Ni accent, ni #';
			break;
		case 'author' :
			$title = '';
			break;
		default :
			$title = $field;
	}
?>
		<p>
			<label for="id_<?php echo $field; ?>" title="<?php echo $title; ?>"<?php echo $classLarge; ?>><?php $plxPlugin->lang('L_'.strtoupper($plugin).'_'.strtoupper($field)); ?></label>
<?php
if ($type == 'boolean') {
	$checked = (intval($value) > 0) ? ' checked' : ''; ?>
			<input id="<?php echo $field; ?>" type="checkbox" name="<?php echo $field; ?>" value="1"<?php echo $checked; ?> />
<?php }
else
	plxUtils::printInput($field, $value, 'text', '50-80', false); ?>
		</p>
<?php
}
?>
		<p>
			<label>&nbsp;</label>
			<?php echo plxToken::getTokenPostMethod()."\n"; ?>
			<input type="submit" />
			<input type="submit" name="tweet" value="<?php $plxPlugin->lang('L_'.strtoupper($plugin).'_TWEET'); ?>"/>
			<input type="submit" name="credits" value="<?php $plxPlugin->lang('L_'.strtoupper($plugin).'_CREDITS'); ?>"/>
			<input type="submit" name="timeline" value="<?php $plxPlugin->lang('L_'.strtoupper($plugin).'_TIMELINE'); ?>"/>
			<input type="submit" name="config" value="<?php $plxPlugin->lang('L_'.strtoupper($plugin).'_CONFIG'); ?>"/>
		</p>
	</form>
<?php
if (isset($_POST['tweet']))
	$response = $plxPlugin->tweet($plxPlugin->getLang('L_'.strtoupper($plugin).'_HELLO_THE_WORLD').' !');
elseif (isset($_POST['credits']))
	$response = $plxPlugin->get_credentials();
elseif (isset($_POST['timeline']))
	$response = $plxPlugin->get_timeline();
elseif (isset($_POST['config']))
	$response = $plxPlugin->get_config();
else
	$response = false;
if ($response !== false) {?>
	<pre id="pre_<?php echo $plugin; ?>">
<?php print_r($response); ?>
	</pre>
<?php
}
?>
