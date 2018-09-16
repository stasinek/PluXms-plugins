<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

# Control du token du formulaire
plxToken::validateFormToken($_POST);

# Control de l'accès à la page en fonction du profil de l'utilisateur connecté
$plxAdmin->checkProfil(PROFIL_ADMIN, PROFIL_MANAGER, PROFIL_MODERATOR);

if (!isset($_GET['gb'])) {
# Suppression des message selectionnes
if (isset($_POST['selection']) AND ((!empty($_POST['btn_ok1']) AND $_POST['selection'][0]=='delete') OR (!empty($_POST['btn_ok2']) AND $_POST['selection'][1]=='delete') AND isset($_POST['idGB']))) {
	foreach ($_POST['idGB'] as $k => $v) $plxPlugin->guestbook->delGuestBook($v);
	header('Location: plugin.php?p=plxGuestBook');
	exit;
}
# Validation des message selectionnes
elseif(isset($_POST['selection']) AND (!empty($_POST['btn_ok1']) AND ($_POST['selection'][0]=='online') OR (!empty($_POST['btn_ok2']) AND $_POST['selection'][1]=='online')) AND isset($_POST['idGB'])) {
	foreach ($_POST['idGB'] as $k => $v) $plxPlugin->guestbook->modGuestBook($v, 'online');
	header('Location: plugin.php?p=plxGuestBook');
	exit;
}
# Mise hors-ligne des message selectionnes
elseif (isset($_POST['selection']) AND ((!empty($_POST['btn_ok1']) AND $_POST['selection'][0]=='offline') OR (!empty($_POST['btn_ok2']) AND $_POST['selection'][1]=='offline')) AND isset($_POST['idGB'])) {
	foreach ($_POST['idGB'] as $k => $v) $plxPlugin->guestbook->modGuestBook($v, 'offline');
	header('Location: plugin.php?p=plxGuestBook');
	exit;
}
elseif (!empty($_POST['update']) AND isset($_POST['idGB'])) {
# Edition
	$plxPlugin->guestbook->editGuestBook($_POST,$_POST['idGB']);
	header('Location: plugin.php?p=plxGuestBook&gb='.$_POST['idGB']);
	exit;
}
elseif (isset($_POST['online'])) {
# Commentaire en ligne
	$plxPlugin->guestbook->editGuestBook($_POST,$_POST['idGB']);
	$plxPlugin->guestbook->modGuestBook($_POST['idGB'],'online');
	header('Location: plugin.php?p=plxGuestBook&gb='.$_POST['idGB']);
	exit;
}
elseif (isset($_POST['offline'])) { 
# Commentaire hors-ligne
	$plxPlugin->guestbook->editGuestBook($_POST,$_POST['idGB']);
	$plxPlugin->guestbook->modGuestBook($_POST['idGB'],'offline');
	header('Location: plugin.php?p=plxGuestBook&gb='.$_POST['idGB']);
	exit;
}
elseif (!empty($_POST['btn_ok3']) AND $_POST['selection']=='delete') {
# Suppression des message selectionnes
	$plxPlugin->guestbook->delGuestBook($_POST['idGB']);
	header('Location: plugin.php?p=plxGuestBook');
	exit;
}

# Récuperation du type de commentaire à afficher
$_GET['sel'] = !empty($_GET['sel']) ? $_GET['sel'] : '';
if(in_array($_GET['sel'], array('online', 'offline', 'all')))
	$gbSel = plxUtils::nullbyteRemove($_GET['sel']);
else
	$gbSel = ((isset($_SESSION['selGB']) AND !empty($_SESSION['selGB'])) ? $_SESSION['selGB'] : 'all');

if($gbSel=='online') {
	$gbSelMotif = '/^[0-9]{4}.(.*).xml$/';
	$_SESSION['selGB'] = 'online';
	$nbComPagination=$plxPlugin->guestbook->nbGuestBook('online');
	echo '<h2>'.$plxPlugin->getlang('L_GB_ONLINE_LIST').'</h2>';
}
elseif($gbSel=='offline') {
	$gbSelMotif = '/^_[0-9]{4}.(.*).xml$/';
	$_SESSION['selGB'] = 'offline';
	$nbComPagination=$plxPlugin->guestbook->nbGuestBook('offline');
	echo '<h2>'.$plxPlugin->getlang('L_GB_OFFLINE_LIST').'</h2>';
}
elseif($gbSel=='all') { // all
	$gbSelMotif = '/^[[:punct:]]?[0-9]{4}.(.*).xml$/';
	$_SESSION['selGB'] = 'all';
	$nbComPagination=$plxPlugin->guestbook->nbGuestBook('all');
	echo '<h2>'.$plxPlugin->getlang('L_GB_ALL_LIST').'</h2>';
}


$breadcrumbs = array();
$breadcrumbs[] = '<a '.($_SESSION['selGB']=='all'?'class="selected" ':'').'href="plugin.php?p=plxGuestBook&sel=all&amp;page=1">'.$plxPlugin->getLang('L_GB_ALL').'</a>&nbsp;('.$plxPlugin->guestbook->nbGuestBook('all').')';
$breadcrumbs[] = '<a '.($_SESSION['selGB']=='online'?'class="selected" ':'').'href="plugin.php?p=plxGuestBook&sel=online&amp;page=1">'.$plxPlugin->getLang('L_GB_ONLINE').'</a>&nbsp;('.$plxPlugin->guestbook->nbGuestBook('online').')';
$breadcrumbs[] = '<a '.($_SESSION['selGB']=='offline'?'class="selected" ':'').'href="plugin.php?p=plxGuestBook&sel=offline&amp;page=1">'.$plxPlugin->getLang('L_GB_OFFLINE').'</a>&nbsp;('.$plxPlugin->guestbook->nbGuestBook('offline').')';

$offline = $plxPlugin->getlang('L_GB_SET_OFFLINE');
$online = $plxPlugin->getlang('L_GB_SET_ONLINE');
$delete = $plxPlugin->getlang('L_GB_DELETE');
$selection = $plxPlugin->getlang('L_GB_FOR_SELECTION');

function selector($gbSel, $id) {
    global $offline, $online, $delete, $selection;
    ob_start();
	if($gbSel=='online')
		plxUtils::printSelect('selection[]', array(''=> $selection, 'offline' => $offline, '-'=>'-----', 'delete' => $delete), '', false,'',$id);
	elseif($gbSel=='offline')
		plxUtils::printSelect('selection[]', array(''=> $selection, 'online' => $online, '-'=>'-----', 'delete' => $delete), '', false,'',$id);
	elseif($gbSel=='all')
		plxUtils::printSelect('selection[]', array(''=> $selection, 'online' => $online, 'offline' => $offline,  '-'=>'-----','delete' => $delete), '', false,'',$id);
	return ob_get_clean();
}

$selector1=selector($gbSel, 'id_selection1');
$selector2=selector($gbSel, 'id_selection2');
?>
<form action="plugin.php?p=plxGuestBook<?php echo !empty($_GET['a'])?'&a='.$_GET['a']:'' ?>" method="post" id="form_comments">

<div class="inline-form action-bar">
<h2><?php $plxPlugin->lang('L_PAGE_TITLE') ?></h2>
<p class="breadcrumbs">
	<?php echo implode('&nbsp;|&nbsp;', $breadcrumbs); ?>
</p>
<p>
	<?php echo plxToken::getTokenPostMethod() ?>
	<?php echo $selector1 ?><input type="submit" name="btn_ok1" value="<?php echo L_OK ?>" onclick="return confirmAction(this.form, 'id_selection1', 'delete', 'idGB[]', '<?php echo L_CONFIRM_DELETE ?>')" />
</p>
</div>
<div class="scrollable-table">
<table id="gb-table" class="full-width">
<thead>
	<tr>
		<th class="checkbox"><input type="checkbox" onclick="checkAll(this.form, 'idGB[]')" /></th>
		<th class="nbnote"><?php $plxPlugin->lang('L_GB_LIST_ID') ?></th>		
		<th class="datetime"><?php $plxPlugin->lang('L_GB_LIST_DATE') ?></th>
		<th class="message"><?php $plxPlugin->lang('L_GB_LIST_MESSAGE') ?></th>
		<th class="author"><?php $plxPlugin->lang('L_GB_LIST_AUTHOR') ?></th>
		<th class="action"><?php $plxPlugin->lang('L_GB_LIST_ACTION') ?></th>
	</tr>
</thead>
<tbody>

<?php
# On va récupérer les messages
$plxPlugin->guestbook->getPagegb();
$savePage = preg_match('/admin\/(index|plugin).php/', $_SERVER['PHP_SELF']);
$bypage_admin_gb = 10;
$start = $bypage_admin_gb*($plxPlugin->guestbook->page-1);
$guestb = $plxPlugin->guestbook->getGuestBook('rsort',$gbSel,$start,$bypage_admin_gb);

if($guestb) {
	$num=0;
	while($plxPlugin->guestbook->plxRecord_gb->loop()) { # On boucle
		$artId = $plxPlugin->guestbook->plxRecord_gb->f('article');
		$status = $plxPlugin->guestbook->plxRecord_gb->f('status');
		if ($status == 'online') { $first = ''; } else { $first = '_'; }
		$id = $first.$plxPlugin->guestbook->plxRecord_gb->f('date').'_'.$plxPlugin->guestbook->plxRecord_gb->f('nbnote');
		$content = nl2br($plxPlugin->guestbook->plxRecord_gb->f('content'));
		if($_SESSION['selGB']=='all') {
			$content = $content.' - <strong>'.($status=='online'?$plxPlugin->getlang('L_GB_ONLINE'):$plxPlugin->getlang('L_GB_OFFLINE')).'</strong>';
		}
		# On génère notre ligne
		echo '<tr class="line-'.(++$num%2).' top type-'.$plxPlugin->guestbook->plxRecord_gb->f('type').'">';
		echo '<td><input type="checkbox" name="idGB[]" value="'.$id.'" /></td>';
		echo '<td class="nbnote">'.$plxPlugin->guestbook->plxRecord_gb->f('nbnote').'&nbsp;</td>';		
		echo '<td class="datetime">'.plxDate::formatDate($plxPlugin->guestbook->plxRecord_gb->f('date')).'&nbsp;</td>';
		echo '<td>'.$content.'&nbsp;</td>';
		echo '<td>'.plxUtils::strCut($plxPlugin->guestbook->plxRecord_gb->f('author'),30).'&nbsp;</td>';
		echo '<td class="action">';
		echo '<a href="plugin.php?p=plxGuestBook&gb='.$id.'" title="'.$plxPlugin->getlang('L_GB_EDIT_TITLE').'">'.$plxPlugin->getlang('L_GB_EDIT').'</a>';
		echo '</td></tr>';
	}
} else { # Pas de message
	echo '<tr><td colspan="6" class="center">'.$plxPlugin->getlang('L_GB_NO_MESSAGE').'</td></tr>';
}
?>
</tbody>
</table>
</div>
</form>

<div id="pagination">
<p>
<?php
# Affichage de la pagination
if($guestb) { # Si on a des message (hors page)
	# Calcul des pages
	$last_page = ceil($nbComPagination/$bypage_admin_gb);
	if($plxPlugin->guestbook->page > $last_page) $plxPlugin->guestbook->page = $last_page;
	$prev_page = $plxPlugin->guestbook->page - 1;
	$next_page = $plxPlugin->guestbook->page + 1;
	# Generation des URLs
	$p_url = 'plugin.php?p=plxGuestBook&page='.$prev_page.'&amp;sel='.$_SESSION['selGB']; # Page precedente
	$n_url = 'plugin.php?p=plxGuestBook&page='.$next_page.'&amp;sel='.$_SESSION['selGB']; # Page suivante
	$l_url = 'plugin.php?p=plxGuestBook&page='.$last_page.'&amp;sel='.$_SESSION['selGB']; # Derniere page
	$f_url = 'plugin.php?p=plxGuestBook&page=1'.'&amp;sel='.$_SESSION['selGB']; # Premiere page
	# On effectue l'affichage
	if($plxPlugin->guestbook->page > 2) # Si la page active > 2 on affiche un lien 1ere page
		echo '<span class="p_first"><a href="'.$f_url.'" title="'.L_PAGINATION_FIRST_TITLE.'">'.L_PAGINATION_FIRST.'</a></span>';
	if($plxPlugin->guestbook->page > 1) # Si la page active > 1 on affiche un lien page precedente
		echo '<span class="p_prev"><a href="'.$p_url.'" title="'.L_PAGINATION_PREVIOUS_TITLE.'">'.L_PAGINATION_PREVIOUS.'</a></span>';
	# Affichage de la page courante
	printf('<span class="p_page">'.L_PAGINATION.'</span>',$plxPlugin->guestbook->page,$last_page);
	if($plxPlugin->guestbook->page < $last_page) # Si la page active < derniere page on affiche un lien page suivante
		echo '<span class="p_next"><a href="'.$n_url.'" title="'.L_PAGINATION_NEXT_TITLE.'">'.L_PAGINATION_NEXT.'</a></span>';
	if(($plxPlugin->guestbook->page + 1) < $last_page) # Si la page active++ < derniere page on affiche un lien derniere page
		echo '<span class="p_last"><a href="'.$l_url.'" title="'.L_PAGINATION_LAST_TITLE.'">'.L_PAGINATION_LAST.'</a></span>';
}
?>
</p>
</div>
<?php } else {

$guestb = $plxPlugin->guestbook->getGuestBook('rsort',$_GET['gb']);

# Statut de le message
if ($plxPlugin->guestbook->plxRecord_gb->f('status') == 'offline') {
	$statut = '<strong>'.$plxPlugin->getlang('L_GB_OFFLINE').'</strong>';
} else {
	$statut = '<strong>'.$plxPlugin->getlang('L_GB_ONLINE').'</strong>';
}	
# Date du commentaire
$date = plxDate::date2Array($plxPlugin->guestbook->plxRecord_gb->f('date'));

# Email visible
$actmail = plxUtils::strCheck($plxPlugin->guestbook->plxRecord_gb->f('actmail'));

# On inclut le header
#include(dirname(__FILE__).'/top.php');
?>
<form action="plugin.php?p=plxGuestBook" method="post" id="form_comment">
	<div class="inline-form action-bar">
	    <h2><?php $plxPlugin->lang('L_GB_EDITING') ?></h2>	
		<p class="back"><a href="plugin.php?p=plxGuestBook"><?php $plxPlugin->lang('L_GB_BACK_TO_MESSAGES') ?></a></p>
		<?php echo plxToken::getTokenPostMethod() ?>
		<input class="red" type="submit" name="btn_ok3" value="<?php $plxPlugin->lang('L_GB_DELETE') ?>" onclick="Check=confirm('<?php $plxPlugin->lang('L_GB_DELETE_CONFIRM') ?>');if(Check==false) return false;"/>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<?php if(substr($_GET['gb'],0,1) == '_') : ?>
		<input type="submit" name="online" value="<?php $plxPlugin->lang('L_GB_PUBLISH_BUTTON') ?>" />
		<?php else : ?>
		<input type="submit" name="offline" value="<?php $plxPlugin->lang('L_GB_OFFLINE_BUTTON') ?>" />
		<?php endif; ?>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" name="update" value="<?php $plxPlugin->lang('L_GB_UPDATE_BUTTON') ?>" />
	</div>

	<ul>
		<li><?php $plxPlugin->lang('L_GB_ID') ?> : <?php echo $plxPlugin->guestbook->plxRecord_gb->f('nbnote'); ?></li> 
		<li><?php $plxPlugin->lang('L_GB_IP_FIELD') ?> : <?php echo $plxPlugin->guestbook->plxRecord_gb->f('ip'); ?></li>
		<li><?php $plxPlugin->lang('L_GB_STATUS_FIELD') ?> : <?php echo $statut; ?></li>
	</ul>
	
	<fieldset>
		<?php plxUtils::printInput('idGB',$_GET['gb'],'hidden'); ?>
        <?php plxUtils::printInput('selection','delete','hidden'); ?>
		<p><label><?php $plxPlugin->lang('L_GB_DATE_FIELD') ?>&nbsp;:</label>
		<?php plxUtils::printInput('day',$date['day'],'text','2-2',false,'fld1'); ?>
		<?php plxUtils::printInput('month',$date['month'],'text','2-2',false,'fld1'); ?>
		<?php plxUtils::printInput('year',$date['year'],'text','2-4',false,'fld2'); ?>
		<?php plxUtils::printInput('time',$date['time'],'text','2-5',false,'fld2'); ?>
		<a href="javascript:void(0)" onclick="dateNow(<?php echo date('Z') ?>); return false;" title="<?php L_NOW; ?>"><img src="theme/images/date.png" alt="" /></a></p>

		<p><label for="id_author"><?php $plxPlugin->lang('L_GB_AUTHOR_FIELD') ?> :</label>
		<?php plxUtils::printInput('author',plxUtils::strCheck($plxPlugin->guestbook->plxRecord_gb->f('author')),'text','40-255') ?></p>
		<?php $site = plxUtils::strCheck($plxPlugin->guestbook->plxRecord_gb->f('site')); ?>
		<p><label for="id_site"><?php $plxPlugin->lang('L_GB_SITE_FIELD') ?> : <?php if($site != '') echo '<a href="'.$site.'">'.$site.'</a>'; ?></label>
		<?php
			plxUtils::printInput('site',$site,'text','40-255');
		?></p>
		<p><label for="id_mail"><?php $plxPlugin->lang('L_GB_EMAIL_FIELD') ?> :<?php if($plxPlugin->guestbook->plxRecord_gb->f('mail') != '') : ?>
		<?php echo '<a href="mailto:'.$plxPlugin->guestbook->plxRecord_gb->f('mail').'">'.$plxPlugin->guestbook->plxRecord_gb->f('mail').'</a>' ?>
		<?php endif; ?></label>
		<?php plxUtils::printInput('mail',plxUtils::strCheck($plxPlugin->guestbook->plxRecord_gb->f('mail')),'text','40-255') ?>
		</p>
		<p><?php $plxPlugin->lang('L_GB_VIEW_EMAIL') ?>&nbsp;:&nbsp;&nbsp;<input type="checkbox" name="actmail" value="off" <?php if ($actmail=='on') echo ' checked="checked"'; ?> /></p>
		<p id="p_content"><label for="id_content"><?php $plxPlugin->lang('L_GB_MESSAGE_FIELD') ?> :</label></p>
		<?php plxUtils::printArea('content',$plxPlugin->guestbook->plxRecord_gb->f('content'), 60, 7); ?>
	</fieldset>
</form>
<?php } ?>
