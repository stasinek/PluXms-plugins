<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

include(dirname(__FILE__).'/lib/class.plx.maintenance.php');  
  
class plxMaintenance extends plxPlugin {

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accéder à la page config.php et admin.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->setAdminProfil(PROFIL_ADMIN);
		
		# Personnalisation du menu admin
		$status =  $this->getParam('mode_maintenance')==0 ? $this->getLang('L_M_DESACTIVED') : $this->getLang('L_M_ACTIVED');
		$pos = @stripos($_SERVER['REDIRECT_URL'], 'core');
		if ($pos !== false) {
			if ($this->getParam('mode_maintenance') == 1) {		    
				echo "\t".'<style type="text/css">'."\n";
				echo "\t".'.aside ul[id$=\'menu\'] a[href$=\'plxMaintenance\'] {'."\n";
				echo "\t".'color: red;'."\n";
				echo "\t".'}'."\n";		
				echo "\t".'</style>'."\n";
			}
		}
		$this->setAdminMenu($this->getlang('L_M_MAINTENANCE').' : '.$status,'',$this->getlang('L_M_MAINTENANCE').' : '.$status);	
		
		$this->maintenance = new maintenance();
		
		# ajout du hook
		$this->addHook('Index', 'Index');

	}

	/**
	 * Méthode pour afficher les differents compteurs
	 *
	 * @return	NULL
	 * @author	DPFPIC
	 **/	
	public function Index() {
		$baseDir = plxUtils::getRacine();
		if ($this->getParam('mode_maintenance') == '1' AND $_SERVER["REMOTE_ADDR"] != $this->getParam('ip_maintenance')) {
			$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
			$html .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >'."\n";
			$html .= '<head>'."\n";
			$html .= '<title>'.plxPlugin::getLang('L_M_TITLE').'</title>'."\n";
			$html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
			$html .= '<style media="screen">'."\n";
			$html .= '/* IMPORTANT */'."\n";
			$html .= 'html {'."\n";
			$html .= 'height: 100%;'."\n";
			$html .= 'font-family: Georgia, serif;'."\n";
			$html .= '}'."\n";
			$html .= 'body {'."\n";
			$html .= 'height: 100%;'."\n";
			$html .= 'margin: 0;'."\n";
			$html .= 'padding: 0;'."\n";
			$html .= '}'."\n";
			$html .= '#page-table {'."\n";
			$html .= 'height: 100%;'."\n";
			$html .= 'width: 100%;'."\n";
			$html .= 'border-collapse: collapse;'."\n";
			$html .= 'text-align: center;'."\n";
			$html .= '}'."\n";
			$html .= '#page-td {'."\n";
			$html .= 'height: 100%;'."\n";
			$html .= 'padding: 0;'."\n";
			$html .= 'vertical-align: middle;'."\n";
			$html .= '}'."\n";
			$html .= 'div#global {'."\n";
			$html .= 'width: 600px;'."\n";
			$html .= 'margin: 20px auto;'."\n";
			$html .= 'text-align: left;'."\n";
			$html .= '}'."\n";
			$html .= '</style>'."\n";
			$html .= '</head>'."\n";
			$html .= '<body>'."\n";
			$html .= '<table id="page-table"><tr><td id="page-td">'."\n";
			$html .= '<div id="global">'."\n";
			$html .= str_replace('../../',$baseDir,plxPlugin::getParam('page_maintenance'))."\n";
			$html .= '</div><!--#global-->';
			if ($this->getParam('link_admin') == '1') {
			
				$html .= '<div id="admin"><a class="footer" href="'.$baseDir.'core/admin">Administration</a></div>'."\n";
			}
			$html .= '</td></tr>'."\n";
			$html .= '</table><!--#page-table-->'."\n";
			$html .= '</body>'."\n";
			$html .= '</html>'."\n";
			
			echo $html;
			exit;
		}
	}
		
}
?>