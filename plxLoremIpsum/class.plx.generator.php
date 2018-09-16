<?php
/**
 * Générateur aléatoire d'articles et de commentaires
 *
 * @author	Stephane F
 */

class plxLoremIpsumGenerator {


	private $loremipsum; # object LoremIpsum

	private $aParams; # paramètres saisis sur l'écran de configuration
	private $aTags = array('Lorem ipsum', 'PluXml', 'Programming', 'Tips', 'Themes', 'Plugins'); # liste de tags pour les articles

	public function __construct() {
		$this->loremipsum = new LoremIpsum();
	}

    public function __get($key) {
		if(isset($this->aParams[$name])) {
			return $this->aParams[$name];
		}
	}

	public function __set($key, $value) {
        $this->aParams[$key]=$value;
    }

	private function newArticle($id, $cat) {

		# alimentation des informations d'un article
		$art = array();
		$art['artId'] = str_pad($id, 4, "0", STR_PAD_LEFT);
		$art['author'] = $_SESSION['user'];
		$art['title'] = $this->loremipsum->getContent(rand(4,8), 'plain'); # titre entre 4 et 8 mots
		$art['chapo'] = '';
		$art['content'] = $this->loremipsum->getContent(rand(100,500), 'plain'); # contenu entre 100 et 500
		$date = strtotime("-". rand(1,360)." days",strtotime(date("Y-m-d")));
		$art['day'] = str_pad(date('d', $date), 2, "0", STR_PAD_LEFT);
		$art['month'] = str_pad(date('m', $date), 2, "0", STR_PAD_LEFT);
		$art['year'] = str_pad(date('Y', $date), 2, "0", STR_PAD_LEFT);
		$art['time'] = str_pad(rand(1,24), 2, "0", STR_PAD_LEFT).':'.str_pad(rand(1,60), 2, "0", STR_PAD_LEFT);
		$art['catId'] = array($cat);
		$art['allow_com'] = 1;
		$art['url'] = '';
		$art['template'] = 'article.php';
		$art['tags'] = implode(', ', array_rand(array_flip($this->aTags), rand(1, sizeof($this->aTags))));
		$art['meta_description']='';
		$art['meta_keywords']='';
		return $art;
	}

	private function newComment() {
		return $this->loremipsum->getContent(rand(20,100), 'plain'); # commentaire entre 20 et 100 mots
	}

	public function generate() {
		global $plxAdmin;

		# compteur d'articles créés
		$count=0;

		# on détermine l'id du prochain article à créer
		$id = intval($plxAdmin->nextIdArticle());

		for($i=$id;$i<($id+$this->aParams['nbart']);$i++) {

			# initialisation du générateur aléatore de nombre
			srand ((double) microtime( )*1000000);

			# nouvel id de l'article à créer
			$idArt = str_pad($i, 4, "0", STR_PAD_LEFT);

			# id de la catégorie de l'article
			if(sizeof($plxAdmin->aCats)>0) {
				$catKeys = array_keys($plxAdmin->aCats);
				$idCat = $catKeys[rand(0,sizeof($plxAdmin->aCats)-1)];
			} else {
				$idCat = '001';
			}
			if($plxAdmin->editArticle($this->newArticle($idArt, $idCat),$idArt)) {

				# génération aléatoire de commentaires pour l'article
				$nbcomment = rand(1,$this->aParams['nbcomsart']);
				for($j=1;$j<=$nbcomment;$j++) {
					$plxAdmin->newCommentaire($idArt,$this->newComment());
				}

				$count++; # nombre d'articles créés
			}
		}
	}
}
?>