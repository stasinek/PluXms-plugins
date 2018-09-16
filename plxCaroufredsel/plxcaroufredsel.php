<?php 

/**
 * Classe plxCaroufredsel
 *
 * @version 1.0
 * @date	01/12/2011
 * @author	Cyril MAGUIRE
 **/

class plxCaroufredsel extends plxPlugin {
	
	/**
	 * Constructeur de la classe
	 *
	 * @return	null
	 * @author	Cyril MAGUIRE  
	 **/
	public function __construct($default_lang) {
		
		# appel du constructeur de la classe plxPlugin (obligatoire) 
		parent::__construct($default_lang);
		
		# Ajoute des hooks
		# Pour les articles
		$this->addHook('plxMotorParseArticle', 'plxMotorParseArticle');
		$this->addHook('AdminArticleContent', 'AdminArticleContent');
		$this->addHook('AdminArticleInitData', 'AdminArticleInitData');
		$this->addHook('AdminArticlePostData', 'AdminArticlePostData');
		$this->addHook('AdminArticleParseData', 'AdminArticleParseData');
		$this->addHook('AdminEditArticle', 'AdminEditArticle');
		$this->addHook('AdminTopBottom', 'AdminTopBottom');
		$this->addHook('AdminFootEndBody', 'AdminFootEndBody');
		$this->addHook('AdminEditArticleXml', 'AdminEditArticleXml');
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('ThemeEndHead', 'caroufredselCss');
		$this->addHook('caroufredselContent', 'caroufredselContent');
		$this->addHook('caroufredselArticle', 'caroufredselArticle');
		$this->addHook('caroufredselHome', 'caroufredselHome');
		$this->addHook('caroufredselJs', 'caroufredselJs');
		$this->addHook('caroufredselMobileJs', 'caroufredselMobileJs');
		$this->addHook('caroufredselFooter', 'caroufredselFooter');
		$this->addHook('caroufredselFooterMobile', 'caroufredselFooterMobile');
		#Pour les pages statiques
		$this->addHook('AdminStatic', 'AdminStatic');
		$this->addHook('AdminStaticTop', 'AdminStaticTop');
		$this->addHook('AdminEditStatiquesXml', 'AdminEditStatiquesXml');
		$this->addHook('AdminEditStatique', 'AdminEditStatique');
		$this->addHook('AdminEditStatiquesUpdate', 'AdminEditStatiquesUpdate');
		$this->addHook('plxMotorGetStatiques', 'plxMotorGetStatiques');
		$this->addHook('caroufredselStaticContent', 'caroufredselStaticContent');
		$this->addHook('caroufredselStatic', 'caroufredselStatic');
		
	}

/**************************
*
*	LES ARTICLES
*
**************************/

	
	/**
	 * Méthode pour le hook plxMotorParseArticle
	 *
	 * Récupère la valeur de mavariable enregistrée précédemment dans le fichier xml
	 * et l'affecte à l'index mavariable du tableau $art. Permet d'afficher la valeur de mavariable dans la vue
	 * via $plxShow->plxMotor->plxRecord_arts->f('mavariable')
	 *
	 * @return	void
	 * @author	Cyril MAGUIRE 
	 **/
	public function plxMotorParseArticle() {
		echo "<?php 
			\$cfsel =  plxUtils::getValue(\$iTags['cfsel'][0]);
			\$art['cfsel'] = plxUtils::getValue(\$values[\$cfsel]['value']); 
			
			\$width =  plxUtils::getValue(\$iTags['width'][0]);
			\$art['width'] = plxUtils::getValue(\$values[\$width]['value']);
			
			\$height =  plxUtils::getValue(\$iTags['height'][0]); 
			\$art['height'] = plxUtils::getValue(\$values[\$height]['value']);
			
			\$direction =  plxUtils::getValue(\$iTags['direction'][0]); 
			\$art['direction'] = plxUtils::getValue(\$values[\$direction]['value']);
			
			\$infinite =  plxUtils::getValue(\$iTags['infinite'][0]); 
			\$art['infinite'] = plxUtils::getValue(\$values[\$infinite]['value']);
			
			\$auto =  plxUtils::getValue(\$iTags['auto'][0]); 
			\$art['auto'] = plxUtils::getValue(\$values[\$auto]['value']); 
			
			\$cfauthor =  plxUtils::getValue(\$iTags['cfauthor'][0]);
			\$art['cfauthor'] = plxUtils::getValue(\$values[\$cfauthor]['value']);
			 
			\$cfartId =  plxUtils::getValue(\$iTags['cfartId'][0]);
			\$art['cfartId'] = plxUtils::getValue(\$values[\$cfartId]['value']); 
			 
			\$cfprevnext =  plxUtils::getValue(\$iTags['cfprevnext'][0]);
			\$art['cfprevnext'] = plxUtils::getValue(\$values[\$cfprevnext]['value']);
		?>"; 
	}
	
	/**
	 * Méthode pour le hook AdminArticleContent
	 *
	 * Affiche le formulaire dans la vue admin/article.php permettant la création / l'édition d'un article
	 *
	 * @return	string
	 * @author	Cyril MAGUIRE 
	 **/
	public function AdminArticleContent() { 
		$string = <<<END
		<?php
		\$up = \$down = \$right = \$ifalse = \$afalse = '';
		\$left = ' selected="selected"';
		\$itrue = ' selected="selected"';
		\$atrue = ' selected="selected"';
		if (\$cfsel != 'on') {
			\$selected = '';
			\$display = 'none';
		} else {
			\$selected = ' checked="checked"';
 			\$display = 'block';
			switch (\$direction) {
				case 'up':
					\$up = ' selected="selected"';
					\$down = \$right = \$left = '';
					break;
				case 'down':
					\$down = ' selected="selected"';
					\$up = \$right = \$left = '';
					break;
				case 'left':
					\$left = ' selected="selected"';
					\$up = \$down = \$right = '';
					break;
				case 'right':
					\$right = ' selected="selected"';
					\$up = \$down = \$left = '';
					break;
				default:
					\$left = ' selected="selected"';
					\$up = \$down = \$right = '';
					break;
			}
			switch (\$infinite) {
				case 'true':
					\$itrue = ' selected="selected"';
					\$ifalse = '';
					break;
				case 'false':
					\$ifalse = ' selected="selected"';
					\$itrue = '';
					break;
				default:
					\$itrue = ' selected="selected"';
					\$ifalse = '';
					break;
			}
			switch (\$auto) {
				case 'true':
					\$atrue = ' selected="selected"';
					\$afalse = '';
					break;
				case 'false':
					\$afalse = ' selected="selected"';
					\$atrue = '';
					break;
				default:
					\$atrue = ' selected="selected"';
					\$afalse = '';
					break;
			}
		}
			\$cfprevnextchecked = (\$cfprevnext == 'on') ? ' checked="checked"' : '';
		
		echo '
			<label for="cfsel">Cet article contient-il un diaporama ?&nbsp;:</label><input type="checkbox" id="cfsel" name="cfsel"'.\$selected.' onclick="$(\'#CfselOptions\').toggle(\'slide\');"/>
			<p>&nbsp</p>
			<div id="CfselOptions">
				<p><label for="width">Largeur du diaporama (nombre, null, variable ou auto)<br/>
					<em>Dans le doute, laissez vide</em> :</label></p>
					<input type="text" name="width" value="'.\$width.'" id="width" />
					<p>&nbsp</p>
				<p><label for="height">Hauteur du diaporama  (nombre ou auto)<br/>
					<em>Dans le doute, laissez vide</em> :</label></p>
					<input type="text" name="height" value="'.\$height.'" id="height" />
					<p>&nbsp</p>
				<p><label for="direction">Direction du diaporama :</label></p>
				<select name="direction" id="direction">
					<option value="up"'.\$up.'>haut</option>
					<option value="down"'.\$down.'>bas</option>
					<option value="left"'.\$left.'>gauche</option>
					<option value="right"'.\$right.'>droite</option>
				</select>
					<p>&nbsp</p>
				<p><label for="infinite">Diaporama en boucle :</label></p>
				<select name="infinite" id="infinite">
					<option value="true"'.\$itrue.'>oui</option>
					<option value="false"'.\$ifalse.'>non</option>
				</select>
					<p>&nbsp</p>
				<p><label for="auto">Diaporama automatique :</label></p>
				<select name="auto" id="auto">
					<option value="true"'.\$atrue.'>oui</option>
					<option value="false"'.\$afalse.'>non</option>
				</select>
					<p>&nbsp</p>
				<label for="cfprevnext">Affichage des liens "prev" et "next" ?&nbsp;:</label><input type="checkbox" id="cfprevnext" name="cfprevnext"'.\$cfprevnextchecked.'/>
					<p>&nbsp</p>
			</div>
			<script type="text/javascript">
				jQuery(function($) {
					$(\'#CfselOptions\').css({\'display\':\''.\$display.'\'});
				});
			</script>
			';
		?>
END;
	
		echo $string;
	}
	
	/**
	 * Méthode pour le hook AdminArticleInitData
	 *
	 * Permet de donner des valeurs par défaut lorsqu'un article est créé
	 *
	 * @author	Cyril MAGUIRE
	 */
	public function AdminArticleInitData()
	{
		$string =<<<END
		<?php
			\$width = 600;
			\$height = '';
			\$direction = 'left';
			\$cfprevnext = '';
		?>
END;
		echo $string;
	}
	
	/**
	 * Méthode pour le hook AdminArticlePostData
	 *
	 * Permet d'alimenter des variables lorsque le formulaire de création d'un article est posté
	 *
	 * @author	Cyril MAGUIRE
	 */
	public function AdminArticlePostData()
	{
		$string =<<<END
		 <?php 
			\$cfsel = \$_POST['cfsel'];
			\$width = \$_POST['width'];
			\$height = \$_POST['height'];
			\$direction = \$_POST['direction'];
			\$infinite = \$_POST['infinite'];
			\$auto = \$_POST['auto'];
			\$cfprevnext = \$_POST['cfprevnext'];
		?>
END;
		echo $string;
	}
	
	/**
	 * Méthode pour le hook AdminArticleParseData
	 *
	 * Affectation de la valeur de $result['mavariable'] à $mavariable précédemment récupérée par plxAdmin via 
	 * $art de plxMotor (voir plxShowConstruct plus haut)
	 *
	 * @author	Cyril MAGUIRE 
	 **/
	public function AdminArticleParseData()
	{
		$string =<<<END
		 <?php 
			\$cfsel = \$result['cfsel'];
			\$width = \$result['width'];
			\$height = \$result['height']; 
			\$direction = \$result['direction']; 
			\$infinite = \$result['infinite']; 
			\$auto = \$result['auto']; 
			\$cfprevnext = \$result['cfprevnext']; 
		?>
END;
		echo $string;
	}
	
	/**
	 * Méthode pour le hook AdminEditArticle
	 *
	 * Crée l'index mavariable dans le tableau $content nécessaire à la classe plxAdmin pour créer le fichier xml (data)
	 * Crée, s'il n'existe pas, le dossier dans lequel seront stockées les images du diaporama
	 * 
	 * @author	Cyril MAGUIRE 
	 **/
	public function AdminEditArticle() { 
		
		echo "<?php
			//si l'article n'a pas de diaporama
			if (\$content['cfsel'] != 'on') {
				\$content['width'] = '';
				\$content['height'] = '';
				\$content['direction'] = '';
				\$content['infinite'] = '';
				\$content['auto'] = '';
				\$content['cfprevnext'] = '';
			} else {
				
				
				//s'il y a un dossier par utilisateur
				if(\$this->aConf['userfolders']){

					if (\$_SESSION['profil'] == 0) {
						\$u = \$content['author'].'/';
					} else {
						\$u = '';
					}

					\$newdir = PLX_ROOT.'data/images/'.\$_SESSION['user'].'/diaporama-article-'.\$id;
				} else {
					\$u = '';
					\$newdir = PLX_ROOT.'data/images/diaporama-article-'.\$id;
				}
				if(!is_dir(\$newdir)) { # Si le dossier n'existe pas on le crée
					if(!@mkdir(\$newdir,0755,true)){
						return plxMsg::Error(L_PLXMEDIAS_NEW_FOLDER_ERR);
					}  else {
							if(\$content['artId'] == '0000' OR \$content['artId'] == ''){
								\$_SESSION['infoCfsel'] = '<div id=\"Cfsel\" class=\"notification success\"><p style=\"text-align:center;\">'.L_ARTICLE_SAVE_SUCCESSFUL.'</p><p>Les images du diaporama sont à mettre dans le dossier :</p><p style=\"text-align:center;\">'.\$u.'diaporama-article-'.\$id.'</p></div>';
							} else {
								\$_SESSION['infoCfsel'] = '<div id=\"Cfsel\" class=\"notification success\"><p style=\"text-align:center;\">'.L_ARTICLE_MODIFY_SUCCESSFUL.'</p><p>Les images du diaporama sont à mettre dans le dossier :</p><p style=\"text-align:center;\">'.\$u.'diaporama-article-'.\$id.'</p></div>';
							}
					}
					
				} else {
						if(\$content['artId'] == '0000' OR \$content['artId'] == ''){
							\$_SESSION['infoCfsel'] = '<div id=\"Cfsel\" class=\"notification success\"><p style=\"text-align:center;\">'.L_ARTICLE_SAVE_SUCCESSFUL.'</p><p>Les images du diaporama sont à mettre dans le dossier :</p><p style=\"text-align:center;\">'.\$u.'diaporama-article-'.\$id.'</p></div>';
						} else {
							\$_SESSION['infoCfsel'] = '<div id=\"Cfsel\" class=\"notification success\"><p style=\"text-align:center;\">'.L_ARTICLE_MODIFY_SUCCESSFUL.'</p><p>Les images du diaporama sont à mettre dans le dossier :</p><p style=\"text-align:center;\">'.\$u.'diaporama-article-'.\$id.'</p></div>';
						}
				}
			}
			
			
		?>";
	}
	
	/**
	 * Méthode permettant l'affichage de la fonction javascript pour les notifications utilisateurs
	 * 
	 * @author	Cyril MAGUIRE
	 */
	public function AdminTopBottom()
	{
		echo "<?php echo \$_SESSION['infoCfsel'];\$_SESSION['infoCfsel'] = ''?>";
	}
	
	/**
	 * Méthode permettant l'affichage de la fonction javascript pour les notifications utilisateurs
	 * 
	 * @author	Cyril MAGUIRE
	 */
	public function AdminFootEndBody()
	{
	echo "
	<script type=\"text/javascript\">
		function setMsgCfsel() {
			if(document.getElementById('Cfsel')) {

				objDiv = document.getElementById('Cfsel');
				objSidebar = document.getElementById('sidebar')
				if (typeof window.innerWidth != 'undefined') {
					wndWidth = window.innerWidth;
				}
				else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth !='undefined' && document.documentElement.clientWidth != 0) {
					wndWidth = document.documentElement.clientWidth;
				}
				else {
					wndWidth = document.getElementsByTagName('body')[0].clientWidth;
				}
				xpos = Math.round(((wndWidth-objDiv.offsetWidth)/2)-objSidebar.offsetWidth);
				objDiv.style.left=xpos+'px';
				fadeOut('Cfsel');
			}
		}
		setMsgCfsel();
	</script>
		";
	}
	
	/**
	 * Méthode pour le hook AdminEditArticleXml
	 *
	 * Enregistre la valeur de $mavariable dans le fichier xml, entre les tags <mavariable></mavariable>
	 *
	 * @author	Cyril MAGUIRE 
	 **/
	public function AdminEditArticleXml() {
		echo "<?php 
			\$xml .= \"\t\".'<cfsel><![CDATA['.plxUtils::cdataCheck(\$content['cfsel']).']]></cfsel>'.\"\n\"; 
			\$xml .= \"\t\".'<width><![CDATA['.plxUtils::cdataCheck(\$content['width']).']]></width>'.\"\n\"; 
			\$xml .= \"\t\".'<height><![CDATA['.plxUtils::cdataCheck(\$content['height']).']]></height>'.\"\n\"; 
			\$xml .= \"\t\".'<direction><![CDATA['.plxUtils::cdataCheck(\$content['direction']).']]></direction>'.\"\n\"; 
			\$xml .= \"\t\".'<infinite><![CDATA['.plxUtils::cdataCheck(\$content['infinite']).']]></infinite>'.\"\n\"; 
			\$xml .= \"\t\".'<auto><![CDATA['.plxUtils::cdataCheck(\$content['auto']).']]></auto>'.\"\n\"; 
			\$xml .= \"\t\".'<cfauthor><![CDATA['.plxUtils::cdataCheck(\$content['author']).']]></cfauthor>'.\"\n\"; 
			\$xml .= \"\t\".'<cfartId><![CDATA['.plxUtils::cdataCheck(\$id).']]></cfartId>'.\"\n\"; 
			\$xml .= \"\t\".'<cfprevnext><![CDATA['.plxUtils::cdataCheck(\$content['cfprevnext']).']]></cfprevnext>'.\"\n\"; 
		?>"; 
	}
	
	/**
	 * Méthode pour le hook caroufredselCss permettant d'afficher le fichier css
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselCss()
	{
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxCaroufredsel/css/style.css" />'."\n";
	}
	
	/**
	 * Méthode permettant d'afficher le fichier caroufredsel.js
	 *
	 * @author Cyril MAGUIRE
	 */
	public function ThemeEndHead()
	{
		echo "\t".'<script type="text/javascript">
				/* <![CDATA[ */
				!window.jQuery && document.write(\'<script  type="text/javascript" src="'.PLX_PLUGINS.'plxCaroufredsel/js/jquery-1.7.1.min.js"><\/script>\');
				/* !]]> */
			</script>'."\n";
		echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxCaroufredsel/js/caroufredsel.js"></script>'."\n";
		echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxCaroufredsel/js/jquery.easing.1.3.js"></script>'."\n";
	}
	
	/**
	 * Méthode pour le hook caroufredselContent permettant d'afficher le diaporama dans l'article
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselContent($params)
	{
		//S'il y a un dossier par utilisateur
		if (!empty($params['userfolder'])) {
			$dir = opendir(PLX_ROOT.'data/images/'.$params['cfauthor'].'/diaporama-article-'.$params['artId'].'/');
			$lienDir = 'data/images/'.$params['cfauthor'].'/diaporama-article-'.$params['artId'].'/';
		}else {
			$dir = opendir(PLX_ROOT.'data/images/diaporama-article-'.$params['artId'].'/');
			$lienDir = 'data/images/diaporama-article-'.$params['artId'].'/';
		}
		
		$listImgs = array();
		$listImg = $thumb = '';
		$larg = 0;
		//Listing des images du dossier
		
		while($file = readdir($dir)) {
			if($file != '..' && $file != '.' && strpos($file,'.tb.') === false && strpos($file,'.DS_Store') === false) {
				$listImgs[] = $file;
			}
		}
		natsort($listImgs);
		foreach ($listImgs as $key => $file) {
			$ext = substr($file,(strpos($file,'.')+1));
			$thumb = str_replace('.'.$ext,'.tb.'.$ext,$file);
			$attr = getimagesize($lienDir.$thumb);
			if ($larg < $attr[0]) {
				$larg = $attr[0];
			}
			$listImg .= '
					<a href="'.$lienDir.$file.'" class="zoombox zgallery'.$params['artId'].'">
						<img src="'.$lienDir.$thumb.'" '.$attr[3].' alt="'.$file.'" />
					</a>'."\n";
		}
		
		if ($params['cfprevnext'] == 'on') {
			$prevnext = '
				<a class="prev" id="diaporama-'.$params['cfauthor'].'-'.$params['artId'].'-prev" href="#"><span>prev</span></a>
				<a class="next"  id="diaporama-'.$params['cfauthor'].'-'.$params['artId'].'-next" href="#"><span>next</span></a>
			';
		} else {
			$prevnext = '';
		}
		//Affichage des images
	 	echo '
	
			<div class="caroussel">
				<div  id="diaporama-'.$params['cfauthor'].'-'.$params['artId'].'">
				    '.$listImg.'
				</div>
				<div class="clearfix">&nbsp;</div>
					'.$prevnext.'
				<div class="pagination"  id="diaporama-'.$params['cfauthor'].'-'.$params['artId'].'-pages"></div>
				<div style="display:none;" id="size-'.$params['cfauthor'].'-'.$params['artId'].'">'.$larg.'</div>
			</div>
		';
	}
	
	
	/**
	 * Méthode pour le hook caroufredselJs permettant d'afficher le code javascript en bas de page
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselJs($params)
	{
		if (!in_array($params['width'],array('auto','variable','null'))) {
			$params['width'] = intval($params['width']);
		} else {
			$params['width'] = "'".$params['width']."'";
		}
		if ($params['width'] == 0 || $params['width'] == '' || $params['width'] == 'null' || $params['width'] == null) {
			$params['width'] = 620;
		}
		if (!in_array($params['height'],array('auto','variable','null'))) {
			$params['height'] = intval($params['height']);
		} else {
			$params['height'] = '"auto"';
		}
		
		if ($params['height'] == 0 || $params['height'] == '' || $params['height'] == 'null' || $params['height'] == null) {
			$params['height'] = '"auto"';
		}

		if ($params['mode'] == 'static') {
			
			if ($params['cfprevnext'] == 'on') {
				$prevnext = ',
						prev	: {	
							button	: "#diaporama-static-'.$params['id'].'-prev",
							key	: "left"
						},
						next	: { 
							button	: "diaporama-static-'.$params['id'].'-next",
							key	: "right"
						}';
			} else {
				$prevnext = '';
			}
			
			echo '
			
				var largeur = parseInt($("#size-'.$params['cfauthor'].'-'.$params['artId'].'").text());
				var nb_img = Math.floor('.$params['width'].' / (largeur + 30));
				$("#diaporama-static-'.$params['id'].'").carouFredSel({
					width : (largeur*nb_img) + (35*nb_img),
					height : '.$params['height'].',
					items: {
						width: largeur + 30,
						height: "variable"
					},
					scroll: {
						pauseOnHover: true
					},
					direction : "'.$params['direction'].'",
					infinite: '.$params['infinite'].',
					auto    : '.$params['auto'].',
					scroll	: {
						items	: 1,
						pauseOnHover : true
					}'.$prevnext.',
					pagination : "#diaporama-static-'.$params['id'].'-pages"
				});
			';
		} else {
			
			if ($params['cfprevnext'] == 'on') {
				$prevnext = ',
						prev	: {	
							button	: "#diaporama-'.$params['cfauthor'].'-'.$params['artId'].'-prev",
							key	: "left"
						},
						next	: { 
							button	: "#diaporama-'.$params['cfauthor'].'-'.$params['artId'].'-next",
							key	: "right"
						}';
			} else {
				$prevnext = '';
			}
			echo '
			
				var largeur = parseInt($("#size-'.$params['cfauthor'].'-'.$params['artId'].'").text());
				var nb_img = Math.floor('.$params['width'].' / (largeur + 30));
				$("#diaporama-'.$params['cfauthor'].'-'.$params['artId'].'").carouFredSel({
					width : (largeur*nb_img) + (35*nb_img),
					height : '.$params['height'].',
					items: {
						width: largeur + 30,
						height: "variable"
					},
					scroll: {
						pauseOnHover: true
					},
					direction : "'.$params['direction'].'",
					infinite: '.$params['infinite'].',
					auto    : '.$params['auto'].',
					scroll	: {
						items	: 1,
						pauseOnHover : true
					}'.$prevnext.',
					pagination : "#diaporama-'.$params['cfauthor'].'-'.$params['artId'].'-pages"
				});
			';
		}
	}
	
	/**
	 * Méthode pour le hook caroufredselMobileJs permettant d'afficher le code javascript en bas de page pour les themes mobile
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselMobileJs($params)
	{
		if (!in_array($params['width'],array('auto','variable','null'))) {
			$params['width'] = intval($params['width']);
		} else {
			$params['width'] = "'".$params['width']."'";
		}
		if ($params['width'] == 0 || $params['width'] == '' || $params['width'] == 'null' || $params['width'] == null) {
			$params['width'] = 100;
		}
		if (!in_array($params['height'],array('auto','variable','null'))) {
			$params['height'] = intval($params['height']);
		} else {
			$params['height'] = '"auto"';
		}
		
		if ($params['height'] == 0 || $params['height'] == '' || $params['height'] == 'null' || $params['height'] == null) {
			$params['height'] = '"auto"';
		}

		if ($params['mode'] == 'static') {
			
			if ($params['cfprevnext'] == 'on') {
				$prevnext = ',
						prev	: {	
							button	: "#diaporama-static-'.$params['id'].'-prev",
							key	: "left"
						},
						next	: { 
							button	: "diaporama-static-'.$params['id'].'-next",
							key	: "right"
						}';
			} else {
				$prevnext = '';
			}
			
			echo '
			
				var largeur = parseInt($("#size-'.$params['cfauthor'].'-'.$params['artId'].'").text());
				$("#diaporama-static-'.$params['id'].'").carouFredSel({
					width : largeur + 35,
					height : '.$params['height'].',
					items: {
						width: largeur + 30,
						height: "variable"
					},
					scroll: {
						pauseOnHover: true
					},
					direction : "'.$params['direction'].'",
					infinite: '.$params['infinite'].',
					auto    : '.$params['auto'].',
					scroll	: {
						items	: 1,
						pauseOnHover : true
					}'.$prevnext.',
					pagination : "#diaporama-static-'.$params['id'].'-pages"
				});
			';
		} else {
			
			if ($params['cfprevnext'] == 'on') {
				$prevnext = ',
						prev	: {	
							button	: "#diaporama-'.$params['cfauthor'].'-'.$params['artId'].'-prev",
							key	: "left"
						},
						next	: { 
							button	: "#diaporama-'.$params['cfauthor'].'-'.$params['artId'].'-next",
							key	: "right"
						}';
			} else {
				$prevnext = '';
			}
			echo '
			
				var largeur = parseInt($("#size-'.$params['cfauthor'].'-'.$params['artId'].'").text());
				var nb_img = Math.floor('.$params['width'].' / (largeur + 30));
				$("#diaporama-'.$params['cfauthor'].'-'.$params['artId'].'").carouFredSel({
					width : (largeur*nb_img) + (35*nb_img),
					height : '.$params['height'].',
					items: {
						width: largeur + 30,
						height: "variable"
					},
					scroll: {
						pauseOnHover: true
					},
					direction : "'.$params['direction'].'",
					infinite: '.$params['infinite'].',
					auto    : '.$params['auto'].',
					scroll	: {
						items	: 1,
						pauseOnHover : true
					}'.$prevnext.',
					pagination : "#diaporama-'.$params['cfauthor'].'-'.$params['artId'].'-pages"
				});
			';
		}
	}
	


/**************************
*
*	LES PAGES STATIQUES
*
**************************/

	/**
	 * Affiche le formulaire dans la vue admin/statique.php permettant la création / l'édition de la page
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function AdminStatic() {
		$string = <<<END
		<?php
		\$up = \$down = \$right = \$ifalse = \$afalse = '';
		\$left = ' selected="selected"';
		\$itrue = ' selected="selected"';
		\$atrue = ' selected="selected"';
		if (\$cfsel != 'on') {
			\$selected = '';
			\$display = 'none';
		} else {
			\$selected = ' checked="checked"';
			\$display = 'block';
			switch (\$direction) {
				case 'up':
					\$up = ' selected="selected"';
					\$down = \$right = \$left = '';
					break;
				case 'down':
					\$down = ' selected="selected"';
					\$up = \$right = \$left = '';
					break;
				case 'left':
					\$left = ' selected="selected"';
					\$up = \$down = \$right = '';
					break;
				case 'right':
					\$right = ' selected="selected"';
					\$up = \$down = \$left = '';
					break;
				default:
					\$left = ' selected="selected"';
					\$up = \$down = \$right = '';
					break;
			}
			switch (\$infinite) {
				case 'true':
					\$itrue = ' selected="selected"';
					\$ifalse = '';
					break;
				case 'false':
					\$ifalse = ' selected="selected"';
					\$itrue = '';
					break;
				default:
					\$itrue = ' selected="selected"';
					\$ifalse = '';
					break;
			}
			switch (\$auto) {
				case 'true':
					\$atrue = ' selected="selected"';
					\$afalse = '';
					break;
				case 'false':
					\$afalse = ' selected="selected"';
					\$atrue = '';
					break;
				default:
					\$atrue = ' selected="selected"';
					\$afalse = '';
					break;
			}
		}
		\$cfprevnextchecked = (\$cfprevnext == 'on') ? ' checked="checked"' : '';
		
		echo '
			<label for="cfsel">Cette page contient-elle un diaporama ?&nbsp;:</label><input type="checkbox" id="cfsel" name="cfsel"'.\$selected.' onclick="$(\'#CfselOptions\').toggle(\'slide\');"/>
			<p>&nbsp</p>
			<div id="CfselOptions">
				<p><label for="width">Largeur du diaporama (nombre, null, variable ou auto)<br/>
					<em>Dans le doute, laissez vide</em> :</label></p>
					<input type="text" name="width" value="'.\$width.'" id="width" />
					<p>&nbsp</p>
				<p><label for="height">Hauteur du diaporama  (nombre ou auto)<br/>
					<em>Dans le doute, laissez vide</em> :</label></p>
					<input type="text" name="height" value="'.\$height.'" id="height" />
					<p>&nbsp</p>
				<p><label for="direction">Direction du diaporama :</label></p>
				<select name="direction" id="direction">
					<option value="up"'.\$up.'>haut</option>
					<option value="down"'.\$down.'>bas</option>
					<option value="left"'.\$left.'>gauche</option>
					<option value="right"'.\$right.'>droite</option>
				</select>
					<p>&nbsp</p>
				<p><label for="infinite">Diaporama en boucle :</label></p>
				<select name="infinite" id="infinite">
					<option value="true"'.\$itrue.'>oui</option>
					<option value="false"'.\$ifalse.'>non</option>
				</select>
					<p>&nbsp</p>
				<p><label for="auto">Diaporama automatique :</label></p>
				<select name="auto" id="auto">
					<option value="true"'.\$atrue.'>oui</option>
					<option value="false"'.\$afalse.'>non</option>
				</select>
					<p>&nbsp</p>
				<label for="cfprevnext">Affichage des liens "prev" et "next" ?&nbsp;:</label><input type="checkbox" id="cfprevnext" name="cfprevnext"'.\$cfprevnextchecked.'/>
					<p>&nbsp</p>
			</div>
			<script type="text/javascript">
				jQuery(function($) {
					$(\'#CfselOptions\').css({\'display\':\''.\$display.'\'});
				});
			</script>
			';
		?>
END;
	
		echo $string;
	}

	/**
	 * Méthode qui ajoute les paramètres du diaporama dans la chaine xml à sauvegarder dans statiques.xml
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function plxAdminEditStatiquesXml() {
		echo "<?php 
			\$xml .= '<cfsel><![CDATA['.plxUtils::cdataCheck(\$static['cfsel']).']]></cfsel>'; 
			\$xml .= '<width><![CDATA['.plxUtils::cdataCheck(\$static['width']).']]></width>'; 
			\$xml .= '<height><![CDATA['.plxUtils::cdataCheck(\$static['height']).']]></height>'; 
			\$xml .= '<direction><![CDATA['.plxUtils::cdataCheck(\$static['direction']).']]></direction>'; 
			\$xml .= '<infinite><![CDATA['.plxUtils::cdataCheck(\$static['infinite']).']]></infinite>'; 
			\$xml .= '<auto><![CDATA['.plxUtils::cdataCheck(\$static['auto']).']]></auto>'; 
			\$xml .= '<cfauthor><![CDATA['.plxUtils::cdataCheck(\$static['cfauthor']).']]></cfauthor>'; 
			\$xml .= '<cfartId><![CDATA['.plxUtils::cdataCheck(\$id).']]></cfartId>';
			\$xml .= '<cfprevnext><![CDATA['.plxUtils::cdataCheck(\$static['cfprevnext']).']]></cfprevnext>';
		?>";
    }

	/**
	 * Méthode qui récupère les paramètres du diaporama saisis lors de l'édition de la page statique
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function plxAdminEditStatique() {
		echo "<?php 
				//si l'article n'a pas de diaporama
				if (\$content['cfsel'] != 'on') {
					\$content['width'] = '';
					\$content['height'] = '';
					\$content['direction'] = '';
					\$content['infinite'] = '';
					\$content['auto'] = '';
					\$content['cfprevnext'] = '';
				} else {

					\$this->aStats[\$content['id']]['cfsel'] = \$content['cfsel'];
					\$this->aStats[\$content['id']]['width'] = \$content['width'];
					\$this->aStats[\$content['id']]['height'] = \$content['height'];
					\$this->aStats[\$content['id']]['direction'] = \$content['direction'];
					\$this->aStats[\$content['id']]['infinite'] = \$content['infinite'];
					\$this->aStats[\$content['id']]['auto'] = \$content['auto'];
					\$this->aStats[\$content['id']]['cfprevnext'] = \$content['cfprevnext'];
					\$this->aStats[\$content['id']]['cfauthor'] = \$_SESSION['user'];

					//s'il y a un dossier par utilisateur
					if(\$this->aConf['userfolders']){

						if (\$_SESSION['profil'] == 0) {
							\$u = \$_SESSION['user'].'/';
						} else {
							\$u = '';
						}

						\$newdir = PLX_ROOT.'data/images/'.\$_SESSION['user'].'/diaporama-static-'.\$content['id'];
					} else {
						\$u = '';
						\$newdir = PLX_ROOT.'data/images/diaporama-static-'.\$content['id'];
					}
					if(!is_dir(\$newdir)) { # Si le dossier n'existe pas on le crée
						if(!@mkdir(\$newdir,0755,true)){
							return plxMsg::Error(L_PLXMEDIAS_NEW_FOLDER_ERR);
						}  else {
								\$_SESSION['infoCfsel'] = '<div id=\"Cfsel\" class=\"notification success\"><p style=\"text-align:center;\">'.L_SAVE_SUCCESSFUL.'</p><p>Les images du diaporama sont à mettre dans le dossier :</p><p style=\"text-align:center;\">'.\$u.'diaporama-static-'.\$content['id'].'</p></div>';
						}

					} else {
							\$_SESSION['infoCfsel'] = '<div id=\"Cfsel\" class=\"notification success\"><p style=\"text-align:center;\">'.L_SAVE_SUCCESSFUL.'</p><p>Les images du diaporama sont à mettre dans le dossier :</p><p style=\"text-align:center;\">'.\$u.'diaporama-static-'.\$content['id'].'</p></div>';
					}
				}
			?>";
    }

	/**
	 * Méthode qui récupère les paramètres du diaporama saisis lors de l'édition de la page statique pour
	 * mettre à jour les données enregistrées
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function plxAdminEditStatiquesUpdate()
	{
		echo "<?php 
				//si l'article n'a pas de diaporama
				if (\$content['cfsel'] != 'on') {
					\$content['width'] = '';
					\$content['height'] = '';
					\$content['direction'] = '';
					\$content['infinite'] = '';
					\$content['auto'] = '';
					\$content['cfprevnext'] = '';
				} else {

					\$this->aStats[\$static_id]['cfsel'] = \$content['cfsel'];
					\$this->aStats[\$static_id]['width'] = \$content['width'];
					\$this->aStats[\$static_id]['height'] = \$content['height'];
					\$this->aStats[\$static_id]['direction'] = \$content['direction'];
					\$this->aStats[\$static_id]['infinite'] = \$content['infinite'];
					\$this->aStats[\$static_id]['auto'] = \$content['auto'];
					\$this->aStats[\$static_id]['cfprevnext'] = \$content['cfprevnext'];
					\$this->aStats[\$static_id]['cfauthor'] = \$content['author'];

				}
			?>";
	}
	
	/**
	 * Méthode qui récupère les paramètres du diaporama stockés dans le fichier xml statiques.xml
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function plxMotorGetStatiques() {
		echo "<?php 
			\$cfsel = plxUtils::getValue(\$iTags['cfsel'][\$i]);
			\$this->aStats[\$number]['cfsel']=plxUtils::getValue(\$values[\$cfsel]['value']); 
			\$width = plxUtils::getValue(\$iTags['width'][\$i]);
			\$this->aStats[\$number]['width']=plxUtils::getValue(\$values[\$width]['value']);
			\$height = plxUtils::getValue(\$iTags['height'][\$i]);
			\$this->aStats[\$number]['height']=plxUtils::getValue(\$values[\$height]['value']);
			\$direction = plxUtils::getValue(\$iTags['direction'][\$i]);
			\$this->aStats[\$number]['direction']=plxUtils::getValue(\$values[\$direction]['value']);
			\$infinite = plxUtils::getValue(\$iTags['infinite'][\$i]);
			\$this->aStats[\$number]['infinite']=plxUtils::getValue(\$values[\$infinite]['value']);
			\$auto = plxUtils::getValue(\$iTags['auto'][\$i]);
			\$this->aStats[\$number]['auto']=plxUtils::getValue(\$values[\$auto]['value']);
			\$cfauthor = plxUtils::getValue(\$iTags['cfauthor'][\$i]);
			\$this->aStats[\$number]['cfauthor']=plxUtils::getValue(\$values[\$cfauthor]['value']);
			\$cfprevnext = plxUtils::getValue(\$iTags['cfprevnext'][\$i]);
			\$this->aStats[\$number]['cfprevnext']=plxUtils::getValue(\$values[\$cfprevnext]['value']);
		?>";
	}
	
	/**
	 * Méthode qui récupère les paramètres du diaporama stockés dans le fichier xml statiques.xml
	 * et qui les passe au formulaire de paramètrage du diaporama dans le fichier statique.php
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function AdminStaticTop()
	{
		echo "<?php
			\$cfsel = \$plxAdmin->aStats[\$id]['cfsel'];
			\$width = \$plxAdmin->aStats[\$id]['width'];
			\$height = \$plxAdmin->aStats[\$id]['height'];
			\$direction = \$plxAdmin->aStats[\$id]['direction'];
			\$infinite = \$plxAdmin->aStats[\$id]['infinite'];
			\$auto = \$plxAdmin->aStats[\$id]['auto'];
			\$cfauthor = \$plxAdmin->aStats[\$id]['cfauthor'];
			\$cfprevnext = \$plxAdmin->aStats[\$id]['cfprevnext'];
		?>";
	}
	
	/**
	 * Méthode pour le hook caroufredselContent permettant d'afficher le diaporama dans une page statique
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselStaticContent($params)
	{
		//S'il y a un dossier par utilisateur
		if (!empty($params['userfolder'])) {
			$dir = opendir(PLX_ROOT.'data/images/'.$params['cfauthor'].'/diaporama-static-'.$params['id'].'/');
			$lienDir = 'data/images/'.$params['cfauthor'].'/diaporama-static-'.$params['id'].'/';
		}else {
			$dir = opendir(PLX_ROOT.'data/images/diaporama-static-'.$params['id'].'/');
			$lienDir = 'data/images/diaporama-static-'.$params['id'].'/';
		}
		
		$listImgs = array();
		$listImg = $thumb = '';
		$larg = 0;
		//Listing des images du dossier
		
		while($file = readdir($dir)) {
			if($file != '..' && $file != '.' && strpos($file,'.tb.') === false && strpos($file,'.DS_Store') === false) {
				$listImgs[] = $file;
			}
		}
		natsort($listImgs);
		foreach ($listImgs as $key => $file) {
			$ext = substr($file,(strpos($file,'.')+1));
			$thumb = str_replace('.'.$ext,'.tb.'.$ext,$file);
			$attr = getimagesize($lienDir.$thumb);
			if ($larg < $attr[0]) {
				$larg = $attr[0];
			}
			$listImg .= '
					<a href="'.$lienDir.$file.'" class="zoombox zgallery'.$params['id'].'">
						<img src="'.$lienDir.$thumb.'" '.$attr[3].' alt="'.$file.'" />
					</a>'."\n";
		}
		if ($params['cfprevnext'] == 'on') {
			$prevnext = '
				<a class="prev" id="diaporama-static-'.$params['id'].'-prev" href="#"><span>prev</span></a>
				<a class="next"  id="diaporama-static-'.$params['id'].'-next" href="#"><span>next</span></a>
			';
		} else {
			$prevnext = '';
		}
		//Affichage des images
	 	echo '
	
			<div class="caroussel">
				<div  id="diaporama-static-'.$params['id'].'">
				    '.$listImg.'
				</div>
				<div class="clearfix">&nbsp;</div>
					'.$prevnext.'
				<div class="pagination"  id="diaporama-static-'.$params['id'].'-pages"></div>
				<div style="display:none;" id="size-'.$params['cfauthor'].'-'.$params['artId'].'">'.$larg.'</div>
			</div>
		';
	}

/**************************
*
*	LES HOOKS SIMPLIFIES
*
**************************/
	/**
	 * Méthode permettant d'afficher le javascript en fonction de la page où on se trouve (home ou article)
	 * Permet de n'utiliser qu'un seul hook dans le footer
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselFooter()
	{
		$string =<<<END
			<?php 
				if (\$plxShow->mode() == 'home' || \$plxShow->mode() == 'categorie'){
					while(\$plxShow->plxMotor->plxRecord_arts->loop()){
						if (\$plxShow->plxMotor->plxRecord_arts->f('cfsel') == 'on'){
								eval(\$plxShow->callHook('caroufredselJs',array(
									'artId' 		=> \$plxShow->plxMotor->plxRecord_arts->f('cfartId'),
									'cfauthor'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfauthor'),
									'width' 		=> \$plxShow->plxMotor->plxRecord_arts->f('width'),
									'height' 		=> \$plxShow->plxMotor->plxRecord_arts->f('height'),
									'direction'		=> \$plxShow->plxMotor->plxRecord_arts->f('direction'),
									'infinite'		=> \$plxShow->plxMotor->plxRecord_arts->f('infinite'),
									'auto'		=> \$plxShow->plxMotor->plxRecord_arts->f('auto'),
									'cfprevnext'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfprevnext'),
									)
								));
						}
					}
				} elseif (\$plxShow->mode() == 'article'){
					if (\$plxShow->plxMotor->plxRecord_arts->f('cfsel') == 'on'){
							eval(\$plxShow->callHook('caroufredselJs',array(
								'artId' 		=> \$plxShow->plxMotor->plxRecord_arts->f('cfartId'),
								'cfauthor'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfauthor'),
								'width' 		=> \$plxShow->plxMotor->plxRecord_arts->f('width'),
								'height' 		=> \$plxShow->plxMotor->plxRecord_arts->f('height'),
								'direction'		=> \$plxShow->plxMotor->plxRecord_arts->f('direction'),
								'infinite'		=> \$plxShow->plxMotor->plxRecord_arts->f('infinite'),
								'auto'		=> \$plxShow->plxMotor->plxRecord_arts->f('auto'),
								'cfprevnext'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfprevnext'),
								)
							));
					}	
				}elseif (\$plxShow->mode() == 'static'){
					if (\$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['cfsel'] == 'on'){
						eval(\$plxShow->callHook('caroufredselJs',array(
							'id' 		=> \$plxShow->plxMotor->cible,
							'cfauthor'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['cfauthor'],
							'width' 		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['width'],
							'height' 		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['height'],
							'direction'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['direction'],
							'infinite'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['infinite'],
							'auto'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['auto'],
							'cfprevnext'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['cfprevnext'],
							'mode' => 'static'
							)
						));
					}	
				} 
			?>
END;
			echo $string;
	}

	/**
	 * Méthode permettant d'afficher le javascript en fonction de la page où on se trouve (home ou article)
	 * Permet de n'utiliser qu'un seul hook dans le footer des thèmes mobile
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselFooterMobile()
	{
		$string =<<<END
			<?php 
				if (\$plxShow->mode() == 'home'){
					while(\$plxShow->plxMotor->plxRecord_arts->loop()){
						if (\$plxShow->plxMotor->plxRecord_arts->f('cfsel') == 'on'){
								eval(\$plxShow->callHook('caroufredselMobileJs',array(
									'artId' 		=> \$plxShow->plxMotor->plxRecord_arts->f('cfartId'),
									'cfauthor'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfauthor'),
									'width' 		=> \$plxShow->plxMotor->plxRecord_arts->f('width'),
									'height' 		=> \$plxShow->plxMotor->plxRecord_arts->f('height'),
									'direction'		=> \$plxShow->plxMotor->plxRecord_arts->f('direction'),
									'infinite'		=> \$plxShow->plxMotor->plxRecord_arts->f('infinite'),
									'auto'		=> \$plxShow->plxMotor->plxRecord_arts->f('auto'),
									'cfprevnext'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfprevnext'),
									)
								));
						}
					}
				} elseif (\$plxShow->mode() == 'article'){
					if (\$plxShow->plxMotor->plxRecord_arts->f('cfsel') == 'on'){
							eval(\$plxShow->callHook('caroufredselMobileJs',array(
								'artId' 		=> \$plxShow->plxMotor->plxRecord_arts->f('cfartId'),
								'cfauthor'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfauthor'),
								'width' 		=> \$plxShow->plxMotor->plxRecord_arts->f('width'),
								'height' 		=> \$plxShow->plxMotor->plxRecord_arts->f('height'),
								'direction'		=> \$plxShow->plxMotor->plxRecord_arts->f('direction'),
								'infinite'		=> \$plxShow->plxMotor->plxRecord_arts->f('infinite'),
								'auto'		=> \$plxShow->plxMotor->plxRecord_arts->f('auto'),
								'cfprevnext'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfprevnext'),
								)
							));
					}	
				}elseif (\$plxShow->mode() == 'static'){
					if (\$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['cfsel'] == 'on'){
						eval(\$plxShow->callHook('caroufredselMobileJs',array(
							'id' 		=> \$plxShow->plxMotor->cible,
							'cfauthor'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['cfauthor'],
							'width' 		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['width'],
							'height' 		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['height'],
							'direction'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['direction'],
							'infinite'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['infinite'],
							'auto'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['auto'],
							'cfprevnext'		=> \$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['cfprevnext'],
							'mode' => 'static'
							)
						));
					}	
				} 
			?>
END;
			echo $string;
	}
	
	/**
	 * Méthode permettant d'utiliser un hook simplifié dans le corps de l'article
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselArticle()
	{
		$string =<<<END
			<?php 
			if (\$plxShow->plxMotor->plxRecord_arts->f('cfsel') == 'on'){
					eval(\$plxShow->callHook('caroufredselContent',array(
						'userfolder'	=> \$plxShow->plxMotor->aConf['userfolders'],
						'artId' 		=> \$plxShow->plxMotor->plxRecord_arts->f('cfartId'),
						'cfauthor'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfauthor'),
						'cfprevnext'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfprevnext'),
						)
					));
			}	
			?>
END;
		echo $string;
	}

	/**
	 * Méthode permettant d'utiliser un hook simplifié dans le corps des pages statiques
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselStatic()
	{
		$string =<<<END
		 <?php 
			if (plxUtils::strCheck(\$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['cfsel']) == 'on'){
					eval(\$plxShow->callHook('caroufredselStaticContent',array(
						'userfolder'=> \$plxShow->plxMotor->aConf['userfolders'],
						'id'=> \$plxShow->plxMotor->cible,
						'cfauthor'=> plxUtils::strCheck(\$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['cfauthor']),
						'cfprevnext'=> plxUtils::strCheck(\$plxShow->plxMotor->aStats[\$plxShow->plxMotor->cible ]['cfprevnext']),
						)
					));
			}
		?>
END;
		echo $string;
	}

	/**
	 * Méthode permettant d'utiliser un hook simplifié dans le corps de la page d'accueil
	 *
	 * @author Cyril MAGUIRE
	 */
	public function caroufredselHome()
	{
		$string =<<<END
		<?php
		if (\$plxShow->plxMotor->plxRecord_arts->f('cfsel') == 'on'){
				eval(\$plxShow->callHook('caroufredselContent',array(
					'userfolder'	=> \$plxShow->plxMotor->aConf['userfolders'],
					'artId' 		=> \$plxShow->plxMotor->plxRecord_arts->f('cfartId'),
					'width' 		=> \$plxShow->plxMotor->plxRecord_arts->f('width'),
					'cfauthor'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfauthor'),
					'cfprevnext'		=> \$plxShow->plxMotor->plxRecord_arts->f('cfprevnext'),
					)
				));
		}
		?>
END;
		echo $string;
	}
} 
?>