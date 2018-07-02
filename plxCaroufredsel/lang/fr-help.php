<?php if(!defined('PLX_ROOT')) exit; ?>
<h2>Aide pour le plugin Caroufredsel</h2>
<p>Le but de ce plugin est de pouvoir afficher un diaporama dans les articles ou dans les pages statiques.</p>
<h3>Configuration</h3>
<p>Pour afficher un diaporama dans les articles de la page d'accueil, il faut ajouter, dans la page home.php du thème choisi, l'appel du hook suivant :</p>
<pre>
	&lt;?php  eval($plxShow->callHook('caroufredselHome'));?&gt;
</pre>
<p>Pour afficher un diaporama dans un article, il faut ajouter, dans la page article.php du thème choisi, l'appel du hook suivant :</p>
<pre>
	&lt;?php  eval($plxShow->callHook('caroufredselArticle'));?&gt;
</pre>
<p>Pour afficher un diaporama dans une page statique, il faut ajouter, dans la page static.php du thème choisi, l'appel du hook suivant :</p>
<pre>
	&lt;?php  eval($plxShow->callHook('caroufredselStatic'));?&gt;
</pre>
<p>Quelle que soit la page, il faut ajouter le code suivant dans le footer (valable à partir de la version 1.3):</p>
<pre>
	&lt;?php eval($plxShow->callHook('caroufredselFooter'));?&gt;
</pre>
<h3>Activation du diaporama</h3>
<p>Dans la partie administration du site, lorsque vous éditez un article ou une page statique, une case à cocher est disponible sous le formulaire de saisie. Lorsque cette case est cochée, un menu d'options s'affiche. Les modifications sont optionnelles. La largeur et la hauteur du formulaire ne sont pas obligatoires.</p>
<p>Une fois le formulaire enregistré, un message s'affiche en haut de page, indiquant dans quel dossier placer les images.</p>
<h3>Import des images</h3>
<p>Les images sont à importer, une fois que le formulaire de saisie à été validé, dans le dossier automatiquement créé correspondant à l'article.</p>
<p>S'il y a un dossier par utilisateur, le dossier images est créé dans le dossier de l'auteur de l'article. Attention de bien récupérer ce dossier avant toute suppression du compte utilisateur correspondant à l'auteur de l'article.</p>