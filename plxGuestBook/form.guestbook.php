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
$plxPlugin = $plxShow->plxMotor->plxPlugins->getInstance('plxGuestBook');

$plxPlugin->guestbook->getPagegbFront();

$bypage = intval($plxPlugin->getParam('byPage'));
$start = $bypage*($plxPlugin->guestbook->page-1);
$guestb = $plxPlugin->guestbook->getGuestBook($plxPlugin->getParam('tri_gb'),'online',$start,$bypage);
$nbGBPagination=$plxPlugin->guestbook->nbGuestBook('online');

$direct = $plxPlugin->getParam('mod'); # 1 - modération : 0 - pas de modération
$error = false;
$success = false;
$comment = array();
$captcha = $plxPlugin->getParam('captcha')=='' ? '1' : $plxPlugin->getParam('captcha');

# Gestion des erreurs
if(!empty($_POST) AND (!empty($_POST['submit']) AND $_POST['btn_ok2']=='add')) {
	# pour compatibilité avec le plugin plxMyCapchaImage
	if(strlen($_SESSION['capcha'])<=10)
		$_SESSION['capcha']=sha1($_SESSION['capcha']);
	$author = plxUtils::unSlash($_POST['author']);
	$mail = plxUtils::unSlash($_POST['mail']);
	$site = plxUtils::unSlash($_POST['site']);
	$content=plxUtils::unSlash($_POST['content']);
	
	if (isset($_POST['actmail'])) { $actmail = 'on'; } else { $actmail = 'off'; }
	if(trim($author)=='')
		$error = $plxPlugin->getLang('L_GB_ERR_AUTHOR');
	elseif(!empty($site) AND !plxUtils::checkSite($site))
		$error = $plxPlugin->getLang('L_GB_ERR_SITE');	
	elseif(!plxUtils::checkMail($mail))
		$error = $plxPlugin->getLang('L_GB_ERR_EMAIL');
	elseif(trim($content)=='')
			$error = $plxPlugin->getLang('L_GB_ERR_CONTENT');
	elseif($captcha != 0 AND $_SESSION['capcha'] != sha1($_POST['rep']))
		$error = $plxPlugin->getLang('L_GB_ERR_ANTISPAM');
	if(!$error) {
		$comment['author'] = $author;
		$comment['ip'] = plxUtils::getIp();
		$comment['mail'] = $mail;
		$comment['actmail'] = $actmail;
		$comment['site'] = $site;
		$comment['content'] = $content;
		# Génération du nouveau nom du fichier
		$nbnote = $plxPlugin->guestbook->nextIdGB();
		$time = time();
		$date =strftime("%Y%m%d%H%M",$time);
		$comment['nbnote'] = $nbnote;
		if ($direct == 1) { $first = '_'; } else { $first = ''; }
		$comment['id'] = $first.$date.'_'.$nbnote;
		
		$dte = plxDate::date2Array($date);
		
		# préparation du contenu du mail
		$corps  = $plxPlugin->getLang('L_GB_FORM_CORPS_1')." ".$dte['day']."-".$dte['month']."-".$dte['year']." ".$dte['time']."<br />"."\t\n";
		$corps .= $plxPlugin->getLang('L_GB_FORM_CORPS_2')." : ".$author." (".$mail.")<br />"."\t\n";
		if ($site != "" OR $site != "http://") {
		$corps .= $plxPlugin->getLang('L_GB_FORM_CORPS_3')." : ".$site."<br />"."\t\n";
		}
		$corps .= "---------------------------<br />"."\t\n";		
		$corps .= $plxPlugin->getLang('L_GB_FORM_CORPS_4')."<br />"."\t\n";
		$corps .= $content."<br />"."\t\n";
		$corps .= "---------------------------<br />"."\t\n";
		if ($direct == 1) {
		$corps .= $plxPlugin->getLang('L_GB_FORM_CORPS_5')." :<br />"."\t\n";
		$corps .= "<a href='".plxUtils::getRacine()."core/admin/plugin.php?p=plxGuestBook&sel=offline&page=1' title='Rubrique 1'>".$plxPlugin->getLang('L_GB_FORM_CORPS_6')."</a><br />"."\t\n";
		} else {
		$corps .= $plxPlugin->getLang('L_GB_FORM_CORPS_7')." :<br />"."\t\n";
		$corps .= "<a href='".plxUtils::getRacine()."core/admin/plugin.php?p=plxGuestBook' title='Rubrique 1'>".$plxPlugin->getLang('L_GB_FORM_CORPS_6')."</a><br />"."\t\n";		
		}
		$corps .= " "."\t\n";
		
		if ($plxPlugin->guestbook->addGuestBook($comment)) # Ajout du nouveau message
		if ($plxPlugin->getParam('supervision') == 1) {
		if(plxUtils::sendMail($plxPlugin->getLang('L_GB_WRITTEN_BY').'  : '.$author,$mail,$plxPlugin->getParam('email'),$plxPlugin->getParam('subject'),$corps,'html')) {
			$success = $plxPlugin->getParam('thankyou');
		} else {
			$error = $plxPlugin->getLang('L_GB_ERR_SENDMAIL');
		}	
		} else {
			$success = $plxPlugin->getParam('thankyou');
		}
	} else {
		$_POST['btn_ok3'] = 'new';
	}
} else {
	$author = '';
	$mail = '';
	$actmail = 'off';
	$content = '';
	$site = '';
}
if($error): ?>
	<p class="contact_error"><?php echo $error ?></p>
<?php endif;

if (!empty($_POST['submit']) AND $_POST['btn_ok3']=='new') {
?>
<div id="form_contact">
	<?php if($plxPlugin->getParam('mnuText')): ?>
	<div class="text_contact">
	<?php echo $plxPlugin->getParam('mnuText') ?>
	</div>
	<?php endif; ?>
	<p><?php $plxPlugin->lang('L_GB_MSG_WELCOME') ?></p>
	<form action="#form" method="post">
		<input id="id_add" name="btn_ok2" type="hidden" value="add" />
		<input id="id_add" name="btn_ok3" type="hidden" value="" />
		<p>		
			<label for="author"><?php $plxPlugin->lang('L_GB_FORM_AUTHOR') ?>&nbsp;:</label>
			<input id="author" name="author" type="text" size="30" value="<?php echo plxUtils::strCheck($author) ?>" maxlength="30" />
		</p>
		<p>
			<label for="site"><?php $plxPlugin->lang('L_GB_FORM_WEBSITE') ?>&nbsp;:</label>
			<input id="site" name="site" type="text" size="30" placeholder=" <?php $plxPlugin->lang('L_GB_PLACEHOLDER_SITE') ?>" value="<?php echo plxUtils::strCheck($site) ?>" />
		</p>
		<p>
			<label for="mail"><?php $plxPlugin->lang('L_GB_FORM_MAIL') ?>&nbsp;:</label>
			<input id="mail" name="mail" type="text" size="30" value="<?php echo plxUtils::strCheck($mail) ?>" /><br/>	
            <?php $plxPlugin->lang('L_GB_FORM_VIEW_MAIL') ?>&nbsp;:&nbsp;&nbsp;<input type="checkbox" name="actmail" value="off" <?php if ($actmail=='on') echo ' checked="checked"'; ?> /><br/>
			<?php $plxPlugin->lang('L_GB_FORM_ANTISPAM_INFO') ?>	
		</p>
		<p>
			<label for="message"><?php $plxPlugin->lang('L_GB_FORM_CONTENT') ?>&nbsp;:</label>
			<textarea id="message" name="content" cols="60" rows="12"><?php echo plxUtils::strCheck($content) ?></textarea>
		</p>	
<?php if($captcha): ?>
		<p>
		<label for="id_rep"><strong><?php $plxPlugin->lang('L_GB_FORM_ANTISPAM') ?></strong></label>
		<?php $plxShow->capchaQ(); ?>
		<input id="id_rep" name="rep" type="text" size="2" maxlength="1" style="width: auto; display: inline;" autocomplete="off" />
		</p>
		<?php endif; ?>
		<p>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_GB_FORM_BTN_SEND') ?>" />
		</p>
	</form>
</div>
<?php } else { ?>
<?php if($success): ?>
		<p class="contact_success"><?php echo plxUtils::strCheck($success) ?>
	<?php if($direct == 1): ?>
		&nbsp;<?php $plxPlugin->lang('L_GB_FORM_MOD') ?>.
		<?php endif; ?>
	</p>
	<?php $plxPlugin->guestbook->RedirGB('index.php?guestbook'); ?>
	<?php endif; ?>	
	
<?php if($plxPlugin->getParam('Text')): ?>
	<div class="text_contact">
	<?php echo $plxPlugin->getParam('Text') ?>
	</div>
	<?php else: ?>
    <p>&nbsp;</p>
	<?php endif; ?>
	
<form class="newgb" action="#form" method="post">
	<input id="id_add" name="btn_ok2" type="hidden" value="" />
	<input id="id_new" name="btn_ok3" type="hidden" value="new" />
    <input type="submit" name="submit" value="<?php $plxPlugin->lang('L_GB_FORM_BTN_SENDTO') ?>" />
</form>
<p>&nbsp;</p>
<?php if($plxPlugin->guestbook->plxRecord_gb): ?>
	<div id="guestbook">
		<?php while($plxPlugin->guestbook->plxRecord_gb->loop()): # On boucle sur les messages ?>
	<hr>	
	<table>
		<tbody><tr>
			<td style="width:12%" class="note"><?php $plxPlugin->lang('L_GB_FORM_MESSAGE') ?>&nbsp;<?php echo intval($plxPlugin->guestbook->plxRecord_gb->f('nbnote')) ?></td>
			<td class="guest"><?php $plxPlugin->lang('L_GB_FORM_BY') ?> 
			&nbsp;<?php echo $plxPlugin->guestbook->gbAuthor(($plxPlugin->guestbook->plxRecord_gb->f('actmail')=='on'?'mailto':'')); ?></strong>&nbsp;
			<?php $plxPlugin->lang('L_GB_FORM_THE') ?>&nbsp;<?php $plxPlugin->guestbook->gbDate('#num_day-#num_month-#num_year(4) #hour:#minute'); ?><br />
			<?php if ($plxPlugin->guestbook->plxRecord_gb->f('site') != '') { ?>
			<img src="<?php echo PLX_PLUGINS ?>plxGuestBook/img/gbkurl.png" style="width:17px;height:17px;vertical-align:middle;" alt="">&nbsp;&nbsp;<?php echo $plxPlugin->guestbook->gbSite('link'); ?>
			<?php } else { ?>
			<span>&nbsp;</span>
			<?php } ?></td>
		</tr>
		<tr>
			<td colspan="2" class="rep"><?php $plxPlugin->guestbook->gbContent(); ?></td>
		</tr>
		</tbody>
	</table>
		<?php endwhile; # Fin de la boucle sur les messages ?>
	</div>
	<?php else: ?>
		<p>
			<?php $plxPlugin->lang('L_GB_FORM_NO_POST') ?>.
		</p>
	<?php endif; ?>	
<?php if ($nbGBPagination > $bypage) { 	?>
<span>&nbsp;</span>
<nav class="pagination text-center">
<?php
# Affichage de la pagination
	if($guestb) { # Si on a des messages (hors page)
		$plxPlugin->guestbook->Pagination($nbGBPagination,$bypage);
}
?>
</nac>	
<?php } ?>
<?php } ?>