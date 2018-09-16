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

function icone_list($type='new'){
	$patern='*';
	$dir = PLX_PLUGINS.'plxNewArtsComs/icone/'.$type.'/';
	$tab = array();
	$icone = array();
	if (is_dir($dir)) {
	    if ($dh = opendir($dir)) {
	        while (($file = readdir($dh)) !== false) {
				$ext=explode('.',$file);
				$ext=$ext[count($ext)-1];
	            if($ext==$patern || $patern=="*" && $file!='.' && $file!='..' && $file!='index.html'){
					$tab[$file]= $file;
				}
	        }
	        closedir($dh);
	    }
	}
	sort($tab);
	foreach($tab as $k=>$v)
		$icone[$v] = ucfirst($v);

	return $icone;
}
  
$icone_new = icone_list('new');
$icone_update = icone_list('update');

if(!empty($_POST)) {

	$plxPlugin->setParam('newArts_Active', $_POST['newArts_Active'], 'numeric');
	$plxPlugin->setParam('newArts_NbDays', $_POST['newArts_NbDays'], 'numeric');
	$plxPlugin->setParam('newArts_Icone', $_POST['newArts_Icone'], 'string');	
	$plxPlugin->setParam('newArts_Img', $_POST['newArts_Img'], 'string');
	$plxPlugin->setParam('updArts_Active', $_POST['updArts_Active'], 'numeric');
	$plxPlugin->setParam('updArts_NbDays', $_POST['updArts_NbDays'], 'numeric');
	$plxPlugin->setParam('updArts_Icone', $_POST['updArts_Icone'], 'string');	
	$plxPlugin->setParam('updArts_Img', $_POST['updArts_Img'], 'string');
	$plxPlugin->setParam('newComs_Active', $_POST['newComs_Active'], 'numeric');
	$plxPlugin->setParam('newComs_NbDays', $_POST['newComs_NbDays'], 'numeric');
	$plxPlugin->setParam('newComs_Icone', $_POST['newComs_Icone'], 'string');	
	$plxPlugin->setParam('newComs_Img', $_POST['newComs_Img'], 'string');
	
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxNewArtsComs');
	exit;
}

$newArts_Active =  $plxPlugin->getParam('newArts_Active')=='' ? 0 : $plxPlugin->getParam('newArts_Active');
$newArts_NbDays =  $plxPlugin->getParam('newArts_NbDays')=='' ? 7 : $plxPlugin->getParam('newArts_NbDays');
$newArts_Icone =  $plxPlugin->getParam('newArts_Icone')=='' ? 'new_001.png' : $plxPlugin->getParam('newArts_Icone');
$newArts_Img =  $plxPlugin->getParam('newArts_Img');
$updArts_Active =  $plxPlugin->getParam('updArts_Active')=='' ? 0 : $plxPlugin->getParam('updArts_Active');
$updArts_NbDays =  $plxPlugin->getParam('updArts_NbDays')=='' ? 7 : $plxPlugin->getParam('updArts_NbDays');
$updArts_Icone =  $plxPlugin->getParam('updArts_Icone')=='' ? 'update_001.png' : $plxPlugin->getParam('updArts_Icone');
$updArts_Img =  $plxPlugin->getParam('updArts_Img');
$newComs_Active =  $plxPlugin->getParam('newComs_Active')=='' ? 0 : $plxPlugin->getParam('newComs_Active');
$newComs_NbDays =  $plxPlugin->getParam('newComs_NbDays')=='' ? 7 : $plxPlugin->getParam('newComs_NbDays');
$newComs_Icone =  $plxPlugin->getParam('newComs_Icone')=='' ? 'new_002.png' : $plxPlugin->getParam('newComs_Icone');
$newComs_Img =  $plxPlugin->getParam('newComs_Img');

if (!empty($newArts_Img)) {
	$newArts_imgUrl = PLX_ROOT.$newArts_Img;
} else {
	$newArts_imgUrl = PLX_PLUGINS.'plxNewArtsComs/icone/new/'.$newArts_Icone;
}
if (!empty($updArts_Img)) {
	$updArts_imgUrl = PLX_ROOT.$updArts_Img;
} else {
	$updArts_imgUrl = PLX_PLUGINS.'plxNewArtsComs/icone/update/'.$updArts_Icone;
}
if (!empty($newComs_Img)) {
	$newComs_imgUrl = PLX_ROOT.$newComs_Img;
} else {
	$newComs_imgUrl = PLX_PLUGINS.'plxNewArtsComs/icone/new/'.$newComs_Icone;
}
?>
<?php if ($plxAdmin->aConf['version'] < '5.4') { ?>
<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<?php } ?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxNewArtsComs/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxNewArtsComs/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_NAC_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_NAC_TAB02') ?></span>
</div>
<div class="content_tab" id="content_tab_01">
<form class="inline-form" id="form_plxnewartscoms" action="parametres_plugin.php?p=plxNewArtsComs" method="post">
<div class="col sml-12 med-7 lrg-10">
	<fieldset>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_newArts_Active"><?php echo $plxPlugin->lang('L_NAC_NEWARTS_ACTIVE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('newArts_Active',array('1'=>L_YES,'0'=>L_NO),$newArts_Active); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_newArts_NbDays"><?php $plxPlugin->lang('L_NAC_NEWARTS_NBDAYS') ?>&nbsp;:</label>
				<?php plxUtils::printInput('newArts_NbDays',$newArts_NbDays,'text','32-32') ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_newArts_Icone"><?php $plxPlugin->lang('L_NAC_NEWARTS_ICONE') ?>&nbsp;:</label>
				<?php plxUtils::printSelect('newArts_Icone',$icone_new,$newArts_Icone); ?>
			</div>
		</div>			
		<div class="grid gridthumb">
				<div class="col sml-12">
					<label for="id_newArts_">
						<?php $plxPlugin->lang('L_NAC_NEWARTS_IMG') ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_newArts_Img" href="javascript:void(0)" onclick="mediasManager.openPopup('id_newArts_Img', false)" style="outline:none; text-decoration: none"><img src="<?php echo PLX_PLUGINS ?>plxNewArtsComs/img/gfichiers.png" height="16" width="16" style="vertical-align: middle"></a>
					</label>
					<?php plxUtils::printInput('newArts_Img',plxUtils::strCheck($newArts_Img),'text','64-255',false,'full-width'); ?>
					<a class="hint"><span><?php $plxPlugin->lang('L_NAC_HELP_NEWARTS_IMG') ?></span></a>
				</div>
		</div>				
		<?php
		if(is_file($newArts_imgUrl)) {
			echo '<div id="id_newArts_Img"><img src="'.$newArts_imgUrl.'" alt="" /></div>';
		} else {
			echo '<div id="id_newArts_Img"></div>';
		}
		?>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_updArts_Active"><?php echo $plxPlugin->lang('L_NAC_UPDARTS_ACTIVE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('updArts_Active',array('1'=>L_YES,'0'=>L_NO),$updArts_Active); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_updArts_NbDays"><?php $plxPlugin->lang('L_NAC_UPDARTS_NBDAYS') ?>&nbsp;:</label>
				<?php plxUtils::printInput('updArts_NbDays',$updArts_NbDays,'text','32-32') ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_updArts_Icone"><?php $plxPlugin->lang('L_NAC_UPDARTS_ICONE') ?>&nbsp;:</label>
				<?php plxUtils::printSelect('updArts_Icone',$icone_update,$updArts_Icone); ?>
			</div>
		</div>			
		<div class="grid gridthumb">
				<div class="col sml-12">
					<label for="id_updArts_">
						<?php $plxPlugin->lang('L_NAC_UPDARTS_IMG') ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_updArts_Img" href="javascript:void(0)" onclick="mediasManager.openPopup('id_updArts_Img', false)" style="outline:none; text-decoration: none"><img src="<?php echo PLX_PLUGINS ?>plxNewArtsComs/img/gfichiers.png" height="16" width="16" style="vertical-align: middle"></a>
					</label>
					<?php plxUtils::printInput('updArts_Img',plxUtils::strCheck($updArts_Img),'text','64-255',false,'full-width'); ?>
					<a class="hint"><span><?php $plxPlugin->lang('L_NAC_HELP_UPDARTS_IMG') ?></span></a>
					<?php
					if(is_file($updArts_imgUrl)) {
						echo '<div id="id_updArts_Img"><img src="'.$updArts_imgUrl.'" alt="" /></div>';
					} else {
						echo '<div id="id_updArts_Img"></div>';
					}
					?>
				</div>
		</div>
		<div class="grid">
			<div class="col sml-12">
			<label for="id_newComs_Active"><?php echo $plxPlugin->lang('L_NAC_NEWCOMS_ACTIVE') ?>&nbsp;:</label>
			<?php plxUtils::printSelect('newComs_Active',array('1'=>L_YES,'0'=>L_NO),$newComs_Active); ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_newComs_NbDays"><?php $plxPlugin->lang('L_NAC_NEWCOMS_NBDAYS') ?>&nbsp;:</label>
				<?php plxUtils::printInput('newComs_NbDays',$newComs_NbDays,'text','32-32') ?>
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_newComs_Icone"><?php $plxPlugin->lang('L_NAC_NEWCOMS_ICONE') ?>&nbsp;:</label>
				<?php plxUtils::printSelect('newComs_Icone',$icone_new,$newComs_Icone); ?>
			</div>
		</div>			
		<div class="grid gridthumb">
				<div class="col sml-12">
					<label for="id_newComs_">
						<?php $plxPlugin->lang('L_NAC_NEWCOMS_IMG') ?>&nbsp;:&nbsp;
						<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_newComs_Img" href="javascript:void(0)" onclick="mediasManager.openPopup('id_newComs_Img', false)" style="outline:none; text-decoration: none"><img src="<?php echo PLX_PLUGINS ?>plxNewArtsComs/img/gfichiers.png" height="16" width="16" style="vertical-align: middle"></a>
					</label>
					<?php plxUtils::printInput('newComs_Img',plxUtils::strCheck($newComs_Img),'text','64-255',false,'full-width'); ?>
					<a class="hint"><span><?php $plxPlugin->lang('L_NAC_HELP_NEWCOMS_IMG') ?></span></a>
					<?php
					if(is_file($newComs_imgUrl)) {
						echo '<div id="id_newComs_Img"><img src="'.$newComs_imgUrl.'" alt="" /></div>';
					} else {
						echo '<div id="id_newComs_Img"></div>';
					}
					?>
				</div>
		</div>				
		</div>
		</div>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_NAC_SAVE') ?>" />
		</p>
	</fieldset>

</form>
<div class="content_tab" id="content_tab_02">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->newartscoms->UpdatePlugin('plxNewArtsComs'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxNewArtsComs/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS; ?>plxNewArtsComs/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxNewArtsComs/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxNewArtsComs/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>
