<?php if(!defined('PLX_ROOT')) exit; ?>
<h2>Aide pour le plugin DonsPayPal</h2>
<p>Ce plugin affiche un bouton PayPal pour faire un don.</p>
<br />

<h3>Utilisation</h3>
<p>
Ajouter la ligne suivante dans votre thème (fichier sidebar.php) à l'endroit où vous voulez afficher le bouton Paypal:
</p>
<pre style="font-size:12px; padding-left:40px; color:green">
&lt;?php eval($plxShow->callHook('plxShowDonsPayPal')) ?&gt;
</pre>

<p>Affichage d'un bouton PayPal dans une page statique</p>
<p>- Editer le contenu d'une page statique et allant dans la gestion des pages statiques: menu "Pages statiques" dans l'administration</p>
<p>- Ajouter les lignes suivantes à l'endroit où vous souhaitez afficher le bouton PayPal.</p>

<pre style="font-size:12px; padding-left:40px; color:green">
<?php echo plxUtils::strCheck('<?php
global $plxShow;
eval($plxShow->callHook("plxShowDonsPayPal"));
?>
');
?>
</pre>
