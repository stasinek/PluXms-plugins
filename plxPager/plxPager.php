<?php
/**
 * Plugin plxPager
 * @author	Stephane F
 **/
class plxPager extends plxPlugin {

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

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

		# déclaration des hooks
		$this->addHook('plxShowPagination', 'plxShowPagination');

	}

	/**
	 * Méthode de traitement du hook plxShowPagination
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function plxShowPagination() {

		$string = '

		$nbpage = ceil($plxGlob_arts->count/$this->plxMotor->bypage);

		if($arg_url!="")
			$arg_url = trim($arg_url, "/")."/";

		$stop = $this->plxMotor->page + 2;
		if($stop<5) $stop=5;
		if($stop>$nbpage) $stop=$nbpage;

		$start = $stop - 4;
		if($start<1) $start=1;

		$plxPlugin = $this->plxMotor->plxPlugins->getInstance("plxPager");

		if($plxPlugin->getParam("elmt0"))
			echo "<span class=\"p_page\">Page ".$this->plxMotor->page." sur ".$nbpage."</span>";

		if($plxPlugin->getParam("elmt1") AND $this->plxMotor->page>2)
			echo "<span class=\"p_first\"><a href=\"".$f_url."\" title=\"".L_PAGINATION_FIRST_TITLE."\">".L_PAGINATION_FIRST."</a></span>";

		if($plxPlugin->getParam("elmt2") AND $this->plxMotor->page>1)
			echo "<span class=\"p_prev\"><a href=\"".$p_url."\" title=\"".L_PAGINATION_PREVIOUS_TITLE."\" rel=\"prev\">".L_PAGINATION_PREVIOUS."</a></span>";

		if($plxPlugin->getParam("elmt3") AND $start>1)
			echo "<span class=\"p_page\">...</span>";

		for($i=$start;$i<=$stop;$i++) {
			$url = $this->plxMotor->urlRewrite("?".$arg_url.($i<=1?"":"page".$i));
			if($i==$this->plxMotor->page)
				echo "<span class=\"p_current\">".$i."</span>";
			else
				echo "<span class=\"p_page\"><a href=\"".$url."\" title=\"".$i."\">".$i."</a></span>";
		}

		if($plxPlugin->getParam("elmt3") AND ($this->plxMotor->page+2)<$nbpage AND $stop<$nbpage)
			echo "<span class=\"p_page\">...</span>";

		if($plxPlugin->getParam("elmt4") AND $this->plxMotor->page<$nbpage)
			echo "<span class=\"p_next\"><a href=\"".$n_url."\" title=\"".L_PAGINATION_NEXT_TITLE."\" rel=\"next\">".L_PAGINATION_NEXT."</a></span>";

		if($plxPlugin->getParam("elmt5") AND ($this->plxMotor->page<($nbpage-1)))
			echo "<span class=\"p_last\"><a href=\"".$l_url."\" title=\"".L_PAGINATION_LAST_TITLE."\">".L_PAGINATION_LAST."</a></span>";

		return true;
		';
		echo "<?php ".$string." ?>";

	}

}
?>