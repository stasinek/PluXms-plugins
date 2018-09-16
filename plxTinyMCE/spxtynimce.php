<?php
/**
 * Plugin Tynimce
 *
 * @package SPX
 * @version	
 * @date	24/08/2013
 * @author	EVRARD J
 **/
class spxtynimce extends plxPlugin {
	
	
	/**
	 * Constructeur de la classe tynimce
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	
	 **/
	public function __construct($default_lang) {
		
		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# droits pour accéder à la page config.php du plugin
		$this->setlanguagename();
		$this->initconfiguration();
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->spxname="spxtynimce";
		$this->aexternal_toolbar = array();
		$this->aexternal_external_plugins = array();
		
		$this->addHook('AdminTopBottom', 'AdminTopBottom');
		
		
		
		
		# si affichage des articles coté visiteurs: protection des emails contre le spam
		if(!defined('PLX_ADMIN')) {
			
			if ($this->getParam('emailprotect')==1){
				$this->addHook('plxMotorParseArticle', 'protectEmailsArticles');
				$this->addHook('plxShowStaticContent', 'protectEmailsStatics');
			}
		} else {
			
			# affichage coté administration si on est pas sur les pages parametres_edittpl.php, comment.php et page statiques (cf config plugin)
			$check = $this->getParam('static')==1 ? '' : '|statique';
			$check .= $this->getParam('pluginedit')==1 ? '' : '|plugin';
			if(!preg_match('/(parametres_edittpl|comment'.$check.')/', basename($_SERVER['SCRIPT_NAME']))) {

				# répertoire racine d'installation de PluXml sur le serveur
				/*$dir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));
				$this->racine = trim(preg_replace("/\/(core|plugins)\/(.*)/", "", $dir), "/")."/";
				$this->racine = $this->racine[0]!="/" ? "/".$this->racine : $this->racine;*/
				
				
				
				$protocol = (!empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')?	'https://' : "http://";
				$servername = $_SERVER['HTTP_HOST'];
				$serverport = (preg_match('/:[0-9]+/', $servername) OR $_SERVER['SERVER_PORT'])=='80' ? '' : ':'.$_SERVER['SERVER_PORT'];
				preg_match("/(.*)\/core\/admin/i", $_SERVER['SCRIPT_NAME'], $capture);
				$_SESSION['tynimce_url'] = $protocol.$servername.$serverport.$capture[1].'/'.$this->getParam('uplDir');

				# déclaration pour ajouter l'éditeur
				$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
				$this->addHook('AdminFootEndBody', 'AdminFootEndBody');
				
				# pour les articles
				if(!isset($_POST['new_category'])) {
					$this->addHook('AdminEditArticle', 'Abs2Rel');
					$this->addHook('AdminArticleTop', 'Rel2Abs');
					
					$this->addHook('AdminArticlePreview', 'Abs2RelPreview');
					
					# pour les pages statiques
					$this->addHook('AdminEditStatique', 'Abs2Rel');
					$this->addHook('AdminStaticTop', 'Rel2Abs');
				}
				
				//$this->addHook('AdminArticlePrepend', 'AdminArticlePrepend'); # conversion des liens pour le preview d'un article
				if ($this->getParam('filemanger_replace_media')){
					$this->addHook('AdminMediasTop', 'AdminMediasTop');
					$this->addHook('AdminMediasFoot', 'AdminMediasFoot');
				}

			}
		}
			
	}
	
	public function setlanguagename() {
		switch ($this->default_lang) {
		case "fr":
			$language = "fr_FR";
			$language2 = "fr_FR";
			break;
		case "en":
			$language = "en_EN";
			$language2 = "en_EN";
			break;
		case "pt":
			$language = "pt_PT";
			$language2 = "pt_PT";
			break;
		case "ro":
			$language = "ro";
			$language2 = "en_EN";
			break;
		default:
			$language = $this->default_lang;
			$language2 = $this->default_lang;
		break;
		}
		$_SESSION['spxtynimce']["tinymce_language"]=$language;
		$_SESSION['spxtynimce']["filemanager_language"]=$language2;
		//echo ("lag=".$_SESSION['spxtynimce']["tinymce"].$_SESSION['spxtynimce']["tinymce_language"]." lang2=".$_SESSION['spxtynimce']["filemanager_language"]);
	}
	
	public function initconfiguration() {
		if(defined('PLX_ADMIN')) {
		
			$protocol = (!empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')?	'https://' : "http://";
			$servername = $_SERVER['HTTP_HOST'];
			$serverport = (preg_match('/:[0-9]+/', $servername) OR $_SERVER['SERVER_PORT'])=='80' ? '' : ':'.$_SERVER['SERVER_PORT'];
			preg_match("/(.*)\/core\/admin/i", $_SERVER['SCRIPT_NAME'], $capture);
			
			$myroot = rtrim($_SERVER['DOCUMENT_ROOT'],'/').$capture[1];
			//echo ($capture[1]);
			//$extend = $_SERVER['PHP_SELF']
			//$myroot = rtrim($_SERVER['DOCUMENT_ROOT'],'/').
			if (plxUtils::strCheck($this->getParam('root'))=="" || plxUtils::strCheck($this->getParam('root'))=="/") $this->setParam('root',$myroot,'cdata');
			if (plxUtils::strCheck($this->getParam('uplDir'))=="") $this->setParam('uplDir','data/medias/','cdata');
			if (plxUtils::strCheck($this->getParam('static'))=="") $this->setParam('static','1','numeric');
			if (plxUtils::strCheck($this->getParam('pluginedit'))=="") $this->setParam('pluginedit','0','numeric');
			
			if (plxUtils::strCheck($this->getParam('image_resizing'))=="") $this->setParam('image_resizing','1','numeric');
			if (plxUtils::strCheck($this->getParam('image_width'))=="") $this->setParam('image_width','600','numeric');
			if (plxUtils::strCheck($this->getParam('image_height'))=="") $this->setParam('image_height','0','numeric');
			
			if (plxUtils::strCheck($this->getParam('filemanger_replace_media'))=="") $this->setParam('filemanger_replace_media','0','numeric');
			
			if (plxUtils::strCheck($this->getParam('style_format_active'))=="") $this->setParam('style_format_active','0','numeric');
			if (plxUtils::strCheck($this->getParam('style_format_content'))=="") $this->setParam('style_format_active','','cdata');
			 
        
			
			
			$aProfils = $this->get_aprofil();
			
			foreach($aProfils as $key=>$val) { 
				if (plxUtils::strCheck($this->getParam('tyniplugin'.$key))=="") $this->setParam('tyniplugin'.$key,$this->get_plugin_list(),'string');
				if (plxUtils::strCheck($this->getParam('tynitoolbar'.$key))=="") $this->setParam('tynitoolbar'.$key,$this->get_toolbar_list(),'string');
				if (plxUtils::strCheck($this->getParam('filemanager_permission'.$key))=="") $this->setParam('filemanager_permission'.$key,$this->get_filemanager_permission_list(),'string');
			}
			
			
			//if(!isset($_SESSION["spxtynimce"]))	session_start();
			$_SESSION['spxtynimce']['root'] = $this->getParam('root');// don't touch this configuration
			// use for img url
			
			$_SESSION['spxtynimce']['base_url'] = $protocol.$servername.$serverport.$capture[1]; // base url of site. If you prefer relative urls leave empty
			$_SESSION['spxtynimce']['upload_dir']=$capture[1]."/".$this->getParam('uplDir');  // path from base_url to base of upload folder
			//$current_path = '../../../../../data/images/';
			$_SESSION['spxtynimce']['current_path']='../../../'.$this->getParam('uplDir');
			// data/ to /jerome/pluxml-5-2/data/
			
			
			
			
			if ($this->getParam('image_resizing')==1){
				$_SESSION['spxtynimce']['filemanager']['image_resizing']= true;
			}else{
				$_SESSION['spxtynimce']['filemanager']['image_resizing']= false;
			}
			$_SESSION['spxtynimce']['filemanager']['image_width']= $this->getParam('image_width');
			$_SESSION['spxtynimce']['filemanager']['image_height']= $this->getParam('image_height');
			
			if ($this->getParam('filemanger_use_aviary')==1){
				$_SESSION['spxtynimce']['filemanager']['filemanger_use_aviary']= true;
			}else{
				$_SESSION['spxtynimce']['filemanager']['filemanger_use_aviary']= false;
			}
			$_SESSION['spxtynimce']['filemanager']['filemanger_cle_aviary']= $this->getParam('filemanger_cle_aviary');
			$_SESSION['spxtynimce']['filemanager']['filemanger_secret_aviary']= $this->getParam('filemanger_secret_aviary');
			
			
			//$image_resizing=true;
			//$image_width=600;
			//$image_height=0;
			$aFMP = explode (' ',$this->get_filemanager_permission_list());
			$profil = $_SESSION["profil"];
			$sperm = $this->getParam('filemanager_permission'.$profil);
			$aperm = explode(' ', $sperm);
			foreach($aFMP as $permission) {
				$bispermitted = in_array($permission,$aperm)?true:false;
				//echo ("bispermitted=".$permission." = ".$bispermitted."<br>\n");
				$_SESSION['spxtynimce']['filemanager'][$permission]=$bispermitted;
			}
			//echo("<pre>");
			//print_r($_SESSION['spxtynimce']);
			//print_r($_SESSION);
			//echo("</pre>");
			//echo("default language=".$this->default_lang);
			
		
		}
		
	}
	public function get_plugin_list() {
		// v1.3 emoticons
		return "advlist autolink link image lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code insertdatetime media nonbreaking table contextmenu directionality paste textcolor youtube emoticons fullscreen noneditable";
		//return "advlist anchor autolink autoresize autosave bbcode charmap code contextmenu directionality emoticons example example_dependency fullpage fullscreen hr image insertdatetime layer legacyoutput link lists media nonbreaking noneditable pagebreak paste preview print save searchreplace spellchecker tabfocus table template textcolor visualblocks visualchars wordcount youtube emoticons" ;
	}
	//
	public function get_toolbar_list() {
		// v1.3 paragraph font family fontface emoticons
		return "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect forecolor backcolor | formatselect | fontselect | fontsizeselect | link unlink anchor | image media | print preview code | youtube | emoticons | fullscreen | insertdatetime";
		//return "newdocument bold italic underline strikethrough alignleft aligncenter alignright alignjustify styleselect formatselect fontselect fontsizeselect cut copy paste bullist numlist outdent indent blockquote undo redo removeformat subscript superscript youtube emoticons";
	}
	public function get_filemanager_permission_list() {
		return "uploadfiles renamefiles deletefiles createfolders renamefolders deletefolders";
	}
	
	
	
	/**
	 * Méthode qui remplace l'editeur de media de pluxml par le file manager
	 *
	 * @return	
	 * @author	jevrard
	 **/
	
	public function AdminMediasTop() {
		
		
		echo ('<iframe src="'.PLX_PLUGINS.$this->spxname.'/filemanager/dialog.php?type=0&lang='.$_SESSION['spxtynimce']["filemanager_language"].'&editor=mce_0" width="99%" height="600">
			<p>Votre navigateur ne supporte la gestion iframe</p>
		</iframe>');
		echo '<?php
		ob_start() ;
		?>';
		
	}
	public function AdminMediasFoot() {
		echo '<?php
		ob_end_clean() ;
		?>';
	}
	
	/**
	 * Méthode qui convertit les liens absolus en liens relatifs
	 *
	 * @return	// foreach($aProfils as $key=>$val) {
	 * @author	
	 **/
	
	public function get_aprofil() {
		# Tableau des profils
		return array(
			0 => $this->getLang('L_PROFIL_ADMIN2'),
			1 => $this->getLang('L_PROFIL_MANAGER2'),
			2 => $this->getLang('L_PROFIL_MODERATOR2'),
			3 => $this->getLang('L_PROFIL_EDITOR2'),
			4 => $this->getLang('L_PROFIL_WRITER2')
		);
		
	}
	
	/**
	 * Méthode qui convertit les liens absolus en liens relatifs
	 *
	 * @return	stdio
	 * @author	Je-evrard
	 **/
	public function Abs2RelPreview() {
		//$plxAdmin = plxAdmin::getinstance();
		//$medias = $plxAdmin->aConf["medias"];
		// article.php ligne 114 //header('Location: article.php?a='.$_POST['artId']);
		
		echo '<?php
		
		
			$abs_path_images = "../../'.$this->getParam('uplDir').'";
			$rel_path_images = "'.$this->getParam('uplDir').'";
			
			$abs_path_tiny = "../../plugins/spxtynimce/tinymce/";
			$rel_path_tiny = "plugins/spxtynimce/tinymce/";
			
			# Les liens absolus commençant par http://www.domaine.com/ sont convertis en liens relatifs
			if(isset($art["chapo"])) {
				$art["chapo"] = str_replace($abs_path_images, $rel_path_images, $art["chapo"]);
				$art["chapo"] = spxtynimce::addImgData($art["chapo"]);
				$art["chapo"] = str_replace($abs_path_tiny, $rel_path_tiny,$art["chapo"]);
			}
			$art["content"] = str_replace($abs_path_images, $rel_path_images, $art["content"]);
			$art["content"] = spxtynimce::addImgData($art["content"]);
			$art["content"] = str_replace($abs_path_tiny, $rel_path_tiny, $art["content"]);
		
		
		?>';
	
	}
	/**
	 * Méthode qui convertit les liens absolus en liens relatifs
	 *
	 * @return	stdio
	 * @author	Je-evrard
	 **/
	public function Abs2Rel() {
		//$plxAdmin = plxAdmin::getinstance();
		//$medias = $plxAdmin->aConf["medias"];
		// article.php ligne 114 //header('Location: article.php?a='.$_POST['artId']);
		
		echo '<?php
		
		
			$abs_path_images = "../../'.$this->getParam('uplDir').'";
			$rel_path_images = "'.$this->getParam('uplDir').'";
			
			$abs_path_tiny = "../../plugins/spxtynimce/tinymce/";
			$rel_path_tiny = "plugins/spxtynimce/tinymce/";
			
			# Les liens absolus commençant par http://www.domaine.com/ sont convertis en liens relatifs
			if(isset($content["chapo"])) {
				$content["chapo"] = str_replace($abs_path_images, $rel_path_images, $content["chapo"]);
				$content["chapo"] = spxtynimce::addImgData($content["chapo"]);
				$content["chapo"] = str_replace($abs_path_tiny, $rel_path_tiny,$content["chapo"]);
			}
			$content["content"] = str_replace($abs_path_images, $rel_path_images, $content["content"]);
			$content["content"] = spxtynimce::addImgData($content["content"]);
			$content["content"] = str_replace($abs_path_tiny, $rel_path_tiny, $content["content"]);
		
		
		?>';
	
	}
	

	/**
	 * Méthode qui convertit les liens relatifs en liens absolus
	 *
	 * @return	stdio
	 * @author	Je-evrard
	 **/
	public function Rel2Abs() {

		echo '<?php

		if(!isset($_POST["draft"]) AND !isset($_POST["publish"]) AND !isset($_POST["update"]) AND !isset($_POST["moderate"] )) {
			# Préparation des variables
			$abs_path_images = "../../'.$this->getParam('uplDir').'";
			$rel_path_images = "'.$this->getParam('uplDir').'";
			
			$abs_path_tiny = "../../plugins/spxtynimce/tinymce/";
			$rel_path_tiny = "plugins/spxtynimce/tinymce/";
			
			/*echo ("abs_path_images2=".$abs_path_images."\n<br>");
			echo ("rel_path_images2=".$rel_path_images."\n<br>");
			echo ("conf medias =".$plxAdmin->aConf["medias"]."\n<br>");
			echo ("upldir ="."'.$this->getParam('uplDir').'"."\n<br>");*/
		
			
			# Les liens relatifs sont convertis en liens absolus, pour que les images soient visibles dans CKEditor
			if(isset($chapo)) {
				$chapo = str_replace($rel_path_images, $abs_path_images, $chapo);
				$chapo = str_replace($rel_path_tiny, $abs_path_tiny, $chapo);
			}
			$content = str_replace($rel_path_images, $abs_path_images, $content);
			$content = str_replace($rel_path_tiny, $abs_path_tiny, $content);
		}
		
		?>';
	}
	
	public static function addImgData($txt) {
		if(preg_match_all('/<img[^>]+>/i', $txt, $matches)){
			foreach($matches[0] as $k => $v) {
				$img = $v;
				preg_match( '/data-spxtynimce="([^"]*)"/i', $img, $dataspxtynimce ) ;
				if ($dataspxtynimce[1]!="true"){
					$newsrcdata = '<img data-spxtynimce="true"';
					$img2 = str_replace("<img", $newsrcdata , $img);
					$txt = str_replace($img, $img2 , $txt);
				}
			}
		};

		return $txt;
	}

	/**
	 * Méthode qui affiche un message si le répertoire d'upload n'est pas définit dans la config du plugin
	 *
	 * @return	stdio
	 * @author	
	 **/
	public function AdminTopBottom() {

		$string = '
		if($plxAdmin->plxPlugins->aPlugins["spxtynimce"]->getParam("uplDir")=="") {
			echo "<p class=\"warning\">Plugin tynimcebr />'.$this->getLang("L_ERR_UPLDIR_NOT_DEFINED").'</p>";
			plxMsg::Display();
		}';
		$string='';	
		echo '<?php '.$string.' ?>';

	}

	/**
	 * Méthode qui ajoute la déclaration du script javascript de tynimce
	 *
	 * @return	stdio
	 * @author	
	 **/
	public function AdminTopEndHead() {
		echo '<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'spxtynimce/css/style.css" />'."\n";
		echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.$this->spxname.'/tinymce/tinymce.min.js"></script>'."\n";

	}

	/**
	 * Méthode qui ajoute les paramètres d'initialisation pour tynimce
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminFootEndBody() {
		$profil = $_SESSION["profil"];
		$extraPlugins = array();
		$buttons = array();
		if($this->getParam('oembed')) {
			$extraPlugins[] = 'oEmbed';
			$buttons['oembed'] = "'oEmbed',";
		}
		if($this->getParam('syntaxhighlight')) {
			$extraPlugins[] = 'syntaxhighlight';
			$buttons['syntaxhighlight'] = "'Code',";
		}
		if($this->getParam('lightbox')) {
			$extraPlugins[] = 'lightbox';
		}
		
		
		
		# check plugin neeeded from other plugin
			/* external_plugins: { 
					"filemanager" : "../../spxtynimce/filemanager/plugin.min.js", 
					"youtube" : "../../spxtinybtshortcodes/shortcode/youtube/plugin.js" 
				}, 
			
			 	and toolbar: "... | youtube"
			*/
			
			/*
			$plxShow = plxShow::getInstance(); // pour appeler les fonctions de la classe plxShow
			$plxShow->plxMotor->plxPlugins->aPlugins
			
			
			*/
			//$this->aexternal_external_plugins	;
			//$this->aexternal_toolbar;
			
		#
		$this->checkshortcodeplugins();
		//$this->aexternal_toolbar
		$sexternal_toolbar="";
		foreach($this->aexternal_toolbar as $key => $value) {
			$sexternal_toolbar.=" | ".$value;		
		}
		$sexternal_external_plugins="";
		foreach($this->aexternal_external_plugins as $key => $value) {
			$sexternal_external_plugins.=", ".$value;		
		}
		
		//echo ("test1 = ".$sexternal_toolbar);
		//echo ("test2 = ".$sexternal_external_plugins);

?>
	<script type="text/javascript">
	tinymce.init({ 
		selector: "textarea", 
		theme: "modern",
		language : '<?php echo $_SESSION['spxtynimce']["tinymce_language"] ?>',
		
		
		
		
		external_filemanager_path: "../../plugins/spxtynimce/filemanager/",
		filemanager_title: "<?php echo L_MEDIAS_TITLE ?>",
		external_plugins: { 
			"filemanager" : "../../spxtynimce/filemanager/plugin.min.js"
			<?php echo $sexternal_external_plugins ;?> 
			},
		//plugins: [  "advlist autolink link image lists charmap print preview hr anchor pagebreak", "searchreplace wordcount visualblocks visualchars code insertdatetime media nonbreaking", "table contextmenu directionality emoticons paste textcolor filemanager"
		//], 
		plugins: [  "<?php echo $this->getParam('tyniplugin'.$profil) ;?> responsivefilemanager"
		],
		image_advtab: true,
		
		// use for fontawesome 
		//extended_valid_elements: 'span[class]',
		//content_css: '../../plugins/spxtynimce/tinymce/plugins/fontawesome/fontawesome/css/font-awesome.min.css',
		//content_css: '//netdna.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css',
		
		
		/*setup: function(editor) {
          editor.on('FullscreenStateChanged', function(e) {
              console.log('FullscreenStateChanged event', e);
			  console.log("state"+e.state);
			  var m = document.getElementsByClassName("action-bar"), c = m.style;
			  console.dir (m);
			  console.dir (c);
			  // c.z-index="none";
			  if (e.state==true){
				  
			  }else{
				  
			  }
          });
		},*/
		
		
		
		/*
		style_formats: [
        {title: 'Bold text', inline: 'b'},
        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
        {title: 'Example 1', inline: 'span', classes: 'example1'},
        {title: 'Example 2', inline: 'span', classes: 'example2'},
        {title: 'Table styles'},
        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'},
		{title: 'Image Left', selector: 'img', 
        styles: {
            'float' : 'left', 
                    'margin': '0 10px 0 10px'
        }
    },
        {title: 'Image Right', selector: 'img', 
        styles: {
            'float' : 'right', 
                    'margin': '0 0 10px 10px'
            }
    }
    ],*/
		<?php 
		if ($this->getParam('style_format_active')=='1') {
			echo ($this->getParam('style_format_content')).",";
		}
		 
		?>
		
		
		
		
		//toolbar: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect forecolor backcolor | link unlink anchor | image media | print preview code" });
		toolbar: "<?php echo $this->getParam('tynitoolbar'.$profil).$sexternal_toolbar ;?>" });
		
		
		
	</script>

	<?php
	}
	
	
	/**
	 * Méthode qui encode une chaine de caractère en hexadecimal
	 *
	 * @parm	s		chaine de caractères à encoder
	 * @return	string	chane de caractères encodée en hexadecimal
	 * @author	Stephane F
	 **/
	public static function encodeBin2Hex($s) {

		$encode = '';
		for ($i = 0; $i < strlen($s); $i++) {
			$encode .= '%' . bin2hex($s[$i]);
		}
		return $encode;
	}

	/**
	 * Méthode qui protège les adresses emails contre le spam
	 *
	 * @parm	txt		chaine de caractères à protéger
	 * @return	string	chaine de caractères avec les adresses emails protégées
	 * @author	Stephane F, Francis
	 **/
	 
	//  
	public static function protectEmails($txt) {
		
		if(preg_match_all('/<a.+href=[\'"]mailto:([\._a-zA-Z0-9-@]+)((\?.*)?)[\'"]>([\._a-zA-Z0-9-@]+)<\/a>/i', $txt, $matches)) {
			
			foreach($matches[0] as $k => $v) {
				
				$string = spxtynimce::encodeBin2Hex('document.write(\''.$matches[0][$k].'\')');
				
				$txt = str_replace($matches[0][$k], '<script type="text/javascript">eval(unescape(\''.$string.'\'))</script>' , $txt);
	
			}
		}
		
		$s = preg_replace('/<input(\s+[^>]*)?>/i', '', $txt);
		$s = preg_replace('/<textarea(\s+[^>]*)?>.*?<\/textarea(\s+[^>]*)?>/i', '', $s);
		if(preg_match_all('/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i', $s, $matches)) {
			foreach($matches[0] as $k => $v) {
				$string = spxtynimce::encodeBin2Hex('document.write(\''.$matches[0][$k].'\')');
				$txt = str_replace($matches[0][$k], '<script type="text/javascript">eval(unescape(\''.$string.'\'))</script>' , $txt);
			}
		}
		
		
		return $txt;
	}
	

	/**
	 * Méthode qui protège les adresses emails contre le spam dans les articles
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function protectEmailsArticles() {

		echo '<?php
			$art["chapo"] = spxtynimce::protectEmails($art["chapo"]);
			$art["content"] = spxtynimce::protectEmails($art["content"]);
		?>';

	}

	/**
	 * Méthode qui protège les adresses emails contre le spam dans les pages statiques
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function protectEmailsStatics() {

		echo '<?php
			$output = spxtynimce::protectEmails($output);
		?>';

	}
	
	
	
	public function checkshortcodeplugins(){
		$this->aexternal_toolbar = array();
		$this->aexternal_external_plugins = array();
		
		$plxMotor = plxMotor::getInstance();
		$tmp = $plxMotor->plxPlugins->aPlugins;
		foreach($tmp as $key => $value) {
			if ($value->getParam("spxtynimce_plugin")){
				
				//echo ("buton = ".$key."\n<br>");
				array_push ($this->aexternal_external_plugins, $value->getParam("spxtynimce_externalplugin"));
				array_push ($this->aexternal_toolbar, $value->getParam("spxtynimce_toolbar"));
				
				
			}
			
		}
		/*
		echo ("<pre>");
		print_r($this->aexternal_external_plugins);
		echo ("</pre>");
		echo ("<pre>");
		print_r($this->aexternal_toolbar);
		echo ("</pre>");
		*/
		
	}
	

}
?>