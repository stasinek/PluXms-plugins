<?php if(!defined('PLX_ROOT')) exit;

global $plxShow;
$StaticGaleries = $plxShow->plxMotor->plxPlugins->getInstance('staticgaleries');
$url = explode('/', plxUtils::getGets());

?>

<div id="StaticGalerie">

<?php
if(!empty($_POST)) {
	if($_POST['password'] == $StaticGaleries->aGaleries[$url[2]]['password']) {
		$_SESSION['static_galerie_password_'.$url[2]] = true;
	} else {
		$error = $StaticGaleries->getLang('L_WRONG_PASS');
	}
}	

// La galerie est privé et le mot de passe n'a pas été donné par le visiteur
if($StaticGaleries->aGaleries[$url[2]]['prive'] == 1 and empty($_SESSION['static_galerie_password_'.$url[2]])) { 
?>
	<p><?php echo $StaticGaleries->getLang('L_GALERIE_PROTECT') ?></p>

	<?php if($error):
	echo plxUtils::showMsg($error, 'error');
	endif; ?>

	<form action="#" method="post">
		<input name="password" type="password" value="">
		<input type="submit" class="button update" value="<?php echo $StaticGaleries->getLang('L_SEND') ?>" />
	</form>
</div>
<?php

// Le mot de passe a été donné par le visiteur ou alors la galerie est public
} elseif($_SESSION['static_galerie_password_'.$url[2]] or $StaticGaleries->aGaleries[$url[2]]['prive'] == 0) {
	// Si c'est une galerie parente
	if($StaticGaleries->aGaleries[$url[2]]['first'] == 1) {
		foreach($StaticGaleries->aGaleries as $k=>$v) {
			if($v['parent'] == $url[2]) {
				if($plxShow->plxMotor->aConf['userfolders'] == 1) {
					$root_dir = 'data/images/'.$StaticGaleries->aGaleries[$k]['user'].'/'.$StaticGaleries->aGaleries[$k]['root_dir'];
				} else {
					$root_dir = 'data/images/'.$StaticGaleries->aGaleries[$k]['root_dir'];
				}
?>
	<span class="listFile">
		<a href="galerie/<?php echo $v['name'].'/'.$k ?>" title="<?php echo plxUtils::strCheck($v['name']) ?>">
			<img src="<?php echo $root_dir.'/'.$v['representative'] ?>" alt="<?php echo plxUtils::strCheck($v['name']) ?>" />
		</a>
<?php
		if($StaticGaleries->aGaleries[$url[2]]['displayName'] == 1) {
			echo '<p>'.plxUtils::strCheck($v['menu_name']).'</p>';
		}
?>
		
	</span>
<?php
			}
		}
	// Si ce n'est pas une galerie parente
	} elseif($StaticGaleries->aGaleries[$url[2]]['first'] != 1) {
		// Création de la structure des sous-répertoires
		// 3 correspond aux nombres de paramètres passés en $_GET
		// avant les sous-dossiers
		$i = 3;
		while($i < count($url)) {
			$dirs = $dirs.'/'.$url[$i];
			$ariane = $ariane.'<a href="'.$url[0].'/'.$url[1].'/'.$url[2].$dirs.'" title="'.$url[$i].'">'.$url[$i].'</a> '.$StaticGaleries->aGaleries[$url[2]]['separateur'];
			$i++;
		}

		// Récupération des fichiers de la galerie
		$images = $StaticGaleries->getDirFiles($url[2], $dirs);
		if($images) {
			if(isset($StaticGaleries->aGaleries[$url[2]]['content'])) {
				echo '<p>'.$StaticGaleries->aGaleries[$url[2]]['content'].'</p>';
			}

			if($StaticGaleries->aGaleries[$url[2]]['activeSeparateur']) {
?>
	<p>
		<a href="<?php $plxShow->staticUrl() ?>">Accueil</a><?php echo $StaticGaleries->aGaleries[$url[2]]['separateur'] ?>
<?php
		// Si la galerie provient d'une parente alors
		// on affiche un lien retour
		if(!empty($StaticGaleries->aGaleries[$url[2]]['parent'])) {
			$parent = $StaticGaleries->aGaleries[$url[2]]['parent'];
			$name = $StaticGaleries->aGaleries[ $parent ]['name'];
			$menu_name = $StaticGaleries->aGaleries[ $parent ]['menu_name'];
?>
		<a href="<?php echo $url[0].'/'.$name.'/'.$parent ?>" title="<?php echo $menu_name ?>"><?php echo $menu_name ?></a><?php echo $StaticGaleries->aGaleries[$url[2]]['separateur'] ?>
<?php
		}
?>
		<a href="<?php echo $url[0].'/'.$url[1].'/'.$url[2] ?>" title="<?php echo $StaticGaleries->aGaleries[$url[2]]['menu_name'] ?>"><?php echo $StaticGaleries->aGaleries[$url[2]]['menu_name'] ?></a><?php echo $StaticGaleries->aGaleries[$url[2]]['separateur'] ?>
		<?php echo $ariane ?>
	</p>
<?php
			}
?>
	<div class="GalThumb">
		<div class="plxyoxview" id="plxyoxview">
<?php
			foreach($images as $v) {
				if($v['type'] == 'dir') {
?>
			<span class="listFile">
				<a href="<?php echo plxUtils::getGets().'/'.$v['name'] ?>" title="<?php echo plxUtils::strCheck($v['name']) ?>">
					<img src="plugins/staticgaleries/folders.png" alt="<?php echo plxUtils::strCheck($v['name']) ?>" />
				</a>
<?php
				if($StaticGaleries->aGaleries[$url[2]]['displayName'] == 1) {
					echo '<p>'.plxUtils::strCheck($v['menu_name']).'</p>';
				}
?>
			</span>
<?php
				} else {
?>
			<span class="listFile">
				<a href="<?php echo $v['path'] ?>" title="<?php echo plxUtils::strCheck($v['name']) ?>">
					<img src="<?php echo plxUtils::thumbName($v['path']) ?>" alt="<?php echo plxUtils::strCheck($v['name']) ?>"/>
				</a>
<?php
					if($StaticGaleries->aGaleries[$url[2]]['displayName'] == 1) {
						echo '<p>'.plxUtils::strCheck($v['menu_name']).'</p>';
					}
?>
			</span>
<?php
				}
			}
?>
		</div>
	</div>
<?php
		}
	} else {
		echo plxUtils::showMsg($StaticGaleries->getLang('L_ERREUR_WRONG_DIR'), 'error');
	} 
}
?>
</div>
