<h2 class="kzGallery">Afficher des galeries photos dans un article</h2>
<p>Au préalable, déposer les photos de la galerie dans un dossier en utilisant le gestionnaire de médias et noter le chemin du dossier ou cliquer sur le bouton copier pour recopier le chemin du dossier dans le presse-papier et le stocker localement pour la session en cours (<em>sessionStorage</em>).</p>
<p>Lorsqu'on dépose les photos pour la galerie, ne pas oublier de créer les miniatures, de préférence avec la même hauteur.</p>
<p>Il n'est pas possible d'utiliser le dossier racine des médias pour les galeries.</p>
<p>Il faut ensuite se rendre dans la page d'édition d'un article et choisir une des 2 solutions suivantes :</p>
<ol>
	<li>Sélectionner en bas de la page dans la liste déroulante le dossier contenant les photos ou cliquer sur le bouton coller. Enregistrer l'article ensuite. Par cette méthode, on ne peut afficher qu'une galerie par article.</li>
	<li><p>Aller dans la page des médias. Sélectionner le dossier pour la galerie qui doit contenir des miniatures. Cliquer sur le bouton "Copier" pour copier dans le presse-papier le code HTML à insérer dans l'article.</p>
	<p>Editer l'article ou la page statique et coller dans le code source HTML le nouveau code copié précèdemment dans le presse-papier.</p>
	<p>Eventuellement, on peut créer directement le code HTML dans le code source de l'article ou la page statique, sous la forme suivante :</p>
		<pre class="kzGallery"><code>&ltdiv data-gallery="chemin-vers-dossier-images/"&gtMes photos&lt;div&gt;</code></pre>
		<p>Il est possible d'ajouter d'autres attributs à la balise &lt;div&gt; comme <em>class</em> par exemple.</p>
		<p>A l'affichage de la page côté site, le contenu de la balise &lt;div&gt; sera remplacé par la galerie photos générée par le plugin.</p>
		<p>Le chemin du dossier est le chemin relatif par rapport au <strong>dossier médias</strong></p>
	</li>
</ol>
<p>Si l'article n'a pas de chapo, le corps de l'article et donc la galerie seront affichés sur la page d'accueil. Attention au temps de téléchargement de la page s'il y a beaucoup de photos. Dans ce cas, il est conseillé d'ajouter un chapo à l'article pour éviter l'affichage de son corps.</p>
<p>il n'est pas possible d'ajouter une galerie photos dans le chapo de l'article.</p>
<h2 class="kzGallery">Afficher des galeries photos dans une page statique</h2>
<ol>
	<li>Procéder comme précèdemment en éditant le code source de la page statique.</li>
</ol>
<h2 class="kzGallery">Configuration</h2>
<p>Dans la configuration par défaut, le plugin utilise la librairie Javascript Lightbox2 pour afficher le diaporama de la galerie.</p>
<p>Un titre basé sur le nom du fichier est ajouté à chaque photo.</p>
<p>Chaque vignette de photo est encapsulée dans une balise &lt;a&gt; précisant l'adresse de la photo en vraie grandeur. Chaque balise contient un attribut <strong>data-lightbox</strong> pour définir un groupe de photos. Il est ainsi possible d'avoir plusieurs galeries dans un article.</p>
<p>Ces trois options sont désactivables pour réduire le contenu de la galerie à une suite de balises &lt;img&gt; contenant chaque vignette.</p>
<p>En désactivant Lightbox2, il est possible d'utiliser un autre plugin pour afficher le diaporama de la galerie. Utiliser <em>div[data-gallery]</em> comme sélecteur CSS.</p>
