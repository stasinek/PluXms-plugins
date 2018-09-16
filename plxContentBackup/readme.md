Plugin de sauvegarde PluXML par l'envoi d'email

Installation
============

Télécharger le plugin, extraire, renommer le dossier en "plxContentBackup" (attention pluxml est sensible à  la casse)

Utilisation
===========

Le plugin sauvegarde les dossiers sélectionnés sur votre serveur selon la fréquence indiquée. Un mail contenant le lien de téléchargement est envoyé après la création de l'archive. Pour les petits envois, il est possible de joindre l'archive au mail.

Historique
=========

Version 1.4 - 03/09/2016
Francois POTEAU - contact: https://github.com/frapfrap

Mise à jour de comptabilité avec Pluxml 5.5
- Bug: Correction bug lien image de suppression des archives lorsque Pluxml est utilisé dans un sous-dossier
- Bug: Correction bug archives zip corrompues
- Modification: Noms du plugin respecte la convention plxContentBackup au lieu de plxcontentbackup
- Modification: remplacement du changelog.txt par un readme.md (github)
- Nouvelle fonctionnalité: L'envoi de l'archive par mail est désormais une option. Par défaut, un mail avec un lien de téléchargement est envoyé. 
- Amélioration: Tri croissant des archives dans l'administration
- Amélioration: Les themes et plugins peuvent être sauvegardés
   
Version 1.3 - 06/02/2011
François POTEAU	- contact: https://github.com/frapfrap

Changements par rapport à la version 1.2

- Mise à jour de config.php, plxcontentbackup.php & styles.css afin de corriger les problèmes d'afficahge liés à la nouvelle administration


Version 1.2 - 07/09/2011
Cyril MAGUIRE - contact: ecyseo.com

Changements par rapport à la version 1.1

- Télécharger une archive sans être redirigé vers le dossier qui la contient
- Affichage d'un lien vers le panneau de configuration du plugin dans le menu principal de l'administration de pluxml (celui de gauche)
- Possibilité de supprimer les archives devenues obsolètes


Version 1.1 - 07/06/2011
François POTEAU - contact: https://github.com/frapfrap


Changements par rapport à la version 1

- Comptabilité avec le plugins de sauvegarde FTP
- Mise à jour des fichiers de description et de langue
- Utilisation de la variable aConf de Plxadmin pour selectionner les dossiers à sauvegarder 
- Autres changements mineurs (css)

