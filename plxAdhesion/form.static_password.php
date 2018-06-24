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
$plxPlugin=$plxMotor->plxPlugins->getInstance('adhesion');
/*
$error=false;

if(!empty($_POST) && isset($_POST['password'])) {
	if(!$plxPlugin->verifPass(md5($_POST['password'])))
		$error = $plxPlugin->getLang('L_PLUGIN_BAD_PASSWORD');
	else {
		//$_SESSION['password_statics'][$_POST['id']]=true;
		$_SESSION['lockArticles']['categorie'] = 'on';
		header('Location: '.$plxMotor->urlRewrite('?static'.intval($_POST['id'])));
		exit;
	}
}

?>

<div id="form_static_password">
	<?php if($error): ?>
	<p class="static_password_error"><?php echo $error ?></p>
	<?php endif; ?>
	<form action="" method="post">
		<fieldset>
		<p><label for="password"><?php $plxPlugin->lang('L_FORM_PASSWORD') ?>&nbsp;:</label></p>
		<p>
			<input id="password" name="password" type="password" size="30" value="" />
			<input type="hidden" name="id" value="<?php echo $plxMotor->idStat ?>" />
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_OK') ?>" />
		</p>
		</fieldset>
	</form>
</div>
*/
?>
<p class="locked"><?php echo $plxPlugin->getLang('L_NEED_AUTH');?></p>