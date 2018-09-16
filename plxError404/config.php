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
	$plxPlugin->setParam('to', $plxAdmin->aUsers['001']['email'], 'string');
	$plxPlugin->setParam('person', $_POST['person'], 'numeric');
	$plxPlugin->setParam('supervision', $_POST['supervision'], 'numeric');
	$plxPlugin->setParam('warningMsg', $_POST['content'], 'cdata');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxError404');
	exit;
}

$to = $plxPlugin->getParam('to')=='' ? $plxAdmin->aUsers['001']['email']:$plxPlugin->getParam('to');
$person =  $plxPlugin->getParam('person')=='' ? 0 : $plxPlugin->getParam('person');
$warningMsg =  $plxPlugin->getParam('warningMsg')=='' ? '' : $plxPlugin->getParam('warningMsg');
$supervision =  $plxPlugin->getParam('supervision')=='' ? 0 : $plxPlugin->getParam('supervision');

?>
<?php if ($plxAdmin->aConf['version'] < '5.4') { ?>
<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php } ?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxError404/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxError404/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_E4_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_E4_TAB02') ?></span>	
</div>
<div class="content_tab" id="content_tab_01">
<form class="inline-form" id="form_plxError404" action="parametres_plugin.php?p=plxError404" method="post">
	<?php
	if(function_exists('mail')) {
		echo '<span style="color:green"><strong>'.$plxPlugin->getLang('L_E4_MAIL_AVAILABLE').'</strong></span>';
	} else {
		echo '<span style="color:#ff0000"><strong>'.$plxPlugin->getLang('L_E4_MAIL_NOT_AVAILABLE').'</strong></span>';
	}
	?>
	<div>&nbsp;</div>
	<fieldset>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_supervision"><?php echo $plxPlugin->lang('L_E4_SUPERVISION') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('supervision',array('1'=>L_YES,'0'=>L_NO),$supervision); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="to"><?php $plxPlugin->lang('L_E4_TO') ?>&nbsp;:</label>
			<?php plxUtils::printInput('to',$to,'text','32-255') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_E4_HELP_TO')?></span></a>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="person"><?php $plxPlugin->lang('L_E4_PERSON') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('person',array('1'=>L_YES,'0'=>L_NO),$person); ?>
			</div>
		</div>			
		<div class="grid">
			<div class="col sml-12">
			<label for="id_warningMsg"><?php echo $plxPlugin->lang('L_E4_WARNINGMSG') ?>&nbsp;:</label>
			<?php plxUtils::printArea('content',plxUtils::strCheck($warningMsg),35,8,false,'full-width'); ?>
			</div>
		</div>		
		</div>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_E4_SAVE') ?>" />
		</p>
	</fieldset>
</form>
<div class="content_tab" id="content_tab_02">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->error404->UpdatePlugin('plxError404'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxError404/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS; ?>plxError404/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxError404/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxError404/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>


