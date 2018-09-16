<?php
/**
 * Plugin plxSlippry
 * @author	Stephane F
 **/

include(dirname(__FILE__).'/lib/class.plx.slippry.php');

class plxSlippry extends plxPlugin {

	public $slippry = null; # objet slippry

	public function __construct($default_lang) {

		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accèder à la page config.php et admin.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->setAdminProfil(PROFIL_ADMIN);

		$this->addHook('AdminMediasTop', 'AdminMediasTop');
		$this->addHook('AdminMediasPrepend', 'AdminMediasPrepend');

		$this->slippry = new slippry($default_lang);
		$this->slippry->getSlides();

		# déclaration des hooks
		if($this->slippry->aSlides) {
			$this->addHook('ThemeEndHead', 'ThemeEndHead');
			$this->addHook('ThemeEndBody', 'ThemeEndBody');
			$this->addHook('Slippry', 'Slippry');
		}

	}

	public function AdminMediasTop() {

		echo '<?php
		$arr = array("Slippry" => array("slippry_add" => "Ajouter au diaporama"));
		$selectionList = array_merge($selectionList, $arr);
		?>';

	}

	public function AdminMediasPrepend() {

		if(isset($_POST['selection']) AND $_POST['selection']=='slippry_add' AND isset($_POST['idFile'])) {
			$this->slippry->editSlides($_POST);
			header('Location: medias.php');
			exit;
		}

	}

	public function Slippry() {
		$s = "";
		foreach($this->slippry->aSlides as $i => $slide) {
			if($slide['active']) {
				if($slide['onclick']!='') {
					$href = $slide['onclick'];
					$onclick = $this->getParam('openwin') ? 'window.open(this, \'_blank\');return false' : '';
				} else {
					$href = '#slide'.intval($i);
					$onclick = 'return false;';
				}
				$s .= '<li><a onclick="'.$onclick.'" href="'.$href.'"><img src="'.plxUtils::strCheck($slide['url']).'" alt="'.plxUtils::strCheck($slide['description']).'" /></a></li>'."\n";
			}
		}
		if($s!="") {
			echo '<div class="sy-box" />'."\n";
			echo '<ul id="slippry" class="sy-list">'."\n".$s."</ul>\n";
			echo "</div>";
		}
	}

	public function ThemeEndHead() {

		echo '<link rel="stylesheet" href="'.PLX_PLUGINS.'plxSlippry/slippry/slippry.css" media="screen" />';
		if(intval($this->getParam('maxwidth'))>0) {
			echo '<style>div.sy-box { max-width: '.$this->getParam('maxwidth').'px !important;</style>'."\n";
		}
	}

	public function ThemeEndBody() {

		if($this->getParam('jquery')) {
			echo "\n".'<script>if (typeof jQuery == "undefined") {	document.write(\'<script src="'.PLX_PLUGINS.'plxSlippry\/slippry\/jquery-3.1.1.min.js"><\/script>\'); }</script>';
		}
		echo '<script src="'.PLX_PLUGINS.'plxSlippry/slippry/slippry.min.js"></script>'."\n";
		echo '
<script>
$(function() {
	var slippry = $("#slippry").slippry({
	transition: "'.$this->getParam('transition').'",
	speed: '.$this->getParam('speed').',
})});
</script>
';
	}
}
?>