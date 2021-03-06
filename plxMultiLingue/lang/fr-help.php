<p style="margin-top:20px">
Pour afficher les drapeaux permettant de sélectionner une langue, éditez par exemple le fichier sidebar.php de votre thème et ajoutez la ligne suivante:
<br /><br />
<pre style="font-size:1.1em;margin-left:20px">
&lt;?php eval($plxShow->callHook('MultiLingue')) ?>
</pre>
</p>

<p style="margin-top:20px">
Pour afficher les articles dépendants rédigés dans une autre langue, ajoutez dans le fichier article.php de votre thème la ligne suivante:
<br /><br />
<pre style="font-size:1.1em;margin-left:20px">
&lt;?php eval($plxShow->callHook('MultiLingue', 'artlinks')) ?>
</pre>
</p>

<p style="margin-top:20px">
Pour afficher les pages statiques dépendantes rédigés dans une autre langue, ajoutez dans le fichier static.php de votre thème la ligne suivante:
<br /><br />
<pre style="font-size:1.1em;margin-left:20px">
&lt;?php eval($plxShow->callHook('MultiLingue', 'staticlinks')) ?>
</pre>
</p>

<p style="margin-top:20px">
<strong>Important :<br />
<ul>
<li>
	plxMultiLingue doit être le premier plugin dans la liste des plugins actifs pour assurer un bon fonctionnement<br />
	Notamment avec le plugin plxBetterUrl.
</li>
<li>
	plxMultiLingue requiert l'activation de la réécriture d'url dans PluXml<br />
	Paramètres > Configuration avancée > Activer la réécriture d'urls : Oui
</li>
</ul>

</strong>
</p>
