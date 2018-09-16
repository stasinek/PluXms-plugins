<?php if(!defined('PLX_ROOT')) exit; ?>
<h2>Aide pour le plugin NewArtsComs</h2>
<p><strong>Ce plugin permet d'afficher</strong> : <br />
    - Une mention pendant x jours pour les nouveaux articles<br />
	- Une mention pendant x jours pour les mise à jour articles<br />
	- Une mention pendant x jours pour les nouveaux commentaires</p>

<h3>Utilisation</h3>
<p>
<strong>Pour afficher une icone nouveau/mise à jour article</strong> : <br />
Éditez le fichier <strong>home.php</strong> de votre thème, ajouter la ligne suivante :<br />
<pre><div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
<?php echo plxUtils::strCheck('<?php eval($plxShow->callHook("plxShowNewArts")) ?>') ?>
</div></pre>
à coté de la ligne suivante :
<pre style="font-size:14px; padding-left:40px; color:green">
<?php echo plxUtils::strCheck('<?php $plxShow->artTitle(\'link\'); ?>') ?>
</pre>
</p>
<p>
<strong>Pour afficher une icone nouveau commentaire</strong> : <br />
Éditez le fichier <strong>commentaires.php</strong> de votre thème, ajouter la ligne suivante :<br />
<pre><div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
<?php echo plxUtils::strCheck('<?php eval($plxShow->callHook("plxShowNewComs")) ?>') ?>
</div></pre>
à coté de la ligne suivante :
<pre style="font-size:14px; padding-left:40px; color:green">
<?php echo plxUtils::strCheck('<div id="<?php $plxShow->comId(); ?>" class="comment">') ?>
</pre>
</p>
