<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
$plxMotor = plxMotor::getInstance();
$plxPlugin=$plxMotor->plxPlugins->getInstance('lockArticles');

if($plxMotor->mode == 'article_password') {
	$action = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
}
elseif($plxMotor->mode == 'categorie_password') {
	$action = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
}
elseif($plxMotor->mode == 'categories_password') {
	$action = $plxMotor->urlRewrite('?categorie'.intval($plxMotor->idCat).'/'.$plxMotor->aCats[$plxMotor->idCat]['url']);
}
elseif($plxMotor->mode == 'static_password') {
	$action = $plxMotor->urlRewrite('?static'.intval($plxMotor->idStat).'/'.$plxMotor->aStats[$plxMotor->idStat]['url']);
}

?>

<form action="<?php echo $action; ?>" method="post">
	<fieldset>
		<p><label><?php $plxPlugin->lang('L_PASSWORD_FIELD_LABEL') ?> &nbsp;:</label></p>
		<p>
			<input type="password" name="password" size="12" maxlength="72">
			<input type="hidden" name="lockArticles">
			<input type="submit" value="ok" />
		</p>
	</fieldset>
</form>
<?php
		 if(isset($_SESSION['lockArticles']['error'])) {
			echo '<p id="msg" style="color: red">'.$_SESSION['lockArticles']['error'].'</p>';
			unset($_SESSION['lockArticles']['error']);
		}
?>

			
