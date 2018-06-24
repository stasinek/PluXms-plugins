<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
 
include(dirname(__FILE__).'/lib/class.plx.sharesocialbuttons.php'); 
 
class plxShareSocialButtons extends plxPlugin {

	protected $callable = false;

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

		# limite l'accès à l'écran d'administration du plugin
		$this-> setConfigProfil(PROFIL_ADMIN);	
		# limite l'accès à l'écran d'administration du plugin
		$this->setAdminProfil(PROFIL_WRITER,PROFIL_MANAGER,PROFIL_MODERATOR,PROFIL_EDITOR,PROFIL_ADMIN);
		
		# déclaration des hooks
		$this->addHook('ThemeEndHead', 'ThemeEndHead');			
		$this->addHook('plxShowShareSocialButtons', 'plxShowShareSocialButtons');
		$this->addHook('plxShowShareSocialButtonsStatic', 'plxShowShareSocialButtonsStatic');
		
		$this->sharesocialbuttons = new sharesocialbuttons();
	}

	
	public function ThemeEndHead() {
		
		$Imagefb = plxPlugin::getParam('Imagefb');
		$plxShow = plxShow::getInstance();
		if ($plxShow->plxMotor->mode === 'article') {		
			$content = $plxShow->plxMotor->plxRecord_arts->f('chapo').$plxShow->plxMotor->plxRecord_arts->f('content');
			// recupère la taille de la première image de l'article
			list($width, $height) = @getimagesize($this->imageUrl($content));
			$baseDirPlugins = plxUtils::getRacine();
			if (strpos($Imagefb, 'http') !== 0) {
				$Imagefb = $baseDirPlugins . trim(plxPlugin::getParam('Imagefb'));
			}
			if ($height > 200) {
				$image = $this->imageUrl($content);
			} else {
				$image = $Imagefb;
			}
		} else {
			$image = $Imagefb;
		}	

		if (plxPlugin::getParam('SSBDefaultfb')== 1) {
			$image = $Imagefb;
		}
		list($width, $height) = @getimagesize($image);
		
		echo "\n\t".'<link rel="stylesheet" href="'.PLX_PLUGINS.'plxShareSocialButtons/css/plxShareSocialButtons.css" type="text/css" media="screen" />'.PHP_EOL;
		if (plxPlugin::getParam('SSBTarget') == 1) {
			echo "\n\t".'<script type="text/javascript">
				/* <![CDATA[ */
				!window.jQuery && document.write(\'<script  type="text/javascript" src="'.PLX_PLUGINS.'plxShareSocialButtons/js/jquery-2.1.3.min.js"><\/script>\');
				/* !]]> */
			</script>'.PHP_EOL;
			echo "\n\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'plxShareSocialButtons/js/plxShareSocialButtons.js"></script>'.PHP_EOL;
		}
		
		echo "\n\t".'<script type="text/javascript">
		(function(d){
			var f = d.getElementsByTagName(\'SCRIPT\')[0], p = d.createElement(\'SCRIPT\');
			p.type = \'text/javascript\';
			p.async = true;
			p.src = \'//assets.pinterest.com/js/pinit.js\';
			f.parentNode.insertBefore(p, f);
			}(document));
		</script>'.PHP_EOL;

		echo '<style>'.PHP_EOL;
		echo '#bloc-social {text-align: '.plxPlugin::getParam('SSBPos').'}'.PHP_EOL;
		echo '#bloc-social .title{font-size: '.plxPlugin::getParam('SSBFont').'px;}'.PHP_EOL;
		echo '.social{background:transparent;display:inline-block;margin-left:0px;}'.PHP_EOL;
		echo '.social img{margin-left: '.plxPlugin::getParam('SSBPad').'px;margin-right: '.plxPlugin::getParam('SSBPad').'px;width: '.plxPlugin::getParam('SSBSize').'px}'.PHP_EOL;
		echo '</style>'.PHP_EOL;
		
		echo '<!-- debut open graph insertion partage open graph -->'.PHP_EOL;
		echo '<meta property="og:type" content="article"/>'.PHP_EOL;
		echo '<meta property="og:image" content="'.$image.'"/>'.PHP_EOL;
		echo '<meta property="og:image:width" content="'.$width.'">'.PHP_EOL;
		echo '<meta property="og:image:height" content="'.$height.'">'.PHP_EOL;
		echo '<!-- fin insertion partage open graph -->'.PHP_EOL;
    }
	
	/**
	 * Méthode qui affiche les boutons sociaux
	 * @return	echo
	 * @author	DPFPIC
	 **/
	public function plxShowShareSocialButtons() {
		$this->callable = true;
		$plxShow = plxShow::getInstance();
		
		# On affiche l'URL
		$id = intval($plxShow->plxMotor->plxRecord_arts->f('numero'));
		$url = $plxShow->plxMotor->plxRecord_arts->f('url');
		#Urlencode = urlencode($plxShow->plxMotor->urlRewrite('?article'.$id.'/'.$url));
		$Urlencode = $plxShow->plxMotor->urlRewrite('?article'.$id.'/'.$url);
		/*$Title = "<?php \$plxShow->artTitle() ?>";*/
		ob_start(); 
		$plxShow->artTitle();
		$Title = ob_get_contents();
		ob_end_clean();
		

		echo '<!-- Début du plugin ShareSocialButtons -->'.PHP_EOL;
		echo '<div id="bloc-social">';
		echo '<p class="title">'.plxPlugin::getParam('SSBText').'</p>';
		echo '<div class="social">';
 
			for ($i = 0; $i < plxPlugin::getParam('SSBenableCount'); $i++)
			{
				$this->GetUrlButtons(plxPlugin::getParam('act_'.$i), $Urlencode, $Title);
			}
 			
		echo '</div>';
		echo '</div>';
		echo '<!-- Fin du plugin ShareSocialButtons -->'.PHP_EOL;
	}

	/**
	 * Méthode qui affiche les boutons sociaux sur une page statique
	 *
	 * @return	echo
	 * @author	DPFPIC
	 **/
	public function plxShowShareSocialButtonsStatic() {
		$this->callable = true;
		$plxShow = plxShow::getInstance();
		
		# On affiche l'URL
		$id = intval($plxShow->staticId());
		$staticIdFill = str_pad($id,3,'0',STR_PAD_LEFT);
		$query = $_SERVER['QUERY_STRING'];
		if(!empty($id) AND isset($plxShow->plxMotor->aStats[ $staticIdFill ])) {
			$Urlencode = $plxShow->plxMotor->urlRewrite('?static'.$id.'/'.$plxShow->plxMotor->aStats[ $staticIdFill ]['url']);
		}
		if ($_SERVER['QUERY_STRING'] != 'static') {
		    $query = $_SERVER['QUERY_STRING'];
			$Urlencode = $plxShow->plxMotor->urlRewrite('?'.$query);
		}
		
		$Title = "<?php \$plxShow->staticTitle() ?>";
	
		echo '<!-- Début du plugin ShareSocialButtonsStatic -->'.PHP_EOL;	
		echo '<div id="bloc-social">';
		echo '<p class="title">'.plxPlugin::getParam('SSBText').'</p>';
		echo '<div class="social">';
 
			for ($i = 0; $i < plxPlugin::getParam('SSBenableCount'); $i++)
			{
				$this->GetUrlButtons(plxPlugin::getParam('act_'.$i), $Urlencode, $Title);
			}
 			
		echo '</div>';
		echo '</div>';
		echo '<!-- Fin du plugin ShareSocialButtonsStatic -->'.PHP_EOL;	
	}
	
	/**
	 * Méthode qui génère les différents liens des services sociaux
	 *
	 * @return	echo
	 * @author	DPFPIC
	 **/
	public function GetUrlButtons($service, $Url, $Title) {
		$Title = urlencode($Title);
		
	// switch based on the inputn type
		switch($service)
		{
			// get buffer button
			case 'buffer':
			default:
				// buffer share link
				$htmlSSBs = '<a  data-site="buffer" href="https://bufferapp.com/add?url=' . $Url . '&amp;text=' . $Title . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/buffer.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Buffer" alt="Share on Buffer" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;
			
			// get diggit button
			case 'digg':
				// digg share link
				$htmlSSBs = '<a  data-site="digg" href="http://www.digg.com/submit?url=' . $Url . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/digg.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Digg" alt="Share on Digg" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;
			
			// get facebook button
			case 'facebook':
				// facebook share link
				$htmlSSBs = '<a  data-site="facebook" href="http://www.facebook.com/sharer/sharer.php?u=' . $Url . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/facebook.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Facebook" alt="Share on Facebook" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;	
			
			// get flattr button
			case 'flattr':
				// flattr share link
				$htmlSSBs = '<a  data-site="flattr" href="https://flattr.com/submit/auto?user_id=&amp;title=' . $Title . '&amp;url=' . $Url . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/flattr.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Flattr" alt="Share on Flattr" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;	

			case 'google':
				// google share link
				$htmlSSBs = '<a  data-site="google" href="https://plus.google.com/share?url=' . $Url . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/google.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Google" alt="Share on Google" />';
				// close href
				$htmlSSBs .= '</a>';	
				
			break;

			// get linkedin button
			case 'linkedin':
				// linkedin share link
				$htmlSSBs = '<a  data-site="linkedin" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . $Url . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/linkedin.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Linkedin" alt="Share on Linkedin" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;	

			// get pinterest button			
			case 'pinterest':
				// pinterest share link
				//$htmlSSBs = '<a data-site="pinterest" href="http://pinterest.com/pin/create/bookmarklet/?is_video=false&url='. $Url .'&amp;media=&amp;description='. $Title .'" '.(plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL).'>';
				$htmlSSBs = '<a id="clickMe" href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonPin" data-pin-custom="true" >'; // Version page full
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/pinterest.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Pinterest" alt="Share on Pinterest" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;

			// get reddit button
			case 'reddit':
				// reddit share link
				$htmlSSBs = '<a  data-site="reddit" href="http://reddit.com/submit?url=' . $Url  . '&amp;title=' . $Title . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/reddit.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Reddit" alt="Share on Reddit" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;			

			// get stumbleupon button
			case 'stumbleupon':
				// stumbleupon share link
				$htmlSSBs = '<a  data-site="stumbleupon" href="http://www.stumbleupon.com/submit?url=' . $Url . '&amp;title=' . $Title . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/stumbleupon.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Stumbleupon" alt="Share on Stumbleupon" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;

			// get tumblr button
			case 'tumblr':
				// tumblr share link
				$htmlSSBs = '<a  data-site="tumblr" href="http://www.tumblr.com/share/link?url=' . $Url . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/tumblr.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Tumblr" alt="Share on Tumblr" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;
			
			// get twitter button
			case 'twitter':
				// twitter share link
				$htmlSSBs = '<a  data-site="twitter" href="http://twitter.com/share?url=' . $Url . '&amp;text=' . $Title . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/twitter.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Twitter" alt="Share on Twitter" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;

			// get vk button			
			case 'vk':
				// vk share link
				$htmlSSBs = '<a  data-site="vk" href="http://vkontakte.ru/share.php?url=' . $Url . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/vk.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Vk" alt="Share on Vk" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;

			// get yummly button
			case 'yummly':
				// yummly share link
				$htmlSSBs = '<a  data-site="yummly" href="http://www.yummly.com/urb/verify?url=' . $Url . '&title=' . $Title . '" ' . (plxPlugin::getParam('SSBTarget') == 0 ? ' target="_blank" ' : NULL) . (plxPlugin::getParam('SSBTarget') == 1 ? ' rel="nofollow" ' : NULL) . '>';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/yummly.png" title="' . plxPlugin::getlang('L_SSB_SHARE') . ' Yummly" alt="Share on Yummly" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;			

			// get email button
			case 'email':
				// email share link
				$htmlSSBs = '<a data-site="email" href="mailto:?subject=' . $Title . '&amp;body=' . $Url . '">';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/email.png" title="' . plxPlugin::getlang('L_SSB_EMAIL') . ' Email" alt="Email this to someone" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;

			// get print button
			case 'print':
				// print share link
				$htmlSSBs = '<a data-site="print" href="#" onclick="window.print()">';
				// show image
				$htmlSSBs .= '<img src="' . PLX_PLUGINS . 'plxShareSocialButtons/themes/' . plxPlugin::getParam('SSBThemes') . '/print.png" title="' . plxPlugin::getlang('L_SSB_PRINT') . '" alt="Print this page" />';
				// close href
				$htmlSSBs .= '</a>';				
			break;
		}
		echo $htmlSSBs;
	}
	
   /**
     * Retourne le contenu de l'url de la première image de l'article
     * @param plxShow $plxShow
     * @return string
     */
    private function imageUrl($content)
    {
        $image = '';
        if (@preg_match('~<img[^>]*?src="(.*?)"[^>]+>~', $content, $match)) {
            $image = trim($match[1]);
            if (strpos($image, 'http') !== 0) {
                $image = plxUtils::getRacine() . trim($match[1]);
				$image = str_replace('https', 'http', $image);
            }
        }
		
        return $image;
    }	
	
}
?>