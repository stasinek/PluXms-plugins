<?php
/**
 * Plugin plxMyZipDownload
 *
 * les fichiers sont à stocker dans le dossier images de PluXml (par défaut: data/images)
 * les liens sont à formater de la façon suivante:
 *		<a href="?download=file.jpg">telecharger</a>
 *		<a href="?download=dossier/file.jpg">telecharger</a>
 **/
class plxMyZipDownload extends plxPlugin {

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accèder à la page config.php et admin.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->setAdminProfil(PROFIL_ADMIN);

		# ajout du hook
		$this->addHook('plxMotorPreChauffageBegin', 'plxMotorPreChauffageBegin');

	}

	/**
	 * Méthode qui zip un fichier pour l'envoyer à un utilisateur s'il y a une demande de téléchargement
	 *
	 * @return	stdio
	 * @author	Stéphane F.
	 **/
	public function plxMotorPreChauffageBegin() {

		$string = "
		\$download = plxUtils::getValue(\$_GET['download']);
		if(\$download!='') {
			\$file = PLX_ROOT.\$this->aConf['medias'].plxUtils::nullbyteRemove(\$_GET['download']);
			if(@file_exists(\$file) AND preg_match('#^'.str_replace('\\\', '/', realpath(PLX_ROOT.\$this->aConf['medias'])).'#', str_replace('\\\', '/', realpath(\$file)))) {
				include(PLX_PLUGINS.'plxMyZipDownload/zipfile.inc.php');
				\$zipfile = new zipfile();
				\$handle = fopen(\$file, 'rb');
				\$data = fread(\$handle, filesize(\$file));
				fclose(\$handle);
				if(\$data) {
					\$zipfile->add_file(\$data, basename(\$file));
					header('Content-type: application/octet-stream');
					\$zipname = str_replace(strtolower(strrchr(\$file,'.')), '.zip', basename(\$file));
					header('Content-disposition: attachment; filename='.\$zipname);
					echo \$zipfile->file();
					exit;
				}
				return true;
			}
		}
		";
		echo "<?php ".$string." ?>";
	}

}
?>