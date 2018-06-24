<?php
//============================================================+
//Impression en xlsx ou ods
//============================================================+
if (isset($_GET['print']) && ($_GET['print']== 'xlsx' ||$_GET['print'] == 'ods')) :

$tpl = $_GET['print'];
$plxMotor->mode = 'print';

$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

$TBS->NoErr = true;

$suffix = $this->getParam('nom_asso');
$debug = 0;

// Retrieve the template to open
$template = ($tpl == 'xlsx') ? 'tpl_ms_excel.xlsx' : (($tpl == 'xls') ? 'tpl_ms_excel.xml' : 'tpl_oo_spreadsheet.ods');
//$template = 'tpl_ms_excel.xlsx';
//$template = 'tpl_oo_spreadsheet.ods';
$template = basename($template);
$chemin = PLX_PLUGINS.'adhesion/opentbs/'.$template;
$x = pathinfo($chemin);
$template_ext = $x['extension'];
if (substr($template,0,4)!=='tpl_') exit("Wrong file.");
if (!file_exists($chemin)) exit($chemin." : File does not exist.");

// Prepare some data
$i = 0;
// Load the template
$TBS->LoadTemplate($chemin, OPENTBS_ALREADY_UTF8);

// Define the name of the output file
$file_name = 'Liste_des_adherents_association_'.$this->getParam('nom_asso').'.'.$x['extension'];

// Merge data
$e[]['nom_asso'] = $this->getParam('nom_asso');
foreach ($this->plxRecord_adherents->result as $k => $v) {
	$this->plxRecord_adherents->result[$k]['nom'] = strtoupper($v['nom']);
	$this->plxRecord_adherents->result[$k]['prenom'] = ucfirst(strtolower($v['prenom']));
	$this->plxRecord_adherents->result[$k]['choix'] = ($v['choix'] == 'adhesion') ? 'Souhaite devenir adhérent' : ($v['choix'] == 'renouveler') ? 'Souhaite renouveler son adhésion' : 'Ne souhaite plus être membre';
	$this->plxRecord_adherents->result[$k]['mailing'] = ($v['mailing'] == 'maillist') ? 'Accepte de recevoir des e-mails' : 'Refuse de recevoir des e-mails';
	$this->plxRecord_adherents->result[$k]['coordonnees'] = ($v['coordonnees'] == 'rec') ? 'Accepte que ses coordonnées professionnelles figurent sur le site de l’association' : 'Refuse que ses coordonnées professionnelles figurent sur le site de l’association';
	$this->plxRecord_adherents->result[$k]['validation'] = ($v['validation'] == 1) ? 'Oui' : 'Non';
	if ($this->getParam('typeAnnuaire')=='') {
		$this->plxRecord_adherents->result[$k]['activite'] = '';
		$this->plxRecord_adherents->result[$k]['etablissement'] = '';
		$this->plxRecord_adherents->result[$k]['service'] = '';
		$this->plxRecord_adherents->result[$k]['tel_office'] = '';
	}
}
$TBS->MergeBlock('e', $e);
$TBS->MergeBlock('a', $this->plxRecord_adherents->result);

// specific merges depending to the document
if ($template_ext=='xlsx') {

	// change the current sheet
	$TBS->PlugIn(OPENTBS_SELECT_SHEET, 1);
	
} elseif ($template_ext=='xml') {
	
	// Final merge and download file
	$TBS->Show(TBS_EXCEL_DOWNLOAD, $file_name);
	exit();

} elseif ($template_ext=='doc') {

	// delete comments
	$TBS->PlugIn(OPENTBS_DELETE_COMMENTS);

} elseif ($template_ext=='docx') {

	// delete comments
	$TBS->PlugIn(OPENTBS_DELETE_COMMENTS);

} elseif ($template_ext=='odt') {

	// delete comments
	$TBS->PlugIn(OPENTBS_DELETE_COMMENTS);
	
}

// Output as a download file (some automatic fields are merged here)
if ($debug==3) { // debug mode 3
	$TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
} elseif ($suffix===$this->getParam('nom_asso')) {
	// download
	$file_name = str_replace('tpl_',$suffix.'_',$file_name);
	$TBS->Show(OPENTBS_DOWNLOAD, $file_name);
} else {
	// save as file
	$file_name = str_replace('tpl_',$suffix.'_',$file_name);
	$TBS->Show(OPENTBS_FILE+TBS_EXIT, $file_name);
}
//============================================================+
//Fin Impression en xlsx ou ods
//============================================================+
//============================================================+
//Impression en xls
//============================================================+
elseif (isset($_GET['print']) && ($_GET['print']== 'xls') ) :

$tpl = $_GET['print'];
$plxMotor->mode = 'print';

$suffix = $this->getParam('nom_asso');
$debug = 0;

// Retrieve the template to open
$template = ($tpl == 'xls') ? 'tpl_ms_excel.xml' : '';
$template = basename($template);
$chemin = PLX_PLUGINS.'adhesion/opentbs/'.$template;
$x = pathinfo($chemin);
$template_ext = $x['extension'];
if (substr($template,0,4)!=='tpl_') exit("Wrong file.");
if (!file_exists($chemin)) exit($chemin." : File does not exist.");

// Prepare some data
$i = 0;

// Define the name of the output file
$file_name = 'Liste_des_adherents_association_'.$this->getParam('nom_asso').'.xls';

$TBS = new clsTinyButStrong;// new instance of TBS

// Install the Excel plug-in (must be before LoadTemplate)
$TBS->PlugIn(TBS_INSTALL, TBS_EXCEL);

// Load the Excel template
$TBS->LoadTemplate($chemin, OPENTBS_ALREADY_UTF8);

// Merge data
$e[]['nom_asso'] = $this->getParam('nom_asso');
foreach ($this->plxRecord_adherents->result as $k => $v) {
	$this->plxRecord_adherents->result[$k]['nom'] = strtoupper($v['nom']);
	$this->plxRecord_adherents->result[$k]['prenom'] = ucfirst(strtolower($v['prenom']));
	$this->plxRecord_adherents->result[$k]['choix'] = ($v['choix'] == 'adhesion') ? 'Souhaite devenir adhérent' : ($v['choix'] == 'renouveler') ? 'Souhaite renouveler son adhésion' : 'Ne souhaite plus être membre';
	$this->plxRecord_adherents->result[$k]['mailing'] = ($v['mailing'] == 'maillist') ? 'Accepte de recevoir des e-mails' : 'Refuse de recevoir des e-mails';
	$this->plxRecord_adherents->result[$k]['coordonnees'] = ($v['coordonnees'] == 'rec') ? 'Accepte que ses coordonnées professionnelles figurent sur le site de l’association' : 'Refuse que ses coordonnées professionnelles figurent sur le site de l’association';
	$this->plxRecord_adherents->result[$k]['validation'] = ($v['validation'] == 1) ? 'Oui' : 'Non';
}
$TBS->MergeBlock('e', $e);
$TBS->MergeBlock('a', $this->plxRecord_adherents->result);

// Final merge and download file
$TBS->Show(TBS_EXCEL_DOWNLOAD, $file_name);

//============================================================+
//Fin Impression en xls
//============================================================+
else : 
$plxMotor->error404(L_UNKNOWN_ARTICLE);
endif;
?>