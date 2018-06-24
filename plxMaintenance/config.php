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
	$plxPlugin->setParam('mode_maintenance', $_POST['mode_maintenance'], 'numeric');
	$plxPlugin->setParam('ip_maintenance', $_POST['ip_maintenance'], 'string');
	$plxPlugin->setParam('page_maintenance', $_POST['content'], 'cdata');
	$plxPlugin->setParam('link_admin', $_POST['link_admin'], 'numeric');	
	$plxPlugin->saveParams();
	
	header('Location: parametres_plugin.php?p=plxMaintenance');
	exit;
}

$mode_maintenance =  $plxPlugin->getParam('mode_maintenance')=='' ? 0 : $plxPlugin->getParam('mode_maintenance');
$ip_maintenance =  $plxPlugin->getParam('ip_maintenance')=='' ? '' : $plxPlugin->getParam('ip_maintenance');
$page_maintenance =  $plxPlugin->getParam('page_maintenance')=='' ? $plxPlugin->getLang('L_M_CONTENU') : $plxPlugin->getParam('page_maintenance');
$link_admin =  $plxPlugin->getParam('link_admin')=='' ? 0 : $plxPlugin->getParam('link_admin');

?>
<?php if ($plxAdmin->aConf['version'] < '5.4') { ?>
<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php } ?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxMaintenance/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxMaintenance/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_M_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_M_TAB02') ?></span>
</div>
<form class="inline-form" id="form_plxMaintenance" action="parametres_plugin.php?p=plxMaintenance" method="post">
<div class="content_tab" id="content_tab_01">		
<div class="col sml-12 med-7 lrg-8">
	<fieldset>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_mode_maintenance"><?php echo $plxPlugin->lang('L_M_MODE_MAINTENANCE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('mode_maintenance',array('1'=>L_YES,'0'=>L_NO),$mode_maintenance); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_ip_maintenance"><?php $plxPlugin->lang('L_M_IP_MAINTENANCE') ?>&nbsp;:</label>
			<?php plxUtils::printInput('ip_maintenance',$ip_maintenance,'text','32-32') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_M_HELP_IP_MAINTENANCE') ?></span></a>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_link_admin"><?php echo $plxPlugin->lang('L_M_LINK_ADMIN') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('link_admin',array('1'=>L_YES,'0'=>L_NO),$link_admin); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
				<label for="id_page_maintenance"><?php $plxPlugin->lang('L_M_PAGE_MAINTENANCE') ?>&nbsp;:&nbsp;</label>
				<?php plxUtils::printArea('content',plxUtils::strCheck($page_maintenance),140,15,false,'full-width'); ?>
			</div>
		</div>			
		</div>
		</div>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_M_SAVE') ?>" />
		</p>
	</fieldset>

</form>
<div class="content_tab" id="content_tab_02">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->maintenance->UpdatePlugin('plxMaintenance'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxMaintenance/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS; ?>plxMaintenance/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxMaintenance/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxMaintenance/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>