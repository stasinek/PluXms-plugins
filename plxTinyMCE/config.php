<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
//echo("<pre>");
//print_r($_SERVER);
//echo("</pre>");





# Control du token du formulaire
plxToken::validateFormToken($_POST);

$plxPlugin->initconfiguration();

$aProfils = $plxPlugin->get_aprofil();



if(!empty($_POST)) {

	$dir = realpath(PLX_ROOT.$_POST['uplDir']);
	if(!is_dir($dir)) {
		plxMsg::Error($plxPlugin->getLang('L_ERROR_INVALID_DIR'));
	} else {
		
		# tynimce plugin
		foreach($aProfils as $key=>$val) { 
			$array_tyniplugin=array();
			if(isset($_POST['tyniplugin'.$key]) AND sizeof($_POST['tyniplugin'.$key])>0) {
				foreach($_POST['tyniplugin'.$key] as $plugin) {
					$array_tyniplugin[$plugin] = $plugin;
				}
			}
			$plxPlugin->setParam('tyniplugin'.$key, implode(" ",array_keys($array_tyniplugin)), 'string');
		}
		
		# tynimce toolbar
		foreach($aProfils as $key=>$val) { 
			$array_tynitoolbar=array();
			if(isset($_POST['tynitoolbar'.$key]) AND sizeof($_POST['tynitoolbar'.$key])>0) {
				foreach($_POST['tynitoolbar'.$key] as $toolbar) {
					$array_tynitoolbar[$toolbar] = $toolbar;
				}
			}
			$plxPlugin->setParam('tynitoolbar'.$key, implode(' | ',array_keys($array_tynitoolbar)), 'string');
		}
		
		# filemanager permission
		foreach($aProfils as $key=>$val) { 
			$array_fmp=array();
			if(isset($_POST['filemanager_permission'.$key]) AND sizeof($_POST['filemanager_permission'.$key])>0) {
				foreach($_POST['filemanager_permission'.$key] as $permission) {
					$array_fmp[$permission] = $permission;
				}
			}
			# add control to check all if not exist
			$plxPlugin->setParam('filemanager_permission'.$key, "control ".implode(' ',array_keys($array_fmp)), 'string');
		}
		// check C:
		$slash ="";
		if ($_POST['root'][1]!=":"){
			$slash="/";
		}
		$plxPlugin->setParam('root',  $slash.trim($_POST['root'],'/'), 'cdata');
		$plxPlugin->setParam('uplDir', trim($_POST['uplDir'],'/')."/", 'cdata');
		
		$plxPlugin->setParam('static', $_POST['static'], 'numeric');
		$plxPlugin->setParam('pluginedit', $_POST['pluginedit'], 'numeric');
		$plxPlugin->setParam('emailprotect', $_POST['emailprotect'], 'numeric');
		
		
		$plxPlugin->setParam('image_resizing', $_POST['image_resizing'], 'numeric');
		$plxPlugin->setParam('image_width', $_POST['image_width'], 'numeric');
		$plxPlugin->setParam('image_height', $_POST['image_height'], 'numeric');
		$plxPlugin->setParam('filemanger_replace_media', $_POST['filemanger_replace_media'], 'numeric');
		
		
		$plxPlugin->setParam('filemanger_use_aviary', $_POST['filemanger_use_aviary'], 'numeric');
		$plxPlugin->setParam('filemanger_cle_aviary', $_POST['filemanger_cle_aviary'], 'cdata');
		$plxPlugin->setParam('filemanger_secret_aviary', $_POST['filemanger_secret_aviary'], 'cdata');
		 
       	# style format
		$plxPlugin->setParam('style_format_active', $_POST['style_format_active'], 'numeric');
		$plxPlugin->setParam('style_format_content', $_POST['style_format_content'], 'cdata');
      
       
		
		
		$plxPlugin->saveParams();
	}
	header('Location: parametres_plugin.php?p=spxtynimce');
	exit;
}







?>

<h2><?php echo $plxPlugin->getInfo('title') ?></h2>

<?php
/*echo ("<pre>");
print_r($aProfils);
echo ("</pre>");*/
?>
<form id="form_ckeditor" action="parametres_plugin.php?p=spxtynimce" method="post">
	<fieldset>
		
        <p class="field_head">&nbsp;<?php $plxPlugin->lang('L_PARAM') ?>
				<strong><?php $plxPlugin->lang('L_GENERAL') ?></strong>
		</p>
       <!-- <p class="field"><label for="id_root"><?php echo $plxPlugin->lang('L_ROOT') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('root',$plxPlugin->getParam('root'),'text','40-255') ?>
		<a class="help" title="<?php echo $plxPlugin->lang('L_HELP_NOSLASH_END') ?>">&nbsp;</a><strong>ex: /var/www/pluxml</strong>-->
        
        <p class="field"><label for="id_uplDir"><?php echo $plxPlugin->lang('L_UPLOAD_DIR') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('uplDir',$plxPlugin->getParam('uplDir'),'text','40-255') ?>
		<a class="help" title="<?php echo L_HELP_SLASH_END ?>">&nbsp;</a><strong>ex: data/</strong>
		
		<p class="field"><label for="id_static"><?php echo $plxPlugin->lang('L_STATIC') ?></label></p>
		<?php plxUtils::printSelect('static',array('1'=>L_YES,'0'=>L_NO), $plxPlugin->getParam('static'));?>
		<br />
        
        <p class="field"><label for="id_pluginedit"><?php echo $plxPlugin->lang('L_PLUGINS_EDIT') ?></label></p>
		<?php plxUtils::printSelect('pluginedit',array('1'=>L_YES,'0'=>L_NO), $plxPlugin->getParam('pluginedit'));?>
		<br />
        
         <p class="field"><label for="id_emailprotect"><?php echo $plxPlugin->lang('L_EMAILPROTECT') ?></label></p>
		<?php plxUtils::printSelect('emailprotect',array('1'=>L_YES,'0'=>L_NO), $plxPlugin->getParam('emailprotect'));?>
		<br />
        			
		
        <p class="field_head">&nbsp;<?php $plxPlugin->lang('L_PARAM') ?>
				<strong><?php $plxPlugin->lang('L_PLUGINS') ?></strong>
		</p>
        
        
        <p class="field"><label for="id_pluginedit"><?php echo $plxPlugin->lang('L_TYNIPLUGIN_ACTIVE') ?></label></p>
        <p class="field">
        <table class="table" style="width:320px">
        <thead>
            <tr>
                <th style="width:100px">Plugin</th>
                <?php 
				foreach($aProfils as $key=>$val) { 
                	echo('<th style="width:60px">'.$val.'</th>');
                	
				 }
				  ?>
           
            </tr>
        </thead>
        <tbody>
        <?php
		# Initialisation de l'ordre
		# Récupération des plugin sélectionnés
		$aTyniplugin = array();
		foreach($aProfils as $key=>$val) { 
			$tyniplugin = $plxPlugin->getParam('tyniplugin'.$key);
			$aTyniplugin[$key] = $tyniplugin=='' ? array() :  explode(' ', $tyniplugin);
		}

		$aPlugin = explode (" ",$plxPlugin->get_plugin_list());
		$num = 0;
		$order =0;
		foreach($aPlugin as $plugin) {
			$order = ++$num;
			echo '<tr class="line-'.($num%2).'">';
			
			echo '<td>'.$plugin.'</td>';
			foreach($aProfils as $key=>$val) { 
				$checkedPlugin = in_array($plugin,$aTyniplugin[$key])?'checked="checked "':'';
				echo '<td><input type="checkbox" '.$checkedPlugin.' id="tyniplugin" name="tyniplugin'.$key.'[]" value="'.$plugin.'" /></td>';
			
			}
			echo '</tr>';
		}
		?>
		</tbody>
		</table>
        </p>
        
        <p class="field"><label for="id_pluginedit"><?php echo $plxPlugin->lang('L_TYNITOOLBAR_ACTIVE') ?></label></p>
       	<p class="field">
        <table class="table" style="width:320px">
        <thead>
            <tr>
                <th style="width:100px">Toolbar</th>
                <?php 
				foreach($aProfils as $key=>$val) { 
                	echo('<th style="width:60px">'.$val.'</th>');
                	
				 }
				  ?>
           
            </tr>
        </thead>
        <tbody>
        <?php
		$aTynitoolbar = array();
		foreach($aProfils as $key=>$val) { 
			$tynitoolbar = $plxPlugin->getParam('tynitoolbar'.$key);
			$aTynitoolbar[$key] = $tynitoolbar=='' ? array() :  explode(' | ', $tynitoolbar);
		}
		# Initialisation de l'ordre
		$aToolbar = explode (' | ',$plxPlugin->get_toolbar_list());
		$num = 0;
		$order =0;
		foreach($aToolbar as $toolbar) {
			$order = ++$num;
			echo '<tr class="line-'.($num%2).'">';
			
			echo '<td>'.$toolbar.'</td>';
			foreach($aProfils as $key=>$val) { 
				$checkedToolbar = in_array($toolbar,$aTynitoolbar[$key])?'checked="checked "':'';
				echo '<td><input type="checkbox" '.$checkedToolbar.' id="tynitoolbar" name="tynitoolbar'.$key.'[]" value="'.$toolbar.'" /></td>';
			}
			
			echo '</tr>';
		}
		?>
		</tbody>
		</table>
        </p>
        
        
        <p class="field_head">&nbsp;<?php $plxPlugin->lang('L_PARAM') ?>
				<strong><?php $plxPlugin->lang('L_STYLEFORMAT') ?></strong>
		</p>
        <p class="field"><label for="id_style_format_active"><?php echo $plxPlugin->lang('L_STYLE_FORMAT_ACTIVE') ?></label></p>
		<?php plxUtils::printSelect('style_format_active',array('1'=>L_YES,'0'=>L_NO), $plxPlugin->getParam('style_format_active'));?>
		<br />
         <p class="field"><label for="id_style_format_content"><?php echo $plxPlugin->lang('L_STYLE_FORMAT_CONTENT') ?></label><a class="help" title="<?php echo $plxPlugin->lang('L_HELP_NOCOMMA_END') ?>">&nbsp;</a></p>
		<?php 
		plxUtils::printArea('style_format_content',plxUtils::strCheck($plxPlugin->getParam('style_format_content')),35,8); 
		?>
		
        <br />
      
       
        
        
        
        <p class="field_head">&nbsp;<?php $plxPlugin->lang('L_PARAM') ?>
				<strong><?php $plxPlugin->lang('L_FILEMANAGER') ?></strong>
		</p>
         <p class="field"><label for="id_filemanger_replace_media"><?php echo $plxPlugin->lang('L_FILEMANGER_REPLACEMEDIA') ?></label></p>
		<?php plxUtils::printSelect('filemanger_replace_media',array('1'=>L_YES,'0'=>L_NO), $plxPlugin->getParam('filemanger_replace_media'));?>
		<br />
        
         <p class="field"><?php echo $plxPlugin->lang('L_FILEMANAGER_IMAGE') ?></p>
        
         <p class="field"><label for="id_image_resizing"><?php echo $plxPlugin->lang('L_IMAGE_RESIZING') ?></label></p>
		<?php plxUtils::printSelect('image_resizing',array('1'=>L_YES,'0'=>L_NO), $plxPlugin->getParam('image_resizing'));?>
		<br />
         <p class="field"><label for="id_image_width"><?php echo $plxPlugin->lang('L_IMAGE_WIDTH') ?></label></p>
		<?php plxUtils::printInput('image_width',$plxPlugin->getParam('image_width'),'text','4-4') ?>
		<br />
         <p class="field"><label for="id_image_height"><?php echo $plxPlugin->lang('L_IMAGE_HEIGHT') ?></label></p>
		<?php plxUtils::printInput('image_height',$plxPlugin->getParam('image_height'),'text','4-4') ?>
		<br />
        
        <p class="field"><?php echo $plxPlugin->lang('L_ABOUTAVIAY') ?></p>
        <p class="field"><label for="id_filemanger_use_aviary"><?php echo $plxPlugin->lang('L_USEAVIARY') ?></label></p>
		<?php plxUtils::printSelect('filemanger_use_aviary',array('1'=>L_YES,'0'=>L_NO), $plxPlugin->getParam('filemanger_use_aviary'));?>
		<br />
        
        <p class="field"><label for="id_cleaviary"><?php echo $plxPlugin->lang('L_CLEAVIARY') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('filemanger_cle_aviary',$plxPlugin->getParam('filemanger_cle_aviary'),'text','40-255') ?>
		<strong>ex: ad37772ffb36fd775</strong>
        <p class="field"><label for="id_secretaviary"><?php echo $plxPlugin->lang('L_SECRETAVIARY') ?>&nbsp;:</label></p>
		<?php plxUtils::printInput('filemanger_secret_aviary',$plxPlugin->getParam('filemanger_secret_aviary'),'text','40-255') ?>
		<strong>ex: 1d37772fjk36fd886</strong>
       
        
        <p class="field"><?php echo $plxPlugin->lang('L_FILEMANAGER_RIGHT') ?></p>
        
        
       	<p class="field">
        <table class="table" style="width:320px">
        <thead>
            <tr>
                <th style="width:100px">Permission</th>
                <?php 
				foreach($aProfils as $key=>$val) { 
                	echo('<th style="width:60px">'.$val.'</th>');
                	
				 }
				  ?>
           
            </tr>
        </thead>
        <tbody>
        <?php
		$aTynitoolbar = array();
		foreach($aProfils as $key=>$val) { 
			$filemanagerperm = $plxPlugin->getParam('filemanager_permission'.$key);
			$aFilemanagerperm[$key] = $filemanagerperm=='' ? array() :  explode(' ', $filemanagerperm);
		}
		# Initialisation de l'ordre
		$aFMP = explode (' ',$plxPlugin->get_filemanager_permission_list());
		
		$num = 0;
		$order =0;
		foreach($aFMP as $permission) {
			$order = ++$num;
			echo '<tr class="line-'.($num%2).'">';
			
			echo '<td>'.$plxPlugin->getlang('L_'.strtoupper($permission)).'</td>';
			foreach($aProfils as $key=>$val) { 
				$checked = in_array($permission,$aFilemanagerperm[$key])?'checked="checked "':'';
				
				echo '<td><input type="checkbox" '.$checked.' id="filemanager_permission" name="filemanager_permission'.$key.'[]" value="'.$permission.'" /></td>';
			}
			
			echo '</tr>';
		}
		?>
		</tbody>
		</table>
        </p>
        
        
        
        
       
        
        
        
        
        
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>
