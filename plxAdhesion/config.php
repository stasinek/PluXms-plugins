<?php 
/**
 * Plugin adhesion
 *
 * @version	1.5
 * @date	07/10/2013
 * @author	Stephane F, Cyril MAGUIRE
 **/

if(!defined('PLX_ROOT')) exit;

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	$plxPlugin->setParam('adherents', $_POST['adherents'], 'cdata');
	$plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
	$plxPlugin->setParam('mnuName', plxUtils::strCheck(str_replace("'","’",$_POST['mnuName'])), 'cdata');
	$plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
	$plxPlugin->setParam('mnuAdherer', plxUtils::strCheck(str_replace("'","’",$_POST['mnuAdherer'])), 'cdata');
	$plxPlugin->setParam('desc_adhesion', plxUtils::cdataCheck(trim(str_replace("'","’",$_POST['desc_adhesion']))), 'cdata');
	$plxPlugin->setParam('mnuForgetPass', plxUtils::strCheck(str_replace("'","’",$_POST['mnuForgetPass'])), 'cdata');
	$plxPlugin->setParam('mnuMyAccount', plxUtils::strCheck(str_replace("'","’",$_POST['mnuMyAccount'])), 'cdata');
	$plxPlugin->setParam('showAnnuaire', (plxUtils::strCheck($_POST['typeAnnuaire']) == 'generaliste' ? 'non' : plxUtils::strCheck($_POST['showAnnuaire']) ), 'string');
	$plxPlugin->setParam('mnuAnnuaire', plxUtils::strCheck(str_replace("'","’",$_POST['mnuAnnuaire'])), 'cdata');
	$plxPlugin->setParam('typeAnnuaire', plxUtils::strCheck($_POST['typeAnnuaire']), 'string');
	$tab = (substr($_POST['tabActivites'],-5) == 'Autre' ? $_POST['tabActivites'] : $_POST['tabActivites'].',Autre' );
	$plxPlugin->setParam('tabActivites', plxUtils::strCheck($tab), 'string');
	if(!plxUtils::checkMail($_POST['email'])) {
		$_POST['email']='';
		plxMsg::Error($plxPlugin->getLang('L_ERR_EMAIL'));
	}
	$plxPlugin->setParam('cle', $_POST['cle'], 'numeric');
	$plxPlugin->setParam('annee', $_POST['annee'], 'cdata');
	$plxPlugin->setParam('nom_asso', plxUtils::strCheck(str_replace("'","’",$_POST['nom_asso'])), 'cdata');
	$plxPlugin->setParam('adresse_asso', plxUtils::cdataCheck(trim(str_replace("'","’",$_POST['adresse_asso']))), 'cdata');
	$plxPlugin->setParam('domaine_asso', $_POST['domaine_asso'], 'cdata');
	$plxPlugin->setParam('email', $_POST['email'], 'cdata');
	$plxPlugin->setParam('subject', plxUtils::strCheck(str_replace("'","’",$_POST['subject'])), 'cdata');
	$plxPlugin->setParam('subject_password', plxUtils::strCheck(str_replace("'","’",$_POST['subject_password'])), 'cdata');
	$plxPlugin->setParam('msg_password', plxUtils::strCheck(str_replace("'","’",$_POST['msg_password'])), 'cdata');
	$plxPlugin->setParam('thankyou', plxUtils::strCheck(str_replace("'","’",$_POST['thankyou'])), 'cdata');
	$plxPlugin->setParam('validation_subject', plxUtils::strCheck(str_replace("'","’",$_POST['validation_subject'])), 'cdata');
	$plxPlugin->setParam('validation_msg', plxUtils::strCheck(str_replace("'","’",$_POST['validation_msg'])), 'cdata');
	$plxPlugin->setParam('devalidation_subject', plxUtils::strCheck(str_replace("'","’",$_POST['devalidation_subject'])), 'cdata');
	$plxPlugin->setParam('devalidation_msg', plxUtils::strCheck(str_replace("'","’",$_POST['devalidation_msg'])), 'cdata');
	$plxPlugin->setParam('template', $_POST['template'], 'cdata');
	//Look articles
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=adhesion');
	exit;
}
$mnuName =  $plxPlugin->getParam('mnuName')=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName');
$mnuDisplay =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
$mnuPos =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
$mnuAdherer =  $plxPlugin->getParam('mnuAdherer')=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_ADHERER') : $plxPlugin->getParam('mnuAdherer');
$desc_adhesion =  $plxPlugin->getParam('desc_adhesion')=='' ? $plxPlugin->getLang('L_DEFAULT_DESC') : $plxPlugin->getParam('desc_adhesion');
$mnuForgetPass =  $plxPlugin->getParam('mnuForgetPass')=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_PASS') : $plxPlugin->getParam('mnuForgetPass');
$mnuMyAccount =  $plxPlugin->getParam('mnuMyAccount')=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_MY_ACCOUNT') : $plxPlugin->getParam('mnuMyAccount');
$showAnnuaire =  ($plxPlugin->getParam('showAnnuaire')=='' || $plxPlugin->getParam('typeAnnuaire')=='generaliste') ? 'no' : $plxPlugin->getParam('showAnnuaire');
$mnuAnnuaire =  $plxPlugin->getParam('mnuAnnuaire')=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_ANNUAIRE') : $plxPlugin->getParam('mnuAnnuaire');
$typeAnnuaire =  $plxPlugin->getParam('typeAnnuaire')=='' ? 'generaliste' : $plxPlugin->getParam('typeAnnuaire');
$tabActivites =  $plxPlugin->getParam('tabActivites')=='' ? 'ARC,TEC,IRC,Autre' : $plxPlugin->getParam('tabActivites');
$cle = $plxPlugin->getParam('cle')=='' ? 3 : $plxPlugin->getParam('cle');
$annee = $plxPlugin->getParam('annee')=='' ? 'civile' : $plxPlugin->getParam('annee');
$nom_asso = $plxPlugin->getParam('nom_asso')=='' ? '' : $plxPlugin->getParam('nom_asso');
$adresse_asso = $plxPlugin->getParam('adresse_asso')=='' ? '' : $plxPlugin->getParam('adresse_asso');
$domaine_asso = $plxPlugin->getParam('domaine_asso')=='' ? '' : $plxPlugin->getParam('domaine_asso');
$email = $plxPlugin->getParam('email')=='' ? '' : $plxPlugin->getParam('email');
$subject = $plxPlugin->getParam('subject')=='' ? $plxPlugin->getLang('L_DEFAULT_SUBJECT') : $plxPlugin->getParam('subject');
$subject_password = $plxPlugin->getParam('subject_password')=='' ? $plxPlugin->getLang('L_DEFAULT_SUBJECT_PASS') : $plxPlugin->getParam('subject_password');
$msg_password = $plxPlugin->getParam('msg_password')=='' ? $plxPlugin->getLang('L_DEFAULT_MSG_PASS') : $plxPlugin->getParam('msg_password');
$thankyou = $plxPlugin->getParam('thankyou')=='' ? $plxPlugin->getLang('L_DEFAULT_THANKYOU') : $plxPlugin->getParam('thankyou');
$val_sub = $plxPlugin->getParam('validation_subject')=='' ? $plxPlugin->getLang('L_DEFAULT_VAL_SUB') : $plxPlugin->getParam('validation_subject');
$val_msg = $plxPlugin->getParam('validation_msg')=='' ? $plxPlugin->getLang('L_DEFAULT_VAL_MSG') : $plxPlugin->getParam('validation_msg');
$deval_sub = $plxPlugin->getParam('devalidation_subject')=='' ? $plxPlugin->getLang('L_DEFAULT_DEVAL_SUB') : $plxPlugin->getParam('devalidation_subject');
$deval_msg = $plxPlugin->getParam('devalidation_msg')=='' ? $plxPlugin->getLang('L_DEFAULT_DEVAL_MSG') : $plxPlugin->getParam('devalidation_msg');
$template = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');

# On récupère les templates des pages statiques
$files = plxGlob::getInstance(PLX_ROOT.'themes/'.$plxAdmin->aConf['style']);
if ($array = $files->query('/^static(-[a-z0-9-_]+)?.php$/')) {
	foreach($array as $k=>$v)
		$aTemplates[$v] = $v;
}
$aAnnee = array(
	'civile'=>$plxPlugin->getLang('L_ANNEE_CIVILE'),
	'entiere'=>$plxPlugin->getLang('L_ANNEE_ENTIERE')
	);
$aClefs = array(3=>3,4=>4,5=>5,6=>6,7=>7,8=>8);
$aAnnuaire = array('on'=>L_YES,'no'=>L_NO);
$aTypeAnnuaires = array('generaliste'=>$plxPlugin->getLang('L_ANNUAIRE_GEN'),'professionnel'=>$plxPlugin->getLang('L_ANNUAIRE_PRO'));
?>

<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php
if(function_exists('mail')) {
	echo '<p style="color:green"><strong>'.$plxPlugin->getLang('L_MAIL_AVAILABLE').'</strong></p>';
} else {
	echo '<p style="color:#ff0000"><strong>'.$plxPlugin->getLang('L_MAIL_NOT_AVAILABLE').'</strong></p>';
}
?>
<form id="form_plxmycontact" action="parametres_plugin.php?p=adhesion" method="post">
	<fieldset>
		<p class="field"><label for="adherents"><?php echo $plxPlugin->getLang('L_CONFIG_ROOT_PATH') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('adherents', $plxPlugin->getParam('adherents'), 'text'); ?>
		<a class="help" title="Ne pas oublier le slash à la fin">&nbsp;</a>
		<p class="field"><label for="id_mnuDisplay"><?php echo $plxPlugin->lang('L_MENU_DISPLAY') ?>&nbsp;:</label></p>
		<?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$mnuDisplay); ?>
		<p class="field"><label for="id_mnuName"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('mnuName',$mnuName,'text') ?>
		<p class="field"><label for="id_mnuPos"><?php $plxPlugin->lang('L_MENU_POS') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('mnuPos',$mnuPos,'text','2-5') ?>
		<p class="field"><label for="id_mnuAdherer"><?php $plxPlugin->lang('L_MENU_ADHERER_TITLE') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('mnuAdherer',$mnuAdherer,'text') ?>
		<p class="field toggle"><label for="id_desc_adhesion"><?php $plxPlugin->lang('L_MENU_DESC') ?>&nbsp;:</label>
		<a id="toggler_desc" href="javascript:void(0)" onclick="toggleDiv('toggle_desc', 'toggler_desc', 'afficher','masquer')">afficher</a></p>
		<div id="toggle_desc" style="display:none"><?php plxUtils::printArea('desc_adhesion',$desc_adhesion) ?></div>
		<p class="field"><label for="id_mnuForgetPass"><?php $plxPlugin->lang('L_MENU_FORGET_PASS') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('mnuForgetPass',$mnuForgetPass,'text') ?>
		<p class="field"><label for="id_mnuMyAccount"><?php $plxPlugin->lang('L_MENU_MY_ACCOUNT') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('mnuMyAccount',$mnuMyAccount,'text') ?>
		<p class="field"><label for="id_showAnnuaire"><?php $plxPlugin->lang('L_SHOW_MENU_ANNUAIRE') ?>&nbsp;:</label></p>
		<?php plxUtils::printSelect('showAnnuaire',$aAnnuaire,$showAnnuaire) ?>
		<a class="help" title="L'annuaire ne sera affiché que s'il est de type professionnel">&nbsp;</a>
		<p class="field"><label for="id_mnuAnnuaire"><?php $plxPlugin->lang('L_MENU_ANNUAIRE') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('mnuAnnuaire',$mnuAnnuaire,'text') ?>
		<p class="field"><label for="id_typeAnnuaire"><?php $plxPlugin->lang('L_TYPE_ANNUAIRE') ?>&nbsp;:</label></p>
		<?php plxUtils::printSelect('typeAnnuaire',$aTypeAnnuaires,$typeAnnuaire) ?>
		<p class="field"><label for="id_tabActivites"><?php $plxPlugin->lang('L_TAB_ACTIVITIES') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('tabActivites',$tabActivites,'text') ?>
		<a class="help" title="Le dernier index doit être : Autre">&nbsp;</a>
		<p class="field"><label for="id_cle"><?php $plxPlugin->lang('L_CLE') ?>&nbsp;:</label></p>
		<?php plxUtils::printSelect('cle', $aClefs, $cle) ?>
		<p class="field"><label for="id_annee"><?php $plxPlugin->lang('L_ANNEE') ?>&nbsp;:</label></p>
		<?php plxUtils::printSelect('annee', $aAnnee, $annee) ?>
		<p class="field"><label for="id_nom_asso"><?php $plxPlugin->lang('L_NOM_ASSO') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('nom_asso',$nom_asso,'text','50-120') ?>
		<p class="field toggle"><label for="id_adresse_asso"><?php $plxPlugin->lang('L_ADRESSE_ASSO') ?>&nbsp;:</label>
		<a id="toggler_ad" href="javascript:void(0)" onclick="toggleDiv('toggle_ad', 'toggler_ad', 'afficher','masquer')">afficher</a></p>
		<div id="toggle_ad" style="display:none"><?php plxUtils::printArea('adresse_asso',$adresse_asso) ?></div>
		<p class="field"><label for="id_domaine_asso"><?php $plxPlugin->lang('L_DOMAINE_ASSO') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('domaine_asso',$domaine_asso,'text','50-120') ?>
		<p class="field"><label for="id_email"><?php $plxPlugin->lang('L_EMAIL') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('email',$email,'text','50-120') ?>
		<p class="field"><label for="id_subject"><?php $plxPlugin->lang('L_EMAIL_SUBJECT') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('subject',$subject,'text','100-120') ?>
		<p class="field"><label for="id_thankyou"><?php $plxPlugin->lang('L_THANKYOU_MESSAGE') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('thankyou',$thankyou,'text','100-680') ?>
		<p class="field"><label for="id_subject_password"><?php $plxPlugin->lang('L_PASSWD_SUBJECT') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('subject_password',$subject_password,'text','100-120') ?>
		<p class="field"><label for="id_msg_password"><?php $plxPlugin->lang('L_PASSWD_MESSAGE') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('msg_password',$msg_password,'text','100-180') ?>
		<p class="field"><label for="id_validation_subject"><?php $plxPlugin->lang('L_VALIDATION_SUBJECT') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('validation_subject',$val_sub,'text','100-180') ?>
		<p class="field"><label for="id_validation_msg"><?php $plxPlugin->lang('L_VALIDATION_MESSAGE') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('validation_msg',$val_msg,'text','100-180') ?>
		<p class="field"><label for="id_devalidation_subject"><?php $plxPlugin->lang('L_DEVALIDATION_SUBJECT') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('devalidation_subject',$deval_sub,'text','100-120') ?>
		<p class="field"><label for="id_devalidation_msg"><?php $plxPlugin->lang('L_DEVALIDATION_MESSAGE') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('devalidation_msg',$deval_msg,'text','100-180') ?>
		<p class="field"><label for="id_template"><?php $plxPlugin->lang('L_TEMPLATE') ?>&nbsp;:</label></p>
		<?php plxUtils::printSelect('template', $aTemplates, $template) ?>
		<p id="sendConfig">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?> &radic;" />
		</p>
	</fieldset>
</form>
