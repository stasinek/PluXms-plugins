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

function trim_value(&$value){
  return trim($value, " \t\n\r\0\x0B/");
}

# accès à la liste des SPAM
if(!empty($_POST['listSPAM'])) {
	header('Location: comments.php?sel=spam&page=1');
	exit;
}

# Si on a des arguments POST, c'est que l'utilisateur a demandé à enregistrer les nouveaux paramètres.
# On enregistre donc.
if(!empty($_POST)) {

	$plxPlugin->setParam('useWL',$_POST['useWL'], 'numeric');
	$plxPlugin->setParam('whiteList', serialize( array_map('trim_value',explode("\n",strtolower(plxUtils::strCheck(trim(str_replace(array("\r\n","\r"),"\n",$_POST['whiteList']))))))), 'cdata');
	$plxPlugin->setParam('autoAddWLIP', $_POST['autoAddWLIP'], 'numeric');
	$plxPlugin->setParam('autoAddWLEmail', $_POST['autoAddWLEmail'], 'numeric');
	$plxPlugin->setParam('autoAddWLUrlsite', $_POST['autoAddWLUrlsite'], 'numeric');

	$plxPlugin->setParam('useBL', $_POST['useBL'], 'numeric');
	$plxPlugin->setParam('blackList', serialize( array_map('trim_value',explode("\n",strtolower(plxUtils::strCheck(trim(str_replace(array("\r\n","\r"),"\n",$_POST['blackList']))))))), 'cdata');
	$plxPlugin->setParam('autoAddBLIP', $_POST['autoAddBLIP'], 'numeric');
	$plxPlugin->setParam('autoAddBLEmail', $_POST['autoAddBLEmail'], 'numeric');
	$plxPlugin->setParam('autoAddBLUrlsite', $_POST['autoAddBLUrlsite'], 'numeric');

	$plxPlugin->setParam('autoBLTimer', (int) $_POST['autoBLTimer'], 'numeric');
	$plxPlugin->setParam('forceModeration', $_POST['forceModeration'], 'numeric');
	$plxPlugin->setParam('SPAMTag', $_POST['SPAMTag'], 'string');		
	$plxPlugin->setParam('adminText', $_POST['adminText'], 'numeric');
	$plxPlugin->setParam('addAdminIcones', $_POST['addAdminIcones'], 'numeric');
	$plxPlugin->setParam('saveMode', (int) $_POST['saveMode'], 'numeric');

	$plxPlugin->setParam('superVision', $_POST['superVision'], 'numeric');	
	$plxPlugin->setParam('senderFrom', $_POST['senderFrom'], 'string');
	$plxPlugin->setParam('senderTo', $_POST['senderTo'], 'string');	

	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p='.get_class($plxPlugin));
	exit;
}
# initialisation des variables
$useWL = $plxPlugin->getParam('useWL')=='' ? 1:$plxPlugin->getParam('useWL');
$whiteList = $plxPlugin->getParam('whiteList')=='' ? '':implode("\n", unserialize($plxPlugin->getParam('whiteList'))); 
$autoAddWLIP = $plxPlugin->getParam('autoAddWLIP')=='' ? 1:$plxPlugin->getParam('autoAddWLIP');
$autoAddWLEmail = $plxPlugin->getParam('autoAddWLEmail')=='' ? 1:$plxPlugin->getParam('autoAddWLEmail');
$autoAddWLUrlsite = $plxPlugin->getParam('autoAddWLUrlsite')=='' ? 1:$plxPlugin->getParam('autoAddWLUrlsite');

$useBL = $plxPlugin->getParam('useBL')=='' ? 1:$plxPlugin->getParam('useBL');
$blackList = $plxPlugin->getParam('blackList')=='' ? '':implode("\n", unserialize($plxPlugin->getParam('blackList')));
$autoAddBLIP = $plxPlugin->getParam('autoAddBLIP')=='' ? 1:$plxPlugin->getParam('autoAddBLIP');
$autoAddBLEmail = $plxPlugin->getParam('autoAddBLEmail')=='' ? 1:$plxPlugin->getParam('autoAddBLEmail');
$autoAddBLUrlsite = $plxPlugin->getParam('autoAddBLUrlsite')=='' ? 1:$plxPlugin->getParam('autoAddBLUrlsite');

$autoBLTimer = $plxPlugin->getParam('autoBLTimer')=='' ? 30:$plxPlugin->getParam('autoBLTimer');
$SPAMTag = $plxPlugin->getParam('SPAMTag')=='' ? '***SPAM***':$plxPlugin->getParam('SPAMTag');
$forceModeration = $plxPlugin->getParam('forceModeration')=='' ? 1:$plxPlugin->getParam('forceModeration');	
$adminText = $plxPlugin->getParam('adminText')=='' ? 1:$plxPlugin->getParam('adminText');
$addAdminIcones = $plxPlugin->getParam('addAdminIcones')=='' ? 1:$plxPlugin->getParam('addAdminIcones');
$saveMode = $plxPlugin->getParam('saveMode')=='' ? 1:$plxPlugin->getParam('saveMode');

$superVision = $plxPlugin->getParam('superVision')=='' ? 0:$plxPlugin->getParam('superVision');
$senderFrom = $plxPlugin->getParam('senderFrom')=='' ? $plxAdmin->aUsers['001']['email']:$plxPlugin->getParam('senderFrom');
$senderTo = $plxPlugin->getParam('senderTo')=='' ? $plxAdmin->aUsers['001']['email']:$plxPlugin->getParam('senderTo');

$racine_coms = $plxAdmin->aConf['racine_commentaires'];

$listCom = @array_diff(scandir(PLX_ROOT.$racine_coms), Array( ".", "..", ".htaccess"));

if(function_exists('mail')) {
	echo '<p style="color:green"><strong>'.$plxPlugin->getLang('L_SBC_MAIL_AVAILABLE').'</strong></p>';
} else {
	echo '<p style="color:#ff0000"><strong>'.$plxPlugin->getLang('L_SBC_MAIL_NOT_AVAILABLE').'</strong></p>';
}
?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS.get_class($plxPlugin); ?>/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS.get_class($plxPlugin); ?>/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_SBC_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_SBC_TAB02') ?></span>
	<span class="tab_0 tab" id="tab_03" onclick="javascript:change_tab('03');"><?php $plxPlugin->lang('L_SBC_TAB03') ?></span>	
</div>
<form class="inline-form" action="parametres_plugin.php?p=<?php echo get_class($plxPlugin); ?>" method="post">
	<fieldset>
		<div class="content_tab" id="content_tab_01">	
			<div class="grid">
				<div class="col sml-12">
					<label for="id_saveMode"><?php $plxPlugin->lang('L_SBC_SAVEMODE') ?>&nbsp;:&nbsp;</label>
					<?php plxUtils::printSelect('saveMode', array("1"=>$plxPlugin->getLang('L_SBC_TAGSPAM'),"2"=>$plxPlugin->getLang('L_SBC_MODSPAN'),"3"=>$plxPlugin->getLang('L_SBC_REJECTSPAM')), $saveMode) ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_SPAMTag"><?php $plxPlugin->lang('L_SBC_SPAMTAG') ?>&nbsp;:</label>
					<?php plxUtils::printInput('SPAMTag',$SPAMTag,'text','32-32') ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_forceModeration"><?php $plxPlugin->lang('L_SBC_FORCEMODERATION') ?>&nbsp;:&nbsp;</label>
					<?php plxUtils::printSelect('forceModeration', array(1=>L_YES,0=>L_NO), $forceModeration) ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_addAdminIcones"><?php $plxPlugin->lang('L_SBC_ADDADMINICONES') ?>&nbsp;:&nbsp;</label>
					<?php plxUtils::printSelect('addAdminIcones', array(1=>L_YES,0=>L_NO), $addAdminIcones) ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_autoBLTimer"><?php $plxPlugin->lang('L_SBC_AUTOBLTIMER') ?>&nbsp;:&nbsp;</label>
					<?php plxUtils::printInput('autoBLTimer', $autoBLTimer, 'number', '5-5') ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_adminText"><?php $plxPlugin->lang('L_SBC_ADMINTEXT') ?>&nbsp;:&nbsp;</label>
					<?php plxUtils::printSelect('adminText', array(1=>L_YES,0=>L_NO), $adminText) ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">
					<label for="id_superVision"><?php $plxPlugin->lang('L_SBC_SUPERVISION') ?>&nbsp;:&nbsp;</label>
					<?php plxUtils::printSelect('superVision', array(1=>L_YES,0=>L_NO), $superVision) ?>
				</div>
			</div>	
			<div class="grid">
				<div class="col sml-12">
					<label for="id_senderFrom"><?php $plxPlugin->lang('L_SBC_SENDER_FROM') ?>&nbsp;:</label>
					<?php plxUtils::printInput('senderFrom',$senderFrom,'text','32-255') ?>
					<a class="hint"><span><?php $plxPlugin->lang('L_SBC_HELP_EMAIL')?></span></a>
				</div>
			</div>	
			<div class="grid">
				<div class="col sml-12">
					<label for="id_senderTo"><?php $plxPlugin->lang('L_SBC_SENDER_TO') ?>&nbsp;:</label>
					<?php plxUtils::printInput('senderTo',$senderTo,'text','52-255') ?>
					<a class="hint"><span><?php $plxPlugin->lang('L_SBC_HELP_COMMAS')?></span></a>
				</div>
			</div>	
		</div>
		<div class="content_tab" id="content_tab_02">
			<div class="grid">
				<div class="col sml-12">
					<label for="id_useWL"><?php $plxPlugin->lang('L_SBC_USE_WL') ?>&nbsp;:&nbsp;</label>
					<?php plxUtils::printSelect('useWL', array(1=>L_YES,0=>L_NO), $useWL) ?>
					<a class="hint"><span><?php $plxPlugin->lang('L_SBC_HELP_WL')?></span></a>
				</div>
			</div>	
			<div class="grid">
				<div class="col sml-12">	
					<?php plxUtils::printArea('whiteList',plxUtils::strCheck($whiteList),140,5,false,'whiteList'); ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">		
					<span><?php $plxPlugin->lang('L_SBC_COM_WL') ?>&nbsp;:&nbsp;</span>
					<input type="checkbox" id="id_autoAddWLIP" name="autoAddWLIP" value=1 <?php if ($autoAddWLIP) echo "checked=\"checked\""; ?>><label for="id_autoAddWLIP">&nbsp;<?php $plxPlugin->lang('L_SBC_IP') ?>&nbsp;</label>
					<input type="checkbox" id="id_autoAddWLEmail" name="autoAddWLEmail" value=1 <?php if ($autoAddWLEmail) echo "checked=\"checked\""; ?>><label for="id_autoAddWLEmail">&nbsp;<?php $plxPlugin->lang('L_SBC_EMAIL') ?>&nbsp;</label>
					<input type="checkbox" id="id_autoAddWLUrlsite" name="autoAddWLUrlsite" value=1 <?php if ($autoAddWLUrlsite) echo "checked=\"checked\""; ?>><label for="id_autoAddWLUrlsite">&nbsp;<?php $plxPlugin->lang('L_SBC_SITE') ?>&nbsp;</label>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">    
					<label for="id_useBL"><?php $plxPlugin->lang('L_SBC_USE_BL') ?>&nbsp;:&nbsp;</label>
					<?php plxUtils::printSelect('useBL', array(1=>L_YES,0=>L_NO), $useBL) ?>
					<a class="hint"><span><?php $plxPlugin->lang('L_SBC_HELP_BL')?></span></a>
				</div>
			</div>	
			<div class="grid">
				<div class="col sml-12"> 
					<?php plxUtils::printArea('blackList',plxUtils::strCheck($blackList),140,5,false,'blackList'); ?>
				</div>
			</div>
			<div class="grid">
				<div class="col sml-12">	
					<span><?php $plxPlugin->lang('L_SBC_COM_BL') ?>&nbsp;:&nbsp;</span>
					<input type="checkbox" id="id_autoAddBLIP" name="autoAddBLIP" value=1 <?php if ($autoAddBLIP) echo "checked=\"checked\""; ?>><label for="id_autoAddBLIP">&nbsp;<?php $plxPlugin->lang('L_SBC_IP') ?>&nbsp;</label>
					<input type="checkbox" id="id_autoAddBLEmail" name="autoAddBLEmail" value=1 <?php if ($autoAddBLEmail) echo "checked=\"checked\""; ?>><label for="id_autoAddBLEmail">&nbsp;<?php $plxPlugin->lang('L_SBC_EMAIL') ?>&nbsp;</label>
					<input type="checkbox" id="id_autoAddBLUrlsite" name="autoAddBLUrlsite" value=1 <?php if ($autoAddBLUrlsite) echo "checked=\"checked\""; ?>><label for="id_autoAddBLUrlsite">&nbsp;<?php $plxPlugin->lang('L_SBC_SITE') ?>&nbsp;</label>
				</div>
			</div>
		</div>
		<p class="in-action-bar"><?php echo plxToken::getTokenPostMethod() ?>
		<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SBC_SAUVE') ?>" />
		<?php if ($useBL) { ?>
		&nbsp;&nbsp;&nbsp;<input type="submit" name="listSPAM" value="<?php $plxPlugin->lang('L_SBC_LIST_SPAM') ?>"/>
		<?php } ?>
		</p>
	</fieldset>
</form>
<div class="content_tab" id="content_tab_03">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->spamblockcoms->UpdatePlugin('plxSpamBlockComs'); ?>
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