<?php
if (!defined('PLX_ROOT')) exit;
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * urlSite : http://dpfpic.com
 * Licence GNU_GPL
 * Sur la base du plugin ModerationList (Jormun)
 **/
include(dirname(__FILE__).'/lib/class.plx.spamblockcoms.php');
 
class plxSpamBlockComs extends plxPlugin {
	
  # Initialisation des variables
  public $whiteList = array();
  public $blackList = array();
  public $useWL = true;
  public $useBL = true;
  public $autoBLTimer = 30;
  public $saveMode = 1;
  public $titleSaveMode = NULL;
  public $forceModeration = false;
  public $adminText = false;
  public $spamPrefix = "~";
  public $autoAddBLIP = true;
  public $autoAddBLEemail = true;
  public $autoAddBLUrlsite = true;
  public $autoAddWLIP = true;
  public $autoAddWLEemail = true;
  public $autoAddWLUrlsite = true;
  public $senderFrom = NULL;
  public $senderTo = NULL;
  public $superVision = 0;
  public $SPAMTag = "***SPAM*** ";
  
	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {
	
		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# Autorisation d'acces à la configuration du plugin
		$this->setConfigProfil(PROFIL_ADMIN); 
		
		#Update plugin
		$this->spamblockcoms = new spamblockcoms();

		# Lecture de la config
		$this->whiteList = unserialize($this->getParam('whiteList'));
		$this->blackList = unserialize($this->getParam('blackList'));
    
		$this->useWL = $this->getParam('useWL');	
		$this->useBL = $this->getParam('useBL');
    
		$this->autoBLTimer = $this->getParam('autoBLTimer');
		$this->forceModeration = $this->getParam('forceModeration');
		$this->addAdminIcones = $this->getParam('addAdminIcones');
		$this->addAdminIcones = $this->getParam('addAdminIcones');
		$this->adminText = $this->getParam('adminText');
		$this->saveMode = (int) $this->getParam('saveMode');
    
		$this->autoAddBLIP = $this->getParam('autoAddBLIP');
		$this->autoAddBLEemail = $this->getParam('autoAddBLEemail');
		$this->autoAddBLUrlsite = $this->getParam('autoAddBLUrlsite');
		$this->autoAddWLIP = $this->getParam('autoAddWLIP');
		$this->autoAddWLEemail = $this->getParam('autoAddWLEemail');
		$this->autoAddWLUrlsite = $this->getParam('autoAddWLUrlsite');
    
		$this->SPAMTag = $this->getParam('SPAMTag');
	
		$this->superVision = $this->getParam('superVision');	
		$this->senderFrom = $this->getParam('senderFrom');
		$this->senderTo = $this->getParam('senderTo');

		# Ajout des hook
		if(defined('PLX_ADMIN')){//admin
			$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
			$this->addHook('AdminCommentsPrepend', 'AdminCommentsPrepend');
			$this->addHook('AdminCommentsTop', 'AdminCommentsTop');
			$this->addHook('AdminCommentTop', 'AdminCommentTop');
			$this->addHook('AdminCommentsPagination', 'AdminCommentsPagination');
		} else {//public
			$this->addHook('plxMotorAddCommentaire', 'plxMotorAddCommentaire');
			$this->addHook('plxMotorDemarrageNewCommentaire', 'plxMotorDemarrageNewCommentaire');
			# Utiliser une variable de session pour vérifier le délai entre 2 pages
			if ($this->autoBLTimer > 0 // seulement si fonction timer activé dans la config 
				AND !isset($_POST["name"]) AND !isset($_POST["content"]) && !isset($_POST["urlSite"]) // sauf si commentaire en cours de soumission
				AND ( empty($_SESSION['msgcom']) OR $_SESSION['msgcom'] == $this->getLang('L_SBC_COM_IN_MODERATION') OR $_SESSION['msgcom'] == $this->getLang('L_SBC_COM_IN_MODERATION'))) { // sauf si un message va etre affiché au visiteur (mais sauf si le message est celui du blacklist ou modération)
				$_SESSION["plxSpamBlockComsTime"] = time();
				$this->addHook('ThemeEndBody', 'ThemeEndBody');
       
			} elseif (isset($_SESSION["profil"]) && $_SESSION["profil"] < 3) {
				$this->addHook('ThemeEndBody', 'ThemeEndBody');
			}
		}
	}

	/**
	 * Méthode qui charge le code css nécessaire pour l'affichage admin
	 *
	 * @return	stdio
	 * @author	Stephane F, Dpfpic
	 **/		
    public function AdminTopEndHead() {
	   $string = "\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'plxSpamBlockComs/css/spamblockcoms.css" type="text/css" media="screen" />'.PHP_EOL;
	   echo $string;
	}	

	/**
	 * Méthode qui envoie un email à la création d'un nouveau commentaire
	 *
	 * @return	stdio
	 * @author	Stéphane F, Dpfpic
	 **/
	public function plxMotorDemarrageNewCommentaire() {
		if ($this->superVision) {
		$string = '
		if($retour[0]=="c" OR $retour=="mod") {
			$from = "'.$this->senderFrom.'";
			$to = "'.$this->senderTo.'";
			$eSubject = "'.$this->getLang("L_SBC_EMAIL_NEW_COMMENT").' ".$this->plxRecord_arts->f("title");
			$eBody  = "'.$this->getLang("L_SBC_EMAIL_NEW_COMMENT_BY").' <strong>".plxUtils::unSlash($_POST["name"])."</strong><br /><br />";
			$eBody .= plxUtils::unSlash($_POST["content"])."<br /><br />";
			$eBody .= "-----------<br />";
			$eBody .= "'.$this->getLang("L_SBC_EMAIL_READ_ONLINE").' : <a href=\"".$url."/#".$retour."\">".$this->plxRecord_arts->f("title")."</a>";
			$eBody .= "<br /><a href=\"".$this->racine."core/admin/comments.php\">'.$this->getLang("L_SBC_EMAIL_MANAGE_COMMENTS").'</a>";
			plxUtils::sendmail($this->aConf["title"], $from, $to, $eSubject, $eBody, "html");
		}';
		echo '<?php '.$string.' ?>';
		}
	}

	/**
	 * Méthode qui supprime le tag "***SPAM***"	 
	 *
	 * @return	stdio
	 **/
	public function cleanBlacklistTag($comid) {
		global $plxAdmin;
		$file = PLX_ROOT.$plxAdmin->aConf["racine_commentaires"].$comid.".xml";
		file_put_contents($file, str_replace($this->SPAMTag,"",file_get_contents($file)));
	}

	/**
	 * Méthode qui permet de mettre en SPAM un commentaire
	 **/
	public function SPAMCommentaire(&$id, $racineCom) {
		# Génération du nom du fichier
		$oldfilename = PLX_ROOT.$racineCom.$id.'.xml';
		if(!file_exists($oldfilename)) # Commentaire inexistant
			return plxMsg::Error(L_ERR_UNKNOWN_COMMENT);
		# Recherche id si comment modéré ou spam
		if(preg_match('/([[:punct:]]?)[0-9]{4}.[0-9]{10}-[0-9]+$/',$id,$capture)) {
			$id=str_replace($capture[1],'',$id);
		}
		$id = $this->spamPrefix.$id;
    
		# Génération du nouveau nom de fichier
		$newfilename = PLX_ROOT.$racineCom.$id.'.xml';
		# On renomme le fichier
		@rename($oldfilename,$newfilename);
		# Contrôle
		if(is_readable($newfilename)) {
			return plxMsg::Info($this->getLang('l_SBC_ADDSPAM'));
		} else {
			return plxMsg::Error(L_COMMENT_MODERATE_ERR);
		}
	}

	/**
	 * Méthode qui ajoute des éléments à la blakclist et les enleve de la whitelist si nécessaire	 
	 * Elements pouvant être enlevés : IP, email, url site	 	 
	 * 
	 * @return	stdio 	 
	 **/
	public function addToBlacklist($author,$ip,$email,$urlSite,$count,$datetime=false){
		include_once(PLX_CORE.'lib/class.plx.msg.php');
		$updated = false;
		$author = strtolower(trim($author));
		$email = strtolower(trim($email));
		$urlSite = strtolower(trim($urlSite));		
		if (!empty($author) AND !in_array($author,$this->blackList)){
			$this->blackList[] = $author;
			$updated = true;
			if (in_array($author,$this->whiteList))
				unset($this->whiteList[ array_search($author,$this->whiteList) ]);
		}		
		if (!empty($ip) AND $this->autoAddBLIP AND !in_array($ip,$this->blackList)){
			$this->blackList[] = $ip;
			$updated = true;
			if (in_array($ip,$this->whiteList))
				unset($this->whiteList[ array_search($ip,$this->whiteList) ]);
		}
		$email = strtolower($email);
		if (!empty($email) AND $this->autoAddBLEemail AND !in_array($email,$this->blackList)){
			$this->blackList[] = $email;
			$updated = true;
			if (in_array($email,$this->whiteList))
				unset($this->whiteList[ array_search($email,$this->whiteList) ]);
		}
		$urlSite = strtolower(trim($urlSite,'/'));
		if (!empty($urlSite) AND $this->autoAddBLUrlsite AND !in_array($urlSite,$this->blackList)){
			$this->blackList[] = $urlSite;
			$updated = true;
			if (in_array($urlSite,$this->whiteList))
				unset($this->whiteList[ array_search($urlSite,$this->whiteList) ]);
		}

		if ($updated){
			$this->setParam('blackList', serialize($this->blackList), 'cdata');
			$this->setParam('whiteList', serialize($this->whiteList), 'cdata');
			$this->saveParams();
		}
    
	}

	/**
	 * Méthode qui ajoute des éléments à la whitelist et les enleve de la blacklist si nécessaire	 
	 * Elements pouvant être enlevés : IP, email, url site
	 *
	 * @return	stdio
	 **/
	public function addToWhitelist($author,$ip,$email,$urlSite){
		include_once(PLX_CORE.'lib/class.plx.msg.php');
		$updated = false;
		$author = strtolower(trim($author));
		$email = strtolower(trim($email));
		$urlSite = strtolower(trim($urlSite));
		if (!empty($author) AND! in_array($author,$this->whiteList)){
			$this->whiteList[] = $author;
			$updated = true;
			if (in_array($author,$this->blackList))
				unset($this->blackList[ array_search($author,$this->blackList) ]);
		}	
		if (!empty($ip) AND $this->autoAddWLIP AND! in_array($ip,$this->whiteList)){
		$this->whiteList[] = $ip;
		$updated = true;
		if (in_array($ip,$this->blackList))
			unset($this->blackList[ array_search($ip,$this->blackList) ]);
		}
		$email = strtolower($email);
		if (!empty($email) AND $this->autoAddWLEemail AND! in_array($email,$this->whiteList)){
		$this->whiteList[] = $email;
		$updated = true;
		if (in_array($email,$this->blackList))
			unset($this->blackList[ array_search($email,$this->blackList) ]);
		}
		$urlSite = strtolower(trim($urlSite,'/'));
		if (!empty($urlSite) AND $this->autoAddWLUrlsite AND !in_array($urlSite,$this->whiteList)){
			$this->whiteList[] = $urlSite;
			$updated = true;
			if (in_array($urlSite,$this->blackList))
				unset($this->blackList[ array_search($urlSite,$this->blackList) ]);
		}

		if ($updated){
			$this->setParam('whiteList', serialize($this->whiteList), 'cdata');
			$this->setParam('blackList', serialize($this->blackList), 'cdata');
			$this->saveParams();
		}
	}

	/**
	 * Méthode qui ajoute les boutons WL et BL a coté de chaque commentaire
	 * sur la page admin des commentaires
	 *
	 * @return	stdio
	 **/
	public function AdminCommentsPagination() {
		$plxAdmin = plxAdmin::getInstance();
		
		$string = '<script type="text/javascript">
		var tds = document.getElementById("comments-table");
		if (!tds) tds = document.getElementById("form_comments"); //for pluxml 5.3
		ths = tds.getElementsByTagName("th");';
		if (!$this->adminText){
			$string .= 'ths[4].classList.add("icone");'; # Class pour afficher les icones
		} else {
			$string .= 'ths[4].classList.add("txt");';	# Class pour afficher les textes
		}		
		$string .= '	
			tds = tds.getElementsByTagName("td");
			for (i=4;i<tds.length;i=i+5){
			com = tds[i].childNodes[2].href
			com = com.substr(com.indexOf("?"));';
		if ($this->adminText){ # Replace les 3 textes par defaut par des icones
			$string .= 'tds[i].childNodes[0].innerHTML="<img src=\"'.PLX_PLUGINS.'plxSpamBlockComs/img/repondre.png\" width=32 height=32>"; 
			tds[i].childNodes[2].innerHTML="<img src=\"'.PLX_PLUGINS.'plxSpamBlockComs/img/modifier-comment.png\" width=32 height=32>";
			tds[i].childNodes[4].innerHTML="<img src=\"'.PLX_PLUGINS.'plxSpamBlockComs/img/modifier-article.png\" width=32 height=32>";';
		if($_SESSION["selCom"]=='spam') {
			$string .= 'tds[i].removeChild(tds[i].childNodes[0]);';
		} else {
			$string .= 'tds[i].removeChild(tds[i].childNodes[3]);
			tds[i].removeChild(tds[i].childNodes[1]);';
		}
		}
		if($_SESSION["selCom"]=='spam' AND !$this->adminText) {	
			$string .= 'tds[i].removeChild(tds[i].childNodes[0]);';
		}
		if ($this->useWL){
			if (!$this->adminText){
				$string .= 'sep = "<?php if (substr($plxAdmin->version,0,3)<="5.3") {?> |<?php }?>";
				tds[i].innerHTML+=sep+" <a href=\""+com+"&whitelist=1"+"\" onclick=\"return confirm(\''.$this->getlang('L_SBC_CONFIRM_WL').'\');\" title=\"'.$this->getlang('L_SBC_TITLE_WL').'\">'.$this->getlang('L_SBC_ADD_WL').'&nbsp;</a>";';
			} else {
				$string .= 'tds[i].innerHTML+="<a href=\""+com+"&whitelist=1"+"\" onclick=\"return confirm(\''.$this->getlang('L_SBC_CONFIRM_WL').'\');\"><img src=\"'.PLX_PLUGINS.'plxSpamBlockComs/img/whitelist.png\"  title=\"'.$this->getlang('L_SBC_TITLE_WL').'\" width=32 height=32></a>";';      
			}
		}
		if ($this->useBL){
			if($_SESSION["selCom"]!='spam') {
				if (!$this->adminText){
					$string .= 'sep = "<?php if (substr($plxAdmin->version,0,3)=="5.3") {?> |<?php }?>";
					tds[i].innerHTML+=sep+" <a href=\""+com+"&blacklist=1"+"\" onclick=\"return confirm(\''.$this->getlang('L_SBC_CONFIRM_BL').'\');\" title=\"'.$this->getlang('L_SBC_SPAM').'\">'.$this->getlang('L_SBC_ADD_BL').'</a>";';
				} else {
					$string .= 'tds[i].innerHTML+="<a href=\""+com+"&blacklist=1"+"\" onclick=\"return confirm(\''.$this->getlang('L_SBC_CONFIRM_BL').'\');\"><img src=\"'.PLX_PLUGINS.'plxSpamBlockComs/img/blacklist.png\" title=\"'.$this->getlang('L_SBC_SPAM').'\" width=32 height=32></a>";';
				}
			}
		}
		$string .= '}';
    
		if ($_SESSION["profil"]==0) # Afficher bouton vers la config si session = admin
			$string .= '
			var ab=document.getElementsByClassName("action-bar");
			if (ab.length>0){
			ab[0].innerHTML+=\'&nbsp;&nbsp;&nbsp;<a href="parametres_plugin.php?p=plxSpamBlockComs"><button type="button" title="'.$this->getlang('L_SBC_TITLE_CONFIG').'">'.$this->getlang('L_SBC_TO_CONFIG').'</button></a>\';
			} else {
			ab=document.getElementById("id_selection1").parentNode;
			if (ab) ab.innerHTML+=\'&nbsp;&nbsp;&nbsp;<a href="parametres_plugin.php?p=plxSpamBlockComs"><button type="button" title="'.$this->getlang('L_SBC_TITLE_CONFIG').'">'.$this->getlang('L_SBC_TO_CONFIG').'</button></a>\';}';
    
		$string .= '</script>';
		echo $string;
	}

		/**
	 * Méthode qui retour le status du commentaire
	 * sur la page d'édition du commentaire
	 *
	 * @return	stdio
	 * @author  DPFPIC
	 **/
	public function AdminCommentTop() {
		if((substr($_GET['c'],0,1) == $this->spamPrefix)) {
			$string = '$statut = "SPAM";';
			echo '<?php '.$string.' ?>';
 		}
	}
	/**
	 * Méthode qui ajoute WL et BL dans le menu déroulant des actions
	 * sur la page admin des commentaires
	 *
	 * @return	stdio
	 **/
	public function AdminCommentsTop() {
		$string = '
		if((!empty($_GET["sel"]) AND $_GET["sel"]=="spam") OR (isset($_SESSION["selCom"]) AND $_SESSION["selCom"]=="spam")){
			$comSel = "spam";
			$comSelMotif = "/^\\'.$this->spamPrefix.'[0-9]{4}.(.*).xml$/";
			$_SESSION["selCom"] = "spam";
			$nbComPagination=$plxAdmin->nbComments($comSelMotif);
			$selector=selector("all", "id_selection");
			foreach($breadcrumbs as $k=>$v) $breadcrumbs[$k]=str_replace("class=\"selected\"","",$v);
			$h2 = "<h2>Liste des SPAMS</h2>";
 		} elseif (strpos($comSelMotif,"punct")!==false){
			$comSelMotif = "/^[_]?[0-9]{4}.(.*).xml$/";
		}
		$nbComSPAM=($comSel=="spam")?$nbComPagination:$plxAdmin->nbComments("/\\'.$this->spamPrefix.'[0-9]{4}.(.*).xml$/");
		#$breadcrumbs[] = "<li><a ".($_SESSION["selCom"]=="spam"?"class=\'selected\' ":"")."href=\'comments.php?sel=spam&amp;page=1\'>Spam</a>&nbsp;(".$nbComSPAM.")</li>";
		'.(($this->useBL) ? '$breadcrumbs[] = "<li><a ".($_SESSION["selCom"]=="spam"?"class=\'selected\' ":"")."href=\'comments.php?sel=spam&amp;page=1\'>Spam</a>&nbsp;(".$nbComSPAM.")</li>";':'').'
		#$selector_add = "<option value=\"-\">-----</option>";
		$selector_add = "";
		'.(($this->useWL OR $this->useBL) ? '$selector_add = "<option value=\"-\">-----</option>";':'').'
		'.(($this->useWL)? '$selector_add .= "<option value=\"WL\">White List</option>";':'').'
		'.(($this->useBL)? '$selector_add .= "<option value=\"BL\">Black List</option>";':'').'
		$selector = str_replace ("</select>",$selector_add."</select>"  ,$selector );';
		echo '<?php '.$string.' ?>';
	}
	
	/**
	 * Méthode qui gère les actions mise en WL et mise en BL
	 * sur la page admin des commentaires
	 *
	 * @return	stdio
	 **/
	public function AdminCommentsPrepend() {

		$string = '
		$plxAdmin->checkProfil(PROFIL_ADMIN, PROFIL_MANAGER, PROFIL_MODERATOR);
		$plxAdminSpamAction=0;
		# Bouton Whitelist
		if (!empty($_GET["c"]) AND !empty($_GET["whitelist"]) ) {
			$id = $_GET["c"];
			if (in_array($id.".xml",$plxAdmin->plxGlob_coms->aFiles)){ # Si commentaire existe
				$comment = $plxAdmin->parseCommentaire(PLX_ROOT.$plxAdmin->aConf["racine_commentaires"].$id.".xml");
				$plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->addToWhitelist($comment["author"],$comment["ip"],$comment["mail"],$comment["site"]);
				$plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->cleanBlacklistTag($id);
				$plxAdmin->modCommentaire($id, "online");
			}
		$plxAdminSpamAction=1;
		}
		# Bouton Blacklist
		if (!empty($_GET["c"]) AND !empty($_GET["blacklist"]) ) {
			$id = $_GET["c"];
			if (in_array($id.".xml",$plxAdmin->plxGlob_coms->aFiles)){ # Si commentaire existe
				$comment = $plxAdmin->parseCommentaire(PLX_ROOT.$plxAdmin->aConf["racine_commentaires"].$id.".xml");
				$plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->addToBlacklist($comment["author"],$comment["ip"],$comment["mail"],$comment["site"],0,$comment["date"]);
				$plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->SPAMCommentaire($id,$plxAdmin->aConf["racine_commentaires"]);
			}
		$plxAdminSpamAction=2;
		}
		# Bouton Delete
		if (!empty($_GET["c"]) AND !empty($_GET["delete"]) ) {
			$id = $_GET["c"];
				if (in_array($id.".xml",$plxAdmin->plxGlob_coms->aFiles)){ # Si commentaire existe
					$plxAdmin->delCommentaire($id);
				}
			$plxAdminSpamAction=3;
		}
		# Blacklist des commentaires sélectionnés
		if (isset($_POST["selection"]) AND !empty($_POST["btn_ok"]) AND ($_POST["selection"]=="BL") AND isset($_POST["idCom"])) {
			foreach ($_POST["idCom"] as $k => $v){ # $v = id de commentaire
				$comment = $plxAdmin->parseCommentaire(PLX_ROOT.$plxAdmin->aConf["racine_commentaires"].$v.".xml");
				$plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->addToBlacklist($comment["author"],$comment["ip"],$comment["mail"],$comment["site"],0,$comment["date"]);
				if ($plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->saveMode==3){
					$plxAdmin->delCommentaire($v);
				} elseif ($plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->saveMode==2){
					$plxAdmin->modCommentaire($v, "offline");
				} elseif ($plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->saveMode==1){
					$plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->SPAMCommentaire($v,$plxAdmin->aConf["racine_commentaires"]);
				}
			}
			$plxAdminSpamAction=4;
		}
		# Whitelist des commentaires sélectionnés
		if (isset($_POST["selection"]) AND !empty($_POST["btn_ok"]) AND ($_POST["selection"]=="WL") AND isset($_POST["idCom"])) {
			foreach ($_POST["idCom"] as $k => $v){ # $v = id de commentaire
				$comment = $plxAdmin->parseCommentaire(PLX_ROOT.$plxAdmin->aConf["racine_commentaires"].$v.".xml");
				$plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->addToWhitelist($comment["author"],$comment["ip"],$comment["mail"],$comment["site"]);
				$plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->cleanBlacklistTag($v);
				$plxAdmin->modCommentaire($v, "online");
			}
			$plxAdminSpamAction=5;
		}
  
		if($plxAdminSpamAction) {
			header("Location: comments.php".(!empty($_GET["a"])?"?a=".$_GET["a"]:""));
			exit;
		}
		# Gestion du clic sur breadcum spam
		if(isset($_GET["sel"]) AND $_GET["sel"]=="spam") $_SESSION["selCom"] = "spam";';

		echo '<?php '.$string.' ?>';
	}
	//strstr('core/admin/comment',$this->path_url)
	/**
	 * Méthode pour l'ajout d'un commentaire
	 *
	 * @return	stdio
	 **/	
	public function plxMotorAddCommentaire(){
		$string = ' //var_dump(strstr($this->path_url,"core/admin/comment"),$this->path_url);exit; // var dump a néttoyé ;)
		if (strstr($this->path_url,"core/admin/comment")) return; # Pas de filtrage en édition de commentaire
		if (empty($content["site"]) and !$this->plxPlugins->aPlugins["plxSpamBlockComs"]->forceModeration) return; # Pas de filtrage si pas d\'url
		# Test WhiteList
		if ($this->plxPlugins->aPlugins["plxSpamBlockComs"]->useWL 
			AND ((!empty($content["mail"]) AND in_array(strtolower(trim($content["mail"])) , $this->plxPlugins->aPlugins["plxSpamBlockComs"]->whiteList))
			OR (!empty($content["author"]) AND in_array(strtolower(trim($content["author"])) , $this->plxPlugins->aPlugins["plxSpamBlockComs"]->whiteList))
			OR (!empty($content["site"]) AND in_array(strtolower(trim($content["site"], " \t\n\r\0\x0B/")) , $this->plxPlugins->aPlugins["plxSpamBlockComs"]->whiteList))
			OR (!empty($content["ip"]) AND in_array($content["ip"] , $this->plxPlugins->aPlugins["plxSpamBlockComs"]->whiteList)))) {
				if ($this->aConf["mod_com"]) $content["filename"] = trim($content["filename"],"_");
					$this->aConf["mod_com"] = false;
		# Test BlackList
		} elseif ($this->plxPlugins->aPlugins["plxSpamBlockComs"]->useBL 
			AND ((!empty($content["mail"]) AND in_array(strtolower(trim($content["mail"])) , $this->plxPlugins->aPlugins["plxSpamBlockComs"]->blackList))
			OR (!empty($content["author"]) AND in_array(strtolower(trim($content["author"])) , $this->plxPlugins->aPlugins["plxSpamBlockComs"]->blackList))
			OR (!empty($content["site"]) AND in_array(strtolower(trim($content["site"], " \t\n\r\0\x0B/")) , $this->plxPlugins->aPlugins["plxSpamBlockComs"]->blackList))
			OR (!empty($content["ip"]) AND in_array($content["ip"] , $this->plxPlugins->aPlugins["plxSpamBlockComs"]->blackList)))) {
				$this->plxPlugins->aPlugins["plxSpamBlockComs"]->addToBlacklist($content["author"],$content["ip"],$content["mail"],$content["site"],1);
				if (!$this->aConf["mod_com"]) $content["filename"] = "_" . $content["filename"];
					$this->aConf["mod_com"] = true;
				if ($this->plxPlugins->aPlugins["plxSpamBlockComs"]->saveMode==3) # Ne pas sauvegarder
					return true; # commentaire rejeté si blacklist
				if ($this->plxPlugins->aPlugins["plxSpamBlockComs"]->saveMode==1) # Sauvegarder comme spam
						$content["filename"] = str_replace("_","'.$this->spamPrefix.'",$content["filename"]);
				if ($this->plxPlugins->aPlugins["plxSpamBlockComs"]->saveMode==2) # Sauvegarder comme modéré
					$content["content"] = $this->plxPlugins->aPlugins["plxSpamBlockComs"]->SPAMTag . $content["content"]; # Ajout de la ligne ***SPAM***
		} else {
		# Chercher si timer pas respecté => BL    
		if ($this->plxPlugins->aPlugins["plxSpamBlockComs"]->autoBLTimer > 0){
			if (empty($_SESSION["plxSpamBlockComsTime"]) OR (time() - $_SESSION["plxSpamBlockComsTime"] < $this->plxPlugins->aPlugins["plxSpamBlockComs"]->autoBLTimer)){
				$this->plxPlugins->aPlugins["plxSpamBlockComs"]->addToBlacklist($comment["author"],$content["ip"],$content["mail"],$content["site"],1);         
				if (!$this->aConf["mod_com"]) $content["filename"] = "_" . $content["filename"];
					$this->aConf["mod_com"] = true;
				if ($this->plxPlugins->aPlugins["plxSpamBlockComs"]->saveMode==3) # Ne pas sauvegarder
					return true; # Commentaire rejeté si blacklist
				if ($this->plxPlugins->aPlugins["plxSpamBlockComs"]->saveMode==1) # Sauvegarder comme spam
					$content["filename"] = str_replace("_","'.$this->spamPrefix.'",$content["filename"]);
				if ($this->plxPlugins->aPlugins["plxSpamBlockComs"]->saveMode==2) # Sauvegarder comme modéré
					$content["content"] = $this->plxPlugins->aPlugins["plxSpamBlockComs"]->SPAMTag . $content["content"]; # Ajout de la ligne ***SPAM***
			}
		}

	# Autre cas, commentaire modéré
		if (!$this->aConf["mod_com"]) $content["filename"] = "_" . $content["filename"];
		$this->aConf["mod_com"] = true;
	}';
	echo '<?php '.$string.' ?>';
	}
  
	/**
	 * Méthode qui ajoute bouton avec le timer et l'admin rapide
	 *
	 * @return	stdio
	 **/	
	public function ThemeEndBody(){

	$string = 'if (strpos(serialize(get_included_files()),"commentaires.php")!==false){';
	echo '<?php '.$string.' ?>';
    
    if ($this->autoBLTimer > 0 # Seulement si fonction timer activé dans la config 
        && !isset($_POST["name"]) && !isset($_POST["content"]) && !isset($_POST["urlSite"]) # Sauf si commentaire en cours de soumission
        && ( empty($_SESSION['msgcom']) OR $_SESSION['msgcom'] == $this->getLang('L_SBC_COM_IN_MODERATION') OR $_SESSION['msgcom'] == $this->getLang('L_SBC_COM_IN_MODERATION'))){ # Sauf si un message va etre affiché au visiteur (mais sauf si le message est celui du blacklist ou modération)
?>
	<script type="text/javascript">
	var inputs = document.getElementById("form");
	if (!inputs) inputs = document.getElementById("comments");
	if (inputs){
	inputs = inputs.getElementsByTagName("input");
	var submit=false;
	for(i=0;i<inputs.length;i++)
		if (inputs[i].type=="submit"){
		submit=inputs[i];
		break;
    }
	if (submit){
		submit.disabled=true;
		submit.plxDefVal=submit.value;
		submit.plxToWait=<?php echo $this->autoBLTimer ?>;
		submit.plxDefToWait = submit.plxToWait;
		submit.value= submit.plxDefVal + " ("+submit.plxToWait+")";
		submit.plxCountdown = setInterval(function () { 
		if (submit.plxToWait>1){
			submit.plxToWait--;
			submit.value= submit.plxDefVal + " ("+submit.plxToWait+")";
			submit.disabled=true;
		} else {
			submit.value= submit.plxDefVal;
			submit.disabled=false;
      }
	}, 1000);
	var tagsA = document.getElementsByTagName("a");
		for (i=0;i<tagsA.length;i++)
			if (tagsA[i].id != "capcha-reload"){
				tagsA[i].onmousedown=reset_timer;
				tagsA[i].addEventListener("touchstart",reset_timer);
			}
//		document.getElementsByTagName("body")[0].onfocus=reset_timer;
	}  
	}
	function reset_timer(){
	submit.plxToWait=submit.plxDefToWait+5;
	}
	</script>
<?php
	}
	if(isset($_SESSION["profil"]) AND $_SESSION["profil"] < 3 AND $this->addAdminIcones) {
		$plxShow = plxShow::getInstance();
		if($plxShow->plxMotor->plxRecord_coms) {
			while($plxShow->plxMotor->plxRecord_coms->loop()):
				$idcom[] = substr($plxShow->comId(false),5);
			endwhile;
		#print_r($idcom);
		$plxMotor = plxMotor::getInstance();
		$racine_coms = $plxMotor->aConf['racine_commentaires'];
		$listCom = @array_diff(scandir(PLX_ROOT.$racine_coms), Array( ".", "..", ".htaccess"));
		#print_r($listCom);
		$i = 0;
		foreach($listCom AS $array) {
			$rpos = strpos($array, $plxShow->plxMotor->cible);
				if($rpos !== false AND substr($array,0,1) == '0') {
					$nbCom[] = substr($array,0,-4);
				}
		}
		#print_r($idcom);
		for ($i = 0; $i < count($idcom); $i++) {
			foreach($nbCom AS $array) {
				if (substr($array,15) == $idcom[$i]) {
					$nbComs[] = $array;
				}
			}
		}

	#print_r($nbComs);
	}

	$string = '
	<script type="text/javascript">
	var numcom = document.getElementsByClassName("nbcom");
	if (numcom.length==0) var numcom = document.getElementsByClassName("num-com");';
	for ($t = 0; $t < count(@$idcom); $t++) {
	    if ($this->useBL) $string .= 'numcom['.$t.'].parentNode.innerHTML += \'<a href="<?php echo $plxShow->plxMotor->racine; ?>core/admin/comments.php?blacklist=1&c='.$nbComs[$t].'&sel=spam&page=1" title="'.$this->getlang('L_SBC_SPAM').'" style="float:right"><img src="<?php echo $plxShow->plxMotor->racine . PLX_PLUGINS ?>plxSpamBlockComs/img/blacklist.png" width=16 height=16></a>&nbsp;&nbsp;\';';
		if ($this->useWL) $string .= 'numcom['.$t.'].parentNode.innerHTML += \'<a href="<?php echo $plxShow->plxMotor->racine; ?>core/admin/comments.php?whitelist=1&c='.$nbComs[$t].'" title="'.$this->getlang('L_SBC_TITLE_WL_FO').'" style="float:right"><img src="<?php echo $plxShow->plxMotor->racine . PLX_PLUGINS ?>plxSpamBlockComs/img/whitelist.png" width=16 height=16></a>&nbsp;&nbsp;\';';
		$string .= 'numcom['.$t.'].parentNode.innerHTML += \'<a href="<?php echo $plxShow->plxMotor->racine; ?>core/admin/comments.php?delete=1&c='.$nbComs[$t].'" title="'.$this->getlang('L_SBC_SUPP_COM').'" style="float:right"><img src="<?php echo $plxShow->plxMotor->racine . PLX_PLUGINS ?>plxSpamBlockComs/img/delete.png" width=16 height=16></a>&nbsp;&nbsp;\';
		numcom['.$t.'].parentNode.innerHTML += \'<a href="<?php echo $plxShow->plxMotor->racine; ?>core/admin/comment.php?c='.$nbComs[$t].'" title="'.$this->getlang('L_SBC_EDIT_COM').'" style="float:right"><img src=\"<?php echo $plxShow->plxMotor->racine . PLX_PLUGINS ?>plxSpamBlockComs/img/modifier-comment.png\" width=16 height=16></a>&nbsp;&nbsp;\';
		numcom['.$t.'].parentNode.innerHTML += \'<a href="<?php echo $plxShow->plxMotor->racine; ?>core/admin/comment_new.php?c='.$nbComs[$t].'" title="'.$this->getlang('L_SBC_REPLY').'" style="float:right"><img src=\"<?php echo $plxShow->plxMotor->racine . PLX_PLUGINS ?>plxSpamBlockComs/img/repondre.png\" width=16 height=16></a>&nbsp;&nbsp;\';';
	}
	$string .= '</script>';
	echo $string;
	}
	$string = '}';
	echo '<?php '.$string.' ?>';
	}

		/**
	 * Méthode qui affiche un message si l'adresse eemail du contact n'est pas renseignée
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminTopBottom() {
		$string = '
			if($plxAdmin->plxPlugins->aPlugins["plxSpamBlockComs"]->getParam("senderTo")=="") {
				echo "<p class=\"warning\">Plugin plxSpamBlockComs<br />'.$this->getLang("L_SBC_ERR_EMAIL").'</p>";
				plxMsg::Display();}';
			echo '<?php '.$string.' ?>';
	}
	
	/**
	 * Méthode qui recupère les variables commentaire
	 *
	 * @return	stdio
	 * @author	DPFPIC
	 **/
	public function get_idcom($valeur, $array) {
		foreach($array AS $ligne) {
			$i = 0;
			if(stristr($ligne, $valeur)) {
				$resultat[] = $ligne;
			}
		}
		return $resultat;
	}

	
}
