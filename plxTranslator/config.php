<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
 
# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	$plxPlugin->setParam('alignInput', $_POST['alignInput'], 'string');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxTranslator');
	exit;
}

$alignInput =  $plxPlugin->getParam('alignInput')=='' ? 'left' : $plxPlugin->getParam('alignInput');

?>
<?php if ($plxAdmin->aConf['version'] < '5.4') { ?>
<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php } ?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxTranslator/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxTranslator/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_TL_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_TL_TAB02') ?></span>	
</div>
<div class="content_tab" id="content_tab_01">
<form class="inline-form" id="form_plxTranslator" action="parametres_plugin.php?p=plxTranslator" method="post">
	<fieldset>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_alignInput"><?php echo $plxPlugin->lang('L_PL_ALIGNINPUT') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('alignInput',array('left'=>$plxPlugin->getlang('L_TL_LEFT'),'center'=>$plxPlugin->getlang('L_TL_CENTER'),'right'=>$plxPlugin->getlang('L_TL_RIGHT')),$alignInput); ?>
			</div>
		</div>
		</div>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_TL_SAVE') ?>" />
		</p>
	</fieldset>
</form>
<div class="content_tab" id="content_tab_02">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->translator->UpdatePlugin('plxTranslator'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxTranslator/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS; ?>plxTranslator/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxTranslator/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxTranslator/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>


