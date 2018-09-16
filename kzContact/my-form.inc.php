<?php
if(!defined('PLX_ADMIN') and isset($error) and $error === false) {
	# Courriel envoyé
?>
			<p class="success"><?php echo nl2br(plxUtils::strCheck($plxPlugin->getParam('thankyou'))); ?></p>
			<p><input type="button" value="Retour à l'accueil" onclick="window.location.replace('<?php echo $this->plxMotor->racine; ?>');" /></p>
<?php
} else {
	if(defined('PLX_ADMIN')) {
		# côté admin
		if(function_exists('mail')) {
			$color = 'green'; $msg = $plxPlugin->getLang('L_MAIL_AVAILABLE');
		} else {
			$color = 'red'; $msg = $plxPlugin->getLang('L_MAIL_NOT_AVAILABLE');
		}
			echo <<< EOT

		<p style="color:$color"><strong>$msg</strong></p>\n
EOT;
	} else {
		# côté site
		if(!empty($error)) {
			# Il y a une erreur
			echo <<< EOT
				<p class="contact_error">$error</p>\n
EOT;
		}
		$mnuText = $plxPlugin->getParam('mnuText');
		if(! empty($mnuText)) {
			echo <<< EOT
		<h4>$mnuText</h4>
EOT;
		}
	}
?>
			<form class="inline-form kz-contact" method="post">
			<fieldset>
<?php
		foreach ($params as $field=>$infos) {
			if(!empty($infos['break-before'])) {
				echo <<< SEPARATOR
			</fieldset><fieldset>
SEPARATOR;
			}
			$class = ($infos['type'] == 'textarea' or !empty($infos['large'])) ? ' class="large"' : '';
			$caption = $plxPlugin->getLang('L_'.strtoupper($field));
			echo <<< START
				<div$class>
					<label for="id_{$field}">$caption</label>\n
START;
			$value = (defined('PLX_ADMIN')) ? $plxPlugin->getParam($field) : '';
			if(empty($value) and !empty($infos['default'])) {
				$value = $infos['default'];
			}
			$required = (!empty($infos['required'])) ? ' required' : '';
			$extras = (!empty($infos['extras'])) ? ' '.implode(' ', array_map(
				function($a, $b) {
					return <<< EXTRAS
$a="$b"
EXTRAS;
				},
				array_keys($infos['extras']),
				array_values($infos['extras'])
			)) : '';
			switch($infos['type']) {
				case 'textarea':
					echo <<< TEXTAREA
					<textarea id="id_{$field}" name="$field"{$extras}{$required}>$value</textarea>\n
TEXTAREA;
					break;
				case 'select' :
					$options = implode("\n", array_map(function($option, $caption) use($value) {
						$selected = ($option == $value) ? ' selected' : '';
						return <<< OPTION
							<option value="$option"$selected>$caption</option>
OPTION;
					}, array_keys($infos['select']), array_values($infos['select'])));
					echo <<< SELECT
					<select name="$field"{$extras}>
$options
					</select>\n
SELECT;
					break;
				case 'checkbox':
					$checked = (!empty($value)) ? ' checked' : '';
					echo <<< CHECKBOX
					<input type="checkbox" id="id_{$field}" name="$field" value="1"{$extras}$checked />\n
CHECKBOX;
					break;
				default:
					$multiple = ($infos['type'] == 'email' and !empty($infos['multiple'])) ? ' multiple' : '';
					echo <<< INPUT
					<input type="{$infos['type']}" id="id_{$field}" name="$field" value="$value"{$extras}{$required}{$multiple} />\n
INPUT;
			}

			echo <<< END
				</div>\n
END;
		}
?>
				<div class="kz-contact-footer">
<?php
if(!defined('PLX_ADMIN') and !empty($plxPlugin->getParam('capcha'))) { ?>
					<div id="captcha-bloc">
						<?php $this->capchaQ(); ?>
						<input id="id_rep" name="rep" type="text" size="6" maxlength="6" data-type="antispam"/>
					</div>
<?php } ?>
					<div class="in-action-bar">
						<?php echo plxToken::getTokenPostMethod(); ?>

						<input type="submit" />
					</div>
				</div>
			</fieldset>
		</form>
<?php
}
?>