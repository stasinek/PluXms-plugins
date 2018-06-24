<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
class counters {

    var $filename = null; # nom du fichier contenant les statistiques
	var $Fileprefix = 'log_';
    var $sessionTime; # durée d'une visite en minute avant expiration
	var $TimeStamp; # "AAAA-MM-JJ HH:MM:SS"
	var $Date;
    var $Hour;

    /**
     * Constructeur qui initialise certaines variables de classe
     * et qui lance les traitements de prise en compte des statistiques
     *
     * @param    null
     * @return    null
     * @author    Stephane F
     **/    
	public function __construct() {
		
		$this->viewstotal = PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/viewstotal.txt'; # emplacement du fichier des stats
		$this->viewstoday = PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/'; # emplacement du fichier des stats
		$this->visitorstotal = PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/visitorstotal.txt'; # emplacement du fichier des stats
		$this->online = PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/online.txt'; # emplacement du fichier des stats
		$this->csv = PLX_ROOT.PLX_CONFIG_PATH.'plugins/counters/'; # emplacement du fichier des stats		
    }
     
    #-------------------
    # Méthodes publiques
    #-------------------
    
    /**
     * Méthode qui affiche le compteur du nombre total de visiteurs
     *
     * @param    null
     * @return    Cpt
     * @author    DPFPIC
     **/    
    public function TotalVisitors() {
		### Compteur de visites ###
		if(!isset($_SESSION['VISITE'])) {$_SESSION['VISITE'] = "";}
		// si c'est le premier hit de la session
		if($_SESSION['VISITE'] == "")  {
			// marque la session
			$_SESSION['VISITE'] = "ok";
			// Incrémente le compteur
			$this->writecpt($this->visitorstotal);
		}
		// Lecture de la taille du fichier
		$Cpt = filesize($this->visitorstotal);
		if (empty($Cpt)) $Cpt = 1; #Si SESSION dejà initialisée (bug fix)
        return $Cpt;
    }
	
    /**
     * Méthode qui affiche le compteur des visiteurs en ligne
     *
     * @param    null
     * @return    online
     * @author    DPFPIC
     **/ 
	public function OnlineVisitors($time_visitor=15) {
		### Compteur de visiteurs en ligne ###
		
		$online = 0;
		$result = null;
		$temp = array();

		$Fnm = $this->online;

		// IP du visiteur
		$IP=$_SERVER["REMOTE_ADDR"];

		// Date/heure courante en minutes
		$NowDate = time()/60;
		// Durée de vie max en min
		$sessionTime = $time_visitor;

		// Si le fichier existe, on le lit
		if (file_exists($Fnm)) {
			$inF = fopen($Fnm,"r");
			while (!feof($inF)) {
				// on lit chaque IP|minutes
				$ligne=fgets($inF, 4096);
				$temp = explode("|",$ligne);
				// différente de l'IP courante ?
					if($temp[0]!=$IP AND (!empty($temp[0]))) {
						if($NowDate-intVal($temp[1])<=$sessionTime) {
							$online++;
							if (!empty($ligne)) $result .= $ligne; # (bug fix) ligne vide dans fichier
						}
					}
				}
		fclose($inF);
		}
	// On ajoute le hit
	$result .= $IP . "|" . $NowDate . "\n";
	$online++;
	// Et on sauve
	$inF = fopen($Fnm,"w");
	fputs($inF,$result);
	fclose($inF);
	return $online;
	}
	
    /**
     * Méthode qui affiche le compteur du nombre de pages vues aujourd'hui
     *
     * @param    null
     * @return    Cpt
     * @author    DPFPIC
     **/
    public function TotalViews($time_page=30) {
		### Compteur de page vue total ###
		// Incrémente le compteur
		if (!isset($_SESSION['VIEWSTOTAL'])) {
			$_SESSION['VIEWSTOTAL'] = time()/60;
			$this->writecpt($this->viewstotal);
		} else if ((time()/60) - $_SESSION['VIEWSTOTAL'] > $time_page) {
			// la session a commencée il y a plus de 30 minutes
			unset($_SESSION['VIEWSTOTAL']);  // RAZ session
		}
		// Lecture de la taille du fichier
		$Cpt = filesize($this->viewstotal);
        return $Cpt;
    }

    /**
     * Méthode qui affiche le compteur du nombre total de pages vues
     *
     * @param    null
     * @return    Cpt
     * @author    DPFPIC
     **/    
    public function TodayViews($time_page=30) {
		### Compteur de page vue par jour ###
		$dateToday = date('dmY');
		$filepath = $this->viewstoday.$dateToday.'.txt';		
		// Incrémente le compteur
		if (!isset($_SESSION['VIEWSTODAY'])) {
			$_SESSION['VIEWSTODAY'] = time()/60;
			$this->writecpt($filepath);
		} else if ((time()/60) - $_SESSION['VIEWSTODAY'] > $time_page) {
			// la session a commencée il y a plus de 30 minutes
			unset($_SESSION['VIEWSTODAY']);  // RAZ session
		}
		// Lecture de la taille du fichier
		$Cpt = filesize($filepath);	
        return $Cpt;
    } 

    /**
     * Méthode qui genére un fichier csv des visiteurs
     *
     * @param    null
     * @return    null
     * @author    DPFPIC
     **/ 	
	public function MakeCsv() {
		
		// Today date. To be defined only once to avoid bug at around midnight
		$this->TimeStamp = date('Y-m-d H:i:s');
		list($this->Date,$this->Hour) = explode(" ",$this->TimeStamp);
		// log visitor only if file is writable
		$filecsv = $this->csv . $this->Fileprefix . $this->Date . '.csv';
		$handle = fopen($filecsv,'a');
		if ($handle!==false){
        // Log the visitor in the current day log file
			fputcsv($handle,
			    array(  $this->Date,
                           $this->Hour,
                           plxUtils::getIp(),
                           htmlspecialchars((isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '',ENT_IGNORE) . ((!empty($_POST["name"]) && !empty($_POST["content"]) && !empty($_POST["site"]))?'#com':''),
                           htmlspecialchars((isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '',ENT_IGNORE),
                           htmlspecialchars((isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '',ENT_IGNORE)
                        ),
                ';','"');
			fclose($handle);
		} //if ($handle!==false)
	}

    /**
     * Méthode qui imcrémante le compteur des différents fichiers
     *
     * @param    null
     * @return    null
     * @author    DPFPIC
     **/ 
	public function writecpt($filename) {

		if(file_exists($filename)) {
			$f = fopen($filename, 'a'); # On ouvre le fichier
			fputs($f,"."); # On écrit
			fclose($f); # On ferme
		} else {
			$f = fopen($filename, 'w'); # On ouvre le fichier
			fputs($f,"."); # On écrit
			fclose($f); # On ferme
		}
		# On place les bons droits
		#chmod($filename,0644);
	}
	
	/**
	 * Verification des version des plugins
	 *
	 * @param	plugin				le nom du plugin
	 * @return	array()
	 * @author	DPFPIC
	 **/
	public function UpdatePlugin($plugin) {
	    $array = array();
		$url = 'http://dpfpic.com/plugins.rep';
		$filename = PLX_PLUGINS.$plugin.'/infos.xml';
		
		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create('UTF-8');
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$strings,$iTags);
		xml_parser_free($parser);
		# Récupération des données xml
		$array['actualversion'] = plxUtils::getValue($strings[$iTags['version'][0]]['value']);
		$array['actualdate'] = plxUtils::getValue($strings[$iTags['date'][0]]['value']);


		if (ini_get('allow_url_fopen')) {
			$handleplugin = @file($url);
		}
		elseif ($curl = @curl_init()){
			$timeout = 5; // set to zero for no timeout
			curl_setopt ($curl, CURLOPT_URL, $url);
			curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
			$version_content = curl_exec($curl);
			curl_close($curl);
			if ($version_content != false) {
				$handleplugin = explode("\n", $version_content);
			}
		}	
		if ($handleplugin) {
			$nb = sizeof($handleplugin);
			for($i=0;$i<$nb;$i++) {
				$seach = strstr($handleplugin[$i], '||', true);
				if ($seach == $plugin) {
				    $value = explode('||', $handleplugin[$i]);
					$array['newplugin'] = $value[0];
					$array['newversion'] = $value[1];
					$array['newdate'] = $value[2];
					$array['newurl'] = $value[3];
					$array['active'] = $value[4];
				}
			}
		} else {
			$array ['status'] = 0;
			return $array;
		}
		
		$adate = explode("/", $array['actualdate']); 
		$ndate = explode("/", $array['newdate']);
		$actualdate = $adate[2].$adate[1].$adate[0];
		$newdate = $ndate[2].$ndate[1].$ndate[0];
		
		if (isset($array['actualversion']) AND ($array['actualversion'] == $array['newversion']  AND  $actualdate == $newdate) AND $array['active'] == 1 AND $array['newplugin'] == $plugin) {
			$array ['status'] = 1;
			return $array;
		} elseif (isset($array['actualversion']) AND ($array['actualversion'] != $array['newversion'] OR  $actualdate != $newdate) AND $array['active'] == 1 AND $array['newplugin'] == $plugin) {
			$array ['status'] = 2;
			return $array;
		} elseif (isset($array['active']) AND $array['active'] == 0) {
			$array ['status'] = 3;
			return $array;
		} else {
		    $array ['status'] = 0;
			return $array;
		}
	}		
}

?>