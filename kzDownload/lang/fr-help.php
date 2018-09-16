<div class="<?php echo $page; ?>-help">
<p>
Ce plugin permet de masquer l'url des fichiers proposés en téléchargement dans un article ou une page statique.
</p>
<p>
Il y a deux utilisations possibles :
</p>
<ul>
<li><p>On sélectionne dans le panneau de configuration, le dossier où sont stockés les fichiers à télécharger.</p>
<p>Dans l'édition de l'article ou de la page statique, on insère un lien vers le fichier à télécharger comme pour n'importe quel fichier.</p>
<p>A l'enregistrement de l'article, le lien sera marqué avec l'attribut <em>data-download</em>.</p>
<p>A l'affichage sur le site, le plugin cryptera automatiquement l'url des fichiers (<em>Valeur de l'attribut href dans la balise &lt;a&gt;) pour tous les liens marqués précèdemment</em>. Il est ensuite possible de changer le dossier pour les autres articles ou pages statiques.</p>
</li>
<li>
Si on veut proposer plusieurs fichiers à télécharger sous la forme d'un joli tableau, on peut insérer dans l'article ou la page statique le code suivant :
</li>
<pre><code>&lt;div class="ma-class" data-download="dossier-des-fichiers-a-telecharger"&gt;
   Emplacement du tableau
&lt;/div&gt;</code></pre>
<p>Avant affichage, le contenu de la balise &lt;div&gt; sera remplacé par un tableau listant le nom des fichiers à télécharger présents dans le dossier précisé par l'attribut <em>data-download</em> avec la valeur de leurs attributs href cryptés. Les tailles et dates de modification sont également précisées.
</p>
<p>
	Le chemin du dossier doit être précisé par rapport au dossier de médias (<em>par défaut: /datas/medias/</em>).
</p>
<p>
En outre, si un fichier <em>.htaccess</em> est présent dans le dossier avec des directives <a href="https://httpd.apache.org/docs/2.4/mod/mod_autoindex.html#adddescription" target="_blank">AddDescription</a>, une description sera ajoutée à chaque fichier concerné.
</p>
</ul>
<p class="kzDownload-warning">
	Si un utilisateur malveillant parvient à installer dans votre dossier de données, <em>data</em>, un fichier PHP malicieux, il pourra l'exécuter sans problème pour récupérer vos données qui l'intéressent.
</p>
<p>Pour éviter ce péril, il suffit de modifier le fichier <strong>.htaccess</strong>, situé dans votre dossier, pour empêcher l'éxecution de tout script PHP comme suit :</p>
<pre><code>options -indexes
&lt;Files "*"&gt;
        SetHandler default-handler
&lt;/Files&gt;
</code></pre>
<p>
Le plugin enregistre  également le nombre total de clics pour chaque url de téléchargement avec un détail hebdomadaire sur une période de trois mois.
</p>
<p>
La popularité des fichiers peut être présentée sous la forme d'un graphique pour l'ensemble de tous les fichiers sur cette période.
</p>
</div>
