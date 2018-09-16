<?php if(!defined('PLX_ROOT')) exit; ?>
<h1>

    Static Galerie

</h1><h2>

    Introduction

</h2><p>

    Static Galerie vous permet de créer autant de galerie que vous souhaitez que vous pouvez imbriquer afin de créer une galerie principal et de sous-galeries. Une fois configurer chaque galeries devient une page statique PluXml. Ce plugin ce base sur le gestionnaire de médias de PluXml pour gérer les répertoires et images de vos galeries.

</p><h2>

    Configuration

</h2><p>

    La configuration ce passe en deux étapes :

</p><ul>

    <li>

        <p>

            Définir la configuration par défaut;

        </p>

    </li><li>

        <p>

            Définir la configuration par galeries.

        </p>

    </li>

</ul><h3>

    Configuration par défaut

</h3><p>

    Cette partie est à faire une seule fois après l'installation du plugin. Certains paramètres sont commun à toute les galeries donc autant les définir une seul fois pour simplifier le processus de création des galeries. Pour accéder depuis la page de gestion des plugins cliquer sur "Configuration".

</p><ul>

    <li>

        <p>

            Liste des extensions séparés par une virgule : liste des extensions qui seront visible dans vos galeries, penser aux majuscules et minuscules. Chaque extensions devra être séparé par une virgule;

        </p>

    </li><li>

        <p>

            Ordre de trie avec les noms de fichiers : Définit dans quel ordre seront affichés les vignettes de vos galeries en ce basant sur les noms de fichiers;

        </p>

    </li><li>

        <p>

            Activer le file d’Ariane : Affiche ou non une hiérarchie permettant aux visiteurs de naviguer dans vos galeries;

        </p>

    </li><li>

        <p>

            Séparateur du file d'Ariane : définie de séparateur entre chaque élément du fil d’Ariane.

        </p>

    </li>

</ul><h3>

    Créer une galerie

</h3><p>

    La gestion des galeries reprend le principe des pages statiques de PluXml.

</p><ul>

    <li>

        <p>

            Identifiant : numéro unique de votre galerie;

        </p>

    </li><li>

        <p>

            Utilisateur : utilisateur PluXml qui a créé la galerie;

        </p>

    </li><li>

        <p>

            Galerie : nom interne de la galerie, ce nom sera utilisé afin de créer l'url pour accéder à la galerie;

        </p>

    </li><li>

        <p>

            Active : si oui la galerie est accessible, si non elle n'est pas accessible;

        </p>

    </li><li>

        <p>

            Menu : si affiché elle sera visible dans la barre de menu comme une page statique.

        </p>

    </li>

</ul><p>

    Remplir le champ Galerie par "Test 1" et choisissez "Oui" dans le menu active et cliquer sur le bouton "Modifier la liste des galeries". La galerie est créé maintenant il va falloir la configurer, cliquer sur le lien "Éditer" dans la colonne "Action". Sous la liste des galeries apparait un formulaire reprenant les paramètres par défaut que vous avez défini et certains paramètres présent lors de la création de la galerie.

</p><ul>

    <li>

        <p>

            Active : si oui la galerie est accessible, si non elle n'est pas accessible;

        </p>

    </li><li>

        <p>

            Galerie principal : définit si votre galerie est une catégorie parente, nous verront par suite l'utilité;

        </p>

    </li><li>

        <p>

            Galerie parente : définit quelle galerie est la parente de celle que vous configurer, nous verront par suite l'utilité;

        </p>

    </li><li>

        <p>

            Afficher dans le menu : si oui elle sera visible dans la barre de menu comme une page statique;

        </p>

    </li><li>

        <p>

            Répertoire de base de votre galerie [...] : nom du répertoire qui va contenir vos images, il peut être créé par avance via le gestionnaire de médias de PluXml et contenir des images. Si vous avez ajouter les images par un autre moyen penser à créer les miniatures. La fonction "Utiliser un dossier images et documents différent pour chaque utilisateur" de PluXml est automatiquement géré;

        </p>

    </li><li>

        <p>

            Liste des extensions séparés par une virgule : liste des extensions des images qui seront affichées dans la galerie. Penser aux majuscules/minuscules;

        </p>

    </li><li>

        <p>

            Ordre de trie avec les noms de fichiers : définit l'ordre d'affichage des images par rapport au nom des fichiers;

        </p>

    </li><li>

        <p>

            Galerie privé : si oui la galerie sera protégée par un mot de passe;

        </p>

    </li><li>

        <p>

            Mot de passe de la galerie : mot de passe utilisé dans le cas d'une galerie privée;

        </p>

    </li><li>

        <p>

            Titre de la page de votre galerie : nom qui sera utilisé pour afficher l'entrée dans le menu des pages statiques;

        </p>

    </li><li>

        <p>

            Position de votre galerie dans le menu : indiquer un chiffre pour définir la position de votre galerie dans le menu des pages statiques;

        </p>

    </li><li>

        <p>

            Activer le file d’Ariane : affiche ou non une hiérarchie permettant aux visiteurs de naviguer dans vos galeries;

        </p>

    </li><li>

        <p>

            Séparateur du file d’Ariane : définie de séparateur entre chaque élément du fil d’Ariane;

        </p>

    </li><li>

        <p>

            Modèle : pour le moment cette fonction n'est pas active;

        </p>

    </li><li>

        <p>

            Représentante de votre galerie : une fois les paramètres enregistrés vous pourrez définir une image de la galerie comme représentante. Utilisé dans le cas des galeries parentes;

        </p>

    </li><li>

        <p>

            Afficher le nom des images : affiche sous la vignette le nom de la galerie ou le nom du fichier;

        </p>

    </li><li>

        <p>

            Texte accompagnant votre galerie : texte libre qui sera visible au dessus de votre galerie.

        </p>

    </li>

</ul><p>

    Pour l'exemple remplissez les champs avec ces valeurs :

</p><ul>

    <li>

        <p>

            Active : oui

        </p>

    </li><li>

        <p>

            Galerie principal : non

        </p>

    </li><li>

        <p>

            Galerie parente : laisser vide

        </p>

    </li><li>

        <p>

            Afficher dans le menu : oui

        </p>

    </li><li>

        <p>

            Répertoire de base de votre galerie [...] : test-1

        </p>

    </li><li>

        <p>

            Liste des extensions séparés par une virgule : jpg,JPG,png,PNG,gif,GIF,bmp,BMP

        </p>

    </li><li>

        <p>

            Ordre de trie avec les noms de fichiers : Croissant

        </p>

    </li><li>

        <p>

            Galerie privé : non

        </p>

    </li><li>

        <p>

            Mot de passe de la galerie : laisser vide

        </p>

    </li><li>

        <p>

            Titre de la page de votre galerie : Test 1

        </p>

    </li><li>

        <p>

            Position de votre galerie dans le menu : 2

        </p>

    </li><li>

        <p>

            Activer le file d’Ariane : oui

        </p>

    </li><li>

        <p>

            Séparateur du file d’Ariane :&nbsp; | 

        </p>

    </li><li>

        <p>

            Modèle : pour le moment cette fonction n'est pas active

        </p>

    </li><li>

        <p>

            Représentante de votre galerie : rien pour le moment

        </p>

    </li><li>

        <p>

            Afficher le nom des images : non

        </p>

    </li><li>

        <p>

            Texte accompagnant votre galerie : Un petit texte pour voir ce que ça donne une galerie avec du texte ;)

        </p>

    </li>

</ul><p>

    Cliquer sur enregistrer, vous revenez à la liste des galeries. Dans le menu de gauche cliquer sur le lien "Médias", dans la liste "Dossier" doit apparaitre un répertoire test-1. Sélectionner le et cliquer sur "Ok". Ajouter des images en cliquant sur le bouton "Ajouter fichiers". Pour voir votre galerie aller sur la page d'accueil de votre site et dans la liste des pages statiques doit apparaitre "Test 1", cliquer dessus pour voir le résultat.

</p><h3>

    Créer des sous-galeries

</h3><p>

    Nous allons voir comment créer une galerie principal et une sous-galerie. Dans l'administration de votre site cliquer sur le lien "Static Galeries", dans la liste des galeries cliquer sur le lien "Éditer" de la galerie "Test 1". Positionner à "Oui" le champ "Galerie principal" et cliquer sur "Enregistrer". Créer une nouvelle galerie que vous nommerez "Test 2", cliquer sur le lien "Éditer" de la galerie "Test 2" et remplissez les champs avec ces valeurs :

</p><ul>

    <li>

        <p>

            Active : oui

        </p>

    </li><li>

        <p>

            Galerie principal : non

        </p>

    </li><li>

        <p>

            Galerie parente : Test 1

        </p>

    </li><li>

        <p>

            Afficher dans le menu : non

        </p>

    </li><li>

        <p>

            Répertoire de base de votre galerie [...] : test-2

        </p>

    </li><li>

        <p>

            Liste des extensions séparés par une virgule : jpg,JPG,png,PNG,gif,GIF,bmp,BMP

        </p>

    </li><li>

        <p>

            Ordre de trie avec les noms de fichiers : Croissant

        </p>

    </li><li>

        <p>

            Galerie privé : non

        </p>

    </li><li>

        <p>

            Mot de passe de la galerie : laisser vide

        </p>

    </li><li>

        <p>

            Titre de la page de votre galerie : Test 2

        </p>

    </li><li>

        <p>

            Position de votre galerie dans le menu : 2

        </p>

    </li><li>

        <p>

            Activer le file d’Ariane : oui

        </p>

    </li><li>

        <p>

            Séparateur du file d’Ariane :&nbsp; | 

        </p>

    </li><li>

        <p>

            Modèle : pour le moment cette fonction n'est pas active

        </p>

    </li><li>

        <p>

            Représentante de votre galerie : rien pour le moment

        </p>

    </li><li>

        <p>

            Afficher le nom des images : non

        </p>

    </li><li>

        <p>

            Texte accompagnant votre galerie : Texte de la sous galerie

        </p>

    </li>

</ul><p>

    Cliquer sur enregistrer, ajouter des images par le gestionnaire de médias dans le répertoire test-2. Éditer de nouveau la galerie "Test 2", dans la liste déroulante "Représentante de votre galerie" sélectionner la vignette qui sera sa représentante lorsque vous visiterez la galerie principale, cliquer sur enregistrer. Pour voir votre galerie aller sur la page d'accueil de votre site et dans la liste des pages statiques doit apparaitre "Test 1", cliquer dessus pour voir le résultat.

</p><h3>

    Galerie privée

</h3><p>

    Il est possible de protéger une galerie principal ou non par un mot de passe. Éditer la galerie que vous souhaitez protéger, dans la liste "Galerie privé" choisissez "Oui" et dans le champ "Mot de passe de la galerie" taper le mot de passe de votre galerie. Ce mot de passe sera demander aux personnes visitant cette galerie.

</p><h2>

    Version et changement

</h2><p>

    Version 0.5 - 03/06/2012

</p><ul>

    <li>

        <p>

            Compatible avec PluXml 5.1.6;

        </p>

    </li><li>

        <p>

            Gestion des galeries parentes et sous-galeries;

        </p>

    </li><li>

        <p>

            Gestion des représentantes pour les sous-galeries;

        </p>

    </li><li>

        <p>

            Rédaction d'une documentation;

        </p>

    </li><li>

        <p>

            Correction problème "Accès interdit" sur configuration du plugin;

        </p>

    </li><li>

        <p>

            Correction de la gestion des messages d'erreur dans l'édition d'une galerie.

        </p>

    </li>

</ul><p>

    Version 0.4 - 08/02/2012

</p><ul>

    <li>

        <p>

            Contrôle que le répertoire de la galerie appartient à l'arborescence des images;

        </p>

    </li><li>

        <p>

            Ajout d'un fil d'Ariane (visible ou non);

        </p>

    </li><li>

        <p>

            Correction du fichier langue.

        </p>

    </li>

</ul><p>

    Version 0.3 - 24/01/2012<br />

</p><ul>

    <li>

        <p>

            Validation xhtml;

        </p>

    </li><li>

        <p>

            Possibilité de définir un mot de passe par galerie;

        </p>

    </li><li>

        <p>

            Chargement uniquement les galeries non supprimés;

        </p>

    </li><li>

        <p>

            Ajout d'une icône pour les répertoires.

        </p>

    </li>

</ul><p>

</p><p>

    Version 0.2 - 17/01/2012<br />

</p><ul>

    <li>

        <p>

            Changement de nom du répertoire et de la classe pour ne pas confondre plxgaleries avec plxgalerie;

        </p>

    </li><li>

        <p>

            Correction du bug d'enregistrement des galeries;

        </p>

    </li><li>

        <p>

            Correction du bug de caractères dans le champ nom;

        </p>

    </li><li>

        <p>

            Gestion des sous-répertoires.

        </p>

    </li>

</ul><p>

</p><p>

    Version 0.1 - 14/01/2012

</p><ul>

    <li>

        <p>

            Version initial pour PluXml 5.1.5.

        </p>

    </li>

</ul>
