<?php
/**
 * Classe plxStaticGaleries
 *
 * @package	plxStaticGaleries
 * @date	26/12/2011
 * version  2.0
 * @author	flipflip <flipflip@blogoflip.fr>
 **/

class plxStaticGaleries extends plxPlugin {

	public $aGaleries = array();
	private static $instance = null;

	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		$this->getGaleries();

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		#  limite l'accès à l'écran d'administration du plugin
		$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER, PROFIL_MODERATOR, PROFIL_EDITOR, PROFIL_WRITER);

		# Ajouts des hooks
		$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
		$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
		$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
	}

	public function plxShowPageTitle() {
		$url = explode('/', plxUtils::getGets());
		echo '<?php
			if($this->plxMotor->mode == "galerie") {
				echo plxUtils::strCheck($this->plxMotor->aConf["title"])." - ".plxUtils::strCheck($this->plxMotor->plxPlugins->aPlugins["plxStaticGaleries"]["instance"]->aGaleries["'.$url[2].'"]["menu_name"]);
				return true;
			}
		?>';
	}

	public function plxMotorPreChauffageBegin() {	
		$string = "
		if(\$this->get && preg_match('/^galerie\/?/',\$this->get)) {
			\$this->mode = 'galerie';
			\$this->cible = '../../plugins/plxStaticGaleries/galerie';
			\$this->template = 'static.php';
			return true;
		}
		";

		echo "<?php ".$string." ?>";
	    }

	public function plxShowStaticListEnd() {
		foreach($this->aGaleries as $galerie_id => $galerie) {
			if($galerie['active'] == 1 and $galerie['delete'] != 1 and $galerie['menu'] != 0) {
				# infos sur la page statique
				$string  = "if(\$this->plxMotor->mode=='galerie') {";
				$string .= "	\$array = array();";
				$string .= "	\$array[\$this->plxMotor->cible] = array('name' => '".$galerie['menu_name']."', 'url' => 'galerie', 'readable' => 1);";
				$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
				$string .= "}";
				echo "<?php ".$string." ?>";
				# ajout du menu pour accèder à la page de contact
				echo "<?php \$class = \$this->plxMotor->mode=='galerie'?'active':'noactive'; ?>";
				echo "<?php array_splice(\$menus, ".(intval($galerie['menu_position'])-1).", 0, '<li><a class=\"static '.\$class.'\" href=\"'.\$this->plxMotor->urlRewrite('?galerie/".$galerie['name']."/".$galerie_id."').'\">".$galerie['menu_name']."</a></li>'); ?>";
			}
		}
	}

	public function getGaleries() {
		$filename = PLX_PLUGINS.'plxStaticGaleries/galeries.xml';
		if(file_exists($filename)) {
			$data = implode('',file($filename));
			$parser = xml_parser_create(PLX_CHARSET);
			xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
			xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
			xml_parse_into_struct($parser,$data,$values,$iTags);
			xml_parser_free($parser);
			if(isset($iTags['galerie']) AND isset($iTags['name'])) {
				$nb = sizeof($iTags['name']);
				$size=ceil(sizeof($iTags['galerie'])/$nb);
				for($i = 0; $i < $nb; $i++) {
					$attributes = $values[$iTags['galerie'][$i*$size]]['attributes'];
					if($attributes['delete'] != 1) {						
						$number = $attributes['number'];
						$this->aGaleries[$number]['name'] = plxUtils::getValue($values[$iTags['name'][$i]]['value']);
						$this->aGaleries[$number]['user'] = $attributes['user'];
						$this->aGaleries[$number]['active'] = intval($attributes['active']);
						$this->aGaleries[$number]['menu'] = intval($attributes['menu']);
						$this->aGaleries[$number]['delete'] = intval($attributes['delete']);
						$this->aGaleries[$number]['first'] = plxUtils::getValue($values[$iTags['first'][$i]]['value']);
						$this->aGaleries[$number]['parent'] = plxUtils::getValue($values[$iTags['parent'][$i]]['value']);
						$this->aGaleries[$number]['root_dir'] = plxUtils::getValue($values[$iTags['root_dir'][$i]]['value']);
						$this->aGaleries[$number]['extensions'] = plxUtils::getValue($values[$iTags['extensions'][$i]]['value']);
						$this->aGaleries[$number]['sort'] = plxUtils::getValue($values[$iTags['sort'][$i]]['value']);
						$this->aGaleries[$number]['prive'] = plxUtils::getValue($values[$iTags['prive'][$i]]['value']);
						$this->aGaleries[$number]['password'] = plxUtils::getValue($values[$iTags['password'][$i]]['value']);
						$this->aGaleries[$number]['menu_name'] = plxUtils::getValue($values[$iTags['menu_name'][$i]]['value']);
						$this->aGaleries[$number]['menu_position'] = plxUtils::getValue($values[$iTags['menu_position'][$i]]['value']);
						$this->aGaleries[$number]['activeSeparateur'] = plxUtils::getValue($values[$iTags['activeSeparateur'][$i]]['value']);
						$this->aGaleries[$number]['separateur'] = plxUtils::getValue($values[$iTags['separateur'][$i]]['value']);
						$this->aGaleries[$number]['representative'] = plxUtils::getValue($values[$iTags['representative'][$i]]['value']);
						$this->aGaleries[$number]['template'] = plxUtils::getValue($values[$iTags['template'][$i]]['value']);
						$this->aGaleries[$number]['displayName'] = plxUtils::getValue($values[$iTags['displayName'][$i]]['value']);
						$this->aGaleries[$number]['content'] = plxUtils::getValue($values[$iTags['content'][$i]]['value']);
					}
				}
			}
		}	
	}

	public function ThemeEndHead() {
		echo "\n"."\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'plxStaticGaleries/plxStaticGaleries.css" media="screen" type="text/css" />'."\n";
	}

	/**
	 * Méthode qui retourne la liste des des fichiers d'un répertoire
	 *
	 * @param	dir		répertoire de lecture
	 * @return	files	tableau contenant la liste de tous les fichiers d'un dossier
	 * @author	Stephane F
	 **/
	public function getDirFiles($galerie, $dirs) {

		if(!empty($this->aGaleries[$galerie]['root_dir'])) {
			if(isset($dirs)) {
				$dirs = $dirs.'/';
			} else {
				$dirs = '/';
			}
			if($plxShow->plxMotor->aConf['userfolders'] == 1) {
				$root_dir = 'data/images/'.$this->aGaleries[$galerie]['user'].'/'.$this->aGaleries[$galerie]['root_dir'].$dirs;
			} else {
				$root_dir = 'data/images/'.$this->aGaleries[$galerie]['root_dir'].$dirs;
			}

			if(!file_exists(PLX_ROOT.$root_dir)) {
				return false;
			} else {
				# Initialisation
				$files = array();
				# Ouverture et lecture du dossier demandé
				if($handle = @opendir(PLX_ROOT.$root_dir)) {
					while(FALSE !== ($file = readdir($handle))) {
						$thumName = plxUtils::thumbName($file);
						if($file[0] != '.' AND !preg_match('/index.htm/i', $file) AND is_dir(PLX_ROOT.$root_dir.$file)) {
								$files[$file] = array(
									'type'		=> 'dir',
									'name'		=> $file,
									'path'		=> $root_dir.$file,
								);				
						} elseif($file[0] != '.' AND !preg_match('/index.htm/i', $file) AND !preg_match('/^(.*\.)tb.([^.]+)$/D', $file)) {
							if(is_file(PLX_ROOT.$root_dir.$file)) {
								$ext = strtolower(strrchr(PLX_ROOT.$root_dir.$file, '.'));
								$_thumb2 = false;
								if(is_file(PLX_ROOT.$root_dir.$thumName)) {
									$_thumb2 = array(
										'infos' => @getimagesize(PLX_ROOT.$root_dir.$thumName)
									);
								}
								$files[$file] = array(
									'type'		=> 'file',
									'name' 		=> $file,
									'path' 		=> $root_dir.$file,
									'extension'	=> $ext,
								);
							}
						}
				    }
					closedir($handle);
				}
				# On tri le contenu
				if($this->aGaleries[$galerie]['sort'] == 'sort') {
					sort($files);
				} else {
					rsort($files);
				}

				# On retourne le tableau
				return $files;
			}
		}
    }

	/* ------------------------------ */
	/* Fonction pour l'administration */
	/* ------------------------------ */
	public function editGaleries($content, $action=false) {

		$save = $this->aGaleries;

		# suppression
		if(!empty($content['selection']) AND $content['selection'] == 'delete' AND isset($content['idGalerie'])) {
			foreach($content['idGalerie'] as $galerie_id) {
				if($content['selection'] == 'delete') {
					$this->aGaleries[$galerie_id]['delete'] = 1;
					$action = true;
				}
			}
		} elseif(!empty($content['update'])) {
			foreach($content['galerieNum'] as $galerie_id) {
				if($content[$galerie_id.'_name']!= '') {
					if (empty($content[$galerie_id.'_user'])) {
						$user = $_SESSION['user'];
					} else {
						$user = $this->aGaleries[$galerie_id]['user'];
					}

					if(isset($this->aGaleries[$galerie_id]['root_dir'])) {
						$root_dir = $this->aGaleries[$galerie_id]['root_dir'];
					} elseif(empty($content[$galerie_id.'_root_dir'])) {
						$root_dir = $this->getParam('root_dir');
					} else {
						$root_dir = $content[$galerie_id.'_root_dir'];
					}

					if(isset($this->aGaleries[$galerie_id]['first'])) {
						$first = $this->aGaleries[$galerie_id]['first'];
					} elseif(empty($content[$galerie_id.'_parent'])) {
						$first = $this->getParam('first');
					} else {
						$first = $content[$galerie_id.'_first'];
					}

					if(isset($this->aGaleries[$galerie_id]['parent'])) {
						$parent = $this->aGaleries[$galerie_id]['parent'];
					} elseif(empty($content[$galerie_id.'_parent'])) {
						$parent = $this->getParam('parent');
					} else {
						$parent = $content[$galerie_id.'_parent'];
					}

					if(isset($this->aGaleries[$galerie_id]['extensions'])) {
						$extensions = $this->aGaleries[$galerie_id]['extensions'];
					} elseif(empty($content[$galerie_id.'_extensions'])) {
						$extensions = $this->getParam('extensions');
					} else {
						$extensions = $content[$galerie_id.'_extensions'];
					}

					if(isset($this->aGaleries[$galerie_id]['activeSeparateur'])) {
						$activeSeparateur = $this->aGaleries[$galerie_id]['activeSeparateur'];
					} elseif(empty($content[$galerie_id.'_activeSeparateur'])) {
						$activeSeparateur = $this->getParam('activeSeparateur');
					} else {
						$activeSeparateur = $content[$galerie_id.'_separateur'];
					}

					if(isset($this->aGaleries[$galerie_id]['separateur'])) {
						$separateur = $this->aGaleries[$galerie_id]['separateur'];
					} elseif(empty($content[$galerie_id.'_separateur'])) {
						$separateur = $this->getParam('separateur');
					} else {
						$separateur = $content[$galerie_id.'_separateur'];
					}

					if(isset($this->aGaleries[$galerie_id]['displayName'])) {
						$displayName = $this->aGaleries[$galerie_id]['displayName'];
					} elseif(empty($content[$galerie_id.'_separateur'])) {
						$displayName = $this->getParam('displayName');
					} else {
						$displayName = $content[$galerie_id.'_displayName'];
					}

					if(isset($this->aGaleries[$galerie_id]['sort'])) {
						$sort = $this->aGaleries[$galerie_id]['sort'];
					} elseif(empty($content[$galerie_id.'_sort'])) {
						$sort = $this->getParam('sort');
					} else {
						$sort = $content[$galerie_id.'_sort'];
					}

					$this->aGaleries[$galerie_id]['name'] = trim($content[$galerie_id.'_name']);
					$this->aGaleries[$galerie_id]['user'] = trim($user);
					$this->aGaleries[$galerie_id]['active'] = intval($content[$galerie_id.'_active']);
					$this->aGaleries[$galerie_id]['menu'] = intval($content[$galerie_id.'_menu']);
					$this->aGaleries[$galerie_id]['delete'] = intval($content[$galerie_id.'_delete']);
					$this->aGaleries[$galerie_id]['root_dir'] = $root_dir;
					$this->aGaleries[$galerie_id]['first'] = $first;
					$this->aGaleries[$galerie_id]['parent'] = $parent;
					$this->aGaleries[$galerie_id]['extensions'] = $extensions;
					$this->aGaleries[$galerie_id]['sort'] =  $sort;
					$this->aGaleries[$galerie_id]['prive'] = (isset($this->aGaleries[$galerie_id]['prive'])?$this->aGaleries[$galerie_id]['prive']:$content[$galerie_id.'_prive']);
					$this->aGaleries[$galerie_id]['password'] = (isset($this->aGaleries[$galerie_id]['password'])?$this->aGaleries[$galerie_id]['password']:$content[$galerie_id.'_password']);
					$this->aGaleries[$galerie_id]['menu_name'] = (isset($this->aGaleries[$galerie_id]['menu_name'])?$this->aGaleries[$galerie_id]['menu_name']:$content[$galerie_id.'_menu_name']);
					$this->aGaleries[$galerie_id]['menu_position'] = (isset($this->aGaleries[$galerie_id]['menu_position'])?$this->aGaleries[$galerie_id]['menu_position']:$content[$galerie_id.'_menu_position']);
					$this->aGaleries[$galerie_id]['activeSeparateur'] = $activeSeparateur;
					$this->aGaleries[$galerie_id]['separateur'] = $separateur;
					$this->aGaleries[$galerie_id]['representative'] = (isset($this->aGaleries[$galerie_id]['representative'])?$this->aGaleries[$galerie_id]['representative']:$content[$galerie_id.'_representative']);
					$this->aGaleries[$galerie_id]['template'] = (isset($this->aGaleries[$galerie_id]['template'])?$this->aGaleries[$galerie_id]['template']:$content[$galerie_id.'_template']);
					$this->aGaleries[$galerie_id]['displayName'] = $displayName;
					$this->aGaleries[$galerie_id]['content'] = (isset($this->aGaleries[$galerie_id]['content'])?$this->aGaleries[$galerie_id]['content']:$content[$galerie_id.'_content']);

					$action = true;
				}
			}
		}

		# sauvegarde
		if($action) {
			$galeries_name = array();
			$xml = "<?xml version='1.0' encoding='".PLX_CHARSET."'?>\n";
			$xml .= "<document>\n";
			foreach($this->aGaleries as $galeries_id => $galeries) {

				# control de l'unicité du nom de la galerie
				if(in_array($galeries['name'], $galeries_name)) {
					$this->aGaleries = $save;
					return plxMsg::Error($this->getLang('L_CREATE_GALERIE_ERR').' : '.$this->getLang('L_ERR_GALERIE_ALREADY_EXISTS').' : '.plxUtils::strCheck($galeries['name']));				
				} else {
					$galeries_name[] = $galeries['name'];
				}

				$xml .= "\t<galerie number=\"".$galeries_id."\" active=\"".$galeries['active']."\" menu=\"".$galeries['menu']."\" user=\"".$galeries['user']."\" delete=\"".$galeries['delete']."\">";
				$xml .= "<name><![CDATA[".plxUtils::title2url(plxUtils::cdataCheck($galeries['name']))."]]></name>";
				$xml .= "<root_dir><![CDATA[".plxUtils::cdataCheck($galeries['root_dir'])."]]></root_dir>";
				$xml .= "<first><![CDATA[".plxUtils::cdataCheck($galeries['first'])."]]></first>";
				$xml .= "<parent><![CDATA[".plxUtils::cdataCheck($galeries['parent'])."]]></parent>";
				$xml .= "<extensions><![CDATA[".plxUtils::cdataCheck($galeries['extensions'])."]]></extensions>";
				$xml .= "<sort><![CDATA[".plxUtils::cdataCheck($galeries['sort'])."]]></sort>";
				$xml .= "<prive><![CDATA[".plxUtils::cdataCheck($galeries['prive'])."]]></prive>";
				$xml .= "<password><![CDATA[".plxUtils::cdataCheck($galeries['password'])."]]></password>";
				$xml .= "<menu_name><![CDATA[".plxUtils::cdataCheck($galeries['menu_name'])."]]></menu_name>";
				$xml .= "<menu_position>".$galeries['menu_position']."</menu_position>";
				$xml .= "<activeSeparateur>".$galeries['activeSeparateur']."</activeSeparateur>";
				$xml .= "<separateur>".$galeries['separateur']."</separateur>";
				$xml .= "<representative>".$galeries['representative']."</representative>";
				$xml .= "<template>".$galeries['template']."</template>";
				$xml .= "<displayName>".$galeries['displayName']."</displayName>";
				$xml .= "<content><![CDATA[".plxUtils::cdataCheck($galeries['content'])."]]></content>";
				$xml .= "</galerie>\n";
			}
			$xml .= "</document>";

			# On écrit le fichier
			if(plxUtils::write($xml, PLX_PLUGINS.'plxStaticGaleries/galeries.xml')) {
				return plxMsg::Info($this->getLang('L_CREATE_GALERIE_SUCCESSFUL'));
			} else {
				$this->aGaleries = $save;
				return plxMsg::Error($this->getLang('L_CREATE_GALERIE_ERR').' '.PLX_PLUGINS.'plxStaticGaleries/galeries.xml');
			}
		}	
	}

	// Mise à jour d'une galerie
	public function editGalerie($content) {
		if($plxShow->plxMotor->aConf['userfolders'] == 1) {
			$gal_dir = PLX_ROOT.'data/images/'.$_SESSION['user'].'/'.$content['root_dir'].'/';
		} else {
			$gal_dir = PLX_ROOT.'data/images/'.$content['root_dir'].'/';
		}

		if (!file_exists($gal_dir)) {
			if (!mkdir($gal_dir)) {
				return plxMsg::Error($this->getLang('L_CREATE_GALERIE_ERR').' : '.$this->getLang('L_ERR_CREATE_DIR'));
			}
		}

		$this->aGaleries[$content['id']]['active'] = intval($content['active']);
		$this->aGaleries[$content['id']]['menu'] = intval($content['menu']);
		$this->aGaleries[$content['id']]['root_dir'] = $content['root_dir'];
		$this->aGaleries[$content['id']]['first'] = $content['first'];
		$this->aGaleries[$content['id']]['parent'] = $content['parent'];
		$this->aGaleries[$content['id']]['extensions'] = trim($content['extensions']);
		$this->aGaleries[$content['id']]['sort'] = trim($content['sort']);
		$this->aGaleries[$content['id']]['prive'] = trim($content['prive']);
		$this->aGaleries[$content['id']]['password'] = trim($content['password']);
		$this->aGaleries[$content['id']]['menu_name'] = trim($content['menu_name']);
		$this->aGaleries[$content['id']]['menu_position'] = trim($content['menu_position']);
		$this->aGaleries[$content['id']]['activeSeparateur'] = $content['activeSeparateur'];
		$this->aGaleries[$content['id']]['separateur'] = $content['separateur'];
		$this->aGaleries[$content['id']]['representative'] = trim($content['representative']);
		$this->aGaleries[$content['id']]['template'] = trim($content['template']);
		$this->aGaleries[$content['id']]['displayName'] = trim($content['displayName']);
		$this->aGaleries[$content['id']]['content'] = trim($content['content']);

		return $this->editGaleries(null, true);
	}
}
?>
