<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	$plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
	$plxPlugin->setParam('mnuName', $_POST['mnuName'], 'cdata');
	$plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
	$plxPlugin->setParam('template', $_POST['template'], 'string');

	$plxPlugin->setParam('jquery', $_POST['jquery'], numeric);
	$plxPlugin->setParam('gihubUser', $_POST['gihubUser'], 'cdata');
	$plxPlugin->setParam('githubRepo', $_POST['githubRepo'], 'cdata');
	$plxPlugin->setParam('githubBranch', $_POST['githubBranch'], 'cdata');

	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxGitHub');
	exit;
}
$mnuDisplay =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
$mnuName =  $plxPlugin->getParam('mnuName')=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName');
$mnuPos =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
$template = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
$jquery =  $plxPlugin->getParam('jquery')=='' ? '1' : $plxPlugin->getParam('jquery');

# On récupère les templates des pages statiques
$files = plxGlob::getInstance(PLX_ROOT.'themes/'.$plxAdmin->aConf['style']);
if ($array = $files->query('/^static(-[a-z0-9-_]+)?.php$/')) {
	foreach($array as $k=>$v)
		$aTemplates[$v] = $v;
}

?>
<style>
form.inline-form label {
	width: 300px;
}
</style>
<form class="inline-form" id="form_plxGitHub" action="parametres_plugin.php?p=plxGitHub" method="post">
	<fieldset>
		<p>
			<label for="id_mnuDisplay"><?php echo $plxPlugin->lang('L_MENU_DISPLAY') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$mnuDisplay); ?>
		<p>
			<label for="id_mnuName"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;:</label>
			<?php plxUtils::printInput('mnuName',$mnuName,'text','20-20') ?>
		</p>
		<p>
			<label for="id_mnuPos"><?php $plxPlugin->lang('L_MENU_POS') ?>&nbsp;:</label>
			<?php plxUtils::printInput('mnuPos',$mnuPos,'text','2-5') ?>
		</p>
		<p>
			<label for="id_template"><?php $plxPlugin->lang('L_TEMPLATE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('template', $aTemplates, $template) ?>
		</p>
		<p>
			<label for="id_jquery"><?php echo $plxPlugin->lang('L_JQUERY') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('jquery',array('1'=>L_YES,'0'=>L_NO),$jquery); ?>
		</p>
		<p>
			<label for="id_gihubUser"><?php $plxPlugin->lang('L_GITHUB_USER') ?>&nbsp;:</label>
			<?php plxUtils::printInput('gihubUser',$plxPlugin->getParam('gihubUser'),'text','20-50') ?>
		</p>
		<p>
			<label for="id_githubRepo"><?php $plxPlugin->lang('L_GITHUB_REPO') ?>&nbsp;:</label>
			<?php plxUtils::printInput('githubRepo',$plxPlugin->getParam('githubRepo'),'text','20-50') ?>
		</p>
		<p>
			<label for="id_githubBranch"><?php $plxPlugin->lang('L_GITHUB_BRANCH') ?>&nbsp;:</label>
			<?php plxUtils::printInput('githubBranch',$plxPlugin->getParam('githubBranch'),'text','20-50') ?>
		</p>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>
