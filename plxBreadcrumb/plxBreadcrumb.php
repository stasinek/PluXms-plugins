<?php
/**
 *
 * Plugin 	plxBreadcrumb
 * @author	Stephane F
 *
 **/
class plxBreadcrumb extends plxPlugin {

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

		$this->addHook('MyBreadcrumb', 'MyBreadcrumb');
	}

	public static function formatLink($href, $name, $link=false, $title='', $class='') {

		if($link) {
			$title = $title=='' ? $name : $title;
			$class = $class=='' ? '' : ' class="'.$class.'"';
			return '<a href="'.$href.'" title="'.plxUtils::strCheck($title).'"'.$class.'>'.plxUtils::strCheck($name).'</a>';
		} else
			return '<span class="here" style="font-weight:bold">'.$name.'</span>';

	}

	public function MyBreadcrumb() {
		echo '
		<?php
			# pages
			preg_match("/\/?page([0-9]+)$/",$plxShow->plxMotor->get,$pages);
			# home
			if($plxShow->plxMotor->get=="blog") {
				$url = "?blog";
				$breadcrumb[] = plxBreadcrumb::formatLink($plxShow->plxMotor->urlRewrite($url), "'.$this->getLang('L_BLOG').'", true, "'.$this->getLang('L_BLOG').' ".$plxShow->plxMotor->aConf["title"], "first");
			} else {
				$url = "?";
				$breadcrumb[] = plxBreadcrumb::formatLink($plxShow->plxMotor->urlRewrite($url), "'.$this->getLang('L_HOMEPAGE').'", true, "'.$this->getLang('L_HOMEPAGE').' ".$plxShow->plxMotor->aConf["title"], "first");
			}
			# traitement des différents modes
			switch($plxShow->mode()) {
				case "categorie":
					$breadcrumb[] = "'.$this->getLang('L_CATEGORY').'";
					$id = $plxShow->plxMotor->cible;
					$name = $plxShow->plxMotor->aCats[$id]["name"];
					$url = "?categorie".intval($id)."/".$plxShow->plxMotor->aCats[$id]["url"];
					$breadcrumb[] = plxBreadcrumb::formatLink($plxShow->plxMotor->urlRewrite($url), $name, isset($pages[1]));
					break;
				case "static":
					$id = $plxShow->plxMotor->cible;
					if($id!=$plxShow->plxMotor->aConf["homestatic"]) {
						$breadcrumb[] = "'.$this->getLang('L_STATIC').'";
						$group = $plxShow->plxMotor->aStats[$id]["group"];
						if(!empty($group)) $breadcrumb[] = $group;
						$name = $plxShow->plxMotor->aStats[$id]["name"];
						$url = "?static".intval($id)."/".$plxShow->plxMotor->aStats[$id]["url"];
						$breadcrumb[] = plxBreadcrumb::formatLink($plxShow->plxMotor->urlRewrite($url), $name);
					}
					break;
				case "tags":
					$breadcrumb[] = "'.$this->getLang('L_TAG').'";
					$id = $plxShow->plxMotor->cible;
					$name = $plxShow->plxMotor->cibleName;
					$url = "?tag/".$id;
					$breadcrumb[] = plxBreadcrumb::formatLink($plxShow->plxMotor->urlRewrite($url), $name, isset($pages[1]), "'.$this->getLang('L_TAG').' ".$id);
					break;
				case "archives":
					$breadcrumb[] = "'.$this->getLang('L_ARCHIVES').'";
					preg_match("/^(\d{4})(\d{2})?(\d{2})?/",$plxShow->plxMotor->cible, $capture);
					# year
					if(!empty($capture[1])) {
						$url = "?archives/".$capture[1];
						$p = isset($capture[2]) OR isset($pages[1]);
						$breadcrumb[] = plxBreadcrumb::formatLink($plxShow->plxMotor->urlRewrite($url), $capture[1], $p, "'.$this->getLang('L_ARCHIVES').' ".$capture[1]);
					}
					# month
					if(!empty($capture[2])) {
						$name = plxDate::getCalendar("month", $capture[2]);
						$url .= "/".$capture[2];
						$breadcrumb[] = plxBreadcrumb::formatLink($plxShow->plxMotor->urlRewrite($url), $name, isset($pages[1]));
					}
					break;
				case "article":
					$breadcrumb[] = "'.$this->getLang('L_ARTICLE').'";
					$id = $plxShow->plxMotor->plxRecord_arts->f("numero");
					$name = $plxShow->plxMotor->plxRecord_arts->f("title");
					$url = "?article".intval($id)."/".$plxShow->plxMotor->plxRecord_arts->f("url");
					$breadcrumb[] = plxBreadcrumb::formatLink($plxShow->plxMotor->urlRewrite($url), $name);
					break;
				case "erreur":
					$breadcrumb[] = "'.$this->getLang('L_ERROR').'";
					break;
				default:
					if(isset($plxShow->plxMotor->aStats[$plxShow->plxMotor->cible])) {
						$breadcrumb[] = "'.$this->getLang('L_STATIC').'";
						$breadcrumb[] = plxBreadcrumb::formatLink("", $plxShow->plxMotor->aStats[$plxShow->plxMotor->cible]["name"]);
					}
			}

			# n° de page
			if(isset($pages[1])) {
				$url .= "/page".$pages[1];
				$breadcrumb[] = plxBreadcrumb::formatLink($plxShow->plxMotor->urlRewrite($url), "'.$this->getLang('L_PAGE').' ".$pages[1]);
			}

			echo "'.$this->getLang('L_HERE').' : ";
			echo implode("<span class=\"sep\">&nbsp;&#187;&nbsp;</span>", $breadcrumb)

		?>
		';
	}

}
?>