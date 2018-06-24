<?php 
/**
 * Plugin adhesion
 *
 * @version	1.5
 * @date	07/10/2013
 * @author	Stephane F, Cyril MAGUIRE
 **/

if(!defined('PLX_ROOT')) exit;

# récuperation d'une instance de plxShow
$plxShow = plxShow::getInstance();
$plxShow->plxMotor->plxCapcha = new plxCapcha();
$plxPlugin = $plxShow->plxMotor->plxPlugins->getInstance('adhesion');

$id = $plxPlugin->nextIdAdherent();

$pro = array();
$error = array();
$success=false;

$wall_e = '';
if(!empty($_POST) && !empty($_POST['wall-e'])) {
	$wall_e = $_POST['wall-e'];
}
if (isset($_GET['q'])) {
	$erase = $plxPlugin->compare($_GET['q']);
	if ($erase) {
		$_SESSION['erase'] = '<p id="password_success">'.$plxPlugin->getLang('L_FORM_ERASE_FORM_LIST_OK').'</p>';
		$plxMotor = plxMotor::getInstance();
		header('location:'.$plxMotor->urlRewrite());
		unset($_GET['q']);
		exit();
	}
}
if(!empty($_POST) && empty($_POST['wall-e'])) {

	$nom=strtolower(trim(plxUtils::strCheck($_POST['nom_'.$id])));
	$prenom=strtolower(trim(plxUtils::strCheck($_POST['prenom_'.$id])));

	if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
		$activite=trim(plxUtils::strCheck($_POST['activite_'.$id]));
		$autre_activite=trim(plxUtils::strCheck($_POST['autre_activite_'.$id]));
		$etablissement=trim(plxUtils::strCheck($_POST['etablissement_'.$id]));
		$service=trim(plxUtils::strCheck($_POST['service_'.$id]));
		$tel_office=str_replace(array('.','-',' ','_','+','(',')',',',':',';','/'),'',plxUtils::strCheck($_POST['tel_office_'.$id]));
		if($activite =='')
			$error[] = $plxPlugin->getLang('L_ERR_ACTIVITE');
		if($activite =='autre' && trim($autre_activite) == '')
			$error[] = $plxPlugin->getLang('L_ERR_AUTRE_ACTIVITE');
		if(trim($etablissement) == '')
			$error[] = $plxPlugin->getLang('L_ERR_ETABLISSEMENT');
		if(trim($service) == '')
			$error[] = $plxPlugin->getLang('L_ERR_SERVICE');
		if(trim($tel_office) != '' && !preg_match('!^[0-9]{9,13}[0-9]$!',$tel_office))
			$error[] = $plxPlugin->getLang('L_ERR_TEL');
		$pro = array(
			'activite'=>$activite,
			'autre_activite'=>$autre_activite,
			'etablissement'=>$etablissement,
			'service'=>$service,
			'tel_office'=>$tel_office,
			'coordonnees'=>''
		);
		if($plxPlugin->getParam('showAnnuaire') == 'on') {
			$coordonnees=$_POST['coordonnees_'.$id];
			if(trim($coordonnees) == '')
				$error[] = $plxPlugin->getLang('L_ERR_COORDONNEES');
			else
				$pro['coordonnees'] = $coordonnees;
		}
	
	}
	$adresse1=trim(plxUtils::strCheck($_POST['adresse1_'.$id]));
	$adresse2=trim(plxUtils::strCheck($_POST['adresse2_'.$id]));
	$cp=intval($_POST['cp_'.$id]);
	$ville=trim(plxUtils::strCheck($_POST['ville_'.$id]));
	$tel=str_replace(array('.','-',' ','_','+','(',')',',',':',';','/'),'',$_POST['tel_'.$id]);
	$mail=trim(str_replace('&#64;', '@', plxUtils::strCheck($_POST['mail_'.$id])));
	$choix=plxUtils::strCheck($_POST['choix_'.$id]);
	$mailing=plxUtils::strCheck($_POST['mailing_'.$id]);
	if(trim($nom)=='')
		$error[] = $plxPlugin->getLang('L_ERR_NAME');
	if(trim($prenom)=='')
		$error[] = $plxPlugin->getLang('L_ERR_FIRST_NAME');
	if(trim($adresse1) == '')
		$error[] = $plxPlugin->getLang('L_ERR_ADRESSE');
	if(trim($cp) == '' || strlen($cp) != 5 || !is_int($cp))
		$error[] = $plxPlugin->getLang('L_ERR_CP');
	if(trim($ville) == '')
		$error[] = $plxPlugin->getLang('L_ERR_VILLE');
	if(trim($tel) == '' || !preg_match('!^[0-9]{9,13}[0-9]$!',$tel))
		$error[] = $plxPlugin->getLang('L_ERR_TEL');
	if(trim($choix) == '')
		$error[] = $plxPlugin->getLang('L_ERR_CHOIX');
	if(trim($mailing) == '')
		$error[] = $plxPlugin->getLang('L_ERR_MAILING');
	if(!plxUtils::checkMail($mail))
		$error[] = $plxPlugin->getLang('L_ERR_MAIL');
	if($plxShow->plxMotor->aConf['capcha'] AND $_POST['rep2'] != sha1($_POST['rep']))
		$error[] = $plxPlugin->getLang('L_ERR_ANTISPAM');
	foreach ($plxPlugin->adherentsList as $adherent) {
		if ($nom.' '.$prenom == $adherent['nom'].' '.$adherent['prenom'] && $mail == $adherent['mail'] && $adherent['validation'] == 1 && $choix == 'adhesion') {
			$error['extra'] = $plxPlugin->getLang('L_ERR_USER_ALREADY_USED');
		}
	}
	if(empty($error)) {
		$content = $plxPlugin->notification($nom,$prenom,$adresse1,$adresse2,$cp,$ville,$tel,$mail,$choix,$mailing,$pro);
		# On édite la liste des adhérents
		$edit = $plxPlugin->editAdherentslist($_POST,$id);
		
		
		if ($choix != 'stop') {
			//Si pas d'erreur, envoie du mail à l'admin contenant les informations de l'adhérent
			if($plxPlugin->sendEmail($plxPlugin->getParam('nom_asso'),$plxPlugin->getParam('email'),$plxPlugin->getParam('email'),$plxPlugin->getParam('subject'),$content,'html')){
				if ($choix == 'renouveler') {
					//Envoie du mail à l'adhérent
					if($plxPlugin->sendEmail($plxPlugin->getParam('nom_asso'),$plxPlugin->getParam('email'),$mail,$plxPlugin->getParam('subject'),$plxPlugin->getParam('thankyou').$plxPlugin->adresse(),'html')) {
						if (empty($error)){
							$success = $plxPlugin->getParam('thankyou');
						}
					}
				} elseif (empty($error)){
					$success = $plxPlugin->getParam('thankyou');
				}
			}
		} elseif($choix == 'stop') {
			$info = $plxPlugin->getLang('L_FORM_ERASE');
		} 

	}
} else {
	$nom = '';
	$prenom = '';
	$adresse1 = '';
	$adresse2 = '';
	$cp = '';
	$ville = '';
	$tel = '';
	$mail = '';
	$choix = '';
	$mailing = '';
	if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
		$activite = '';
		$autre_activite = '';
		$etablissement = '';
		$service = '';
		$tel_office = '';
	}
	if($plxPlugin->getParam('showAnnuaire') == 'on') {
		$coordonnees='';
	}
}
?>
<div id="form_adherer">
	<?php  $_POST = '';
if(!empty($info)) { ?>

		<p class="contact_success"><?php echo $info; ?></p>
	<?php 
	$nom = '';
	$prenom = '';
	$adresse1 = '';
	$adresse2 = '';
	$cp = '';
	$ville = '';
	$tel = '';
	$mail = '';
	$choix = '';
	$mailing = '';
	if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
		$activite = '';
		$autre_activite = '';
		$etablissement = '';
		$service = '';
		$tel_office = '';
	}
	if($plxPlugin->getParam('showAnnuaire') == 'on') {
		$coordonnees='';
	}
} else {
		if(!empty($error)): ?>

		<div class="contact_error">
			<?php if(isset($error['extra'])) :
				echo '<p>'.$error['extra'].'</p>';
				unset($error['extra']); 
		 	endif; 
		 	if(!empty($error)): ?>

				<h3>Un ou plusieurs champs n'ont pas été convenablement complétés :</h3>
				<ul>
					<?php foreach ($error as $e) {

					echo '<li>'.$e.'</li>
						';
				} ?>

				</ul>
			<?php endif; ?>

		</div>
	<?php endif; ?>
	<?php unset($_POST);
if($success): $_POST = '';
		$l = current($plxPlugin->plxRecord_adherents->result);?>

	<p id="showpass"><?php echo $plxPlugin->getLang('L_ADMIN_ID'); ?><br/>
	<?php echo $plxPlugin->getLang('L_ADMIN_PASSWORD');?>&nbsp;<?php echo $l['cle'].'-'.substr($l['mail'],0,-$l['rand1']).$l['rand2']; ?>

	</p>

	<?php
	 //On affiche les instructions pour régler l'adhésion
		echo $plxPlugin->adresse();
else:?>

	<p id="all_required"><?php $plxPlugin->lang('L_FORM_ALL_REQUIRED');?></p>
	<form action="#form" method="post">
		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_IDENTITY');?></h2></legend>
		<p>	
			<label for="name"><?php $plxPlugin->lang('L_FORM_NAME') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="name" name="nom_<?php echo $id?>" type="text" size="30" value="<?php echo plxUtils::strCheck($nom) ?>" maxlength="30" />
		</p>
		<p>
			<label for="firstname"><?php $plxPlugin->lang('L_FORM_FIRST_NAME') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="firstname" name="prenom_<?php echo $id?>" type="text" size="30" value="<?php echo plxUtils::strCheck($prenom) ?>" maxlength="30" />
		</p>
		</fieldset>
		<?php if($plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_ACTIVITY');?></h2></legend>
		<p>
			<input id="arc" name="activite_<?php echo $id?>" type="radio" value="arc" <?php echo plxUtils::strCheck($activite) == 'arc'? 'checked="checked"' : ''; ?> />
			<label for="arc">ARC</label>
		</p>
		<p>
			<input id="tec" name="activite_<?php echo $id?>" type="radio" value="tec" <?php echo plxUtils::strCheck($activite) == 'tec'? 'checked="checked"' : ''; ?> />
			<label for="tec">TEC</label>
		</p>
		<p>
			<input id="irc" name="activite_<?php echo $id?>" type="radio" value="irc" <?php echo plxUtils::strCheck($activite) == 'irc'? 'checked="checked"' : ''; ?> />
			<label for="irc">IRC</label>
		</p>
		<p>
			<input id="autre" name="activite_<?php echo $id?>" type="radio" value="autre" <?php echo plxUtils::strCheck($activite) == 'autre' ? 'checked="checked"' : ''; ?> />
			<label for="autre"><?php $plxPlugin->lang('L_FORM_OTHER') ?></label>
		</p>
		<p class="mask">
			<label for="autre_activite"><?php echo $plxPlugin->lang('L_FORM_DETAIL');?>&nbsp;:</label>
		</p>
		<p class="mask">
			<input id="autre_activite" name="autre_activite_<?php echo $id?>" type="text" value="<?php echo plxUtils::strCheck($autre_activite);?>" />
		</p>
		</fieldset>
		<?php endif; ?>

		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_AGENDA');?></h2></legend>
		<?php if($plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

		<p>	
			<label for="etablissement"><?php $plxPlugin->lang('L_FORM_SOCIETY') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="etablissement" name="etablissement_<?php echo $id?>" type="text" size="50" value="<?php echo plxUtils::strCheck($etablissement) ?>" maxlength="50" />
		</p>
		<p>	
			<label for="service"><?php $plxPlugin->lang('L_FORM_SERVICE') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="service" name="service_<?php echo $id?>" type="text" size="50" value="<?php echo plxUtils::strCheck($service) ?>" maxlength="50" />
		</p>
		<?php endif; ?>

		<p>	
			<label for="adresse1"><?php $plxPlugin->lang('L_FORM_ADDRESS') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="adresse1" name="adresse1_<?php echo $id?>" type="text" size="50" value="<?php echo plxUtils::strCheck($adresse1) ?>" maxlength="50" />
		</p>
		<p>
			<input id="adresse2" name="adresse2_<?php echo $id?>" type="text" size="50" value="<?php echo plxUtils::strCheck($adresse2) ?>" maxlength="50" />
		</p>
		<p>	
			<label for="cp"><?php $plxPlugin->lang('L_FORM_ZIP_CODE') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="cp" name="cp_<?php echo $id?>" type="text" size="5" value="<?php echo plxUtils::strCheck($cp) ?>" maxlength="5" />
		</p>
		<p>	
			<label for="ville"><?php $plxPlugin->lang('L_FORM_CITY') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="ville" name="ville_<?php echo $id?>" type="text" size="50" value="<?php echo plxUtils::strCheck($ville) ?>" maxlength="50" />
		</p>
		<p>	
			<label for="tel"><?php $plxPlugin->lang('L_FORM_TEL') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="tel" name="tel_<?php echo $id?>" type="text" size="50" value="<?php echo plxUtils::strCheck($tel) ?>" maxlength="50" />
		</p>
		<?php if($plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

		<p>	
			<label for="tel_office"><?php $plxPlugin->lang('L_FORM_TEL_OFFICE') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="tel_office" name="tel_office_<?php echo $id?>" type="text" size="50" value="<?php echo plxUtils::strCheck($tel_office) ?>" maxlength="50" />
		</p>
		<?php endif; ?>

		<p>
			<label for="mail"><?php $plxPlugin->lang('L_FORM_MAIL') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="mail" name="mail_<?php echo $id?>" type="text" size="30" value="<?php echo ($mail != '')? str_replace('@','&#64;',$mail):''; ?>" />
		</p>
		</fieldset>
		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_CHOICE');?></h2></legend>
		<p>
			<input id="adhesion" name="choix_<?php echo $id?>" type="radio" value="adhesion" <?php echo plxUtils::strCheck($choix) == 'adhesion'? 'checked="checked"' : ''; ?> />
			<label for="adhesion">Je souhaite devenir adhérent de l’Association <?php echo $plxPlugin->getParam('nom_asso'); ?></label>
		</p>
		<p>
			<input id="renouveler" name="choix_<?php echo $id?>" type="radio" value="renouveler" <?php echo plxUtils::strCheck($choix) == 'renouveler'? 'checked="checked"' : ''; ?> />
			<label for="renouveler">Je souhaite renouveler mon adhésion</label>
		</p>
		<p>
			<input id="stop" name="choix_<?php echo $id?>" type="radio" value="stop" <?php echo plxUtils::strCheck($choix) == 'stop'? 'checked="checked"' : ''; ?> />
			<label for="stop">Je ne souhaite plus être membre de l’Association <?php echo $plxPlugin->getParam('nom_asso'); ?></label>
		</p>
		<?php if($plxPlugin->getParam('showAnnuaire') == 'on' && $plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_SHARING');?></h2></legend>
		<p>
			<input id="rec" name="coordonnees_<?php echo $id?>" type="radio" value="rec" <?php echo plxUtils::strCheck($coordonnees) == 'rec' ? 'checked="checked"' : ''; ?> />
			<label for="rec">J’accepte que mes coordonnées professionnelles figurent sur le site de l’Association <?php echo $plxPlugin->getParam('nom_asso'); ?></label>
		</p>
		<p>
			<input id="refus" name="coordonnees_<?php echo $id?>" type="radio" value="refus" <?php echo plxUtils::strCheck($coordonnees) == 'refus' ? 'checked="checked"' : ''; ?> />
			<label for="refus">Je refuse que mes coordonnées professionnelles figurent sur le site de l’Association <?php echo $plxPlugin->getParam('nom_asso'); ?></label>
		</p>
		</fieldset>
		<?php endif; ?>

		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_MAILING');?></h2></legend>
		<p>
			<input id="maillist" name="mailing_<?php echo $id?>" type="radio" value="maillist" <?php echo plxUtils::strCheck($mailing) == 'maillist' ? 'checked="checked"' : ''; ?> />
			<label for="maillist">J’accepte de recevoir par mail toute information concernant le site de l’Association <?php echo $plxPlugin->getParam('nom_asso'); ?></label>
		</p>
		<p>
			<input id="blacklist" name="mailing_<?php echo $id?>" type="radio" value="blacklist" <?php echo plxUtils::strCheck($mailing) == 'blacklist' ? 'checked="checked"' : ''; ?> />
			<label for="blacklist">Je refuse de recevoir des informations concernant le site sur ma messagerie</label>
		</p>
		<p id="wall-e">
			<label for="walle">Si vous souhaitez que votre demande ne soit jamais prise en compte, remplissez ce champ ^_^</label>
			<input id="walle" name="wall-e" type="text" size="50" value="<?php echo plxUtils::strCheck($wall_e) ?>" maxlength="50" />
		</p>
		</fieldset>
		<fieldset>
		<?php if($plxShow->plxMotor->aConf['capcha']): ?>
		<p>
			<label for="id_rep"><strong><?php $plxPlugin->lang('L_FORM_ANTISPAM') ?></strong>&nbsp;:</label>
		</p>
			<?php echo $plxShow->capchaQ() ?>&nbsp;:&nbsp;<input id="id_rep" name="rep" type="text" size="10" />	
			<input name="rep2" type="hidden" value="<?php echo $plxShow->capchaR() ?>" />
		<?php endif; ?>
		<p>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_BTN_SEND') ?>" />
		</p>
		</fieldset>
	</form>
	<?php if($plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

	<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(function($){
			$('.mask').css({'display':'none'});
			$("#autre").click(function() {
				$('.mask').css({'display':'block'});
				$('#autre_activite').select();
			});
			$("#arc").click(function() {
				$('.mask').css({'display':'none'});
			});
			$("#tec").click(function() {
				$('.mask').css({'display':'none'});
			});
			$("#irc").click(function() {
				$('.mask').css({'display':'none'});
			});
		});
		/* ]]> */
	</script>
	<?php endif; ?>

<?php endif;
} ?>
</div>