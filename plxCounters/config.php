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
	$plxPlugin->setParam('totalView', $_POST['totalView'], 'numeric');
	$plxPlugin->setParam('todayView', $_POST['todayView'], 'numeric');
	$plxPlugin->setParam('connectVistor', $_POST['connectVistor'], 'numeric');
	$plxPlugin->setParam('numVistor', $_POST['numVistor'], 'numeric');
	$plxPlugin->setParam('numArticle', $_POST['numArticle'], 'numeric');
	$plxPlugin->setParam('numComment', $_POST['numComment'], 'numeric');	
	$plxPlugin->setParam('numLogFile', $_POST['numLogFile'], 'numeric');	
	$plxPlugin->setParam('time_Page', $_POST['time_Page'], 'numeric');
	$plxPlugin->setParam('time_Visitor', $_POST['time_Visitor'], 'numeric');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxCounters');
	exit;
}

$totalView =  $plxPlugin->getParam('totalView')=='' ? 1 : $plxPlugin->getParam('totalView');
$todayView =  $plxPlugin->getParam('todayView')=='' ? 1 : $plxPlugin->getParam('todayView');
$connectVistor =  $plxPlugin->getParam('connectVistor')=='' ? 1 : $plxPlugin->getParam('connectVistor');
$numVistor =  $plxPlugin->getParam('numVistor')=='' ? 1 : $plxPlugin->getParam('numVistor');
$numArticle =  $plxPlugin->getParam('numArticle')=='' ? 1 : $plxPlugin->getParam('numArticle');
$numComment =  $plxPlugin->getParam('numComment')=='' ? 1 : $plxPlugin->getParam('numComment');
$numLogFile =  $plxPlugin->getParam('numLogFile')=='' ? 0 : $plxPlugin->getParam('numLogFile');
$time_Page =  $plxPlugin->getParam('time_Page')=='' ? 30 : $plxPlugin->getParam('time_Page');
$time_Visitor =  $plxPlugin->getParam('time_Visitor')=='' ? 15 : $plxPlugin->getParam('time_Visitor');

?>
<?php if ($plxAdmin->aConf['version'] < '5.4') { ?>
<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php } ?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxCounters/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxCounters/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_CT_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_CT_TAB02') ?></span>
</div>
<div class="content_tab" id="content_tab_01">
<form class="inline-form" id="form_plxCounters" action="parametres_plugin.php?p=plxCounters" method="post">
<div class="col sml-12 med-7 lrg-8">
	<fieldset>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_totalView"><?php echo $plxPlugin->lang('L_CT_TOTALVIEW') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('totalView',array('1'=>L_YES,'0'=>L_NO),$totalView); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_todayView"><?php echo $plxPlugin->lang('L_CT_TODAYVIEW') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('todayView',array('1'=>L_YES,'0'=>L_NO),$todayView); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_connectVistor"><?php echo $plxPlugin->lang('L_CT_CONNECTVISITOR') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('connectVistor',array('1'=>L_YES,'0'=>L_NO),$connectVistor); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_numVistor"><?php echo $plxPlugin->lang('L_CT_NUMVISTOR') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('numVistor',array('1'=>L_YES,'0'=>L_NO),$numVistor); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_numArticle"><?php echo $plxPlugin->lang('L_CT_ARTICLE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('numArticle',array('1'=>L_YES,'0'=>L_NO),$numArticle); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_numComment"><?php echo $plxPlugin->lang('L_CT_COMMENT') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('numComment',array('1'=>L_YES,'0'=>L_NO),$numComment); ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_numComment"><?php echo $plxPlugin->lang('L_CT_LOGFILE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('numLogFile',array('1'=>L_YES,'0'=>L_NO),$numLogFile); ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppNameService"><?php $plxPlugin->lang('L_CT_TIME_VISITOR') ?>&nbsp;:</label>
			<?php plxUtils::printInput('time_Visitor',$time_Visitor,'text','32-32') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_CT_HELP_TIME_VISITOR') ?></span></a>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppNameService"><?php $plxPlugin->lang('L_CT_TIME_PAGE') ?>&nbsp;:</label>
			<?php plxUtils::printInput('time_Page',$time_Page,'text','32-32') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_CT_HELP_TIME_PAGE') ?></span></a>
			</div>
		</div>			
		</div>
		</div>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_CT_SAVE') ?>" />
		</p>
	</fieldset>

</form>
</div>
<div class="content_tab" id="content_tab_02">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->counters->UpdatePlugin('plxCounters'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxCounters/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS; ?>plxCounters/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxCounters/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxCounters/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>