<?php if (!defined('PLX_ROOT')) exit; ?>
<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if (!empty($_POST)) {
	
	$plxPlugin->setParam('icon_Select', $_POST['icon_Select'], 'string');
	$plxPlugin->setParam('upCustomButton', $_POST['upCustomButton'], 'string');
	$plxPlugin->setParam('downCustomButton', $_POST['downCustomButton'], 'string');	
	$plxPlugin->setParam('iconSize', $_POST['iconSize'], 'numeric');
	$plxPlugin->setParam('scrollSpeed', $_POST['scrollSpeed'], 'numeric');	
	$plxPlugin->setParam('whichButton', $_POST['whichButton'], 'numeric');
	$plxPlugin->setParam('posButtonV', $_POST['posButtonV'], 'string');	
	$plxPlugin->setParam('posButtonH', $_POST['posButtonH'], 'string');		
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxScrollToTopBottom');
	exit;
}
$icon_Select =  $plxPlugin->getParam('icon_Select')=='' ? 1 : $plxPlugin->getParam('icon_Select');
$upCustomButton = $plxPlugin->getParam('upCustomButton')=='' ? '' : $plxPlugin->getParam('upCustomButton');
$downCustomButton = $plxPlugin->getParam('downCustomButton')=='' ? '' : $plxPlugin->getParam('downCustomButton');
$iconSize = $plxPlugin->getParam('iconSize')=='' ? 32 : $plxPlugin->getParam('iconSize');
$scrollSpeed = $plxPlugin->getParam('scrollSpeed')=='' ? 1000 : $plxPlugin->getParam('scrollSpeed');
$whichButton = $plxPlugin->getParam('whichButton')=='' ? 1 : $plxPlugin->getParam('whichButton');
$posButtonV = $plxPlugin->getParam('posButtonV')=='' ? 'left' : $plxPlugin->getParam('posButtonV');
$posButtonH = $plxPlugin->getParam('posButtonH')=='' ? 'middle' : $plxPlugin->getParam('posButtonH');
?>

<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxScrollToTopBottom/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxScrollToTopBottom/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_STT_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_STT_TAB02') ?></span>
</div>
<form id="form_plxScrollToTopBottom" action="parametres_plugin.php?p=plxScrollToTopBottom" method="post">
<div class="inline-form">
	<div class="content_tab" id="content_tab_01">
	<fieldset>
		<div><?php echo $plxPlugin->lang('L_STTB_CHOOSE_ICON') ?>&nbsp;:<div>
		<div class="grid">
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_1" name="icon_Select" value="1" <?php if($icon_Select == 1) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/1_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/1_d.png" > '; ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_5" name="icon_Select" value="5" <?php if($icon_Select == 5) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/5_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/5_d.png" > '; ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_9" name="icon_Select" value="9" <?php if($icon_Select == 9) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/9_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/9_d.png" > '; ?>
		</div>	
		<div class="grid">
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_2" name="icon_Select" value="2" <?php if($icon_Select == 2) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/2_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/2_d.png" > '; ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_6" name="icon_Select" value="6" <?php if($icon_Select == 6) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/6_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/6_d.png" > '; ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_10" name="icon_Select" value="10" <?php if($icon_Select == 10) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/10_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/10_d.png" > '; ?>
		</div>	
		<div class="grid">
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_3" name="icon_Select" value="3" <?php if($icon_Select == 3) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/3_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/3_d.png" > '; ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_7" name="icon_Select" value="7" <?php if($icon_Select == 7) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/7_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/7_d.png" > '; ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_11" name="icon_Select" value="11" <?php if($icon_Select == 11) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/11_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/11_d.png" > '; ?>
		</div>	
		<div class="grid">
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_4" name="icon_Select" value="4" <?php if($icon_Select == 4) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/4_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/4_d.png" > '; ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_8" name="icon_Select" value="8" <?php if($icon_Select == 8) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/8_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/8_d.png" > '; ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_12" name="icon_Select" value="12" <?php if($icon_Select == 12) { echo('checked="checked"'); } ?> />
			<?php echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/12_u.png" > '; echo '<img src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/12_d.png" > '; ?>
		</div><br/>	
		<div class="grid">
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="icon_Select_13" name="icon_Select" value="13" <?php if($icon_Select == 13) { echo('checked="checked"'); } ?> />
			<span for="icon_Select_13"><?php echo $plxPlugin->lang('L_STTB_CUSTOM_ICON') ?>&nbsp;:</span>
		</div><br/>	
		<div class="grid gridthumb">
				<div class="col sml-12 med-7 lrg-8">
					<label for="id_upCustomButton">
						<?php $plxPlugin->lang('L_STTB_UP_BUTTON') ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_baFooter" href="javascript:void(0)" onclick="mediasManager.openPopup('id_upCustomButton', true)" style="outline:none; text-decoration: none">+</a>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_STTB_HELP_BUTTON') ?></span></a>
					</label>
					<?php plxUtils::printInput('upCustomButton',plxUtils::strCheck($upCustomButton),'text','255-255',false,'full-width','','onkeyup="refreshImg(this.value)"'); ?>
				</div>
			</div>	
			<div class="grid gridthumb">
				<div class="col sml-12 med-7 lrg-8">
					<label for="id_downCustomButton">
						<?php $plxPlugin->lang('L_STTB_DOWN_BUTTON') ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_baFirstSide" href="javascript:void(0)" onclick="mediasManager.openPopup('id_downCustomButton', true)" style="outline:none; text-decoration: none">+</a>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_STTB_HELP_BUTTON') ?></span></a>
					</label>
					<?php plxUtils::printInput('downCustomButton',plxUtils::strCheck($downCustomButton),'text','255-255',false,'full-width','','onkeyup="refreshImg(this.value)"'); ?>
				</div>
			</div>
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_iconSize"><?php echo $plxPlugin->lang('L_STTB_ICON_SIZE') ?>&nbsp;:</label>
				<?php plxUtils::printInput('iconSize',$iconSize,'numeric','3-6') ?>
				<a class="hint"><span><?php $plxPlugin->lang('L_STTB_HELP_ICON_SIZE') ?></span></a>
			</div>
		</div>			
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_scrollSpeed"><?php echo $plxPlugin->lang('L_STTB_SCROLL_SPEED') ?>&nbsp;:</label>
				<?php plxUtils::printInput('scrollSpeed',$scrollSpeed,'numeric','6-6') ?>
				<a class="hint"><span><?php $plxPlugin->lang('L_STTB_HELP_SCROLL_SPEED') ?></span></a>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">		
			<label for="id_whichButton"><?php echo $plxPlugin->lang('L_STTB_WHICH_BUTTON') ?>&nbsp;?</label>
			<?php plxUtils::printSelect('whichButton',array('1'=>$plxPlugin->getlang('L_STTB_BOTH_BUTTON'),'2'=>$plxPlugin->getlang('L_STTB_ONLY_UP_BOTTON'),'3'=>$plxPlugin->getlang('L_STTB_ONLY_DOWN_BOTTON')),$whichButton); ?>	
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">		
			<label for="id_posButtonV"><?php echo $plxPlugin->lang('L_STTB_POSITION_BUTTONS_V') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('posButtonV',array('left'=>$plxPlugin->getlang('L_STTB_LEFT'),'right'=>$plxPlugin->getlang('L_STTB_RIGHT')),$posButtonV); ?>	
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">		
			<label for="id_posButtonH"><?php echo $plxPlugin->lang('L_STTB_POSITION_BUTTONS_H') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('posButtonH',array('middle'=>$plxPlugin->getlang('L_STTB_MIDDLE'),'bottom'=>$plxPlugin->getlang('L_STTB_BOTTOM')),$posButtonH); ?>	
			</div>
		</div>			
</div>	
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_STTB_SAVE') ?>" />
		</p>	
		
	</fieldset>
	</div>
</form>
<div class="content_tab" id="content_tab_02">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->scrolltotopbottom->UpdatePlugin('plxScrollToTopBottom'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS.get_class($plxPlugin); ?>/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS.get_class($plxPlugin); ?>/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS.get_class($plxPlugin); ?>/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS.get_class($plxPlugin); ?>/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>