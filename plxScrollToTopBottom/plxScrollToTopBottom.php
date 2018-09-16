<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
include(dirname(__FILE__).'/lib/class.plx.scrolltotopbottom.php'); 
 
class plxScrollToTopBottom extends plxPlugin {
	
	
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

		# droits pour accéder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		
		# Vérification des version du plugin
		$this->scrolltotopbottom = new scrolltotopbottom();
		
		# Ajouts des hooks
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('ThemeEndBody', 'ThemeEndBody');
	}

	/**
	 * Méthode pour afficher la mise en page 
	 *
	 * @author DPFPIC
	 */
	public function ThemeEndHead() {
	
	    $pleft = plxPlugin::getParam('iconSize') + 14;
		$pright = plxPlugin::getParam('iconSize') + 14;
	
		echo "\n\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'plxScrollToTopBottom/css/scrolltotopbottom.css" media="screen" />'."\n";
		echo "\n\t".'<style type="text/css">'."\n";
		echo "\n\t".'.sttb-bottom-left .scroll-to-bottom {'."\n";
		echo "\n\t".'	left: '.$pleft.'px;'."\n";
		echo "\n\t".'}'."\n";
		echo "\n\t".'.sttb-bottom-right .scroll-to-bottom {'."\n";
		echo "\n\t".'	right: '.$pright.'px;'."\n";
		echo "\n\t".'}'."\n";		
		echo "\n\t".'</style>'."\n";  

	}
	
	/**
	 * Méthode pour afficher le javascript
	 *
	 * @author DPFPIC
	 */
	public function ThemeEndBody() {
	
		echo "\n\t".'
		<script type="text/javascript">
		if (typeof jQuery == "undefined") {
			document.write(\'<script type="text\/javascript" src="'.PLX_PLUGINS.'plxScrollToTopBottom\/js\/jquery-2.1.3.min.js"><\/script>\');
		}
		</script>'."\n";
		echo "\n\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxScrollToTopBottom/js/scrolltotopbottom.js"></script>'."\n";

		?>
		
		<script>
				jQuery(document).ready(function() {

					jQuery('.scroll-to-top').click(function(){
						jQuery("html,body").animate({ scrollTop: 0 }, <?php echo plxPlugin::getParam('scrollSpeed'); ?>, "swing");
						return false;
					});
					jQuery('.scroll-to-bottom').click(function(){
						jQuery('html,body').animate({scrollTop: jQuery(document).height()}, <?php echo plxPlugin::getParam('scrollSpeed'); ?>, "swing");
						return false;
					});
				});
			</script><?php
			$class = plxPlugin::getParam('posButtonH').'-'.plxPlugin::getParam('posButtonV');
			
		?><div class="sttb-container sttb-<?php echo $class; ?>"><?php
				# récupérer la racine du site http//.... et du repertoire plugins
				$baseDir = plxUtils::getRacine();
				#if (substr(plxPlugin::getParam('upCustomButton'),0,4) != 'http') {
					
				$up_btn = plxPlugin::getParam('upCustomButton');
				$down_btn = plxPlugin::getParam('downCustomButton');
				$iconsize = plxPlugin::getParam('iconSize');
		
					if(plxPlugin::getParam('icon_Select') == '13'){
				if(plxPlugin::getParam('whichButton') == 2) { ?>
					<div style="display:none;" class="scroll-to-top" id="scroll-to-top">
						<?php echo '<img alt="&uarr;" width="'.$iconsize.'" height="'.$iconsize.'" src="' .$up_btn. '"> '; ?>
					</div>
				<?php } elseif(plxPlugin::getParam('whichButton') == 3) { ?>
					<div style="display:block;" class="scroll-to-bottom" id="scroll-to-bottom">
						<?php echo '<img alt="&darr;" width="'.$iconsize.'" height="'.$iconsize.'" src="' .$down_btn. '"> '; ?>
					</div>
				<?php } else { ?>
					<div style="display:none;" class="scroll-to-top" id="scroll-to-top">
						<?php echo '<img alt="&uarr;" width="'.$iconsize.'" height="'.$iconsize.'" src="' .$up_btn. '"> '; ?>
					</div>
					<div style="display:block;" class="scroll-to-bottom" id="scroll-to-bottom">
						<?php echo '<img alt="&darr;" width="'.$iconsize.'" height="'.$iconsize.'" src="' .$down_btn. '">  '; ?>
					</div>
						
				<?php }
			} else {
				$icon_Select = plxPlugin::getParam('icon_Select');
				if(plxPlugin::getParam('whichButton') == 2) { ?>
				<div style="display:none;" class="scroll-to-top" id="scroll-to-top">
					<?php echo '<img alt="&uarr;" width="'.$iconsize.'" height="'.$iconsize.'" src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/'.$icon_Select.'_u.png">'; ?>
				</div>
				<?php } elseif(plxPlugin::getParam('whichButton') == 3) { ?>
				<div style="display:block;" class="scroll-to-bottom" id="scroll-to-bottom">
					<?php echo '<img alt="&darr;" width="'.$iconsize.'" height="'.$iconsize.'" src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/'.$icon_Select.'_d.png">'; ?>
				</div>
				<?php } else { ?> 
				<div style="display:none;" class="scroll-to-top" id="scroll-to-top">
					<?php echo '<img alt="&uarr;" width="'.$iconsize.'" height="'.$iconsize.'" src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/'.$icon_Select.'_u.png">'; ?>
				</div>
				<div style="display:block;" class="scroll-to-bottom" id="scroll-to-bottom">
					<?php echo '<img alt="&darr;" width="'.$iconsize.'" height="'.$iconsize.'" src="' .PLX_PLUGINS.'plxScrollToTopBottom/img/'.$icon_Select.'_d.png">'; ?>
				</div>
			<?php } }
			?></div><?php
	}
}	