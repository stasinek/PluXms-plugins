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
//Si l'utilisateur n'est pas connecté, on le redirige vers l'accueil
if ( ( !isset($_SESSION['lockArticles']['articles']) && !isset($_SESSION['lockArticles']['categorie']) && !isset($_SESSION['account']) ) || ($_SESSION['lockArticles']['articles'] != 'on' && $_SESSION['lockArticles']['categorie'] != 'on' && $_SESSION['account'] != '') ) {
	header('Location:'.$plxMotor->urlRewrite());
	exit();
}

//print_r($_SESSION);

# récuperation d'une instance de plxShow
$plxShow = plxShow::getInstance();
$plxShow->plxMotor->plxCapcha = new plxCapcha();
$plxPlugin = $plxShow->plxMotor->plxPlugins->getInstance('adhesion');

$plxPlugin->getAdherents('/^[0-9]{5}.(.[a-z-]+){2}.[0-9]{10}.xml$/');


if(!isset($_GET['a'])) {
	header('Location:'.$plxMotor->urlRewrite());
	exit();
}
$verif = substr($_GET['a'],5,-3);
$compte = array(NULL);
foreach ($plxPlugin->plxRecord_adherents->result as $key => $account) {
	if (md5($account['mail']) == $verif) {
		$compte = $plxPlugin->plxRecord_adherents->result[$key];
		$compte['id'] = $key;
		break;
	}
}

if($compte == array(NULL)) {
	header('Location:'.$plxMotor->urlRewrite());
	exit();
}
//Définition de variables
$error = array();
$success=false;
$wall_e = '';

if(!empty($_POST) && !empty($_POST['wall-e'])) {
	$wall_e = $_POST['wall-e'];
}
if(!empty($_POST) && empty($wall_e)) {

	$compte['nom']=strtoupper(trim(plxUtils::strCheck($_POST['nom'])));
	$compte['prenom']=strtolower(trim(plxUtils::strCheck($_POST['prenom'])));


	if ($plxPlugin->getParam('typeAnnuaire') == 'professionnel') {
		$compte['activite'] = plxUtils::strCheck($_POST['activite']);
		$compte['autre_activite']=trim(plxUtils::strCheck($_POST['autre_activite']));
		$compte['etablissement']=trim(plxUtils::strCheck($_POST['etablissement']));
		$compte['service']=trim(plxUtils::strCheck($_POST['service']));
		$compte['tel_office']=str_replace(array('.','-',' ','_','+','(',')',',',':',';','/'),'',plxUtils::strCheck($_POST['tel_office']));
		if($compte['activite'] =='')
			$error[] = $plxPlugin->getLang('L_ERR_ACTIVITE');
		if($compte['activite'] =='autre' && trim($compte['autre_activite']) == '')
			$error[] = $plxPlugin->getLang('L_ERR_AUTRE_ACTIVITE');
		if(trim($compte['etablissement']) == '')
			$error[] = $plxPlugin->getLang('L_ERR_ETABLISSEMENT');
		if(trim($compte['service']) == '')
			$error[] = $plxPlugin->getLang('L_ERR_SERVICE');
		if(trim($compte['tel_office']) != '' && !preg_match('!^[0-9]{3,13}[0-9]?$!',$compte['tel_office']))
			$error[] = $plxPlugin->getLang('L_ERR_TEL');
		$pro = array(
			'activite'=>$compte['activite'],
			'autre_activite'=>$compte['autre_activite'],
			'etablissement'=>$compte['etablissement'],
			'service'=>$compte['service'],
			'tel_office'=>$compte['tel_office'],
			'coordonnees'=>''
		);
		if($plxPlugin->getParam('showAnnuaire') == 'on') {
			$compte['coordonnees']=plxUtils::strCheck($_POST['coordonnees']);
			if(trim($compte['coordonnees']) == '')
				$error[] = $plxPlugin->getLang('L_ERR_COORDONNEES');
			else
				$pro['coordonnees'] = $compte['coordonnees'];
		}
	
	}

	$compte['adresse1']=trim(plxUtils::strCheck($_POST['adresse1']));
	$compte['adresse2']=trim(plxUtils::strCheck($_POST['adresse2']));
	$compte['cp']=intval($_POST['cp']);
	$compte['ville']=trim(plxUtils::strCheck($_POST['ville']));
	$compte['tel']=str_replace(array('.','-',' ','_','+','(',')',',',':',';','/'),'',$_POST['tel']);
	$compte['mail']=trim(str_replace('&#64;', '@', plxUtils::strCheck($_POST['mail'])));
	$compte['choix']=empty($_POST['choix']) ? 'adhesion': plxUtils::strCheck($_POST['choix']);
	$compte['mailing']=plxUtils::strCheck($_POST['mailing']);
	if(trim($compte['nom'])=='')
		$error[] = $plxPlugin->getLang('L_ERR_NAME');
	if(trim($compte['prenom'])=='')
		$error[] = $plxPlugin->getLang('L_ERR_FIRST_NAME');
	if(trim($compte['adresse1']) == '')
		$error[] = $plxPlugin->getLang('L_ERR_ADRESSE');
	if(trim($compte['cp']) == '' || strlen($compte['cp']) != 5 || !is_int($compte['cp']))
		$error[] = $plxPlugin->getLang('L_ERR_CP');
	if(trim($compte['ville']) == '')
		$error[] = $plxPlugin->getLang('L_ERR_VILLE');
	if(trim($compte['tel']) == '' || !preg_match('!^[0-9]{9,13}[0-9]$!',$compte['tel']))
		$error[] = $plxPlugin->getLang('L_ERR_TEL');
	if(trim($compte['mailing']) == '')
		$error[] = $plxPlugin->getLang('L_ERR_MAILING');
	if(!plxUtils::checkMail($compte['mail']))
		$error[] = $plxPlugin->getLang('L_ERR_MAIL');
	if($plxShow->plxMotor->aConf['capcha'] AND $_POST['rep2'] != sha1($_POST['rep']))
		$error[] = $plxPlugin->getLang('L_ERR_ANTISPAM');
	if(empty($error) ) {
		# On édite le compte de l'adhérent
		if ($plxPlugin->editMyAccount($compte,$compte['id'])) {
			//Si l'utilisateur ne souhaite plus être membre de l'asso, on envoie une notification à un admin
			if($choix == 'stop') {
				$content = $plxPlugin->notification($compte['nom'],$compte['prenom'],$compte['adresse1'],$compte['adresse2'],$compte['cp'],$compte['ville'],$compte['tel'],$compte['mail'],$compte['choix'],$compte['mailing']);
				if($plxPlugin->sendEmail($plxPlugin->getParam('nom_asso'),$plxPlugin->getParam('email'),$plxPlugin->getParam('email'),$plxPlugin->getParam('devalidation_subject'),$content,'html')){
						$_SESSION['erase'] = '<p id="password_success">'.$plxPlugin->getLang('L_EDIT_OK').'<br/>'.$plxPlugin->getLang('L_FORM_ERASE_OK').'</p>';
						unset($_SESSION['account']);
						unset($_SESSION['isConnected']);
						unset($_SESSION['lockArticles']);
						header('Location:'.$plxMotor->urlRewrite());
						exit();
				} 
			}
			$success = $plxPlugin->getLang('L_EDIT_OK');
		} else {
			$error = array($plxPlugin->getLang('L_INTERNAL_ERR'));
		}
	}
}?>
<div id="form_adherer">
	<?php  $_POST = '';
if(!empty($success)): ?>

		<p class="success"><?php echo $success; ?></p>
<?php
endif;
	if(!empty($error)): ?>

		<div class="contact_error">
		 <?php if(!empty($error)): ?>

				<h3><?php $plxPlugin->lang('L_FORM_EMPTY'); ?></h3>
				<ul>
					<?php foreach ($error as $e) {

					echo '<li>'.$e.'</li>
						';
				} ?>

				</ul>
		<?php endif; ?>

		</div>
	<?php endif;
	unset($_POST);?>

	<p id="all_required"><?php $plxPlugin->lang('L_FORM_ALL_REQUIRED');?></p>
	<form action="#form" method="post">
		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_IDENTITY');?></h2></legend>
		<p>	
			<label for="name"><?php $plxPlugin->lang('L_FORM_NAME') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="name" name="nom" type="text" size="30" value="<?php echo plxUtils::strCheck($compte['nom']) ?>" maxlength="30" />
		</p>
		<p>
			<label for="firstname"><?php $plxPlugin->lang('L_FORM_FIRST_NAME') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="firstname" name="prenom" type="text" size="30" value="<?php echo plxUtils::strCheck($compte['prenom']) ?>" maxlength="30" />
		</p>
		</fieldset>
		<?php if($plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_ACTIVITY');?></h2></legend>
		<p>
			<input id="arc" name="activite" type="radio" value="arc" <?php echo plxUtils::strCheck($compte['activite']) == 'arc'? 'checked="checked"' : ''; ?> />
			<label for="arc">ARC</label>
		</p>
		<p>
			<input id="tec" name="activite" type="radio" value="tec" <?php echo plxUtils::strCheck($compte['activite']) == 'tec'? 'checked="checked"' : ''; ?> />
			<label for="tec">TEC</label>
		</p>
		<p>
			<input id="irc" name="activite" type="radio" value="irc" <?php echo plxUtils::strCheck($compte['activite']) == 'irc'? 'checked="checked"' : ''; ?> />
			<label for="irc">IRC</label>
		</p>
		<p>
			<input id="autre" name="activite" type="radio" value="autre" <?php echo plxUtils::strCheck($compte['activite']) == 'autre' ? 'checked="checked"' : ''; ?> />
			<label for="autre"><?php $plxPlugin->lang('L_FORM_OTHER') ?></label>
		</p>
		<p class="mask">
			<label for="autre_activite"><?php echo $plxPlugin->lang('L_FORM_DETAIL');?>&nbsp;:</label>
		</p>
		<p class="mask">
			<input id="autre_activite" name="autre_activite" type="text" value="<?php echo plxUtils::strCheck($compte['autre_activite']);?>" />
		</p>
		</fieldset>
		<?php endif; ?>

		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_AGENDA');?></h2></legend>
		<?php if($plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

		<p>	
			<label for="etablissement"><?php $plxPlugin->lang('L_FORM_SOCIETY') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="etablissement" name="etablissement" type="text" size="50" value="<?php echo plxUtils::strCheck($compte['etablissement']) ?>" maxlength="50" />
		</p>
		<p>	
			<label for="service"><?php $plxPlugin->lang('L_FORM_SERVICE') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="service" name="service" type="text" size="50" value="<?php echo plxUtils::strCheck($compte['service']) ?>" maxlength="50" />
		</p>
		<?php endif; ?>

		<p>	
			<label for="adresse1"><?php $plxPlugin->lang('L_FORM_ADDRESS') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="adresse1" name="adresse1" type="text" size="50" value="<?php echo plxUtils::strCheck($compte['adresse1']) ?>" maxlength="50" />
		</p>
		<p>
			<input id="adresse2" name="adresse2" type="text" size="50" value="<?php echo plxUtils::strCheck($compte['adresse2']) ?>" maxlength="50" />
		</p>
		<p>	
			<label for="cp"><?php $plxPlugin->lang('L_FORM_ZIP_CODE') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="cp" name="cp" type="text" size="5" value="<?php echo plxUtils::strCheck($compte['cp']) ?>" maxlength="5" />
		</p>
		<p>	
			<label for="ville"><?php $plxPlugin->lang('L_FORM_CITY') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="ville" name="ville" type="text" size="50" value="<?php echo plxUtils::strCheck($compte['ville']) ?>" maxlength="50" />
		</p>
		<p>	
			<label for="tel"><?php $plxPlugin->lang('L_FORM_TEL') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="tel" name="tel" type="text" size="50" value="<?php echo plxUtils::strCheck($compte['tel']) ?>" maxlength="50" />
		</p>
		<?php if($plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

		<p>	
			<label for="tel_office"><?php $plxPlugin->lang('L_FORM_TEL_OFFICE') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="tel_office" name="tel_office" type="text" size="50" value="<?php echo plxUtils::strCheck($compte['tel_office']) ?>" maxlength="50" />
		</p>
		<?php endif; ?>

		<p>
			<label for="mail"><?php $plxPlugin->lang('L_FORM_MAIL') ?>&nbsp;:</label>
		</p>
		<p>
			<input id="mail" name="mail" type="text" size="30" value="<?php echo ($compte['mail'] != '')? str_replace('@','&#64;',$compte['mail']):''; ?>" />
		</p>
		</fieldset>
		<fieldset><legend><h2><?php $plxPlugin->lang('L_ADMIN_LIST_DEVALIDATION');?></h2></legend>
		<p>
			<input id="stop" name="choix" type="checkbox" value="stop" <?php echo plxUtils::strCheck($compte['choix']) == 'stop'? 'checked="checked"' : ''; ?> />
			<label for="stop">Je ne souhaite plus être membre de l’Association <?php echo $plxPlugin->getParam('nom_asso'); ?></label>
		</p>
		<?php if($plxPlugin->getParam('showAnnuaire') == 'on' && $plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_SHARING');?></h2></legend>
		<p>
			<input id="rec" name="coordonnees" type="radio" value="rec" <?php echo plxUtils::strCheck($compte['coordonnees']) == 'rec' ? 'checked="checked"' : ''; ?> />
			<label for="rec">J’accepte que mes coordonnées professionnelles figurent sur le site de l’Association <?php echo $plxPlugin->getParam('nom_asso'); ?></label>
		</p>
		<p>
			<input id="refus" name="coordonnees" type="radio" value="refus" <?php echo plxUtils::strCheck($compte['coordonnees']) == 'refus' ? 'checked="checked"' : ''; ?> />
			<label for="refus">Je refuse que mes coordonnées professionnelles figurent sur le site de l’Association <?php echo $plxPlugin->getParam('nom_asso'); ?></label>
		</p>
		</fieldset>
		<?php endif; ?>

		<fieldset><legend><h2><?php $plxPlugin->lang('L_FORM_MAILING');?></h2></legend>
		<p>
			<input id="maillist" name="mailing" type="radio" value="maillist" <?php echo plxUtils::strCheck($compte['mailing']) == 'maillist' ? 'checked="checked"' : ''; ?> />
			<label for="maillist">J’accepte de recevoir par mail toute information concernant le site de <?php echo $plxPlugin->getParam('nom_asso'); ?></label>
		</p>
		<p>
			<input id="blacklist" name="mailing" type="radio" value="blacklist" <?php echo plxUtils::strCheck($compte['mailing']) == 'blacklist' ? 'checked="checked"' : ''; ?> />
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
	<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(function($){
			$('#stop').click(function() {
				if ($('#stop').is(':checked') ){
					if (!confirm('<?php echo str_replace("'","\'",$plxPlugin->getLang('L_FORM_CONFIRM')); ?>')) {
						$('#stop').attr('checked',false);
					}
				}
			})

	<?php if($plxPlugin->getParam('typeAnnuaire') == 'professionnel') : ?>

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
	<?php endif; ?>

		});
		/* ]]> */
	</script>
</div>