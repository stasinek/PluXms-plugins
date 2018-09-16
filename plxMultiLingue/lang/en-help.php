<p style="margin-top:20px">
To display flags, edit sidebar.php in your theme folder and add this following line:
<br /><br />
<pre style="font-size:1.1em;margin-left:20px">
&lt;?php eval($plxShow->callHook('MultiLingue')) ?>
</pre>
</p>

<p style="margin-top:20px">
To display dependent articles written in another language, add the following line to article.php file<br /><br />
<pre style="font-size:1.1em;margin-left:20px">
&lt;?php eval($plxShow->callHook('MultiLingue', 'artlinks')) ?>
</pre>
</p>

<p style="margin-top:20px">
To display dependent static pages written in another language, add the following line to static.php file<br /><br />
<pre style="font-size:1.1em;margin-left:20px">
&lt;?php eval($plxShow->callHook('MultiLingue', 'staticlinks')) ?>
</pre>
</p>

<p style="margin-top:20px">
<strong>Caution :<br />
<ul>
<li>Set plxMultiLingue in first position in the active plugins list.</li>
<li>Activate url rewriting (Parameters > Advanced configuration, Enable url rewriting : Yes</li>
</ul>
</strong>
</p>