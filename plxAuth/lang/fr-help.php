<h1>Aide d'installation du formulaire d'authentification</h1>
<p>Pour ajouter le formulaire il faut ajouter à votre thème un appel (hook). Par exemple dans le fichier themes/defaut/sidebar.php après :<br />
<pre style="border:1px solid #000000; background-color: #CCCCCC; padding: 10px;">&lt;ul&gt;
	&lt;?php $plxShow-&gt;lastComList('&lt;li&gt;&lt;a href="#com_url"&gt;#com_author '.$plxShow-&gt;getLang('SAID').' : #com_content(34)&lt;/a&gt;&lt;/li&gt;'); ?&gt;
&lt;/ul&gt;</pre></p>

<p>Ajouter<br />
<pre style="border:1px solid #000000; background-color: #CCCCCC; padding: 10px;">&lt;?php eval($plxShow-&gt;callHook('plxFormAuth')) ?></pre></p>
