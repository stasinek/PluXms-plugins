<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
?>

<style>
h2	{ font-size: 2.0em; margin: .75em 0; color: #013162 } 
h3	{ font-size: 1.8em; margin: .83em 0; color: #13518C }
h4	{ font-size: 1.6em; margin: 1.12em 0; color: #2D7CC1 }
h5	{ font-size: 1.4em; font-style: italic; margin: 1.5em 0; color: #A4A4A4 } 
p	{ font-size: 1.0em; }
pre	{ font-size: 12px; }
</style>

<h2>Aide</h2>
<p>Fichier d'aide du plugin <b>plxTranslator</b></p>

<h2>Installation</h2>
</p><b>Activation du plugin</b></p>
<p>- Aller dans le menu Paramètres > Plugins</p>
<p>- Cocher le plugin plxTranslator et dans le déroulant "Pour la sélection", sélectionner le menu "Activer"</p>
<p>&nbsp;</p>
<p>Editez le fichier template "sidebar.php" par exemple. Ajoutez y le code suivant à l'endroit ou vous souhaitez voir apparaitre les bannières ou les citations.</p>
<pre>
<div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
<?php echo plxUtils::strCheck('<?php eval($plxShow->callHook(\'plxShowTranslator\')) ?>') ?>
</div>
</pre>
<p>Affichage Translator dans une page statique</p>
<p>- Editer le contenu d'une page statique et allant dans la gestion des pages statiques: menu "Pages statiques" dans l'administration</p>
<p>- Ajouter les lignes suivantes à l'endroit où vous souhaitez afficher Translator.</p>
<pre>
<div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
<?php echo plxUtils::strCheck('<?php
global $plxShow;
eval($plxShow->callHook(\'plxShowTranslator\'));
?>
');
?>
</div>
<p style="color:red;"> ATTENTION ! : Un seul Translator activé dans le thème ou la page statique.</p>