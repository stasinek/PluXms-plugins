#!/usr/bin/env php
<?php
const APACHE_MIME_TYPES_URL = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
const MASK = '@^((?:application|audio|image|message|model|text|video|x-conference)/[\w-.]+)\s+(\w+(?:\s+\w+)*)$@';

/*
 * ANSI codes : for($i=30; $i<38; $i++) { echo "\e[{$i}m$i\e[0m\n"; }
 * */
$workdir = __DIR__;
$filename = "$workdir/mime.types";

try {
	if(!file_exists($filename) or filesize($filename) < 1000) {
		echo "\n\e[33mRequesting ".APACHE_MIME_TYPES_URL."\e[0m\n";
		$fp = fopen($filename, 'w');
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL				=> APACHE_MIME_TYPES_URL,
			CURLOPT_USERAGENT		=> 'Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/18.0.1',
			CURLOPT_FOLLOWLOCATION	=> true,
			CURLOPT_HEADER			=> false,
			CURLOPT_DNS_USE_GLOBAL_CACHE	=> true,
			CURLOPT_FILE			=> $fp
		));
		curl_exec($ch);
		echo "Retrieving ".curl_getinfo($ch, CURLINFO_EFFECTIVE_URL)."\n";
		echo "Received ".curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD)." bytes\n";
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$color = ($http_code != 200) ? "\e[31m" : '';
		echo "{$color}HTTP response : $http_code\e[0m\n";
		curl_close($ch);
		fclose($fp);
		if($http_code != 200) { exit; }
		echo "Downloading is done !\n";
	}
	echo "\e[33mParsing $filename...\e[0m\n";
	$output = array(
		'mime_types' =>	array(),
		'extensions' =>	array()
	);
	foreach(file($filename) as $line) {
		if(preg_match(MASK, $line, $matches)) {
			$parts = preg_split('@\s+@', $matches[2], -1, PREG_SPLIT_NO_EMPTY);
			foreach($parts as $key) {
				$output['extensions'][$key] = $matches[1];
			}
			$output['mime_types'][$matches[1]] = $parts;
		}
	}
	foreach($output as $part=>$values) {
		if(!empty($values)) {
			ksort($values);
			file_put_contents("$workdir/$part.json", json_encode($values, JSON_UNESCAPED_SLASHES));
			echo "File $workdir/$part.json saved\n";
		} else {
			echo "\e[31mNo value for $part\e[0m\n";
			exit;
		}
	}
	echo "\e[32mDone\e[0m\n";
}
catch (Extension $e) {
	echo "\e[31mSomething's wrong : ".$e->getMessage()."\e[0m";
}
?>
