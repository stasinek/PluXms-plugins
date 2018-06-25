<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

if(!empty($_POST)) {
	var_dump($_POST);
	$plxPlugin->setParam('elmt0', plxUtils::getValue($_POST['elmt0']), 'numeric');
	$plxPlugin->setParam('elmt1', plxUtils::getValue($_POST['elmt1']), 'numeric');
	$plxPlugin->setParam('elmt2', plxUtils::getValue($_POST['elmt2']), 'numeric');
	$plxPlugin->setParam('elmt3', plxUtils::getValue($_POST['elmt3']), 'numeric');
	$plxPlugin->setParam('elmt4', plxUtils::getValue($_POST['elmt4']), 'numeric');
	$plxPlugin->setParam('elmt5', plxUtils::getValue($_POST['elmt5']), 'numeric');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxPager');
	exit;
}
$sel=' checked="checked"';
$elmt0 = ($plxPlugin->getParam('elmt0')==1 OR $plxPlugin->getParam('elmt0')=='')?$sel:'';
$elmt1 = ($plxPlugin->getParam('elmt1')==1 OR $plxPlugin->getParam('elmt1')=='')?$sel:'';
$elmt2 = ($plxPlugin->getParam('elmt2')==1 OR $plxPlugin->getParam('elmt2')=='')?$sel:'';
$elmt3 = ($plxPlugin->getParam('elmt3')==1 OR $plxPlugin->getParam('elmt3')=='')?$sel:'';
$elmt4 = ($plxPlugin->getParam('elmt4')==1 OR $plxPlugin->getParam('elmt4')=='')?$sel:'';
$elmt5 = ($plxPlugin->getParam('elmt5')==1 OR $plxPlugin->getParam('elmt5')=='')?$sel:'';
?>

<script type="text/javascript">
var head = document.getElementsByTagName("head")[0];
var css = document.createElement('link');
css.type = "text/css";
css.rel = "stylesheet";
css.href = "<?php echo PLX_PLUGINS ?>plxPager/style.css";
css.media = "screen";
head.appendChild(css);
</script>

<form class="inline-form" id="form_plxPager" action="parametres_plugin.php?p=plxPager" method="post">
	<fieldset class="withlabel">
		<p><?php $plxPlugin->lang('L_PAGER_CHECK_LIB') ?></p>
		<p>
			<input<?php echo $elmt0 ?> type="checkbox" id="id_elmt0" name="elmt0" value="1" />			
			<label for="id_elmt0"><?php $plxPlugin->lang('L_PAGER_NUM_PAGES') ?>:</label>
			<?php printf('<span class="p_page">'.ucfirst(L_PAGINATION).'</span>',1,10) ?>
		</p>
		<p>
			<input<?php echo $elmt1 ?> type="checkbox" id="id_elmt1" name="elmt1" value="1" />
			<label for="id_elmt1"><?php echo L_PAGINATION_FIRST_TITLE ?>&nbsp;:</label>
			<?php echo '<span class="p_first">'.L_PAGINATION_FIRST.'</span>' ?>
		</p>
		<p>
			<input<?php echo $elmt2 ?> type="checkbox" id="id_elmt2" name="elmt2" value="1" />		
			<label for="id_elmt2"><?php echo L_PAGINATION_PREVIOUS_TITLE ?>&nbsp;:</label>
			<?php echo '<span class="p_prev">'.L_PAGINATION_PREVIOUS.'</span>' ?>
		</p>
		<p>
			<input<?php echo $elmt3 ?> type="checkbox" id="id_elmt3" name="elmt3" value="1" />
			<label for="id_elmt3"><?php $plxPlugin->lang('L_PAGER_INDICATOR') ?>&nbsp;:</label>
			<?php echo '<span class="p_page">...</span>' ?>
		</p>
		<p>
			<input<?php echo $elmt4 ?> type="checkbox" id="id_elmt4" name="elmt4" value="1" />		
			<label for="id_elmt4"><?php echo L_PAGINATION_NEXT_TITLE ?>&nbsp;:</label>
			<?php echo '<span class="p_next">'.L_PAGINATION_NEXT.'</span>' ?>
		</p>
		<p>
			<input<?php echo $elmt5 ?> type="checkbox" id="id_elmt5" name="elmt5" value="1" />		
			<label for="id_elmt5"><?php echo L_PAGINATION_LAST_TITLE ?>&nbsp;:</label>
			<?php echo '<span class="p_last">'.L_PAGINATION_LAST.'</span>' ?>
		</p>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_PAGER_SAVE') ?>" />
		</p>
	</fieldset>
</form>
<div class="p_exemple">
<?php
if($elmt0!='') printf('<span class="p_page">'.ucfirst(L_PAGINATION).'</span>',1,10);
if($elmt1!='')echo '<span class="p_first">'.L_PAGINATION_FIRST.'</span>';
if($elmt2!='')echo '<span class="p_prev">'.L_PAGINATION_PREVIOUS.'</span>';
if($elmt3!='')echo '<span class="p_page">...</span>';
echo '<span class="p_page">1</span>';
echo '<span class="p_page">2</span>';
echo '<span class="p_current">4</span>';
echo '<span class="p_page">5</span>';
echo '<span class="p_page">6</span>';
if($elmt3!='') echo '<span class="p_page">...</span>';
if($elmt4!='') echo '<span class="p_next">'.L_PAGINATION_NEXT.'</span>';
if($elmt5!='') echo '<span class="p_last">'.L_PAGINATION_LAST.'</span>';
?>
</div>
<p>
	<?php $plxPlugin->lang('L_PAGER_CSS_DESCRIPTION') ?><br />
	.p_page : <?php $plxPlugin->lang('L_PAGER_CSS_ITEM') ?><br />
	.p_first : <?php $plxPlugin->lang('L_PAGER_CSS_FIRST') ?><br />
	.p_prev : <?php $plxPlugin->lang('L_PAGER_CSS_PREV') ?><br />
	.p_current : <?php $plxPlugin->lang('L_PAGER_CSS_CURRENT') ?><br />
	.p_next : <?php $plxPlugin->lang('L_PAGER_CSS_NEXT') ?><br />
	.p_last : <?php $plxPlugin->lang('L_PAGER_CSS_LAST') ?><br />
</p>