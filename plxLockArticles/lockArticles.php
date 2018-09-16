<?php
/**
 * Plugin lockArticles
 *
 * @package	PLX
 * @version	1.7
 * @date	23/11/2016
 * @author	Rockyhorror
 **/

require("PasswordHash.php");

class lockArticles extends plxPlugin {

	/**
	 * Constructeur de la classe lockArticles
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Rockyhorror
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# Autorisation d'acces à la configuration du plugins
		$this-> setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);

		# Déclarations des hooks
		$this->addHook('plxMotorPreChauffageEnd', 'plxMotorPreChauffageEnd');
		$this->addHook('plxMotorDemarrageEnd', 'plxMotorDemarrageEnd');
		$this->addHook('plxFeedConstructLoadPlugins', 'plxFeedConstructLoadPlugins');
		$this->addHook('AdminArticleSidebar','AdminArticleSidebar');
		$this->addHook('AdminEditArticleXml','AdminEditArticleXml');
		$this->addHook('plxMotorParseArticle','plxMotorParseArticle');		
		$this->addHook('plxShowConstruct', 'plxShowConstruct');				
		$this->addHook('showIconIfLock','showIconIfLock');
		$this->addHook('AdminIndexTop', 'AdminIndexTop');
		$this->addHook('AdminIndexFoot', 'AdminIndexFoot');
		$this->addHook('plxFeedPreChauffageEnd','plxFeedPreChauffageEnd');
		$this->addHook('plxFeedDemarrageEnd','plxFeedDemarrageEnd');
		$this->addHook('AdminCategory','AdminCategory');
		$this->addHook('AdminEditCategoriesUpdate','AdminEditCategoriesUpdate');
		$this->addHook('AdminEditCategoriesNew','AdminEditCategoriesNew');
		$this->addHook('AdminEditCategoriesXml','AdminEditCategoriesXml');
		$this->addHook('AdminEditCategorie','AdminEditCategorie');
		$this->addHook('plxMotorGetCategories','plxMotorGetCategories');
		$this->addHook('AdminCategoriesTop','AdminCategoriesTop');
		$this->addHook('AdminCategoriesFoot','AdminCategoriesFoot');
		$this->addHook('AdminArticlePostData', 'AdminArticlePostData');
		$this->addHook('AdminArticleParseData', 'AdminArticleParseData');
		$this->addHook('AdminArticleInitData', 'AdminArticleInitData');
		$this->addHook('AdminStatic', 'AdminStatic');
		$this->addHook('AdminEditStatiquesXml', 'AdminEditStatiquesXml');
		$this->addHook('AdminEditStatiquesUpdate', 'AdminEditStatiquesUpdate');
		$this->addHook('AdminEditStatique', 'AdminEditStatique');
		$this->addHook('plxMotorGetStatiques', 'plxMotorGetStatiques');
		$this->addHook('AdminStaticsTop', 'AdminStaticsTop');
		$this->addHook('AdminStaticsFoot', 'AdminStaticsFoot');
		
		$this->hasher = new PasswordHash(8, false);
	}


	public function plxFeedConstructLoadPlugins(){
		$this->FeedMode = true;
	}

	/**
	 * Méthode qui ajoute le champs 'mot de passe' dans les options des catégories
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function AdminCategory() {
		echo '<div class="grid">
				<div class="col sml-12">
					<label for="id_password">'.$this->getlang('L_PASSWORD_FIELD_LABEL').'&nbsp;:</label>
					<?php $image = "<img src=\"".PLX_PLUGINS."lockArticles/locker.png\" alt=\"\" />";
						if(!empty($plxAdmin->aCats[$id]["password"])){ echo $image; }
						plxUtils::printInput("password","","text","27-72"); ?>
					<label for="id_resetpassword">'.$this->getlang('L_RESETPASSWORD_FIELD').'&nbsp;<input type="checkbox" name="resetpassword" /></label>
					<input type="hidden" name="passwordhash" value="<?php echo plxUtils::strCheck($plxAdmin->aCats[$id]["password"]); ?>" />
				</div>
			</div>';
	}

	public function plxAdminEditCategoriesNew() {
		echo "<?php \$this->aCats[\$cat_id]['password'] = ''; ?>";
	}

	public function plxAdminEditCategoriesUpdate() {
		echo "<?php \$this->aCats[\$cat_id]['password']=(isset(\$this->aCats[\$cat_id]['password'])?\$this->aCats[\$cat_id]['password']:'') ?>";
	}

	public function plxAdminEditCategoriesXml() {
		echo "<?php \$xml .= '<password><![CDATA['.plxUtils::cdataCheck(\$cat['password']).']]></password>'; ?>";
	}

	public function plxAdminEditCategorie() {
		echo '<?php
			if(isset($content["resetpassword"])) {
				$this->aCats[$content["id"]]["password"] = "";
			}
			elseif(!empty($content["password"])) {
				$password = trim($content["password"]);
				if(strlen($password) > 72) { return plxMsg::Error("'.$this->getlang('L_TOO_LONG').'"); }
				$hash = $this->plxPlugins->aPlugins["lockArticles"]->hasher->HashPassword($password);
				if(strlen($hash) >= 20){
					$this->aCats[$content["id"]]["password"] = $hash;
				}
				else {
					return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
				}
			}
			else {
				if(!empty($content["passwordhash"])) {
					if(strlen($content["passwordhash"]) >= 20) {
						$this->aCats[$content["id"]]["password"] = $content["passwordhash"];
					}
					else {
						return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
					}
				}
			}
			?>';
	}

	
	/*
	 * Méthode qui récupère le mot de passe des catégories dans le fichier XML, enlève les articles 
	 * des catégories avec mot de passe de la home page
	 * 
	 * @return stdio
	 * @author Rockyhorror
	 * 
	 */ 
	public function plxMotorGetCategories() {
		echo "<?php \$this->aCats[\$number]['password']=isset(\$iTags['password'])?plxUtils::getValue(\$values[\$iTags['password'][\$i]]['value']):''; ?>";
		if(($this->getParam('hide_l_categories') || isset($this->FeedMode)) && !defined('PLX_ADMIN')){
			echo "<?php if(!empty(\$this->aCats[\$number]['password']) && !isset(\$_SESSION['lockArticles']['categorie'][\$number])){ 
				foreach(\$arts as \$filename){
					\$artId = substr(\$filename, 0, 4);
					unset (\$this->plxGlob_arts->aFiles[\$artId]);
				}
			} ?>";
		}
	}

	/*
	 * Méthode qui démarrage la bufferisation des categories
	 * 
	 * @return stdio
	 * @author Rockyhorror
	 * 
	 */ 
	public function AdminCategoriesTop() {
		echo '<?php ob_start(); ?>';
	}


	/*
	 * Méthode qui ajoute le cadenas dans l'administration des categories 
	 * 
	 * @return stdio
	 * @author Rockyhorror
	 * 
	 */
	public function AdminCategoriesFoot() {
		echo '<?php
				$content=ob_get_clean();
				if(preg_match_all("#<td>([0-9]{3})</td>#", $content, $capture)) {
					$image = "<img src=\"".PLX_PLUGINS."lockArticles/locker.png\" alt=\"\" />";
					foreach($capture[1] as $idCat) {
						if(!empty($plxAdmin->aCats[$idCat]["password"])) {
							$str = "<td>".$idCat;
							$content = str_replace($str, $str."&nbsp;".$image, $content);
						}
					}
				}
				echo $content;
			?>';
	}

	public function AdminArticlePostData () {
		echo '<?php $password = $_POST["password"]; ?>';
	}

	public function AdminArticleParseData () {
		echo '<?php $password = $result["password"]; ?>';
	}

	public function AdminArticleInitData () {
		echo '<?php $password= ""; ?>';
	}
	
	/**
	 * Méthode qui ajoute le champs 'mot de passe' dans l'edition de l'article
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function AdminArticleSidebar(){
			echo '<div class="grid">
					<div class="col sml-12">
						<label for="id_password">'.$this->getlang('L_PASSWORD_FIELD_LABEL').'&nbsp;:</label>
						<?php $image = "<img src=\"".PLX_PLUGINS."lockArticles/locker.png\" alt=\"\" />";
						if(!empty($password)) { echo $image; } plxUtils::printInput("password","","text","27-72"); ?>
						<label for="id_resetpassword">'.$this->getlang('L_RESETPASSWORD_FIELD').'&nbsp;<input type="checkbox" name="resetpassword" />
						<input type="hidden" name="passwordhash" value="<?php echo $password; ?>" />
					</div>
				</div>';
        }

	/*
	 * Méthode qui enregistre le mot de passe dans le fichier XML de l'article
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */ 
	public function AdminEditArticleXml(){
		
		echo '<?php
		if(isset($content["resetpassword"])) {
			$xml .= "\t"."<password><![CDATA[]]></password>"."\n";
		}
		elseif(!empty($content["password"])) {
			$password = plxUtils::cdataCheck(trim($content["password"]));
			if(strlen($password) > 72) { return plxMsg::Error("'.$this->getlang('L_TOO_LONG').'"); }
			$hash = $this->plxPlugins->aPlugins["lockArticles"]->hasher->HashPassword($password);
			if(strlen($hash) >= 20){
				$xml .= "\t"."<password><![CDATA[".$hash."]]></password>"."\n";
			}
			else {
				return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
			}
		}
		else {
			if(!empty($content["passwordhash"])) {
				if(strlen($content["passwordhash"]) >= 20) {
					$xml .= "\t"."<password><![CDATA[".$content["passwordhash"]."]]></password>"."\n";
				}
				else {
					return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
				}
			}
		}
		?>';
	}

	/*
	 * Méthode qui démarre la bufferisation de la liste des articles
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */ 
	public function AdminIndexTop (){
		echo "<?php ob_start(); ?>";
	}
	
	/*
	 * Méthode qui ajoute le cadenas dans l'administration des articles
	 * 
	 * @return stdio
	 * @authro Rockyhorror
	 * 
	 */ 
	public function AdminIndexFoot () {
		echo '<?php
				$content=ob_get_clean();
				$image = "<img src=\"".PLX_PLUGINS."lockArticles/locker.png\" alt=\"\">";
				while($plxAdmin->plxRecord_arts->loop()) {
					$passwordhash = $plxAdmin->plxRecord_arts->f("password");
					if(!empty($passwordhash)) {
						$artId = $plxAdmin->plxRecord_arts->f("numero");
						$str = "<td>".$idArt;
						$content = str_replace($str, $str."&nbsp;".$image, $content);
					}
				}
				echo $content;
			?>';

	}

	/*
	 * Méthode qui récupère les informations de mot de passe dans le fichier XML de l'article
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */ 
	public function plxMotorParseArticle(){
		echo "<?php    \$art['password'] = (isset(\$iTags['password']))?trim(\$values[ \$iTags['password'][0] ]['value']):''; ?>";
	}

	
	/*
	 * Fonction qui ajoute le champs password dans l'administration des pages statiques 
	 * 
	 * @return stdio
	 * @author Rockyhorror
	 * 
	 */
	public function AdminStatic() {

		echo '<div class="grid">
				<div class="col sml-12">
					<label for="id_password">'.$this->getlang('L_PASSWORD_FIELD_LABEL').'&nbsp;:</label>
					<?php $image = "<img src=\"".PLX_PLUGINS."lockArticles/locker.png\" alt=\"\" />";
						$password = $plxAdmin->aStats[$id][\'password\'];
						if(!empty($password)) { echo $image; } plxUtils::printInput("password","","text","27-72"); ?>
					<label for="id_resetpassword">'.$this->getlang('L_RESETPASSWORD_FIELD').'&nbsp;<input type="checkbox" name="resetpassword" /></label>
					<input type="hidden" name="passwordhash" value="<?php echo $password; ?>" />
				</div>
			</div>';
	}

	
	
	/*
	 * Méthode qui enregistre le mot de passe dans le fichier XML statiques.xml
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */ 
	public function plxAdminEditStatiquesXml() {
		echo "<?php \$xml .= '<password><![CDATA['.plxUtils::cdataCheck(\$static['password']).']]></password>'; ?>";
	}
	
	
	/*
	 * Fonction qui gère la mise à jour de la liste des pages statiques.
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */ 
	public function plxAdminEditStatiquesUpdate() {
		echo "<?php \$this->aStats[\$static_id]['password'] = (isset(\$this->aStats[\$static_id]['password'])?\$this->aStats[\$static_id]['password']:''); ?>";
	}


	/*
	 * Méthode qui prépare l'enregistrement du mot de passe de la page statique
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */ 
	public function plxAdminEditStatique() {
		
		echo '<?php
				if(isset($content["resetpassword"])) {
					$this->aStats[$content["id"]]["password"] = "";
				}
				elseif(!empty($content["password"])) {
					$password = trim($content["password"]);
					if(strlen($password) > 72) { return plxMsg::Error("'.$this->getlang('L_TOO_LONG').'"); }
					$hash = $this->plxPlugins->aPlugins["lockArticles"]->hasher->HashPassword($password);
					if(strlen($hash) >= 20){
						$this->aStats[$content["id"]]["password"] = $hash;
					}
					else {
						return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
					}
				}
				else {
					if(!empty($content["passwordhash"])) {
						if(strlen($content["passwordhash"]) >= 20) {
							$this->aStats[$content["id"]]["password"] = $content["passwordhash"];
						}
						else {
							return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
						}
					}
				}	
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
		if(preg_match_all("#<td>([0-9]{3})</td>#", $content, $capture)) {
			$image = "<img src=\"".PLX_PLUGINS."lockArticles/locker.png\" alt=\"\" />";
			foreach($capture[1] as $idStat) {
				if(!empty($plxAdmin->aStats[$idStat]["password"])) {
					$str = "<td>".$idStat;
					$content = str_replace($str, $str."&nbsp;".$image, $content);
				}
			}
		}
		echo $content;
		?>';
    }
	
	
	/*
	 * Méthode qui récupère les informations de mot de passe dans le fichier statiques.xml
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */
	public function plxMotorGetStatiques() {
		echo "<?php \$this->aStats[\$number]['password']=isset(\$iTags['password'])?plxUtils::getValue(\$values[\$iTags['password'][\$i]]['value']):''; ?>";
	}

	/*
	 * Méthode qui affiche un cadenas si l'article a un mot de passe
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 * 
	 */
	public function showIconIfLock() {
		$plxMotor = plxMotor::getInstance();

		$passwordhash = $plxMotor->plxRecord_arts->f('password');
		if(!empty($passwordhash)){
			echo '<img src="'.PLX_PLUGINS.'lockArticles/locker.png" alt="locker icon" />';
		}
	}

	
	/**
	 * Méthode qui redefinie le mode de l'article
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 * 
	 **/
    public function plxMotorPreChauffageEnd() {
		$plxMotor = plxMotor::getInstance();
		
		if($plxMotor->mode=='article') {
			$password = (($a = $plxMotor->plxRecord_arts->f('password')) == false )? "": plxUtils::getValue($a);
			if(!empty($password)) {
				if(!isset($_SESSION['lockArticles']['articles'][$plxMotor->cible])) {
					$plxMotor->mode = 'article_password';
				}
			}
			else {
				$cat_id = $plxMotor->plxRecord_arts->f('categorie');
				if(!empty($plxMotor->aCats[$cat_id]['password'])) {
					if(!isset($_SESSION['lockArticles']['categorie'][$cat_id])) {
						$plxMotor->mode = 'categorie_password';
					}
				}
			}
		}
		elseif($plxMotor->mode == 'categorie') {
			if(!empty($plxMotor->aCats[$plxMotor->cible]['password'])) {
				if(!isset($_SESSION['lockArticles']['categorie'][$plxMotor->cible])) {
					$plxMotor->mode = 'categories_password';
					$plxMotor->idCat = $plxMotor->cible;
				}
			}
		}
		elseif($plxMotor->mode == 'static') {
			if(!empty($plxMotor->aStats[$plxMotor->cible]['password'])) {
				if(!isset($_SESSION['lockArticles']['static'][$plxMotor->cible])){
					$plxMotor->mode = 'static_password';
					$plxMotor->idStat = $plxMotor->cible;
				}
			}
		}

	}

	/**
	 * Méthode qui valide le mot de passe
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function plxMotorDemarrageEnd() {
		$plxMotor = plxMotor::getInstance();

			$showForm = false;
			if($plxMotor->mode == 'article_password') {
				if(isset($_POST['lockArticles']) && isset($_POST['password'])) {
					$passwordhash = (($a = $plxMotor->plxRecord_arts->f('password')) == false )? "": plxUtils::getValue($a);
					$pw = strip_tags(substr($_POST['password'],0,72));
					if($this->hasher->CheckPassword($pw, $passwordhash)) {
						$_SESSION['lockArticles']['articles'][$plxMotor->cible] = True;
						$url = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
						header('Location: '.$url);
						exit;
					}
					else {
						$_SESSION['lockArticles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
					}
				}
				$showForm = true;
			}
			elseif($plxMotor->mode == 'categorie_password') {
				if(isset($_POST['lockArticles']) && isset($_POST['password'])) {
					$cat_id = $plxMotor->plxRecord_arts->f('categorie');
					$passwordhash = $plxMotor->aCats[$cat_id]['password'];
					$pw = strip_tags(substr($_POST['password'],0,72));
					if($this->hasher->CheckPassword($pw, $passwordhash)){
						$_SESSION['lockArticles']['categorie'][$cat_id] = true;
						$url = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
						header('Location: '.$url);
						exit;
					}
					else {
						$_SESSION['lockArticles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
					}
				}
				$showForm = true;

			}
			elseif($plxMotor->mode == 'categories_password') {
				if(isset($_POST['lockArticles']) && isset($_POST['password'])) {
					$passwordhash = $plxMotor->aCats[$plxMotor->cible]['password'];
					$pw = strip_tags(substr($_POST['password'],0,72));

					if ($this->hasher->CheckPassword($pw, $passwordhash)) {
						$_SESSION['lockArticles']['categorie'][$plxMotor->cible] = true;
						$url = $plxMotor->urlRewrite('?categorie'.intval($plxMotor->cible).'/'.$plxMotor->aCats[$plxMotor->cible]['url']);
						header('Location: '.$url);
						exit;
					}
					else {
						$_SESSION['lockArticles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
					}
				}
				$showForm = true;
			}
			elseif($plxMotor->mode == 'static_password') {
				if(isset($_POST['lockArticles']) && isset($_POST['password'])) {
					$passwordhash = $plxMotor->aStats[$plxMotor->cible]['password'];
					$pw = strip_tags(substr($_POST['password'],0,72));
					if($this->hasher->Checkpassword($pw, $passwordhash)) {
						$_SESSION['lockArticles']['static'][$plxMotor->cible] = true;
						$url = $plxMotor->urlRewrite('?static'.intval($plxMotor->cible).'/'.$plxMotor->aStats[$plxMotor->cible]['url']);
						header('Location: '.$url);
						exit;
					}
					else {
						$_SESSION['lockArticles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
					}
				}
				$showForm = true;
			}
			if($showForm) {
				$plxMotor->cible = '../../'.PLX_PLUGINS.'lockArticles/form';
				$plxMotor->template = 'static.php';
			}

	}


	/*
	 * Method qui affiche le formulaire
	 * 
	 * @return stdio
	 * @author	Rockyhorror
	 */ 
	public function plxShowConstruct() {
		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='article_password' or \$this->plxMotor->mode=='categorie_password') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=>	\$this->plxMotor->plxRecord_arts->f('title'),
			'menu'		=> '',
			'url'		=> 'article_password',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		$string .= "elseif(\$this->plxMotor->mode=='categories_password') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=>	\$this->plxMotor->aCats[\$this->plxMotor->idCat]['name'],
			'menu'		=> '',
			'url'		=> 'article_password',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		$string .= "elseif(\$this->plxMotor->mode=='static_password') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=>	\$this->plxMotor->aStats[\$this->plxMotor->idStat]['name'],
			'menu'		=> '',
			'url'		=> 'article_password',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		echo "<?php ".$string." ?>";
	}


	
	/*
	 * Méthode qui enlève les liens RSS des articles des catégories avec mot de passe
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */ 
	public function plxFeedPreChauffageEnd() {

		$plxFeed = plxFeed::getInstance();
		
		if ($plxFeed->mode == 'article') {
			$plxFeed->mode = 'article_password';
		}
	}


	/*
	 * Méthode qui enlève les liens RSS des articles avec mot de passe
	 * 
	 * @return null
	 * @author Rockyhorror
	 * 
	 */ 
	public function plxFeedDemarrageEnd() {
		$plxFeed = plxFeed::getInstance();

		if ($plxFeed->mode != 'article_password')
			return;

		if($plxFeed->plxRecord_arts) {
			$i = 0;
			while($plxFeed->plxRecord_arts->loop()) {
				$password = $plxFeed->plxRecord_arts->f('password');
				if(!empty($password)) {
					unset($plxFeed->plxRecord_arts->result[$plxFeed->plxRecord_arts->i]);
					$i++;
				}
			}
			$plxFeed->plxRecord_arts->size -= $i;
			$plxFeed->plxRecord_arts->result = array_values($plxFeed->plxRecord_arts->result);
		}
		echo '<?php $this->getRssArticles(); ?>';
	}

}
?>
