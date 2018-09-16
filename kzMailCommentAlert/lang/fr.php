<?php

const KZ_MAIL_COMMENT_MSG_CONTENT_AUTHOR = <<< MSG1
<html><head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head><body style="font: sans-serif 12pt;">
<p>Bonjour ##NAME##,</p>
<p>
Un nouveau commentaire a été posté pour l'article suivant :<br />
<a href="##ART_URL##">##ART_TITLE##</a>
</p>
<div><p>
Cordialement,<br />
Le Webmaster
</p></div>
</body></html>
MSG1;

const KZ_MAIL_COMMENT_MSG_CONTENT_FOLLOWERS = <<< MSG2
<html><head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head><body style="font: sans-serif 12pt;">
<p>Bonjour ##NAME##,</p>
<p>Un Nouveau commentaire a été posté pour l'article :<br />
<a href="##ART_URL##">##ART_TITLE##</a></p>
<p><em>Pour ne plus recevoir de courriel pour cet article, veuillez cliquer sur le lien suivant :
<a href="##UNSUBSCRIBE##">##UNSUBSCRIBE_URL##</a></em></p>
<div><p>
Cordialement,<br />
Le Webmaster
</p></div>
</body></html>
</p>
MSG2;

$LANG = array(
	'ADMIN'					=> 'Administrateur',
	'ADMIN_HINT'			=> 'Reçoit les alertes en copie carbone s\'il n\'est pas l\'auteur du commentaire',
	'AUTHOR'				=> 'Auteur de l\'article',
	'AUTHOR_HINT'			=> 'Si l\'auteur et l\'administrateur sont une même personne, alors une seule alerte sera envoyée',
	'FOLLOWERS'				=> 'Abonnés aux commentaires',
	'FOLLOWERS_HINT'		=> 'L\'alerte leur sera envoyée uniquement lorsque le commentaire sera publié',
	'FROM'					=> 'Adresse de l\'expéditeur',
	'MAX_COMMENTS'			=> 'Nbre maxi de commentaires',
	'MAX_COMMENTS_TITLE'	=> 'Lorsque le nombre de commentaires à modérer dépasse ce seuil, l\'envoi des alertes par courriel est suspendu (Protection anti-spam).',
	'DELAY'					=> 'Délai en heures',
	'DELAY_TITLE'			=> 'Délai minimum entre 2 alertes pour un article',
	'RECIPIENTS_TITLE'		=> 'A chaque commentaire posté, envoyer un courriel à',
	'SAVE'					=> 'Enregistrer',
	'MSG_SUBJECT'			=> '[%s] Un nouveau commentaire a été posté',
	'MSG_CONTENT_AUTHOR'	=> KZ_MAIL_COMMENT_MSG_CONTENT_AUTHOR,
	'MSG_CONTENT_FOLLOWERS'	=> KZ_MAIL_COMMENT_MSG_CONTENT_FOLLOWERS,
	'SUBSCRIBE'				=> 'Etre alerté pour un nouveau commentaire, par courriel',
	'MY_FRIEND'				=> 'Webmaster',
	'SUCCESS_UNSUBSCRIPTION'=> 'L\'auteur %s ne recevra plus d\'alerte pour les prochains commentaires.',
	'MISSING_RECIPIENT_FROM'=> 'Précisez au moins un destinataire et l\'adresse de l\'expéditeur'
);
?>