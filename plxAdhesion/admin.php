<?php 
/**
 * Plugin adhesion
 *
 * @version	1.5
 * @date	07/10/2013
 * @author	Stephane F, Cyril MAGUIRE
 **/

if(!defined('PLX_ROOT')) exit; 

$plxMotor = plxMotor::getInstance();

# Control du token du formulaire
plxToken::validateFormToken($_POST);

$a = $plxPlugin->getAdherents('/^[0-9]{5}.(.[a-z-]+){2}.[0-9]{10}.xml$/');

//$aActivites = array('arc'=>'ARC','tec'=>'TEC','irc'=>'IRC','autre'=>'Autre');
$aA = explode(',',$plxPlugin->getParam('tabActivites'));
$aK = array_map('strtolower', $aA);
$aActivites = array_combine($aK, $aA);

$enteteTableau = '
		<th>'.$plxPlugin->getLang('L_ADMIN_LIST_NAME').'<br/>
			'.$plxPlugin->getLang('L_ADMIN_LIST_FIRST_NAME').'</th>
		<th>'.$plxPlugin->getLang('L_ADMIN_LIST_ADRESSE').'<br/>
			'.$plxPlugin->getLang('L_ADMIN_LIST_ZIP_CODE').'&nbsp;
			'.$plxPlugin->getLang('L_ADMIN_LIST_CITY').'</th>
		<th>'.$plxPlugin->getLang('L_ADMIN_LIST_TEL').'<br/>
			'.$plxPlugin->getLang('L_ADMIN_LIST_MAIL').'</th>
		<th>'.$plxPlugin->getLang('L_ADMIN_LIST_CHOICE').'</th>
';
if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
$enteteTableau = '
		<th>'.$plxPlugin->getLang('L_ADMIN_LIST_NAME').'<br/>
			'.$plxPlugin->getLang('L_ADMIN_LIST_FIRST_NAME').'</th>
		<th>'.$plxPlugin->getLang('L_ADMIN_LIST_ACTIVITY').'<br/>
			'.$plxPlugin->getLang('L_ADMIN_LIST_STRUCTURE').'<br/>
			'.$plxPlugin->getLang('L_ADMIN_LIST_DPT').'</th>
		<th>'.$plxPlugin->getLang('L_ADMIN_LIST_ADRESSE').'<br/>
			'.$plxPlugin->getLang('L_ADMIN_LIST_ZIP_CODE').'&nbsp;
			'.$plxPlugin->getLang('L_ADMIN_LIST_CITY').'</th>
		<th>'.$plxPlugin->getLang('L_ADMIN_LIST_TEL').'<br/>
			'.$plxPlugin->getLang('L_ADMIN_LIST_TEL_OFFICE').'<br/>
			'.$plxPlugin->getLang('L_ADMIN_LIST_MAIL').'</th>
		<th>'.$plxPlugin->getLang('L_ADMIN_LIST_CHOICE').'</th>
';
}


//Mot de passe oublié, on renvoie la clé si l'email correspond
if(isset($_GET['forgetmypass']) && !empty($_GET['forgetmypass'])) {
	$mail = str_replace('true&mail=','',base64_decode($_GET['forgetmypass']));
	if($plxPlugin->retrieveMyPass(plxUtils::strCheck($mail) )) {
		$_SESSION['info'] = $plxPlugin->getLang('L_PASS_SENT');
		header('Location: plugin.php?p=adhesion');
		exit();
	}
}

# On édite les catégories
if(!empty($_POST)) {

	//echo'<pre>';print_r($_POST);echo '</pre>';exit();
	if ($_POST['nom_'.$_POST['adherentNum'][0]] =='nom'){
		unset($_POST['nom_'.$_POST['adherentNum'][0]]);
		unset($_POST['prenom_'.$_POST['adherentNum'][0]]);
		unset($_POST['adresse1_'.$_POST['adherentNum'][0]]);
		unset($_POST['adresse2_'.$_POST['adherentNum'][0]]);
		unset($_POST['cp_'.$_POST['adherentNum'][0]]);
		unset($_POST['ville_'.$_POST['adherentNum'][0]]);
		unset($_POST['tel_'.$_POST['adherentNum'][0]]);
		unset($_POST['mail_'.$_POST['adherentNum'][0]]);
		unset($_POST['choix_'.$_POST['adherentNum'][0]]);
		unset($_POST['mailing_'.$_POST['adherentNum'][0]]);
		unset($_POST['adherentNum']);
		if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
			unset($_POST['activite_'.$_POST['adherentNum'][0]]);
			unset($_POST['activite_autre_'.$_POST['adherentNum'][0]]);
			unset($_POST['etablissement_'.$_POST['adherentNum'][0]]);
			unset($_POST['service_'.$_POST['adherentNum'][0]]);
			unset($_POST['tel_office_'.$_POST['adherentNum'][0]]);
			if ($plxPlugin->getParam('showAnnuaire') == 'on') {
				unset($_POST['coordonnees_'.$_POST['adherentNum'][0]]);
			}
		}
	}
		$plxPlugin->editAdherentsList($_POST,$_POST['idAdherent']);
		header('Location: plugin.php?p=adhesion');
		exit;
}
?>

<style type="text/css">
	table {
		font-size:1em;
	}
	tr.line-0, tr.line-0 input, tr.new input {
		background: #E1E1E1;
	}
	tr input {
		border:none;
		text-transform:capitalize;
	}
	tr input.clefs {
		border:none;
		text-transform:none;
	}
	tr input.email {
		border:none;
		text-transform:lowercase;
	}
	tr.new {
		background: #D6E3B3;
	}
	tr.new input.ville {
		width:130px;
	}
	table select {
		width:260px;
	}
	table select.activite, table select.activite_new {
		width:auto;
	}
	#title {
		position:relative;
		margin:0;
		border-bottom: 1px solid #D6D6D6;
		padding:20px;
		font-size: 2em;
	}
	#tabs {
		position:absolute;
		bottom:0;
		right:0;
	}
	#tabs a {
		font-size: 0.5em;
		border:1px solid #D6D6D6;
		border-bottom:none;
		padding:15px 10px 10px 10px;
		margin-bottom: -1px;
		z-index: 99999;
		-moz-border-radius-topright: 5px;
		-webkit-border-top-right-radius: 5px;
		border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-webkit-border-top-left-radius: 5px;
		border-top-left-radius: 5px;
		display: inline-block;
		height:15px;
		line-height:15px;
		background:#D3D3D3;
	}
	#tabs a:hover, #tabs a.current {
		background: #FFF;
		border:1px solid #D6D6D6;
		border-bottom:none;
	}
	#attente, #validees {
		border-left:1px solid #D6D6D6;
		border-right:1px solid #D6D6D6;
		border-bottom:1px solid #D6D6D6;
		padding:10px;
	}
	.stop {
		background-color: red;
	}
	.petit {
		font-size: 0.8em;
	}
	#export {
		font-size: 0.7em;
		position: relative;
	}
	#export ul li{
		display: inline;
	}
</style>
<div id="title"><?php $plxPlugin->lang('L_TITLE_CONFIG'); ?>
	<div id="export">
		<p>Exporter les données</p>
		<ul>
			<li><a href="plugin.php?p=adhesion&print=ods" title="Format ods (LibreOffice)"><img src="<?php echo PLX_PLUGINS.'adhesion/opentbs/ods.png' ?> " alt="Format ods" width="32" height="32"/></a></li>
			<li><a href="plugin.php?p=adhesion&print=xlsx" title="Format xlsx (Excel 2010)"><img src="<?php echo PLX_PLUGINS.'adhesion/opentbs/xlsx.png' ?> " alt="Format xlsx" width="32" height="32"/></a></li>
			<li><a href="plugin.php?p=adhesion&print=xls" title="Format xls (Excel 2003)"><img src="<?php echo PLX_PLUGINS.'adhesion/opentbs/xls.png' ?> " alt="Format xls" width="32" height="32"/></a></li>
		</ul>
	</div>
	<div id="tabs" class="css-tabs history">
		<a href="#attente" id="tabattente"><?php $plxPlugin->lang('L_ADMIN_LIST_MEMBERS_TO_VALIDATE')?></a>
		<a href="#validees" id="tabvalidees"><?php $plxPlugin->lang('L_ADMIN_LIST_MEMBERS_VALIDATED')?></a>
	</div>
</div>
<div id="listes">
<div id="attente">
	<?php eval($plxAdmin->plxPlugins->callHook('AdhesionUsersTop')) # Hook Plugins ?>

<form action="plugin.php?p=adhesion" method="post" id="form">
	<p>
		<?php plxUtils::printSelect('selection[]', array( 'false' => $plxPlugin->getLang('L_ADMIN_SELECTION'), 'validation' => $plxPlugin->getLang('L_ADMIN_LIST_VALIDATION'), 'update' => $plxPlugin->getLang('L_ADMIN_LIST_UPDATE'), '-' => '-----','delete' => $plxPlugin->getLang('L_ADMIN_DELETE')), '') ?>
		<input class="button submit" type="submit" name="submit" value="<?php echo L_OK ?>" />
	</p>
	<table class="table" id="table">
	<caption style="text-align:left"><?php $plxPlugin->lang('L_ADMIN_LIST_MEMBERS_TO_VALIDATE')?></caption>
	<thead>
		<tr class="new">
			<th class="checkbox"><input type="checkbox" onclick="checkAll(this.form, 'idAdherent[]');" /></th>
			<th class="title"><?php $plxPlugin->lang('L_ADMIN_LIST_ID'); ?><br/><span class="petit"><?php $plxPlugin->lang('L_ADMIN_FIRST_ASK'); ?></span></th>
			<?php echo $enteteTableau; ?>

		</tr>
	</thead>
	<tbody>
		<?php 
		$num = 0;
		if($a) { # On a des adhérents
		# Initialisation de l'ordre
		$num=0;
		while($plxPlugin->plxRecord_adherents->loop()) { # Pour chaque adhérent
			$ad = $plxPlugin->plxRecord_adherents;
				if ($ad->f('validation') == 0) {
				$ordre = ++$num;
				if (!array_key_exists($ad->f('activite'),$aActivites)) {
					$activite = 'autre';
					$autre_activite = $ad->f('activite');
				} else {
					$activite = $ad->f('activite');
					$autre_activite = '';
				}
				echo "\n".'<!-- ADHERENT -->'."\n";
				echo '<tr class="line-'.($num%2).'">';
				echo '<td><input type="checkbox" name="idAdherent[]" value="'.$ad->f('id').'" /><input type="hidden" name="update" value="true" /><input type="hidden" name="validation_'.$ad->f('id').'" value="1" /></td>';
				echo '<td>'.$ad->f('id'); echo ($ad->f('firstDate') != ''? '<br/><span class="petit">'.date('d/m/y',intval(plxUtils::strCheck($ad->f('firstDate')))).'</span>' : '');echo ($ad->f('date')!='' && $ad->f('validation') == 0) ? '<br/><span class="petit">'.$plxPlugin->getLang('L_ADMIN_DATE_DEL').'<br/>'.date('d/m/y',intval(plxUtils::strCheck($ad->f('date')))).'</span>' : '<br/><span class="petit">'.$plxPlugin->getLang('L_FORM_NEW').'</span>';echo '</td><td>';
				plxUtils::printInput('nom_'.$ad->f('id'), strtoupper(plxUtils::strCheck($ad->f('nom'))), 'text', '15-50','','',$num);
				plxUtils::printInput('prenom_'.$ad->f('id'), ucfirst(plxUtils::strCheck($ad->f('prenom'))), 'text', '15-50','','',$num);
				echo '</td><td>';
				if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
				plxUtils::printSelect('activite_'.$ad->f('id'), $aActivites, $activite,'','activite_new');
				plxUtils::printInput('activite_autre_'.$ad->f('id'), $autre_activite, 'text', '15-50','','autre');
				plxUtils::printInput('etablissement_'.$ad->f('id'), plxUtils::strCheck($ad->f('etablissement')), 'text', '15-150');
				plxUtils::printInput('service_'.$ad->f('id'), plxUtils::strCheck($ad->f('service')), 'text', '15-150');
				echo '</td><td>';
				}
				plxUtils::printInput('adresse1_'.$ad->f('id'), plxUtils::strCheck($ad->f('adresse1')), 'text', '25-250');
				plxUtils::printInput('adresse2_'.$ad->f('id'), plxUtils::strCheck($ad->f('adresse2')), 'text', '25-250');
				echo '<br/>';
				plxUtils::printInput('cp_'.$ad->f('id'), plxUtils::strCheck($ad->f('cp')), 'text', '5-6');
				plxUtils::printInput('ville_'.$ad->f('id'), plxUtils::strCheck($ad->f('ville')), 'text', '15-150');
				echo '</td><td>';
				plxUtils::printInput('tel_'.$ad->f('id'), plxUtils::strCheck($ad->f('tel')), 'text', '15-15');
				echo '<br/>';
				if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
				plxUtils::printInput('tel_office_'.$ad->f('id'), plxUtils::strCheck($ad->f('tel_office')), 'text', '15-15');
				echo '<br/>';
				}
				plxUtils::printInput('mail_'.$ad->f('id'), plxUtils::strCheck($ad->f('mail')), 'text', '25-150',false,'email');
				echo '</td><td>';
				plxUtils::printSelect('choix_'.$ad->f('id'), array('adhesion'=>'Souhaite devenir adhérent','renouveler'=>'Souhaite renouveler son adhésion'), plxUtils::strCheck($ad->f('choix')));
				if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel' && $plxPlugin->getParam('showAnnuaire') == 'on') {
					plxUtils::printSelect('coordonnees_'.$ad->f('id'), array('rec'=>'Accepte la diffusion de ses coordonnées','refus'=>'Refuse la diffusion de ses coordonnées'), plxUtils::strCheck($ad->f('coordonnees')));
				}
				plxUtils::printSelect('mailing_'.$ad->f('id'), array('maillist'=>'Accepte les mails','blacklist'=>'Refuse les mails'), plxUtils::strCheck($ad->f('mailing')));
				echo '</td></tr>';
				}
			}
			if ($num == 0) {
				echo '<tr><td colspan="8" style="text-align:center;"><strong>'.$plxPlugin->getLang('L_ADMIN_VALIDATION_PENDING').'</strong></td></tr>';
			}
			# On récupère le dernier identifiant
			$lastId = array_keys($plxPlugin->plxRecord_adherents);
			rsort($lastId);
		}
		else {
			echo '<tr><td colspan="8" style="text-align:center;"><strong>'.$plxPlugin->getLang('L_ADMIN_VALIDATION_PENDING').'</strong></td></tr>';
			$lastId[1] = 0;
		}
		$new_adherentid = $plxPlugin->nextIdAdherent();
		//Tableau pour saisir nouvel adhérent sans passer par la partie publique
		 ?>

		<!-- NOUVEL ADHERENT -->
		 <tr class="new">
			<th class="checkbox"><input type="checkbox" onclick="checkAll(this.form, 'idAdherent[]')" /></th>
			<th class="title"><?php $plxPlugin->lang('L_ADMIN_LIST_ID'); ?></th>
			
			<?php echo $enteteTableau; ?>
			
		</tr>
		 <tr>
			<?php
				echo '<td><input type="hidden" name="adherentNum[]" value="'.$new_adherentid.'" /><input type="hidden" name="new" value="'.$new_adherentid.'" /><input class="button update " type="submit" name="update" value="'.L_OK.'" /></td>';
				echo '<td>'.$plxPlugin->getLang('L_ADMIN_LIST_NEW').'</td><td>';
				
				plxUtils::printInput('nom_'.$new_adherentid, 'nom', 'text', '15-50',false,'" onfocus="if (this.value == \'nom\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'nom\';}" onsubmit="if (this.value == \'nom\') {this.value = \'\';}');
				plxUtils::printInput('prenom_'.$new_adherentid, 'prénom', 'text', '15-50',false,'" onfocus="if (this.value == \'prénom\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'prénom\';}" onsubmit="if (this.value == \'prénom\') {this.value = \'\';}');
				echo '</td><td>';
				if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
				plxUtils::printSelect('activite_'.$new_adherentid, $aActivites, '','','activite_new');
				plxUtils::printInput('activite_autre_'.$new_adherentid, '', 'text', '15-50','','autre');
				plxUtils::printInput('etablissement_'.$new_adherentid, 'établissement', 'text', '15-150',false,'" onfocus="if (this.value == \'établissement\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'établissement\';}" onsubmit="if (this.value == \'établissement\') {this.value = \'\';}');
				plxUtils::printInput('service_'.$new_adherentid, 'service', 'text', '15-150',false,'" onfocus="if (this.value == \'service\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'service\';}" onsubmit="if (this.value == \'service\') {this.value = \'\';}');
				echo '</td><td>';
				}
				plxUtils::printInput('adresse1_'.$new_adherentid, 'adresse', 'text', '25-250',false,'" onfocus="if (this.value == \'adresse\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'adresse\';}" onsubmit="if (this.value == \'adresse\') {this.value = \'\';}');
				plxUtils::printInput('adresse2_'.$new_adherentid, '', 'text', '25-250');
				echo '<br/>';
				plxUtils::printInput('cp_'.$new_adherentid, 'cp', 'text', '5-6',false,'" onfocus="if (this.value == \'cp\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'cp\';}" onsubmit="if (this.value == \'cp\') {this.value = \'\';}').'&nbsp;';
				plxUtils::printInput('ville_'.$new_adherentid, 'ville', 'text', '15-150',false,'" onfocus="if (this.value == \'ville\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'ville\';}" onsubmit="if (this.value == \'ville\') {this.value = \'\';}');
				echo '</td><td>';
				plxUtils::printInput('tel_'.$new_adherentid, 'téléphone', 'text', '15-15',false,'" onfocus="if (this.value == \'téléphone\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'téléphone\';}" onsubmit="if (this.value == \'téléphone\') {this.value = \'\';}');
				echo '<br/>';
				if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
				plxUtils::printInput('tel_office_'.$new_adherentid, '', 'text', '15-15');
				echo '<br/>';
				}
				plxUtils::printInput('mail_'.$new_adherentid, 'email', 'text', '25-150',false,'email" onfocus="if (this.value == \'email\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'email\';}" onsubmit="if (this.value == \'email\') {this.value = \'\';}');
				echo '</td><td>';
				plxUtils::printSelect('choix_'.$new_adherentid, array('adhesion'=>'Souhaite devenir adhérent','renouveler'=>'Souhaite renouveler son adhésion'), '');
				if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel' && $plxPlugin->getParam('showAnnuaire') == 'on') {
					plxUtils::printSelect('coordonnees_'.$new_adherentid, array('rec'=>'Accepte la diffusion de ses coordonnées','refus'=>'Refuse la diffusion de ses coordonnées'), '');
				}
				plxUtils::printSelect('mailing_'.$new_adherentid, array('maillist'=>'Accepte les mails','blacklist'=>'Refuse les mails'), '');
				echo '</td>';
			?>

		</tr>
	</tbody>
	</table>

	<p>
	<?php echo plxToken::getTokenPostMethod() ?>
		
	</p>
	<p>
		<?php plxUtils::printSelect('selection[]', array( 'false' => $plxPlugin->getLang('L_ADMIN_SELECTION'), 'validation' => $plxPlugin->getLang('L_ADMIN_LIST_VALIDATION'), 'update' => $plxPlugin->getLang('L_ADMIN_LIST_UPDATE'), '-' => '-----', 'delete' => $plxPlugin->getLang('L_ADMIN_DELETE')), '') ?>
		<input class="button submit" type="submit" name="submit" value="<?php echo L_OK ?>" />
	</p>

</form>
</div>
<!-- ADHERENTS VALIDES -->
<div id="validees">
	<?php eval($plxAdmin->plxPlugins->callHook('AdhesionUsersTopV')) # Hook Plugins ?>

<form action="plugin.php?p=adhesion" method="post" id="form2">
	<p>
		<?php plxUtils::printSelect('selection[]', array( 'false' => $plxPlugin->getLang('L_ADMIN_SELECTION'), 'validation' => $plxPlugin->getLang('L_ADMIN_LIST_DEVALIDATION'), 'update' => $plxPlugin->getLang('L_ADMIN_LIST_UPDATE'), '-' => '-----', 'delete' => $plxPlugin->getLang('L_ADMIN_DELETE')), '') ?>
		<input class="button submit" type="submit" name="submit" value="<?php echo L_OK ?>" />
	</p>
	<table class="table" id="tableV">
	<caption style="text-align:left"><?php $plxPlugin->lang('L_ADMIN_LIST_MEMBERS_VALIDATED')?></caption>
	<thead>
		<tr class="new">
			<th class="checkbox"><input type="checkbox" onclick="checkAll(this.form, 'idAdherent[]')" /></th>
			<th class="title"><?php $plxPlugin->lang('L_ADMIN_LIST_ID'); ?><br/><span class="petit"><?php $plxPlugin->lang('L_ADMIN_DATE_VAL'); ?></span></th>
			<?php echo $enteteTableau; ?>
			
			<th><?php $plxPlugin->lang('L_ADMIN_ACTION') ?>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		if($a) { # On a des adhérents
		# Initialisation de l'ordre
		$num=0;
		while($plxPlugin->plxRecord_adherents->loop()) { # Pour chaque adhérent
			$ad = $plxPlugin->plxRecord_adherents;
				if ($ad->f('validation') == 1) {

				if (!in_array($ad->f('activite'),$aActivites)) {
					$activite = 'autre';
					$autre_activite = $ad->f('activite');
				} else {
					$activite = $ad->f('activite');
					$autre_activite = '';
				}
				$ordre = ++$num;
				echo "\n".'<!-- ADHERENT -->'."\n";
				echo '<tr class="line-'.($num%2).'">';
				echo '<td><input type="checkbox" name="idAdherent[]" value="'.$ad->f('id').'" /><input type="hidden" name="update" value="true" /><input type="hidden" name="validation_'.$ad->f('id').'" value="0" /></td>';
				echo '<td>'.$ad->f('id').'<br/><span class="petit">'.date('d/m/y',intval(plxUtils::strCheck($ad->f('date')))).'</span></td><td>';
				plxUtils::printInput('nom_'.$ad->f('id'), plxUtils::strCheck($ad->f('nom')), 'text', '15-50','','',$num);
				plxUtils::printInput('prenom_'.$ad->f('id'), plxUtils::strCheck($ad->f('prenom')), 'text', '15-50','','',$num);
				echo '</td><td>';
				if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
				plxUtils::printSelect('activite_'.$ad->f('id'), $aActivites, $activite,'','activite_new');
				plxUtils::printInput('activite_autre_'.$ad->f('id'), $autre_activite, 'text', '15-50','','autre');
				plxUtils::printInput('etablissement_'.$ad->f('id'), plxUtils::strCheck($ad->f('etablissement')), 'text', '15-150');
				plxUtils::printInput('service_'.$ad->f('id'), plxUtils::strCheck($ad->f('service')), 'text', '15-150');
				echo '</td><td>';
				}
				plxUtils::printInput('adresse1_'.$ad->f('id'), plxUtils::strCheck($ad->f('adresse1')), 'text', '25-250');
				plxUtils::printInput('adresse2_'.$ad->f('id'), plxUtils::strCheck($ad->f('adresse2')), 'text', '25-250');
				echo '<br/>';
				plxUtils::printInput('cp_'.$ad->f('id'), plxUtils::strCheck($ad->f('cp')), 'text', '5-6');
				plxUtils::printInput('ville_'.$ad->f('id'), plxUtils::strCheck($ad->f('ville')), 'text', '15-150');
				echo '</td><td>';
				plxUtils::printInput('tel_'.$ad->f('id'), plxUtils::strCheck($ad->f('tel')), 'text', '15-15');
				echo '<br/>';
				if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
				plxUtils::printInput('tel_office_'.$ad->f('id'), plxUtils::strCheck($ad->f('tel_office')), 'text', '15-15');
				echo '<br/>';
				}
				plxUtils::printInput('mail_'.$ad->f('id'), plxUtils::strCheck($ad->f('mail')), 'text', '25-150',false,'email');
				echo '</td><td>';
				plxUtils::printSelect('choix_'.$ad->f('id'), array('adhesion'=>$plxPlugin->getLang('L_ADMIN_FIRST_ASK').date('d/m/y',intval(plxUtils::strCheck($ad->f('firstDate')))),'renouveler'=>'Souhaite renouveler son adhésion','stop' =>'Ne souhaite plus être membre'), plxUtils::strCheck($ad->f('choix')),false,'choices');
				if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel' && $plxPlugin->getParam('showAnnuaire') == 'on') {
					plxUtils::printSelect('coordonnees_'.$ad->f('id'), array('rec'=>'Accepte la diffusion de ses coordonnées','refus'=>'Refuse la diffusion de ses coordonnées'), plxUtils::strCheck($ad->f('coordonnees')));
				}
				plxUtils::printSelect('mailing_'.$ad->f('id'), array('maillist'=>'Accepte les mails','blacklist'=>'Refuse les mails'), plxUtils::strCheck($ad->f('mailing')));
				echo '</td><td style="text-align:left"><a href="plugin.php?p=adhesion&forgetmypass='.base64_encode('true&mail='.$ad->f('mail')).'" title="'.$plxPlugin->getLang('L_ADMIN_SEND_PASS').'"><span style="font-size:3em;">&#9993;</span></a></td></tr>';
				}
			}
			if ($num == 0) {
				echo '<tr><td colspan="8" style="text-align:center;"><strong>'.$plxPlugin->getLang('L_ADMIN_NO_VALIDATION').'</strong></td></tr>';
			}
			# On récupère le dernier identifiant
			$a = array_keys($plxPlugin->adherentsList);
			rsort($a);
		}
		else {
			echo '<tr><td colspan="8" style="text-align:center;"><strong>'.$plxPlugin->getLang('L_ADMIN_NO_VALIDATION').'</strong></td></tr>';
			$a[1] = 0;
		}?>

	</tbody>
	</table>
	<p>
	<?php echo plxToken::getTokenPostMethod() ?>
		
	</p>
	<p>
		<?php plxUtils::printSelect('selection[]', array( 'false' => $plxPlugin->getLang('L_ADMIN_SELECTION'), 'validation' => $plxPlugin->getLang('L_ADMIN_LIST_DEVALIDATION'), 'update' => $plxPlugin->getLang('L_ADMIN_LIST_UPDATE'), '-' => '-----', 'delete' => $plxPlugin->getLang('L_ADMIN_DELETE')), '') ?>
		<input class="button submit" type="submit" name="submit" value="<?php echo L_OK ?>" />
	</p>

</form>
</div>
</div>
<?php eval($plxAdmin->plxPlugins->callHook('AdhesionUsersFoot')); ?>

<script type="text/javascript">
	(function($){
		<?php if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') :?>

		$('.autre').css({'display':'none'});
  		$('.activite_new').each(function() {
			var selectVal = $(this).val();
			var id = $(this).attr('id');
			id = id.replace('id_activite','id_activite_autre');
			var idVal = $('#'+id).val();
			if (selectVal == 'autre' && idVal == '') {
				$('#'+id).val('<?php $plxPlugin->lang('L_ADMIN_NOT_DONE') ?>');
			}
		});
		$('.autre').each(function() {
		    if ($(this).val() != '') {
		    	$(this).css({'display':'block'});
		    }
		});
		$('.activite_new').click(function() {
			var selectVal = $(this).val();
			var id = $(this).attr('id');
			id = id.replace('id_activite','id_activite_autre');
			var idVal = $('#'+id).val();
			if (selectVal == 'autre' && idVal == '') {
				$('#'+id).css({'display':'block'}).focus();

			}else if (selectVal == 'autre' && idVal != '') {
				$('#'+id).css({'display':'block'});

			} else {
				$('#'+id).css({'display':'none'});
			}
		});
		<?php endif; ?>
		$('.choices').change(function(event){
			var value = $(this).val();
			if (value == 'stop') {
				var reponse = confirm('<?php $plxPlugin->lang('L_FORM_CONFIRM') ?>');
				if (!reponse) {
					$(this).val('adhesion');
				} else {
					$(this).addClass('stop');
				}
			} else {
				$(this).removeClass('stop');
			}
		})
	})(jQuery);
</script>
<script type="text/javascript" src="<?php echo PLX_PLUGINS ?>adhesion/js/jquery-cookie/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo PLX_PLUGINS ?>adhesion/js/tab.js"></script>