<?php 
/**
 * Plugin adhesion
 *
 * @version	1.5
 * @date	07/10/2013
 * @author	Stephane F, Cyril MAGUIRE
 **/
 if(!defined('PLX_ROOT')) exit;

$plxMotor = plxMotor::getInstance();
$plxPlugin=$plxMotor->plxPlugins->getInstance('adhesion');
?>
<?php if ($plxPlugin->getParam('desc_adhesion') != str_replace("'","’",$plxPlugin->getLang('L_DEFAULT_DESC'))) echo htmlspecialchars_decode($plxPlugin->getParam('desc_adhesion'));?>