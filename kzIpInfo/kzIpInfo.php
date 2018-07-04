<?php
if(!defined('PLX_ROOT')) { exit; }

/**
 * Affiche le drapeau du pays du visiteur qui a posté un commentaire,
 * ainsi que la ville et le code ISO 3166-1 alpha2 du pays.
 *
 * le container pour afficher le drapeau doit contenir l'adresse IP dans son attribut "data-ip".
 * Les infos de géo-localisation sont fournis par le site http://ipinfo.io.
 * Pour limiter le nombre de requêtes vers ce fournisseur, les infos sont mises
 * en cache dans le containeur sessionStorage entre chaque page HTML.
 *
 * L'affichage de chaque drapeau est affiché en fond d'image en utilisant un
 * grand sprite regroupant tous les drapeaux. Le sprite eput être généré avec
 * le script https://kazimentou.fr/divers/PluXml/flags.py.
 *
 * Le plugin étant exécuté en javascript côté navigateur, il n'y a aucune
 * exigence côté serveur ( allow_url_open : valeur quelconque ).
 *
 * @author	J.P. Pourrez
 * @version 2018-01-23
 * */
class kzIpInfo extends plxPlugin {

	const COMMENTS_SELECTOR = '#comments-table:not(.flag) td[data-ip]';
	const COMMENT_EDIT_SELECTOR = '#form_comment li[data-ip]';
	const PLX_FLAGS_32_PATH = '../assets/img/flags/32/';
	const SPRITES = true;
	const FILTER_IP = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE;

	public function __construct($default_lang) {

		parent::__construct($default_lang);

		if(self::SPRITES) {
			parent::addHook('AdminTopEndHead', 'AdminTopEndHead');
		}
		parent::addHook('AdminCommentsFoot', 'AdminCommentsFoot');
		parent::addHook('AdminCommentFoot', 'AdminCommentFoot');
	}

	private function __printJS($selectorCSS, $blacklist='') {
		$data = '';
		if(!empty($blacklist)) {
			$blacklist = array_values(array_unique($blacklist)); # array_unique brise la numérotation continue des index
			if(!empty($blacklist)) {
				$data = " data-blacklist='".json_encode($blacklist)."'";
			}
		}
?>
<script type="text/javascript"<?php echo $data; ?>>
	(function() {
		'use strict';

		const cells = Array.prototype.slice.call(document.body.querySelectorAll('<?php echo $selectorCSS; ?>'));

		if(cells.length > 0) {
			cells.reverse();
			const script = document.body.querySelector('script[data-blacklist]');
			const blacklist = (script != null) ? JSON.parse(script.getAttribute('data-blacklist')) : null;
			const KEY_STORAGE = 'kzIpInfo';
			const XHR =  new XMLHttpRequest();
			const content = sessionStorage.getItem(KEY_STORAGE);
			var datas = (content == null) ? { ipList: {}, timeStamp: 0} : JSON.parse(content);
			var ipListUpdated = false;
			var counter = cells.length -1;
			function setFlag() {
				if(counter >= 0) {
					const cell = cells[counter]
					const ip = cell.getAttribute('data-ip');
					if(blacklist == null || blacklist.indexOf(ip) < 0) {
						if(!(ip in datas.ipList)) {
							XHR.open('GET', 'https://ipinfo.io/' + ip + '/geo', true);
							XHR.send();
						} else {
							datas.ipList[ip].timeStamp = datas.timeStamp;
							const country = datas.ipList[ip].country;

							// Adds a flag in the HTML page. And more...
<?php
		if(!empty(self::SPRITES)) {
?>
							const flag = document.createElement('SPAN');
							flag.className = 'kz-flag ' + country;
<?php
		} else {
?>
							const flag = document.createElement('IMG');
							flag.src = '<?php echo PLX_FLAGS_32_PATH; ?>' + country + '.png';
							flag.className = 'flag';
							flag.alt = country;
<?php
		}
?>
							flag.title = (datas.ipList[ip].region.trim().length > 0) ? [country, datas.ipList[ip].region].join(' : ') : country;
							// cell.appendChild(document.createElement('BR'));
							cell.appendChild(flag);

							if(datas.ipList[ip].city.trim().length > 0) {
								const span = document.createElement('SPAN');
								span.innerHTML = datas.ipList[ip].city + ', ' + country;
								cell.appendChild(span);
							}

							counter--;
							setFlag();
						}
					} else {
						counter--;
						setFlag();
					}
				} else {
					// Save infos about IP in sessionStorage
					const maxIPs = 256
					if(Object.keys(datas.ipList).length > maxIPs) {
						var index = new Array();
						for(var ip in datas.ipList) {
							index.push([ip, datas.ipList[ip].timeStamp]);
						}
						// Tri selon la valeur de timeStamp
						index.sort(function (a,b) {
							return b[1] - a[1]
						});
						console.log('Trop d\'IPs : ' + index.length);
						console.log(index);
						for(var i=maxIPs, iMax=index.length; i<iMax; i++) {
							delete datas.ipList[index[i][0]];
						}
					}
					if(ipListUpdated) {
						console.log(Object.keys(datas.ipList).length + ' IPs enregistrés')
						sessionStorage.setItem(KEY_STORAGE, JSON.stringify(datas));
					}
				}
			}

			XHR.onreadystatechange = function (event) {
			    if (this.readyState === XMLHttpRequest.DONE) {
			        if (this.status === 200) {
						datas.timeStamp++;
						const geoInfos = JSON.parse(this.responseText);
						geoInfos.timeStamp = datas.timeStamp;
			            datas.ipList[geoInfos.ip] = geoInfos;
			            ipListUpdated = true;
			            setFlag();
			        } else {
			            console.log("Status de la réponse: %d (%s)", this.status, this.statusText);
			        }
			    }
			}

			setFlag();
		}
	})();
</script>
<?php
	}

/* --------- Hooks ------------- */

	public function AdminCommentsFoot() {
		# https://stackoverflow.com/questions/13818064/check-if-an-ip-address-is-private
		global $plxAdmin;

		$blacklist = array();
		foreach($plxAdmin->plxRecord_coms->result as $com) {
			$ip = $com['ip'];
			if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_IP)) {
				$blacklist[] = $ip;
			}
		}
		self::__printJS(self::COMMENTS_SELECTOR, $blacklist);
?>
	<div class="in-action-bar ipinfo-logo">
		<a href="https://ipinfo.io" rel="noreferrer, nofollow" target="_blank"><img src="<?php echo PLX_PLUGINS.__CLASS__;  ?>/logo.svg"></a>
	</div>
<?php
	}

	public function AdminCommentFoot() {
		global $plxAdmin;

		$ipAddr = $plxAdmin->plxRecord_coms->f('ip');
		if(
			!empty($ipAddr) and
			filter_var($ipAddr, FILTER_VALIDATE_IP, FILTER_IP)
		) {
			self::__printJS(self::COMMENT_EDIT_SELECTOR);
		}
	}

	public function AdminTopEndHead() {
		# For building flags.* files, download: https://kazimentou.fr/divers/PluXml/flags.py
		if(file_exists(__DIR__.'/flags.css')) {
			$href = PLX_PLUGINS.__CLASS__.'/flags.css';
			echo <<< LINK
		<link rel="stylesheet" href="$href" />
LINK;
		}
	}

}