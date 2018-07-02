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
	$plxPlugin->setParam('dppItem_Name', $_POST['dppItem_Name'], 'string');
	$plxPlugin->setParam('dppEmail', $_POST['dppEmail'], 'string');
	$plxPlugin->setParam('dppTextTop', $_POST['dppTextTop'], 'string');	
	$plxPlugin->setParam('dppTextBottom', $_POST['dppTextBottom'], 'string');		
	$plxPlugin->setParam('dppOptDonation', $_POST['dppOptDonation'], 'string');
	$plxPlugin->setParam('dppItem_Price', $_POST['dppItem_Price'], 'numeric');	
	$plxPlugin->setParam('dppDevise', $_POST['dppDevise'], 'string');	
	$plxPlugin->setParam('dppImgButton', $_POST['dppImgButton'], 'string');
	$plxPlugin->setParam('dppCustomButton', $_POST['dppCustomButton'], 'string');	
	$plxPlugin->setParam('dppActTextTop', $_POST['dppActTextTop'], 'numeric');	
	$plxPlugin->setParam('dppActTextBottom', $_POST['dppActTextBottom'], 'numeric');	
	$plxPlugin->setParam('dppOpens', $_POST['dppOpens'], 'string');	
	$plxPlugin->setParam('dppAlign', $_POST['dppAlign'], 'string');	
	$plxPlugin->setParam('dppReturnPage', $_POST['dppReturnPage'], 'string');	
	$plxPlugin->setParam('dppReturnMethod', $_POST['dppReturnMethod'], 'numeric');
	$plxPlugin->setParam('dppAccountType', $_POST['dppAccountType'], 'numeric');	
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxDonsPayPal');
	exit;
}

$dppItem_Name =  $plxPlugin->getParam('dppItem_Name')=='' ? $plxAdmin->aConf['title'] : $plxPlugin->getParam('dppItem_Name');
$dppEmail =  $plxPlugin->getParam('dppEmail')=='' ? $plxAdmin->aUsers['001']['email'] : $plxPlugin->getParam('dppEmail');
$dppTextTop =  $plxPlugin->getParam('dppTextTop')=='' ? $plxPlugin->getlang('L_DPP_TEXTTOPDEFAULT') : $plxPlugin->getParam('dppTextTop');
$dppTextBottom =  $plxPlugin->getParam('dppTextBottom')=='' ? $plxPlugin->getlang('L_DPP_TEXTBOTTOMDEFAULT') : $plxPlugin->getParam('dppTextBottom');
$dppOptDonation =  $plxPlugin->getParam('dppOptDonation')=='' ? 'flexible' : $plxPlugin->getParam('dppOptDonation');
$dppItem_Price =  $plxPlugin->getParam('dppItem_Price')=='' ? 5 : $plxPlugin->getParam('dppItem_Price');
$dppDevise =  $plxPlugin->getParam('dppDevise')=='' ? 'EUR' : $plxPlugin->getParam('dppDevise');
$dppImgButton =  $plxPlugin->getParam('dppImgButton')=='' ? 'btn_donate_SM' : $plxPlugin->getParam('dppImgButton');
$dppCustomButton =  $plxPlugin->getParam('dppCustomButton')=='' ? '' : $plxPlugin->getParam('dppCustomButton');
$dppActTextTop =  $plxPlugin->getParam('dppActTextTop')=='' ? '1' : $plxPlugin->getParam('dppActTextTop');
$dppActTextBottom =  $plxPlugin->getParam('dppActTextBottom')=='' ? '1' : $plxPlugin->getParam('dppActTextBottom');
$dppOpens =  $plxPlugin->getParam('dppOpens')=='' ? '_top' : $plxPlugin->getParam('dppOpens');
$dppAlign =  $plxPlugin->getParam('dppAlign')=='' ? 'center' : $plxPlugin->getParam('dppAlign');
$dppReturnMethod =  $plxPlugin->getParam('dppReturnMethod')=='' ? 0 : $plxPlugin->getParam('dppReturnMethod');
$dppAccountType =  $plxPlugin->getParam('dppAccountType')=='' ? 0 : $plxPlugin->getParam('dppAccountType');
$dppReturnPage =  $plxPlugin->getParam('dppReturnPage');

if ($dppImgButton == 'custom') {
	$imgUrl = PLX_ROOT.$dppCustomButton;
} else {
	$imgUrl = PLX_PLUGINS.'plxDonsPayPal/img/'.$plxAdmin->aConf['default_lang'].'/'.$dppImgButton.'.png';	
}

?>
<?php if ($plxAdmin->aConf['version'] < '5.4') { ?>
<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php } ?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxDonsPayPal/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxDonsPayPal/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_DPP_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_DPP_TAB02') ?></span>
	<span class="tab_0 tab" id="tab_03" onclick="javascript:change_tab('03');"><?php $plxPlugin->lang('L_DPP_TAB03') ?></span>
</div>
<div class="content_tab" id="content_tab_01">
<form class="inline-form" id="form_plxDonsPayPal" action="parametres_plugin.php?p=plxDonsPayPal" method="post">
	<fieldset>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppAccountType"><?php $plxPlugin->lang('L_DPP_ACCOUNTTYPE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('dppAccountType',array('0'=>$plxPlugin->getlang('L_DPP_LIVE'),'1'=>$plxPlugin->getlang('L_DPP_SANDBOX')),$dppAccountType); ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_DPP_HELP_ACCOUNTTYPE') ?></span></a>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppEmail"><?php $plxPlugin->lang('L_DPP_EMAIL') ?>&nbsp;:</label>
			<?php plxUtils::printInput('dppEmail',$dppEmail,'text','32-32') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_DPP_HELP_EMAIL') ?></span></a>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppNameService"><?php $plxPlugin->lang('L_DPP_ITEM_NAME') ?>&nbsp;:</label>
			<?php plxUtils::printInput('dppItem_Name',$dppItem_Name,'text','32-32') ?>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppDevise"><?php $plxPlugin->lang('L_DPP_DEVISE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('dppDevise',array('EUR'=>'EUR','THB'=>'THB','DKK'=>'DKK','NOK'=>'NOK','SEK'=>'SEK','CZK'=>'CZK','AUD'=>'AUD','CAD'=>'CAD','HKD'=>'HKD','SGD'=>'SGD','NZD'=>'NZD','USD'=>'USD','HUF'=>'HUF','CHF'=>'CHF','GBP'=>'GBP','TWD'=>'TWD','ILS'=>'ILS','MXN'=>'MXN','PHP'=>'PHP','RUB'=>'RUB','BRL'=>'BRL','JPY'=>'JPY','PLN'=>'PLN'),$dppDevise); ?>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppOptDonation"><?php echo $plxPlugin->lang('L_DPP_OPTDONATION') ?>&nbsp;:</label>
			<select id="dppOptDonation" onchange="if(this.selectedIndex == 1) document.getElementById('fixedDonation').style.display = 'block';
				else document.getElementById('fixedDonation').style.display = 'none';" name="dppOptDonation">
				<?php
				foreach(array('flexible'=>$plxPlugin->getlang('L_DPP_FLEXIBLE'),'fixed'=>$plxPlugin->getlang('L_DPP_FIXED')) as $c=>$d) {
					if($c == $dppOptDonation)
						echo "\t".'<option value="'.$c.'" selected="selected">'.$d.'</option>'."\n";
					else
						echo "\t".'<option value="'.$c.'">'.$d.'</option>'."\n";
				}
				?>
			</select>
			</div>
		</div>
		<?php
			if ($dppOptDonation == 'fixed') {
				?>
				<div id="fixedDonation" style="display:block" >
				<?php
			} else {
				?>
				<div id="fixedDonation" style="display:none" >
				<?php
			}
			?>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppItem_Price"><?php $plxPlugin->lang('L_DPP_ITEM_PRICE') ?>&nbsp;:</label>
			<?php plxUtils::printInput('dppItem_Price',$dppItem_Price,'text','32-32') ?>
			</div>
		</div>		
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppOptDonation"><?php echo $plxPlugin->lang('L_DPP_IMGBUTTON') ?>&nbsp;:</label>
			<select id="dppImgButton" onchange="if(this.selectedIndex == 3) document.getElementById('imgcustom').style.display = 'block';
				else document.getElementById('imgcustom').style.display = 'none';" name="dppImgButton">
				<?php
				foreach(array('btn_donate_SM'=>$plxPlugin->getlang('L_DPP_BTN_DONATE_SM'),'btn_donate_LG'=>$plxPlugin->getlang('L_DPP_BTN_DONATE_LG'),'btn_donateCC_LG'=>$plxPlugin->getlang('L_DPP_BTN_DONATECC_LG'),'custom'=>$plxPlugin->getlang('L_DPP_BTN_DONATE_CUSTOM')) as $c=>$d) {
					if($c == $dppImgButton)
						echo "\t".'<option value="'.$c.'" selected="selected">'.$d.'</option>'."\n";
					else
						echo "\t".'<option value="'.$c.'">'.$d.'</option>'."\n";
				}
				?>
			</select>
			</div>
		</div>
		<?php
			if ($dppImgButton == 'custom') {
				?>
				<div id="imgcustom" style="display:block" >
				<?php
			} else {
				?>
				<div id="imgcustom" style="display:none" >
				<?php
			}
			?>		
		<div class="grid gridthumb">
				<div class="col sml-12">
					<label for="id_dppImgButton">
						<?php $plxPlugin->lang('L_DPP_CUSTOMBUTTON') ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_dppCustomButton" href="javascript:void(0)" onclick="mediasManager.openPopup('id_dppCustomButton', true)" style="outline:none; text-decoration: none"><img src="<?php echo PLX_PLUGINS ?>plxDonsPayPal/img/gfichiers.png" height="16" width="16" style="vertical-align: middle"></a>
					</label>
					<?php plxUtils::printInput('dppCustomButton',plxUtils::strCheck($dppCustomButton),'text','64-255',false,'full-width'); ?>
					<a class="hint"><span><?php $plxPlugin->lang('L_DPP_HELP_CUSTOMBUTTON') ?></span></a>
				</div>
		</div>
		</div>
			<?php
			#$imgUrl = PLX_ROOT.$dppCustomButton;
			if(is_file($imgUrl)) {
				echo '<div id="id_dppCustomButton_img"><img src="'.$imgUrl.'" alt="" /></div>';
			} else {
				echo '<div id="id_dppCustomButton_img"></div>';
				}
			?>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppText"><?php $plxPlugin->lang('L_DPP_TEXTTOP') ?>&nbsp;:</label>
			<?php plxUtils::printInput('dppTextTop',$dppTextTop,'text','32-32') ?>&nbsp;&nbsp;&nbsp;
			<?php echo $plxPlugin->lang('L_DPP_ACTTEXTTOP') ?>&nbsp;:
			<?php plxUtils::printSelect('dppActTextTop',array('1'=>L_YES,'0'=>L_NO),$dppActTextTop); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppText"><?php $plxPlugin->lang('L_DPP_TEXTBOTTOM') ?>&nbsp;:</label>
			<?php plxUtils::printInput('dppTextBottom',$dppTextBottom,'text','32-32') ?>&nbsp;&nbsp;&nbsp;
			<?php echo $plxPlugin->lang('L_DPP_ACTTEXTBOTTOM') ?>&nbsp;:
			<?php plxUtils::printSelect('dppActTextBottom',array('1'=>L_YES,'0'=>L_NO),$dppActTextBottom); ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppOpens"><?php $plxPlugin->lang('L_DPP_OPENS') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('dppOpens',array('_top'=>$plxPlugin->getlang('L_DPP_TOP'),'_target'=>$plxPlugin->getlang('L_DPP_TARGET')),$dppOpens); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppAlign"><?php $plxPlugin->lang('L_DPP_ALIGN') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('dppAlign',array('left'=>$plxPlugin->getlang('L_DPP_LEFT'),'center'=>$plxPlugin->getlang('L_DPP_CENTER'),'right'=>$plxPlugin->getlang('L_DPP_RIGHT')),$dppAlign); ?>
			</div>
		</div>
		</div>	
		<div class="content_tab" id="content_tab_02">
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppReturnPage"><?php $plxPlugin->lang('L_DPP_RETURNPAGE') ?>&nbsp;:</label>
			<?php plxUtils::printInput('dppReturnPage',$dppReturnPage,'text','64-64') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_DPP_HELP_RETURNPAGE') ?></span></a>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_dppReturnMethod"><?php $plxPlugin->lang('L_DPP_RETURNMETHOD') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('dppReturnMethod',array('0'=>'GET method (default)','1'=>'GET method, no variables','2'=>'POST method'),$dppReturnMethod); ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_DPP_HELP_RETURNMETHOD') ?></span></a>
			</div>
		</div>	
		</div>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_DPP_SAVE') ?>" />
		</p>
	</fieldset>
</form>
<div class="content_tab" id="content_tab_03">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->donspaypal->UpdatePlugin('plxDonsPayPal'); ?>
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