<?php

if(!defined('PLX_ROOT')) exit;

# Control du token du formulaire
plxToken::validateFormToken($_POST);

$params = array(
	'cdn'					=> 'boolean',
	'theme'					=> 'string',
	'article'				=> 'numeric',
	'statique'				=> 'boolean',
	'comment'				=> 'boolean',
	'categorie'				=> 'boolean',
	'parametres_edittpl'	=> 'boolean',
	'parametres_plugincss'	=> 'boolean',
	'profil'				=> 'boolean',
	'lineNumbers'			=> 'boolean', // respecter la casse !
	'matchTags'				=> 'boolean',
//	'site'					=> 'boolean',
	'lint'					=> 'boolean',
	'keyMap'				=> 'string'
//	'emmet'					=> 'boolean'
);

// A faire créer avant le $_POST, les tableaux pour les printSelect
$extensions = array(
	'theme'		=> 'css',
	'keyMap'	=> 'js'
);

if(!empty($_POST)) {

	/* Traitement  du formulaire de configuration */
	foreach($params as $field=>$type) {
		$plxPlugin->delParam($field);
		if(!empty($_POST[$field])) {
			switch($type) {
				case 'boolean':
					$value = filter_input(INPUT_POST, $field, FILTER_VALIDATE_BOOLEAN) ? 1 : false;
					break;
				case 'numeric':
					if(is_array($_POST[$field])) {
						//$value = array_sum(filter_input_array(INPUT_POST, $field, FILTER_SANITIZE_NUMBER_INT));
						$value = array_sum($_POST[$field]);
					} else {
						$value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_NUMBER_INT);
					}
					break;
				default:
					// Remplacer FILTER_SANITIZE_STRING en vérifiant que la valeur du $_POST est bien présente dans les optiosn du select correspondant
					$value = trim(filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING));
			}
			if(!empty($value)) {
				$plxPlugin->setParam($field, $value, ($type == 'boolean' ? 'numeric' : $type));
				continue;
			}
		}
	}
	$plxPlugin->saveParams();

	header('Location: parametres_plugin.php?p='.$plugin);
	exit;
}

$plxPlugin->updateLang();

function print_profils() {
	global $plxPlugin;
?>
		<p>
			<span class="profils-label"><?php echo $plxPlugin->lang('L_PROFILS'); ?></span>
<?php
	foreach($plxPlugin->profils as $profil=>$weigth) {
?>
		<span class="profil" title="<?php $plxPlugin->lang('L_PROFIL_'.$profil)?>"><?php $plxPlugin->lang('L_SHORT_PROFIL_'.$profil)?></span>
<?php
	}
?>
		</p>
<?php
}

function print_checkboxes_profils($name, $value) {
	global $plxPlugin;

	if(!is_integer($value)) { $value = 0; }
	foreach($plxPlugin->profils as $profil=>$weight) {
		$checked = (!empty($value & $weight)) ? ' checked' : '';
?>
		<input class="profil" type="checkbox" name="<?php echo $name; ?>[]" value="<?php echo $weight; ?>"<?php echo $checked; ?> />
<?php
	}
}

$url_base = (empty($plxPlugin->local_path)) ? 'http://codemirror.net' : $plxPlugin->cm_url;
$sample = PLX_ROOT.PLX_CONFIG_PATH."plugins/$plugin.admin.css";
if(!file_exists($sample)) {
	$sample = __DIR__.'/css/admin.css';
}

/* ------------ form starts here -----------------*/
?>

<div id="codemirror-configuration">
	<form id="form_<?php echo $plugin; ?>" method="post">
		<div class="in-action-bar">
			<p>
				<input type="submit" value="<?php echo L_ARTICLE_UPDATE_BUTTON; ?>" />
			</p>
		</div>
<?php

echo plxToken::getTokenPostMethod();

foreach($params as $field=>$type) {
	$value = $plxPlugin->getParam($field);
	if($field == 'article') {
		print_profils();
	}
?>
		<p>
			<label for="id_<?php echo $field; ?>"><?php echo $plxPlugin->lang('L_'.strtoupper($field)); ?></label>
<?php
		switch($type) {
			case 'boolean':
				if($field == 'cdn') {
					$checked = (empty($plxPlugin->local_path)) ? ' checked' : '';
				} else {
					$checked = (!empty($plxPlugin->getParam($field))) ? ' checked' : '';
				}
?>
			<input type="checkbox" id="id_<?php echo $field; ?>" name="<?php echo $field; ?>" value="1" <?php echo $checked; ?> />
<?php
				break;
			case 'numeric':
				switch($field) {
					case 'article':
						print_checkboxes_profils($field, $value);
						break;
					default:
						plxUtils::printInput($field, $value, 'number');
				}
				break;
			default:
				if(array_key_exists($field, $extensions)) {
					$plxPlugin->print_select($field, $value, $extensions[$field]);
				} else {
					plxUtils::printInput($field, $value);
				}
		}

		if($field == 'cdn') {
			if(empty(glob(__DIR__.'/codemirror*', GLOB_ONLYDIR))) {
				if(class_exists('ZipArchive') and function_exists('curl_init')) {
?>
			<input type="submit" id="id_download" name="download" value="<?php $plxPlugin->lang('L_DOWNLOAD'); ?>" />
<?php
				}
			} else {
?>
			<span title="<?php $plxPlugin->lang('L_LOCAL_LIBRARY_TITLE'); ?>"><?php $plxPlugin->lang('L_LOCAL_LIBRARY'); ?></span>
<?php
			}
		}
?>
		</p>
<?php
}
?>
	</form>
</div>
<textarea id="id_sandbox" name="sandbox" class="full-width"><?php readfile($sample); ?></textarea>
<div id="codemirror-promotion">
	<p><a href="<?php echo $url_base; ?>" rel="noreferrer" target="_blank"><img src="<?php echo $url_base; ?>icon.png" alt="CodeMirror" /></a></p>
	<p><a href="<?php echo $url_base; ?>/doc/manual.html#addons" rel="noreferrer" target="_blank">Addons</a></p>
	<p><a href="<?php echo $url_base; ?>/demo/" rel="noreferrer" target="_blank">Demos</a></p>
</div>
<script type="text/javascript">
	(function() {
		'use strict';
<?php
	if(empty($plxPlugin->local_path)) {
?>

		// récupère tous les thèmes disponibles de Codemirror sur Internet. Ajouter raccourcis clavier
		const API_CDNJS = 'https://api.cdnjs.com/libraries/codemirror';

		const XHR = new XMLHttpRequest();
		XHR.onreadystatechange = function(event) {
			if(this.readyState === XMLHttpRequest.DONE) {
				if(this.status === 200) {
					var resp = JSON.parse(XHR.responseText);
					var version = resp.version
					var patterns = {
						theme	: /^theme\/([\w\-]+)\.css$/,
						keymap	: /^keymap\/([\w\-]+)\.js$/
					}
					for(var key in patterns) {
						var filenames = resp.assets[0].files.filter(function(item) {
							return item.match(patterns[key]);
						});
						var	select = document.getElementById('id_' + key),
							value = select.value,
							innerHTML = '<option value=""><?php $plxPlugin->lang('L_DEFAULT'); ?></option>\n';
						filenames.forEach(function(item) {
							var	option = item.replace(patterns[key], '$1'),
								caption = option.replace(/^([a-z])/, function(a) { return a.toUpperCase(); }),
								selected = (option == value) ? ' selected' : '';
							innerHTML += '<option' + selected + ' value="'+ option + '">' + caption + '</option>\n';
						});
						select.innerHTML = innerHTML;
					}
				}
			}
		}

		XHR.open('GET', API_CDNJS);
		XHR.send();
<?php
	}
?>

		const THEMES_SELECT = document.getElementById('id_theme');
		if(THEMES_SELECT != null) {

			const STYLESHEET_TEMPLATE = '<?php echo $plxPlugin->cm_url; ?>/theme/##theme##.css'
			THEMES_SELECT.addEventListener('change', function(event) {
				var theme = THEMES_SELECT.value;
				var stylesheet = document.head.querySelector('link[rel="alternate stylesheet"][title="' + theme + '"]');
				if(stylesheet == null) {
					stylesheet = document.createElement('LINK');
					stylesheet.type = 'text/css';
					stylesheet.rel = 'alternate stylesheet';
					stylesheet.title = theme;
					stylesheet.href = STYLESHEET_TEMPLATE.replace('##theme##', theme);
					document.head.appendChild(stylesheet);
				}
				const links = document.head.querySelectorAll('link[rel="alternate stylesheet"]');
				for(var i=0, iMax=links.length; i<iMax; i++) {
					links[i].disabled = (links[i].title != theme);
				}

				editors.forEach(function(ed) {
					ed.setOption('theme', theme);
				});
			});
		}

		const CDN_CHECKBOX = document.getElementById('id_cdn');
		if(CDN_CHECKBOX != null) {
			CDN_CHECKBOX.addEventListener('change', function(event) {
				if(confirm('<?php $plxPlugin->lang('L_SAVE_CONFIG'); ?>')) {
					CDN_CHECKBOX.form.submit();
				}
			});
		}
	})();
</script>
