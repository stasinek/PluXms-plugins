<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	$plxPlugin->setParam('buttons', $_POST['buttons'], 'cdata');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxToolbarEditor');
	exit;
}
?>


<div id="form" class="plxToolbarEditor" >
<form class="inline-form" action="parametres_plugin.php?p=plxToolbarEditor" method="post" id="form_test">

<?php plxUtils::printArea('buttons',$plxPlugin->getParam('buttons'),'50','50') ; ?>

<p class="in-action-bar"><?php echo plxToken::getTokenPostMethod() ?><input type="submit" name="submit" value="Enregistrer" /></p>

</div>