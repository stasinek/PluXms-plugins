<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

include(dirname(__FILE__).'/lib/class.plx.counters.php');  
  
class plxCounters extends plxPlugin {

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
		
		$this->counters = new counters();
		
		# ajout du hook
		$this->addHook('plxShowCounters', 'plxShowCounters');
		$this->addHook('plxShowConstruct', 'plxShowConstruct');		

	}

	/**
	 * Méthode appelée à l'activation du plugin pour créer le répertoire gbook
	 *
	 * @author	Stephane F
	 **/
	public function onActivate() {
		# Vérification de l'existence du dossier configuration/plugins/counter
		if(!is_dir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters')) {
			@mkdir(PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters',0755,true);
			
		# creation fichier vide visitortotal.txt (bug fix)
		if (!file_exists(PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/visitorstotal.txt')){
			plxUtils::write('', PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/visitorstotal.txt');
		}
		# Protection du répertoire counters
		plxUtils::write('', PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/index.html');
		if (!file_exists(PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/.htaccess')){
			file_put_contents(PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/.htaccess',"<Files *>
	Order allow,deny
	Deny from all
</Files>      
      ");
    }
		}	
	}
	
	/**
	 * Méthode de traitement du hook plxShowConstruct
	 *
	 * @return	NULL
	 * @author	DPFPIC
	 **/
	public function plxShowConstruct() {
		# infos sur la session
		#session_start();
		if ( $this->is_session_started() === FALSE ) session_start();
	}
	
	/**
	 * Méthode pour vérifier la pressence d'une session active
	 *
	 * @return	bool
	 * @author	DPFPIC
	 **/
	private function is_session_started() {
		if ( php_sapi_name() !== 'cli' ) {
			if ( version_compare(phpversion(), '5.4.0', '>=') ) {
				return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			} else {
				return session_id() === '' ? FALSE : TRUE;
			}
		}
    return FALSE;
	}	

	/**
	 * Méthode pour afficher les differents compteurs
	 *
	 * @return	NULL
	 * @author	DPFPIC
	 **/	
	public function plxShowCounters() {
		$plxShow = plxShow::getInstance();
		
		$time_visitor = plxPlugin::getParam('time_Visitor');
		$time_page = plxPlugin::getParam('time_Page');
		$nbAllArt = "<?php \$plxShow->nbAllArt('".$this->getLang('L_CT_NBARTICLE')." : <span class=\'nbr\'>".$this->getLang('L_CT_NONEARTICLE')."</span>','".$this->getLang('L_CT_NBARTICLE')." : <span class=\'nbr\'>#nb</span>','".$this->getLang('L_CT_NBARTICLE')." : <span class=\'nbr\'>#nb</span>') ?>";
		$nbAllCom = "<?php \$plxShow->nbAllCom('".$this->getLang('L_CT_NBCOMMENT')." : <span class=\'nbr\'>".$this->getLang('L_CT_NONECOMMENT')."</span>','".$this->getLang('L_CT_NBCOMMENT')." : <span class=\'nbr\'>#nb</span>','".$this->getLang('L_CT_NBCOMMENT')." : <span class=\'nbr\'>#nb</span>') ?>";
	    $online = sizeof($this->counters->OnlineVisitors());
		if ($online <= 1){$nbtemp = "";} else {$nbtemp = "s";}
		
		echo '<!-- Début plugin plxCounters -->'."\n";
		echo '<ul>'."\n";
		if (plxPlugin::getParam('totalView') == 1) echo '<li>'.$this->getLang("L_CT_TOTALV").' : <span class="nbr">'.$this->counters->TotalViews($time_page).'</span></li>'."\n";
		if (plxPlugin::getParam('todayView') == 1) echo '<li>'.$this->getLang("L_CT_TODAYV").' : <span class="nbr">'.$this->counters->TodayViews($time_page).'</span></li>'."\n";
		if (plxPlugin::getParam('connectVistor') == 1) echo '<li>'.$this->getLang("L_CT_VISTOR").$nbtemp.' '.$this->getLang("L_CT_CONNECT").$nbtemp.' : <span class="nbr">'.$this->counters->OnlineVisitors($time_visitor).'</span></li>'."\n";
		if (plxPlugin::getParam('numVistor') == 1) echo '<li>'.$this->getLang("L_CT_NBVISTOR").' : <span class="nbr">'.$this->counters->TotalVisitors().'</span></li>'."\n";
		if (plxPlugin::getParam('numArticle') == 1) echo '<li>'.$nbAllArt.'</li>'."\n";
		if (plxPlugin::getParam('numComment') == 1)echo '<li>'.$nbAllCom.'</li>'."\n";		
		echo '</ul>'."\n";
		echo '<!-- Fin plugin plxCounters -->'."\n";
		
		if (plxPlugin::getParam('numLogFile') == 1) $this->counters->MakeCsv();
	}
		
}
?>