<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
 
class bonachat {

	public $plxRecord_ba = null; # Objet du non d'achat
	public $plxGlob_ba = null; # Objet plxGlob du bon d'achat
	
	public function __construct() {
	
		$this->pathname = PLX_ROOT.PLX_CONFIG_PATH.'plugins/bachat/';
		 
	}

	public function getBonAchat($ordre='sort',$type='all',$start=0,$limite=false) {

		# On recupère les fichiers des Messages
		$this->plxGlob_ba = $this->getDirectoryTree($this->pathname,'xml',$ordre,$type,$start,$limite);
		$aFiles = $this->plxGlob_ba;
		if($aFiles) { # On a des fichiers
			foreach($aFiles as $k=>$v) # On parcourt tous les fichiers
				$array[ $k ] = $this->parseBonAchat($this->pathname.$v);
				# On stocke les enregistrements dans un objet plxRecord				
				$this->plxRecord_ba = new plxRecord($array);
			return true;
		}
		else return false;
	}
	
	public function parseBonAchat($filename) {

		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		# Recuperation des valeurs de nos champs XML
		$ba['idcode'] = trim($values[ $iTags['idcode'][0] ]['value']);
		$ba['expiration_date'] = plxUtils::getValue($values[$iTags['expiration_date'][0]]['value']);		
		$ba['price'] = plxUtils::getValue($values[$iTags['price'][0]]['value']);		
		$ba['title'] = plxUtils::getValue($values[ $iTags['title'][0]]['value']);
		$ba['name'] = plxUtils::getValue($values[$iTags['name'][0]]['value']);
		$ba['firstname'] = plxUtils::getValue($values[$iTags['firstname'][0]]['value']);
		$ba['address'] = plxUtils::getValue($values[$iTags['address'][0]]['value']);		
		$ba['zipcode'] = plxUtils::getValue($values[$iTags['zipcode'][0]]['value']);
		$ba['city'] = trim($values[ $iTags['city'][0] ]['value']);
		$ba['phone'] = trim($values[ $iTags['phone'][0] ]['value']);
		$ba['email'] = trim($values[ $iTags['email'][0] ]['value']);
		$ba['country'] = trim($values[ $iTags['country'][0] ]['value']);
		$ba['recipient_title'] = trim($values[ $iTags['recipient_title'][0] ]['value']);
		$ba['recipient_name'] = trim($values[ $iTags['recipient_name'][0] ]['value']);
		$ba['recipient_firstname'] = trim($values[ $iTags['recipient_firstname'][0] ]['value']);
		$ba['recipient_phone'] = trim($values[ $iTags['recipient_phone'][0] ]['value']);
		$ba['recipient_email'] = trim($values[ $iTags['recipient_email'][0] ]['value']);
		$ba['check_recipient_email'] = plxUtils::getValue($values[ $iTags['check_recipient_email'][0] ]['value']);		
		# Informations obtenues en analysant le nom du fichier
		$tmp1 = substr((basename($filename)),-11,-7);
		$tmp = substr((basename($filename)),0,12);
		$ba['nbnote'] = $tmp1;
		$ba['date'] = $tmp;
		# On retourne le tableau
		return $ba;
	}
	
	public function addBonAchat($record) {
		# On genere le contenu de notre fichier XML
		$xml = "<?xml version='1.0' encoding='".PLX_CHARSET."'?>\n";
		$xml .= "<record>\n";
		$xml .= "\t<idcode><![CDATA[".plxUtils::cdataCheck($record['idcode'])."]]></idcode>\n";		
		$xml .= "\t<expiration_date><![CDATA[".plxUtils::cdataCheck($record['expiration_date'])."]]></expiration_date>\n";		
		$xml .= "\t<price><![CDATA[".plxUtils::cdataCheck($record['price'])."]]></price>\n";			
		$xml .= "\t<title><![CDATA[".plxUtils::cdataCheck($record['title'])."]]></title>\n";
		$xml .= "\t<name><![CDATA[".plxUtils::cdataCheck($record['name'])."]]></name>\n";
		$xml .= "\t<firstname><![CDATA[".plxUtils::cdataCheck($record['firstname'])."]]></firstname>\n";
		$xml .= "\t<address><![CDATA[".plxUtils::cdataCheck($record['address'])."]]></address>\n";		
		$xml .= "\t<zipcode><![CDATA[".plxUtils::cdataCheck($record['zipcode'])."]]></zipcode>\n";
		$xml .= "\t<city><![CDATA[".plxUtils::cdataCheck($record['city'])."]]></city>\n";
		$xml .= "\t<phone><![CDATA[".plxUtils::cdataCheck($record['phone'])."]]></phone>\n";
		$xml .= "\t<email><![CDATA[".plxUtils::cdataCheck($record['email'])."]]></email>\n";	
		$xml .= "\t<country><![CDATA[".plxUtils::cdataCheck($record['country'])."]]></country>\n";
		$xml .= "\t<recipient_title><![CDATA[".plxUtils::cdataCheck($record['recipient_title'])."]]></recipient_title>\n";
		$xml .= "\t<recipient_name><![CDATA[".plxUtils::cdataCheck($record['recipient_name'])."]]></recipient_name>\n";
		$xml .= "\t<recipient_firstname><![CDATA[".plxUtils::cdataCheck($record['recipient_firstname'])."]]></recipient_firstname>\n";
		$xml .= "\t<recipient_phone><![CDATA[".plxUtils::cdataCheck($record['recipient_phone'])."]]></recipient_phone>\n";
		$xml .= "\t<recipient_email><![CDATA[".plxUtils::cdataCheck($record['recipient_email'])."]]></recipient_email>\n";		
		$xml .= "\t<check_recipient_email><![CDATA[".plxUtils::cdataCheck($record['check_recipient_email'])."]]></check_recipient_email>\n";		
		$xml .= "</record>\n";
		# On ecrit ce contenu dans notre fichier XML
		$filename = $record['id'].'_ba.xml';
		return plxUtils::write($xml, $this->pathname.$filename);
	}

	public function modBonAchat(&$id, $mod) {

		# Génération du nom du fichier
		$oldfilename = $this->pathname.$id.'_ba.xml';
		if(!file_exists($oldfilename)) # message inexistante
			return plxMsg::Error(L_ERR_UNKNOWN_COMMENT);
		# Génération du nouveau nom de fichier
		$newfilename = $this->pathname.$id.'_ba.xml';
		# On renomme le fichier
		@rename($oldfilename,$newfilename);
		# Contrôle
		if(is_readable($newfilename)) {
			if($type == 'online')
				return plxMsg::Info(L_COMMENT_VALIDATE_SUCCESSFUL);
			else
				return plxMsg::Info(L_COMMENT_MODERATE_SUCCESSFUL);
		} else {
			if($type == 'online')
				return plxMsg::Error(L_COMMENT_VALIDATE_ERR);
			else
				return plxMsg::Error(L_COMMENT_MODERATE_ERR);
		}
	}

	public function editBonAchat($record, &$id) {

		$comment=array();
		# Génération du nom du fichier
		$comment['filename'] = $id.'_ba.xml';
		if(!file_exists($this->pathname.$comment['filename'])) # Message inexistant
			return plxMsg::Error(L_ERR_UNKNOWN_COMMENT);
		# Controle des saisies
		if(trim($record['email'])!='' AND !plxUtils::checkMail(trim($record['email'])))
			return plxMsg::Error(L_ERR_INVALID_EMAIL);
		# On récupère les infos du Message
		$com = $this->parseBonAchat($this->pathname.$comment['filename']);
		# Formatage des données
		$comment['idcode'] = plxUtils::strCheck($record['idcode']);	
		$comment['expiration_date'] = plxUtils::strCheck($record['expiration_date']);	
		$comment['price'] = plxUtils::strCheck($record['price']);
		$comment['title'] = plxUtils::strCheck(trim($record['title']));		
		$comment['name'] = plxUtils::strCheck(trim($record['name']));
		$comment['firstname'] = plxUtils::strCheck(trim($record['firstname']));
		$comment['address'] = plxUtils::strCheck(trim($record['address']));	
		$comment['zipcode'] = plxUtils::strCheck(trim($record['zipcode']));
		$comment['city'] = plxUtils::strCheck(trim($record['city']));		
		$comment['phone'] = plxUtils::strCheck(trim($record['phone']));
		$comment['email'] = plxUtils::strCheck(trim($record['email']));
		$comment['country'] = plxUtils::strCheck(trim($record['country']));	
		$comment['recipient_title'] = plxUtils::strCheck(trim($record['recipient_title']));		
		$comment['recipient_name'] = plxUtils::strCheck(trim($record['recipient_name']));
		$comment['recipient_firstname'] = plxUtils::strCheck(trim($record['recipient_firstname']));
		$comment['recipient_phone'] = plxUtils::strCheck(trim($record['recipient_phone']));
		$comment['recipient_email'] = plxUtils::strCheck(trim($record['recipient_email']));	
		$comment['check_recipient_email'] = plxUtils::strCheck($record['check_recipient_email']);
		$idnew = $id;
		# Suppression de l'ancien Message
		$this->delBonAchat($id);
		# Création du nouveau Message
		$comment['id'] = $idnew;
		if($this->addBonAchat($comment)) { 
		    $_POST['idBA'] = $idnew;
			return plxMsg::Info(L_COMMENT_SAVE_SUCCESSFUL);
		} else {
			return plxMsg::Error(L_COMMENT_UPDATE_ERR);
		}	
	}

	public function sendMailBA($name, $from, $to, $subject, $content, $file, $attachment) { 
	
		$attachment = chunk_split(base64_encode($attachment));    

		$boundary = "-----=".md5(uniqid(rand()));
		$header  = "From: ".$name." <".$from.">\r\n";
		$header .= "Reply-To: ".$from."\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
		$header .= "\r\n";

		$msg = "MIME 1.0 multipart/mixed.\r\n";
		$msg .= "--$boundary\r\n";
		$msg .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
		$msg .= "Content-Transfer-Encoding:8bit\r\n";
		$msg .= "\r\n";
		$msg .= $content."\r\n";
		$msg .= "\r\n";
		$msg .= "--$boundary\r\n";
		$msg .= "Content-Type: application/octet-stream; name=\"$file\"\r\n";
		$msg .= "Content-Transfer-Encoding: base64\r\n";
		$msg .= "Content-Disposition: attachment\r\n";
		$msg .= "\r\n";
		$msg .= $attachment . "\r\n";
		$msg .= "\r\n\r\n";
		$msg .= "--$boundary--\r\n";

		return mail($to, $subject, $msg, $header);
	}
	
	public function nbBonAchat() {

		$nb = 0;
		
		if($coms = $this->getDirectoryTree($this->pathname,'xml','sort'))
			$nb = sizeof($coms);

		return $nb;
	}

	public function delBonAchat($id) {

		# Génération du nom du fichier
		$filename = $this->pathname.$id.'_ba.xml';
		# Suppression du Message
		if(file_exists($filename)) {
			unlink($filename);
		}
		
		if(!file_exists($filename))
			return plxMsg::Info(L_COMMENT_DELETE_SUCCESSFUL);
		else
			return plxMsg::Error(L_COMMENT_DELETE_ERR);
	}

	public function getPageba() {

		# Initialisation
		$pageName = basename($_SERVER['PHP_SELF']);
		$savePage = preg_match('/admin\/(index|plugin).php/', $_SERVER['PHP_SELF']);
		# On check pour avoir le numero de page
		if(!empty($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] > 0)
			$this->page = $_GET['page'];
		elseif($savePage) {
			if(!empty($_POST['sel_cat']))
				$this->page = 1;
			else
				$this->page = !empty($_SESSION['page'][$pageName])?intval($_SESSION['page'][$pageName]):1;
		}
		# On sauvegarde
		if($savePage) $_SESSION['page'][$pageName] = $this->page;
	}	
	
	public function getPagebafront() {

		# Initialisation
		$pageName = basename($_SERVER['PHP_SELF']);
		$savePage = preg_match('/\/(index|plugin).php/', $_SERVER['PHP_SELF']);
		# On check pour avoir le numero de page
		if(!empty($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] > 0)
			$this->page = $_GET['page'];
		elseif($savePage) {
			if(empty($_GET['page']))
				$this->page = 1;
			else
				$this->page = !empty($_SESSION['page'][$pageName])?intval($_SESSION['page'][$pageName]):1;
		}
		# On sauvegarde
		if($savePage) $_SESSION['page'][$pageName] = $this->page;
	}	
	
	// Génération d'une chaine aléatoire
	public function idCode($nb_car, $chaine = 'AZERTYQSDFGHJKCVBN123456789') {
		$nb_lettres = strlen($chaine) - 1;
		$generation = '';
		for($i=0; $i < $nb_car; $i++) {
			$pos = mt_rand(0, $nb_lettres);
			$car = $chaine[$pos];
			$generation .= $car;
		}
		return $generation;
	}
	
	public function getDirectoryTree($Dir,$ext,$ordre='sort',$type='all',$depart='0',$limite=false){ 
		if ($ordre == 'sort') {
			$dirs = array_diff(scandir($Dir), Array( ".", ".." )); 
		} else {
			$dirs = array_diff(scandir($Dir,1), Array( ".", ".." )); 
		}
		$dir_array = Array(); 
		foreach($dirs as $d){ 
			if(is_dir($Dir.$d)){ 
				$dir_array[$d] = getDirectoryTree( $Dir."/".$d , $ext); 
			}else{ 
			if (($ext)?preg_match('/'.$ext.'/',$d):1)
			    if ($type == 'all') {
					$dir_array[] = $d; 
				} elseif ($type == 'online') {
					if (substr($d,0,1) != '_') {
						$dir_array[] = $d;
					}	
				} elseif ($type == 'offline') {
					if (substr($d,0,1) == '_') {
						$dir_array[] = $d;
					}
				} else { 
					if (substr($d,0,strlen($type)) == $type ) {
						$dir_array[] = $d;
					}
					}
				}		
            } 
		
		# On a une limite, on coupe le tableau
		if($limite)
			$dir_array = array_slice($dir_array,$depart,$limite);
		# On retourne le tableau
		return $dir_array; 
	}
	
	 public function nextIdBA() {
		$idBA = $this->getDirectoryTree($this->pathname,'xml','rsort','all');
			if($idBA) { # On a des fichiers
			foreach($idBA as $k=>$v) # On parcourt tous les fichiers
				$array[ $k ] = substr((basename($this->pathname.$v)),-11,-7);
				rsort($array);
			return str_pad($array['0']+1, 4, "0", STR_PAD_LEFT);
			#return $array['0']+1;
		}
		else return '0001';
	}	
	
	public function Pagination($nbBAPagination,$bypage) {
	
		$plxMotor = plxMotor::getInstance();
		$this->getPagebaFront();
	
		# Calcul des pages
		$last_page = ceil($nbBAPagination/$bypage);
		if($this->page > $last_page) $this->page = $last_page;
		$prev_page = $this->page - 1;
		$next_page = $this->page + 1;
		# Generation des URLs
		$f_url = $plxMotor->urlRewrite('index.php?bonachat&page=1'); # Premiere page
		$p_url = $plxMotor->urlRewrite('index.php?bonachat&page='.$prev_page); # Page precedente
		$n_url = $plxMotor->urlRewrite('index.php?bonachat&page='.$next_page); # Page suivante	
		$l_url = $plxMotor->urlRewrite('index.php?bonachat&page='.$last_page); # Derniere page	
		# On effectue l'affichage
		if($this->page > 2) # Si la page active > 2 on affiche un lien 1ere page
			echo '<span class="p_first"><a href="'.$f_url.'" title="'.L_PAGINATION_FIRST_TITLE.'">'.L_PAGINATION_FIRST.'</a></span>';
		if($this->page > 1) # Si la page active > 1 on affiche un lien page precedente
			echo '<span class="p_prev"><a href="'.$p_url.'" title="'.L_PAGINATION_PREVIOUS_TITLE.'">'.L_PAGINATION_PREVIOUS.'</a></span>';
		# Affichage de la page courante
		printf('<span class="p_page">'.L_PAGINATION.'</span>',$this->page,$last_page);
		if($this->page < $last_page) # Si la page active < derniere page on affiche un lien page suivante
			echo '<span class="p_next"><a href="'.$n_url.'" title="'.L_PAGINATION_NEXT_TITLE.'">'.L_PAGINATION_NEXT.'</a></span>';
		if(($this->page + 1) < $last_page) # Si la page active++ < derniere page on affiche un lien derniere page
			echo '<span class="p_last"><a href="'.$l_url.'" title="'.L_PAGINATION_LAST_TITLE.'">'.L_PAGINATION_LAST.'</a></span>';
	}

	public function transform_vars_to_value($template, $vars) {
		$output = $template;
		foreach ($vars as $key => $value) {
			$tag_to_replace = "{{ $key }}";
			$output = str_replace($tag_to_replace, $value, $output);
		}
	return $output;
	}	
	
	public function generer_token($nom = '') {
		$token = uniqid(rand(), true);
		$_SESSION[$nom.'_token'] = $token;
		$_SESSION[$nom.'_token_time'] = time();
	return $token;
	}

	public function verifier_token($temps, $referer, $nom = '') {
	if(isset($_SESSION[$nom.'_token']) && isset($_SESSION[$nom.'_token_time']) && isset($_POST['token']))
		if($_SESSION[$nom.'_token'] == $_POST['token'])
			if($_SESSION[$nom.'_token_time'] >= (time() - $temps))
				if($_SERVER['HTTP_REFERER'] == $referer)
					return true;
	return false;
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