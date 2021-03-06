<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	$plxPlugin->setParam('jquery', $_POST['jquery'], 'numeric');
	$plxPlugin->setParam('width', $_POST['width'], 'numeric');
	$plxPlugin->setParam('height', $_POST['height'], 'numeric');
	$plxPlugin->setParam('spw', $_POST['spw'], 'numeric');
	$plxPlugin->setParam('sph', $_POST['sph'], 'numeric');
	$plxPlugin->setParam('delay', $_POST['delay'], 'numeric');
	$plxPlugin->setParam('sDelay', $_POST['sDelay'], 'numeric');
	$plxPlugin->setParam('opacity', $_POST['opacity'], 'string');
	$plxPlugin->setParam('titleSpeed', $_POST['titleSpeed'], 'numeric');
	$plxPlugin->setParam('effect', $_POST['effect'], 'string');
	$plxPlugin->setParam('navigation', $_POST['navigation'], 'numeric');
	$plxPlugin->setParam('links', $_POST['links'], 'numeric');
	$plxPlugin->setParam('hoverPause', $_POST['hoverPause'], 'numeric');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxCoinSlider');
	exit;
}
$parms = array();
$parms['jquery'] = $plxPlugin->getParam('jquery')!='' ? $plxPlugin->getParam('jquery') : true;
$parms['width'] = $plxPlugin->getParam('width')!='' ? $plxPlugin->getParam('width') : '500';
$parms['height'] = $plxPlugin->getParam('height')!='' ? $plxPlugin->getParam('height') : '300';
$parms['spw'] = $plxPlugin->getParam('spw')!='' ? $plxPlugin->getParam('spw') : '7';
$parms['sph'] = $plxPlugin->getParam('sph')!='' ? $plxPlugin->getParam('sph') : '5';
$parms['delay'] = $plxPlugin->getParam('delay')!='' ? $plxPlugin->getParam('delay') : 3000;
$parms['sDelay'] = $plxPlugin->getParam('sDelay')!='' ? $plxPlugin->getParam('sDelay') : '30';
$parms['opacity'] = $plxPlugin->getParam('opacity')!='' ? $plxPlugin->getParam('opacity') : '0.7';
$parms['titleSpeed'] = $plxPlugin->getParam('titleSpeed')!='' ? $plxPlugin->getParam('titleSpeed') : '500';
$parms['effect'] = $plxPlugin->getParam('effect')!='' ? $plxPlugin->getParam('effect') : 'random';
$parms['navigation'] = $plxPlugin->getParam('navigation')!='' ? $plxPlugin->getParam('navigation') : true;
$parms['links'] = $plxPlugin->getParam('links')!='' ? $plxPlugin->getParam('links') : false;
$parms['hoverPause'] = $plxPlugin->getParam('hoverPause')!='' ? $plxPlugin->getParam('hoverPause') : true;
?>
<style>
form.inline-form label {
	width: 300px;
}
</style>
<form class="inline-form" action="parametres_plugin.php?p=plxCoinSlider" method="post" id="form_plxCoinSlider">
	<fieldset>
		<p>
			<label for="id_jquery"><?php $plxPlugin->lang('L_JQUERY') ?></label>
			<?php plxUtils::printSelect('jquery',array('1'=>$plxPlugin->getLang('L_YES'),'0'=>$plxPlugin->getLang('L_NO')),$parms['jquery']) ?>
		</p>
		<p>
			<label for="id_width"><?php $plxPlugin->lang('L_WIDTH') ?></label>
			<?php plxUtils::printInput('width',$parms['width'],'text','4-4') ?>
		</p>
		<p>
			<label for="id_height"><?php $plxPlugin->lang('L_HEIGHT') ?></label>
			<?php plxUtils::printInput('height',$parms['height'],'text','4-4') ?>
		</p>
		<p>
			<label for="id_spw"><?php $plxPlugin->lang('L_SPW') ?></label>
			<?php plxUtils::printInput('spw',$parms['spw'],'text','2-2') ?>
		</p>
		<p>
			<label for="id_sph"><?php $plxPlugin->lang('L_SPH') ?></label>
			<?php plxUtils::printInput('sph',$parms['sph'],'text','2-2') ?>
		</p>
		<p>
			<label for="id_delay"><?php $plxPlugin->lang('L_DELAY') ?></label>
			<?php plxUtils::printInput('delay',$parms['delay'],'text','4-4') ?>
		</p>
		<p>
			<label for="id_sdelay"><?php $plxPlugin->lang('L_SDELAY') ?></label>
			<?php plxUtils::printInput('sDelay',$parms['sDelay'],'text','4-4') ?>
		</p>
		<p>
			<label for="id_opacity"><?php $plxPlugin->lang('L_OPACITY') ?></label>
			<?php plxUtils::printInput('opacity',$parms['opacity'],'text','4-4') ?>
		</p>
		<p>
			<label for="id_titleSpeed"><?php $plxPlugin->lang('L_TITLESPEED') ?></label>
			<?php plxUtils::printInput('titleSpeed',$parms['titleSpeed'],'text','4-4') ?>
		</p>
		<p>
			<label for="id_effect"><?php $plxPlugin->lang('L_EFFECT') ?></label>
			<?php plxUtils::printSelect('effect',array('random'=>$plxPlugin->getLang('L_RANDOM'),'swirl'=>$plxPlugin->getLang('L_SWIRL'),'rain'=>$plxPlugin->getLang('L_RAIN'),'straight'=>$plxPlugin->getLang('L_STRAIGHT')),$parms['effect']) ?>
		</p>
		<p>
			<label for="id_navigation"><?php $plxPlugin->lang('L_NAVIGATION') ?></label>
			<?php plxUtils::printSelect('navigation',array('1'=>$plxPlugin->getLang('L_YES'),'0'=>$plxPlugin->getLang('L_NO')),$parms['navigation']) ?>
		</p>
		<p>
			<label for="id_links"><?php $plxPlugin->lang('L_LINKS') ?></label>
			<?php plxUtils::printSelect('links',array('1'=>$plxPlugin->getLang('L_YES'),'0'=>$plxPlugin->getLang('L_NO')),$parms['links']) ?>
		</p>
		<p>
			<label for="id_hoverPause"><?php $plxPlugin->lang('L_HOVERPAUSE') ?></label>
			<?php plxUtils::printSelect('hoverPause',array('1'=>$plxPlugin->getLang('L_YES'),'0'=>$plxPlugin->getLang('L_NO')),$parms['hoverPause']) ?>
		</p>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>