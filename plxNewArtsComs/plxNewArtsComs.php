<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/	
	
include(dirname(__FILE__).'/lib/class.plx.newartscoms.php');
	
class plxNewArtsComs extends plxPlugin {

  # param	default_lang	langue par défaut utilisée par PluXml
  public function __construct($default_lang) {

      # appel du constructeur de la classe plxPlugin (obligatoire)
      parent::__construct($default_lang);

  		# droits pour accéder à la page config.php du plugin
  		$this->setConfigProfil(PROFIL_ADMIN);
		
		# Vérification des version du plugin
		$this->newartscoms = new newartscoms();  
		  
  		# Ajouts des hooks
  		$this->addHook('plxShowNewComs', 'plxShowNewComs');
  		$this->addHook('plxShowNewArts', 'plxShowNewArts');		
  }

	public function plxShowNewComs() {
		$plxShow = plxShow::getInstance();
		
		$newComs_Img = plxPlugin::getParam('newComs_Img');
		if (!empty($newComs_Img)) {
			$newComs_imgUrl = PLX_ROOT.$newComs_Img;
		} else {
			$newComs_imgUrl = PLX_PLUGINS.'plxNewArtsComs/icone/new/'.plxPlugin::getParam('newComs_Icone');
		}
		
		$newComs_Nbdays = plxPlugin::getParam('newComs_NbDays');
		$ndaycom = floor((strtotime(date('YmdHi')) - strtotime($plxShow->plxMotor->plxRecord_coms->f('date'))) / (60*60*24));
		
		if (plxPlugin::getParam('newComs_Active') == 1) {
			if($ndaycom < $newComs_Nbdays) {
				echo '<span><img src="'.$newComs_imgUrl.'" alt="" title="" /></span>'; 
			}
		}
	}

	public function plxShowNewArts() {
		$plxShow = plxShow::getInstance();
		
		$newArts_Img = plxPlugin::getParam('newArts_Img');
		$updArts_Img = plxPlugin::getParam('updArts_Img');
		if (!empty($newArts_Img)) {
			$newArts_imgUrl = PLX_ROOT.$newArts_Img;
		} else {
			$newArts_imgUrl = PLX_PLUGINS.'plxNewArtsComs/icone/new/'.plxPlugin::getParam('newArts_Icone');
		}
		if (!empty($updArts_Img)) {
			$updArts_imgUrl = PLX_ROOT.$updArts_Img;
		} else {
			$updArts_imgUrl = PLX_PLUGINS.'plxNewArtsComs/icone/update/'.plxPlugin::getParam('updArts_Icone');
		}

		$newArts_Nbdays = plxPlugin::getParam('newArts_NbDays');
		$updArts_Nbdays = plxPlugin::getParam('updArts_NbDays');
		$ndaypub = floor((strtotime(date('YmdHi')) - strtotime($plxShow->plxMotor->plxRecord_arts->f('date'))) / (60*60*24));
		$ndayupd = floor((strtotime(date('YmdHi')) - strtotime($plxShow->plxMotor->plxRecord_arts->f('date_update'))) / (60*60*24));
		
		if (plxPlugin::getParam('newArts_Active') == 1) {
			if($ndaypub < $newArts_Nbdays) {
				echo '<span><img src="'.$newArts_imgUrl.'" alt="" title="" /></span>'; 
			}
		}
		
		if (plxPlugin::getParam('updArts_Active') == 1) {
			if($ndaypub > $newArts_Nbdays AND $ndayupd < $updArts_Nbdays) {
				echo '<span><img src="'.$updArts_imgUrl.'" alt="" title="" /></span>'; 
			}
		}
	}
}

?>
