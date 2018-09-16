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
<p>Fichier d'aide du plugin <b>plxCounters</b></p>

<p>Code à ajouter dans votre thème pour afficher les statistiques :</p>
<pre>
<div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
<?php echo plxUtils::strCheck('<?php eval($plxShow->callHook(\'plxShowCounters\')) ?>') ?>
</div>			
</pre>
<p>Un fichier log par jour et de type CSV. Il contient les informations suivantes :</p>
<p>Chaque ligne contient 1 entr&eacute;e de la forme :
<p>&lt;DATE&gt;;&lt;HOUR&gt;;&lt;IP&gt;;&lt;QUERY_STRING&gt;;&lt;HTTP_USER_AGENT&gt;;&lt;HTTP_REFERER&gt;</p>
<p>&nbsp;</p>
</p>
