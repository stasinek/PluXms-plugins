<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

# récuperation d'une instance de plxShow
$plxShow = plxShow::getInstance();
$plxShow->plxMotor->plxCapcha = new plxCapcha();
$plxPlugin = $plxShow->plxMotor->plxPlugins->getInstance('plxBonAchat');
include(dirname(__FILE__).'/lib/class.plx.country.php');

# Inistialisation des variables
$_GET['req'] = isset($_GET['req']) ? $_GET['req'] : NULL;
$price = isset($price) ? $price : NULL;
$title = isset($title) ? $title : NULL;
$name = isset($name) ? $name : NULL;
$firstname = isset($firstname) ? $firstname : NULL;
$address = isset($address) ? $address : NULL;
$zipcode = isset($zipcode) ? $zipcode : NULL;
$city = isset($city) ? $city : NULL;
$phone = isset($phone) ? $phone : NULL;
$email = isset($email) ? $email : NULL;
$country = isset($country) ? $country : NULL;
$recipient_title = isset($recipient_title) ? $recipient_title : NULL;
$recipient_name = isset($recipient_name) ? $recipient_name : NULL;
$recipient_firstname = isset($recipient_firstname) ? $recipient_firstname : NULL;
$recipient_phone = isset($recipient_phone) ? $recipient_phone : NULL;
$recipient_email = isset($recipient_email) ? $recipient_email : NULL;
$check_recipient_email = isset($check_recipient_email) ? $check_recipient_email : NULL;
$baseDir = plxUtils::getRacine();
$baFooter = substr($plxPlugin->getParam('baFooter'), 0, 4) == 'http' ? $plxPlugin->getParam('baFooter') : $baseDir.$plxPlugin->getParam('baFooter');
$baFirstSide = substr($plxPlugin->getParam('baFirstSide'), 0, 4) == 'http' ? $plxPlugin->getParam('baFirstSide') : $baseDir.$plxPlugin->getParam('baFirstSide');
$baReverseSize = substr($plxPlugin->getParam('baReverseSize'), 0, 4) == 'http' ? $plxPlugin->getParam('baReverseSize') : $baseDir.$plxPlugin->getParam('baReverseSize');

# Control du token du formulaire
if ($_GET['req']!='ok') {
	$token = $plxPlugin->bonachat->generer_token('form'); 
}

$error=false;
$success=false;
$confirmed=false;
$record = array();
$req=$_GET['req'];
$idcode=$plxPlugin->bonachat->idCode(12);
$country = strtoupper($plxShow->plxMotor->aConf['default_lang']);

$captcha = $plxPlugin->getParam('captcha')=='' ? '1' : $plxPlugin->getParam('captcha');

if ($req<>'ok' AND $req<>'ko') {
if(!empty($_POST)) {
	
    $_SESSION['idcode']=$plxPlugin->bonachat->idCode(12);
    $_SESSION['price']=$price=plxUtils::unSlash($_POST['price']);
	$_SESSION['title']=$title=plxUtils::unSlash($_POST['title']);
    $_SESSION['name']=$name=plxUtils::unSlash($_POST['name']);
	$_SESSION['firstname']=$firstname=plxUtils::unSlash($_POST['firstname']);	
	$_SESSION['address']=$address=plxUtils::unSlash($_POST['address']);
	$_SESSION['zipcode']=$zipcode=plxUtils::unSlash($_POST['zipcode']);
	$_SESSION['city']=$city=plxUtils::unSlash($_POST['city']);
	$_SESSION['phone']=$phone=plxUtils::unSlash($_POST['phone']);	
	$_SESSION['email']=$email=plxUtils::unSlash($_POST['email']);	
	$_SESSION['country']=$country=plxUtils::unSlash($_POST['country']);	
	
	$_SESSION['recipient_title']=$recipient_title=plxUtils::unSlash($_POST['recipient_title']);
    $_SESSION['recipient_name']=$recipient_name=plxUtils::unSlash($_POST['recipient_name']);
	$_SESSION['recipient_firstname']=$recipient_firstname=plxUtils::unSlash($_POST['recipient_firstname']);
	$_SESSION['recipient_phone']=$recipient_phone=plxUtils::unSlash($_POST['recipient_phone']);
	$_SESSION['recipient_email']=$recipient_email=plxUtils::unSlash($_POST['recipient_email']);
	$_SESSION['check_recipient_email']=$check_recipient_email=plxUtils::unSlash($_POST['check_recipient_email']);
	if ($check_recipient_email==1) { $check_rc_email=$plxPlugin->getLang('L_BA_YES'); } else { $check_rc_email=$plxPlugin->getLang('L_BA_NO');} 
	
	# pour compatibilité avec le plugin plxMyCapchaImage
	if(strlen($_SESSION['capcha'])<=10)
		$_SESSION['capcha']=sha1($_SESSION['capcha']);

	if(trim($name)=='')
		$error = $plxPlugin->getLang('L_BA_ERR_NAME');
	elseif(trim($firstname)=='')
		$error = $plxPlugin->getLang('L_BA_ERR_FIRSTNAME');
	elseif(!is_numeric($phone))
		$error = $plxPlugin->getLang('L_BA_ERR_PHONE');		
	elseif(!plxUtils::checkMail($email))
		$error = $plxPlugin->getLang('L_BA_ERR_EMAIL');
	elseif(trim($recipient_name)=='')
		$error = $plxPlugin->getLang('L_ERR_RECIPIENT_NAME');
	elseif(trim($recipient_firstname)=='')
		$error = $plxPlugin->getLang('L_ERR_RECIPIENT_FIRSTNAME');
	elseif(!is_numeric($recipient_phone))
		$error = $plxPlugin->getLang('L_ERR_RECIPIENT_PHONE');
	elseif(!plxUtils::checkMail($recipient_email))
		$error = $plxPlugin->getLang('L_BA_ERR_RECIPIENT_EMAIL');		
	elseif($captcha != 0 AND $_SESSION['capcha'] != sha1($_POST['rep']))
		$error = $plxPlugin->getLang('L_BA_ERR_ANTISPAM');
	if(!$error) {
		$confirmed=true;
		$url = $plxPlugin->getParam('baAccountType')==0 ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';	
		$baEmail = plxUtils::strCheck($plxPlugin->getParam('baEmail'));
		$baDevise = plxUtils::strCheck($plxPlugin->getParam('baDevise'));
		?>
		<form action="<?php echo $url ?>" method="post" target="_top">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="<?php echo $baEmail ?>">
		<input type="hidden" name="lc" value="ZA">
		<input type="hidden" name="item_name" value="Bon d'achat">
		<input type="hidden" name="amount" value="<?php echo $price ?>">
		<input type="hidden" name="currency_code" value="<?php echo $baDevise ?>">
		<input type="hidden" name="button_subtype" value="services">
		<input type="hidden" name="no_note" value="0">
		<input type="hidden" name="tax_rate" value="0.000">
		<input type="hidden" name="shipping" value="0.00">
		<input name="return" type="hidden" value="<?php echo $_SERVER['HTTP_REFERER'] ?>&req=ok&valid=<?php echo $token ?>" />
		<input name="cancel_return" type="hidden" value="<?php echo $_SERVER['HTTP_REFERER'] ?>&req=ko" />
		<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
		<table id="mon-ID" class="label12" width="100%" border="0" cellpadding="1" cellspacing="1">
		<tbody>
		<tr>
			<td class="col-md-3">
				<select name="price" id="price" disabled="">
					<option value="<?php echo $price ?>" selected=""><?php echo $price.' '.$plxPlugin->getParam('baDevise') ?></option>
				</select>
			</td>
		</tr>	
		<tr>
			<td height="10" colspan="2"><div class="bordure"></td>
		</tr>		
		<tr>
			<td class="text4" width="150"><strong><?php $plxPlugin->lang('L_BA_TITLE') ?></strong></td>
			<td class="text4"><?php echo $title ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_NAME') ?></strong></td>
			<td class="text4"><?php echo $name ?></td>
			</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_FIRSTNAME') ?></strong></td>
			<td class="text4"><?php echo $firstname ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_ADDRESS') ?></strong></td>
			<td class="text4"><?php echo $address ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_ZIPCODE') ?></strong></td>
			<td class="text4"><?php echo $zipcode ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_CITY') ?></strong></td>
			<td class="text4"><?php echo $city ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_COUNTRY') ?></strong></td>
			<td class="text4"><?php echo $country ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_PHONE') ?></strong></td>
			<td class="text4"><?php echo $phone ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_EMAIL') ?></strong></td>
			<td class="text4"><?php echo $email ?></td>
		</tr>
		</tbody>
		</table>
		<table id="mon-ID" class="label12" width="100%" border="0" cellpadding="1" cellspacing="1">
		<tbody>
		<tr>
			<td height="10" colspan="2"><div class="bordure"></div></td>
		</tr>
		<tr>
			<td class="text4" width="250"><strong><?php $plxPlugin->lang('L_BA_RECIPIENT_TITLE') ?></strong></td>
			<td class="text4"><?php echo $recipient_title ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_RECIPIENT_NAME') ?></strong></td>
			<td class="text4"><?php echo $recipient_name ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_RECIPIENT_FIRSTNAME') ?></strong></td>
			<td class="text4"><?php echo $recipient_firstname ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_RECIPIENT_PHONE') ?></strong></td>
			<td class="text4"><?php echo $recipient_phone ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_RECIPIENT_EMAIL') ?></strong></td>
			<td class="text4"><?php echo $recipient_email ?></td>
		</tr>
		<tr>
			<td class="text4"><strong><?php $plxPlugin->lang('L_BA_CHECK_RC_EMAIL2') ?></strong></td>
			<td class="text4"><?php echo $check_rc_email ?></td>
		</tr>		
		</tbody>
		</table>
		<br \>
		<input type="button" value="<?php $plxPlugin->lang('L_BA_FORM_BTN_CANCEL') ?>" onClick="document.location.href = document.referrer" />
		<input id="mon-ID" type="submit" name="submit" alt="PayPal, le réflexe sécurité pour payer en ligne" value="<?php $plxPlugin->lang('L_BA_FORM_BTN_CONFIRMED') ?>" />		
		</form>
		<?php
	}
} else {
	$name='';
	$mail='';
	$subject = '';
	$content='';
}

if (!$confirmed) {
	# Création de la liste des montant bon d'achat
	$PriceList = explode(',', $plxPlugin->getParam('baPriceList'));
	sort($PriceList);
	foreach ($PriceList as $key) {
		$SelectList[$key] = $key.' '.$plxPlugin->getParam('baDevise');
	}

?>

<div id="form_bonachat">
	<?php if($error): ?>
	<p class="contact_error"><?php echo $error ?></p>
	<?php endif; ?>
	<?php if($success): ?>
	<p class="contact_success"><?php echo plxUtils::strCheck($success) ?></p>
	
	<?php endif; ?>
	<p><?php echo $plxPlugin->getParam('baContent') ?></p>
	<form action="#form" method="post">
		<fieldset>
		<input type="hidden" name="idcode" value="<?php echo $idcode ?>">
		<input type="hidden" name="check_recipient_email" value="0">
		<p>
			<label for="name" class="label12"><?php $plxPlugin->lang('L_BA_CHOIX') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('price',$SelectList,$price,'','col-md-3'); ?>
		</p>
		<div class="bordure"></div>		
		<p class="label12"><?php $plxPlugin->lang('L_BA_CONTACT_INFORMATION') ?>&nbsp;:</p>
		<p>
			<label for="title"><?php $plxPlugin->lang('L_BA_TITLE') ?>&nbsp;*&nbsp;:</label>
			<?php plxUtils::printSelect('title',array('Mr'=>$plxPlugin->getLang('L_BA_MR'),'Mme'=>$plxPlugin->getLang('L_BA_MRS')),$title,'','col-md-3'); ?>
		</p>
		<p>
			<label for="name"><?php $plxPlugin->lang('L_BA_NAME') ?>&nbsp;*&nbsp;:</label>
			<input id="name" name="name" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_NAME') ?>" value="<?php echo plxUtils::strCheck($name) ?>" />
		</p>
		<p>
			<label for="firstname"><?php $plxPlugin->lang('L_BA_FIRSTNAME') ?>&nbsp;*&nbsp;:</label>
			<input id="firstname" name="firstname" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_FIRSTNAME') ?>" value="<?php echo plxUtils::strCheck($firstname) ?>" />
		</p>		
		<p>
			<label for="address"><?php $plxPlugin->lang('L_BA_ADDRESS') ?>&nbsp;:</label>
			<input id="address" name="address" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_ADDRESS') ?>" value="<?php echo plxUtils::strCheck($address) ?>" />
		</p>		
		<p>
			<label for="zipcode"><?php $plxPlugin->lang('L_BA_ZIPCODE') ?>&nbsp;:</label>
			<input id="zipcode" name="zipcode" type="text" size="30" class="form-control col-md-3" placeholder=" <?php $plxPlugin->lang('L_BA_PLACEHOLDER_ZIPCODE') ?>" value="<?php echo plxUtils::strCheck($zipcode) ?>" />
		</p>	
		<p>
			<label for="city"><?php $plxPlugin->lang('L_BA_CITY') ?>&nbsp;:</label>
			<input id="city" name="city" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_CITY') ?>" value="<?php echo plxUtils::strCheck($city) ?>" />
		</p>
		<p>
			<label for="phone"><?php $plxPlugin->lang('L_BA_PHONE') ?>&nbsp;*&nbsp;:</label>
			<input id="phone" name="phone" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_PHONE') ?>" value="<?php echo plxUtils::strCheck($phone) ?>" />
		</p>		
		<p>
			<label for="email"><?php $plxPlugin->lang('L_BA_EMAIL') ?>&nbsp;*&nbsp;:</label>
			<input id="email" name="email" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_EMAIL') ?>" value="<?php echo plxUtils::strCheck($email) ?>" />
		</p>
		<p>
			<label for="country"><?php $plxPlugin->lang('L_BA_COUNTRY') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('country',baCountry::country(),$country,'','col-md-3'); ?>
		</p>
		<p class="label10i"><?php  $plxPlugin->lang('L_BA_WARNING') ?></p>
		<div class="bordure"></div>
		<p class="label12"><?php  $plxPlugin->lang('L_BA_RECIPIENT_INFORMATION') ?>&nbsp;:</p>		
		<p>
			<label for="recipient_title">Civilité *&nbsp;:</label>
			<?php plxUtils::printSelect('recipient_title',array('Mr'=>$plxPlugin->getLang('L_BA_MR'),'Mme'=>$plxPlugin->getLang('L_BA_MRS')),$recipient_title,'','col-md-3'); ?>
		</p>
		<p>
			<label for="recipient_name"><?php  $plxPlugin->lang('L_BA_RECIPIENT_NAME') ?>&nbsp;*&nbsp;:</label>
			<input id="recipient_name" name="recipient_name" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_RC_NAME') ?>" value="<?php echo plxUtils::strCheck($recipient_name) ?>" />
		</p>
		<p>
			<label for="recipient_firstname"><?php  $plxPlugin->lang('L_BA_RECIPIENT_FIRSTNAME') ?>&nbsp;*&nbsp;:</label>
			<input id="recipient_firstname" name="recipient_firstname" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_RC_FIRSTNAME') ?>" value="<?php echo plxUtils::strCheck($recipient_firstname) ?>" />
		</p>		
		<p>
			<label for="recipient_phone"><?php  $plxPlugin->lang('L_BA_RECIPIENT_PHONE') ?>&nbsp;*&nbsp;:</label>
			<input id="recipient_phone" name="recipient_phone" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_RC_PHONE') ?>" value="<?php echo plxUtils::strCheck($recipient_phone) ?>" />
		</p>
		<p>
			<label for="recipient_email"><?php  $plxPlugin->lang('L_BA_RECIPIENT_EMAIL') ?>&nbsp;*&nbsp;:</label>
			<input id="recipient_email" name="recipient_email" type="text" size="30" class="form-control" placeholder=" <?php $plxPlugin->lang('l_BA_PLACEHOLDER_RC_EMAIL') ?>" value="<?php echo plxUtils::strCheck($recipient_email) ?>" />
		</p>
		<div class="check_recipient_email"><input type="checkbox" id="check_recipient_email" name="check_recipient_email" value="1" <?php echo (!isset($check_recipient_email)? '' : 'checked="checked"'); ?>/>&nbsp;<?php $plxPlugin->lang('l_BA_CHECK_RC_EMAIL') ?></div>	
		<?php if($captcha): ?>
		<p>
		<label for="id_rep"><strong><?php $plxPlugin->lang('L_BA_FORM_ANTISPAM') ?></strong></label>
		<?php $plxShow->capchaQ(); ?>
		<input id="id_rep" name="rep" type="text" size="2" maxlength="1" style="width: auto; display: inline;" autocomplete="off" />
		</p>
		<?php endif; ?>
		<p>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_BA_FORM_BTN_VALID') ?>" />
		</p>
		</fieldset>
	</form>

</div>
<?php
	} 
}
elseif($req == 'ok' AND isset($_GET['valid']) AND $_GET['valid'] == $_SESSION['form_token']) {
	
	$record['idcode']=$_SESSION['idcode'];
	$record['price']=$_SESSION['price'];
	$record['title']=$_SESSION['title'];
    $record['name']=$_SESSION['name'];
	$record['firstname']=$_SESSION['firstname'];	
	$record['address']=$_SESSION['address'];
	$record['zipcode']=$_SESSION['zipcode'];
	$record['city']=$_SESSION['city'];
	$record['phone']=$_SESSION['phone'];	
	$record['email']=$_SESSION['email'];	
	$record['country']=$_SESSION['country'];	
	
	$record['recipient_title']=$_SESSION['recipient_title'];
    $record['recipient_name']=$_SESSION['recipient_name'];
	$record['recipient_firstname']=$_SESSION['recipient_firstname'];
	$record['recipient_phone']=$_SESSION['recipient_phone'];
	$record['recipient_email']=$_SESSION['recipient_email'];	
	$record['check_recipient_email']=$_SESSION['check_recipient_email'];
	
	# Génération du nouveau nom du fichier
	$nbnote = $plxPlugin->bonachat->nextIdBA();
	$duration = $plxPlugin->getParam('baDuration');
	$time = time();
	$date =strftime("%Y%m%d%H%M",$time);
	$timestamp = strtotime(str_replace('/', '-', plxDate::formatDate($date)));
	$record['expiration_date'] = date('d/m/Y', strtotime('+'.$duration.' month', $timestamp ));
	$record['nbnote'] = $nbnote;
	$record['id'] = $date.'_'.$nbnote;

	$plxPlugin->bonachat->addBonAchat($record); # Ajout du nouveau bon d'achat
	
	if ($record['check_recipient_email'] == 1 OR $plxPlugin->getParam('baBuyer') == 1) { #création du pdf
		$duration = $plxPlugin->getParam('baDuration');
		$timestamp = strtotime(str_replace('/', '-', plxDate::formatDate($date))); 
		$expiration_date = date('d/m/Y', strtotime('+'.$duration.' month', $timestamp ));
	
		$datas = array( # variable pour les templates (date -> {{ date }})
			'date'=>plxDate::formatDate($date),
			'idcode'=>$record['idcode'],
			'duration'=>$plxPlugin->getParam('baDuration'),
			'expiration_date'=>$expiration_date,
			'title'=>$record['title'],
			'name'=>$record['name'],
			'firstname'=>$record['firstname'],
			'price'=>$record['price'].' '.$plxPlugin->getParam('baDevise'),
			'recipient_title'=>$record['recipient_title'],
			'recipient_name'=>$record['recipient_name'],
			'text_color'=>$plxPlugin->getParam('bacolorText'), # couleur du texte pour le bon
			'recipient_firstname'=>$record['recipient_firstname'],
			'backgroundimg_recto'=>$baFirstSide,
			'backgroundimg_verso'=>$baReverseSize
		);	
	
		$template = file_get_contents(dirname(__FILE__).'/tpl/'.$plxShow->plxMotor->aConf['default_lang'].'/bonachat.tpl');
		$content = $plxPlugin->bonachat->transform_vars_to_value($template, $datas);
		require_once(dirname(__FILE__).'/lib/pdf/html2pdf.class.php');
	
		// convert to PDF
		try {	
			$html2pdf = new HTML2PDF('L', array(140,140), 'fr');
			$html2pdf->pdf->SetDisplayMode('real');
			$html2pdf->writeHTML($content);
			$content_PDF = $html2pdf->Output('', true);	
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}		
	}
	# Mail de supervision
	if ($plxPlugin->getParam('baSupervision') == 1) {
		$name = $plxPlugin->getParam('baAdmin');
		$from = $plxPlugin->getParam('adminEmail');
		$to = $plxPlugin->getParam('adminEmail');
		$subject = $plxPlugin->getLang('L_BA_SUBJECT_ADMIN').' '.$record['idcode'];

		$datas = array(
			'idcode'=>$record['idcode'],
			'date'=>plxDate::formatDate($date),
			'title'=>$record['title'],
			'name'=>$record['name'],
			'firstname'=>$record['firstname'],
			'price'=>$record['price'].' '.$plxPlugin->getParam('baDevise'),
			'address'=>$record['address'],
			'zipcode'=>$record['zipcode'],
			'city'=>$record['city'],
			'phone'=>$record['phone'],
			'country'=>$record['country'],
			'email'=>$record['email'],
			'recipient_title'=>$record['recipient_title'],
			'recipient_name'=>$record['recipient_name'],
			'recipient_firstname'=>$record['recipient_firstname'],
			'recipient_phone'=>$record['recipient_phone'],
			'recipient_email'=>$record['recipient_email'],
			'site'=>$plxPlugin->getParam('baSite')
		);
		
		$template = file_get_contents(dirname(__FILE__).'/tpl/'.$plxShow->plxMotor->aConf['default_lang'].'/bodyadmin.tpl');
		$content = $plxPlugin->bonachat->transform_vars_to_value($template, $datas);
		
		if(plxUtils::sendMail($name,$from,$to,$subject,$content,'html')) {
			$success = $plxPlugin->getLang('L_BA_OK_SENDMAIL');
		} else {
			$error = $plxPlugin->getLang('L_BA_ERR_SENDMAIL');
		}	
	}
	# Mail pour le receveur de bon
	if ($record['check_recipient_email'] == 1) {
	
		$duration = $record['duration'];
		$timestamp = strtotime(str_replace('/', '-', plxDate::formatDate($date))); 
		$expiration_date = date('d/m/Y', strtotime('+'.$duration.' month', $timestamp ));
	
		$datas = array(
			'date'=>plxDate::formatDate($date),
			'duration'=>$plxPlugin->getParam('baDuration'),
			'expiration_date'=>$expiration_date,
			'title'=>$record['title'],
			'name'=>$record['name'],
			'firstname'=>$record['firstname'],
			'price'=>$record['price'].' '.$plxPlugin->getParam('baDevise'),
			'recipient_title'=>$record['recipient_title'],
			'recipient_name'=>$record['recipient_name'],
			'recipient_firstname'=>$record['recipient_firstname'],
			'footer'=>$baFooter
		);	
	
		$file = 'BA_'.$record['idcode'].'.pdf';
		$name = $plxPlugin->getParam('baSite');
		$from = $plxPlugin->getParam('adminEmail');
		$to = $record['recipient_email'];
		$subject = $plxPlugin->getLang('L_BA_SUBJECT_RECIPIENT').' '.$record['title'].' '.$record['name'];

		$template = file_get_contents(dirname(__FILE__).'/tpl/'.$plxShow->plxMotor->aConf['default_lang'].'/bodyrecipient.tpl');
		$content = $plxPlugin->bonachat->transform_vars_to_value($template, $datas);
	
		if($plxPlugin->bonachat->sendMailBA($name, $from, $to, $subject, $content, $file, $content_PDF)) {
			$success = $plxPlugin->getLang('L_BA_OK_SENDMAIL');
		} else {
			$error = $plxPlugin->getLang('L_BA_ERR_SENDMAIL');
		}	
	}		

	# Mail pour l'acheteur de bon d'achat
	if ($plxPlugin->getParam('baBuyer') == 1) {
	
		$datas = array(
			'date'=>plxDate::formatDate($date),
			'duration'=>$plxPlugin->getParam('baDuration'),
			'title'=>$record['title'],
			'name'=>$record['name'],
			'firstname'=>$record['firstname'],
			'price'=>$record['price'].' '.$plxPlugin->getParam('baDevise'),
			'recipient_title'=>$record['recipient_title'],
			'recipient_name'=>$record['recipient_name'],
			'recipient_firstname'=>$record['recipient_firstname'],
			'footer'=>$baFooter
		);	
	
		$file = 'BA_'.$record['idcode'].'.pdf';
		$name = $plxPlugin->getParam('baSite');
		$from = $plxPlugin->getParam('adminEmail');
		$to = $record['email'];
		$subject = $plxPlugin->getLang('L_BA_SUBJECT_BUYER').' '.$record['idcode'];

		$template = file_get_contents(dirname(__FILE__).'/tpl/'.$plxShow->plxMotor->aConf['default_lang'].'/bodybuyer.tpl');
		$content = $plxPlugin->bonachat->transform_vars_to_value($template, $datas);
	
		if($plxPlugin->bonachat->sendMailBA($name, $from, $to, $subject, $content, $file, $content_PDF)) {
			$success = $plxPlugin->getLang('L_BA_OK_SENDMAIL');
		} else {
			$error = $plxPlugin->getLang('L_BA_ERR_SENDMAIL');
		}	
	}
	
	echo $plxPlugin->getParam('baContentValid')."\n";
	unset($_SESSION['form_token']);


	} elseif($req=='ko') {
		echo $plxPlugin->getParam('baContentCancel')."\n";
	}

?>