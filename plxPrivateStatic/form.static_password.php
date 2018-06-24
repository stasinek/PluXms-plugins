<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

$plxMotor = plxMotor::getInstance();
$plxPlugin=$plxMotor->plxPlugins->getInstance('plxMyPrivateStatic');

$error=false;

if(!empty($_POST)) {
	if($plxMotor->aStats[$_POST['id']]['password']!=sha1($_POST['password']))
		$error = $plxPlugin->getLang('L_INVALID_PASSWORD');
	else {
		$_SESSION['password_statics'][$_POST['id']]=true;
		header('Location: '.$plxMotor->urlRewrite('?static'.intval($_POST['id']).'/'.$plxMotor->aStats[$_POST['id']]['url']));
		exit;
	}
}
$filename = PLX_ROOT.$plxMotor->aConf["racine_statiques"].$plxMotor->idStat.".plxMyPrivateStatic.php";
if(file_exists($filename))
	require $filename;
?>
<div id="form_static_password">
	<?php if($error): ?>
	<p class="static_password_error"><?php echo $error ?></p>
	<?php endif; ?>
	<form action="<?php echo $plxMotor->urlRewrite('?static'.intval($plxMotor->idStat).'/'.$plxMotor->aStats[$plxMotor->idStat]['url']) ?>" method="post">
		<fieldset>
		<p><label for="password"><?php $plxPlugin->lang('L_FORM_PASSWORD') ?>&nbsp;:</label></p>
		<input id="password" name="password" type="text" size="30" value="" />
		<p>
			<input type="hidden" name="id" value="<?php echo $plxMotor->idStat ?>" />
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_BTN_SEND') ?>" />
		</p>
		</fieldset>
	</form>
</div>