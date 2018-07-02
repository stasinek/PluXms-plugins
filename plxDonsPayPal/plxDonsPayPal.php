<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
include(dirname(__FILE__).'/lib/class.plx.donspaypal.php');
 
class plxDonsPayPal extends plxPlugin {

	# param	default_lang	langue par défaut utilisée par PluXml
	public function __construct($default_lang) {

      # appel du constructeur de la classe plxPlugin (obligatoire)
      parent::__construct($default_lang);

  		# droits pour accéder à la page config.php du plugin
  		$this->setConfigProfil(PROFIL_ADMIN);
		
		$this->lang = strtoupper($default_lang);
		
		# verification mise à jour plugin
		$this->donspaypal = new donspaypal();
		  
  		# Ajouts des hooks
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
  		$this->addHook('plxShowDonsPayPal', 'plxShowDonsPayPal');
  }
  
	public function ThemeEndHead() {
		
		# récupérer la racine du site http//.... et du repertoire plugins
		$baseDirPlugins = plxUtils::getRacine().substr(PLX_PLUGINS,2);
		
		# récupérer la largeur et la hauteur de l'image
		if (plxPlugin::getParam('dppImgButton') == 'custom') {
			$imgUrl = PLX_ROOT.plxPlugin::getParam('dppCustomButton');
		} else {
			$imgUrl = $baseDirPlugins.'plxDonsPayPal/img/'.$this->default_lang."/".plxPlugin::getParam('dppImgButton').'.png';
		}
		list($width, $height) = getimagesize($imgUrl);
		$align = plxPlugin::getParam('dppAlign');
		
		echo "\n\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'plxDonsPayPal/css/plxDonsPayPal.css" type="text/css" media="screen" />'.PHP_EOL;;
		echo "\t".'<style type="text/css">'.PHP_EOL;;
		if ($align != 'center') {
		echo "\t".'#indonspaypal {'.PHP_EOL;;
		echo "\t".'float: '.$align.';'.PHP_EOL;;
		echo "\t".'}'.PHP_EOL;;	
        }		
		echo "\t".'#donspaypal .img_input {'.PHP_EOL;;
		echo "\t".'width: '.$width.'px !important;'.PHP_EOL;;		
		echo "\t".'height: '.$height.'px !important;'.PHP_EOL;;
		echo "\t".'background-image: url("'.$imgUrl.'");'.PHP_EOL;;
		echo "\t".'}'.PHP_EOL;;
		echo "\t".'</style>'.PHP_EOL;;
    }  

	public function plxShowDonsPayPal() {
		
	   $url =  plxPlugin::getParam('dppAccountType')==0 ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	
		echo '<div id="donspaypal">'.PHP_EOL;  
		echo '<div id="indonspaypal">'.PHP_EOL;
		if (plxPlugin::getParam('dppActTextTop') == 1) echo '<span class="txt">'.plxPlugin::getParam('dppTextTop').'</span>'.PHP_EOL;
		echo '<form action="'.$url.'" method="post" target="'.plxUtils::strCheck(plxPlugin::getParam('dppOpens')).'">'.PHP_EOL;
		echo '<input type="hidden" name="cmd" value="_donations">'.PHP_EOL;
		echo '<input type="hidden" name="business" value="'.plxUtils::strCheck(plxPlugin::getParam('dppEmail')).'">'.PHP_EOL;
		echo '<input type="hidden" name="lc" value="'.$this->lang.'">'.PHP_EOL;
		echo '<input type="hidden" name="item_name" value="'.plxUtils::strCheck(plxPlugin::getParam('dppItem_Name')).'">'.PHP_EOL;
		if (plxPlugin::getParam('dppOptDonation') == 'fixed') {
			echo '<input type="hidden" name="amount" value="'.plxUtils::strCheck(plxPlugin::getParam('dppItem_Price')).'">'.PHP_EOL;
		} else {
			echo '<input type="hidden" name="no_note" value="0">'.PHP_EOL;
		}
		if (plxPlugin::getParam('dppReturnPage')!= '') {
			echo '<input type="hidden" name="return" value="'.plxUtils::strCheck(plxPlugin::getParam('dppReturnPage')).'" />'.PHP_EOL;
		}
        echo '<input type="hidden" name="rm" value="'.plxUtils::strCheck(plxPlugin::getParam('dppReturnMethod')).'" />'.PHP_EOL;
		echo '<input type="hidden" name="currency_code" value="'.plxUtils::strCheck(plxPlugin::getParam('dppDevise')).'">'.PHP_EOL;
		echo '<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">'.PHP_EOL;
		echo '<input class="img_input" type="submit" name="submit" alt="'.$this->getLang('L_DPP_HREF_TITLE').'" title="'.$this->getLang('L_DPP_HREF_TITLE').'">'.PHP_EOL;
		echo '</form>'.PHP_EOL;
		if (plxPlugin::getParam('dppActTextBottom') == 1) echo '<span class="txt">'.plxPlugin::getParam('dppTextBottom').'</span>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<div style="clear:both"></div></div>'.PHP_EOL;
	}

}

?>
