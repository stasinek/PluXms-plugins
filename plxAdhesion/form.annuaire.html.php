<?php
/**
 * Plugin adhesion
 *
 * @version	1.5
 * @date	07/10/2013
 * @author	Stephane F, Cyril MAGUIRE
 **/
if(!defined('PLX_ROOT')) exit; 

$plxMotor = plxMotor::getInstance();
$plxPlugin = $plxMotor->plxPlugins->aPlugins["adhesion"];

if($plxPlugin->getParam('showAnnuaire') != 'on') {
	header('Location:'.$plxMotor->urlRewrite('?erreur'));
	exit();
}

//Si l'utilisateur n'est pas connecté, on affiche le message pour demander la connexion
if ( ( !isset($_SESSION['lockArticles']['articles']) && !isset($_SESSION['lockArticles']['categorie']) ) || ($_SESSION['lockArticles']['articles'] != 'on' && $_SESSION['lockArticles']['categorie'] != 'on') ) :
	echo '<p class="locked">'.$plxPlugin->getLang('L_NEED_AUTH').'</p>';
else :
$r = $plxPlugin->getAdherents('/^[0-9]{5}.(.[a-z-]+){2}.[0-9]{10}.xml$/');?>

<table class="table" summary="membres">
	<thead>
		<tr class="new">
			<th><?php $plxPlugin->lang('L_ADMIN_LIST_NAME') ?>
				<?php $plxPlugin->lang('L_ADMIN_LIST_FIRST_NAME') ?><br/>
				<?php $plxPlugin->lang('L_ADMIN_LIST_ACTIVITY') ?></th>
			<th><?php $plxPlugin->lang('L_ADMIN_LIST_STRUCTURE') ?><br/>
				<?php $plxPlugin->lang('L_ADMIN_LIST_DPT') ?></th>
			<th><?php $plxPlugin->lang('L_ADMIN_LIST_ADRESSE') ?><br/>
				<?php $plxPlugin->lang('L_ADMIN_LIST_ZIP_CODE') ?>&nbsp;
				<?php $plxPlugin->lang('L_ADMIN_LIST_CITY') ?></th>
			<th style="width:12%;"><?php $plxPlugin->lang('L_ADMIN_LIST_TEL') ?><br/>
				<?php $plxPlugin->lang('L_ADMIN_LIST_TEL_OFFICE') ?></th>
			<th><?php $plxPlugin->lang('L_ADMIN_LIST_MAIL') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$num = 0;
		if ($r) {

			foreach($plxPlugin->plxRecord_adherents->result as $k=>$v) {
				if ($v['validation'] == 1 && $v['coordonnees'] == 'rec') {
				$ordre = ++$num;
				echo '<tr class="line-'.($num%2).'">';
				echo '<td>'.plxUtils::strCheck($v['nom']).'<br/>'.
				plxUtils::strCheck($v['prenom']).'<br/>'.
				plxUtils::strCheck($v['activite']);
				echo '</td><td>';
				echo plxUtils::strCheck($v['etablissement']).'<br/>'.
				plxUtils::strCheck($v['service']);
				echo '</td><td>';
				echo plxUtils::strCheck($v['adresse1']).'<br/>'.
				plxUtils::strCheck($v['adresse2']).'<br/>'.
				plxUtils::strCheck($v['cp']).' '.
				plxUtils::strCheck($v['ville']);
				echo '</td><td>';
				echo plxUtils::strCheck($v['tel']).'<br/>'.
				plxUtils::strCheck($v['tel_office']);
				echo '</td><td>';
				echo $plxPlugin->badEmail(plxUtils::strCheck($v['mail']));
				echo '</td></tr>';
				}
			}
		}
		else {
			echo '<tr><td colspan="8" style="text-align:center;"><strong>'.$plxPlugin->getLang('L_ADMIN_NO_VALIDATION').'</strong></td></tr>';
			$a[1] = 0;
		}?>

	</tbody>
	</table>
<?php endif; ?>