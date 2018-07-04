<?php
# config for lostPassword

$fields = array('name'=>'string', 'from'=>'string', 'subject'=>'string', 'bcc'=>'string', 'body'=>'cdata');
$default_values = array(
	'name'=>'Webmaster', 'from'=>'',
	'subject'=>$plxPlugin->getLang('L_PASSWD_SENT_SUBJECT'), 'bcc'=>'',
	'body'=>$plxPlugin->getLang('L_PASSWD_SENT_BODY'));

if(!empty($_POST)) {
	foreach ($fields as $field=>$type)
		$plxPlugin->setParam($field, $_POST[$field], $type);
	$plxPlugin->saveParams();
}

define('BODY', 'body');
?>
<h2><?php echo($plxPlugin->getInfo('title')); ?></h2>
<form id="form_lostpassword" method="post" onsubmit="return lostpassword_config_onsubmit('id_<?php echo(BODY); ?>', '<?php  echo($plxPlugin->lang('L_PASSWD_BAD_BODY')); ?>');">
<?php
if (plxUtils::testMail(true, "<p><span style=\"color:#color\">#symbol #message</span></p>\n")) {
?>
<?php foreach ($fields as $field=>$type) {
	$class = $type == 'cdata' ? ' class="large"': ''; ?>
	<p>
		<label for="name"<?php echo $class; ?>><?php $plxPlugin->lang('L_PASSWD_'.strtoupper($field)); ?></label> <?php
		$param = $plxPlugin->getParam($field);
		$value = $param ? plxUtils::strCheck($param) : $default_values[$field];
		switch ($type) {
			case 'cdata' :
				plxUtils::printArea(BODY, $value, 70, 12);
				break;
			default :
				plxUtils::printInput($field, $value, 'text', '50-80');
				break;
		} ?>
	</p>
<?php } ?>
	<p>
		<label>&nbsp;</label>
		<input type="submit" />
	</p>
<?php } ?>
</form>
