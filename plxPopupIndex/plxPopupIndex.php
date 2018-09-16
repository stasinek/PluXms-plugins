<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
include(dirname(__FILE__).'/lib/class.plx.popupindex.php'); 
 
class plxPopupIndex extends plxPlugin {

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

		# droits pour accéder à la page config.php et admin.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->setAdminProfil(PROFIL_ADMIN);
		
		# Vérification des version du plugin
		$this->popupindex = new popupindex();
		
		# ajout du hook
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('ThemeEndBody', 'ThemeEndBody');
		$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
	}
	
	public function AdminTopEndHead() {
	
		echo "\n\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxPopupIndex/js/jscolor/jscolor.js"></script>';
	}
	
	
	public function ThemeEndHead() {
	
			echo "\n\t".'
			<script type="text/javascript">
			if (typeof jQuery == "undefined") {
				document.write(\'<script type="text\/javascript" src="'.PLX_PLUGINS.'plxPopupIndex\/js\/jquery-2.1.3.min.js"><\/script>\');
			}
			</script>';
			echo "\n\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'plxPopupIndex/css/magnific-popup.css" type="text/css" media="screen" />'."\n";
			echo "\n\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxPopupIndex/js/magnific-popup.js"></script>'."\n";
			echo "\n\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxPopupIndex/js/jquery.cookie.js"></script>';
	}
	
	public function ThemeEndBody() {
	
	
	    $datenow = date('YmdHi');

		if($datenow > plxPlugin::getParam('date_start') AND plxPlugin::getParam('date_end') > $datenow OR plxPlugin::getParam('inFinite') == 1) {

		if (substr($_SERVER['REQUEST_URI'], -1) == "/") {
			$racine = plxUtils::getRacine();
			$content = str_replace(array("\r\n","\r"),"",plxPlugin::getParam('contentPopup'));
		    $content = str_replace("'", "\'",$content);
			$content = str_replace("../../", $racine,$content);
			?>
			<style type="text/css">
			.popup {
			    max-width: <?php echo plxPlugin::getParam('widthPopup'); ?>px;
				border: <?php echo plxPlugin::getParam('borderSizeColorPopup'); ?>px solid #<?php echo plxPlugin::getParam('borderColorPopup'); ?>;
				background: #<?php echo plxPlugin::getParam('BgColorPopup'); ?>;
				border-radius: <?php echo plxPlugin::getParam('borderRadiusPopup'); ?>px;
			}	
			</style>
			<script type="text/javascript">
			var onlySession = <?php echo plxPlugin::getParam('onlySession'); ?>;
			if(onlySession != 1) { $.cookie('the_cookie', '1', { expires: -1 }); }
			if($.cookie('the_cookie') != 1 || onlySession == 0) { // Si the_cookie n'a pas pour valeur 1 alors on l'initialise et on joue l'appel de la popup
				$.cookie('the_cookie', '1', { expires: 1 }); // valeur en jour avant expiration du cookie
				$.magnificPopup.open({
					closeAutoDelay: <?php echo plxPlugin::getParam('closeAutoDelay'); ?>,
					tClose: '<?php echo plxPlugin::getLang('L_PI_CLOSE'); ?>',
					showCloseBtn : true,
					items: {
					src: '<div class="popup"><?php echo $content; ?></div>',
					type: 'inline'	
					}
				});
				}
			</script>
			<?php
		}
		}
	}
	
}
?>