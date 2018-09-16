<?php
/*
    *   archive Class
    *
    *   Vous êtes libre d'utiliser et de distribuer ce script comme vous l'entendez, en gardant à l'esprit 
    *   que ce script est, à l'origine, fait par des développeurs bénévoles : en conséquence, veillez à 
    *   laisser le Copyright, par respect de ceux qui ont consacré du temps à la création du script. 
    *
    *   @author         François Poteau 
	* 	@copyright		2010-2011 François Poteau
    *   @link           https://github.com/frapfrap
    *   @license        http://www.gnu.org/licenses/gpl.html (COPYING) GNU Public License
    *   @begin          24/02/2011, François Poteau
    *   @last           03/09/2016, François Poteau
*/

require_once(dirname(__FILE__).'/../../../core/admin/prepend.php');

class archive {
    
	// Variable
	// Ensemble des dossiers sauvegardés
	
	public $saved_dirs = array(
		'data/articles/',
		'data/commentaires/',
		'data/statiques/'
	);
	
	
	// *********************************************************************
	// __construct
	//
	// Paramètres de sauvegarde
	// string $save_dir :: Localisation du dossier de sauvegarde
	// int $days :: sauvegarde tous les X jours
	// string $email :: Adresse email du destinaitaire de la sauvegarde
	//
	// return bool
	// *********************************************************************
	
	
    public function __construct($save_dir='../../sauvegarde/',$days='14',$saved_dirs)
    {
		if($saved_dirs) {
			$this->saved_dirs = explode(",", $saved_dirs);
		}
        $this->save_dir = (substr($save_dir, -1) != '/') ? $save_dir.'/' : $save_dir;
		
		if (is_numeric($days)) {
			$this->days = $days;
		}
		
		else { return false; }
		return true;
    }
	
	
	// *********************************************************************
	// Méthode zip()
	//
	// Créer l'archive zip dans le dossier $save_dir à partir de $saved_dirs
	//
	// @return void
	// @author	François POTEAU
	// *********************************************************************
	
	public function zip() {
		$zip_name = 'archive.'.date('ymd').'.zip';
		$zip = new Zip($zip_name,$this->save_dir);
		foreach($this->saved_dirs as $dir) {
			$zip->add('../../'.$dir);
			// On supprime les références aux dossiers précédents ..
			$zip->renameName('../../'.$dir,$dir);
		}
		$zip->close();
		$this->lastbackup = date('ymd');
	}
	
	// *********************************************************************
	// Méthode check()
	//
	// Vérifie s'il est nécessaire de réaliser une sauvegarde en accord avec la variable $days
	// Génère la variable $lastbackup qui indique la dernière date de sauvegarde
	//
	// @return bool
	// @author	François POTEAU
	// *********************************************************************
	
	public function check() {
		// récuperation de la date de la dernière archive
		$date = array();
		$dir = @opendir($this->save_dir);
		if($dir) {
			// parcours des archives
			while ($file = readdir($dir)) {
				if ($file != "." && $file != ".." && !is_dir($file) && $file != ".htaccess") {
					$index = explode('.',$file);
					$date[] = $index[1];
				}
			}
			closedir($dir);
			// s'il n'existe pas de fichier, retourner vrai
			if (!count($date)) {
				$this->lastbackup = 1;
				return true;
			}
			else {
				$lastbackup = max($date);
				$this->lastbackup = $lastbackup;
				$day = substr($lastbackup, -2, 2); // yymmdd -> dd
				$month = substr($lastbackup, -4, 2); // yymmdd -> mm
				$year = substr($lastbackup, -6, 2); // yymmdd -> yy
				$days = (strtotime(date("Y-m-d")) - strtotime($year.'-'.$month.'-'.$day)) / (60 * 60 * 24);
				// Si l'écart entre aujourd'hui et la dernière sauvegarde excède le nombre de jours paramétré, retourner vrai
				if($days >= $this->days) {
					return true;
				}
				// sinon retourner faux
				else { return false; }
			}
		}
		else {
			// Création du dossier
			if(mkdir($this->save_dir)) {
				// Création du htaccess
				$this->htaccess($this->save_dir);
				// Vérification du success et recherche de sauvegarde
				$this->check();
			}
			else { return false; }
		}
	}
	
	// *********************************************************************
	// Méthode sendmail()
	//
	// Envoi la dernière sauvegarde par mail - La fonction check() doit avoir été préalablement appellée.
	//
	// @param string $email :: e-mail du destinataire
	// @param string $email_sender :: e-mail de l'expéditeur
	// @param string $sender :: Nom de l'expéditeur
	// @param bool :: true = archive en pièce jointe 

	// @author François POTEAU
	// @return bool
	// *********************************************************************
	
	
	public function sendmail($email,$email_sender='nobody@save.com',$sender='Sauvegarde',$titre='Sauvegarde site internet',$message='Sauvegarde du site internet.',$attached=false) {
		if($this->lastbackup) {
			$mail = new PHPMailerLite(); // defaults to using php "Sendmail" (or Qmail, depending on availability)
			$mail->IsMail(); // telling the class to use native PHP mail()
			$mail->SetFrom($email_sender, $sender);
		  	$mail->CharSet = 'utf-8';
		 	$mail->AddAddress($email, $email);
		  	$mail->Subject = $titre.' '. $this->lastbackup;
			// si l'on souhaite joindre l'archive en pièce jointe du mail
			if($attached) {

				try {
					  $mail->Body = $message; // Body text/plain
					  $mail->AddAttachment($this->save_dir.'archive.'.$this->lastbackup.'.zip');      // attachment
					  return $mail->Send();
				} 
				catch (phpmailerException $e) { return false; } 
				catch (Exception $e) { return false; }
			}
			// sinon envoi d'un lien pour télécharger l'archive
			else {
				$url = 'http://'. $_SERVER[HTTP_HOST] . $_SERVER[SCRIPT_NAME] .'?p=plxContentBackup&f='.plxEncrypt::encryptId('archive.'.$this->lastbackup.'.zip');
				try {
					  $mail->Body = $message . ' '. $url; // Body text/plain
					  return $mail->Send();
				} 
				catch (phpmailerException $e) { return false; } 
				catch (Exception $e) { return false; }
			}
		}
	}
	
	
	// *********************************************************************
	// Méthode displaylist()
	// affiche la liste des fichiers avec sa taille (sortie HTML) et un lien pour leur suppression éventuelle
	//
	// @author	François POTEAU
	// @author	Cyril MAGUIRE
	// @update 03/09/2016 : sorting name, fixed bug display delete icon
	// @return void
	public function displaylist() {
		global $plxShow;
		$dirname = $this->save_dir;
		$dir = @opendir($dirname);
		$i = 0;
		if ($dir) {

			$files = array();
			while ($files[] = readdir($dir));
			sort($files);
			closedir($dir);

			echo '<table>';
			foreach ($files as $file) {
				if($file != '.' && $file != '..' && !is_dir($dirname.$file) && substr($file, 0, 1) != '.')
				{
					
					echo '<tr><td class="name"><a href="parametres_plugin.php?p=plxContentBackup&f='.plxEncrypt::encryptId($file).'">'.$file.'</a></td><td>'.$this->format_bytes(filesize($dirname.$file)).'</td><td><a href="parametres_plugin.php?p=plxContentBackup&d='.plxEncrypt::encryptId($file).'" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer l’'.$file.' ?\')"><img src="'.PLX_PLUGINS.'plxContentBackup/img/delete.gif" alt="Supprimer l\'archive" title="Supprimer l\'archive" /></a></td></tr>';
					$i++;
				}
			}
			if ($i==0) {
					echo '<tr><td><em>Aucune archive disponible.</em></td></tr>';
			}
			echo '</table>';
		}
	}
	
	
	// *********************************************************************
	// Méthode format_bytes()
	// Convertit l'unité bytes 
	//
	// @author	François POTEAU
	// @return void
	private function format_bytes($size) {
		$units = array(' o', ' Ko', ' Mo', ' Go', ' To');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}
	
	
	// *********************************************************************
	// Méthode htaccess
	// création d'un fichier htaccess pour la sécurité
	//
	// @author	François POTEAU
	// @return void
	private function htaccess($path) {
		$htaccess = "<Files *>\r\n\tOrder allow,deny\r\n\tDeny from all\r\n</Files>";
		file_put_contents($path.'.htaccess',$htaccess);
	}
}
?>