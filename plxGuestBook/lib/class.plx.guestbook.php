<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
 
class guestbook {

	public $plxRecord_gb = null; # Objet du livre d'or
	public $plxGlob_gb = null; # Objet plxGlob du livre d'or
	
	public function __construct() {
	
		$this->pathname = PLX_ROOT.PLX_CONFIG_PATH.'plugins/gbook/';
		 
	}

	public function getGuestBook($ordre='sort',$type='all',$start=0,$limite=false) {

		# On recupère les fichiers des Messages
		$this->plxGlob_gb = $this->getDirectoryTree($this->pathname,'xml',$ordre,$type,$start,$limite);
		$aFiles = $this->plxGlob_gb;
		if($aFiles) { # On a des fichiers
			foreach($aFiles as $k=>$v) # On parcourt tous les fichiers
				$array[ $k ] = $this->parseGuestBook($this->pathname.$v);
				# On stocke les enregistrements dans un objet plxRecord				
				$this->plxRecord_gb = new plxRecord($array);
			return true;
		}
		else return false;
	}
	
	public function parseGuestBook($filename) {

		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		# Recuperation des valeurs de nos champs XML
		$gb['author'] = plxUtils::getValue($values[ $iTags['author'][0]]['value']);
		$gb['ip'] = plxUtils::getValue($values[$iTags['ip'][0]]['value']);
		$gb['mail'] = plxUtils::getValue($values[$iTags['mail'][0]]['value']);
		$gb['actmail'] = plxUtils::getValue($values[$iTags['actmail'][0]]['value']);		
		$gb['site'] = plxUtils::getValue($values[$iTags['site'][0]]['value']);
		$gb['content'] = trim($values[ $iTags['content'][0] ]['value']);
		# Informations obtenues en analysant le nom du fichier
		$tmp1 = substr((basename($filename)),-11,-7);
		if (substr((basename($filename)),0,1) == '_') {
		$tmp = substr((basename($filename)),1,12);
		$status = 'offline';
		} else {
		$tmp = substr((basename($filename)),0,12);
		$status = 'online';
		}
		$gb['nbnote'] = $tmp1;
		$gb['status'] = $status;
		$gb['date'] = $tmp;
		# On retourne le tableau
		return $gb;
	}
	
	public function addGuestBook($content) {
		# On genere le contenu de notre fichier XML
		$xml = "<?xml version='1.0' encoding='".PLX_CHARSET."'?>\n";
		$xml .= "<comment>\n";
		$xml .= "\t<author><![CDATA[".plxUtils::cdataCheck($content['author'])."]]></author>\n";
		$xml .= "\t<ip>".$content['ip']."</ip>\n";
		$xml .= "\t<mail><![CDATA[".plxUtils::cdataCheck($content['mail'])."]]></mail>\n";
		$xml .= "\t<actmail><![CDATA[".plxUtils::cdataCheck($content['actmail'])."]]></actmail>\n";		
		$xml .= "\t<site><![CDATA[".plxUtils::cdataCheck($content['site'])."]]></site>\n";
		$xml .= "\t<content><![CDATA[".plxUtils::cdataCheck($content['content'])."]]></content>\n";
		$xml .= "</comment>\n";
		# On ecrit ce contenu dans notre fichier XML
		$filename = $content['id'].'_gb.xml';
		return plxUtils::write($xml, $this->pathname.$filename);
	}

	public function modGuestBook(&$id, $mod) {

		# Génération du nom du fichier
		$oldfilename = $this->pathname.$id.'_gb.xml';
		if(!file_exists($oldfilename)) # message inexistante
			return plxMsg::Error(L_ERR_UNKNOWN_COMMENT);
		# Valider
		if ($mod == 'online')
			$id=substr($id,-17);
		# Modérer 
		if($mod=='offline')
		    if (substr($id,0,1)!='_')
			$id = '_'.$id;
		# Génération du nouveau nom de fichier
		$newfilename = $this->pathname.$id.'_gb.xml';
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

	public function editGuestbook($content, &$id) {

		# Vérification de la validité de la date de publication
		if(!plxDate::checkDate($content['day'],$content['month'],$content['year'],$content['time']))
			return plxMsg::Error(L_ERR_INVALID_PUBLISHING_DATE);

		$comment=array();
		# Génération du nom du fichier
		$comment['filename'] = $id.'_gb.xml';
		if(!file_exists($this->pathname.$comment['filename'])) # Message inexistant
			return plxMsg::Error(L_ERR_UNKNOWN_COMMENT);
		# Controle des saisies
		if(trim($content['mail'])!='' AND !plxUtils::checkMail(trim($content['mail'])))
			return plxMsg::Error(L_ERR_INVALID_EMAIL);
		if(trim($content['site'])!='' AND !plxUtils::checkSite($content['site']))
			return plxMsg::Error(L_ERR_INVALID_SITE);
		# On récupère les infos du Message
		$com = $this->parseGuestbook($this->pathname.$comment['filename']);
		# Formatage des données
		$comment['author'] = plxUtils::strCheck(trim($content['author']));
		$comment['site'] = plxUtils::strCheck(trim($content['site']));
		$comment['ip'] = $com['ip'];
		$comment['mail'] = $content['mail'];
		 if (isset($content['actmail'])) { $comment['actmail'] = 'on'; } else { $comment['actmail'] = 'off'; }
		$comment['content'] = $content['content'];		
		# Génération du nouveau nom du fichier
		$time = explode(':', $content['time']);
		if (substr($id, 0, 1) == '_') { $first = '_'; } else { $first = ''; }
		$idnew = $first.$content['year'].$content['month'].$content['day'].$time[0].$time[1].substr($id, -5);
		# Suppression de l'ancien Message
		$this->delGuestBook($id);
		# Création du nouveau Message
		$comment['id'] = $idnew;
		if($this->addGuestBook($comment)) { 
		    $_POST['idGB'] = $idnew;
			return plxMsg::Info(L_COMMENT_SAVE_SUCCESSFUL);
		} else {
			return plxMsg::Error(L_COMMENT_UPDATE_ERR);
		}	
	}

	
	public function gbDate($format='#day #num_day #month #num_year(4) &agrave; #hour:#minute') {

		echo plxDate::formatDate($this->plxRecord_gb->f('date'),$format);
	}	

	public function gbAuthor($type='') {

		# Initialisation de nos variables interne
		$author = $this->plxRecord_gb->f('author');
		$email = $this->split_email($this->plxRecord_gb->f('mail'));
		if($type == 'mailto') # Type MailTo
		echo '<span class="email"><span class="n">'.$author.'</span><span class="u">'.$email['user'].'</span>(arobase)<span class="h">'.$email['host'].'</span>(point)<span class="d">'.$email['domain'].'</span></span>';
		else # Type normal
			echo '<span class="email">'.$author.'</span>';
	}	
	
	public function gbSite($type='') {

		# Initialisation de nos variables interne
		$site = $this->plxRecord_gb->f('site');
		if($type == 'link' AND $site != '') # Type lien
			echo '<a href="'.$site.'" title="'.$site.'">'.$site.'</a>';
		else # Type normal
			echo $site;
	}	

	public function gbContent() {

		echo nl2br($this->plxRecord_gb->f('content'));
	}
	
	public function nbGuestBook($select='online', $publi='all') {

		$nb = 0;
		if($select == 'all')
			$motif = '/[^[:punct:]?][0-9]{4}.(.*).xml$/';
		elseif($select=='offline')
			$motif = '/^_[0-9]{4}.(.*).xml$/';
		elseif($select=='online')
			$motif = '/^[0-9]{4}.(.*).xml$/';
		else
			$motif = $select;

		if($coms = $this->getDirectoryTree($this->pathname,'xml','sort',$select))
			$nb = sizeof($coms);

		return $nb;
	}

	public function delGuestBook($id) {

		# Génération du nom du fichier
		$filename = $this->pathname.$id.'_gb.xml';
		# Suppression du Message
		if(file_exists($filename)) {
			unlink($filename);
		}
		
		if(!file_exists($filename))
			return plxMsg::Info(L_COMMENT_DELETE_SUCCESSFUL);
		else
			return plxMsg::Error(L_COMMENT_DELETE_ERR);
	}

	public function getPagegb() {

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
	
	public function getPagegbfront() {

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
	
	public function notificationgb() {
	
		if ($this->nbGuestBook('offline') != 0) {
		$nb =' ['.$this->nbGuestBook('offline').']';
		} else {
		$nb = '';
		}
		return $nb;
	}
	
	public function getDirectoryTree($Dir,$ext,$ordre='sort',$type='all',$depart='0',$limite=false){ 
		if ($ordre == 'sort') {
			$dirs = @array_diff(scandir($Dir), Array( ".", ".." )); 
		} else {
			$dirs = @array_diff(scandir($Dir,1), Array( ".", ".." )); 
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
	
	 public function nextIdGB() {
		$idGB = $this->getDirectoryTree($this->pathname,'xml','rsort','all');
			if($idGB) { # On a des fichiers
			foreach($idGB as $k=>$v) # On parcourt tous les fichiers
				$array[ $k ] = substr((basename($this->pathname.$v)),-11,-7);
				rsort($array);
			return str_pad($array['0']+1, 4, "0", STR_PAD_LEFT);
			#return $array['0']+1;
		}
		else return '0001';
	}	
	
	public function Pagination($nbGBPagination,$bypage) {
	
		$plxMotor = plxMotor::getInstance();
		$this->getPagegbFront();
	
		# Calcul des pages
		$last_page = ceil($nbGBPagination/$bypage);
		if($this->page > $last_page) $this->page = $last_page;
		$prev_page = $this->page - 1;
		$next_page = $this->page + 1;
		# Generation des URLs
		$f_url = $plxMotor->urlRewrite('index.php?guestbook&page=1'); # Premiere page
		$p_url = $plxMotor->urlRewrite('index.php?guestbook&page='.$prev_page); # Page precedente
		$n_url = $plxMotor->urlRewrite('index.php?guestbook&page='.$next_page); # Page suivante	
		$l_url = $plxMotor->urlRewrite('index.php?guestbook&page='.$last_page); # Derniere page	
		# On effectue l'affichage
		if($this->page > 2) # Si la page active > 2 on affiche un lien 1ere page
			echo '<span class="p_first"><a href="'.$f_url.'" title="'.ucfirst(L_PAGINATION_FIRST_TITLE).'">'.ucfirst(L_PAGINATION_FIRST).'</a>&nbsp;</span>';
		if($this->page > 1) # Si la page active > 1 on affiche un lien page precedente
			echo '<span class="p_prev"><a href="'.$p_url.'" title="'.ucfirst(L_PAGINATION_PREVIOUS_TITLE).'">'.ucfirst(L_PAGINATION_PREVIOUS).'</a>&nbsp;</span>';
		# Affichage de la page courante
		printf('<span class="p_page">'.ucfirst(L_PAGINATION).'</span>',$this->page,$last_page);
		if($this->page < $last_page) # Si la page active < derniere page on affiche un lien page suivante
			echo '<span class="p_next">&nbsp;<a href="'.$n_url.'" title="'.ucfirst(L_PAGINATION_NEXT_TITLE).'">'.ucfirst(L_PAGINATION_NEXT).'</a></span>';
		if(($this->page + 1) < $last_page) # Si la page active++ < derniere page on affiche un lien derniere page
			echo '<span class="p_last">&nbsp;<a href="'.$l_url.'" title="'.ucfirst(L_PAGINATION_LAST_TITLE).'">'.ucfirst(L_PAGINATION_LAST).'</a></span>';
	}

	public function RedirGB($url){
		$plxMotor = plxMotor::getInstance();
	    $r_url = $plxMotor->urlRewrite($url);
		echo "<script type=\"text/javascript\">setTimeout('window.location=\"$r_url\";',3000);</script>";
	}
	
	public function email_encode($string) {
	
		$ret_string="";
		$len=strlen($string);
		for($x=0;$x<$len;$x++)
			{
				$ord=ord(substr($string,$x,1));
				$ret_string.="&#$ord;";
			}
		return $ret_string;
    }	
	public function split_email($mail) {
		$split = array();
		$p1 = strpos($mail,'@');
		$user = substr($mail,0,$p1);
		$tmp = substr($mail,$p1+1);
		$p2 = strpos($tmp,'.');
		$host = substr($tmp,0,$p2);
		$domain = substr($tmp,$p2+1);
		$split['user'] = $user;
		$split['host'] = $host;
		$split['domain'] = $domain;
		return $split;
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