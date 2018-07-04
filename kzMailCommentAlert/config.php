<?php
if(!defined('PLX_ROOT')) { exit('You are a looser !'); }

# Contrôle du token du formulaire
plxToken::validateFormToken();

if(!empty($_POST)) {
	$plxPlugin->save();
	header('Location: parametres_plugin.php?p='.$plugin);
	exit;
}

?>
<form id="form_<?php echo $plugin; ?>" class="normal inline-form" method="post">
	<h3><?php $plxPlugin->lang('RECIPIENTS_TITLE'); ?> :</h3>
<?php $plxPlugin->get_recipients(); ?>
	<div class="small-full">
		<label for="id_from"><?php $plxPlugin->lang('FROM'); ?></label>
		<input type="email" name="from" id="id_from" value="<?php echo $plxPlugin->getParam('from'); ?>" />
	</div>
<?php
foreach(explode(' ', kzMailCommentAlert::NUM_FIELDS) as $field) {
	$plxPlugin->get_numericField($field);
}
?>
	<div class="in-action-bar">
		<?php echo plxToken::getTokenPostMethod(); ?>
		<input type="submit" value="<?php $plxPlugin->lang('SAVE'); ?>">
	</div>
</form>
<script type="text/javascript">
	(function() {
		'use strict';

		const form = document.forms[0];
		const chks = form.querySelectorAll('input[type="checkbox"][name="recipients[]"]');
		if(chks.length > 0) {
			const SELECTOR_CSS = 'input[type="checkbox"][name="recipients[]"]:checked';
			for(var i=0, iMax=chks.length; i<iMax; i++) {
				chks[i].addEventListener('change', function(event) {
					const disabled = (form.querySelectorAll(SELECTOR_CSS).length == 0);
					const entries = form.querySelectorAll('input[type="email"], input[type="number"]');
					for(var k=0, kMax=entries.length; k<kMax; k++) {
						const item = entries[k];
						item.disabled = disabled;
					}
					event.preventDefault();
				});
			}
		}
	})();
</script>