<?php if(!defined('PLX_ROOT')) exit;

$plxMotor = plxMotor::getInstance();
$StaticGaleries = $plxMotor->plxPlugins->getInstance('plxStaticGaleries');

# Control du token du formulaire
plxToken::validateFormToken($_POST);

// Edition d'une galerie
if($_POST['edit']) {
	if(empty($_POST['root_dir'])) {
		plxMsg::Error($plxPlugin->getLang('L_CREATE_GALERIE_ERR').' : '.$plxPlugin->getLang('L_ERR_ROOT_DIR'));
		header('Location: plugin.php?p=plxStaticGaleries&galerie='.$_POST['id']);
		exit;
	} elseif(empty($_POST['extensions'])) {
		plxMsg::Error($plxPlugin->getLang('L_CREATE_GALERIE_ERR').' : '.$plxPlugin->getLang('L_ERR_EXTENSIONS'));
		header('Location: plugin.php?p=plxStaticGaleries&galerie='.$_POST['id']);
		exit;
	} elseif(empty($_POST['menu_name'])) {
		plxMsg::Error($plxPlugin->getLang('L_CREATE_GALERIE_ERR').' : '.$plxPlugin->getLang('L_ERR_TITLE_GALERIE'));
		header('Location: plugin.php?p=plxStaticGaleries&galerie='.$_POST['id']);
		exit;
	} elseif(empty($_POST['menu_position'])) {
		plxMsg::Error($plxPlugin->getLang('L_CREATE_GALERIE_ERR').' : '.$plxPlugin->getLang('L_ERR_POSITION'));
		header('Location: plugin.php?p=plxStaticGaleries&galerie='.$_POST['id']);
		exit;
	} elseif($_POST['prive'] == 1 AND empty($_POST['password'])) {
		plxMsg::Error($plxPlugin->getLang('L_CREATE_GALERIE_ERR').' : '.$plxPlugin->getLang('L_ERR_EMPTY_PASSWORD'));	
		header('Location: plugin.php?p=plxStaticGaleries&galerie='.$_POST['id']);
		exit;
	} elseif($_POST['first'] == 1 AND !empty($_POST['parent'])) {
		plxMsg::Error($plxPlugin->getLang('L_CREATE_GALERIE_ERR').' : '.$plxPlugin->getLang('L_ERR_FIRST_GALERIE'));
		header('Location: plugin.php?p=plxStaticGaleries&galerie='.$_POST['id']);
		exit;
	} else {
		$StaticGaleries->editGalerie($_POST);
		header('Location: plugin.php?p=plxStaticGaleries');
		exit;
	}
} elseif($_POST['add']) {
	// Création ou modification des galeries
	$StaticGaleries->editGaleries($_POST);
	header('Location: plugin.php?p=plxStaticGaleries');
	exit;
}
?>

<h2><?php echo $plxPlugin->getInfo('title') ?></h2>
<form action="plugin.php?p=plxStaticGaleries" method="post" id="form_galeries">
	<table class="table">
		<thead>
			<tr>
				<th class="checkbox"><input type="checkbox" onclick="checkAll(this.form, 'idGalerie[]')" /></th>
				<th class="title"><?php echo $plxPlugin->getLang('L_GALERIE_ID') ?></th>
				<th><?php echo $plxPlugin->getLang('L_CONFIG_USERS_ID') ?></th>
				<th><?php echo $plxPlugin->getLang('L_GALERIE_NAME') ?></th>
				<th><?php echo $plxPlugin->getLang('L_ACTIVE') ?></th>
				<th><?php echo $plxPlugin->getLang('L_CAT_LIST_MENU') ?></th>
				<th><?php echo $plxPlugin->getLang('L_ACTION') ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$num = 0;
		if($StaticGaleries->aGaleries) {
			foreach($StaticGaleries->aGaleries as $k=>$v) {
				if (!$v['delete']) {
					echo '<tr class="line-'.($num%2).'">';
					echo '<td><input type="checkbox" name="idGalerie[]" value="'.$k.'" />';
					plxUtils::printInput('add', 'true', 'hidden', '4-4');
					plxUtils::printInput('galerieNum[]', $k, 'hidden');
					plxUtils::printInput('userNum[]', $v['user'], 'hidden');
					echo '</td>';
					echo '<td>'.$k.'</td>';
					echo '<td>'.$plxAdmin->aUsers[$v['user']]['name'].'</td>';
					echo '<td>';
					plxUtils::printInput($k.'_name', $v['name'], 'text', '15-50');
					echo '</td><td>';
					plxUtils::printSelect($k.'_active', array('1'=>L_YES,'0'=>L_NO), $v['active']);
					echo '</td><td>';
					plxUtils::printSelect($k.'_menu', array('1'=>L_DISPLAY,'0'=>L_HIDE), $v['menu']);
					echo '</td><td>';
					echo '<a href="plugin.php?p=plxStaticGaleries&galerie='.$k.'" title="Editer">Editer</a>';
					echo '</td></tr>';
				}	
			}

			# On récupère le dernier identifiant
			$a = array_keys($StaticGaleries->aGaleries);
			rsort($a);
		} else {
			$a['0'] = 0;
		}

		$new_galeries_id = str_pad($a['0']+1, 4, "0", STR_PAD_LEFT);
		?>
			<tr class="new">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><?php echo $plxPlugin->getLang('L_NEW_GALERY') ?></td>
				<td>
				<?php
					plxUtils::printInput('galerieNum[]', $new_galeries_id, 'hidden');
					plxUtils::printInput('add', 'true', 'hidden', '4-4');
					plxUtils::printInput($new_galeries_id.'_name', '', 'text', '15-50');
				?>
				</td>
				<td><?php plxUtils::printSelect($new_galeries_id.'_active', array('1'=>L_YES,'0'=>L_NO), '0'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
	</table>
	<p class="center">
		<?php echo plxToken::getTokenPostMethod() ?>
		<input class="button update" type="submit" name="update" value="<?php echo $plxPlugin->getLang('L_GALERIE_APPLY_BUTTON') ?>" />
	</p>
	<p>
		<?php plxUtils::printSelect('selection', array( '' => L_FOR_SELECTION, 'delete' => L_DELETE), '') ?>
		<input class="button submit" type="submit" name="submit" value="<?php echo L_OK ?>" />
	</p>
</form>

<?php 
	if(!empty($_GET['galerie'])) { # On vérifie l'existence de la galerie
		$id = plxUtils::strCheck($_GET['galerie']);
		if(isset($StaticGaleries->aGaleries[ $id ])) {
			# On récupère les templates la liste des images de la galeries
			if(!empty($StaticGaleries->aGaleries[$id]['root_dir'])) {
				if($plxMotor->aConf['userfolders'] == 1) {
					$root_dir = 'data/images/'.$StaticGaleries->aGaleries[$id]['user'].'/'.$StaticGaleries->aGaleries[$id]['root_dir'].'/';
				} else {
					$root_dir = 'data/images/'.$StaticGaleries->aGaleries[$id]['root_dir'].'/';
				}
				$ext = str_replace(',', '|', $StaticGaleries->aGaleries[$id]['extensions']);
				$files = plxGlob::getInstance(PLX_ROOT.$root_dir);
				if ($array = $files->query('/^(.*\.)tb.('.$ext.')$/D')) {
					foreach($array as $k=>$v)
						$aFiles[$v] = $v;
				}
				$active = false;
			} else {
				$active = true;
				$aFiles[0] = $plxPlugin->getLang('L_GALERIE_ROOT_DIR_EMPTY');
			}

			# On récupère les templates des pages statiques
			$files = plxGlob::getInstance(PLX_ROOT.'themes/'.$plxAdmin->aConf['style']);
			if ($array = $files->query('/^static(-[a-z0-9-_]+)?.php$/')) {
				foreach($array as $k=>$v)
					$aTemplates[$v] = $v;
			}
?>
<form action="plugin.php?p=plxStaticGaleries" method="post">
	<fieldset class="withlabel">
		<p>* obligatoire</p>
		<?php plxUtils::printInput('id', $id, 'hidden'); ?>
		<?php plxUtils::printInput('edit', 'true', 'hidden'); ?>
		<p><?php echo $plxPlugin->getLang('L_ACTIVE') ?></p>
		<?php plxUtils::printSelect('active', array('1'=>L_YES,'0'=>L_NO), $StaticGaleries->aGaleries[$id]['active']); ?>

		<p><?php echo $plxPlugin->getLang('L_FIRSTGALERIE') ?></p>
		<?php plxUtils::printSelect('first', array('1'=>L_YES,'0'=>L_NO), $StaticGaleries->aGaleries[$id]['first']); ?>

		<p><?php echo $plxPlugin->getLang('L_PARENT') ?></p>
		<select name="parent">
			<option></option>
		<?php
			foreach($StaticGaleries->aGaleries as $k=>$v) {
				if(!empty($v['menu_name'])) {
					if($StaticGaleries->aGaleries[$id]['parent'] == $k) {
						echo '<option value="'.$k.'" selected="selected">'.$v['menu_name'].'</option>';
					} else {
						echo '<option value="'.$k.'">'.$v['menu_name'].'</option>';
					}
				}
			}
		?>

		<?php plxUtils::printSelect('parent', $aParents, $StaticGaleries->aGaleries[$id]['parent']); ?>

		<p><?php echo $plxPlugin->getLang('L_MENU') ?></p>
		<?php plxUtils::printSelect('menu', array('1'=>L_YES,'0'=>L_NO), $StaticGaleries->aGaleries[$id]['menu']); ?>

		<p><?php echo $plxPlugin->getLang('L_ROOT_DIR') ?>*</p>
		<?php plxUtils::printInput('root_dir', plxUtils::strCheck($StaticGaleries->aGaleries[$id]['root_dir']), 'text'); ?>

		<p><?php echo $plxPlugin->getLang('L_EXTENSIONS') ?>*</p>
		<?php plxUtils::printInput('extensions', plxUtils::strCheck($StaticGaleries->aGaleries[$id]['extensions']), 'text'); ?>

		<p><?php echo $plxPlugin->getLang('L_TSORT') ?></p>
		<?php plxUtils::printSelect('sort',
			array(
				'sort'=>$plxPlugin->getLang('L_SORT'), 
				'rsort'=>$plxPlugin->getLang('L_RSORT')),
			$StaticGaleries->aGaleries[$id]['sort']); 
		?>

		<p><?php echo $plxPlugin->getLang('L_PRIVE') ?></p>
		<?php plxUtils::printSelect('prive', array('1'=>L_YES,'0'=>L_NO), $StaticGaleries->aGaleries[$id]['prive']); ?>

		<p><?php echo $plxPlugin->getLang('L_PASSWORD') ?></p>
		<?php plxUtils::printInput('password', plxUtils::strCheck($StaticGaleries->aGaleries[$id]['password']), 'text'); ?>

		<p><?php echo $plxPlugin->getLang('L_GALERIE_TITLE') ?>*</p>
		<?php plxUtils::printInput('menu_name', plxUtils::strCheck($StaticGaleries->aGaleries[$id]['menu_name']), 'text') ?>

		<p><?php echo $plxPlugin->getLang('L_GALERIE_POS') ?>*</p>
		<?php plxUtils::printInput('menu_position', plxUtils::strCheck($StaticGaleries->aGaleries[$id]['menu_position']), 'text', '2-5') ?>

		<p><?php echo $plxPlugin->getLang('L_ACTIVE_ARIANE') ?></p>
		<?php plxUtils::printSelect('activeSeparateur', array('1'=>L_YES,'0'=>L_NO), $StaticGaleries->aGaleries[$id]['activeSeparateur']); ?>

		<p><?php echo $plxPlugin->getLang('L_SEPARATEUR') ?></p>
		<?php plxUtils::printInput('separateur', plxUtils::strCheck($StaticGaleries->aGaleries[$id]['separateur']), 'text'); ?>

		<p><?php echo $plxPlugin->getLang('L_GALERIE_TEMPLATE') ?></p>
		<?php plxUtils::printSelect('template', $aTemplates, $StaticGaleries->aGaleries[$id]['template']) ?>

		<p><?php echo $plxPlugin->getLang('L_GALERIE_REPRESENTATIVE') ?></p>
		<?php plxUtils::printSelect('representative', $aFiles, $StaticGaleries->aGaleries[$id]['representative'], $active) ?>

		<p><?php echo $plxPlugin->getLang('L_DISPLAY_NAME') ?></p>
		<?php plxUtils::printSelect('displayName', array('1'=>L_YES,'0'=>L_NO), $StaticGaleries->aGaleries[$id]['displayName']); ?>

		<p><?php echo $plxPlugin->getLang('L_GALERIE_TXT') ?></p>
		<?php plxUtils::printArea('content', plxUtils::strCheck($StaticGaleries->aGaleries[$id]['content']), 35, 28); ?>

		<?php plxUtils::printInput('user', $StaticGaleries->aGaleries[$id]['user'], 'hidden'); ?>
		<?php plxUtils::printInput('id', $_GET['galerie'], 'hidden'); ?>
		<?php plxUtils::printInput('name', $StaticGaleries->aGaleries[$id]['name'], 'hidden'); ?>
		<?php echo plxToken::getTokenPostMethod() ?>
	</fieldset>
	<input type="submit" class="button update" value="<?php echo $plxPlugin->getLang('L_SAVE') ?>" />
</form>
<?php
		} else { # Sinon, on redirige
			plxMsg::Error(L_CAT_UNKNOWN);
			header('Location: plugin.php?p=plxStaticGaleries&galerie='.$id);
			exit;
		}
	}
?>
