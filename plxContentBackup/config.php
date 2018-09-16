<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
require_once('class/class.zip.php');
require_once('class/class.phpmailer-lite.php');
require_once('class/class.archive.php');


// s'il s'agit de la première utilisation
// définitions des valeurs par défaut

if(!$plxPlugin->getParam('savedir')) { $plxPlugin->setParam('savedir', '../../sauvegarde/', 'string'); }

if(!$plxPlugin->getParam('days')) { $plxPlugin->setParam('days', '7', 'numeric'); }


$archive = new archive($plxPlugin->getParam('savedir'),$plxPlugin->getParam('days'),$plxPlugin->getParam('saved_dirs'));
$archive->check();
if(!empty($_POST)) {

	$plxPlugin->setParam('attach', 0, 'numeric');
	$plxPlugin->setParam('attach', $_POST['attach'][0], 'numeric');

	$plxPlugin->setParam('savedir', $_POST['savedir'], 'string');
	$plxPlugin->setParam('days', $_POST['days'], 'numeric');
	$plxPlugin->setParam('email', $_POST['email'], 'string');
	$plxPlugin->setParam('senderemail', $_POST['senderemail'], 'string');
	$plxPlugin->setParam('sendername', $_POST['sendername'], 'string');
	// on ajoute le répertoire 'data/' aux dossiers choisis
	//while(list ($key, $val) = each ($_POST['data'])) { $_POST['data'][$key] = 'data/'.$val; }
	$dirs = implode(',',$_POST['data']);
	$plxPlugin->setParam('saved_dirs', $dirs, 'string');
	// écriture des paramètres
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxContentBackup');
	exit;
}
if($_GET['action'] == 'zip') {
		$archive->zip();
		plxMsg::Info('L\'archive a été crée avec succès');
}
if($_GET['action'] == 'mail') {
	if($archive->sendmail($plxPlugin->getParam('email'),$plxPlugin->getParam('senderemail'),$plxPlugin->getParam('sendername'),$plxPlugin->getLang('L_TITLE'),$plxPlugin->getLang('L_CONTENT'),$plxPlugin->getParam('attach'))) { 
		plxMsg::Info('L\'email a été envoyé avec succès'); 
	}
}

if(isset($_GET['f']) && file_exists($plxPlugin->getParam('savedir').plxEncrypt::decryptId($_GET['f'])))
{
	ob_clean();
	//ob_end_flush();
	ob_start();
	header('Expires: 0');
	header('Cache-Control: private');
	header('Pragma: cache');
	header('Content-Disposition: attachment; filename="'.plxEncrypt::decryptId($_GET['f']).'"');
	header('Content-type: application/zip');
	header('Content-Length: '.filesize($plxPlugin->getParam('savedir').plxEncrypt::decryptId($_GET['f'])));
	ob_end_clean();
	readfile($plxPlugin->getParam('savedir').plxEncrypt::decryptId($_GET['f']));
	exit;
}

if(isset($_GET['d']) && file_exists($plxPlugin->getParam('savedir').plxEncrypt::decryptId($_GET['d'])))
{
	unlink($plxPlugin->getParam('savedir').plxEncrypt::decryptId($_GET['d']));
	plxMsg::Info('Le fichier '.plxEncrypt::decryptId($_GET['d']).' a bien été supprimé'); 
}
?>
<div id="plxContentBackupmail">
	<h2><?php $plxPlugin->lang('L_TITLE_CONFIG') ?></h2>
	<p><?php $plxPlugin->lang('L_DESCRIPTION_CONFIG') ?></p>
	<h3><?php $plxPlugin->lang('L_ACTION_CONFIG') ?></h3>
	<div class="options">
		<a class="archive" href="parametres_plugin.php?p=plxContentBackup&action=zip" title="<?php $plxPlugin->lang('L_ARCHIVE_CONFIG') ?>"><?php $plxPlugin->lang('L_ARCHIVE_CONFIG') ?></a>
		<a class="email" href="parametres_plugin.php?p=plxContentBackup&action=mail"title="<?php $plxPlugin->lang('L_MAIL_CONFIG') ?>"><?php $plxPlugin->lang('L_MAIL_CONFIG') ?></a>
		<div class="clear"></div>
	</div>
	<h3><?php $plxPlugin->lang('L_ARCHIVELIST_CONFIG') ?></h3>
	<div class="liste">
		<?php $archive->displaylist() ?>
	</div>
	<div class="clear"></div>
	<h3><?php $plxPlugin->lang('L_OPTIONS_CONFIG') ?></h3>
	<form action="parametres_plugin.php?p=plxContentBackup" method="post">
		<fieldset>
					<p><input type="checkbox" name="attach[]" value="1" <?php if($plxPlugin->getParam('attach') == 1)  { echo ' checked'; } ?> /><?php $plxPlugin->lang('L_ATTACH_CONFIG') ?></p>
					<label><?php $plxPlugin->lang('L_SAVEDIR_CONFIG') ?></label> <input type="text" name="savedir" value="<?php echo plxUtils::strCheck($plxPlugin->getParam('savedir')) ?>" /><br />
					<label><?php $plxPlugin->lang('L_DAY_CONFIG') ?></label> <input type="text" name="days" value="<?php echo plxUtils::strCheck($plxPlugin->getParam('days')) ?>" /><br />
					<label><?php $plxPlugin->lang('L_EMAIL_CONFIG') ?></label> <input type="text" name="email" value="<?php echo plxUtils::strCheck($plxPlugin->getParam('email')) ?>" /><br />
					<label><?php $plxPlugin->lang('L_EMAILSENDER_CONFIG') ?></label> <input type="text" name="senderemail" value="<?php echo plxUtils::strCheck($plxPlugin->getParam('senderemail')) ?>" /><br />
					<label><?php $plxPlugin->lang('L_SENDERNAME_CONFIG') ?></label> <input type="text" name="sendername" value="<?php echo plxUtils::strCheck($plxPlugin->getParam('sendername')) ?>" /><br />
					<label><?php $plxPlugin->lang('L_SAVED_DIRS') ?></label>
						<?php 
						$data = array(
							$plxAdmin->aConf['medias'],
							$plxAdmin->aConf['racine_articles'],
							$plxAdmin->aConf['racine_commentaires'],
							$plxAdmin->aConf['racine_statiques'],
							PLX_CONFIG_PATH.'parametres.xml',
							PLX_CONFIG_PATH.'statiques.xml',
							PLX_CONFIG_PATH.'users.xml',
							PLX_CONFIG_PATH.'plugins.xml',
							PLX_CONFIG_PATH.'tags.xml',
							$plxAdmin->aConf['racine_plugins'],
							$plxAdmin->aConf['racine_themes'],
						);
						
						foreach($data as $d) {
							echo '<br /><input';
							if(in_array($d,explode(',',$plxPlugin->getParam('saved_dirs')))) { echo ' checked'; }
							echo' type="checkbox" value="'. $d .'" name="data[]" />'. $d .'<br />';
						}
						?>
					<div class="clear"></div>
					<input type="submit" name="submit" value="OK" />
		</fieldset>
	</form>
	<div class="clear"></div>
</div>
