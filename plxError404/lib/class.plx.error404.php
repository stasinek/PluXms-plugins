<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
class error404 {

	public function __construct() {
		
	}

	/**
	 * Verification des version des plugins
	 *
	 * @param	plugin				le nom du plugin
	 * @return	array()
	 * @author	DPFPIC
	 **/
	public function UpdatePlugin($plugin) {
	    $array = array();
		$url = 'http://dpfpic.com/plugins.rep';
		$filename = PLX_PLUGINS.$plugin.'/infos.xml';
		
		# Mise en place du parseur XML
		$data = implode('',file($filename));
		$parser = xml_parser_create('UTF-8');
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
		xml_parse_into_struct($parser,$data,$strings,$iTags);
		xml_parser_free($parser);
		# Récupération des données xml
		$array['actualversion'] = plxUtils::getValue($strings[$iTags['version'][0]]['value']);
		$array['actualdate'] = plxUtils::getValue($strings[$iTags['date'][0]]['value']);


		if (ini_get('allow_url_fopen')) {
			$handleplugin = @file($url);
		}
		elseif ($curl = @curl_init()){
			$timeout = 5; // set to zero for no timeout
			curl_setopt ($curl, CURLOPT_URL, $url);
			curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
			$version_content = curl_exec($curl);
			curl_close($curl);
			if ($version_content != false) {
				$handleplugin = explode("\n", $version_content);
			}
		}	
		if ($handleplugin) {
			$nb = sizeof($handleplugin);
			for($i=0;$i<$nb;$i++) {
				$seach = strstr($handleplugin[$i], '||', true);
				if ($seach == $plugin) {
				    $value = explode('||', $handleplugin[$i]);
					$array['newplugin'] = $value[0];
					$array['newversion'] = $value[1];
					$array['newdate'] = $value[2];
					$array['newurl'] = $value[3];
					$array['active'] = $value[4];
				}
			}
		} else {
			$array ['status'] = 0;
			return $array;
		}
		
		$adate = explode("/", $array['actualdate']); 
		$ndate = explode("/", $array['newdate']);
		$actualdate = $adate[2].$adate[1].$adate[0];
		$newdate = $ndate[2].$ndate[1].$ndate[0];
		
		if (isset($array['actualversion']) AND ($array['actualversion'] == $array['newversion']  AND  $actualdate == $newdate) AND $array['active'] == 1 AND $array['newplugin'] == $plugin) {
			$array ['status'] = 1;
			return $array;
		} elseif (isset($array['actualversion']) AND ($array['actualversion'] != $array['newversion'] OR  $actualdate != $newdate) AND $array['active'] == 1 AND $array['newplugin'] == $plugin) {
			$array ['status'] = 2;
			return $array;
		} elseif (isset($array['active']) AND $array['active'] == 0) {
			$array ['status'] = 3;
			return $array;
		} else {
		    $array ['status'] = 0;
			return $array;
		}
	}		
}
?>