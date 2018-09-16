<?php
/**
 * Plugin lockArticles
 *
 * @package	PLX
 * @version	1.7
 * @date	23/11/2016
 * @author	Rockyhorror
 **/


if(!defined('PLX_ROOT')) exit;
# Control du token du formulaire
plxToken::validateFormToken($_POST);

	if(!empty($_POST)) {
		$plxPlugin->setParam('hide_l_categories', isset($_POST['hide_l_categories'])?1:0, 'numeric');
		$plxPlugin->saveParams();
		header('Location: parametres_plugin.php?p=lockArticles');
		exit;
	}


 ?>
 
	<h2><?php $plxPlugin->lang('L_TITLE') ?></h2>
	<p><?php $plxPlugin->lang('L_DESCRIPTION') ?></p>

	<form action="parametres_plugin.php?p=lockArticles" method="post">
	<fieldset class="withlabel">
		
		<p><?php echo $plxPlugin->getLang('L_HIDE_LOCKED_CATEGORIES') ?>
		<input type="checkbox" name="hide_l_categories" value="True" <?php if($plxPlugin->getParam('hide_l_categories')) { echo 'checked="true"'; }?>/></p>
		
		
	</fieldset>
	<br />
	<?php echo plxToken::getTokenPostMethod() ?>
	<input type="submit" name="submit" value="<?php echo $plxPlugin->getLang('L_SAVE') ?>" />

	</form>
