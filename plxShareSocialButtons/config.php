<?php if (!defined('PLX_ROOT')) exit; ?>
<?php
/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/

# Control du token du formulaire
plxToken::validateFormToken($_POST);

function themes_list($patern='*'){
	$dir = PLX_PLUGINS.'plxShareSocialButtons/themes/';
	$tab = array();
	$themes = array();
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
		$themes[$v] = ucfirst($v);

	return $themes;
}
  
$themes = themes_list();

# Services supportés
$SSBDesable = $SSBEnable = array ('buffer', 'digg', 'facebook', 'flattr', 'google', 'linkedin', 'pinterest', 'reddit', 'stumbleupon', 'tumblr', 'twitter', 'vk', 'yummly', 'email', 'print');

if (!empty($_POST)) {
	
	$plxPlugin->setParam('SSBCount', $_POST['SSBCount'], 'numeric');
	$plxPlugin->setParam('SSBThemes', $_POST['SSBThemes'], 'string');
	$plxPlugin->setParam('SSBText', $_POST['SSBText'], 'string');	
	$plxPlugin->setParam('SSBSize', $_POST['SSBSize'], 'numeric');	
	$plxPlugin->setParam('SSBTarget', $_POST['SSBTarget'], 'numeric');
	$plxPlugin->setParam('SSBPos', $_POST['SSBPos'], 'string');	
	$plxPlugin->setParam('SSBPad', $_POST['SSBPad'], 'numeric');
	$plxPlugin->setParam('SSBFont', $_POST['SSBFont'], 'numeric');
	$plxPlugin->setParam('SSBNofollow', $_POST['SSBNofollow'], 'numeric');
	$plxPlugin->setParam('SSBDefaultfb', $_POST['SSBDefaultfb'], 'numeric');		
	$plxPlugin->setParam('Imagefb', $_POST['Imagefb'], 'string');
	
	$idEnable = $_POST['idEnable'];
	$idDesable = $_POST['idDesable'];
	
	$idEnable_count = count($idEnable);
    
	foreach ($idDesable as $k => $v)
	{	
		unset($idEnable[array_search($v, $idEnable)]);
	}
	$idEnable = array_values($idEnable);
	
	if (isset($idEnable)) {
		for ($i = 0; $i < count($idEnable); $i++)
		{
			$SSB[$i]['service'] = $idEnable[$i];
			if (!empty($_POST['ord_'.$i])) {
				$SSB[$i]['ordre'] = intval($_POST['ord_'.$i]);
			} else {
				$SSB[$i]['ordre'] = $i+1;
			}
		}
	}
	
	# On va trier les clés selon l'ordre choisi
	if(sizeof($SSB)>0) uasort($SSB, create_function('$a, $b', 'return $a["ordre"]>$b["ordre"];'));
	
	$SSB = array_values($SSB);	
	
	if (isset($idEnable)) {
		for ($i = 0; $i < count($idEnable); $i++)
		{
			$plxPlugin->setParam('act_'.$i, $SSB[$i]['service'], 'string');
		}
	}	
	
	$plxPlugin->setParam('SSBenableCount', count($idEnable), 'numeric');
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxShareSocialButtons');
	exit;
}
$SSBCount = count($SSBDesable);
$SSBThemes =  $plxPlugin->getParam('SSBThemes')=='' ? 'simple' : $plxPlugin->getParam('SSBThemes');
$SSBText =  $plxPlugin->getParam('SSBText')=='' ? 'Vous avez aimé cet article ? Alors partagez-le avec vos amis en cliquant sur les boutons ci-dessous :' : $plxPlugin->getParam('SSBText');
$SSBSize =  $plxPlugin->getParam('SSBSize')=='' ? 32 : $plxPlugin->getParam('SSBSize');
$SSBTarget =  $plxPlugin->getParam('SSBTarget')=='' ? 1 : $plxPlugin->getParam('SSBTarget');
$SSBPos =  $plxPlugin->getParam('SSBPos')=='' ? 'right' : $plxPlugin->getParam('SSBPos');
$SSBPad =  $plxPlugin->getParam('SSBPad')=='' ? 2 : $plxPlugin->getParam('SSBPad');
$SSBFont =  $plxPlugin->getParam('SSBFont')=='' ? 14 : $plxPlugin->getParam('SSBFont');
$SSBenableCount =  $plxPlugin->getParam('SSBenableCount')=='' ? 0 : $plxPlugin->getParam('SSBenableCount');
$SSBNofollow =  $plxPlugin->getParam('SSBNofollow')=='' ? 0 : $plxPlugin->getParam('SSBNofollow');
$SSBDefaultfb =  $plxPlugin->getParam('SSBDefaultfb')=='' ? 0 : $plxPlugin->getParam('SSBDefaultfb');
$Imagefb =  $plxPlugin->getParam('Imagefb')=='' ? '' : $plxPlugin->getParam('Imagefb');

$SSBEnable = array();
for ($k = 0; $k < $SSBenableCount; $k++)
{
	${'act_'.$k} = $plxPlugin->getParam('act_'.$k) == '' ? 0 : $plxPlugin->getParam('act_'.$k);
	$SSBEnable[] = $plxPlugin->getParam('act_'.$k);
}
if ($SSBenableCount != 0) {
foreach ($SSBEnable as $k => $v)
	{	
		unset($SSBDesable[array_search($v, $SSBDesable)]);
	}
$SSBDesable = array_values($SSBDesable);		
}
$_SESSION['medias'] = 'data/';
?>
<style>
#bloc-social {text-align:<?php echo $SSBPos; ?>;}
#bloc-social .title{font-size:<?php echo $SSBFont; ?>px;}
.social{background:transparent;display:inline-block;margin-left:0px;}
.social img{margin-left:<?php echo $SSBPad; ?>px;margin-right:<?php echo $SSBPad; ?>px;}
table{table-layout:fixed;width:400px;}
th, td{{vertical-align: middle;}
</style>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS; ?>plxShareSocialButtons/css/config.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo PLX_PLUGINS; ?>plxShareSocialButtons/js/config.js"></script>
<div>
	<span class="tab_0 tab" id="tab_01" onclick="javascript:change_tab('01');"><?php $plxPlugin->lang('L_SBB_TAB01') ?></span>
	<span class="tab_0 tab" id="tab_02" onclick="javascript:change_tab('02');"><?php $plxPlugin->lang('L_SBB_TAB02') ?></span>
	<span class="tab_0 tab" id="tab_03" onclick="javascript:change_tab('03');"><?php $plxPlugin->lang('L_SBB_TAB03') ?></span>
</div>
<div class="content_tab" id="content_tab_01">
<?php
echo $plxPlugin->lang('L_SSB_APERCU');
echo '<div id="bloc-social">';
echo '<p class="title">'.$SSBText.'</p>';
echo '<div class="social">';
foreach($SSBEnable as $k=>$v) {
	echo '<img src="'.PLX_PLUGINS.'plxShareSocialButtons/themes/'.$SSBThemes.'/'.$v.'.png" title="'.ucfirst($v).'" alt="'.ucfirst($v).'" style="width: '.$SSBSize.'px; "></a>';
}
echo '</div>';
echo '</div>';	

?>
<form id="form_plxShareSocialButtons" action="parametres_plugin.php?p=plxShareSocialButtons" method="post">
	<fieldset>
	<div class="scrollable-table">
	    <h2><?php echo $plxPlugin->lang('L_SSB_LIST_ENABLE'); ?></h2>
		<table id="SSB-table" class="full-width">
			<thead>
				<tr>
					<th><?php echo $plxPlugin->lang('L_SSB_IMAGE'); ?></th>
					<th><?php echo $plxPlugin->lang('L_SSB_SERVICE'); ?></th>
					<th><input type="checkbox" onclick="checkAll(this.form, 'idDesable[]')" />&nbsp;<?php echo $plxPlugin->lang('L_SSB_DESABLE'); ?></th>
					<th><?php echo $plxPlugin->lang('L_SSB_ORDER') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			# Initialisation de l'ordre
			$num = 0;
			# Si on a des pages statiques
				foreach($SSBEnable as $k=>$v) { # Pour chaque boutons
					if (!empty(${'act_'.$k})) {
					$ordre = ++$num;	
					echo '<tr>';
					echo '<td><img src="'.PLX_PLUGINS.'plxShareSocialButtons/themes/'.$SSBThemes.'/'.$v.'.png" title="'.$v.'" alt="Share on '.$v.'" style="width: 48px; height: 48px"></td>';
					echo '<td>'.ucfirst($v).'</td>';
					echo '<td><input type="checkbox" name="idDesable[]" value="'.$v.'" /><input type="hidden" name="idEnable[]" value="'.$v.'" /></td>';
					echo '<td>';
					$id = $ordre - 1;
					plxUtils::printInput('ord_'.$id, $ordre, 'text', '1-3');
					echo '</td></tr>';
					}
				}
				
				if ($num == 0) {
					echo '<tr><td colspan="4" class="center">';
					echo $plxPlugin->lang('L_SSB_BUTTON_NONE');
					echo '</td></tr>';
				}
				?>
			</tbody>
		</table>	
		<h2><?php echo $plxPlugin->lang('L_SSB_LIST_DESABLE'); ?></h2>		
		<table id="SSB-table" class="full-width">
			<thead>
				<tr>
					<th><?php echo $plxPlugin->lang('L_SSB_IMAGE'); ?></th>
					<th><?php echo $plxPlugin->lang('L_SSB_SERVICE'); ?></th>
					<th><input type="checkbox" onclick="checkAll(this.form, 'idEnable[]')" />&nbsp;<?php echo $plxPlugin->lang('L_SSB_ENABLE'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>	
			<?php
			    $num = 0;
				foreach($SSBDesable as $k=>$v) { # Pour chaque boutons
					++$num;	
					echo '<tr>';
					echo '<td><img src="'.PLX_PLUGINS.'plxShareSocialButtons/themes/'.$SSBThemes.'/'.$v.'.png" title="'.$v.'" alt="Share on '.$v.'" style="width: 48px; height: 48px"></td>';
					echo '<td>'.ucfirst($v).'</td>';
					echo '<td><input type="checkbox" name="idEnable[]" value="'.$v.'" /></td>';
					echo '<td>&nbsp;</td>';
					echo '</tr>';
				}
				if ($num == 0) {
					echo '<tr><td colspan="4" class="center">';
					echo $plxPlugin->lang('L_SSB_BUTTON_NONE');
					echo '</td></tr>';
				}
			?>
			</tbody>
		</table>
	</div>
	</div>
<div class="content_tab" id="content_tab_02">	    
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_SSBThemes"><?php $plxPlugin->lang('L_SSB_THEMES') ?>&nbsp;:</label>
				<?php plxUtils::printSelect('SSBThemes',$themes,$SSBThemes); ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_SSBSize"><?php $plxPlugin->lang('L_SSB_SIZE') ?>&nbsp;:</label>
				<?php plxUtils::printInput('SSBSize',$SSBSize,'numeric','32-32') ?> Px
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_SSBPad"><?php $plxPlugin->lang('L_SSB_PADDING') ?>&nbsp;:</label>
				<?php plxUtils::printInput('SSBPad',$SSBPad,'numeric','32-32') ?> Px
			</div>
		</div>
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_SSBPos"><?php $plxPlugin->lang('L_SSB_POSITION') ?>&nbsp;:</label>
				<?php plxUtils::printSelect('SSBPos',array('left'=>$plxPlugin->getlang('L_SSB_LEFT'),'center'=>$plxPlugin->getlang('L_SSB_CENTER'),'right'=>$plxPlugin->getlang('L_SSB_RIGHT')),$SSBPos); ?>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_SSBTarget"><?php $plxPlugin->lang('L_SSB_TARGET') ?>&nbsp;:</label>
				<?php plxUtils::printSelect('SSBTarget', array('1'=>L_YES,'0'=>L_NO), $SSBTarget) ?>
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_SSBNofollow"><?php $plxPlugin->lang('L_SSB_NOFOLLOW') ?>&nbsp;:&nbsp;<a class="hint"><span><?php $plxPlugin->lang('L_SSB_HELP_NOFOLLOW') ?></span></a></label>
				<?php plxUtils::printSelect('SSBNofollow', array('1'=>L_YES,'0'=>L_NO), $SSBNofollow) ?>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_SSBText"><?php $plxPlugin->lang('L_SSB_TEXT') ?>&nbsp;:</label>
				<?php plxUtils::printInput('SSBText',$SSBText,'text','100-255') ?>
			</div>
		</div>		
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_SSBFont"><?php $plxPlugin->lang('L_SSB_FONT') ?>&nbsp;:</label>
				<?php plxUtils::printInput('SSBFont',$SSBFont,'numeric','32-32') ?> Px
			</div>
		</div>	
		<div class="grid">
			<div class="col sml-12">	
				<label for="id_SSBDefaultfb"><?php $plxPlugin->lang('L_SSB_DEFAULTFB') ?>&nbsp;:</label>
				<?php plxUtils::printSelect('SSBDefaultfb', array('1'=>L_YES,'0'=>L_NO), $SSBDefaultfb) ?>
			</div>
		</div>			
		<div class="grid gridthumb">
				<div class="col sml-12">
					<label for="id_Imagefb">
						<?php $plxPlugin->lang('L_SSB_DEFAULT_IMAGE_FB') ?>&nbsp;:&nbsp;<a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_Imagefb" href="javascript:void(0)" onclick="mediasManager.openPopup('id_Imagefb', true)" style="outline:none; text-decoration: none"><img src="<?php echo PLX_PLUGINS ?>plxShareSocialButtons/img/gfichiers.png" height="16" width="16" style="vertical-align: middle"></a>
						<a class="hint"><span><?php $plxPlugin->lang('L_SSB_HELP_DEFAULT_IMAGE_FB') ?></span></a>
					</label>
					<?php plxUtils::printInput('Imagefb',plxUtils::strCheck($Imagefb),'text','100-255',false); ?>&nbsp;&nbsp;
					<?php
					$imgUrl = PLX_ROOT.$Imagefb;
					if(is_file($imgUrl)) {
						echo '<div id="id_Imagefb_img"><img src="'.$imgUrl.'" alt="" /></div>';
					} else {
						echo '<div id="id_Imagefb_img"></div>';
					}
					?>
				</div>
			</div>	
		</div>	
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SSB_SAVE') ?>" />
		</p>	
	</fieldset>
</form>
<div class="content_tab" id="content_tab_03">
	<div id="update">
		<?php $infoPlugin = $plxPlugin->sharesocialbuttons->UpdatePlugin('plxShareSocialButtons'); ?>
		<p><?php $plxPlugin->lang('L_VP_ACTUAL_VERSION'); ?>&nbsp;:&nbsp;<?php echo $infoPlugin['actualversion'].' ('.$infoPlugin['actualdate'].')'; ?></p>	
		<?php if ($infoPlugin['status'] == 0) { ?>
			<p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_ERROR'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxShareSocialButtons/img/vers/up_warning.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 1) { ?>
			<p><?php $plxPlugin->lang('L_VP_LAST_VERSION'); ?>&nbsp;<?php echo $infoPlugin['newplugin']; ?>.</p>
			<p><img src="<?php echo PLX_PLUGINS; ?>plxShareSocialButtons/img/vers/up_ok.gif" alt=""></p>
		<?php } elseif ($infoPlugin['status'] == 2) { ?>
			<p><?php $plxPlugin->lang('L_VP_NEW_VERSION'); ?><p>
			<p><span class="color"><?php echo $infoPlugin['newplugin']; ?>&nbsp;(<?php echo $infoPlugin['newversion']; ?>&nbsp;-&nbsp;<?php echo $infoPlugin['newdate']; ?>)</span></p>
			<p><?php $plxPlugin->lang('L_VP_NEW2_VERSION'); ?>.</p>
			<p><a href="<?php echo $infoPlugin['newurl']; ?>" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxShareSocialButtons/img/vers/up_download.gif" alt=""></a></p>
		<?php } elseif ($infoPlugin['status'] == 3) {?>
		    <p class="center"><span class="color"><?php $plxPlugin->lang('L_VP_DESACTIVED'); ?></span></p>
			<p><a href="http://dpfpic.com" target="blank" title=""><img src="<?php echo PLX_PLUGINS; ?>plxShareSocialButtons/img/vers/up_warning.gif" alt=""></a></p>
		<?php } ?>	
	</div>
</div>	
<script type="text/javascript">
var anc_tab = '01';
change_tab(anc_tab);
</script>