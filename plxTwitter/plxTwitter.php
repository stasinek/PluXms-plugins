<?php

if (!defined('PLX_ROOT')) exit;

/*
 * Pour PluXml version 5.3.1 et précédentes versions, modifier le script core/lib/plx.class.admin.php comme suit, vers la ligne 801 :
	$this->editTags();
	if($content['artId'] == '0000' OR $content['artId'] == '') {
		eval($this->plxPlugins->callHook('AdminSavedArticle', $id));
		return plxMsg::Info(L_ARTICLE_SAVE_SUCCESSFUL);
	}
	else
		return plxMsg::Info(L_ARTICLE_MODIFY_SUCCESSFUL);
 * */

class plxTwitter extends plxPlugin {

	public $default_values = array();

	public function __construct($default_lang) {
		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accéder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
		$this->addHook('AdminSavedArticle', 'AdminSavedArticle');
		$this->addHook('AdminEditArticle', 'AdminSavedArticle');
	}

	public function init_config() {
	}

	public function AdminTopEndHead() {
		$script_name = basename(strtolower($_SERVER['SCRIPT_NAME']),'.php');
		switch ($script_name) {
			case 'parametres_pluginhelp' :
				break;
			case 'parametres_plugin' : ?>
			<link rel="stylesheet" href="<?php echo PLX_PLUGINS.__CLASS__.'/'.__CLASS__;?>.css"/>
<?php
				break;
		}
	}

	private function connect() {
		if (!class_exists('TwitterOAuth'))
			require_once(__DIR__.'/twitteroauth/twitteroauth/twitteroauth.php');
		$consumer_key = $this->getParam('consumer_key');
		$consumer_secret = $this->getParam('consumer_secret');
		$access_token_key = $this->getParam('access_token_key');
		$access_token_secret = $this->getParam('access_token_secret');
		return new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
	}

	public function tweet($msg="Hello the World") {
		$connection = $this->connect();
		return $connection->post('statuses/update', array('status' => date(DATE_RFC822).' : '.$msg));
	}

	public function get_credentials() {
		$connection = $this->connect();
		return $connection->get('account/verify_credentials');
	}

	public function get_timeline() {
		$connection = $this->connect();
		return $connection->get('statuses/home_timeline');
	}

	public function get_config() {
		$connection = $this->connect();
		return $connection->get('help/configuration');
	}

	// envoi d'un tweet pour un nouvel article
	public function plxAdminSavedArticle($idArticle) {
		// $_POST: author, day, month, year, time, tags, url, title, chapo
		global $plxAdmin;
		$config_twitter = $this->get_config();
		// longueur maxi de l'url abrégée
		$short_url_length = intval($config_twitter->short_url_length);
		$title = $_POST['title'];
		$url = $plxAdmin->urlRewrite('index.php?article'.intval($idArticle).'/'.$_POST['url']);
		$items = array('1'=>$title, '2'=>$url);
		$max_length = 140 - strlen($title) - $short_url_length - 1;
		$tags = '';
		$str1 = $this->getParam('tags');
		if (strlen($str1) > 0) {
			$listTags = explode(' ', $str1);
			foreach ($listTags as $t) {
				if (strlen($tags) + strlen($t) < $max_length - 2) {
					if (strlen($tags) > 0) $tags .= ' ';
					$tags .= '#'.$t;
				}
			}
			$items['3'] = $tags;
			$maxlength -= strlen($tags) + 1;
		}
		$author = $plxAdmin->aUsers[$_POST['author']]['name'];
		if ((intval($this->getParam('author')) > 0) and (strlen($author) < $maxlength - 1))
			$items['4'] = $author;
		ksort($items);
		$msg = implode(' ', array_values($items));
		$response = $this->tweet($msg);
		plxMsg::Display($response[0]->message);
	}

}

?>
