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

# Vérification de l'existence du dossier /plugins/gbook
if(!is_dir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/gbook')) {
	@mkdir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/gbook',0755,true);

	# Protection du répertoire des entrées du GuestBook
	plxUtils::write('', PLX_ROOT.PLX_CONFIG_PATH.'plugins/gbook/index.html');
}


if(!empty($_POST)) {
	$plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
	$plxPlugin->setParam('mnuName',addslashes($_POST['mnuName']), 'cdata');
	$plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
	$plxPlugin->setParam('mnuText', plxUtils::strCheck($_POST['mnuText']), 'string');
	$plxPlugin->setParam('Text',$_POST['content'], 'cdata');
	$plxPlugin->setParam('email', $_POST['email'], 'string');
	$plxPlugin->setParam('supervision', $_POST['supervision'], 'numeric');
	$plxPlugin->setParam('subject', $_POST['subject'], 'string');
	$plxPlugin->setParam('thankyou', $_POST['thankyou'], 'string');
	$plxPlugin->setParam('template', $_POST['template'], 'string');
	$plxPlugin->setParam('captcha', $_POST['captcha'], 'numeric');
	$plxPlugin->setParam('mod', $_POST['mod'], 'numeric');	
	$plxPlugin->setParam('byPage', $_POST['byPage'], 'numeric');
	$plxPlugin->setParam('byPage_admin', $_POST['byPage_admin'], 'numeric');	
	$plxPlugin->setParam('tri_gb', $_POST['tri_gb'], 'string');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxGuestBook');
	exit;
}
$mnuDisplay =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
$tmp =  $plxPlugin->getParam('mnuName')=='' ? $plxPlugin->getLang('L_GB_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName');
$mnuName = stripslashes($tmp);
$mnuPos =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
$mnuText =  $plxPlugin->getParam('mnuText')=='' ? '' : $plxPlugin->getParam('mnuText');
$Text =  $plxPlugin->getParam('Text')=='' ? '' : $plxPlugin->getParam('Text');
$email = $plxPlugin->getParam('email')=='' ? '' : $plxPlugin->getParam('email');
$supervision = $plxPlugin->getParam('supervision')=='' ? 1 : $plxPlugin->getParam('supervision');
$subject = $plxPlugin->getParam('subject')=='' ? $plxPlugin->getLang('L_GB_DEFAULT_OBJECT') : $plxPlugin->getParam('subject');
$thankyou = $plxPlugin->getParam('thankyou')=='' ? $plxPlugin->getLang('L_GB_DEFAULT_THANKYOU') : $plxPlugin->getParam('thankyou');
$template = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
$captcha = $plxPlugin->getParam('captcha')=='' ? '1' : $plxPlugin->getParam('captcha');
$mod = $plxPlugin->getParam('mod')=='' ? '1' : $plxPlugin->getParam('mod');
$byPage =  $plxPlugin->getParam('byPage')=='' ? 10 : $plxPlugin->getParam('byPage');
$byPage_admin =  $plxPlugin->getParam('byPage_admin')=='' ? 10 : $plxPlugin->getParam('byPage_admin');
$tri_gb =  $plxPlugin->getParam('tri_gb')=='' ? 'rsort' : $plxPlugin->getParam('tri_gb');

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
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxGuestBook/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxGuestBook/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_GB_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_GB_TAB02') ?></span>
	<span class="tab_0 tab" id="tab_03" onclick="javascript:change_tab('03');"><?php $plxPlugin->lang('L_GB_TAB03') ?></span>
</div>
<form class="inline-form" id="form_plxguestbook" action="parametres_plugin.php?p=plxGuestBook" method="post">
<div class="content_tab" id="content_tab_01">	
	<?php
	if(function_exists('mail')) {
		echo '<span style="color:green"><strong>'.$plxPlugin->getLang('L_GB_MAIL_AVAILABLE').'</strong></span>';
	} else {
		echo '<span style="color:#ff0000"><strong>'.$plxPlugin->getLang('L_GB_MAIL_NOT_AVAILABLE').'</strong></span>';
	}
	?>
	<fieldset>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_mnuDisplay"><?php echo $plxPlugin->lang('L_GB_MENU_DISPLAY') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$mnuDisplay); ?>
			</div>
		</div>			
		<div class="grid">
			<div class="col sml-12">
			<label for="id_mnuName"><?php $plxPlugin->lang('L_GB_MENU_TITLE') ?>&nbsp;:</label>
			<?php plxUtils::printInput('mnuName',$mnuName,'text','20-20') ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_mnuPos"><?php $plxPlugin->lang('L_GB_MENU_POS') ?>&nbsp;:</label>
			<?php plxUtils::printInput('mnuPos',$mnuPos,'text','2-5') ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_email"><?php $plxPlugin->lang('L_GB_EMAIL') ?>&nbsp;:</label>
			<?php plxUtils::printInput('email',$email,'text','50-120') ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_subject"><?php $plxPlugin->lang('L_GB_EMAIL_SUBJECT') ?>&nbsp;:</label>
			<?php plxUtils::printInput('subject',$subject,'text','100-120') ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_thankyou"><?php $plxPlugin->lang('L_GB_THANKYOU_MESSAGE') ?>&nbsp;:</label>
			<?php plxUtils::printInput('thankyou',$thankyou,'text','100-120') ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_captcha"><?php echo $plxPlugin->lang('L_GB_CAPTCHA') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('captcha',array('1'=>L_YES,'0'=>L_NO),$captcha); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_template"><?php $plxPlugin->lang('L_GB_TEMPLATE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('template', $aTemplates, $template) ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_mod"><?php echo $plxPlugin->lang('L_GB_MOD') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('mod',array('1'=>L_YES,'0'=>L_NO),$mod); ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">
			<label for="id_byPage_admin"><?php $plxPlugin->lang('L_GB_BYPAGE_ADMIN') ?>&nbsp;:</label>
			<?php plxUtils::printInput('byPage_admin',$byPage_admin,'text','2-5') ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_tri_gb"><?php echo $plxPlugin->lang('L_GB_TRI_GB') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('tri_gb',array('sort'=>$plxPlugin->getlang('L_GB_SORT'),'rsort'=>$plxPlugin->getlang('L_GB_RSORT')),$tri_gb); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_byPage"><?php $plxPlugin->lang('L_GB_BYPAGE') ?>&nbsp;:</label>
			<?php plxUtils::printInput('byPage',$byPage,'text','2-5') ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_supervision"><?php echo $plxPlugin->lang('L_GB_SUPERVISION_EMAIL') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('supervision',array('1'=>L_YES,'0'=>L_NO),$supervision); ?>
			</div>
		</div>
		<p><?php $plxPlugin->lang('L_GB_COMMA') ?></p>
		</div>
<div class="content_tab" id="content_tab_02">
		<div class="grid">
			<div class="col sml-12">
				<label for="id_text"><?php $plxPlugin->lang('L_GB_FORM_TEXT') ?>&nbsp;:</label>
				<?php plxUtils::printArea('content',plxUtils::strCheck($Text),140,30,false,'Test'); ?>
			</div>
		</div>	
</div>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_GB_SAVE') ?>" />
		</p>		
	</fieldset>
</form>
<div class="content_tab" id="content_tab_03">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->guestbook->UpdatePlugin('plxGuestBook'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxGuestBook/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS; ?>plxGuestBook/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxGuestBook/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxGuestBook/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>