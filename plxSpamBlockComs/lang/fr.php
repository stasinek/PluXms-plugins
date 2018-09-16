<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

	$LANG = array(
		# config.php
		'L_SBC_MAIL_AVAILABLE'			=> 'Fonction d\'envoi de mail disponible',
		'L_SBC_MAIL_NOT_AVAILABLE'		=> 'Fonction d\'envoi de mail non disponible',
		'L_SBC_EMAIL_NEW_COMMENT'		=> 'Nouveau commentaire :',
		'L_SBC_EMAIL_NEW_COMMENT_BY'	=> 'Nouveau commentaire de',
		'L_SBC_EMAIL_READ_ONLINE'		=> 'Lire le commentaire en ligne ici ',	
		'L_SBC_EMAIL_MANAGE_COMMENTS'	=> 'Gérer les commentaires',
		'L_SBC_EMAIL_DIRECT_ANSWER'		=> 'Répondre à ce commentaire',
		'L_SBC_EMAIL_READ_ONLINE'		=> 'Lire le commentaire en ligne ici',		
		'L_SBC_HELP_COMMAS'				=> 'Séparer les adresses emails par des virgules', 
		'L_SBC_SENDER_FROM' 			=> 'Adresse email utilisée pour envoyer les mails',
		'L_SBC_SENDER_TO'				=> 'Destinataires du mail',
		'L_SBC_SUPERVISION'				=> 'Envoi d\'un mail lors d\'un nouveau commentaire',
		'L_SBC_USE_WL'					=> 'Utiliser la WhiteList',
		'L_SBC_USE_BL'					=> 'Utiliser la BlackList',
		'L_SBC_HELP_WL'		 			=> 'WhiteList : Saisir Auteur, E-mail, Url site ou IP - 1 par ligne',
		'L_SBC_HELP_BL'					=> 'BlackList : Saisir Auteur, E-mail, Url site ou IP - 1 par ligne',
		'L_SBC_HELP_EMAIL'				=> 'Par default récupère le email de l\'administrateur',
		'L_SBC_COM_WL'					=> 'Lors d\'un ajout à la White List via la page d\'administration des commentaires, ajouter',
		'L_SBC_COM_BL'					=> 'Lorsqu\'un spammeur est identifié par le plugin ou lors d\'un ajout à la Black List via la page d\'administration des commentaires, ajouter',
		'L_SBC_AUTOBLTIMER'				=> 'Temps (en secondes) au dessous duquel un commentaire est refusé/blacklisté automatiquement (0 pour désactiver)',
		'L_SBC_FORCEMODERATION'			=> 'Appliquer les contrôles même si le commentaire est posté sans site',
		'L_SBC_ADMINTEXT'				=> 'Utiliser des icones d\'action sur la page admin des commentaires',
		'L_SBC_SAVEMODE'				=> 'Lorsqu\'un SPAM est détecté par le plugin',
		'L_SBC_TAGSPAM'					=> 'Deplacer le commentaire dans la liste des SPAM',
		'L_SBC_TAGSPAM'					=> 'Le déplacer vers la liste des SPAM',
		'L_SBC_SPAM'					=> 'Ajouter le visiteur en Black liste et déplacer le commentaire dans la liste des SPAM',
		'l_SBC_MODSPAN'					=> 'Le tagger comme SPAM et le mettre en modération',
		'L_SBC_REJECTSPAM'				=> 'Rejeter le message',
		'L_SBC_ADDADMINICONES'			=> 'Accès à l\'administration rapide sur les commentaires',
		'L_SBC_IP'						=> 'IP',
		'L_SBC_EMAIL'					=> 'Email',
		'L_SBC_SITE'					=> 'Site',		
		'L_SBC_SAUVE'					=> 'Enregistrer',
		'L_SBC_ERR_EMAIL'				=> 'Veuillez saisir une adresse email valide',	
		'L_SBC_LIST_SPAM'				=> 'Liste des SPAMS',
		'L_SBC_TAB01'					=> 'Général',
		'L_SBC_TAB02'					=> 'Listes blanche et noire',
		'L_SBC_TAB03'					=> 'Mise à jour',

		'L_SBC_SPAMTAG'					=> 'Tag du SPAM',
		'L_SBC_ADD_WL'					=> 'Ajouter WL',
		'L_SBC_TITLE_WL'				=> 'Ajouter le visiteur en White liste et mettre le commentaire en ligne',
		'L_SBC_TITLE_WL_FO'				=> 'Ajouter le visiteur en White liste',
		'L_SBC_CONFIRM_WL'				=> 'Confirmer la mise en White List ?',
		'L_SBC_ADD_BL'					=> 'Ajouter WL',
		'L_SBC_TITLE_BL'				=> 'Ajouter le visiteur en Black liste et',
		'L_SBC_TITLE_BL_MOD'			=> 'modérer',
		'L_SBC_TITLE_BL_SUPP'			=> 'supprimer',		
		'L_SBC_CONFIRM_BL'				=> 'Confirmer la mise en Black List ?',
		'L_SBC_TO_CONFIG'				=> 'Configurer SpamBlockComs',
		'L_SBC_TITLE_CONFIG'			=> 'Modifier manuellement WhiteList et BlackList ou accéder aux options du plugin',
		'L_SBC_TITLE_COM'				=> 'le commentaire',
		'L_SBC_SUPP_COM'				=> 'Supprimer le commentaire',
		'L_SBC_EDIT_COM'				=> 'Editer ce commentaire',
		'L_SBC_REPLY'					=> 'Répondre au commentaire',
		'L_SBC_ADDSPAM'					=> 'Mise en SPAM effectuée avec succès',
		'L_SBC_NEWCOMMENT_ERR'			=> 'Une erreur s\'est produite lors de la publication de ce commentaire',
		'L_SBC_COM_IN_MODERATION'		=> 'Le commentaire est en cours de modération par l\'administrateur de ce site',

		# Update plugin

		'L_VP_ACTUAL_VERSION'			=> 'Version actuelle',
		'L_VP_LAST_VERSION'				=> 'Vous avez la dernière version du plugin',
		'L_VP_NEW_VERSION'				=> 'Une nouvelle version du plugin',
		'L_VP_NEW2_VERSION'				=> 'est disponible sur le site dpfpic.com',
		'L_VP_ERROR'					=> 'Impossible de voir le statut, une erreur à été rencontrée',
		'L_VP_DESACTIVED'				=> 'La mise à jour à été désactivée'

	);

?>
