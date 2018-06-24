<?php if(!defined('PLX_ROOT')) exit; ?>
<h2>Помощь для плагина NewArtsComs</h2>
<p><strong>Этот плагин позволяет отображать</strong> : <br />
    - Упоминания в течение х дней для новых статей<br />
	- Упоминания в течение х дней для обновленных статей<br />
	- Упоминания в течение х дней для новых комментариев</p>

<h3>Utilisation</h3>
<p>
<strong>Для отображения иконки новых/обновленных статей</strong> : <br />
Отредактируйте файл <strong>home.php</strong> вашей темы, и добавьте следующую строку :<br />
<pre><div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
<?php echo plxUtils::strCheck('<?php eval($plxShow->callHook("plxShowNewArts")) ?>') ?>
</div></pre>
сразу за следующующей строкой :
<pre style="font-size:14px; padding-left:40px; color:green">
<?php echo plxUtils::strCheck('<?php $plxShow->artTitle(\'link\'); ?>') ?>
</pre>
</p>
<p>
<strong>Чтобы отобразить значок новый комментарий</strong> : <br />
Отредактируйте файл <strong>commentaires.php</strong> вашей темы, и добавьте следующую строку :<br />
<pre><div style="color:#000;padding:0 10px 15px 10px;border:1px solid #dedede">
<?php echo plxUtils::strCheck('<?php eval($plxShow->callHook("plxShowNewComs")) ?>') ?>
</div></pre>
сразу за следующующей строкой :
<pre style="font-size:14px; padding-left:40px; color:green">
<?php echo plxUtils::strCheck('<div id="<?php $plxShow->comId(); ?>" class="comment">') ?>
</pre>
</p>
