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

# Vérification de l'existence du dossier /plugins/bachat
if(!is_dir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/bachat')) {
	@mkdir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/bachat',0755,true);

	# Protection du répertoire des entrées du Bon Achat
	plxUtils::write('', PLX_ROOT.PLX_CONFIG_PATH.'plugins/bachat/index.html');
}

if(!empty($_POST)) {
	$plxPlugin->setParam('baName', $_POST['baName'], 'string');
	$plxPlugin->setParam('baDisplay', $_POST['baDisplay'], 'numeric');	
	$plxPlugin->setParam('baPos', $_POST['baPos'], 'numeric');	
	$plxPlugin->setParam('baEmail', $_POST['baEmail'], 'string');
	$plxPlugin->setParam('baDevise', $_POST['baDevise'], 'string');	
	$plxPlugin->setParam('baAccountType', $_POST['baAccountType'], 'numeric');
	$plxPlugin->setParam('baPriceList', $_POST['baPriceList'], 'string');	
	$plxPlugin->setParam('captcha', $_POST['captcha'], 'numeric');
	$plxPlugin->setParam('baSupervision', $_POST['baSupervision'], 'numeric');
	$plxPlugin->setParam('adminEmail', $_POST['adminEmail'], 'string');
	$plxPlugin->setParam('baRecipient', $_POST['baRecipient'], 'numeric');
	$plxPlugin->setParam('baBuyer', $_POST['baBuyer'], 'numeric');	
	$plxPlugin->setParam('baContentValid', $_POST['chapo'], 'string');
	$plxPlugin->setParam('baContentCancel', $_POST['content'], 'string');
	$plxPlugin->setParam('baSite', $plxAdmin->aConf['title'], 'string');
	$plxPlugin->setParam('baAdmin', $plxAdmin->aUsers['001']['name'], 'string');
	$plxPlugin->setParam('template', $_POST['template'], 'string');
	$plxPlugin->setParam('baDuration', $_POST['baDuration'], 'numeric');
	$plxPlugin->setParam('baContent', $_POST['baContent'], 'string');
	$plxPlugin->setParam('bacolorText', $_POST['bacolorText'], 'string'); # couleur du texte du bon cadeau #c93b91
	$plxPlugin->setParam('baFooter', $_POST['baFooter'], 'string');
	$plxPlugin->setParam('baFirstSide', $_POST['baFirstSide'], 'string');	
	$plxPlugin->setParam('baReverseSize', $_POST['baReverseSize'], 'string');	
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxBonAchat');
	exit;
}

$baName =  $plxPlugin->getParam('baName')=='' ? $plxPlugin->getLang('L_BA_PG_TITLE') : $plxPlugin->getParam('baName');
$baDisplay =  $plxPlugin->getParam('baDisplay')=='' ? 1 : $plxPlugin->getParam('baDisplay');
$baPos =  $plxPlugin->getParam('baPos')=='' ? 2 : $plxPlugin->getParam('baPos');
$baEmail =  $plxPlugin->getParam('baEmail')=='' ? $plxAdmin->aUsers['001']['email'] : $plxPlugin->getParam('baEmail');
$baDevise =  $plxPlugin->getParam('baDevise')=='' ? 'EUR' : $plxPlugin->getParam('baDevise');
$baAccountType =  $plxPlugin->getParam('baAccountType')=='' ? 0 : $plxPlugin->getParam('baAccountType');
$baDuration =  $plxPlugin->getParam('baDuration')=='' ? 6 : $plxPlugin->getParam('baDuration');
$baPriceList =  $plxPlugin->getParam('baPriceList')=='' ? '10,20,30,40' : $plxPlugin->getParam('baPriceList');
$captcha = $plxPlugin->getParam('captcha')=='' ? '1' : $plxPlugin->getParam('captcha');
$baSupervision = $plxPlugin->getParam('baSupervision')=='' ? 1 : $plxPlugin->getParam('baSupervision');
$adminEmail =  $plxPlugin->getParam('adminEmail')=='' ? $plxAdmin->aUsers['001']['email'] : $plxPlugin->getParam('adminEmail');
$baRecipient = $plxPlugin->getParam('baRecipient')=='' ? 1 : $plxPlugin->getParam('baRecipient');
$baBuyer = $plxPlugin->getParam('baBuyer')=='' ? 1 : $plxPlugin->getParam('baBuyer');
$baContentValid =  $plxPlugin->getParam('baContentValid')=='' ? $plxPlugin->getLang('L_BA_CONTENTVALID') : $plxPlugin->getParam('baContentValid');
$baContentCancel =  $plxPlugin->getParam('baContentCancel')=='' ? $plxPlugin->getLang('L_BA_CONTENTCANCEL') : $plxPlugin->getParam('baContentCancel');
$template = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
$baContent =  $plxPlugin->getParam('baContent')=='' ? '' : $plxPlugin->getParam('baContent');
$bacolorText = $plxPlugin->getParam('bacolorText')=='' ? '000000' : $plxPlugin->getParam('bacolorText');
$baFooter =  $plxPlugin->getParam('baFooter')=='' ? 'plugins/plxBonAchat/img/bonCadeau_pied_de_page.jpg' : $plxPlugin->getParam('baFooter');
$baFirstSide =  $plxPlugin->getParam('baFirstSide')=='' ? 'plugins/plxBonAchat/img/bonCadeau_recto.jpg' : $plxPlugin->getParam('baFirstSide');
$baReverseSize =  $plxPlugin->getParam('baReverseSize')=='' ? 'plugins/plxBonAchat/img/bonCadeau_verso.jpg' : $plxPlugin->getParam('baReverseSize');

# On récupère les templates des pages statiques
$files = plxGlob::getInstance(PLX_ROOT.'themes/'.$plxAdmin->aConf['style']);
if ($array = $files->query('/^static(-[a-z0-9-_]+)?.php$/')) {
	foreach($array as $k=>$v)
		$aTemplates[$v] = $v;
}
?>
<?php if ($plxAdmin->aConf['version'] < '5.4') { ?>
<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php } ?>
<?php
if(function_exists('mail')) {
	echo '<p style="color:green"><strong>'.$plxPlugin->getLang('L_BA_MAIL_AVAILABLE').'</strong></p>';
} else {
	echo '<p style="color:#ff0000"><strong>'.$plxPlugin->getLang('L_BA_MAIL_NOT_AVAILABLE').'</strong></p>';
}
?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxBonAchat/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxBonAchat/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_BA_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_BA_TAB02') ?></span>
	<span class="tab_0 tab" id="tab_03" onclick="javascript:change_tab('03');"><?php $plxPlugin->lang('L_BA_TAB03') ?></span>	
</div>
<form id="form_plxBonAchat" action="parametres_plugin.php?p=plxBonAchat" method="post">
	<div class="inline-form">
	<div class="content_tab" id="content_tab_01">
	<fieldset>
		<div class="grid">
			<div class="col sml-12">	
			<label for="id_baName"><?php echo $plxPlugin->lang('L_BA_MENU_TITLE') ?>&nbsp;:</label>
			<?php plxUtils::printInput('baName',$baName,'text','32-64') ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">	
			<label for="id_baDisplay"><?php echo $plxPlugin->lang('L_BA_MENU_DISPLAY') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('baDisplay',array('1'=>L_YES,'0'=>L_NO),$baDisplay); ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">		
			<label for="id_baPos"><?php $plxPlugin->lang('L_BA_MENU_POS') ?>&nbsp;:</label>
				<?php plxUtils::printInput('baPos',$baPos,'text','2-5') ?>	
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12 med-7 lrg-8">
				<label for="id_content"><?php echo $plxPlugin->lang('L_BA_CONTENT') ?>&nbsp;:</label>
				<?php plxUtils::printArea('baContent',plxUtils::strCheck($baContent),140,5,false,'full-width'); ?>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">
			<label for="id_baAccountType"><?php $plxPlugin->lang('L_BA_ACCOUNTTYPE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('baAccountType',array('0'=>$plxPlugin->getlang('L_BA_LIVE'),'1'=>$plxPlugin->getlang('L_BA_SANDBOX')),$baAccountType); ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_BA_HELP_ACCOUNTTYPE') ?></span></a>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">
			<label for="id_baEmail"><?php $plxPlugin->lang('L_BA_EMAIL') ?>&nbsp;:</label>
			<?php plxUtils::printInput('baEmail',$baEmail,'text','32-64') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_BA_HELP_EMAIL') ?></span></a>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">
			<label for="id_baDevise"><?php $plxPlugin->lang('L_BA_DEVISE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('baDevise',array('EUR'=>'EUR','THB'=>'THB','DKK'=>'DKK','NOK'=>'NOK','SEK'=>'SEK','CZK'=>'CZK','AUD'=>'AUD','CAD'=>'CAD','HKD'=>'HKD','SGD'=>'SGD','NZD'=>'NZD','USD'=>'USD','HUF'=>'HUF','CHF'=>'CHF','GBP'=>'GBP','TWD'=>'TWD','ILS'=>'ILS','MXN'=>'MXN','PHP'=>'PHP','RUB'=>'RUB','BRL'=>'BRL','JPY'=>'JPY','PLN'=>'PLN'),$baDevise); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">	
			<label for="id_baPriceList"><?php echo $plxPlugin->lang('L_BA_MENU_PRICELIST') ?>&nbsp;:</label>
			<?php plxUtils::printInput('baPriceList',$baPriceList,'text','32-255') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_BA_HELP_PRICE') ?></span></a>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">	
			<label for="id_baDuration"><?php echo $plxPlugin->lang('L_BA_MENU_DURATION') ?>&nbsp;:</label>
			<?php plxUtils::printInput('baDuration',$baDuration,'text','32-255') ?>
			<a class="hint"><span><?php $plxPlugin->lang('L_BA_HELP_DIRATION') ?></span></a>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">		
			<label for="id_captcha"><?php echo $plxPlugin->lang('L_BA_CAPTCHA') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('captcha',array('1'=>L_YES,'0'=>L_NO),$captcha); ?>	
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_template"><?php $plxPlugin->lang('L_BA_TEMPLATE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('template', $aTemplates, $template) ?>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">
			<label for="id_baSupervision"><?php echo $plxPlugin->lang('L_BA_SUPERVISION_EMAIL') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('baSupervision',array('1'=>L_YES,'0'=>L_NO),$baSupervision); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_adminEmail"><?php $plxPlugin->lang('L_BA_ADMINEMAIL') ?>&nbsp;:</label>
			<?php plxUtils::printInput('adminEmail',$adminEmail,'text','32-64') ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_baBuyer"><?php echo $plxPlugin->lang('L_BA_BUYER_EMAIL') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('baBuyer',array('1'=>L_YES,'0'=>L_NO),$baBuyer); ?>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12 med-7 lrg-8">
			<label for="id_baContentValid"><?php echo $plxPlugin->lang('L_BA_PG_VALID') ?>&nbsp;:</label>
			<?php plxUtils::printArea('chapo',plxUtils::strCheck($baContentValid),35,8,false,'full-width'); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12 med-7 lrg-8">
			<label for="id_baContentCancel"><?php echo $plxPlugin->lang('L_BA_PG_CANCEL') ?>&nbsp;:</label>
			<?php plxUtils::printArea('content',plxUtils::strCheck($baContentCancel),35,8,false,'full-width'); ?>
			</div>
		</div>
		</div>
	    <div class="content_tab" id="content_tab_02">
		<div class="grid">
			<div class="col sml-12">
			<label for="id_bacolorText"><?php $plxPlugin->lang('L_BA_COLORTEXT') ?>&nbsp;:</label>
			<?php plxUtils::printInput('bacolorText',$bacolorText,'text','6-6',false,'color') ?>
			</div>
		</div>	
	    <div><span style="color: #ff0000;"><?php echo $plxPlugin->lang('L_BA_ATTENTION') ?>.</span></div>		
			<div class="grid gridthumb">
				<div class="col sml-12 med-7 lrg-8">
					<label for="id_baFooter">
						<?php $plxPlugin->lang('L_BA_FOOTER') ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_baFooter" href="javascript:void(0)" onclick="mediasManager.openPopup('id_baFooter', true)" style="outline:none; text-decoration: none">+</a>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_BA_HELP_IMG') ?></span></a>
					</label>
					<?php plxUtils::printInput('baFooter',plxUtils::strCheck($baFooter),'text','255-255',false,'full-width','','onkeyup="refreshImg(this.value)"'); ?>
				</div>
			</div>	
			<div class="grid gridthumb">
				<div class="col sml-12 med-7 lrg-8">
					<label for="id_baFirstSide">
						<?php $plxPlugin->lang('L_BA_FIRST_SIDE') ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_baFirstSide" href="javascript:void(0)" onclick="mediasManager.openPopup('id_baFirstSide', true)" style="outline:none; text-decoration: none">+</a>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_BA_HELP_IMG') ?></span></a>
					</label>
					<?php plxUtils::printInput('baFirstSide',plxUtils::strCheck($baFirstSide),'text','255-255',false,'full-width','','onkeyup="refreshImg(this.value)"'); ?>
				</div>
			</div>
			<div class="grid gridthumb">
				<div class="col sml-12 med-7 lrg-8">
					<label for="id_baReverseSize">
						<?php $plxPlugin->lang('L_BA_REVERSE_SIZE') ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_baReverseSize" href="javascript:void(0)" onclick="mediasManager.openPopup('id_baReverseSize', true)" style="outline:none; text-decoration: none">+</a>&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_BA_HELP_IMG') ?></span></a>
					</label>
					<?php plxUtils::printInput('baReverseSize',plxUtils::strCheck($baReverseSize),'text','255-255',false,'full-width','','onkeyup="refreshImg(this.value)"'); ?>
				</div>
			</div>	
		</div>	
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_BA_SAVE') ?>" />
		</p>
	</fieldset>
	</div>
</form>
<div class="content_tab" id="content_tab_03">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->bonachat->UpdatePlugin('plxBonAchat'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxBonAchat/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS; ?>plxBonAchat/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxBonAchat/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxBonAchat/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>
