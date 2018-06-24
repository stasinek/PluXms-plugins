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

$init = true;

if(!empty($_POST)) {
	
	$valid = true;
	
	# Vérification de la validité de la date de début
	if(!plxDate::checkDate($_POST['date_start_day'],$_POST['date_start_month'],$_POST['date_start_year'],$_POST['date_start_time'])) {
		$valid = plxMsg::Error($plxPlugin->getLang("L_PP_INVADIDDATE")) AND $valid;
	}
		
	# Vérification de la validité de la date de fin
	if(!plxDate::checkDate($_POST['date_end_day'],$_POST['date_end_month'],$_POST['date_end_year'],$_POST['date_end_time'])) {
		$valid = plxMsg::Error($plxPlugin->getLang("L_PP_INVADIDDATE")) AND $valid;
	}
	
	$plxPlugin->setParam('widthPopup', $_POST['widthPopup'], 'numeric');	
	$plxPlugin->setParam('BgColorPopup', $_POST['BgColorPopup'], 'string');
	$plxPlugin->setParam('borderColorPopup', $_POST['borderColorPopup'], 'string');
	$plxPlugin->setParam('borderSizeColorPopup', $_POST['borderSizeColorPopup'], 'numeric');
	$plxPlugin->setParam('borderRadiusPopup', $_POST['borderRadiusPopup'], 'numeric');	
	$plxPlugin->setParam('closeAutoDelay', $_POST['closeAutoDelay'], 'numeric');
	$plxPlugin->setParam('onlySession', $_POST['onlySession'], 'numeric');
	$plxPlugin->setParam('contentPopup', $_POST['content'], 'cdata');	
	$plxPlugin->setParam('inFinite', $_POST['inFinite'], 'string');		
	$plxPlugin->setParam('date_start', $_POST['date_start_year'].$_POST['date_start_month'].$_POST['date_start_day'].substr(str_replace(':','',$_POST['date_start_time']),0,4), 'cdata');
	$plxPlugin->setParam('date_end', $_POST['date_end_year'].$_POST['date_end_month'].$_POST['date_end_day'].substr(str_replace(':','',$_POST['date_end_time']),0,4), 'cdata');
	
	if($valid) {
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxPopupIndex');
	exit;
	} else {
		$widthPopup =  $_POST['widthPopup'];
		$BgColorPopup =  $_POST['BgColorPopup'];
		$borderColorPopup =  $_POST['borderColorPopup'];
		$borderSizeColorPopup =  $_POST['borderSizeColorPopup'];
		$borderRadiusPopup =  $_POST['borderRadiusPopup'];
		$closeAutoDelay =  $_POST['closeAutoDelay'];
		$onlySession =  $_POST['onlySession'];
		$contentPopup =  $_POST['content'];
		$inFinite = $_POST['inFinite'];		
		$date_start['day'] = $_POST['date_start_day'];
		$date_start['month'] = $_POST['date_start_month'];
		$date_start['year'] = $_POST['date_start_year'];
		$date_start['time'] = $_POST['date_start_time'];
		$date_end['day'] = $_POST['date_end_day'];
		$date_end['month'] = $_POST['date_end_month'];
		$date_end['year'] = $_POST['date_end_year'];
		$date_end['time'] = $_POST['date_end_time'];		
		$init = false;
	}
}
if ($init) {
	
$date_now = date('YmdHi');

$widthPopup =  $plxPlugin->getParam('widthPopup')=='' ? 500 : $plxPlugin->getParam('widthPopup');
$BgColorPopup =  $plxPlugin->getParam('BgColorPopup')=='' ? 'FFFFFF' : $plxPlugin->getParam('BgColorPopup');
$borderColorPopup =  $plxPlugin->getParam('borderColorPopup')=='' ? '60C4EA' : $plxPlugin->getParam('borderColorPopup');
$borderSizeColorPopup =  $plxPlugin->getParam('borderSizeColorPopup')=='' ? 1 : $plxPlugin->getParam('borderSizeColorPopup');
$borderRadiusPopup =  $plxPlugin->getParam('borderRadiusPopup')=='' ? 4 : $plxPlugin->getParam('borderRadiusPopup');
$closeAutoDelay =  $plxPlugin->getParam('closeAutoDelay')=='' ? 0 : $plxPlugin->getParam('closeAutoDelay');
$onlySession =  $plxPlugin->getParam('onlySession')=='' ? 0 : $plxPlugin->getParam('onlySession');
$contentPopup =  $plxPlugin->getParam('contentPopup')=='' ? $plxPlugin->getLang('L_PI_YOURMESSAGE') : $plxPlugin->getParam('contentPopup');
$inFinite =  $plxPlugin->getParam('inFinite')=='' ? 1 : $plxPlugin->getParam('inFinite');
$date_S =  $plxPlugin->getParam('date_start')=='' ? $date_now : $plxPlugin->getParam('date_start');
$date_E =  $plxPlugin->getParam('date_end')=='' ? $date_now : $plxPlugin->getParam('date_end');
$date_start = plxDate::date2Array($date_S);
$date_end = plxDate::date2Array($date_E);
}
?>
<?php if ($plxAdmin->aConf['version'] < '5.4') { ?>
<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php } ?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxPopupIndex/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxPopupIndex/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_PI_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_PI_TAB02') ?></span>	
	<span class="tab_0 tab" id="tab_03" onclick="javascript:change_tab('03');"><?php $plxPlugin->lang('L_PI_TAB03') ?></span>
</div>
<div class="content_tab" id="content_tab_01">
<form class="inline-form" id="form_plxPopupIndex" action="parametres_plugin.php?p=plxPopupIndex" method="post">
	<fieldset>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_widthPopup"><?php $plxPlugin->lang('L_PI_WIDTHPOPUP') ?>&nbsp;:</label>
			<?php plxUtils::printInput('widthPopup',$widthPopup,'numeric','4-6') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_PI_HELP_POPUP_PX') ?></span></a>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_BgColorPopup"><?php $plxPlugin->lang('L_PI_BGCOLORPOPUP') ?>&nbsp;:</label>
			<?php plxUtils::printInput('BgColorPopup',$BgColorPopup,'text','6-6',false,'color') ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_borderColorPopup"><?php $plxPlugin->lang('L_PI_BORDERCOLORPOPUP') ?>&nbsp;:</label>
			<?php plxUtils::printInput('borderColorPopup',$borderColorPopup,'text','6-6',false,'color') ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_borderSizeColorPopup"><?php $plxPlugin->lang('L_PI_BORDERSIZECOLORPOPUP') ?>&nbsp;:</label>
			<?php plxUtils::printInput('borderSizeColorPopup',$borderSizeColorPopup,'numeric','2-5') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_PI_HELP_POPUP_PX') ?></span></a>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_borderRadiusPopup"><?php $plxPlugin->lang('L_PI_BORDERRADIUSPOPUP') ?>&nbsp;:</label>
			<?php plxUtils::printInput('borderRadiusPopup',$borderRadiusPopup,'numeric','2-5') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_PI_HELP_POPUP_PX') ?></span></a>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">
			<label for="id_closeAutoDelay"><?php $plxPlugin->lang('L_PI_CLOSEAUTODELAY') ?>&nbsp;:</label>
			<?php plxUtils::printInput('closeAutoDelay',$closeAutoDelay,'numeric','2-5') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_PI_HELP_DELAY') ?></span></a>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">
			<label for="id_onlySession"><?php echo $plxPlugin->lang('L_PI_ONLYSESSION') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('onlySession',array('1'=>L_YES,'0'=>L_NO),$onlySession); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_inFinite"><?php echo $plxPlugin->lang('L_PI_INFINITE') ?>&nbsp;:</label>
			<select id="inFinite" onchange="if(this.selectedIndex == 1) document.getElementById('zdate').style.display = 'block';
				else document.getElementById('zdate').style.display = 'none';" name="inFinite">
				<?php
				foreach(array('1'=>L_YES,'0'=>L_NO) as $c=>$d) {
					if($c == $inFinite)
						echo "\t".'<option value="'.$c.'" selected="selected">'.$d.'</option>'."\n";
					else
						echo "\t".'<option value="'.$c.'">'.$d.'</option>'."\n";
				}
				?>
			</select>
			</div>
		</div>		
		<?php
			if ($inFinite == '0') {
				?>
				<div id="zdate" style="display:block" >
				<?php
			} else {
				?>
				<div id="zdate" style="display:none" >
				<?php
			}
			?>
			<div class="grid">
				<div class="col sml-12">
					<div class="inline-form start">
					<label><?php echo $plxPlugin->lang('L_PI_DATESTART') ?>&nbsp;:</label>
						<?php plxUtils::printInput('date_start_day',$date_start['day'],'text','2-2',false,'day'); ?>
						<?php plxUtils::printInput('date_start_month',$date_start['month'],'text','2-2',false,'month'); ?>
						<?php plxUtils::printInput('date_start_year',$date_start['year'],'text','2-4',false,'year'); ?>
						<?php plxUtils::printInput('date_start_time',$date_start['time'],'text','2-5',false,'time'); ?>
						<a class="ico_cal" href="javascript:void(0)" onclick="dateNow('date_start', <?php echo date('Z') ?>); return false;" title="<?php L_NOW; ?>">
							<img src="theme/images/date.png" alt="calendar" />
						</a>
					</div>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<div class="inline-form end">
					<label><?php echo $plxPlugin->lang('L_PI_DATEEND') ?>&nbsp;:</label>
						<?php plxUtils::printInput('date_end_day',$date_end['day'],'text','2-2',false,'day'); ?>
						<?php plxUtils::printInput('date_end_month',$date_end['month'],'text','2-2',false,'month'); ?>
						<?php plxUtils::printInput('date_end_year',$date_end['year'],'text','2-4',false,'year'); ?>
						<?php plxUtils::printInput('date_end_time',$date_end['time'],'text','2-5',false,'time'); ?>
						<a class="ico_cal" href="javascript:void(0)" onclick="dateNow('date_end', <?php echo date('Z') ?>); return false;" title="<?php L_NOW; ?>">
							<img src="theme/images/date.png" alt="calendar" />
						</a>
					</div>
				</div>
			</div>
			</div>			
		</div>		
		</div>
		<div class="content_tab" id="content_tab_02">
		<div class="grid">
			<div class="col sml-12">
			<p><label for="id_contentPopup"><?php echo $plxPlugin->lang('L_PI_CONTENTPOPUP') ?>&nbsp;:</label></p>
			<?php plxUtils::printArea('content',plxUtils::strCheck($contentPopup),80,20,false,'full-width'); ?>
			</div>
		</div>
		</div>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_PI_SAVE') ?>" />
		</p>
	</fieldset>
</form>
<div class="content_tab" id="content_tab_03">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->popupindex->UpdatePlugin('plxPopupIndex'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxPopupIndex/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS; ?>plxPopupIndex/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxPopupIndex/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxPopupIndex/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>

