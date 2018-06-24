<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

include(dirname(__FILE__).'/lib/class.plx.country.php'); 

# Control du token du formulaire
plxToken::validateFormToken($_POST);

# Control de l'accès à la page en fonction du profil de l'utilisateur connecté
$plxAdmin->checkProfil(PROFIL_ADMIN, PROFIL_MANAGER, PROFIL_MODERATOR);

$baseDirPlugins = plxUtils::getRacine().substr(PLX_PLUGINS,2);

if (!isset($_GET['ba'])) {
# Suppression des message selectionnes
if (isset($_POST['selection']) AND ((!empty($_POST['btn_ok1']) AND $_POST['selection'][0]=='delete') OR (!empty($_POST['btn_ok2']) AND $_POST['selection'][1]=='delete') AND isset($_POST['idBA']))) {
	foreach ($_POST['idBA'] as $k => $v) $plxPlugin->bonachat->delBonAchat($v);
	header('Location: plugin.php?p=plxBonAchat');
	exit;
}
# Mise à jour
elseif (!empty($_POST['update']) AND isset($_POST['idBA'])) {
# Edition
	$plxPlugin->bonachat->editBonAchat($_POST,$_POST['idBA']);
	header('Location: plugin.php?p=plxBonAchat&ba='.$_POST['idBA']);
	exit;
}
elseif (!empty($_POST['print']) AND isset($_POST['idBA'])) {
	# Impression bon
	$baseDir = plxUtils::getRacine();
	$baFirstSide = substr($plxPlugin->getParam('baFirstSide'), 0, 4) == 'http' ? $plxPlugin->getParam('baFirstSide') : $baseDir.$plxPlugin->getParam('baFirstSide');
	$baReverseSize = substr($plxPlugin->getParam('baReverseSize'), 0, 4) == 'http' ? $plxPlugin->getParam('baReverseSize') : $baseDir.$plxPlugin->getParam('baReverseSize');

	$datas = array(
		'expiration_date'=>$_POST['expiration_date'],
		'idcode'=>$_POST['idcode'],
		'title'=>$_POST['title'],
		'name'=>$_POST['name'],
		'firstname'=>$_POST['firstname'],
		'price'=>$_POST['price'].' '.$plxPlugin->getParam('baDevise'),
		'recipient_title'=>$_POST['recipient_title'],
		'recipient_name'=>$_POST['recipient_name'],
		'recipient_firstname'=>$_POST['recipient_firstname'],
		'text_color'=>$plxPlugin->getParam('bacolorText'), # couleur du texte pour le bon
		'backgroundimg_recto'=>$baFirstSide,
		'backgroundimg_verso'=>$baReverseSize
	);	

	$template = file_get_contents(dirname(__FILE__).'/tpl/'.$plxAdmin->aConf['default_lang'].'/bonachat.tpl');
	$content = $plxPlugin->bonachat->transform_vars_to_value($template, $datas);

	require_once(dirname(__FILE__).'/lib/pdf/html2pdf.class.php');
	
	// convert to PDF
	$html2pdf = new HTML2PDF('P', array(140,140), 'fr');
	$html2pdf->pdf->SetDisplayMode('real');
	$html2pdf->writeHTML($content);
	ob_end_clean();
	$html2pdf->Output('BA_'.$_POST['idcode'].'.pdf');

	header('Location: plugin.php?p=plxBonAchat&ba='.$_POST['idBA']);
	exit;
}
elseif (!empty($_POST['btn_ok3']) AND $_POST['selection']=='delete') {
# Suppression des message selectionnes
	$plxPlugin->bonachat->delBonAchat($_POST['idBA']);
	header('Location: plugin.php?p=plxBonAchat');
	exit;
	}

# Récuperation du type de commentaire à afficher
$_GET['sel'] = !empty($_GET['sel']) ? $_GET['sel'] : '';
if(in_array($_GET['sel'], array('online', 'offline', 'all')))
	$baSel = plxUtils::nullbyteRemove($_GET['sel']);
else
	$baSel = ((isset($_SESSION['selGB']) AND !empty($_SESSION['selGB'])) ? $_SESSION['selGB'] : 'all');

if($baSel=='all') { // all
	$baSelMotif = '/^[[:punct:]]?[0-9]{4}.(.*).xml$/';
	$_SESSION['selGB'] = 'all';
	$nbComPagination=$plxPlugin->bonachat->nbBonAchat('all');
	echo '<h2>'.$plxPlugin->getlang('L_BA_ALL_LIST').'</h2>';
}


$breadcrumbs = array();
$breadcrumbs[] = '<a '.($_SESSION['selGB']=='all'?'class="selected" ':'').'href="plugin.php?p=plxBonAchat&sel=all&amp;page=1">'.$plxPlugin->getLang('L_BA_ALL').'</a>&nbsp;('.$plxPlugin->bonachat->nbBonAchat('all').')';

$delete = $plxPlugin->getlang('L_BA_DELETE');
$selection = $plxPlugin->getlang('L_BA_FOR_SELECTION');

function selector($baSel, $id) {
    global $delete, $selection;
    ob_start();
	if($baSel=='all')
		plxUtils::printSelect('selection[]', array(''=> $selection, '-'=>'-----','delete' => $delete), '', false,'',$id);
	return ob_get_clean();
}

$selector1=selector($baSel, 'id_selection1');
$selector2=selector($baSel, 'id_selection2');

?>
<form action="plugin.php?p=plxBonAchat<?php echo !empty($_GET['a'])?'&a='.$_GET['a']:'' ?>" method="post" id="form_comments">

<div class="inline-form action-bar">
<h2><?php $plxPlugin->lang('L_BA_PG_TITLE') ?></h2>
<p class="breadcrumbs">
	<?php echo implode('&nbsp;|&nbsp;', $breadcrumbs); ?>
</p>
<p>
	<?php echo plxToken::getTokenPostMethod() ?>
	<?php echo $selector1 ?><input type="submit" name="btn_ok1" value="<?php echo L_OK ?>" onclick="return confirmAction(this.form, 'id_selection1', 'delete', 'idBA[]', '<?php echo L_CONFIRM_DELETE ?>')" />
</p>
</div>
<div class="scrollable-table">
<table id="ba-table" class="full-width">
<thead>
	<tr>
		<th class="checkbox"><input type="checkbox" onclick="checkAll(this.form, 'idBA[]')" /></th>
		<th class="date"><?php $plxPlugin->lang('L_BA_LIST_DATE') ?></th>
		<th class="expiration date"><?php $plxPlugin->lang('L_BA_EXPIRATION_DATE') ?></th>
		<th class="idcode"><?php $plxPlugin->lang('L_BA_IDCODE') ?></th>		
		<th class="name"><?php $plxPlugin->lang('L_BA_LIST_NAME') ?></th>
		<th class="firstname"><?php $plxPlugin->lang('L_BA_LIST_FIRSTNAME') ?></th>
		<th class="price"><?php $plxPlugin->lang('L_BA_LIST_PRICE') ?></th>		
		<th class="recipient_name"><?php $plxPlugin->lang('L_BA_LIST_RECIPIENT_NAME') ?></th>
		<th class="recipient_firstname"><?php $plxPlugin->lang('L_BA_LIST_RECIPIENT_FIRSTNAME') ?></th>
		<th class="action"><?php $plxPlugin->lang('L_BA_LIST_ACTION') ?></th>
	
	</tr>
</thead>
<tbody>

<?php
# On va récupérer les messages
$plxPlugin->bonachat->getPageba();
$savePage = preg_match('/admin\/(index|plugin).php/', $_SERVER['PHP_SELF']);
$bypage_admin_ba = 15;
$start = $bypage_admin_ba*($plxPlugin->bonachat->page-1);
$bonac = $plxPlugin->bonachat->getBonAchat('rsort',$baSel,$start,$bypage_admin_ba);

if($bonac) {
	$num=0;
	while($plxPlugin->bonachat->plxRecord_ba->loop()) { # On boucle
		$id = $plxPlugin->bonachat->plxRecord_ba->f('date').'_'.$plxPlugin->bonachat->plxRecord_ba->f('nbnote');
		$expiration_date = $plxPlugin->bonachat->plxRecord_ba->f('expiration_date');
		#$timestamp = strtotime(str_replace('/', '-', plxDate::formatDate($plxPlugin->bonachat->plxRecord_ba->f('date')))); 
		# On génère notre ligne
		echo '<tr class="line-'.(++$num%2).' top type-'.$plxPlugin->bonachat->plxRecord_ba->f('type').'">';
		echo '<td><input type="checkbox" name="idBA[]" value="'.$id.'" /></td>';
		echo '<td class="date">'.plxDate::formatDate($plxPlugin->bonachat->plxRecord_ba->f('date')).'&nbsp;</td>';
		echo '<td class="expiration_date">'.$expiration_date.'&nbsp;</td>';
		echo '<td class="idcode">'.$plxPlugin->bonachat->plxRecord_ba->f('idcode').'&nbsp;</td>';
		echo '<td class="name">'.$plxPlugin->bonachat->plxRecord_ba->f('name').'&nbsp;</td>';		
		echo '<td class="firstname">'.$plxPlugin->bonachat->plxRecord_ba->f('firstname').'&nbsp;</td>';
		echo '<td class="price">'.$plxPlugin->bonachat->plxRecord_ba->f('price').'&nbsp;'.$plxPlugin->getParam('baDevise').'&nbsp;</td>';
		echo '<td class="recipient_name">'.$plxPlugin->bonachat->plxRecord_ba->f('recipient_name').'&nbsp;</td>';	
		echo '<td class="recipient_firstname">'.$plxPlugin->bonachat->plxRecord_ba->f('recipient_firstname').'&nbsp;</td>';		
		echo '<td class="action">';
		echo '<a href="plugin.php?p=plxBonAchat&ba='.$id.'" title="'.$plxPlugin->getlang('L_BA_EDIT_TITLE').'">'.$plxPlugin->getlang('L_BA_EDIT').'</a>';
		echo '</td></tr>';
	}
} else { # Pas de message
	echo '<tr><td colspan="10" class="center">'.$plxPlugin->getlang('L_BA_NO_BON').'</td></tr>';
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
if($bonac) { # Si on a des message (hors page)
	# Calcul des pages
	$last_page = ceil($nbComPagination/$bypage_admin_ba);
	if($plxPlugin->bonachat->page > $last_page) $plxPlugin->bonachat->page = $last_page;
	$prev_page = $plxPlugin->bonachat->page - 1;
	$next_page = $plxPlugin->bonachat->page + 1;
	# Generation des URLs
	$p_url = 'plugin.php?p=plxBonAchat&page='.$prev_page.'&amp;sel='.$_SESSION['selGB']; # Page precedente
	$n_url = 'plugin.php?p=plxBonAchat&page='.$next_page.'&amp;sel='.$_SESSION['selGB']; # Page suivante
	$l_url = 'plugin.php?p=plxBonAchat&page='.$last_page.'&amp;sel='.$_SESSION['selGB']; # Derniere page
	$f_url = 'plugin.php?p=plxBonAchat&page=1'.'&amp;sel='.$_SESSION['selGB']; # Premiere page
	# On effectue l'affichage
	if($plxPlugin->bonachat->page > 2) # Si la page active > 2 on affiche un lien 1ere page
		echo '<span class="p_first"><a href="'.$f_url.'" title="'.L_PAGINATION_FIRST_TITLE.'">'.L_PAGINATION_FIRST.'</a></span>';
	if($plxPlugin->bonachat->page > 1) # Si la page active > 1 on affiche un lien page precedente
		echo '<span class="p_prev"><a href="'.$p_url.'" title="'.L_PAGINATION_PREVIOUS_TITLE.'">'.L_PAGINATION_PREVIOUS.'</a></span>';
	# Affichage de la page courante
	printf('<span class="p_page">'.L_PAGINATION.'</span>',$plxPlugin->bonachat->page,$last_page);
	if($plxPlugin->bonachat->page < $last_page) # Si la page active < derniere page on affiche un lien page suivante
		echo '<span class="p_next"><a href="'.$n_url.'" title="'.L_PAGINATION_NEXT_TITLE.'">'.L_PAGINATION_NEXT.'</a></span>';
	if(($plxPlugin->bonachat->page + 1) < $last_page) # Si la page active++ < derniere page on affiche un lien derniere page
		echo '<span class="p_last"><a href="'.$l_url.'" title="'.L_PAGINATION_LAST_TITLE.'">'.L_PAGINATION_LAST.'</a></span>';
}
?>
</p>
</div>
<?php } else {

$bonac = $plxPlugin->bonachat->getBonAchat('rsort',$_GET['ba']);
	
# Date d'expiration du bon d'achat
$expiration_date = $plxPlugin->bonachat->plxRecord_ba->f('expiration_date');
if ($plxPlugin->bonachat->plxRecord_ba->f('check_recipient_email')==1) { $check_rc_email=$plxPlugin->getLang('L_BA_YES'); } else { $check_rc_email=$plxPlugin->getLang('L_BA_NO');} 
?>
<form action="plugin.php?p=plxBonAchat" method="post" id="form_records">
	<div class="inline-form action-bar">
	    <h2><?php $plxPlugin->lang('L_BA_EDITING') ?></h2>	
		<p class="back"><a href="plugin.php?p=plxBonAchat"><?php $plxPlugin->lang('L_BA_BACK_TO_MESSAGES') ?></a></p>
		<?php echo plxToken::getTokenPostMethod() ?>
		<input class="red" type="submit" name="btn_ok3" value="<?php $plxPlugin->lang('L_BA_DELETE') ?>" onclick="Check=confirm('<?php $plxPlugin->lang('L_BA_DELETE_CONFIRM') ?>');if(Check==false) return false;"/>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" name="update" value="<?php $plxPlugin->lang('L_BA_UPDATE_BUTTON') ?>" />
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" name="print" value="<?php $plxPlugin->lang('L_BA_PRINT_BUTTON') ?>" />
	</div>
	<ul>
		<li><?php $plxPlugin->lang('L_BA_DATE') ?> : <?php echo plxDate::formatDate($plxPlugin->bonachat->plxRecord_ba->f('date')); ?></li> 
		<li><?php $plxPlugin->lang('L_BA_IDCODE') ?> : <?php echo $plxPlugin->bonachat->plxRecord_ba->f('idcode'); ?></li> 
		<li><?php $plxPlugin->lang('L_BA_PRICE') ?> : <strong><?php echo plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('price')).' '.$plxPlugin->getParam('baDevise'); ?></strong></li>
		<li><?php $plxPlugin->lang('L_BA_EXPIRATION_DATE') ?> : <span style="color: #ff0000;"><?php echo $expiration_date; ?></span></li> 		
	</ul>
	<fieldset>
		<?php plxUtils::printInput('idBA',$_GET['ba'],'hidden'); ?>
		<?php plxUtils::printInput('expiration_date',$expiration_date,'hidden'); ?>
		<?php plxUtils::printInput('date', plxDate::formatDate($plxPlugin->bonachat->plxRecord_ba->f('date')),'hidden'); ?>
		<?php plxUtils::printInput('idcode',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('idcode')),'hidden'); ?>		
        <?php plxUtils::printInput('selection','delete','hidden'); ?>
		<?php plxUtils::printInput('price',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('price')),'hidden'); ?>
	<div id="parente">  
        <p><div><label for="id_contributor"><strong><?php $plxPlugin->lang('L_BA_CONTRIBUTOR') ?> :</strong></label>
		</div></p><div class="pos12">&nbsp;</div>
        <p><div><label for="id_offer"><strong><?php $plxPlugin->lang('L_BA_RECIPIENT') ?> :</strong></label>
		</div></p>
 	</div><div style="clear:both"></div>		
	<div id="parente">  
        <p><div><label for="id_title"><?php $plxPlugin->lang('L_BA_TITLE') ?> :</label>
		<?php plxUtils::printSelect('title',array('Mr'=>$plxPlugin->getLang('L_BA_MR'),'Mme'=>$plxPlugin->getLang('L_BA_MRS')),plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('title')),'','col-md-3'); ?></div></p><div class="pos10">&nbsp;</div>
        <p><div><label for="id_recipient_title"><?php $plxPlugin->lang('L_BA_RECIPIENT_TITLE') ?> :</label>
		<?php plxUtils::printSelect('recipient_title',array('Mr'=>$plxPlugin->getLang('L_BA_MR'),'Mme'=>$plxPlugin->getLang('L_BA_MRS')),plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('recipient_title')),'','col-md-3'); ?></div></p>
 	</div><div style="clear:both"></div>
	<div id="parente">
		<p><div><label for="id_name"><?php $plxPlugin->lang('L_BA_NAME') ?> :</label>
		<?php plxUtils::printInput('name',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('name')),'text','40-255') ?></div></p><div class="pos11">&nbsp;</div>
        <p><div><label for="id_recipient_title"><?php $plxPlugin->lang('L_BA_RECIPIENT_NAME') ?> :</label>
		<?php plxUtils::printInput('recipient_name',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('recipient_name')),'text','40-255') ?></div></p>
	</div><div style="clear:both"></div>
	<div id="parente">     
        <p><div><label for="id_firstname"><?php $plxPlugin->lang('L_BA_FIRSTNAME') ?> :</label>
		<?php plxUtils::printInput('firstname',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('firstname')),'text','40-255') ?></div></p><div class="pos11">&nbsp;</div>
        <p><div><label for="id_recipient_firstname"><?php $plxPlugin->lang('L_BA_RECIPIENT_FIRSTNAME') ?> :</label>
		<?php plxUtils::printInput('recipient_firstname',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('recipient_firstname')),'text','40-255') ?></div></p>
 	</div><div style="clear:both"></div>
	<div id="parente">
		<p><div><label for="id_address"><?php $plxPlugin->lang('L_BA_ADDRESS') ?> :</label>
		<?php plxUtils::printInput('address',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('address')),'text','40-255') ?></div></p><div class="pos11">&nbsp;</div>
        <p><div><label for="id_recipient_title"><?php $plxPlugin->lang('L_BA_RECIPIENT_PHONE') ?> :</label>
		<?php plxUtils::printInput('recipient_phone',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('recipient_phone')),'text','40-255') ?></div></p>
	</div><div style="clear:both"></div>
	<div id="parente">
		<p><div><label for="id_zipcode"><?php $plxPlugin->lang('L_BA_ZIPCODE') ?> :</label>
		<?php plxUtils::printInput('zipcode',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('zipcode')),'text','40-255') ?></div></p><div class="pos11">&nbsp;</div>
        <p><div><label for="id_recipient_email"><?php $plxPlugin->lang('L_BA_RECIPIENT_EMAIL') ?> :</label>
		<?php plxUtils::printInput('recipient_email',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('recipient_email')),'text','40-255') ?></div></p>
	</div><div style="clear:both"></div>
	<div id="parente">
		<p><div><label for="id_city"><?php $plxPlugin->lang('L_BA_CITY') ?> :</label>
		<?php plxUtils::printInput('city',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('city')),'text','40-255') ?></div></p><div class="pos11">&nbsp;</div>
		<p><div><label for="check_recipient_email"><?php $plxPlugin->lang('L_BA_SEND_RC_BA') ?> : <span style="color: #ff0000;"><?php echo $check_rc_email ?></span></label>&nbsp;</div></p>
	</div><div style="clear:both"></div>
	<div id="parente">
		<p><div><label for="id_phone"><?php $plxPlugin->lang('L_BA_PHONE') ?> :</label>
		<?php plxUtils::printInput('phone',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('phone')),'text','40-255') ?></div></p>
	</div><div style="clear:both"></div>
	<div id="parente">
		<p><div><label for="id_email"><?php $plxPlugin->lang('L_BA_EMAIL') ?> :</label>
		<?php plxUtils::printInput('email',plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('email')),'text','40-255') ?></div></p>
	</div><div style="clear:both"></div>
	<div id="parente">
		<p><div><label for="id_country"><?php $plxPlugin->lang('L_BA_COUNTRY') ?> :</label>
		<?php plxUtils::printSelect('country',baCountry::country(),plxUtils::strCheck($plxPlugin->bonachat->plxRecord_ba->f('country')),'','col-md-4'); ?></div></p>
	</div><div style="clear:both"></div>	
	</fieldset>
</form>
<?php } ?>
