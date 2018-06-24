<?php
/**
 * Plugin adhesion
 *
 * @version	1.5
 * @date	07/10/2013
 * @author	Stephane F, Cyril MAGUIRE
 **/
class adhesion extends plxPlugin {

	public $plxGlob_adherents; # Obj des données concernant les fichiers adhérents
	public $plxRecord_adherents; # Obj des données concernant les adhérents

	public $oldAdherentsList = array(); # Tableau des données des adhérents, extraites de la liste générée par l'ancienne version du plugin
	public $adherentsList = array(); # Tableau des index des adhérents
	public $listsDiff = array(); # Tableau des listes de diffusion
	public $listDiff = array(); # La liste de diffusion des adhérents
	public $msg = FALSE;
	public $ok = FALSE;
	//Paramètres des listes de diffusion Gutuma
	public $isGutumaActivated=false;
	public $GutumaListsDir;
	private $id;
	private $name;
	private $private;
	private $addresses;
	private $size;
	// Paramètres de connexion
	private $ban = array();
	private $session_domain;

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
	public function __construct($default_lang) {

        # appel du constructeur de la classe plxPlugin (obligatoire)
        parent::__construct($default_lang);

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->setAdminProfil(PROFIL_ADMIN, PROFIL_MANAGER);

        # déclaration des hooks
		$this->addHook('AdminPrepend', 'AdminPrepend');
		$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
		$this->addHook('AdminTopBottom', 'AdminTopBottom');
		$this->addHook('AdhesionUsersTop', 'AdhesionUsersTop');
		$this->addHook('AdhesionUsersTopV', 'AdhesionUsersTopV');
		$this->addHook('AdhesionUsersFoot', 'AdhesionUsersFoot');
		$this->addHook('plxMotorConstructLoadPlugins', 'plxMotorConstructLoadPlugins');
		$this->addHook('plxMotorConstruct', 'plxMotorConstruct');
		if(plxUtils::checkMail($this->getParam('email'))) {
			$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');
			$this->addHook('plxShowConstruct', 'plxShowConstruct');
			$this->addHook('plxShowStaticListEnd', 'plxShowStaticListEnd');
			$this->addHook('plxShowPageTitle', 'plxShowPageTitle');
			$this->addHook('ThemeEndHead', 'ThemeEndHead');
			$this->addHook('ThemeEndBody', 'ThemeEndBody');
			$this->addHook('SitemapStatics', 'SitemapStatics');

			# Déclarations des hooks pour sécuriser les articles
			$this->addHook('AdminArticleSidebar','AdminArticleSidebar');
			$this->addHook('plxAdminEditArticleXml','plxAdminEditArticleXml');
			$this->addHook('plxMotorParseArticle','plxMotorParseArticle');
			$this->addHook('plxMotorPreChauffageEnd', 'plxMotorPreChauffageEnd');
			$this->addHook('plxMotorDemarrageEnd', 'plxMotorDemarrageEnd');
			$this->addHook('showIconIfLock','showIconIfLock');
			$this->addHook('AdminIndexTop', 'AdminIndexTop');
			$this->addHook('AdminIndexFoot', 'AdminIndexFoot');
			$this->addHook('plxFeedPreChauffageEnd','plxFeedPreChauffageEnd');
			$this->addHook('plxFeedDemarrageEnd','plxFeedDemarrageEnd');
			$this->addHook('AdminCategory','AdminCategory');
			$this->addHook('plxAdminEditCategoriesUpdate','plxAdminEditCategoriesUpdate');
			$this->addHook('plxAdminEditCategoriesXml','plxAdminEditCategoriesXml');
			$this->addHook('plxAdminEditCategorie','plxAdminEditCategorie');
			$this->addHook('plxMotorGetCategories','plxMotorGetCategories');
			$this->addHook('loginLogout','loginLogout');
			$this->addHook('AdminCategoriesTop','AdminCategoriesTop');
			$this->addHook('AdminCategoriesFoot','AdminCategoriesFoot');

			# déclaration des hooks pour sécuriser les pages statiques
			$this->addHook('AdminStatic', 'AdminStatic');
			$this->addHook('plxAdminEditStatique', 'plxAdminEditStatique');
			$this->addHook('plxAdminEditStatiquesXml', 'plxAdminEditStatiquesXml');
			$this->addHook('plxMotorGetStatiques', 'plxMotorGetStatiques');
			$this->addHook('AdminStaticsTop', 'AdminStaticsTop');
	        $this->addHook('AdminStaticsFoot', 'AdminStaticsFoot');
			
			$this->addHook('plxShowConstruct', 'plxShowConstructStat');		
			$this->addHook('plxMotorPreChauffageEnd', 'plxMotorPreChauffageEndStat');
			$this->addHook('plxMotorDemarrageEnd', 'plxMotorDemarrageEndStat');
			$this->addHook('plxShowPageTitle', 'plxShowPageTitleStat');
			$this->addHook('ThemeEndHead', 'ThemeEndHeadStat');
		}
		$plxMotor = plxMotor::getInstance();
		$this->isGutumaActivated = $plxMotor->plxPlugins->aPlugins['gutuma'];
		if(is_object($this->isGutumaActivated)) {
			$this->isGutumaActivated = true;
		}
		# On récupère l'ensemble des adhérents
		$this->plxGlob_adherents = plxGlob::getInstance(PLX_ROOT.$this->getParam('adherents').'adhesions',false,true,'arts');
		$this->adherentsList = array_flip(array_keys($this->plxGlob_adherents->aFiles));

		$htaccess = "Allow from none\n";
		$htaccess .= "Deny from all\n";
		$htaccess .= "<Files *.php>\n";
		$htaccess .= "order allow,deny\n";
		$htaccess .= "deny from all\n";
		$htaccess .= "</Files>\n";
		$htaccess .= "Options -Indexes\n";


		if ($this->isGutumaActivated) {

			# Emplacement des listes de diffusion de Gutuma
			if ($plxMotor->plxPlugins->aPlugins['gutuma']->listsDir != null)
				$this->GutumaListsDir = $plxMotor->plxPlugins->aPlugins['gutuma']->listsDir;
			else
				$this->GutumaListsDir = PLX_ROOT.'data/gutuma';

		# Récupération des listes des anciennes versions de Gutuma
		if (is_dir(PLX_PLUGINS.'gutuma/news/lists')) {
			@rename(PLX_PLUGINS.'gutuma/news/lists', $this->GutumaListsDir);
			touch($this->GutumaListsDir.'/.htaccess');
			file_put_contents($this->GutumaListsDir.'/.htaccess', $htaccess);
		}
		# Récupération de la config des anciennes versions de Gutuma
		if (is_file(PLX_PLUGINS.'gutuma/news/inc/config.php')) {
			@mkdir($this->GutumaListsDir.'/inc');
			@rename(PLX_PLUGINS.'gutuma/news/inc/config.php', $this->GutumaListsDir.'/inc/config.php');
			touch($this->GutumaListsDir.'/inc/.htaccess');
			file_put_contents($this->GutumaListsDir.'/inc/.htaccess', $htaccess);
		}
		


			# On récupère les paramètres de la liste de diffusion
			$list = $this->getAllGutumaLists(TRUE);

			$k = 0;
			foreach ($list as $key => $value) {
				if ($value['name'] == 'adherents') {
					$k = $key;
				}
			}
			if (isset($list[$k])) {
				$this->id = $list[$k]['id'];
				$this->name = $list[$k]['name'];
				$this->private = $list[$k]['private'];
				$this->addresses = $list[$k]['addresses'];
				$this->listDiff = $list[$k]['addresses'];
			}
		}
		
		if ($this->id != ''){
			$this->ok = TRUE;
		}
    }

    ///////////////////////////////////////////////////////////
	//
	// Méthodes permettant la mise en place du plugin
	//
	//////////////////////////////////////////////////////////

    public function plxMotorConstructLoadPlugins() {
    	$string = "
    	\$this->plxGlob_adherents = plxGlob::getInstance(PLX_ROOT.$this->getParam('adherents').'adhesions',false,true,'arts');
		\$this->adherentsList = array_flip(array_keys(\$this->plxGlob_adherents->aFiles));";
		echo "<?php".$string."?>";
    }

    public function plxMotorConstruct() {
    	$string = "
    	# Récupération des données
    	\$this->plxPlugins->aPlugins['adhesion']->getAdherents('/^[0-9]{5}.(.[a-z-]+){2}.[0-9]{10}.xml$/');";
    	echo "<?php".$string."?>";
    }

    /**
	 * Méthode qui préconfigure le plugin
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function onActivate() {
		$plxAdmin = plxAdmin::getInstance();
		#Paramètres par défaut
		if(!is_file($this->plug['parameters.xml'])) {
			$this->setParam('adherents', 'data/configuration/', 'cdata');
			$this->setParam('mnuName', 'Devenir membre', 'string');
			$this->setParam('domaine_asso', plxUtils::strCheck($plxAdmin->aConf['racine']), 'string');
			$this->saveParams();
		}
		if (!is_dir(PLX_ROOT.$this->getParam('adherents').'adhesions')) {
			@mkdir(PLX_ROOT.$this->getParam('adherents').'adhesions');
		}
		# Si le fichier unique des adhérents existe (ancienne version), on le découpe
		if (is_file(PLX_ROOT.$this->getParam('adherents').'plugin.adhesion.adherents.xml')) {
		 	$this->genNewFilesFormOldData(PLX_ROOT.$this->getParam('adherents').'plugin.adhesion.adherents.xml');
		}
		if (isset($plxAdmin->plxPlugins->aPlugins["gutuma"])) {
			$listeDeDiffusion = plxUtils::strCheck($plxAdmin->aConf['title']);

			if ($listeDeDiffusion == '') {
				$listeDeDiffusion = 'Newsletters';
			}
			# On crée la liste de diffusion si elle n'existe pas
			if ($this->name != $listeDeDiffusion) {
				$this->listDiff = $this->createGutumaList($listeDeDiffusion);
			}
		}
		//Si les plugins lockArticles et plxMyPrivateStatic sont activés, on les désactive
		if (isset($plxAdmin->plxPlugins->aPlugins["lockArticles"])) {
			$content['selection'] ='deactivate';
			$content['plugName'] = array('lockArticles'=>'on');
			$content['action']['lockArticles'] = 'on';
			$plxAdmin->plxPlugins->saveConfig($content);
		}
		if (isset($plxAdmin->plxPlugins->aPlugins["plxMyPrivateStatic"])) {
			$content['selection'] ='deactivate';
			$content['plugName'] = array('plxMyPrivateStatic'=>'on');
			$content['action']['plxMyPrivateStatic'] = 'on';
			$plxAdmin->plxPlugins->saveConfig($content);
		}
	}

	/**
	 * Méthode qui récupère les infos enregistrées dans le fichier data/configuration/plugin.adhesion.adherents.xml
	 * 
	 * @param $filename ressource le chemin vers le fichier des adhérents
	 * @return array
	 * 
	 * @author Cyril MAGUIRE
	 */
	private function getAdherentsFromOldFile($filename) {
		
		if(!is_file($filename)) return;
		
		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		if(isset($iTags['adherent']) AND isset($iTags['nom'])) {
			$nb = sizeof($iTags['nom']);
			$size=ceil(sizeof($iTags['adherent'])/$nb);
			for($i=0;$i<$nb;$i++) {
				$attributes = $values[$iTags['adherent'][$i*$size]]['attributes'];
				$number = $attributes['number'];
				# Recuperation du nom
				$this->oldAdherentsList[$number]['nom']=plxUtils::getValue($values[$iTags['nom'][$i]]['value']);
				# Recuperation du prenom
				$this->oldAdherentsList[$number]['prenom']=plxUtils::getValue($values[$iTags['prenom'][$i]]['value']);
				# Recuperation de l'adresse 1
				$this->oldAdherentsList[$number]['adresse1']=plxUtils::getValue($values[$iTags['adresse1'][$i]]['value']);
				# Recuperation de l'adresse 2
				$this->oldAdherentsList[$number]['adresse2']=plxUtils::getValue($values[$iTags['adresse2'][$i]]['value']);
				# Recuperation du code postal
				$this->oldAdherentsList[$number]['cp']=plxUtils::getValue($values[$iTags['cp'][$i]]['value']);
				# Recuperation de la ville
				$this->oldAdherentsList[$number]['ville']=plxUtils::getValue($values[$iTags['ville'][$i]]['value']);
				# Recuperation du téléphone
				$this->oldAdherentsList[$number]['tel']=plxUtils::getValue($values[$iTags['tel'][$i]]['value']);
				# Recuperation du mail
				$this->oldAdherentsList[$number]['mail']=plxUtils::getValue($values[$iTags['mail'][$i]]['value']);
				# Recuperation du choix quant à l'adhésion
				$this->oldAdherentsList[$number]['choix']=plxUtils::getValue($values[$iTags['choix'][$i]]['value']);
				# Recuperation du choix pour le mailing
				$this->oldAdherentsList[$number]['mailing']=plxUtils::getValue($values[$iTags['mailing'][$i]]['value']);
				# Recuperation du statut de l'adhérent
				$this->oldAdherentsList[$number]['validation']=plxUtils::getValue($values[$iTags['validation'][$i]]['value']);
				# Recuperation de la date de première adhésion
				$this->oldAdherentsList[$number]['firstDate']=plxUtils::getValue($values[$iTags['firstDate'][$i]]['value']);
				# Recuperation de la date de validation de l'adhésion
				$this->oldAdherentsList[$number]['date']=plxUtils::getValue($values[$iTags['date'][$i]]['value']);
				# Recuperation de la chaine salt de l'adhérent
				$this->oldAdherentsList[$number]['salt']=plxUtils::getValue($values[$iTags['salt'][$i]]['value']);
				# Recuperation du mot de passe cripté de l'adhérent
				$this->oldAdherentsList[$number]['password']=plxUtils::getValue($values[$iTags['password'][$i]]['value']);
				# Recuperation de la clé
				$this->oldAdherentsList[$number]['cle']=plxUtils::getValue($values[$iTags['cle'][$i]]['value']);
				# Recuperation des chaines aléatoires
				$this->oldAdherentsList[$number]['rand1']=plxUtils::getValue($values[$iTags['rand1'][$i]]['value']);
				$this->oldAdherentsList[$number]['rand2']=plxUtils::getValue($values[$iTags['rand2'][$i]]['value']);
				if ($this->getParam('typeAnnuaire') == 'professionnel') {
					# Recuperation de l'activité
					$this->oldAdherentsList[$number]['activite']=plxUtils::getValue($values[$iTags['activite'][$i]]['value']);
					# Recuperation de l'établissement
					$this->oldAdherentsList[$number]['etablissement']=plxUtils::getValue($values[$iTags['etablissement'][$i]]['value']);
					# Recuperation du service
					$this->oldAdherentsList[$number]['service']=plxUtils::getValue($values[$iTags['service'][$i]]['value']);
					# Recuperation du poste
					$this->oldAdherentsList[$number]['tel_office']=plxUtils::getValue($values[$iTags['tel_office'][$i]]['value']);
				}
				if ($this->getParam('showAnnuaire') == 'on') {
					# Recuperation du choix sur le partage des coordonnées
					$this->oldAdherentsList[$number]['coordonnees']=plxUtils::getValue($values[$iTags['coordonnees'][$i]]['value']);
				}
			}
		}
		//tri du tableau par ordre alphabétique des noms
		$tmp = array();
		foreach($this->oldAdherentsList as $id=>$v){
			    $tmp[$v["nom"]] = array(
			    	'id'=>$id,
			    	'details'=>$v
			    );}
		ksort($tmp);
		$this->oldAdherentsList = array();
		foreach ($tmp as $nom => $value) {
		 	$this->oldAdherentsList[$value['id']] = $value['details'];
		 }
		return $this->oldAdherentsList;
	}

	private function genNewFilesFormOldData($filename) {
		$this->getAdherentsFromOldFile($filename);
		
		foreach ($this->oldAdherentsList as $id => $adherent) {
			$fileName = $id.'.'.plxUtils::title2filename($adherent['nom'].'.'.$adherent['prenom']).'.'.(empty($adherent['firstDate'])? time() : $adherent['firstDate']).'.xml';
			# On génére le fichier XML
			$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
			$xml .= "<document>\n";
				$xml .= "\t<adherent number=\"".$id."\">\n\t\t";
				$xml .= "<nom><![CDATA[".plxUtils::cdataCheck($adherent['nom'])."]]></nom>\n\t\t";
				$xml .= "<prenom><![CDATA[".plxUtils::cdataCheck($adherent['prenom'])."]]></prenom>\n\t\t";
				$xml .= "<adresse1><![CDATA[".plxUtils::cdataCheck($adherent['adresse1'])."]]></adresse1>\n\t\t";
				$xml .= "<adresse2><![CDATA[".plxUtils::cdataCheck($adherent['adresse2'])."]]></adresse2>\n\t\t";
				$xml .= "<cp><![CDATA[".plxUtils::cdataCheck($adherent['cp'])."]]></cp>\n\t\t";
				$xml .= "<ville><![CDATA[".plxUtils::cdataCheck($adherent['ville'])."]]></ville>\n\t\t";
				$xml .= "<tel><![CDATA[".plxUtils::cdataCheck($adherent['tel'])."]]></tel>\n\t\t";
				$xml .= "<mail><![CDATA[".plxUtils::cdataCheck($adherent['mail'])."]]></mail>\n\t\t";
				$xml .= "<choix><![CDATA[".plxUtils::cdataCheck($adherent['choix'])."]]></choix>\n\t\t";
				$xml .= "<mailing><![CDATA[".plxUtils::cdataCheck($adherent['mailing'])."]]></mailing>\n\t\t";
				$xml .= "<salt><![CDATA[".plxUtils::cdataCheck($adherent['salt'])."]]></salt>\n\t\t";
				$xml .= "<password><![CDATA[".plxUtils::cdataCheck($adherent['password'])."]]></password>\n\t\t";
				$xml .= "<rand1><![CDATA[".plxUtils::cdataCheck($adherent['rand1'])."]]></rand1>\n\t\t";
				$xml .= "<rand2><![CDATA[".plxUtils::cdataCheck($adherent['rand2'])."]]></rand2>\n\t\t";
				$xml .= "<cle><![CDATA[".plxUtils::cdataCheck($adherent['cle'])."]]></cle>\n\t\t";
				$xml .=	"<validation>".plxUtils::cdataCheck($adherent['validation'])."</validation>\n\t\t";
				$xml .=	"<firstDate>".plxUtils::cdataCheck($adherent['firstDate'])."</firstDate>\n\t\t";
				$xml .=	"<date>".plxUtils::cdataCheck($adherent['date'])."</date>\n\t\t";
				if ($this->getParam('typeAnnuaire') == 'professionnel') {
				$xml .= "<activite><![CDATA[".plxUtils::cdataCheck($adherent['activite'])."]]></activite>\n\t\t";
				$xml .= "<etablissement><![CDATA[".plxUtils::cdataCheck($adherent['etablissement'])."]]></etablissement>\n\t\t";
				$xml .= "<service><![CDATA[".plxUtils::cdataCheck($adherent['service'])."]]></service>\n\t\t";
				$xml .= "<tel_office><![CDATA[".plxUtils::cdataCheck($adherent['tel_office'])."]]></tel_office>\n\t";
				}
				if ($this->getParam('showAnnuaire') == 'on') {
				$xml .= "\t<coordonnees><![CDATA[".plxUtils::cdataCheck($adherent['coordonnees'])."]]></coordonnees>\n\t";
				}
				$xml .= "</adherent>\n";
			$xml .= "</document>";
			# On écrit le fichier
			if(!plxUtils::write($xml, PLX_ROOT.$this->getParam('adherents').'adhesions/'.$fileName)) {
				$_SESSION['error'] = $this->getLang('L_ERR_FILE_ADHERENTS').'<br/>' ;
				break;
			}
		}
	}


	/**
	 * Méthode de traitement du hook plxShowConstruct
	 *
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
    public function plxShowConstruct() {

		# infos sur la page statique
		$string  = "
		if(\$this->plxMotor->mode=='adhesion') {
						\$array = array();
						\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuName')."',
			'menu'		=> '',
			'url'		=> 'adhesion.html',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);
			\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);
		}
		if(\$this->plxMotor->mode=='adherer') {
						\$array = array();
						\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuAdherer')."',
			'menu'		=> '',
			'url'		=> 'adherer.html',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);
			\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);
		}
		if(\$this->plxMotor->mode=='forgetmypass') {
						\$array = array();
						\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuForgetPass')."',
			'menu'		=> '',
			'url'		=> 'forgetmypass.html',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);
			\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);
		}
		if(\$this->plxMotor->mode=='annuaire') {
						\$array = array();
						\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuAnnuaire')."',
			'menu'		=> '',
			'url'		=> 'annuaire.html',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);
			\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);
		}
		if(\$this->plxMotor->mode=='myaccount') {
						\$array = array();
						\$array[\$this->plxMotor->cible] = array(
			'name'		=> '".$this->getParam('mnuMyAccount')."',
			'menu'		=> '',
			'url'		=> 'myaccount.html',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);
			\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);
		}";
		echo "<?php ".$string." ?>";
    }

	/**
	 * Méthode de traitement du hook plxMotorPreChauffageBegin
	 * 1) On met à jour la liste des adhérents, en fonction de la date de leur inscription, dès qu'une page publique est affichée
	 * 2) On utilise une page statique pour afficher le formulaire d'adhésion
	 *
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
    public function plxMotorPreChauffageBegin() {
    	$content = array();//echo strtotime("20 October 2011");exit();
    	foreach ($this->plxRecord_adherents->result as $i => $value) {
    		if ($value['validation'] == 1) {
	    		if ($this->getParam('annee') == 'civile') {
					$datetimeOld = date('Y',$value['date']);
		    		//(60*60*24*365) = 31536000 secondes soit 1 an
		    		$datetimeNew = strtotime('01 January '.($datetimeOld+1).' 00:00:01' );
				}
				if ($this->getParam('annee') == 'entiere') {
					$datetimeOld = $value['date'];
		    		//(60*60*24*365) = 31536000 secondes soit 1 an
		    		$datetimeNew = $datetimeOld+365*24*60*60;
				}
				if ( $datetimeNew < time() ){
	    			foreach ($value as $key => $v) {
	    				$content[$key.'_'.$value['id']] = $v;
	    			}
					$content['validation_'.$value['id']] = 0;
					$content['idAdherent'] = array($value['id']);
					$this->editAdherentslist($content,$value['id'],TRUE);
				}
			}
    	}

		$template = $this->getParam('template')==''?'static.php':$this->getParam('template');

		$string = "
		if(\$this->get && preg_match('/^adhesion\/?/',\$this->get)) {
			\$this->mode = 'adhesion';
			\$this->cible = '../../plugins/adhesion/form';
			\$this->template = '".$template."';
			return TRUE;
		}
		if(\$this->get && preg_match('/^adherer\/?/',\$this->get)) {
			\$this->mode = 'adherer';
			\$this->cible = '../../plugins/adhesion/form';
			\$this->template = '".$template."';
			return TRUE;
		}
		if(\$this->get && preg_match('/^myaccount\/?/',\$this->get)) {
			\$this->mode = 'myaccount';
			\$this->cible = '../../plugins/adhesion/form';
			\$this->template = '".$template."';
			return TRUE;
		}
		if(\$this->get && preg_match('/^forgetmypass\/?/',\$this->get)) {
			\$this->mode = 'forgetmypass';
			\$this->cible = '../../plugins/adhesion/form';
			\$this->template = '".$template."';
			return TRUE;
		}
		if(\$this->get && preg_match('/^annuaire\/?/',\$this->get)) {
			\$this->mode = 'annuaire';
			\$this->cible = '../../plugins/adhesion/form';
			\$this->template = '".$template."';
			return TRUE;
		}
		";

		echo "<?php ".$string." ?>";
    }

	/**
	 * Méthode de traitement du hook plxShowStaticListEnd
	 *
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
    public function plxShowStaticListEnd() {
    	$plxMotor = plxMotor::getInstance();
    		echo "<?php \$class = (\$this->plxMotor->mode=='adhesion' || \$this->plxMotor->mode=='adherer' || \$this->plxMotor->mode=='annuaire')?'active':'noactive'; ?>";
			echo "<?php \$class1 = \$this->plxMotor->mode=='adhesion'?'active':'noactive'; ?>";
			echo "<?php \$class2 = \$this->plxMotor->mode=='adherer'?'active':'noactive'; ?>";
			echo "<?php \$class3 = \$this->plxMotor->mode=='annuaire'?'active':'noactive'; ?>";
		# ajout du menu pour accèder à la page d'adhesion si l'utilisateur n'est pas connecté
		if($this->getParam('mnuDisplay') && !isset($_SESSION['account'])) {
			echo ($this->getParam('showAnnuaire') == 'on' && $this->getParam('typeAnnuaire') == 'professionnel') ? "<?php \$annuaire = '<li class=\"'.\$class3.'\"><a href=\"'.\$this->plxMotor->urlRewrite('?annuaire.html').'\"><span>".$this->getParam('mnuAnnuaire')."</span></a></li>';?>" : "<?php \$annuaire = '';?>";
			echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, '<li class=\"page_item '.\$class.'\"><a href=\"'.\$this->plxMotor->urlRewrite('?adhesion.html').'\">".$this->getParam('mnuName')."</a><ul><li class=\"'.\$class2.'\"><a href=\"'.\$this->plxMotor->urlRewrite('?adherer.html').'\"><span>".$this->getParam('mnuAdherer')."</span></a></li><li class=\"'.\$class1.'\"><a href=\"'.\$this->plxMotor->urlRewrite('?adhesion.html').'\"><span>".$this->getParam('mnuName')."</span></a></li>'.\$annuaire.'</ul></li>'); ?>";
		} elseif ($this->getParam('mnuDisplay') && isset($_SESSION['account'])) {//L'utilisateur est connecté
			echo ($this->getParam('showAnnuaire') == 'on' && $this->getParam('typeAnnuaire') == 'professionnel') ? "<?php \$annuaire = '<li class=\"'.\$class3.'\"><a href=\"'.\$this->plxMotor->urlRewrite('?annuaire.html').'\"><span>".$this->getParam('mnuAnnuaire')."</span></a></li>';?>" : "<?php \$annuaire = '';?>";
			echo "<?php array_splice(\$menus, ".($this->getParam('mnuPos')-1).", 0, \$annuaire); ?>";
		}

    }

	/**
	 * Méthode qui ajoute le fichier css dans le fichier header.php du thème
	 *
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
	public function ThemeEndHead() {
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'adhesion/style.css" media="screen" />'."\n";
		echo '
			<style type="text/css">
				#wall-e {
					position:absolute;
					left:-99999px;
				}
			</style>
		';
	}

	/**
	 * Méthode qui renseigne le titre de la page dans la balise html <title>
	 *
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
	public function plxShowPageTitle() {
		echo '<?php
			if($this->plxMotor->mode == "adhesion") {
				echo plxUtils::strCheck($this->plxMotor->aConf["title"]." - '.$this->getParam('mnuName').'");
				return TRUE;
			}
		?>';
	}

	/**
	 * Méthode qui référence la page de contact dans le sitemap
	 *
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
	public function SitemapStatics() {
		echo '<?php
		echo "\n";
		echo "\t<url>\n";
		echo "\t\t<loc>".$plxMotor->urlRewrite("?adhesion")."</loc>\n";
		echo "\t\t<changefreq>monthly</changefreq>\n";
		echo "\t\t<priority>0.8</priority>\n";
		echo "\t</url>\n";
		?>';
	}

	/**
	 * Méthode permettant l'export de la liste des adhérents
	 * 
	 * @return void
	 * @author Cyril MAGUIRE
	 */
	public function AdminPrepend() {

		# Impression de la liste des adhérents
		if (isset($_GET['print'])) {

			$plxMotor = plxMotor::getInstance();
			$this->getAdherents('/^[0-9]{5}.(.[a-z-]+){2}.[0-9]{10}.xml$/');

			# Inclusion des librairies de TBS
			if (version_compare(PHP_VERSION,'5')<0) {
				include_once 'opentbs/tbs_class.php'; // TinyButStrong template engine
			} else {
				include_once 'opentbs/tbs_class_php5.php'; // TinyButStrong template engine
			}
			if (($_GET['print'] == 'xls') ) {
				include_once 'opentbs/tbs_plugin_excel.php'; // Excel plugin
			} else {
				include_once 'opentbs/tbs_plugin_opentbs.php'; // OpenTBS plugin
			}
			include_once 'print.php';
			exit();
		}

	}

	/**
	 * Méthode qui affiche la css du plugin dans la partie administration
	 * 
	 * @author Cyril MAGUIRE, Stephane F
	 */
	public function AdminTopEndHead() {
		echo '<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'adhesion/style.css" media="screen" />';
		{?>

		<script type="text/javascript">
		/* <![CDATA[ */
		if (typeof jQuery == 'undefined') {
			document.write('<script type="text\/javascript" src="<?php echo PLX_PLUGINS ?>adhesion\/js\/jquery.min.js"><\/script>');
		}
		/* ]]> */
		</script>
		<?php
		}
	}

	/**
	 * Méthode de traitement du hook AdhesionUserTop
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdhesionUsersTop() {?>

		<p style="text-align:right">
			<?php $this->lang('L_LABEL_FIND') ?>&nbsp;:&nbsp;
			<input type="text" id="txtFilter" name="txtFilter" />&nbsp;
			<img style="display:none" id="imgDeleteFilter" src="<?php echo PLX_PLUGINS ?>adhesion/cancel.gif" alt="<?php $this->lang('L_LABEL_DELETE_FILTER') ?>" title="<?php $this->lang('L_LABEL_DELETE_FILTER') ?>" />
		</p>

		<?php
	}
	/**
	 * Méthode de traitement du hook AdhesionUserTop
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdhesionUsersTopV() {?>

		<p style="text-align:right">
			<?php $this->lang('L_LABEL_FIND') ?>&nbsp;:&nbsp;
			<input type="text" id="txtFilterV" name="txtFilterV" />&nbsp;
			<img style="display:none" id="imgDeleteFilterV" src="<?php echo PLX_PLUGINS ?>adhesion/cancel.gif" alt="<?php $this->lang('L_LABEL_DELETE_FILTER') ?>" title="<?php $this->lang('L_LABEL_DELETE_FILTER') ?>" />
		</p>

		<?php
	}


	/**
	 * Méthode de traitement du hook AdminUsersFoot
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdhesionUsersFoot() {?>

		<script type="text/javascript">
		/* <![CDATA[ */
		$(document).ready(function(){
			// reset the search when the cancel image is clicked
			$('#imgDeleteFilter').click(function(){
				$('#txtFilter').val("").keyup();
				$('#imgDeleteFilter').hide();
			});
			$('#txtFilter').keyup(function(){
				$('#imgDeleteFilter').show();
				var s = $(this).val().toLowerCase().split(" ");
				//show all rows.
				$('#table>tbody>tr:hidden').show();
				$.each(s, function(){
					$("#table>tbody>tr:visible>.indexColumn:not(:contains('" + this + "'))").parent().hide();
				});
			});
			$('#imgDeleteFilterV').click(function(){
				$('#txtFilterV').val("").keyup();
				$('#imgDeleteFilterV').hide();
			});
			$('#txtFilterV').keyup(function(){
				$('#imgDeleteFilterV').show();
				var s = $(this).val().toLowerCase().split(" ");
				//show all rows.
				$('#tableV>tbody>tr:hidden').show();
				$.each(s, function(){
					$("#tableV>tbody>tr:visible>.indexColumnV:not(:contains('" + this + "'))").parent().hide();
				});
			});
			$('#table>tbody>tr:has(td)').each(function(){
				// recuperation des valeurs des colonnes
				if ( $(this).find("td:eq(1)").text() !== undefined ) {
					var userid = $(this).find("td:eq(1)").text().substr(0,5);
				}
				if ( $(this).find("td:eq(2)>input[type='text']").val() !== undefined ) {
					var username = $(this).find("td:eq(2)>input[type='text']").val().toLowerCase();
				}
				
				// ajout index de recherche
				$('<td class="indexColumn"><\/td>').hide().text(userid+username).appendTo(this);
			});
			$('#tableV>tbody>tr:has(td)').each(function(){
				// recuperation des valeurs des colonnes
				if ( $(this).find("td:eq(1)").text() !== undefined ) {
					var useridV = $(this).find("td:eq(1)").text().substr(0,5);
				}
				if ( $(this).find("td:eq(2)>input[type='text']").val() !== undefined ) {
					var usernameV = $(this).find("td:eq(2)>input[type='text']").val().toLowerCase();
				}
				
				// ajout index de recherche
				$('<td class="indexColumnV"><\/td>').hide().text(useridV+usernameV).appendTo(this);
			});
		});
		/* ]]> */
		</script>

			<?php
    }


	/**
	 * Méthode qui affiche un message s'il y a un message à afficher
	 *
	 * @return	stdio
	 * @author	Stephane F, Cyril MAGUIRE
	 **/
	public function AdminTopBottom() {
		
			$string = '
			$adhesion = $plxAdmin->plxPlugins->aPlugins["adhesion"];
			echo $adhesion->msg;
			if($adhesion->msg=="L_ADMIN_UPDATE_ADH") {
				echo "<p class=\"success\">Plugin adhesion<br />'.$this->getLang("L_ADMIN_UPDATE_ADH").'</p>";
				plxMsg::Display();
			}';
			if ($this->isGutumaActivated) {
				$string .='if(!$adhesion->ok) {
				echo "<p class=\"warning\">Plugin adhesion<br />'.$this->getLang("L_ERR_NO_LIST").'</p>";
				plxMsg::Display();
			}';
			}
			$string .='
			if($adhesion->getParam("email")=="") {
				echo "<p class=\"warning\">Plugin adhesion config<br />'.$this->getLang("L_ERR_EMAIL").'</p>";
				plxMsg::Display();
			}
			if($adhesion->getParam("nom_asso")=="") {
				echo "<p class=\"warning\">Plugin adhesion config<br />'.$this->getLang("L_WARNING_NAME_ASSO").'</p>";
				plxMsg::Display();
			}
			if($adhesion->getParam("adresse_asso")=="") {
				echo "<p class=\"warning\">Plugin adhesion config<br />'.$this->getLang("L_WARNING_ADDRESS_ASSO").'</p>";
				plxMsg::Display();
			}
			if($adhesion->getParam("adherents")=="") {
				echo "<p class=\"warning\">Plugin adhesion<br />'.$this->getLang("L_ERR_FILE_ADHERENTS").'</p>";
				plxMsg::Display();
			}
			if($adhesion->getParam("subject")==str_replace("\'","’",$adhesion->getLang(\'L_DEFAULT_SUBJECT\'))) {
				echo "<p class=\"notice\">Plugin adhesion config<br />'.$this->getLang("L_WARNING_SUBJECT").'</p>";
				plxMsg::Display();
			}
			if(trim(strip_tags(htmlspecialchars_decode($adhesion->getParam("desc_adhesion"))))==str_replace("\'","’",$adhesion->getLang(\'L_DEFAULT_DESC\'))) {
				echo "<p class=\"notice\">Plugin adhesion config<br />'.$this->getLang("L_WARNING_DESC").'</p>";
				plxMsg::Display();
			}
			if($adhesion->getParam("validation_subject")==str_replace("\'","’",$adhesion->getLang(\'L_DEFAULT_VAL_SUB\'))) {
				echo "<p class=\"notice\">Plugin adhesion config<br />'.$this->getLang("L_WARNING_VAL_SUBJECT").'</p>";
				plxMsg::Display();
			}
			if($adhesion->getParam("devalidation_subject")==str_replace("\'","’",$adhesion->getLang(\'L_DEFAULT_DEVAL_SUB\'))) {
				echo "<p class=\"notice\">Plugin adhesion config<br />'.$this->getLang("L_WARNING_DEVAL_SUBJECT").'</p>";
				plxMsg::Display();
			}
			if($adhesion->getParam("thankyou")==str_replace("\'","’",$adhesion->getLang(\'L_DEFAULT_THANKYOU\'))) {
				echo "<p class=\"notice\">Plugin adhesion config<br />'.$this->getLang("L_WARNING_THANKYOU").'</p>";
				plxMsg::Display();
			}
			if($adhesion->getParam("subject_password")==str_replace("\'","’",$adhesion->getLang(\'L_DEFAULT_SUBJECT_PASS\'))) {
				echo "<p class=\"notice\">Plugin adhesion config<br />'.$this->getLang("L_WARNING_PASS").'</p>";
				plxMsg::Display();
			}
			if (!is_dir(PLX_ROOT.$adhesion->getParam("adherents")."adhesions")) {
				echo "<p class=\"warning\">Plugin adhesion config<br />'.$this->getLang("L_WARNING_NO_DIRECTORY").'</p>";
				plxMsg::Display();
			}';
			echo '<?php '.$string.' ?>';
	}

	
    /**
     * Méthode qui inverse la position des lettres composant un email afin d'éviter les spams
     * 
     * @param email string l'email à obfusquer
     * @return string
     * 
     * @author Cyril MAGUIRE
     */
    public function badEmail($email) {
    	$longueur = strlen(trim($email));
    	for ($i=1; $i < $longueur+1 ; $i++) { 
    		$tmp[$i] = $email[$longueur-$i];
    	}
    	$email = implode('', $tmp);
     	return '<span class="baddirection">'.$email.'</span>';
    }

	/**
	* Méthode d'envoi de mail
	*
	* @param	name		string 			Nom de l'expéditeur
	* @param	from		string 			Email de l'expéditeur
	* @param	to			array/string	Adresse(s) du(des) destinataires(s)
	* @param	subject		string			Objet du mail
	* @param	body		string			Contenu du mail
	* @param	contentType	string			Format du mail : txt ou html
	* @param	cc			array			Les destinataires en copie
	* @param	bcc			string			Les destinataires en copie
	* @return				boolean			renvoie FAUX en cas d'erreur d'envoi
	* @author	Cyril MAGUIRE
	**/
	public function sendEmail($name, $from, $to, $subject, $body, $contentType="html", $cc=FALSE, $bcc=FALSE) {

		$eBody  = "<html><head><title>'.$subject.'</title></head><body style=\"margin:10px;\">".$body;
		$eBody .= "-----------<br />";
		$eBody .= $this->getParam('nom_asso')."</body></html>";
		
		if(is_array($to))
			$to = implode(', ', $to);
		if(is_array($cc))
			$cc = implode(', ', $cc);
		if(is_array($bcc))
			$bcc = implode(', ', $bcc);

		$headers  = "From: ".$name." <".$this->getParam('email').">\r\n";
		$headers .= "Reply-To: no-reply@".$this->getParam('domaine_asso')."\r\n";
		$headers .= 'MIME-Version: 1.0'."\r\n";
		// Content-Type
		if($contentType == 'html')
			$headers .= 'Content-type: text/html; charset="'.PLX_CHARSET.'"'."\r\n";
		else
			$headers .= 'Content-type: text/plain; charset="'.PLX_CHARSET.'"'."\r\n";

		$headers .= 'Content-transfer-encoding: 8bit'."\r\n";

		if($cc != "")
			$headers .= 'Cc: '.$cc."\r\n";
		if($bcc != "")
			$headers .= 'Bcc: '.$bcc."\r\n";

		if (mail($to, $subject, $eBody, $headers) ){
			return TRUE;
		} else {
			return FALSE;
		}	
	}

	/**
	 * Méthode permettant de mettre en forme le mail de notification à l'administrateur
	 * 
	 * @param $nom string nom de l'adhérent
	 * @param $prenom string prenom de l'adhérent
	 * @param $adresse1 string première partie de l'adhérent
	 * @param $adresse2 string deuxième partie de l'adhérent
	 * @param $cp numeric code postal de l'adhérent
	 * @param $ville string ville de l'adhérent
	 * @param $tel numeric téléphone de l'adhérent
	 * @param $mail string email de l'adhérent
	 * @param $choix string choix de l'adhérent quant à l'adhésion
	 * @param $mailing string choix de l'adhérent quant à l'envoi de mail par l'asso
	 * @param $pro array ensemble des caractéristiques professionnelles
	 * 
	 * @return string
	 * @author Cyril MAGUIRE
 	 */
	public function notification($nom,$prenom,$adresse1,$adresse2,$cp,$ville,$tel,$mail,$choix,$mailing,$pro=array()) {
		return '
		<table style="border:none;">
			<thead>
				<tr>
					<th style="border:none;text-align:left;">Nom : '.$nom.'</th>
				</tr>
				<tr>
					<th style="border:none;text-align:left;">Prénom : '.$prenom.'</th>
				</tr>
			</thead>
			<tbody>
				'.($this->getParam('typeAnnuaire') == 'professionnel' ?
			'
				<tr>
					<td style="border:none;">Activité : '.$pro['activite'].'</td>
				</tr>
				<tr>
					<td style="border:none;">Etablissement : '.$pro['etablissement'].'</td>
				</tr>
				<tr>
					<td style="border:none;">Service : '.$pro['service'].'</td>
				</tr>
			': '').'
				<tr>
					<td style="border:none;">Adresse : '.$adresse1.'</td>
				</tr>
				<tr>
					<td style="border:none;">'.$adresse2.'</td>
				</tr>
				<tr>
					<td style="border:none;">Code postal : '.$cp.'</td>
				</tr>
				<tr>
					<td style="border:none;">Ville : '.$ville.'</td>
				</tr>
				<tr>
					<td style="border:none;">Téléphone : '.$this->formatFrenchPhoneNumber($tel).'</td>
				</tr>
				'.($this->getParam('typeAnnuaire') == 'professionnel' ?
			'
				<tr>
					<td style="border:none;">Poste : '.$this->formatFrenchPhoneNumber($pro['tel_office']).'</td>
				</tr>
			': '').'
				<tr>
					<td style="border:none;">&nbsp;</td>
				</tr>
				<tr>
					<td style="border:none;">Email : '.$mail.'</td>
				</tr>
				<tr>
					<td style="border:none;">'.(($choix == 'adhesion') ? 'Souhaite devenir adhérent de l’Association '.$this->getParam('nom_asso') : ($choix == 'renouveler') ? 'Souhaite renouveler son adhésion' : 'Ne souhaite plus être membre de l’Association').'</td>
				</tr>
				'.($this->getParam('showAnnuaire') == 'on' ?
			'
				<tr>
					<td style="border:none;">'.(($pro['coordonnees'] == 'rec') ? 'Accepte que ses coordonnées professionnelles figurent sur le site de l’association' : 'Refuse que ses coordonnées professionnelles figurent sur le site de l’association').'</td>
				</tr>
			': '').'
				<tr>
					<td style="border:none;">'.(($mailing == 'maillist') ? 'Accepte de recevoir par mail toute information concernant le site de '.$this->getParam('nom_asso') : 'Refuse de recevoir par mail toute information concernant le site de '.$this->getParam('nom_asso')).'</td>
				</tr>
				<tr>
					<td style="border:none;">Envoyé à l’association le '.date('d/m/Y').'</td>
				</tr>
			</tbody>
		</table>
		';
	}

	/**
	 * Méthode qui affiche un message contenant les instructions à suivre pour régler son adhésion
	 * 
	 * @return string
	 * @author Cyril MAGUIRE
	 */
	public function adresse() {
		return '<p>Merci d\'établir votre règlement à l’ordre de : Association '.$this->getParam('nom_asso').' et de le retourner à l\'adresse suivante :</p>
	 	<div style="padding-left:50px;">'.$this->getParam('adresse_asso').'</div>
		<p>Dès que l’inscription sera enregistrée par le secrétariat de l\'Association, vous recevrez par E-mail votre confirmation d’inscription.</p>
		<p style="font-size:80%"><strong>Si vous ne recevez aucun mail, pensez à vérifier régulièrement votre dossier <em>spam</em>.</strong></p>';
	}
	///////////////////////////////////////////////////////////
	//
	// Méthodes permettant la modification des listes de Gutuma
	//
	//////////////////////////////////////////////////////////

	/**
	 * Checks the ending of the specified string
	 * @param string $haystack The string to check
	 * @param string $needle The ending to check for
	 * @return bool TRUE if the string ends with the given string, else FALSE
	 */
	public function strEnds($haystack, $needle) {
		$ending = substr($haystack, strlen($haystack) - strlen($needle));
		return $ending === $needle;
	}

	/**
	 * Loads all of the lists
	 * @param bool $load_addresses TRUE if lists addresses should be loaded (default is FALSE)
	 * @param bool $inc_private TRUE if private lists should included (default is TRUE)
	 * @return mixed Array of lists or FALSE if an error occured
	 */
	public function getAllGutumaLists($load_addresses = FALSE, $inc_private = TRUE){
		$lists = array();
		if (!$this->isGutumaActivated) return $lists;
		if ($dh = @opendir(realpath(rtrim($this->GutumaListsDir,'/')))) {
			while (($file = readdir($dh)) !== FALSE) {
				if (!is_dir($file) && $this->strEnds($file, '.php')) {
					$list = $this->getGutumaList(substr($file, 0, strlen($file - 4)), $load_addresses);
					if ($inc_private || !$list->private)
						$lists[] = $list;	
				}
			}
			closedir($dh);
		}
		return $lists;
	}

	/**
	 * Gets the list with the specified id
	 * @param int $id The list id
	 * @param bool $load_addresses TRUE is list addresses should be loaded (default FALSE) 
	 * @return mixed The list or FALSE if an error occured
	 */
	public function getGutumaList($id, $load_addresses = FALSE){
		$time_start = microtime();
		$list = array();
		if (!$this->isGutumaActivated) return $list;
		// Open list file
		$lh = @fopen(realpath(rtrim($this->GutumaListsDir,'/').'/'.$id.'.php'), 'r');
		if ($lh == FALSE)
			return FALSE;
	
		// Read header from first line
		$header = explode("|", fgetss($lh));
		
		$list['id'] = $header[0];
		$list['name'] = $header[1];
		$list['private'] = (bool)$header[2];
		$list['size'] = (int)$header[3];
		
		// Read all address lines
		if ($load_addresses) {
			$addresses = array();
			while (!feof($lh)) {
				$address = trim(fgets($lh));
				if (strlen($address) > 0)
					$addresses[] = $address;
			}
			$list['addresses'] = $addresses;
		}
		
		fclose($lh);
		return $list;
	}

	/**
	 * Creates a new address list
	 * @param string $name The list name
	 * @param bool $private TRUE if the list should be private (default is FALSE)  
	 * @param array $addresses
	 * @return mixed The new list if it was successfully created, else FALSE
	 */
	public function createGutumaList($name, $private = FALSE, $addresses = NULL){
		if (!$this->isGutumaActivated) return true;
		if ($name == '' || preg_match('[^a-zA-Z0-9 \-]', $name))
			return FALSE;
			
		// Demo mode check for number of addresses
		if (isset($addresses) && count($addresses) >= 100)
			return FALSE;
		
		// Check for duplicate name
		$all_lists = $this->listsDiff;
		foreach ($all_lists as $l) {
			if (strcasecmp($l->name, $name) == 0)
				return FALSE;
		}
		
		// Demo mode check for number of lists
		if (count($all_lists) >= 100)
			return FALSE;
		
		$this->id = time();
		$this->name = $name;
		$this->private = $private;
		$this->addresses = isset($addresses) ? $addresses : array();
		
		// Save the list
		if (!$this->updateGutumaList()){
			return FALSE;
		}
		return $this;
	}

	/**
	 * Adds the specified address to this list
	 * @param string $address The address to add
	 * @param bool $update TRUE if list should be updated, else FALSE
	 * @return bool TRUE if the address was successfully added
	 */
	public function addAdressInGutumaList($address, $update){	
		if (!$this->isGutumaActivated) return true;
		if (in_array($address, $this->addresses))
			return FALSE;
		
		if (strlen($address) > 320)
			return FALSE;
		
		if (count($this->addresses) >= 100)
			return FALSE;
		
		// Add and then sort addresses alphabetically	
		$this->addresses[] = $address;
		natcasesort($this->addresses);
		
		if ($update) {
			if (!$this->updateGutumaList())
				return FALSE;
		}
		return TRUE;
	}

	/**
	 * Updates this address list, i.e., saves any changes
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function updateGutumaList() {
		if (!$this->isGutumaActivated) return true;
		$lh = @fopen(realpath(rtrim($this->GutumaListsDir,'/')).'/'.$this->id.'.php', 'w');
		if ($lh == FALSE)
			return FALSE;
		
		fwrite($lh, "<?php die(); ?>".$this->id.'|'.$this->name.'|'.($this->private ? '1' : '0').'|'.count($this->addresses)."\n");
		foreach ($this->addresses as $a)
			fwrite($lh, $a."\n");
							
		fclose($lh);
		return TRUE;
	}

	/**
	 * Removes the specified address from this list
	 * @param string $address The address to remove
	 * @param bool $update TRUE if list should be updated, else FALSE	 
	 * @return bool TRUE if operation was successful, else FALSE
	 */
	public function removeAdressFromGutumaList($address, $update){
		if (!$this->isGutumaActivated) return true;
		// Create new address array minus the one being removed
		$found = FALSE;
		$newaddresses = array();

		foreach ($this->addresses as $a) {
			if ($address != $a)
				$newaddresses[] = $a;
			else
				$found = TRUE;
		}
		
		if (!$found)
			return FALSE;
	
		$this->addresses = $newaddresses;
		
		if ($update) {
			if (!$this->updateGutumaList())
				return FALSE;
		}
		return TRUE;
	}

	///////////////////////////////////////////////////////////
	//
	// Méthodes permettant la gestion des adhérents
	//
	//////////////////////////////////////////////////////////

	/**
	 * Méthode qui selon le paramètre tri retourne sort ou rsort (tri PHP)
	 *
	 * @param	tri	asc ou desc
	 * @return	string
	 * @author	Stéphane F.
	 **/
	protected function mapTri($tri) {
		if($tri=='desc')
			return 'rsort';
		elseif($tri=='asc')
			return 'sort';
		elseif($tri=='alpha')
			return 'alpha';
		else
			return 'rsort';
	}

	/**
	 * Méthode qui récupere la liste des  articles
	 *
	 * @param	publi	before, after ou all => on récupère tous les fichiers (date) ?
	 * @return	boolean	vrai si articles trouvés, sinon faux
	 * @author	Stéphane F
	 **/
	public function getAdherents($motif) {

		# On fait notre traitement sur notre tri
		$ordre = $this->mapTri('asc');
		# On recupere nos fichiers (tries) selon le motif, la pagination, la date de publication
		if($aFiles = $this->plxGlob_adherents->query($motif,'',$ordre,0,false,'all')) {
			# on mémorise le nombre total d'articles trouvés
			foreach($aFiles as $k=>$v) # On parcourt tous les fichiers
				$array[$k] = $this->parseAdherent(PLX_ROOT.$this->getParam('adherents').'adhesions/'.$v);
			# On stocke les enregistrements dans un objet plxRecord
			$this->plxRecord_adherents = new plxRecord($array);
			return true;
		}
		else return false;
	}


	/**
	 * Méthode qui récupère les infos enregistrées dans le fichier xml d'un adhérent
	 * 
	 * @param $filename ressource le chemin vers le fichier de l'adhérent
	 * @return array
	 * 
	 * @author Cyril MAGUIRE
	 */
	public function parseAdherent($filename) {

		if(!is_file($filename)) return;
		
		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create(PLX_CHARSET);
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$values,$iTags);
		xml_parser_free($parser);
		if(isset($iTags['adherent']) AND isset($iTags['nom'])) {
			$nb = sizeof($iTags['nom']);
			$size=ceil(sizeof($iTags['adherent'])/$nb);
			for($i=0;$i<$nb;$i++) {
				$attributes = $values[$iTags['adherent'][$i*$size]]['attributes'];
				$adherent['id'] = $attributes['number'];
				# Recuperation du nom
				$adherent['nom']=plxUtils::getValue($values[$iTags['nom'][$i]]['value']);
				# Recuperation du prenom
				$adherent['prenom']=plxUtils::getValue($values[$iTags['prenom'][$i]]['value']);
				# Recuperation de l'adresse 1
				$adherent['adresse1']=plxUtils::getValue($values[$iTags['adresse1'][$i]]['value']);
				# Recuperation de l'adresse 2
				$adherent['adresse2']=plxUtils::getValue($values[$iTags['adresse2'][$i]]['value']);
				# Recuperation du code postal
				$adherent['cp']=plxUtils::getValue($values[$iTags['cp'][$i]]['value']);
				# Recuperation de la ville
				$adherent['ville']=plxUtils::getValue($values[$iTags['ville'][$i]]['value']);
				# Recuperation du téléphone
				$adherent['tel']=plxUtils::getValue($values[$iTags['tel'][$i]]['value']);
				# Recuperation du mail
				$adherent['mail']=plxUtils::getValue($values[$iTags['mail'][$i]]['value']);
				# Recuperation du choix quant à l'adhésion
				$adherent['choix']=plxUtils::getValue($values[$iTags['choix'][$i]]['value']);
				# Recuperation du choix pour le mailing
				$adherent['mailing']=plxUtils::getValue($values[$iTags['mailing'][$i]]['value']);
				# Recuperation du statut de l'adhérent
				$adherent['validation']=plxUtils::getValue($values[$iTags['validation'][$i]]['value']);
				# Recuperation de la date de première adhésion
				$adherent['firstDate']=plxUtils::getValue($values[$iTags['firstDate'][$i]]['value']);
				# Recuperation de la date de validation de l'adhésion
				$adherent['date']=plxUtils::getValue($values[$iTags['date'][$i]]['value']);
				# Recuperation de la chaine salt de l'adhérent
				$adherent['salt']=plxUtils::getValue($values[$iTags['salt'][$i]]['value']);
				# Recuperation du mot de passe cripté de l'adhérent
				$adherent['password']=plxUtils::getValue($values[$iTags['password'][$i]]['value']);
				# Recuperation de la clé
				$adherent['cle']=plxUtils::getValue($values[$iTags['cle'][$i]]['value']);
				# Recuperation des chaines aléatoires
				$adherent['rand1']=plxUtils::getValue($values[$iTags['rand1'][$i]]['value']);
				$adherent['rand2']=plxUtils::getValue($values[$iTags['rand2'][$i]]['value']);
				if ($this->getParam('typeAnnuaire') == 'professionnel') {
					# Recuperation de l'activité
					$adherent['activite']=plxUtils::getValue($values[$iTags['activite'][$i]]['value']);
					# Recuperation de l'établissement
					$adherent['etablissement']=plxUtils::getValue($values[$iTags['etablissement'][$i]]['value']);
					# Recuperation du service
					$adherent['service']=plxUtils::getValue($values[$iTags['service'][$i]]['value']);
					# Recuperation du poste
					$adherent['tel_office']=plxUtils::getValue($values[$iTags['tel_office'][$i]]['value']);
				}
				if ($this->getParam('showAnnuaire') == 'on') {
					# Recuperation du choix sur le partage des coordonnées
					$adherent['coordonnees']=plxUtils::getValue($values[$iTags['coordonnees'][$i]]['value']);
				}
			}
		}
		return $adherent;
	}

	/**
	 * Méthode permettant de formater un numéro de téléphone au format français ou international
	 */
	public function formatFrenchPhoneNumber($phoneNumber, $international = FALSE){
		//Supprimer tous les caractères qui ne sont pas des chiffres
		$phoneNumber = preg_replace('/[^0-9]+/', '', $phoneNumber);
		//Garder les 9 derniers chiffres
		$phoneNumber = substr($phoneNumber, -9);
		//On ajoute +33 si la variable $international vaut TRUE et 0 dans tous les autres cas
		$motif = $international ? '+33 (\1) \2 \3 \4 \5' : '0\1 \2 \3 \4 \5';
		$phoneNumber = preg_replace('/(\d{1})(\d{2})(\d{2})(\d{2})(\d{2})/', $motif, $phoneNumber);

		return $phoneNumber;
	} 

	/**
	 *  Méthode qui retourne le prochain id d'un adhérent
	 *
	 * @return	string		id d'un nouvel adhérent sous la forme 0001
	 * @author	Stephane F.
	 **/
	public function nextIdAdherent() {
		# On récupère l'ensemble des adhérents
		//$this->adherentsList = $this->getAdherent(PLX_ROOT.$this->getParam('adherents').'plugin.adhesion.adherents.xml');
		# On récupère le dernier identifiant
		if($aKeys = array_keys($this->plxGlob_adherents->aFiles)) {
			rsort($aKeys);
			return str_pad($aKeys['0']+1,5, '0', STR_PAD_LEFT);			
		} else {
			return '00001';
		}
	}

	/**
	 * Méthode permettant de supprimer les adhérents sélectionnés
	 * 
	 * @param $content 	array tableau contenant les index des adhérents à supprimer
	 * @param $mail 	array tableau optionnel contenant les paramètres des mails à envoyer pour confirmation
	 * 
	 * @return bool
	 * @author Cyril MAGUIRE
	 */
	public function deleteAdherentsList ($content,$mail=array(null)) {
		$action = FALSE;
		foreach($content['idAdherent'] as $k=>$id) {
			# Vérification de l'intégrité de l'identifiant
			if(!preg_match('/^[0-9]{5}$/',$id))
				return false;
			# Variable d'état
			$resDelAd = true;
			# Suppression de l'adhérent
			if($globAd = $this->plxGlob_adherents->query('/^'.$id.'.(.*).xml$/')) {
				unlink(PLX_ROOT.$this->getParam('adherents').'adhesions/'.$globAd['0']);
				$resDelAd = !file_exists(PLX_ROOT.$this->getParam('adherents').'adhesions/'.$globArt['0']);
			}
			$_SESSION['info'] = $this->getLang('L_ADMIN_REMOVE_ADH').'<br/>' ;
			$action = TRUE;
		}
		return $action;
	}

	/**
	 * Méthode permettant d'ajouter les mentions légales de la cnil aux mails envoyés avec le lien de suppression
	 * 
	 * @param $id 		string index de l'adhérent
	 * @param $mail 	string mail de l'adhérent
	 * @param $text 	string mail au format texte ou html (html par défaut)
	 * 
	 * @return string
	 * @author Cyril MAGUIRE
	 * 
	 */
	public function cnil($id=0,$mail='',$text=FALSE) {
		$plxMotor = plxMotor::getInstance();
		if ($id == 0 && $mail == '') {
			if ($text) {
				return 'Merci de ne pas répondre à cet e-mail. Celui-ci ayant été généré automatiquement, nous ne pourrons traiter votre réponse.'."\n".'
				Conformément à la Loi Informatique et Libertés du 06/01/1978 et à nos mentions légales vous disposez d\'un droit d\'accès et de rectification sur les données vous concernant.';
			} else {
				return '<p style="text-align:center;">Merci de ne pas répondre à cet e-mail. Celui-ci ayant été généré automatiquement, nous ne pourrons traiter votre réponse.<br/><hr/>Conformément à la Loi Informatique et Libertés du 06/01/1978 et à nos mentions légales vous disposez d\'un droit d\'accès et de rectification sur les données vous concernant.</p>';
			}
		} else {
			if ($text) {
				return 'Merci de ne pas répondre à cet e-mail. Celui-ci ayant été généré automatiquement, nous ne pourrons traiter votre réponse.'."\n".'
				Conformément à la Loi Informatique et Libertés du 06/01/1978 et à nos mentions légales vous disposez d\'un droit d\'accès et de rectification sur les données vous concernant.'."\n".'Pour vous désinscrire de la liste de diffusion, recopiez ce lien dans votre navigateur internet : '.$plxMotor->urlRewrite('?adhesion&q='.md5($id.'-'.$mail));
			} else {
				return '<p style="text-align:center;">Merci de ne pas répondre à cet e-mail. Celui-ci ayant été généré automatiquement, nous ne pourrons traiter votre réponse.<br/><hr/>Conformément à la Loi Informatique et Libertés du 06/01/1978 et à nos mentions légales vous disposez d\'un droit d\'accès et de rectification sur les données vous concernant.<br/><a href="'.$plxMotor->urlRewrite('?adhesion&q='.md5($id.'-'.$mail)).'">Se désinscrire de la liste de diffusion</a></p>';
			}
		}
	}

	/**
	 * Méthode permettant de comparer les données du lien cnil et le cas échéant de supprimer le compte associé des listes de diffusion
	 * 
	 * @param $value string 	$value = md5($id-$mail);
	 * 
	 * @return bool
	 * @author Cyril MAGUIRE
	 */
	public function compare($value) {
		$content['idAdherent'] = array();
		$mail = array();
		foreach ($this->plxRecord_adherents->result as $id => $array) {
			if ($value == md5($array['id'].'-'.$array['mail']) && $array['validation'] == 1) {
				$content['idAdherent'][] = $array['id'];
				foreach ($array as $key => $value) {
					$content[$key.'_'.$array['id']] = $value;
				}
				$content['mailing_'.$array['id']] = 'blacklist';
				$content['selection'][0] ='update';
				$addresses = $this->getAllGutumaLists(TRUE);
				$this->listDiff = $addresses[0]['addresses'];
				$this->editAdherentsList($content,$array['id'],TRUE);
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Générateur de chaines de caractères aléatoire
	 * 
	 * @param $nbLettres integer Nombre de caractères que la chaine contiendra
	 * @param $nbCaracteres integer Nombre de caracteres exotiques maximum que la chaine contiendra
	 * @param $caracteresSup array Caracteres exotiques que la chaine ne contiendra pas
	 * @param $voyellesSup array Voyelles que la chaine ne contiendra pas
	 * @param $consonnesSupp array Consonnes que la chaine ne contiendra pas
	 * 
	 * @return string la chaine aléatoire
	 * @author Cyril MAGUIRE
	 */
	public function generateurMot($nbLettres = 8,$nbCaracteres = 4,$caracteresSup = array(),$nombresSup = array(),$voyellesSup = array(),$consonnesSupp = array()) {
	
		$choix = array('consonnes','voyelles','caracteres','nombres');
		$mot = '';
		
		$consonnes = array('b','c','d','f','g','h','j','k','l','m','n','p','q','r','s','t','v','w','x','z');
		$voyelles = array('a','e','i','o','u','y');
		$caracteres = array('@','#','?','!','+','=','-','%','&','*');
		$nombres = array('0','1','2','3','4','5','6','7','8','9');
		
		if (!empty($consonnesSupp)) {
			$consonnes = array_diff($consonnes,$consonnesSupp);
		}
		if (!empty($voyellesSup)) {
			$voyelles = array_diff($voyelles,$voyellesSup);
		}
		if (!empty($caracteresSup)) {
			$caracteres = array_diff($caracteres,$caracteresSup);
		}
		if (!empty($nombresSup)) {
			$nombres = array_diff($nombres,$nombresSup);
		}
		
		if (empty($consonnes)) {
			$consonnes = array('b');
		}
		if (empty($voyelles)) {
			$voyelles = $consonnes;
		}
		if (empty($nombres)) {
			$nombres = $consonnes;
		}
		
		if ($nbCaracteres == 0) {
			$caracteres = $consonnes;
		}
		
		
		$j = 0;
		for($i=0;$i<$nbLettres;$i++) {
			//choix aléatoire entre consonnes et voyelles
			$rand = array_rand($choix,1);
			if ($rand == 3) {
				$j++;
			}
			if ($nbCaracteres != 0 && $j == ($nbCaracteres-1)) {
				$caracteres = $consonnes;
			}
			$type = $choix[$rand];
			$tab = $$type;
			//on recherche l'index d'une lettre, au hasard dans le tableau choisi
			$lettre = array_rand($$type,1);
			//on recherche la dernière lettre du mot généré
			if (strlen($mot) > 0) {
				$derniereLettre = $mot[strlen($mot)-1];
			} else {
				$derniereLettre = '';
			}
			
			//si la lettre choisie est déjà à la fin du mot généré, on relance la boucle
			if ($tab[$lettre] == $derniereLettre || in_array($derniereLettre,$tab)) {
				$i--;
			} else {//sinon on l'ajoute au mot généré
				$maj = mt_rand(0,10);
				if ($maj<2) {
					$mot .= strtoupper($tab[$lettre]);	
				} else {
					$mot .= $tab[$lettre];	
				}
			}
		}
		
		return $mot;
	} 

	/**
	 * Méthode permettant de définir un mot de passe et une clé lors d'une nouvelle inscription validée
	 * 
	 * @param $id 	string index de l'adhérent
	 * 
	 * @return $password string mot de passe de l'adhérent en clair
	 * @author Cyril MAGUIRE
	 */
	public function defPassword($id) {
		if (array_key_exists($id, $this->adherentsList)){
			$id = $this->adherentsList[$id];
		}else {
			$id = end($this->adherentsList) + 1;
		}

		if ($this->plxRecord_adherents->result[$id]['firstDate'] == '') {
			$this->plxRecord_adherents->result[$id]['firstDate'] = time();
		}
		# controle du mot de passe
		$salt = empty($this->plxRecord_adherents->result[$id]['salt']) ? plxUtils::charAleatoire(10) : $this->plxRecord_adherents->result[$id]['salt'];
		$this->plxRecord_adherents->result[$id]['salt'] = $salt;
		//Définition du mot de passe
			//Définition de la clé
			$cle = plxUtils::charAleatoire($this->getParam('cle'));
			$rand1 = mt_rand(1,9);
			$rand2 = $this->generateurMot(3,1);
			$password = $cle.'-'.substr($this->plxRecord_adherents->result[$id]['mail'],0,-$rand1).$rand2;
		//Attribution du mot de passe à l'adhérent
		$this->plxRecord_adherents->result[$id]['password'] = empty($this->plxRecord_adherents->result[$id]['password']) ? sha1($salt.md5($password)) : $this->plxRecord_adherents->result[$id]['password'];
		$this->plxRecord_adherents->result[$id]['rand1'] = $rand1;
		$this->plxRecord_adherents->result[$id]['rand2'] = $rand2;
		$this->plxRecord_adherents->result[$id]['cle'] = $cle;
		return $password;
	}


	/**
	 * Méthode qui retourne les informations $output en analysant
	 * le nom du fichier de l'adhérent $filename
	 *
	 * @param	filename	fichier de l'adhérent à traiter
	 * @return	array		information à récupérer
	 * @author	Stephane F
	 **/
	public function adInfoFromFilename($filename) {

		# On effectue notre capture d'informations
		if(preg_match('/([0-9]{5}).([a-z-]+).([a-z-]+).([0-9]{10}).xml$/',$filename,$capture)) {
			return array(
				'adId'		=> $capture[1],
				'nom'		=> $capture[2],
				'prenom'	=> $capture[3],
				'firstDate'	=> $capture[4]
			);
		}
	}

	/**
	 * Méthode permettant de mettre à jour un compte adhérent
	 * 
	 * @param $content array tableau contenant les informations des comptes à mettre à jour
	 * 
	 * @return $mail array tableau contenant les paramètres des mails à envoyer aux adhérents
	 * @author Cyril MAGUIRE
	 */
	public function updateAdherentsList($content) {
		$mail = array();
		
		foreach($content['idAdherent'] as $key => $id) {
				$ad = $this->adherentsList[$id];
				//L'adhérent souhaite ne plus faire partie de l'association
				if ($content['choix_'.$id] == 'stop') {
					//On retire l'email de la liste de diffusion
					if (in_array($content['mail_'.$id],$this->listDiff)) {
						$this->removeAdressFromGutumaList($content['mail_'.$id],TRUE);
						$_SESSION['info'] = $this->getLang('L_ADMIN_REMOVE_ADD').'<br/>' ;
					}
					$this->plxRecord_adherents->result[$ad]['choix'] = $content['choix_'.$id];
					$_SESSION['info'] = $this->getLang('L_ADMIN_REMOVE_ADH').'<br/>' ;
					$mail[] = array(
						'name'=>$this->getParam('nom_asso'),
						'from'=>$this->getParam('email'),
						'to'=>$content['mail_'.$id],
						'subject'=>$this->getParam('devalidation_subject'),
						'body'=>'<p>'.$this->getParam('devalidation_msg').'</p>'.$this->cnil(),
						'contentType'=>'html',
						'cc'=>FALSE,
						'bcc'=>FALSE,
						'notification' => array(
							'adherent' => $this->plxRecord_adherents->result[$ad],
							'sujet' => 'Suppression'
						)
					);
					$this->deleteAdherentsList($content,$mail);
					return FALSE;
				}
				elseif($content['nom_'.$id]!='') {

					$this->plxRecord_adherents->result[$ad]['nom'] = $content['nom_'.$id];
					$this->plxRecord_adherents->result[$ad]['prenom'] = $content['prenom_'.$id];
					$this->plxRecord_adherents->result[$ad]['adresse1'] = $content['adresse1_'.$id];
					$this->plxRecord_adherents->result[$ad]['adresse2'] = $content['adresse2_'.$id];
					$this->plxRecord_adherents->result[$ad]['cp'] = intval($content['cp_'.$id]);
					$this->plxRecord_adherents->result[$ad]['ville'] = $content['ville_'.$id];
					$this->plxRecord_adherents->result[$ad]['tel'] = $this->formatFrenchPhoneNumber($content['tel_'.$id]);
					$this->plxRecord_adherents->result[$ad]['mail'] = $content['mail_'.$id];
					$this->plxRecord_adherents->result[$ad]['mailing'] = $content['mailing_'.$id];
					$this->plxRecord_adherents->result[$ad]['choix'] = $content['choix_'.$id];
					if ($this->getParam('typeAnnuaire') == 'professionnel') {
						if ($content['activite_'.$id] == 'autre') {
							$this->plxRecord_adherents->result[$ad]['activite'] = $content['activite_autre_'.$id];
						} else {
							$this->plxRecord_adherents->result[$ad]['activite'] = $content['activite_'.$id];
						}
						$this->plxRecord_adherents->result[$ad]['etablissement'] = $content['etablissement_'.$id];
						$this->plxRecord_adherents->result[$ad]['service'] = $content['service_'.$id];
						$this->plxRecord_adherents->result[$ad]['tel_office'] = $this->formatFrenchPhoneNumber($content['tel_office_'.$id]);
					}
					if ($this->getParam('showAnnuaire') == 'on') {
						$this->plxRecord_adherents->result[$ad]['coordonnees'] = $content['coordonnees_'.$id];
					}

					//On retire l'email de la liste de diffusion si c'est le choix de l'adhérent
					if (in_array($content['mail_'.$id],$this->listDiff) && $content['mailing_'.$id] == 'blacklist') {
						$this->removeAdressFromGutumaList($content['mail_'.$id],TRUE);
						$_SESSION['info'] = $this->getLang('L_ADMIN_REMOVE_ADD').'<br/>' ;
					} elseif (!in_array($content['mail_'.$id],$this->listDiff)  && $content['mailing_'.$id] != 'blacklist' && ($content['selection'][0]=='update' || $content['selection'][1]=='update')) {
						$this->addAdressInGutumaList($content['mail_'.$id],TRUE);
						$_SESSION['info'] = $this->getLang('L_ADMIN_ADD_ADD').'<br/>' ;
					}

					$this->plxRecord_adherents->result[$ad]['mailing'] = $content['mailing_'.$id];
					if ($content['selection'][0]!='update' && $content['selection'][1]!='update') {

						if ($this->plxRecord_adherents->result[$ad]['validation'] == 1 && $content['choix_'.$id] == 'renouveler') {
							$plxMotor = plxMotor::getInstance();
							$_SESSION['erase'] = '<p id="password_error">'.$this->getLang('L_ERR_USER_ALREADY_VALID').'</p>';
							header('Location:'.$plxMotor->urlRewrite());
							exit();
						}
						$this->plxRecord_adherents->result[$ad]['validation'] = intval($content['validation_'.$id]);

						//Si l'inscription n'est pas validée
						//On supprime l'inscription de l'adhérent mais on conserve ses coordonnées
						if ($this->plxRecord_adherents->result[$ad]['validation'] == 0 && $content['choix_'.$id] != 'renouveler') {
							//On retire l'email de la liste de diffusion
							if (in_array($content['mail_'.$id],$this->listDiff)) {
								$this->removeAdressFromGutumaList($content['mail_'.$id],TRUE);
								$_SESSION['info'] = $this->getLang('L_ADMIN_REMOVE_ADD').'<br/>' ;
							}

							//On supprime le contrôle de mot de passe
							$this->plxRecord_adherents->result[$ad]['salt'] = '';
							//On supprime le mot de passe
							$this->plxRecord_adherents->result[$ad]['password'] = '';
							//On supprime la clé
							$this->plxRecord_adherents->result[$ad]['cle'] = '';
							//On supprime les chaines aléatoires
							$this->plxRecord_adherents->result[$ad]['rand1'] = '';
							$this->plxRecord_adherents->result[$ad]['rand2'] = '';
							//On supprime la date de la validation
							$this->plxRecord_adherents->result[$ad]['date'] = time();

							$mail[] = array(
								'name'=>$this->getParam('nom_asso'),
								'from'=>$this->getParam('email'),
								'to'=>$content['mail_'.$id],
								'subject'=>$this->getParam('devalidation_subject'),
								'body'=>'<p>'.$this->getParam('devalidation_msg').'</p>'.$this->cnil(),
								'contentType'=>'html',
								'cc'=>FALSE,
								'bcc'=>FALSE,
								'notification' => array(
									'adherent' => $this->plxRecord_adherents->result[$ad],
									'sujet' => 'Modification'
								)
							);
							$_SESSION['info'] = $this->getLang('L_ADMIN_DEVALIDATION_ADH').'<br/>' ;
						}
						//Si c'est une demande de renouvellement
						if ($this->plxRecord_adherents->result[$ad]['validation'] == 0 && $content['choix_'.$id] == 'renouveler') {
							
							//On supprime le contrôle de mot de passe
							$this->plxRecord_adherents->result[$ad]['salt'] = '';
							//On supprime le mot de passe
							$this->plxRecord_adherents->result[$ad]['password'] = '';
							//On supprime la clé
							$this->plxRecord_adherents->result[$ad]['cle'] = '';
							//On supprime les chaines aléatoires
							$this->plxRecord_adherents->result[$ad]['rand1'] = '';
							$this->plxRecord_adherents->result[$ad]['rand2'] = '';
							//On supprime la date de la validation
							$this->plxRecord_adherents->result[$ad]['date'] = time();

							$mail[] = array(
								'name'=>$this->getParam('nom_asso'),
								'from'=>$this->getParam('email'),
								'to'=>$content['mail_'.$id],
								'subject'=>$this->getParam('subject'),
								'body'=>'<p>'.$this->getParam('thankyou').'</p>'.$this->adresse().$this->cnil(),
								'contentType'=>'html',
								'cc'=>FALSE,
								'bcc'=>FALSE,
								'notification' => array(
									'adherent' => $this->plxRecord_adherents->result[$ad],
									'sujet' => 'Modification'
								)
							);
							$_SESSION['info'] = $this->getLang('L_FORM_NEW').'<br/>' ;
						}
						//On valide l'inscription de l'adhérent
						if (intval($content['validation_'.$id]) == 1) {
							//On ajoute l'email à l'ensemble des mails de la liste de diffusion
							if ($content['mailing_'.$id] == 'maillist') {
								$this->addAdressInGutumaList($content['mail_'.$id],TRUE);
								$_SESSION['info'] = $this->getLang('L_ADMIN_ADD_ADD').'<br/>' ;
							}
							if (!empty($this->plxRecord_adherents->result[$ad]['cle']) && !empty($this->plxRecord_adherents->result[$ad]['mail']) && !empty($this->plxRecord_adherents->result[$ad]['rand1']) && !empty($this->plxRecord_adherents->result[$ad]['rand2'])) {
								//Affichage du mot de passe
								$password = $this->plxRecord_adherents->result[$ad]['cle'].'-'.substr($this->plxRecord_adherents->result[$ad]['mail'],0,-$this->plxRecord_adherents->result[$ad]['rand1']).$this->plxRecord_adherents->result[$ad]['rand2'];
							} else {
								$password = $this->defPassword($id);
								$this->plxRecord_adherents->result[$ad]['password'] = sha1($this->plxRecord_adherents->result[$ad]['salt'].md5($password));
							}
							
							$this->plxRecord_adherents->result[$ad]['validation'] = intval($content['validation_'.$id]);

							//On ajoute la date de la validation
							$this->plxRecord_adherents->result[$ad]['date'] = time();
							if (empty($this->plxRecord_adherents->result[$ad]['firstDate'])) {
								$this->plxRecord_adherents->result[$ad]['firstDate'] = $this->plxRecord_adherents->result[$ad]['date'];
							}

							$mail[] = array(
								'name'=>$this->getParam('nom_asso'),
								'from'=>$this->getParam('email'),
								'to'=>$content['mail_'.$id],
								'subject'=>$this->getParam('validation_subject'),
								'body'=>'<p>'.$this->getParam('validation_msg').'</p><p>&nbsp;</p><p>'.$this->getLang('L_ADMIN_ID').'</p><p>&nbsp;</p><p>'.$this->getLang('L_ADMIN_PASSWORD').'</p><p><strong>'.$password.'</strong></p>'.$this->cnil($id,$content['mail_'.$id]),
								'contentType'=>'html',
								'cc'=>FALSE,
								'bcc'=>FALSE,
								'notification' => array(
									'adherent' => $this->plxRecord_adherents->result[$ad],
									'sujet' => 'Validation'
								)
							);
							$_SESSION['info'] = $this->getLang('L_ADMIN_NEW_ADH').'<br/>' ;
						}
					}else {
						if ($this->plxRecord_adherents->result[$ad]['password'] != '') {
							$_SESSION['info'] = $this->getLang('L_ADMIN_UPDATE_ADH').'<br/>' ;
							$this->plxRecord_adherents->result[$ad]['validation'] = intval($content['validation_'.$id]);
							//On n'envoie pas de mail de confirmation
							$mail = array(null);
						} else {
							$password = $this->plxRecord_adherents->result[$ad]['cle'].'-'.substr($this->plxRecord_adherents->result[$ad]['mail'],0,-$this->plxRecord_adherents->result[$ad]['rand1']).$this->plxRecord_adherents->result[$ad]['rand2'];
							$this->plxRecord_adherents->result[$ad]['validation'] = intval($content['validation_'.$id]);
							$mail[] = array(
								'name'=>$this->getParam('nom_asso'),
								'from'=>$this->getParam('email'),
								'to'=>$content['mail_'.$id],
								'subject'=>$this->getParam('validation_subject'),
								'body'=>'<p>'.$this->getParam('validation_msg').'</p><p>Votre mot de passe est :</p><p><strong>'.$password.'</strong></p>'.$this->cnil($id,$content['mail_'.$id]),
								'contentType'=>'html',
								'cc'=>FALSE,
								'bcc'=>FALSE,
								'notification' => array(
									'adherent' => $this->plxRecord_adherents->result[$ad],
									'sujet' => 'Modification'
								)
							);
						}
					}
				}
			}
		return $mail;
	}
	
	/**
	 * Méthode qui insère un nouvel adhérent dans la liste des adhérents
	 *
	 * @param	content	tableau multidimensionnel du plugin adhesion
	 * 
	 * @return	bool
	 * @author	MAGUIRE Cyril
	 **/
	public function insertNewAdherent($content) {
		$mail = array();
		$id = $content['new'];

		$isAlreadyExists = $this->isAlreadyExists($content,$id);
		if ($isAlreadyExists) {
			if ($content['choix_'.$id] != 'adhesion') {
				foreach ($this->plxRecord_adherents->result as $key => $value) {
					if($value['mail'] == $content['mail_'.$id]) {
						$adherent = array(
		                    'nom_'.$key => $value['nom'],
		                    'prenom_'.$key => $value['prenom'],
		                    'adresse1_'.$key => $value['adresse1'],
		                    'adresse2_'.$key => $value['adresse2'],
		                    'cp_'.$key => $value['cp'],
		                    'ville_'.$key => $value['ville'],
		                    'tel_'.$key => $value['tel'],
		                    'mail_'.$key => $value['mail'],
		                    'choix_'.$key => $content['choix_'.$id],
		                    'mailing_'.$key => $content['mailing_'.$id],
		                    'validation_'.$key => $value['validation']
		                );
		                if ($this->getParam('typeAnnuaire') == 'professionnel') {
							if ($value['activite'] == 'autre') {
								$adherent['activite'] = $value['activite_autre'];
							} else {
								$adherent['activite'] = $value['activite'];
							}
							$adherent['etablissement'] = $value['etablissement'];
							$adherent['service'] = $value['service'];
							$adherent['tel_office'] = $value['tel_office'];
						}
						if ($this->getParam('showAnnuaire') == 'on') {
							$adherent['coordonnees'] = $content['coordonnees'];
						}
						$adherent['idAdherent'][] = $key;
					}
				}
				$mail = $this->updateAdherentslist($adherent);
				
				unset($content);
			} else {
				$plxMotor = plxMotor::getInstance();
				if ($plxMotor->mode == 'static') {
					$_SESSION['erase'] = '<p id="password_error">'.$this->getLang('L_ERR_USER_ALREADY_USED').'</p>';
					header('Location:'.$plxMotor->urlRewrite());
					exit();
				} else {
					$_SESSION['error'] = $this->getLang('L_ERR_USER_ALREADY_USED');
					header('Location:'.$plxMotor->urlRewrite('core/admin/plugin.php').'?p=adhesion');
					exit();
				}
					
			}
		}
		if(isset($content['nom_'.$id]) && $content['nom_'.$id] !='') {

			$ad = end($this->adherentsList)+1;

			$this->plxRecord_adherents->result[$ad]['nom'] = $content['nom_'.$id];
			$this->plxRecord_adherents->result[$ad]['prenom'] = $content['prenom_'.$id];
			$this->plxRecord_adherents->result[$ad]['adresse1'] = $content['adresse1_'.$id];
			$this->plxRecord_adherents->result[$ad]['adresse2'] = $content['adresse2_'.$id];
			$this->plxRecord_adherents->result[$ad]['cp'] = $content['cp_'.$id];
			$this->plxRecord_adherents->result[$ad]['ville'] = $content['ville_'.$id];
			$this->plxRecord_adherents->result[$ad]['tel'] = $this->formatFrenchPhoneNumber($content['tel_'.$id]);
			$this->plxRecord_adherents->result[$ad]['mail'] = $content['mail_'.$id];
			$this->plxRecord_adherents->result[$ad]['choix'] = $content['choix_'.$id];
			$this->plxRecord_adherents->result[$ad]['mailing'] = $content['mailing_'.$id];
            if ($this->getParam('typeAnnuaire') == 'professionnel') {
				if ($content['activite_'.$id] == 'autre') {
					$this->plxRecord_adherents->result[$ad]['activite'] = $content['activite_autre_'.$id];
				} else {
					$this->plxRecord_adherents->result[$ad]['activite'] = $content['activite_'.$id];
				}
				$this->plxRecord_adherents->result[$ad]['etablissement'] = $content['etablissement_'.$id];
				$this->plxRecord_adherents->result[$ad]['service'] = $content['service_'.$id];
				$this->plxRecord_adherents->result[$ad]['tel_office'] = $content['tel_office_'.$id];
			}
			if ($this->getParam('showAnnuaire') == 'on') {
				$this->adherentsList[$ad]['coordonnees'] = $content['coordonnees_'.$id];
			}
			//Définition du mot de passe
			$password = $this->defPassword($id);
			$this->plxRecord_adherents->result[$ad]['password'] = sha1($this->plxRecord_adherents->result[$ad]['salt'].md5($password));
			
			$this->plxRecord_adherents->result[$ad]['validation'] = 0;
			$this->plxRecord_adherents->result[$ad]['firstDate'] = '';
			$this->plxRecord_adherents->result[$ad]['date'] = '';
			$_SESSION['info'] = $this->getLang('L_ADMIN_NEW_ADH').'<br/>' ;
			$mail[] = array(
					'name'=>$this->getParam('nom_asso'),
					'from'=>$this->getParam('email'),
					'to'=>$content['mail_'.$id],
					'subject'=>$this->getParam('subject'),
					'body'=>'<p>'.$this->getParam('thankyou').'</p>'.$this->adresse().$this->cnil($id,$content['mail_'.$id]),
					'contentType'=>'html',
					'cc'=>FALSE,
					'bcc'=>FALSE
				);

		}
		
		return $mail;
	}

	/**
	 * Méthode qui écrit le fichier XML selon la liste des adhérents
	 *
	 * @param	$mail array tableau contenant les paramètres du mail à envoyer lors de la mise à jour du fichier
	 * 
	 * @return	bool
	 * @author	MAGUIRE Cyril
	 **/
	public function recAdherentsList($mail,$id=false) {
			if ($id === false) {
				$id = $this->nextIdAdherent();
				$ad = end($this->adherentsList)+1;
 			} else {
 				$ad = $this->adherentsList[$id];
 			}
			$adherent = $this->plxRecord_adherents->result[$ad];
			# On génére le fichier XML
			$xml = "<?xml version=\"1.0\" encoding=\"".PLX_CHARSET."\"?>\n";
			$xml .= "<document>\n";
				$xml .= "\t<adherent number=\"".$id."\">\n\t\t";
				$xml .= "<nom><![CDATA[".plxUtils::cdataCheck($adherent['nom'])."]]></nom>\n\t\t";
				$xml .= "<prenom><![CDATA[".plxUtils::cdataCheck($adherent['prenom'])."]]></prenom>\n\t\t";
				$xml .= "<adresse1><![CDATA[".plxUtils::cdataCheck($adherent['adresse1'])."]]></adresse1>\n\t\t";
				$xml .= "<adresse2><![CDATA[".plxUtils::cdataCheck($adherent['adresse2'])."]]></adresse2>\n\t\t";
				$xml .= "<cp><![CDATA[".plxUtils::cdataCheck($adherent['cp'])."]]></cp>\n\t\t";
				$xml .= "<ville><![CDATA[".plxUtils::cdataCheck($adherent['ville'])."]]></ville>\n\t\t";
				$xml .= "<tel><![CDATA[".plxUtils::cdataCheck($adherent['tel'])."]]></tel>\n\t\t";
				$xml .= "<mail><![CDATA[".plxUtils::cdataCheck($adherent['mail'])."]]></mail>\n\t\t";
				$xml .= "<choix><![CDATA[".plxUtils::cdataCheck($adherent['choix'])."]]></choix>\n\t\t";
				$xml .= "<mailing><![CDATA[".plxUtils::cdataCheck($adherent['mailing'])."]]></mailing>\n\t\t";
				$xml .= "<salt><![CDATA[".plxUtils::cdataCheck($adherent['salt'])."]]></salt>\n\t\t";
				$xml .= "<password><![CDATA[".plxUtils::cdataCheck($adherent['password'])."]]></password>\n\t\t";
				$xml .= "<rand1><![CDATA[".plxUtils::cdataCheck($adherent['rand1'])."]]></rand1>\n\t\t";
				$xml .= "<rand2><![CDATA[".plxUtils::cdataCheck($adherent['rand2'])."]]></rand2>\n\t\t";
				$xml .= "<cle><![CDATA[".plxUtils::cdataCheck($adherent['cle'])."]]></cle>\n\t\t";
				$xml .=	"<validation>".plxUtils::cdataCheck($adherent['validation'])."</validation>\n\t\t";
				$xml .=	"<firstDate>".plxUtils::cdataCheck($adherent['firstDate'])."</firstDate>\n\t\t";
				$xml .=	"<date>".plxUtils::cdataCheck($adherent['date'])."</date>\n\t\t";
				if ($this->getParam('typeAnnuaire') == 'professionnel') {
				$xml .= "<activite><![CDATA[".plxUtils::cdataCheck($adherent['activite'])."]]></activite>\n\t\t";
				$xml .= "<etablissement><![CDATA[".plxUtils::cdataCheck($adherent['etablissement'])."]]></etablissement>\n\t\t";
				$xml .= "<service><![CDATA[".plxUtils::cdataCheck($adherent['service'])."]]></service>\n\t\t";
				$xml .= "<tel_office><![CDATA[".plxUtils::cdataCheck($adherent['tel_office'])."]]></tel_office>\n\t";
				}
				if ($this->getParam('showAnnuaire') == 'on') {
				$xml .= "\t<coordonnees><![CDATA[".plxUtils::cdataCheck($adherent['coordonnees'])."]]></coordonnees>\n\t";
				}
				$xml .= "</adherent>\n";
			$xml .= "</document>";

			$time = (empty($adherent['firstDate'])) ? time() : plxUtils::cdataCheck($adherent['firstDate']);

			if (is_file(PLX_ROOT.$this->getParam('adherents').'adhesions/'.$this->plxGlob_adherents->aFiles[$id])) {
				$fileName = $this->plxGlob_adherents->aFiles[$id];
			} else {
				$fileName = $id.'.'.plxUtils::title2filename(plxUtils::cdataCheck($adherent['nom']).'.'.plxUtils::cdataCheck($adherent['prenom'])).'.'.time().'.xml';
			}
			# On écrit le fichier
			if(plxUtils::write($xml, PLX_ROOT.$this->getParam('adherents').'adhesions/'.$fileName)) {
			//chmod(PLX_ROOT.$this->getParam('adherents').'adhesions/'.$fileName.'.xml',0777);
				if ($mail != array(null) && $mail != NULL) {
					if (is_array($mail)) {
						$action = FALSE;
						foreach ($mail as $key => $m) {
							if (isset($m['notification'])) {
								if ($m['notification']['adherent']['validation'] == 0 || $m['notification']['adherent']['choix'] == 'stop') {
									$pro = array();
									if ($this->getParam('typeAnnuaire') == 'professionnel') {
										$pro['activite'] = $m['notification']['adherent']['activite'];
										$pro['etablissement'] = $m['notification']['adherent']['etablissement'];
										$pro['service'] = $m['notification']['adherent']['service'];
										$pro['tel_office'] = $m['notification']['adherent']['tel_office'];
									}
									if ($this->getParam('typeAnnuaire') == 'professionnel' && $this->getParam('showAnnuaire') == 'on') {
										$pro['coordonnees'] = $m['notification']['adherent']['coordonnees'];
									}
									$body = $this->notification(
										$m['notification']['adherent']['nom'],
										$m['notification']['adherent']['prenom'],
										$m['notification']['adherent']['adresse1'],
										$m['notification']['adherent']['adresse2'],
										$m['notification']['adherent']['cp'],
										$m['notification']['adherent']['ville'],
										$m['notification']['adherent']['tel'],
										$m['notification']['adherent']['mail'],
										$m['notification']['adherent']['choix'],
										$m['notification']['adherent']['mailing'],
										$pro);

									$this->sendEmail($this->getParam('nom_asso'), $this->getParam('email'), $this->getParam('email'), $m['sujet'], $body, 'html');
								}
							}
							if($this->sendEmail($m['name'], $m['from'], $m['to'], $m['subject'], $m['body'], $m['contentType'], $m['cc'], $m['bcc'])){
								$action =  TRUE;
							}else{
								$action = FALSE;
							}
						}
						return $action;
					} else {
						if($this->sendEmail($mail['name'], $mail['from'], $mail['to'], $mail['subject'], $mail['body'], $mail['contentType'], $mail['cc'], $mail['bcc'])){
							return TRUE;
						}else{
							return FALSE;
						}
					}
				} else {
					return TRUE;
				}
			}else {
				return FALSE;
			}	
	}
	/**
	 * Méthode qui édite le fichier XML du plugin adhesion selon le tableau $content
	 *
	 * @param	$content 	array	tableau multidimensionnel du plugin adhesion
	 * @param 	$id 		string 	index de l'adhérent
	 * @param	$action		bool 	permet de forcer la mise à jour du fichier
	 * 
	 * @return	string
	 * @author	MAGUIRE Cyril
	 **/
	public function editAdherentslist($content, $id = 0, $action=FALSE) {
		
		$error = FALSE;
		$success = FALSE;
		$_SESSION['info'] = '';
		$_SESSION['error'] = '';

		if ($id == 0 && empty($content['adherentNum'])) {
			$content['adherentNum'][] = $this->nextIdAdherent();
		}

		// On force la mise à jour
		if($action===TRUE){
			$mail = $this->updateAdherentsList($content);
			if (is_array($mail)) {
				$action = TRUE;
			} else {
				$action = FALSE;
			}
		}
		# suppression
		elseif(!empty($content['selection']) && ($content['selection'][0]=='delete' || $content['selection'][1]=='delete') && isset($content['idAdherent'])) {
			$this->deleteAdherentsList($content);
			$action = TRUE;
		} 
		# mise à jour de la liste des adhérents
		elseif(isset($content['update']) && $content['update'] == true && ($content['selection'][0]=='validation' || $content['selection'][1]=='validation' || $content['selection'][0]=='update' || $content['selection'][1]=='update')) {
			if ($content['selection'][0] == 'update' || $content['selection'][1] == 'update') {
				foreach ($content['idAdherent'] as $key => $value) {
					$content['validation_'.$value] = $this->plxRecord_adherents->result[$this->adherentsList[$value]]['validation'];
				}
			}
			$mail = $this->updateAdherentsList($content);
			if (is_array($mail)) {
				$action = TRUE;
			} else {
				$action = FALSE;
			}
		} 
		# nouvel enregistrement dans la liste des adhérents depuis l'administration
		elseif( isset($content['new']) && $content['choix_'.$content['new']] == 'adhesion' && $content['adherentNum'][0] == $content['new']){
			$mail = $this->insertNewAdherent($content);
			if (is_array($mail)) {
				$action = TRUE;
				$content['idAdherent'][] = false;
			} else {
				$action = FALSE;
			}
		}
		# nouvel enregistrement dans la liste des adhérents depuis la partie publique
		elseif(isset($content['wall-e']) && empty($content['wall-e']) && $content['choix_'.$id] == 'adhesion'){
			$content['new'] = $id;
			$mail = $this->insertNewAdherent($content);
			if (is_array($mail)) {
				$action = TRUE;
				$content['idAdherent'][] = false;
			} else {
				$action = FALSE;
			}
		}

		# sauvegarde
		if($action) {
			foreach ($content['idAdherent'] as $key => $id) {
				$this->recAdherentsList($mail,$id);	
			}
		}
	}

	/**
	 * Méthode qui vérifie si un nouvel enregistrement n'est pas déjà présent dans la liste des adhérents
	 * 
	 * @param array $adherent tableau contenant les renseignements sur l'enregistrement qui doit être vérifié
	 * @param integer $id index de l'enregistrement en cours
	 * 
	 * @return bool
	 * @author Cyril MAGUIRE
	 */
	public function isAlreadyExists($adherent,$id) {
		$verif = $this->plxRecord_adherents->result;
		$search['nom'] = strtolower($adherent['nom_'.$id]);
		$search['prenom'] = strtolower($adherent['prenom_'.$id]);
		$search['ville'] = strtolower($adherent['ville_'.$id]);
		$search['mail'] = $adherent['mail_'.$id];
		
		foreach ($verif as $index => $data) {
			if ($search['mail'] == $data['mail']) {
				return TRUE;
			}
			if ($search['nom'] == strtolower($data['nom']) && $search['prenom'] == strtolower($data['prenom']) && $search['ville'] == strtolower($data['ville'])) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Méthode permettant de retrouver le mot de passe associé à un compte
	 * 
	 * @param $email string  l'email du compte dont il faut retrouver le mot de passe
	 * 
	 * @return string la clé associée au compte
	 * @author Cyril MAGUIRE
	 */
	public function retrieveMyPass($email) {
		foreach ($this->plxRecord_adherents->result as $key => $compte) {
			if ($compte['mail'] == $email) {
				$m = array(
					'name'=>$this->getParam('nom_asso'),
					'from'=>$this->getParam('email'),
					'to'=>$email,
					'subject'=>$this->getParam('subject_password'),
					'body'=>'<p>'.$this->getParam('msg_password').'<br/>'.$compte['cle'].'-'.substr($email,0,-$compte['rand1']).$compte['rand2'].'</p>'.$this->cnil($key,$email),
					'contentType'=>'html',
					'cc'=>FALSE,
					'bcc'=>FALSE
				);//print_r($m);exit();
				if($this->sendEmail($m['name'], $m['from'], $m['to'], $m['subject'], $m['body'], $m['contentType'], $m['cc'], $m['bcc'])){
					return TRUE;
				}else{
					return FALSE;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Méthode permettant d'éditer les paramètres d'un compte adhérent
	 * 
	 * @param $compte array tableau des paramètres du compte
	 * @param $id string index du compte dans la liste des adhérents
	 * 
	 * @return bool
	 * @author Cyril MAGUIRE
	 */
	public function editMyAccount($compte,$ad) {

		$id = $this->adherentsList[$ad];

		$this->plxRecord_adherents->result[$id]['nom'] = $compte['nom'];
		$this->plxRecord_adherents->result[$id]['prenom'] = $compte['prenom'];
		$this->plxRecord_adherents->result[$id]['adresse1'] = $compte['adresse1'];
		$this->plxRecord_adherents->result[$id]['adresse2'] = $compte['adresse2'];
		$this->plxRecord_adherents->result[$id]['cp'] = intval($compte['cp']);
		$this->plxRecord_adherents->result[$id]['ville'] = $compte['ville'];
		$this->plxRecord_adherents->result[$id]['tel'] = $this->formatFrenchPhoneNumber($compte['tel']);
		$this->plxRecord_adherents->result[$id]['mail'] = $compte['mail'];
		$this->plxRecord_adherents->result[$id]['choix'] = $compte['choix'];
		$this->plxRecord_adherents->result[$id]['mailing'] = $compte['mailing'];
		if ($this->getParam('typeAnnuaire') == 'professionnel') {
			if ($compte['activite'] == 'autre') {
				$this->plxRecord_adherents->result[$id]['activite'] = $compte['activite_autre'];
			} else {
				$this->plxRecord_adherents->result[$id]['activite'] = $compte['activite'];
			}
			$this->plxRecord_adherents->result[$id]['etablissement'] = $compte['etablissement'];
			$this->plxRecord_adherents->result[$id]['service'] = $compte['service'];
			$this->plxRecord_adherents->result[$id]['tel_office'] = $compte['tel_office'];
		}
		if ($this->getParam('showAnnuaire') == 'on') {
			$this->plxRecord_adherents->result[$id]['coordonnees'] = $compte['coordonnees'];
		}
		//On ajoute l'email à l'ensemble des mails de la liste de diffusion
		if ($compte['mailing'] == 'maillist') {
			$this->addAdressInGutumaList($compte['mail'],TRUE);
		}
		if ($compte['choix'] == 'stop') {
			$content['idAdherent'] = array(0 => $ad);
			return $this->deleteAdherentsList($content);
		} else {
			return $this->recAdherentsList(false,$ad);
		}
	}
	///////////////////////////////////////////////////////////
	//
	// Méthodes permettant la gestion des connexions des adhérents
	//
	//////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////
	//
	// Pages dynamiques : catégories et articles
	//
	//////////////////////////////////////////////////////////

	/**
	 * Méthode qui liste tous les mots de passe des adhérents
	 * 
	 * @return array
	 * @author Cyril MAGUIRE
	 */
	public function getPasswords() {
		$pw = null;
		//print_r($this->adherentsList);
		foreach ($this->plxRecord_adherents->result as $key => $value) {
			$pw[] = array(
				'rand1' => $value['rand1'],
				'rand2' => $value['rand2'],
				'cle' => $value['cle'],
				'mail' => $value['mail'],
				'salt' => $value['salt'],
				'pass' => $value['password'],
				'nom' => $value['nom'],
				'prenom' => $value['prenom']
			);
		}
		return $pw;
	}

	/**
	 * Méthode qui vérifie si le mot de passe saisi par l'utilisateur est dans la liste des mots de passe
	 * @param $pw string Mot de passe saisi crypté en md5
	 * @param $login string identifiant, correspondant au nom collé au prénom en minuscules sans espace ni caractères accentués ou exotiques
	 * 
	 * @return bool
	 * @author Cyril MAGUIRE
	 */
	public function verifPass($pw,$login){
		//$id = md5($login);
		$listPass = $this->getPasswords();
		//print_r($listPass);

		if(isset($_SESSION['maxtry']) && $_SESSION['maxtry'] >= 2) {
			$_SESSION['timeout'] = time() + (60*15);
		}

		$error = false;
		foreach ($listPass as $k => $v) {

			$logInBase = str_replace(array('-','_'),'',plxUtils::title2url(strtolower($v['nom'].$v['prenom'] )));

			if (sha1($v['salt'].$pw) == $v['pass'] && $login == $logInBase ) {
				$_SESSION['account'] = plxUtils::charAleatoire(5).md5($v['mail']).plxUtils::charAleatoire(3);
				$_SESSION['domainAd'] = $this->session_domain;
				unset($_SESSION['maxtry']);
				return TRUE;
			}
			if ( (sha1($v['salt'].$pw) != $v['pass'] && $login == $logInBase) || (sha1($v['salt'].$pw) == $v['pass'] && $login != $logInBase) ) {
				$error = true;
			}
		}
		if ($error) {
			if(!isset($_SESSION['maxtry'])) {
				$_SESSION['maxtry'] = 1;
			} else{ 
				//$_SESSION['maxtry']++;
			}
		}
		if(!isset($_SESSION['maxtry'])) {
			$_SESSION['maxtry'] = 1;
		} else{ 
			//$_SESSION['maxtry']++;
		}
		return FALSE;
	}

	/**
	 * Méthode qui ajoute le champs 'mot de passe' dans les options des catégories
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function AdminCategory() {
		echo '<?php
				$password = plxUtils::strCheck($plxAdmin->aCats[$id][\'password\']);
				$image = "&nbsp;<img src=\"".PLX_PLUGINS."adhesion/locker.png\" alt=\"\" />";
			?>';
		echo '<p><label for="id_password">'.$this->getlang('L_CATEGORIE_PASSWORD_FIELD_LABEL').'</label>';
		echo "<input type=\"checkbox\" name=\"password\"<?php echo (\$password == 'on' ? ' checked=\"checked\"' : '');?> /><?php if(\$password == 'on') echo \$image ?></p>";
	}

	public function plxAdminEditCategoriesUpdate(){
		echo "<?php \$this->aCats[\$cat_id]['password']=(isset(\$this->aCats[\$cat_id]['password']) && \$this->aCats[\$cat_id]['password'] == 'on' ? 'on' :'') ?>";
	}

	public function plxAdminEditCategoriesXml() {
		echo "<?php \$xml .= '<password><![CDATA['.plxUtils::cdataCheck(\$cat['password']).']]></password>'; ?>";
	}

	public function plxAdminEditCategorie() {
		echo "<?php \$this->aCats[\$content['id']]['password'] = trim(\$content['password']); ?>";
	}

	public function plxMotorGetCategories() {
		echo "<?php \$this->aCats[\$number]['password'] = isset(\$iTags['password'][\$i]) ? plxUtils::getValue(\$values[\$iTags['password'][\$i]]['value']) : ''; ?>";
	}

	/**
	 * Méthode qui permet de démarrer la bufferisation de sortie sur la page admin/categories.php
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdminCategoriesTop() {
		echo "<?php ob_start(); ?>";
    }

	/**
	 * Méthode qui affiche l'image du cadenas si la page est protégée par un mot de passe
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdminCategoriesFoot() {
		echo '<?php
		$content=ob_get_clean();
		if(preg_match_all("#<td>".L_CATEGORY." ([0-9]{3})</td>#", $content, $capture)) {
			$image = "<img src=\"".PLX_PLUGINS."adhesion/locker.png\" alt=\"\" />";
			foreach($capture[1] as $idCat) {
				$str = "<td>".L_CATEGORY." ".$idCat;
				if(isset($plxAdmin->aCats[$idCat]["password"]) AND $plxAdmin->aCats[$idCat]["password"] == \'on\') {
					$content = str_replace($str, $str." ".$image, $content);
				}
			}
		}
		echo $content;
		?>';
    }

	/**
	 * Méthode qui ajoute le champ 'mot de passe' dans l'édition de l'article
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function AdminArticleSidebar(){
			echo '<p><label for="id_password">'.$this->getlang('L_ARTICLE_PASSWORD_FIELD_LABEL').'</label>';
			echo "<input type=\"checkbox\" name=\"password\"<?php echo (isset(\$result['password']) ? (plxUtils::strCheck(trim(\$result['password'])) == 'on' ? 'checked=\"checked\"' : '') : '' );?> /></p>";

        }

	public function plxAdminEditArticleXml(){
		echo "<?php \$xml .= '\t'.'<password><![CDATA['.plxUtils::cdataCheck(trim(\$content['password'])).']]></password>'.'\n'; ?>";
	}

	public function plxMotorParseArticle(){
		echo "<?php \$art['password'] = (isset(\$iTags['password']) && isset(\$values[ \$iTags['password'][0] ]) )?trim(plxUtils::getValue(\$values[ \$iTags['password'][0] ]['value']) ):''; ?>";
	}


	/**
	 * Méthode qui affiche un cadenas si l'article a un mot de passe
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function showIconIfLock() {
		$plxMotor = plxMotor::getInstance();

		$artPassword = $plxMotor->plxRecord_arts->f('password');
		if(!empty($artPassword)){

			$string = <<<END
			 <?php echo '<img src="'.PLX_PLUGINS.'adhesion/locker.png">'; ?>
END;
			echo $string;
		}
	}

	/**
	 * Méthode qui masque les commentaires
	 * 
	 * @author Cyril MAGUIRE
	 */
	private function hideComs() {
		$plxMotor = plxMotor::getInstance();
		
		foreach($plxMotor->plxGlob_coms->aFiles as $key => $comFilename) {
			$fileInfo = $plxMotor->comInfoFromFilename($comFilename);
			$artInfo = $plxMotor->artInfoFromFilename($plxMotor->plxGlob_arts->aFiles[$fileInfo['artId']]);
			$catPassword = $plxMotor->aCats[$artInfo['catId']]['password'];
			if(!empty($catPassword)) {
				//if(!isset($_SESSION['lockArticles']['categorie'][$artInfo['catId']])){
				if(!isset($_SESSION['lockArticles']['categorie'])){
					unset ($plxMotor->plxGlob_coms->aFiles[$key]);
				}
			}
		}
	}

	/**
	 * Méthode qui masque les articles d'une catégorie
	 * 
	 * @author Cyril MAGUIRE
	 */
	private function hideArts($catId='') {
		$plxMotor = plxMotor::getInstance();
		
		foreach($plxMotor->plxGlob_arts->aFiles as $key => $artFilename){
			$fileInfo = $plxMotor->artInfoFromFilename($artFilename);
			$catPassword = $plxMotor->aCats[$fileInfo['catId']]['password'];
			if(!empty($catPassword)) {
				//if(($fileInfo['catId'] != $catId) &&  !isset($_SESSION['lockArticles']['categorie'][$fileInfo['catId']])){
				if(($fileInfo['catId'] != $catId) &&  !isset($_SESSION['lockArticles']['categorie'])){
					unset ($plxMotor->plxGlob_arts->aFiles[$key]);
				}
				//elseif (!isset($_SESSION['lockArticles']['categorie'][$fileInfo['catId']])) {
				elseif (!isset($_SESSION['lockArticles']['categorie'])) {
					unset ($plxMotor->plxGlob_arts->aFiles[$key]);
				}
			}
		}
	}
	
	/**
	 * Méthode qui redefinit le mode de l'article
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function plxMotorPreChauffageEnd() {
		$plxMotor = plxMotor::getInstance();

		if($plxMotor->mode != 'article' && $plxMotor->mode != 'categorie' && $plxMotor->mode != 'archives' && $plxMotor->mode != 'tags') {
			if($this->getParam('hide_l_categories')) {
				$this->hideComs();
				$this->hideArts();
			}
		}
		elseif($plxMotor->mode=='article') {
			if($this->getParam('hide_l_categories')) {
				$this->hideComs();
				$this->hideArts();
			}
			ob_start();
			echo $plxMotor->plxRecord_arts->f('password');
			$p = ob_get_clean();

			$cat_id = explode(',',$plxMotor->plxRecord_arts->f('categorie'));
			foreach ($cat_id as $key => $value) {
				if (!empty($plxMotor->aCats[$value]['password'])) {
					$p = $plxMotor->aCats[$value]['password'];
				}
			}

			if (!empty($p)) {
				$password = plxUtils::getValue($p);
			} else {
				$password = '';
			}

			if(!empty($password)) {
				//if(!isset($_SESSION['lockArticles']['articles'][$plxMotor->cible])) {
				if(!isset($_SESSION['lockArticles']['articles'])) {
					$plxMotor->mode = 'article_password';
				}
			}
			else {
				$cat_id = $plxMotor->plxRecord_arts->f('categorie');
				if(!empty($plxMotor->aCats[$cat_id]['password'])) {
					//if(!isset($_SESSION['lockArticles']['categorie'][$cat_id])) {
					if(!isset($_SESSION['lockArticles']['categorie'])) {
						$plxMotor->mode = 'categorie_password';
					}
				}
			}
		}
		elseif($plxMotor->mode == 'categorie') {
			if($this->getParam('hide_l_categories')) {
				$this->hideComs();
				$this->hideArts($plxMotor->cible);
			}
			if(!empty($plxMotor->aCats[$plxMotor->cible]['password'])) {
				//if(!isset($_SESSION['lockArticles']['categorie'][$plxMotor->cible])) {
				if(!isset($_SESSION['lockArticles']['categorie'])) {
					$plxMotor->mode = 'categories_password';
				}
			}
		}
		elseif($plxMotor->mode == 'archives') {
			$plxMotor->getArticles();
			foreach ($plxMotor->plxRecord_arts->result as $key => $art) {
				$cat_id = explode(',',$art['categorie']);
				foreach ($cat_id as $key => $value) {
					if(!empty($plxMotor->aCats[$value]['password']) && !isset($_SESSION['lockArticles']['categorie'])) {
						$plxMotor->mode = 'home';
					}
				}
			}
		}
		elseif($plxMotor->mode == 'tags') {
			$plxMotor->getArticles();
			foreach ($plxMotor->plxRecord_arts->result as $key => $art) {
				$cat_id = explode(',',$art['categorie']);
				foreach ($cat_id as $key => $value) {
					if(!empty($plxMotor->aCats[$value]['password']) && !isset($_SESSION['lockArticles']['categorie'])) {
						$plxMotor->mode = 'home';
					}
				}
			}
		}
	}

	/**
	 * Méthode qui affiche le formulaire de saisie du mot de passe
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function plxMotorDemarrageEnd() {
		$plxMotor = plxMotor::getInstance();

			//Mot de passe oublié, on renvoie la clé si l'email correspond
			if(isset($_POST['forgetmypass']) ) {
				if($this->retrieveMyPass(plxUtils::strCheck($_POST['email']) )) {
					$_SESSION['retrievePass'] = '<p id="password_success">'.$this->getLang('L_EMAIL_PASS_OK').'</p>';
					header('Location:'.$plxMotor->urlRewrite() );
					exit();
				} else {
					echo '<p id="password_error">'.$this->getLang('L_EMAIL_PASS_KO').'</p>';
				}
			}
			//Déconnexion
			if(isset($_POST['logout'])) {
				//On supprime les index de session
				unset($_SESSION['lockArticles']);
				unset($_SESSION['account']);
				unset($_SESSION['domainAd']);

				$_SESSION['logout'] = $this->getlang('L_DECONNEXION_OK');
				if (isset($_SESSION['referer'])){
					header('Location:'.$_SESSION['referer']);
					unset($_SESSION['referer']);
					exit();
				} else {
					if (isset($_SERVER['QUERY_STRING']) ) {
						header('Location:'.$plxMotor->urlRewrite('?'.$_SERVER['QUERY_STRING']) );
						exit();
					} else {
						header('Location:'.$plxMotor->urlRewrite() );
						exit();
					}
				}
			}

			//Définition du domaine initial
			$this->session_domain = str_replace('plugins/adhesion','',dirname(__FILE__));

			//Si le mode est protégé, on affiche le message de connexion, sinon on affiche la page normalement
			switch ($plxMotor->mode) {
						case 'article_password':
						case 'categorie_password':
						case 'categories_password':
						case 'annuaire':
						$showForm = TRUE;
						break;
						case 'home':
						$protectedCats = array();
						foreach($plxMotor->plxGlob_arts->aFiles as $key => $artFilename){
							$fileInfo = $plxMotor->artInfoFromFilename($artFilename);
							$cats = explode(',', $fileInfo['catId']);
							$catPassword = '';
							foreach ($cats as $k => $value) {
								if ($plxMotor->aCats[$value]['password'] == 'on') {
									$catPassword = $plxMotor->aCats[$value]['password'];
									if($catPassword == 'on') {
										$protectedCats[] = $fileInfo['catId'];
									}
									$catPassword = '';
								}
							}
						}
						if (!isset($_SESSION['lockArticles']['categorie'])) {
							foreach ($plxMotor->plxRecord_arts->result as $k => $data) {
								if (in_array($data['categorie'],$protectedCats) ) {
									$plxMotor->plxRecord_arts->result[$k]['chapo'] = '<p class="locked">'.$this->getLang('L_NEED_AUTH').'</p>';
									$plxMotor->plxRecord_arts->result[$k]['allow_com'] = 0;
									$plxMotor->plxRecord_arts->result[$k]['content'] = '';
								}
								if ($data['password'] == 'on' ) {
									$plxMotor->plxRecord_arts->result[$k]['chapo'] = '<p class="locked">'.$this->getLang('L_NEED_AUTH').'</p>';
									$plxMotor->plxRecord_arts->result[$k]['allow_com'] = 0;
									$plxMotor->plxRecord_arts->result[$k]['content'] = '';
								}
							}
						}
						$showForm = FALSE;
						break;
						default:
						$showForm = FALSE;
						break;
			}

			//echo $plxMotor->mode;exit();
			//Vérification de la connexion
			if(isset($_POST['lockArticles']) && isset($_POST['login']) && isset($_POST['password'])) {
				
				$pw = md5($_POST['password']);
				
				if($this->verifPass($pw,plxUtils::strCheck($_POST['login']))) {
					
					$_SESSION['lockArticles']['articles'] = $_SESSION['lockArticles']['categorie'] = 'on';
					$_SESSION['lockArticles']['success'] = $this->getlang('L_PLUGIN_GOOD_PASSWORD');
					$showForm = FALSE;

					switch ($plxMotor->mode) {
						case 'article_password':
							$url = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
							break;
						case 'categorie_password':
							$url = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
							break;
						case 'categories_password':
							$url = $plxMotor->urlRewrite('?categorie'.intval($plxMotor->cible).'/'.$plxMotor->aCats[$plxMotor->cible]['url']);
							break;
						case 'annuaire':
							$url = $plxMotor->urlRewrite('?annuaire');
							break;
						case 'categorie':
						case 'home':
							if ($plxMotor->mode == 'categorie') {
								$url = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
							} else {
								$url = $plxMotor->urlRewrite();
							}
							foreach ($plxMotor->plxRecord_arts->result as $key => $value) {
								if(isset($value['password']) && $value['password'] == 'on' && !isset($_SESSION['lockArticles']['categorie']) && !isset($_SESSION['lockArticles']['articles']) ) {
									//On modifie le contenu de l'article
									$a = array(); 
									$a['content'] =  '<p class="locked">'.$this->getLang('L_NEED_AUTH').'</p>';
									$a['allow_com'] = 0;
									$a['chapo'] = '';

									$plxMotor->plxRecord_arts->result[$key] = array_merge($plxMotor->plxRecord_arts->result[$key], $a);
								}
							}
							break;
					}//Fin du switch	

					header('Location: '.$url);
					exit;
				}
				else {
					if (isset($_SESSION['maxtry']) && $_SESSION['maxtry'] >= 3) {
						$_SESSION['erase'] = '<p id="password_error">'.$this->getlang('L_PLUGIN_MAXTRY').'&nbsp;'.date('H\hi',$_SESSION['timeout']).'</p>';
					} else {
						$_SESSION['lockArticles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
					}
				}
			}
			
			if (isset($_SESSION['domainAd']) && $_SESSION['domainAd'] != $this->session_domain) {
				$showForm = TRUE;
				unset($_SESSION['domainAd']);
			}

			if($showForm) {
				if($plxMotor->mode == 'categories_password') {
					$plxMotor->template = 'article.php';
					$a = array(
					array(
						'title' => $plxMotor->aCats[$plxMotor->cible]['name'],
						'allow_com' => 0,
						'template' => 'article.php',
						'chapo' => '',
						//'content' => file_get_contents(PLX_PLUGINS.'adhesion/form.article_password.php'),
						'content' => '<p class="locked">'.$this->getLang('L_NEED_AUTH').'</p>',
						'tags' => '',
						'meta_description' => '',
						'meta_keywords' => '',
						'title_htmltag' => '',
						'filename' => '',
						'numero' => '0000',
						'author' => '000',
						'categorie' => $plxMotor->cible,
						'url' => '',
						'date' => '<br>',//date("c"),
						'nb_com' => 0,
						)
					);

					$plxMotor->plxRecord_arts = new plxRecord($a);
				}
				else {
					$a = array(); 
					$a['content'] =  '<p class="locked">'.$this->getLang('L_NEED_AUTH').'</p>';
					if(isset($_SESSION['lockArticles']['error'])) {
						$a['content'] .= '<p class="static_password_error">'.$_SESSION['lockArticles']['error'].'</p>';
						unset($_SESSION['lockArticles']['error']);
					}
					$a['allow_com'] = 0;
					$a['chapo'] = '';
					if($plxMotor->plxRecord_arts) {
						$plxMotor->plxRecord_arts->result[0] = array_merge($plxMotor->plxRecord_arts->result[0], $a);
						if ($plxMotor->mode != 'annuaire') {
							$plxMotor->template = $plxMotor->plxRecord_arts->f('template');
						}
					}
				}
			}
	}

	/**
	 * Méthode qui affiche le bouton de déconnexion et le formulaire de connexion
	 * Voir $this->ThemeEndBody pour l'affichage des messages
	 * 
	 * @return string
	 * @author Cyril MAGUIRE
	 */
	public function loginLogout() { //unset($_SESSION['maxtry']);unset($_SESSION['timeout']);
		$plxMotor = plxMotor::getInstance();
		if ((isset($_SESSION['domain']) AND $_SESSION['domain'] == $this->session_domain) || (isset($_SESSION['lockArticles']['categorie']) && $_SESSION['lockArticles']['categorie'] == 'on') || (isset($_SESSION['lockArticles']['articles']) && $_SESSION['lockArticles']['articles'] == 'on') ) {
			if (isset($_SESSION['timeout']) ) {
				unset($_SESSION['timeout']);
			}
		}
		if (isset($_SESSION['timeout']) ) {
			if (time() < $_SESSION['timeout'] ) {
			echo '
<div id="espace-membres">
<h3 class="widget-title icon-parents"> '.$this->getLang('L_FORM_MEMBERS').'</h3>
<p style="text-align:center;">'.$this->getlang('L_PLUGIN_MAXTRY').'&nbsp;'.date('H\hi',$_SESSION['timeout']).'</p>
<p id="forgetmypass"><a href="<?php echo $plxShow->urlRewrite(\'?forgetmypass.html\');?>">'.$this->getLang('L_FORGET_PASS').'</a></p>
</div>
'; 
			} else {
				unset($_SESSION['maxtry']);
				unset($_SESSION['timeout']);
				header('Location:'.$plxMotor->urlRewrite());
				exit();
			}
		} else {
			if ( (isset($_SESSION['domain']) AND $_SESSION['domain'] == $this->session_domain) || (isset($_SESSION['lockArticles']['categorie']) && $_SESSION['lockArticles']['categorie'] == 'on') || (isset($_SESSION['lockArticles']['articles']) && $_SESSION['lockArticles']['articles'] == 'on') ) :
			echo '
<div id="espace-membres">
<h3 class="widget-title icon-parents"> '.$this->getLang('L_FORM_MEMBERS').'</h3>
<form action="" method="post" id="logout">
	<fieldset>
		<p>
			<input type="hidden" name="logout">
			<input type="submit" value="'.$this->getLang('L_FORM_LOGOUT').'" id="sub"/>
		</p>
	</fieldset>
</form>
<p id="myaccount"><a href="<?php echo $plxShow->urlRewrite(\'?myaccount.html&a='.$_SESSION['account'].'\');?>">'.$this->getLang('L_MY_ACCOUNT').'</a></p>
</div>
';
			else :
			echo '
<div id="espace-membres">
<h3 class="widget-title icon-parents"> '.$this->getLang('L_FORM_MEMBERS').'</h3>
<form action="" method="post">
	<fieldset>
		<p>
			<label>'.$this->getLang('L_ID').' &nbsp;:</label><br/>
			<input type="text" name="login" maxlength="50" value="" id="id">
			<br/>
			<label>'.$this->getLang('L_FORM_PASSWORD').' &nbsp;:</label><br/>
			<input type="password" name="password" maxlength="50" value="" id="pass">
			<input type="hidden" name="lockArticles">
			<input type="submit" value="'.$this->getLang('L_FORM_OK').'" id="submit"/>
		</p>
	</fieldset>
</form>
<p id="forgetmypass"><a href="<?php echo $plxShow->urlRewrite(\'?forgetmypass.html\');?>">'.$this->getLang('L_FORGET_PASS').'</a></p>
</div>
';
		endif;
		}	
	}

	/**
	 * Méthode qui enclanche la bufferisation de sortie pour afficher les cadenas (voir AdminIndexFoot())
	 * 
	 * @author Cyril MAGUIRE
	 */
	public function AdminIndexTop (){
		echo "<?php ob_start(); ?>";
	}

	/**
	 * Méthode qui affiche le cadenas au niveau de la page articles de l'administration si un article a un mot de passe
	 * 
	 * @author Cyril MAGUIRE
	 */
	public function AdminIndexFoot () {
		echo '<?php
				$content=ob_get_clean();
				if(preg_match_all("#value=\"([0-9]{4})\"#", $content, $capture)) {
					$image = "<img src=\"".PLX_PLUGINS."adhesion/locker.png\" alt=\"\">";
					$imagecat = "<img src=\"".PLX_PLUGINS."adhesion/locker-cat.png\" alt=\"\">";
					$artTab = array();
					$artTabCat = array();
					while($plxAdmin->plxRecord_arts->loop()) {
						$art_id = $plxAdmin->plxRecord_arts->f(\'numero\');
						$cat_id = explode(",",$plxAdmin->plxRecord_arts->f(\'categorie\'));

						$artTab[$art_id] = $plxAdmin->plxRecord_arts->f(\'password\');
						foreach ($cat_id as $key => $value) {
							if (!empty($plxAdmin->aCats[$value][\'password\'])) {
								$artTabCat[$art_id] = $plxAdmin->aCats[$value][\'password\'];
							}
						}
					}
					foreach($capture[1] as $ArtId) {
						
						$str = "<td><a href=\"article.php?a=".$ArtId."\" title=\"";
						if(!empty($artTab[$ArtId] ) && empty($artTabCat[$ArtId] ) ){
							$content = str_replace($str.L_ARTICLE_EDIT_TITLE."\">", $str, $content);
							$content = str_replace($str, $str."'.$this->getLang('L_ART_PROTECTED').' : ".L_ARTICLE_EDIT_TITLE."\">".$image."&nbsp;", $content);
						}
						if(!empty($artTabCat[$ArtId] ) ){
							$content = str_replace($str.L_ARTICLE_EDIT_TITLE."\">", $str, $content);
							$content = str_replace($str, $str."'.$this->getLang('L_CAT_PROTECTED').' : ".L_ARTICLE_EDIT_TITLE."\">".$imagecat."&nbsp;", $content);
						}
					}
				}
				echo $content;
			?>';
	}

	/**
	 * Méthode permettant de masquer s'il y a un mot de passe
	 * 
	 * @author Cyril MAGUIRE
	 */
	public function plxFeedPreChauffageEnd() {

		$plxFeed = plxFeed::getInstance();
		
		if ($plxFeed->mode == 'article') {
			foreach($plxFeed->plxGlob_arts->aFiles as $key => $artFilename){
				$fileInfo = $plxFeed->artInfoFromFilename($artFilename);
				$catPassword = $plxFeed->aCats[$fileInfo['catId']]['password'];
				if(!empty($catPassword)) {
					unset ($plxFeed->plxGlob_arts->aFiles[$key]);
				}
			}
			$plxFeed->mode = 'article_password';
		}
		elseif ($plxFeed->mode == 'commentaire') {
			foreach($plxFeed->plxGlob_coms->aFiles as $key => $comFilename) {
				$fileInfo = $plxFeed->comInfoFromFilename($comFilename);
				$artInfo = $plxFeed->artInfoFromFilename($plxFeed->plxGlob_arts->aFiles[$fileInfo['artId']]);
				$catPassword = $plxFeed->aCats[$artInfo['catId']]['password'];
				if(!empty($catPassword)) {
					unset ($plxFeed->plxGlob_coms->aFiles[$key]);					
				}
			}
		}

	}

	/**
	 * Méthode qui permet d'afficher un article dans le flux RSS s'il n'est pas protégé par un mot de passe
	 * 
	 * @author Cyril MAGUIRE
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

	///////////////////////////////////////////////////////////
	//
	// Pages statiques
	//
	//////////////////////////////////////////////////////////

	/**
	 * Méthode qui ajoute le champ de saisie du mot de passe dans la page d'édition de la page statique
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function AdminStatic() {
		echo '
			<?php
				$password = plxUtils::getValue($plxAdmin->aStats[$id]["password"]);
				$image = "&nbsp;<img src=\"".PLX_PLUGINS."adhesion/locker.png\" alt=\"\" />";
			?>
			<fieldset>
				<p><label for="id_password">'.$this->getLang('L_FORM_ADMIN_PASSWORD').'&nbsp;:</label>
				<input type="checkbox" name="password"<?php echo ($password == \'on\' ? \' checked="checked"\' : \'\');?> /></p>
				<?php if($password == \'on\') echo $image ?>
			</fieldset>
		';
	}
	/**
	 * Méthode qui ajoute la notification de mot de passe dans la chaine xml à sauvegarder dans statiques.xml
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function plxAdminEditStatiquesXml() {
		echo "<?php \$xml .= \"<password><![CDATA[\".plxUtils::cdataCheck(\$static['password']).\"]]></password>\"; ?>";
    }

	/**
	 * Méthode qui récupère la notification de mot de passe saisit lors de l'édition de la page statique
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function plxAdminEditStatique() {
		echo "<?php \$this->aStats[\$content['id']]['password'] = (!empty(\$content['password']) ? 'on' : 'FALSE'); ?>";
    }

	/**
	 * Méthode qui récupère la notification de mot de passe stockée dans le fichier xml statiques.xml
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function plxMotorGetStatiques() {
		echo "<?php \$password = plxUtils::getValue(\$iTags['password'][\$i]); ?>";
		echo "<?php \$this->aStats[\$number]['password']=plxUtils::getValue(\$values[\$password]['value']); ?>";
	}

	/**
	 * Méthode qui permet de démarrer la bufferisation de sortie sur la page admin/statiques.php
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function AdminStaticsTop() {
		echo "<?php ob_start(); ?>";
    }

	/**
	 * Méthode qui affiche l'image du cadenas si la page est protégée par un mot de passe
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function AdminStaticsFoot() {
		echo '<?php
		$content=ob_get_clean();
		if(preg_match_all("#<td>".L_PAGE." ([0-9]{3})</td>#", $content, $capture)) {
			$image = "<img src=\"".PLX_PLUGINS."adhesion/locker.png\" alt=\"\" />";
			foreach($capture[1] as $idStat) {
				$str = "<td>".L_PAGE." ".$idStat;
				if(isset($plxAdmin->aStats[$idStat]["password"]) AND $plxAdmin->aStats[$idStat]["password"] == \'on\') {
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
	 * @author	Cyril MAGUIRE
	 **/
    public function plxShowConstructStat() {

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
	 * Méthode qui affiche le formulaire d'identification si un mot de passe est présent pour la page statique
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
    public function plxMotorPreChauffageEndStat() {
		echo "<?php
		if(\$this->mode=='static') {
			\$password = plxUtils::getValue(\$this->aStats[\$this->cible]['password']);
			if(\$password=='on') {
				if(!isset(\$_SESSION['lockArticles']['categorie'])) {
					\$this->idStat = \$this->cible;
					\$this->cible = '../../plugins/adhesion/form';
					\$this->mode = 'static_password';
					\$this->template = 'static.php';
				}
			}
		}
		?>";
	}


	/**
	 * Méthode qui valide la connexion d'un adhérent
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function plxMotorDemarrageEndStat(){
			$plxMotor = plxMotor::getInstance();
			if($plxMotor->mode == 'static' || $plxMotor->mode == 'static_password') {
				if(isset($_POST['lockArticles']) && isset($_POST['password'])) {
					
					$pw = md5($_POST['password']);
					
					if ($this->verifPass($pw,$_POST['password'])) {
						$_SESSION['lockArticles']['categorie'] = $_SESSION['lockArticles']['articles'] = 'on';
						$url = $plxMotor->urlRewrite('?'.$plxMotor->aCats[$plxMotor->cible]['url']);
						$_SESSION['lockArticles']['success'] = $this->getlang('L_PLUGIN_GOOD_PASSWORD');
						header('Location: '.$url);
						exit;
					}
					else {
						$_SESSION['lockArticles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
					}
				}
			}
	}
	/**
	 * Méthode qui renseigne le titre de la page dans la balise html <title>
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowPageTitleStat() {
		echo '<?php
			if($this->plxMotor->mode == "static_password") {
				echo plxUtils::strCheck($this->plxMotor->aConf["title"])." - ".plxUtils::strCheck($this->plxMotor->aStats[$this->plxMotor->idStat]["name"]);
				return TRUE;
			}
		?>';
	}

	/**
	 * Méthode qui ajoute le fichier css dans le fichier header.php du thème
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function ThemeEndHeadStat() {
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'adhesion/style-lock.css" media="screen" />'."\n";
	}

	/**
	 * Méthode qui affiche des messages succes/erreur
	 *
	 * @return	stdio
	 * @author	Cyril MAGUIRE
	 **/
	public function ThemeEndBody() {
		if (isset($_SESSION['lockArticles']['success']) && $_SESSION['lockArticles']['success'] == $this->getlang('L_PLUGIN_GOOD_PASSWORD') ) {
			if (!isset($_SESSION['lockArticles']['log'])) {
				echo '<p id="password_success">'.$this->getLang('L_CONNEXION_OK').'</p>';
				echo '
			<script type="text/javascript">
				(function($){
					$("#password_success").slideUp(5000);
				})(jQuery);
			</script>';
				unset($_SESSION['lockArticles']['success']);
			}
		}
		if (isset($_SESSION['logout']) && $_SESSION['logout'] == $this->getlang('L_DECONNEXION_OK')) {
				echo '<p id="password_success">'.$this->getLang('L_DECONNEXION_OK').'</p>';
				echo '
			<script type="text/javascript">
				(function($){
					$("#password_success").slideUp(5000);
				})(jQuery);
			</script>';
				unset($_SESSION['logout']);
		}
		if(isset($_SESSION['lockArticles']['error']) && $_SESSION['lockArticles']['error'] == $this->getlang('L_PLUGIN_BAD_PASSWORD')) {
			echo '<p id="password_error">'.$_SESSION['lockArticles']['error'].'</p>';
				echo '
			<script type="text/javascript">
				(function($){
					$("#password_error").slideUp(5000);
				})(jQuery);
			</script>';
			unset($_SESSION['lockArticles']['error']);
		}
		if (isset($_SESSION['retrievePass'])) {
			echo $_SESSION['retrievePass'];
			echo '
			<script type="text/javascript">
				(function($){
					$("#password_success").slideUp(5000);
					$("#password_error").slideUp(5000);
				})(jQuery);
			</script>';
			unset($_SESSION['retrievePass']);
		}
		if (isset($_SESSION['erase'])) {
			echo $_SESSION['erase'];
			echo '
			<script type="text/javascript">
				(function($){
					$("#password_success").slideUp(5000);
					$("#password_error").slideUp(5000);
				})(jQuery);
			</script>';
			unset($_SESSION['erase']);
		}
	}
}
?>