<?php
/**
 * Plugin plxMyPrivateStatic
 * Protection de l'accès aux pages statiques par mot de passe
 *
 * @version	1.1
 * @date	05/04/2012
 * @author	Stephane F
 **/
class plxMyPrivateStatic extends plxPlugin {

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

        # déclaration des hooks
		$this->addHook('AdminStatic', 'AdminStatic');
		$this->addHook('AdminStaticsPrepend', 'AdminStaticsPrepend');
		$this->addHook('plxAdminEditStatique', 'plxAdminEditStatique');
		$this->addHook('plxAdminEditStatiquesUpdate', 'plxAdminEditStatiquesUpdate');
		$this->addHook('plxAdminEditStatiquesXml', 'plxAdminEditStatiquesXml');
		$this->addHook('plxMotorGetStatiques', 'plxMotorGetStatiques');
		$this->addHook('AdminStaticsTop', 'AdminStaticsTop');
        $this->addHook('AdminStaticsFoot', 'AdminStaticsFoot');

		$this->addHook('plxShowConstruct', 'plxShowConstruct');
		$this->addHook('plxMotorPreChauffageEnd', 'plxMotorPreChauffageEnd');
		$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
    }

	/**
	 * Méthode qui ajoute le champ de saisie du mot de passe dans la page d'édition de la page statique
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function AdminStatic() {
		echo '
			<?php
				$protect = isset($plxAdmin->aStats[$id]["protect"]) ? $plxAdmin->aStats[$id]["protect"] : 0;
				$filename = PLX_ROOT.$plxAdmin->aConf["racine_statiques"].$id.".plxMyPrivateStatic.php";
				$chapo = file_exists($filename) ? file_get_contents($filename) : "";
				$password = isset($plxAdmin->aStats[$id]["password"]) ? $plxAdmin->aStats[$id]["password"] : "";
				$image = "<img src=\"".PLX_PLUGINS."plxMyPrivateStatic/locker.png\" alt=\"\" />";
			?>
			<h2>'.$this->getLang('L_PROTECT').'</h2>
			<fieldset>
				<p><label for="id_protect">'.$this->getLang('L_FORM_PROTECT').'&nbsp;:</label></p>
				<?php plxUtils::printSelect("protect",array("1"=>L_YES,"0"=>L_NO),$protect); ?>
				<p><label for="id_password">'.$this->getLang('L_FORM_PASSWORD').'&nbsp;:</label></p>
				<input type="password" id="id_password" name="password" value="" size="20" maxlength="25" />
				<?php if($password!="" AND $protect) echo $image; ?>
				<p><label for="id_chapo">'.$this->getLang('L_FORM_PUBLIC_CONTENT').'&nbsp;:</label></p>
				<?php plxUtils::printArea("chapo",plxUtils::strCheck($chapo),35,10); ?>
			</fieldset>
		';
	}

	/**
	 * Méthode qui gère la suppression des pages contenant le contenu public
	 *
	 * @return	null
	 * @author	Stephane F
	 **/
    public function AdminStaticsPrepend() {
		echo '<?php
			if(!empty($_POST)) {
				if(!empty($_POST["selection"]) AND $_POST["selection"]=="delete" AND isset($_POST["idStatic"])) {
					foreach($_POST["idStatic"] as $static_id) {
						$filename = PLX_ROOT.$plxAdmin->aConf["racine_statiques"].$static_id.".plxMyPrivateStatic.php";
						if(is_file($filename)) unlink($filename);
					}
				}
			}
		?>';
    }

	/**
	 * Méthode qui gère la mise à jour de la liste des pages statiques
	 *
	 * @return	null
	 * @author	Stephane F
	 **/
    public function plxAdminEditStatiquesUpdate() {
		echo '<?php
			$this->aStats[$static_id]["protect"] = (isset($this->aStats[$static_id]["protect"])?$this->aStats[$static_id]["protect"]:0);
			$this->aStats[$static_id]["password"] = (isset($this->aStats[$static_id]["password"])?$this->aStats[$static_id]["password"]:"");
		?>';
    }

	/**
	 * Méthode qui rajoute les informations dans la chaine xml à sauvegarder dans statiques.xml
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function plxAdminEditStatiquesXml() {
		echo '<?php
			$xml .= "<protect>".intval($static["protect"])."</protect>";
			$xml .= "<password><![CDATA[".plxUtils::cdataCheck($static["password"])."]]></password>";
		?>';
    }

	/**
	 * Méthode qui formate les données saisies lors de l'édition de la page statique
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function plxAdminEditStatique() {
		echo '<?php
			$this->aStats[$content["id"]]["protect"] = (isset($content["protect"]) ? intval($content["protect"]) : 0);
			$this->aStats[$content["id"]]["password"] = (!empty($content["password"]) AND $this->aStats[$content["id"]]["protect"]) ? sha1($content["password"]) : "";
			if(!empty($content["chapo"]))
				plxUtils::write($content["chapo"], PLX_ROOT.$this->aConf["racine_statiques"].$content["id"].".plxMyPrivateStatic.php");
		?>';
    }

	/**
	 * Méthode qui récupère les informations stockées dans le fichier xml statiques.xml
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function plxMotorGetStatiques() {
		echo '<?php
			$protect = plxUtils::getValue($iTags["protect"][$i]);
			$this->aStats[$number]["protect"]=plxUtils::getValue($values[$protect]["value"]);
			$password = plxUtils::getValue($iTags["password"][$i]);
			$this->aStats[$number]["password"]=plxUtils::getValue($values[$password]["value"]);
		?>';
	}

	/**
	 * Méthode qui permet de démarrer la bufférisation de sortie sur la page admin/statiques.php
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdminStaticsTop() {
		echo '<?php ob_start(); ?>';
    }

	/**
	 * Méthode qui affiche l'image du cadenas si la page est protégée par un mot de passe
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdminStaticsFoot() {
		echo '<?php
		$content=ob_get_clean();
		if(preg_match_all("#<td>".L_PAGE." ([0-9]{3})</td>#", $content, $capture)) {
			$image = "<img src=\"".PLX_PLUGINS."plxMyPrivateStatic/locker.png\" alt=\"\" />";
			foreach($capture[1] as $idStat) {
				$str = "<td>".L_PAGE." ".$idStat;
				if(isset($plxAdmin->aStats[$idStat]["protect"]) AND $plxAdmin->aStats[$idStat]["protect"]) {
					$content = str_replace($str, $str." ".$image, $content);
				}
			}
		}
		echo $content;
		?>';
    }


	/**
	 * Méthode de traitement du hook plxShowConstruct
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function plxShowConstruct() {

		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='static_password') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=> \$this->plxMotor->aStats[\$this->plxMotor->idStat]['name'],
			'menu'		=> '',
			'url'		=> 'static_password',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		echo "<?php ".$string." ?>";
    }

	/**
	 * Méthode qui affiche le formulaire d'identification s'il faut protéger la page statique
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function plxMotorPreChauffageEnd() {
		echo "<?php
		if(\$this->mode=='static') {
			\$protect = plxUtils::getValue(\$this->aStats[\$this->cible]['protect']);
			if(\$protect==1) {
				\$password = plxUtils::getValue(\$this->aStats[\$this->cible]['password']);
				if(\$password!='') {
					if(!isset(\$_SESSION['password_statics'][\$this->cible])) {
						\$this->idStat = \$this->cible;
						\$this->cible = '../../'.PLX_PLUGINS.'plxMyPrivateStatic/form';
						\$this->mode = 'static_password';
						\$this->template = 'static.php';
					}
				}
			}
		}
		?>";
	}

	/**
	 * Méthode qui renseigne le titre de la page dans la balise html <title>
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowPageTitle() {
		echo '<?php
			if($this->plxMotor->mode == "static_password") {
				echo plxUtils::strCheck(plxUtils::strCheck($this->plxMotor->aStats[$this->plxMotor->idStat]["name"]." - ".$this->plxMotor->aConf["title"]));
				return true;
			}
		?>';
	}

	/**
	 * Méthode qui ajoute le fichier css dans le fichier header.php du thème
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function ThemeEndHead() {
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxMyPrivateStatic/style.css" media="screen" />'."\n";
	}
}
?>