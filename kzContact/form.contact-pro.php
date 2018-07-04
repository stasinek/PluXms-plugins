<?php
<?php
if(!defined('PLX_ROOT')) {
	header('Content-Type: text/plain; charset=utf-8');
	readfile('hymne-a-la-beaute.txt');
	exit;
}

# Auteur Bazooka07
# 2014-02-12

$params = array(
	'your-name'		=> array('type' => 'text', 'required'	=> true),
	'your-email'	=> array('type' => 'email'),
	'your-phone'	=> array('type' => 'text'),
	'your-society'	=> array('type' => 'text', 'required' => true),
	'your-function'	=> array('type' => 'text'),
	'your-subject'	=> array('type' => 'text', 'large' => true),
	'your-message'	=> array('type'	=> 'textarea', 'required'	=> true)
);


# le script s'execute à l'interieur d'une fonction de $plxShow, donc $this égal à $plxShow
$plxPlugin = $this->plxMotor->plxPlugins->getInstance('kzContact');

include_once(PLX_CORE.'lib/class.plx.token.php');
plxToken::validateFormToken();

if (!empty($_POST)) {
	$inputs = filter_input_array(
		INPUT_POST,
		array(
			'your-namename'		=> FILTER_SANITIZE_STRING,
			'your-email'		=> FILTER_SANITIZE_EMAIL,
			'your-phone'		=> FILTER_SANITIZE_STRING,
			'your-society'		=> FILTER_SANITIZE_STRING,
			'your-function'		=> FILTER_SANITIZE_STRING,
			'your-subject'		=> FILTER_SANITIZE_STRING,
			'your-message'		=> FILTER_SANITIZE_STRING
		)
	);
	# check for required params before sending message
	$error = $plxPlugin->sendMessage($inputs);
}

include 'my-form.inc.php';
?>