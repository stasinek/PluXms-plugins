<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	$plxPlugin->setParam('jquery', $_POST['jquery'], 'numeric');
	$plxPlugin->setParam('speed', $_POST['speed'], 'numeric');
	$plxPlugin->setParam('transition', $_POST['transition'], 'string');
	$plxPlugin->setParam('maxwidth', $_POST['maxwidth'], 'numeric');	
	$plxPlugin->setParam('openwin', $_POST['openwin'], 'numeric');		
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxSlippry');
	exit;
}
$parms = array();
$parms['jquery'] = $plxPlugin->getParam('jquery')!='' ? $plxPlugin->getParam('jquery') : true;
$parms['speed'] = $plxPlugin->getParam('speed')!='' ? $plxPlugin->getParam('speed') : '800';
$parms['transition'] = $plxPlugin->getParam('transition')!='' ? $plxPlugin->getParam('transition') : 'fade';
$parms['maxwidth'] = $plxPlugin->getParam('maxwidth')!='' ? $plxPlugin->getParam('maxwidth') : '';
$parms['openwin'] = $plxPlugin->getParam('openwin')!='' ? $plxPlugin->getParam('openwin') : false;
?>
<style>
form.inline-form label {
	width: 300px;
}
</style>
<form class="inline-form" action="parametres_plugin.php?p=plxSlippry" method="post" id="form_plxSlippry">
	<fieldset>
		<p>
			<label for="id_jquery"><?php $plxPlugin->lang('L_JQUERY') ?></label>
			<?php plxUtils::printSelect('jquery',array('1'=>$plxPlugin->getLang('L_YES'),'0'=>$plxPlugin->getLang('L_NO')),$parms['jquery']) ?>
		</p>
		<p>
			<label for="id_speed"><?php $plxPlugin->lang('L_SPEED') ?></label>
			<?php plxUtils::printInput('speed',$parms['speed'],'text','4-4') ?>
		</p>
		<p>
			<label for="id_transition"><?php $plxPlugin->lang('L_TRANSITION') ?></label>
			<?php plxUtils::printSelect('transition',array('fade'=>'fade','horizontal'=>'horizontal','vertical'=>'vertical','kenburns'=>'kenburns'),$parms['transition']) ?>
		</p>	
		<p>
			<label for="id_maxwidth"><?php $plxPlugin->lang('L_MAXWIDTH') ?></label>
			<?php plxUtils::printInput('maxwidth',$parms['maxwidth'],'text','4-4') ?>
		</p>	
		<p>
			<label for="id_jquery"><?php $plxPlugin->lang('L_OPEN_IN_NEW_WINDOW') ?></label>
			<?php plxUtils::printSelect('openwin',array('1'=>$plxPlugin->getLang('L_YES'),'0'=>$plxPlugin->getLang('L_NO')),$parms['openwin']) ?>
		</p>		
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>