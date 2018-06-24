<h2>Aide</h2>

Pour afficher les boutons sociaux Google+, Facebook, Twitter, LinkedIn etc...

<h3 style="font-size:1.3em;font-weight:bold;padding:10px 0 10px 0">Dans les articles</h3>
<p>
Dans le fichier <strong>article.php</strong> de votre thème, ajoutez la ligne suivante à l'endroit où vous souhaitez afficher les boutons.
</p>
<pre style="font-size:12px; padding-left:40px; color:green">
&lt;?php eval($plxShow->callHook('plxShowShareSocialButtons')) ?&gt;
</pre>

<h3 style="font-size:1.3em;font-weight:bold;padding:10px 0 10px 0">Dans les pages statiques</h3>
<p>
Dans le fichier <strong>static.php</strong> de votre thème, ajoutez la ligne suivante à l'endroit où vous souhaitez afficher les boutons.
</p>
<pre style="font-size:12px; padding-left:40px; color:green">
&lt;?php eval($plxShow->callHook('plxShowShareSocialButtonsStatic')) ?&gt;
</pre>

<hr>
<p><strong>AJOUTER UNE IMAGE PAR D&Eacute;FAUT LORS DE VOS PARTAGES FACEBOOK</strong></p>
<p>Il se peut que votre contenu soit d&eacute;j&agrave; partag&eacute; avec&nbsp;une image diff&eacute;rente (prise par l'outils <em lang="en" title="robot d'indexation">crawler</em>)&nbsp;sur ce r&eacute;seau social.</p>
<p>Dans ce cas, rendez-vous sur l'url suivante et ins&eacute;rez l'url de votre site :</p>
<p><a title="http://developers.facebook.com/tools/lint" href="http://developers.facebook.com/tools/lint" target="_blank">http://developers.facebook.com/tools/lint</a></p>
<p>Cette op&eacute;ration videra les images pr&eacute;cedemment charg&eacute;es et vous permettra de partager <strong>VOTRE</strong> image.</p>

