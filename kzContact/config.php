<?php
if(!defined('PLX_ROOT')) {
	header('Content-Type: text/plain; charset=utf-8');
	readfile('hymne-a-la-beaute.md');
	exit;
}

# Control du token du formulaire
plxToken::validateFormToken($_POST);

# On récupère les templates des pages statiques
$files = plxGlob::getInstance(PLX_ROOT.'themes/'.$plxAdmin->aConf['style']);
if ($array = $files->query('/^static(-\w[\w-]*)?.php$/')) {
	$aTemplates = array();
	foreach($array as $k=>$v)
		$aTemplates[$v] = ucfirst(substr($v, 0, -4));
}

// liste des formulaires disponibles
$prefix = kzContact::PREFIX_CONTACT;
$forms = glob(PLX_PLUGINS."$plugin/{$prefix}.*.php");
$formSelect = array();
foreach ($forms as $f) {
	$value = substr(basename($f, '.php'), strlen($prefix)+1);
	$formSelect[$value] = ucfirst($value);
}

$params = array(
	'mnuDisplay'	=> array('type' => 'checkbox'),
	'mnuName'		=> array(
		'type' => 'text',
		'default' => $plxPlugin-> getLang('L_DEFAULT_MENU_NAME'),
		'required' => true
	),
	'mnuPos'		=> array('type' => 'number', 'default' => 2),
	'mnuGroup'		=> array(
		'type' => 'text',
		'extras' => array('min' => '1', 'max' => count($plxAdmin->aStats))
	),
	'sitemap'		=> array('type' => 'checkbox'),
	'title_htmltag'	=> array('type' => 'text', 'break-before' => true),
	'template'		=> array(
		'type' => 'select',
		'default' => 'static.php',
		'select' => $aTemplates
	),
	'content'		=> array(
		'type' => 'select',
		'default' => 'form',
		'select' => $formSelect
	),
	'mnuText'		=> array(
		'type' => 'textarea',
		'default' => $plxPlugin-> getLang('L_MSG_WELCOME')
	),
	'capcha'		=> array('type' => 'checkbox'),
	'subject'		=> array(
		'type' => 'text',
		'default' => $plxPlugin-> getLang('L_DEFAULT_OBJECT'),
		'large' => true,
		'break-before' => true
	),
	'email'			=> array('type' => 'email', 'required' => true),
	'email_cc'		=> array('type' => 'email', 'multiple' => true),
	'email_bcc'		=> array('type' => 'email', 'multiple' => true),
	'envoiSepare'	=> array('type' => 'checkbox'),
	'thankyou'		=> array(
		'type' => 'textarea',
		'default' => $plxPlugin-> getLang('L_DEFAULT_THANKYOU'),
		'break-before' => true
	)
);

if(!empty($_POST)) {
	$newLocation = "Location: parametres_plugin.php?p=$plugin";
	foreach ($params as $field=>$infos) {
		$value = trim(filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING));
		if(!empty($value)) {
			switch($infos['type']) {
				case 'select':
					if(array_key_exists($value, $infos['select'])) {
						$paramType = 'string';
					} else {
						plxMsg::Error(sprint($this->getLang('L_BAD_VALUE'), $plxPlugin->getLang('L_'.strtoupper($field))));
					}
					break;
				case 'number':
					$paramType = 'numeric';
					$value = intVal($value);
					break;
				case 'checkbox':
					if($value == '1') {
						$paramType = 'numeric';
					} else {
						plxMsg::Error(sprint($this->getLang('L_BAD_VALUE'), $plxPlugin->getLang('L_'.strtoupper($field))));
					}
					break;
				case 'textarea':
					$paramType = 'cdata';
					break;
				#case 'email':
				#	break;
				default:
					$paramType = 'string';
			}
			$plxPlugin->setParam($field, $value, $paramType);
		} elseif(array_key_exists('required', $infos)) {
			plxMsg::Error(sprint($this->getLang('L_MISSING_VALUE'), $plxPlugin->getLang('L_'.strtoupper($field))));
			header($newLocation);
			exit;
		} else {
			$plxPlugin->delParam($field);
		}
	}
	$plxPlugin->saveParams();
	header($newLocation);
	exit;
}

include 'my-form.inc.php';
?>