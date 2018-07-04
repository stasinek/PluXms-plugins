<?php
if(!defined('PLX_ROOT')) { exit; }

/**
 * Assure le suivi des commentaires par courriel pour l'auteur et les visiteurs.
 *
 * Chaque courriel contient le titre et l'URL de l'article.
 *
 * De plus, pour les visiteurs, il contient un lien de désabonnement,
 * comprenant numéro d'article, mail et nom sous forme cryptée.
 * La liste des abonnés est stockée au format JSON dans le dossier de
 * configuration des plugins.
 * */
class kzMailCommentAlert extends plxPlugin {

	const RECIPIENTS = 'admin author followers';
	const NUM_FIELDS = 'max_comments delay';

	# using by plxMotorAddCommentaire hook.
	# catch email, name and ip addr of the author of the new comment
	# and leave go on.
	const CODE_ADD_COMMENT = <<< 'CODE'
<?php
if(
	!empty($_POST['subscribe']) and
	$_POST['subscribe'] === '1'
) {
	$this->plxPlugins->aPlugins['kzMailCommentAlert']->newFollower = array(
		'author'	=> $content['author'],
		'mail'		=> $content['mail'],
		'ip'		=> $_SERVER['REMOTE_ADDR']
	);
}
	return false; # must to be continued
?>
CODE;

	# using by plxMotorDemarrageNewCommentaire hook.
	const CODE_NEW_COMMENT = <<< 'CODE'
<?php
if(($retour[0] == 'c') or ($retour == 'mod')) {
	$this->plxPlugins->aPlugins['kzMailCommentAlert']->alertMail(
		$this,
		($retour[0] == 'c')
	);
}
?>
CODE;

	# Using by ThemeEndBody hook for adding a checkbox at the form for comment.
	const CODE_THEME_END_BODY = <<< 'CODE'
<?php
	$content = <<< CONTENT
</textarea>
		<input type="checkbox" name="subscribe" value="1" id="id_subscribe" disabled />
		<label for="id_subscribe">##SUBSCRIBE##</label>\n
CONTENT;
	$output = str_replace('</textarea>', $content, $output);
	$mail_mask = '@(<input\s+[^>]*name=(?:"mail"|\'mail\')[^>]*>)@';
	if(preg_match($mail_mask, $output, $matches)) {
		$replace = preg_replace('@type=(?:"text"|\'text\')@', 'type="email"', $matches[1]);
		$output = preg_replace($mail_mask, $replace, $output);
	}
?>
	<script type="text/javascript"> <!-- kzMailCommentAlert -->
		(function() {
			'use strict';

			const el = document.body.querySelector('form input[type="email"]');
			if(el != null) {
				el.addEventListener('change', function(event) {
					const chk = document.body.querySelector('form input[type="checkbox"][name="subscribe"]')
					chk.disabled = !event.target.checkValidity();
					if(chk.disabled) { chk.checked = false; }
					event.preventDefault();
				})
			}
		})();
	</script>

CODE;

	public $newFollower = false;

	public function __construct($default_lang) {

		parent::__construct($default_lang);

		parent::setConfigProfil(PROFIL_ADMIN);

		# For unsubscribing
		parent::addHook('IndexBegin', 'IndexBegin');

		if(
			!empty(parent::getParam('recipients')) and
			!empty(parent::getParam('from')) and
			function_exists('mail')
		) {
			parent::addHook('ThemeEndBody', 'ThemeEndBody');
			parent::addHook('plxMotorDemarrageNewCommentaire', 'plxMotorDemarrageNewCommentaire');
			parent::addHook('plxMotorAddCommentaire', 'plxMotorAddCommentaire');
			$this->data_filename = PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.__CLASS__.'.json';
		}

	}

	public function save() {
		if(!empty($_POST['recipients']) and !empty($_POST['from'])) {
			$values = array();
			$recipients = explode(' ', self::RECIPIENTS);
			foreach($_POST['recipients'] as $value) {
				if(in_array($value, $recipients)) {
					$values[] = $value;
				}
			}
			if(!empty($values)) {
				parent::setParam('recipients', implode(',', $values), 'string');
				$from = filter_input(INPUT_POST, 'from', FILTER_VALIDATE_EMAIL);
				if(!empty($from)) {
					parent::setParam('from', $from, 'string');
				} else {
					parent::delParam('from');
				}

				foreach(explode(' ', self::NUM_FIELDS) as $field) {
					# cant't be empty. Minimal value is not null.
					parent::setParam($field, self::get_numericField($field, $_POST[$field]), 'numeric');
				}
			} else {
				parent::delParam('recipients');
			}

			parent::saveParams();
		} else {
			plxMsg::Error(parent::getLang('MISSING_RECIPIENT_FROM'));
		}
	}

	/**
	 * Displays an array of checkoxes in config.php for the recipients.
	 * */
	public function  get_recipients() {
		$str1 = parent::getParam('recipients');
		$recipientsValues = (!empty($str1)) ? explode(',', $str1) : false;
		foreach(explode(' ', self::RECIPIENTS) as $name) {
			$caption = parent::getLang(strtoupper($name));
			$checked = (!empty($recipientsValues) and in_array($name, $recipientsValues)) ? ' checked' : '';
			$hint = parent::getLang(strtoupper("{$name}_HINT"));
			echo <<< RECIPIENT
	<div>
		<label for="id_$name">$caption</label>
		<input type="checkbox" name="recipients[]" id="id_$name" value="$name"$checked />
		<a class="hint"><span>$hint</span></a>
	</div>\n
RECIPIENT;
		}
	}

	/**
	 * If $screen strictly equals to true, prints label and input tags for config.php;
	 * else evals $screen as integer and returns its value.
	 * */
	public function get_numericField($field, $screen=true) {
		$limits = array(
			'max_comments'	=> array(5, 100), # min, max counter for unmoderated comments
			'delay'			=> array(1, 168) # hours. 7 days maxi
		);
		$value = ($screen === true) ? parent::getParam($field) : intval($screen);
		if(array_key_exists($field, $limits)) {
			if($value < $limits[$field][0]) { $value = $limits[$field][0]; }
			if($value > $limits[$field][1]) { $value = $limits[$field][1]; }
			if($screen === true) {
				$caption = parent::getLang(strtoupper($field));
				$title = parent::getLang(strtoupper("{$field}_title"));
				echo <<< SCREEN
	<div>
		<label for="id_delay">$caption</label>
		<input type="number" name="$field" id="id_{$field}" value="$value" min="{$limits[$field][0]}" max="{$limits[$field][1]}"/>
		<a class="hint"><span>$title</span></a>
	</div>\n
SCREEN;
				return true;
			} else {
				return $value;
			}
		}
		return false;
	}

	/**
	 * Collects the list of followers 's mail for this article.
	 * Returns an array. May be empty.
	 * */
	private function __getFollowers($artId=false) {
		if(file_exists($this->data_filename)) {
			$followers = json_decode(file_get_contents($this->data_filename), true);
			if(!empty($artId)) {
				return (array_key_exists($artId, $followers)) ? $followers[$artId] : false;
			} else {
				return $followers;
			}
		} else {
			return array();
		}
	}

	private function __updateFollowers($artId, $author=false, $mail=false) {
		$followers = self::__getFollowers();
		if($mail === false) {
			if(!empty($this->newFollower['mail'])) {
				# Add follower
				if(!array_key_exists($artId, $followers)) {
					$followers[$artId] = array();
				}
				$followers[$artId][$mail] = array(
					'name'	=> $this->newFollower['name'],
					'ip'	=> $this->newFollower['ip']
				);
			}
		} elseif(
			!empty($followers[$artId][$mail]) and
			$followers[$artId][$mail]['name'] == $author
		) {
			# Delete follower
			unset($followers[$artId][$mail]);
		} else {
			# Nothing to do. Exit !
			return;
		}
		$content = json_encode($followers, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
		file_put_contents($this->data_filename, $content);
	}

	private function __get_key_encryption() {
		return sha1(date('ldSaF', filectime(__FILE__)));
	}

	/**
	 * Sends mail to the author, and perhaps to the administrator.
	 * Also, to the followers if the comment is published.
	 * */
	public function alertMail(&$plxMotor, $published) {
		$record_arts = $plxMotor->plxRecord_arts;
		$artId = $record_arts->f('numero');

		# anti-spam. Limits alerts count with a lot of comments for moderation
		$comments = $plxMotor->plxGlob_coms->query('@^_\d{4}\..*\.xml$@', 'com', '', 0, false, 'all');
		if(!empty($comments) and count($comments) > intval(parent::getParam('max_comments'))) {
			return;
		}
		# Limits the frequency of alerts
		$relax_time = time() - parent::getParam('delay') * 3600;
		# search the last comment for this article and checks its timestamp.
		$comments = $plxMotor->plxGlob_coms->query('@^_?'.str_pad($artId, 4, '0', STR_PAD_LEFT).'\..*\.xml$@', 'com', 'rsort', 5, false, 'all');
		if(
			!empty($comments) and
			preg_match('@^_?\d{4}\.(\d+)-.*\.xml$@', array_keys($comments)[0], $matches) and
			$matches[1] > $relax_time
		) {
			return;
		}

		$artUrl = $record_arts->f('url');
		$replaces = array(
			'##ART_TITLE##'		=> $record_arts->f('title'),
			'##ART_URL##'		=> $plxMotor->urlRewrite("?article{$artId}/{$artUrl}"),
			'##NAME##'			=> parent::getLang('MY_FRIEND')
		);

		$recipients = explode(',', parent::getParam('recipients')); # may contain admin, author orfollowers.

		$name		= 'Webmaster';
		$from		= parent::getParam('from');
		$subject	= sprintf(parent::getLang('MSG_SUBJECT'), $plxMotor->aConf['title']); # title of the site
		$cc			= (
			in_array('admin', $recipients) and
			!empty($plxMotor->aUsers['001']['email'])
		) ? $plxMotor->aUsers['001']['email'] : false;

		# checks if the author of article has a mail an in $recipients
		if(!empty($plxMotor->aUsers[$record_arts->f('author')]['email']) and in_array('author', $recipients)) {
			$to = $plxMotor->aUsers[$record_arts->f('author')]['email'];
			$replaces['##NAME##'] = $plxMotor->aUsers[$record_arts->f('author')]['name'];
		} else {
			# Avoid sending the mail twice if if author and administrator are the same personn.
			$to = $cc;
			$cc = false;
		}
		if(!empty($to)) {
			# setup the special body of mail for author and administrator
			$body = str_replace(
				array_keys($replaces),
				array_values($replaces),
				parent::getLang('MSG_CONTENT_AUTHOR')
			);
		}

		if(plxUtils::sendMail($name, $from, $to, $subject, $body, 'html', $cc) and in_array('followers', $recipients)) {
			if($published) {
				# Checks if we have followers
				$followers = self::__getFollowers($artId);
				if(!empty($followers)) {
					$key = self::__get_key_encryption();
					$template = parent::getLang('MSG_CONTENT_FOLLOWERS');
					$prefix_url = 'index.php?'. __CLASS__ .'=';
					foreach($followers as $email=>$name) {
						$replaces['##NAME##'] = $name;
						$replaces['##UNSUBSCRIBE_URL##'] = $plxMotor->urlRewrite($prefix_url.mcrypt_encrypt(MCRYPT_BLOWFISH, $key, implode('|', array($artId, $to, $name))));
						# Setup the body of mail for the followers.
						$body = str_replace(array_keys($replaces), array_values($replaces), $template);
						if(!plxUtils::sendMail($name, $from, $email, $subject, $body, 'html') and defined('PLX_ADMIN')) {
							plxMsg::Error('Mail is unavailable');
						}
					}
				}

				# stores author for this comment for next comments.
				if(!empty($this->newFollower)) {
					self::__updateFollowers($artId);
				}
			}
		}
	}

/* =================== Hooks ==================== */

	/**
	 * Checks if removing a subscription is requested.
	 * */
	public function IndexBegin() {
		if(!empty($_GET[__CLASS__])) {
			$key = self::__get_key_encryption();
			$unsubscribe = filter_input('INPUT_GET', __CLASS__, FILTER_SANITIZE_STRING);
			if(!empty($unsubscribe)) {
				$decodeStr = mcrypt_decrypt(MCRYPT_BLOWFISH, $key);
				list($artId, $to, $author) = explode('', $decodeStr);
				self::__updateFollowers($artId, $to, $author, true);
				header('Content-Type: text/plain; charset=UTF-8');
				printf(parent::getLang('SUCCESS_UNSUBSCRIPTION', $author));
				exit;
			}
			header('Location: http://linux.org');
			exit;
		}
	}

	/**
	 * Ajoute une case à cocher dans le formulaire du commentaire d'un article
	 * pour être averti par courriel d'un nouveau commentaire.
	 * */
	public function ThemeEndBody() {
		global $plxMotor;

		if(
			($plxMotor->mode === 'article') and
			!empty($plxMotor->aConf['allow_com']) and
			!empty($plxMotor->plxRecord_arts->f('allow_com')) and
			in_array('followers', explode(',', self::getParam('recipients')))
		) {
			echo str_replace('##SUBSCRIBE##', parent::getLang('SUBSCRIBE'), self::CODE_THEME_END_BODY);
		}
	}

	/**
	 * Stores mail, name adn Ip address of the author of new comment
	 * into $this->newFollower attribute for using with self::alertMail(..)
	 * procedure. Mya be drop with by self::IndexBegin.
	 * */
	public function plxMotorAddCommentaire() {
		if(in_array('followers', explode(',', self::getParam('recipients')))) {
			echo self::CODE_ADD_COMMENT;
		}
	}

	/**
	 * Send an email if the comment is valid.
	 * */
	public function plxMotorDemarrageNewCommentaire() {
		echo self::CODE_NEW_COMMENT;
	}

}
?>