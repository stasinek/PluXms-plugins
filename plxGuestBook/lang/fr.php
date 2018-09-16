<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

$LANG = array(

'L_PAGE_TITLE'					=> 'Livre d\'or',

# config.php
'L_GB_MAIL_AVAILABLE'			=> 'Fonction d\'envoi de mail disponible',
'L_GB_MAIL_NOT_AVAILABLE'		=> 'Fonction d\'envoi de mail non disponible',

'L_GB_MENU_DISPLAY'				=> 'Afficher le menu de la page de contact',
'L_GB_MENU_TITLE'		    	=> 'Titre du menu',
'L_GB_MENU_TEXT'		    	=> 'Texte à insérer au dessus du formulaire',
'L_GB_MENU_POS'		    		=> 'Position du menu',
'L_GB_EMAIL'		   			=> 'Destinataire(s) du mail *',
'L_GB_EMAIL_SUBJECT'  			=> 'Objet du mail',
'L_GB_THANKYOU_MESSAGE'			=> 'Message de remerciement',
'L_GB_TEMPLATE'					=> 'Template',
'L_GB_CAPTCHA'					=> 'Activer le captcha anti-spam',
'L_GB_MOD'						=> 'Modérer les messages à la création',
'L_GB_BYPAGE'					=> 'Nombre de messages affichés par page',
'L_GB_BYPAGE_ADMIN'				=> 'Nombre de messages affichés par page dans l\'administration',
'L_GB_TRI_GB'					=> 'Tri des messages',
'L_GB_SORT'						=> 'date décroissante',
'L_GB_RSORT'					=> 'date croissante',
'L_GB_SAVE'						=> 'Enregistrer',
'L_GB_COMMA'					=> '* séparer les adresses emails des destinataires par une virgule',
'L_GB_SUPERVISION_EMAIL'		=> 'Email supervision publication livre d\'or', 
'L_GB_TAB01'					=> 'Général',
'L_GB_TAB02'					=> 'Présentation',
'L_GB_TAB03'					=> 'Mise à jour',

# admin.php
'L_GB_ALL'						=> 'Tous',
'L_GB_ONLINE'					=> 'En ligne',
'L_GB_OFFLINE'					=> 'Hors Ligne',
'L_GB_SET_ONLINE'				=> 'Mettre en ligne',
'L_GB_SET_OFFLINE'				=> 'Mettre hors ligne',
'L_GB_DELETE'					=> 'Supprimer',
'L_GB_FOR_SELECTION' 			=> 'Pour la sélection...',
'L_GB_ONLINE_LIST'				=> 'Liste des messages publiés',
'L_GB_OFFLINE_LIST'				=> 'Liste des messages en attente de validation',
'L_GB_ALL_LIST'					=> 'Liste des messages',
'L_GB_EDIT'						=> 'Éditer',
'L_GB_ID'						=> 'ID',
'L_GB_EDIT_TITLE'				=> 'Éditer ce message',
'L_GB_NO_MESSAGE' 				=> 'aucun message',
'L_GB_ARTICLE_LINKED'			=> 'Article',
'L_GB_ARTICLE_LINKED_TITLE'		=> 'Article attaché à ce message',
'L_GB_OFFLINE' 					=> 'Hors ligne',
'L_GB_ONLINE'					=> 'En ligne',
'L_GB_ONLINE_TITLE'				=> 'Visualiser ce message en ligne',
'L_GB_BACK_TO_MESSAGES'    		=> 'Retour à la liste des messages',
'L_GB_EDITING' 					=> 'Édition d\'un message',
'L_GB_AUTHOR_FIELD'				=> 'Auteur',
'L_GB_TYPE_FIELD'				=> 'Type de message',
'L_GB_DATE_FIELD'				=> 'Date et heure du message',
'L_GB_IP_FIELD'					=> 'Ip',
'L_GB_SITE_FIELD'				=> 'Site',
'L_GB_EMAIL_FIELD'				=> 'E-mail',
'L_GB_STATUS_FIELD'				=> 'Statut',
'L_GB_MESSAGE_FIELD'			=> 'Message',
'L_GB_DELETE_CONFIRM'			=> 'Supprimer ce message ?',
'L_GB_PUBLISH_BUTTON'			=> 'Valider le message',
'L_GB_OFFLINE_BUTTON'			=> 'Mettre hors ligne',
'L_GB_UPDATE_BUTTON'			=> 'Mettre à jour',
'L_GB_WRITTEN_BY'				=> 'Rédigé par',
'L_GB_LIST_ID'					=> 'ID',
'L_GB_LIST_DATE'				=> 'Date',
'L_GB_LIST_AUTHOR'				=> 'Auteur',
'L_GB_LIST_MESSAGE'				=> 'Message',
'L_GB_LIST_ACTION'				=> 'Action',
'L_GB_VIEW_EMAIL'				=> 'Montrer l\'adresse email',

'L_GB_DEFAULT_MENU_NAME'		=> 'Livre d\'or',
'L_GB_DEFAULT_OBJECT'			=> '## PluXml ## Publication dans le livre d\'or',
'L_GB_DEFAULT_THANKYOU'			=> 'Merci de votre contribution.',

'L_GB_ERROR_EMAIL'				=> 'Veuillez saisir une adresse email valide',

# form.guestbook.php
'L_GB_MSG_WELCOME'				=> 'Je vous remercie de prendre le temps d\'écrire ce message.',
'L_GB_ERR_AUTHOR'				=> 'Veuillez saisir votre nom',
'L_GB_ERR_EMAIL'				=> 'Veuillez saisir une adresse email valide',
'L_GB_ERR_CONTENT'				=> 'Veuillez saisir le contenu de votre message',
'L_GB_ERR_ANTISPAM'				=> 'La v&eacute;rification anti-spam a échoué',
'L_GB_ERR_SENDMAIL'				=> 'Une erreur est survenue pendant l\'envoi de votre message',
'L_GB_ERR_SITE'					=> 'Veuillez saisir une adresse de site valide',

'L_GB_FORM_AUTHOR'				=> 'Votre nom (ou pseudo)',
'L_GB_FORM_MAIL'				=> 'Votre adresse email',
'L_GB_FORM_WEBSITE'				=> 'Votre site web',
'L_GB_PLACEHOLDER_SITE'			=> 'http://',
'L_GB_FORM_VIEW_MAIL'			=> 'Montrer aux visiteurs votre adresse email',
'L_GB_FORM_ANTISPAM_INFO'		=> 'Note : l\'adresse email utilisée est protégée contre le SPAM.',
'L_GB_FORM_CONTENT'				=> 'Le contenu de votre message',
'L_GB_FORM_ANTISPAM'			=> 'Vérification anti-spam',
'L_GB_FORM_BTN_SEND'			=> 'Envoyer',
'L_GB_FORM_BTN_SENDTO'			=> 'Poster un message',
'L_GB_FORM_MESSAGE'				=> 'Message',
'L_GB_FORM_NO_POST'				=> 'Aucun message',
'L_GB_FORM_MOD'					=> 'Le message est en cours de modération par l\'administrateur de ce site',
'L_GB_FORM_BY'					=> 'Par',
'L_GB_FORM_THE'					=> 'le',
'L_GB_FORM_CORPS_1'				=> 'Publication dans le livre d\'or, le',
'L_GB_FORM_CORPS_2'				=> 'Soumission de',
'L_GB_FORM_CORPS_3'				=> 'Son site web',
'L_GB_FORM_CORPS_4'				=> 'Message',
'L_GB_FORM_CORPS_5'				=> 'Message en attente de modération',
'L_GB_FORM_CORPS_6'				=> 'Accès à l\'administration',
'L_GB_FORM_CORPS_7'				=> 'Gerer les messages',
'L_GB_FORM_TEXT'				=> 'Texte à insérer au dessus de "Poster un message"',

# Update plugin
'L_VP_ACTUAL_VERSION'			=> 'Version actuelle',
'L_VP_LAST_VERSION'				=> 'Vous avez la dernière version du plugin',
'L_VP_NEW_VERSION'				=> 'Une nouvelle version du plugin',
'L_VP_NEW2_VERSION'				=> 'est disponible sur le site dpfpic.com',
'L_VP_ERROR'					=> 'Impossible de voir le statut, une erreur à été rencontrée',
'L_VP_DESACTIVED'				=> 'La mise à jour à été désactivée'
);
?>
