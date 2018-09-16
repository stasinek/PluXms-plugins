<?php if(!defined('PLX_ROOT')) exit; ?>
<h2>Help for DonsPayPal plugin</h2>
<p>This plugin displays a PayPal button to donate.</p>
<br />

<h3>Use</h3>
<p>
Add the following line in your template (sidebar.php) to where you want to display the Paypal button:
</p>
<pre style="font-size:12px; padding-left:40px; color:green">
&lt;?php eval($plxShow->callHook('plxShowDonsPayPal')) ?&gt;
</pre>

<p>Displaying a PayPal button in a static page</p>
<p>- Edit the contents of a static page and going in the management of static pages "Static pages" menu in the administration</p>
<p>- Add the following lines to where you want to display the PayPal button.</p>

<pre style="font-size:12px; padding-left:40px; color:green">
<?php echo plxUtils::strCheck('<?php
global $plxShow;
eval($plxShow->callHook("plxShowDonsPayPal"));
?>
');
?>
</pre>
