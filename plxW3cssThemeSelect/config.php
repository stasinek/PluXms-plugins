<?php if(!defined('PLX_ROOT')) exit; ?>

<?php
# Control du token du formulaire
plxToken::validateFormToken($_POST);
if(!empty($_POST)) {
$plxPlugin->setParam('css', $_POST['css'], 'cdata');
$plxPlugin->setParam('w3themel5color', $_POST['w3themel5color'], 'cdata');
$plxPlugin->setParam('w3themel5backgroundcolor', $_POST['w3themel5backgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themel4color', $_POST['w3themel4color'], 'cdata');
$plxPlugin->setParam('w3themel4backgroundcolor', $_POST['w3themel4backgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themel3color', $_POST['w3themel3color'], 'cdata');
$plxPlugin->setParam('w3themel3backgroundcolor', $_POST['w3themel3backgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themel2color', $_POST['w3themel2color'], 'cdata');
$plxPlugin->setParam('w3themel2backgroundcolor', $_POST['w3themel2backgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themel1color', $_POST['w3themel1color'], 'cdata');
$plxPlugin->setParam('w3themel1backgroundcolor', $_POST['w3themel1backgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themecolor', $_POST['w3themecolor'], 'cdata');
$plxPlugin->setParam('w3themebackgroundcolor', $_POST['w3themebackgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themed5color', $_POST['w3themed5color'], 'cdata');
$plxPlugin->setParam('w3themed5backgroundcolor', $_POST['w3themed5backgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themed4color', $_POST['w3themed4color'], 'cdata');
$plxPlugin->setParam('w3themed4backgroundcolor', $_POST['w3themed4backgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themed3color', $_POST['w3themed3color'], 'cdata');
$plxPlugin->setParam('w3themed3backgroundcolor', $_POST['w3themed3backgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themed2color', $_POST['w3themed2color'], 'cdata');
$plxPlugin->setParam('w3themed2backgroundcolor', $_POST['w3themed2backgroundcolor'], 'cdata');
$plxPlugin->setParam('w3themed1color', $_POST['w3themed1color'], 'cdata');
$plxPlugin->setParam('w3themed1backgroundcolor', $_POST['w3themed1backgroundcolor'], 'cdata');

$plxPlugin->setParam('w3themelightcolor', $_POST['w3themelightcolor'], 'cdata');
$plxPlugin->setParam('w3themelightbackgroundcolor', $_POST['w3themelightbackgroundcolor'], 'cdata');

$plxPlugin->setParam('w3themedarkcolor', $_POST['w3themedarkcolor'], 'cdata');
$plxPlugin->setParam('w3themedarkbackgroundcolor', $_POST['w3themedarkbackgroundcolor'], 'cdata');

$plxPlugin->setParam('w3themeactioncolor', $_POST['w3themeactioncolor'], 'cdata');
$plxPlugin->setParam('w3themeactionbackgroundcolor', $_POST['w3themeactionbackgroundcolor'], 'cdata');

$plxPlugin->setParam('w3themehovercolor', $_POST['w3themehovercolor'], 'cdata');
$plxPlugin->setParam('w3themehoverbackgroundcolor', $_POST['w3themehoverbackgroundcolor'], 'cdata');

$plxPlugin->setParam('w3themetextcolor', $_POST['w3themetextcolor'], 'cdata');
$plxPlugin->setParam('w3themehovertext', $_POST['w3themehovertext'], 'cdata');

$plxPlugin->setParam('w3themebordercolor', $_POST['w3themebordercolor'], 'cdata');
$plxPlugin->setParam('w3themehoverborder', $_POST['w3themehoverborder'], 'cdata');


$plxPlugin->saveParams();
header('Location: parametres_plugin.php?p=plxW3cssThemeSelect');
exit;
}
?>

<h2>Configurer le thème</h2>
<form action="parametres_plugin.php?p=plxW3cssThemeSelect" method="post">

<div class="scrollable-table" >

	<table class="table full-width" oninput="previewTheme()">
	<thead>
			<tr>

				<th>Description</th>
				<th>Couleur</th>
				<th>Description</th>
				<th>Couleur</th>
				<th>Prévisualisation</th>
			</tr>
	</thead>
	<tbody>
			<tr>
				<td>w3-theme-l5:color</td>
				<td><?php plxUtils::printInput('w3themel5color',$plxPlugin->getParam('w3themel5color'),'text','20-40', false) ?></td>
				<td>w3-theme-l5:background</td>
				<td><?php plxUtils::printInput('w3themel5backgroundcolor',$plxPlugin->getParam('w3themel5backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemel5">theme l5</td>
			</tr>		
			<tr>
				<td>w3-theme-l4:color</td>
				<td><?php plxUtils::printInput('w3themel4color',$plxPlugin->getParam('w3themel4color'),'text','20-40', false) ?></td>
				<td>w3-theme-l4:background</td>
				<td><?php plxUtils::printInput('w3themel4backgroundcolor',$plxPlugin->getParam('w3themel4backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemel4">theme l4</td>
			</tr>		
			<tr>
				<td>w3-theme-l3:color</td>
				<td><?php plxUtils::printInput('w3themel3color',$plxPlugin->getParam('w3themel3color'),'text','20-40', false) ?></td>
				<td>w3-theme-l3:background</td>
				<td><?php plxUtils::printInput('w3themel3backgroundcolor',$plxPlugin->getParam('w3themel3backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemel3">theme l3</td>
			</tr>		
			<tr>
				<td>w3-theme-l2:color</td>
				<td><?php plxUtils::printInput('w3themel2color',$plxPlugin->getParam('w3themel2color'),'text','20-40', false) ?></td>
				<td>w3-theme-l2:background</td>
				<td><?php plxUtils::printInput('w3themel2backgroundcolor',$plxPlugin->getParam('w3themel2backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemel2">theme l2</td>
			</tr>		
			<tr>
				<td>w3-theme-l1:color</td>
				<td><?php plxUtils::printInput('w3themel1color',$plxPlugin->getParam('w3themel1color'),'text','20-40', false) ?></td>
				<td>w3-theme-l1:background</td>
				<td><?php plxUtils::printInput('w3themel1backgroundcolor',$plxPlugin->getParam('w3themel1backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemel1">theme l1</td>
			</tr>		
			<tr>
				<td>w3-theme:color</td>
				<td><?php plxUtils::printInput('w3themecolor',$plxPlugin->getParam('w3themecolor'),'text','20-40', false) ?></td>
				<td>w3-theme:background</td>
				<td><?php plxUtils::printInput('w3themebackgroundcolor',$plxPlugin->getParam('w3themebackgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewtheme">theme</td>
			</tr>		
			<tr>
				<td>w3-theme-d1:color</td>
				<td><?php plxUtils::printInput('w3themed1color',$plxPlugin->getParam('w3themed1color'),'text','20-40', false) ?></td>
				<td>w3-theme-d1:background</td>
				<td><?php plxUtils::printInput('w3themed1backgroundcolor',$plxPlugin->getParam('w3themed1backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemed1">theme d1</td>
			</tr>		
			<tr>
				<td>w3-theme-d2:color</td>
				<td><?php plxUtils::printInput('w3themed2color',$plxPlugin->getParam('w3themed2color'),'text','20-40', false) ?></td>
				<td>w3-theme-d2:background</td>
				<td><?php plxUtils::printInput('w3themed2backgroundcolor',$plxPlugin->getParam('w3themed2backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemed2">theme d2</td>
			</tr>			
			<tr>
				<td>w3-theme-d3:color</td>
				<td><?php plxUtils::printInput('w3themed3color',$plxPlugin->getParam('w3themed3color'),'text','20-40', false) ?></td>
				<td>w3-theme-d3:background</td>
				<td><?php plxUtils::printInput('w3themed3backgroundcolor',$plxPlugin->getParam('w3themed3backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemed3">theme d3</td>
			</tr>		
			<tr>
				<td>w3-theme-d4:color</td>
				<td><?php plxUtils::printInput('w3themed4color',$plxPlugin->getParam('w3themed4color'),'text','20-40', false) ?></td>
				<td>w3-theme-d4:background</td>
				<td><?php plxUtils::printInput('w3themed4backgroundcolor',$plxPlugin->getParam('w3themed4backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemed4">theme d4</td>
			</tr>
			<tr>
				<td>w3-theme-d5:color</td>
				<td><?php plxUtils::printInput('w3themed5color',$plxPlugin->getParam('w3themed5color'),'text','20-40', false) ?></td>
				<td>w3-theme-d5:background</td>
				<td><?php plxUtils::printInput('w3themed5backgroundcolor',$plxPlugin->getParam('w3themed5backgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemed5">theme d5</td>
			</tr>	

			<tr>
				<td>w3-theme-light:color</td>
				<td><?php plxUtils::printInput('w3themelightcolor',$plxPlugin->getParam('w3themelightcolor'),'text','20-40', false) ?></td>
				<td>w3-theme-light:background</td>
				<td><?php plxUtils::printInput('w3themelightbackgroundcolor',$plxPlugin->getParam('w3themelightbackgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemelight">theme light</td>
			</tr>	

			<tr>
				<td>w3-theme-dark:color</td>
				<td><?php plxUtils::printInput('w3themedarkcolor',$plxPlugin->getParam('w3themedarkcolor'),'text','20-40', false) ?></td>
				<td>w3-theme-dark:background</td>
				<td><?php plxUtils::printInput('w3themedarkbackgroundcolor',$plxPlugin->getParam('w3themedarkbackgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemedark">theme dark</td>
			</tr>	

			<tr>
				<td>w3-theme-action:color</td>
				<td><?php plxUtils::printInput('w3themeactioncolor',$plxPlugin->getParam('w3themeactioncolor'),'text','20-40', false) ?></td>
				<td>w3-theme-action:background</td>
				<td><?php plxUtils::printInput('w3themeactionbackgroundcolor',$plxPlugin->getParam('w3themeactionbackgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemeaction">theme action</td>
			</tr>	

			<tr>
				<td>w3-hover-theme:color</td>
				<td><?php plxUtils::printInput('w3themehovercolor',$plxPlugin->getParam('w3themehovercolor'),'text','20-40', false) ?></td>
				<td>w3-hover-theme:background</td>
				<td><?php plxUtils::printInput('w3themehoverbackgroundcolor',$plxPlugin->getParam('w3themehoverbackgroundcolor'),'text','20-40', false) ?></td>
				<td id="previewthemehover"><!--theme hover--></td>
			</tr>	

			<tr>
				<td>w3-text-theme:color</td>
				<td><?php plxUtils::printInput('w3themetextcolor',$plxPlugin->getParam('w3themetextcolor'),'text','20-40', false) ?></td>
				<td>w3-hover-text-theme:hover:color</td>
				<td><?php plxUtils::printInput('w3themehovertext',$plxPlugin->getParam('w3themehovertext'),'text','20-40', false) ?></td>
				<td id="previewthemetext"><!--theme text--></td>
			</tr>	

			<tr>
				<td>w3-border-theme:border-color</td>
				<td><?php plxUtils::printInput('w3themebordercolor',$plxPlugin->getParam('w3themebordercolor'),'text','20-40', false) ?></td>
				<td>w3-hover-border-theme:hover:border-color</td>
				<td><?php plxUtils::printInput('w3themehoverborder',$plxPlugin->getParam('w3themehoverborder'),'text','20-40', false) ?></td>
				<td id="previewthemeborder"><!--theme border--></td>
			</tr>	
			
	</tbody>
	</table>
</div>
<script type="text/javascript">
function previewTheme(){
		document.getElementById('previewthemel5').style.color =  document.getElementById('id_w3themel5color').value;
		document.getElementById('previewthemel5').style.backgroundColor =  document.getElementById('id_w3themel5backgroundcolor').value;

		document.getElementById('previewthemel4').style.color =  document.getElementById('id_w3themel4color').value;
		document.getElementById('previewthemel4').style.backgroundColor =  document.getElementById('id_w3themel4backgroundcolor').value;

		document.getElementById('previewthemel3').style.color =  document.getElementById('id_w3themel3color').value;
		document.getElementById('previewthemel3').style.backgroundColor =  document.getElementById('id_w3themel3backgroundcolor').value;

		document.getElementById('previewthemel2').style.color =  document.getElementById('id_w3themel2color').value;
		document.getElementById('previewthemel2').style.backgroundColor =  document.getElementById('id_w3themel2backgroundcolor').value;

		document.getElementById('previewthemel1').style.color =  document.getElementById('id_w3themel1color').value;
		document.getElementById('previewthemel1').style.backgroundColor =  document.getElementById('id_w3themel1backgroundcolor').value;

		document.getElementById('previewtheme').style.color =  document.getElementById('id_w3themecolor').value;
		document.getElementById('previewtheme').style.backgroundColor =  document.getElementById('id_w3themebackgroundcolor').value;

		document.getElementById('previewthemed5').style.color =  document.getElementById('id_w3themed5color').value;
		document.getElementById('previewthemed5').style.backgroundColor =  document.getElementById('id_w3themed5backgroundcolor').value;

		document.getElementById('previewthemed4').style.color =  document.getElementById('id_w3themed4color').value;
		document.getElementById('previewthemed4').style.backgroundColor =  document.getElementById('id_w3themed4backgroundcolor').value;

		document.getElementById('previewthemed3').style.color =  document.getElementById('id_w3themed3color').value;
		document.getElementById('previewthemed3').style.backgroundColor =  document.getElementById('id_w3themed3backgroundcolor').value;

		document.getElementById('previewthemed2').style.color =  document.getElementById('id_w3themed2color').value;
		document.getElementById('previewthemed2').style.backgroundColor =  document.getElementById('id_w3themed2backgroundcolor').value;

		document.getElementById('previewthemed1').style.color =  document.getElementById('id_w3themed1color').value;
		document.getElementById('previewthemed1').style.backgroundColor =  document.getElementById('id_w3themed1backgroundcolor').value;

		document.getElementById('previewthemelight').style.color =  document.getElementById('id_w3themelightcolor').value;
		document.getElementById('previewthemelight').style.backgroundColor =  document.getElementById('id_w3themelightbackgroundcolor').value;

		document.getElementById('previewthemedark').style.color =  document.getElementById('id_w3themedarkcolor').value;
		document.getElementById('previewthemedark').style.backgroundColor =  document.getElementById('id_w3themedarkbackgroundcolor').value;

		document.getElementById('previewthemeaction').style.color =  document.getElementById('id_w3themeactioncolor').value;
		document.getElementById('previewthemeaction').style.backgroundColor =  document.getElementById('id_w3themeactionbackgroundcolor').value;

		/*
		document.getElementById('previewthemehover').style.color =  document.getElementById('id_w3themehovercolor').value;
		document.getElementById('previewthemehover').style.backgroundColor =  document.getElementById('id_w3themehoverbackgroundcolor').value;

		document.getElementById('previewthemetext').style.color =  document.getElementById('id_w3themetextcolor').value;
		document.getElementById('previewthemetext').style.backgroundColor =  document.getElementById('id_w3themehovertext').value;

		document.getElementById('previewthemeborder').style.color =  document.getElementById('id_w3themebordercolor').value;
		document.getElementById('previewthemeborder').style.backgroundColor =  document.getElementById('id_w3themehoverborder').value;
		*/
}
window.onload = function() {previewTheme(); };
</script>

<?php
// w3css 2.0
$emplacement = PLX_ROOT.'themes/w3css/css/w3-custom.css'; 
	
$w3themel5color = $plxPlugin->getParam('w3themel5color');
$w3themel5backgroundcolor = $plxPlugin->getParam('w3themel5backgroundcolor');
$w3themel4color = $plxPlugin->getParam('w3themel4color');
$w3themel4backgroundcolor = $plxPlugin->getParam('w3themel4backgroundcolor');
$w3themel3color = $plxPlugin->getParam('w3themel3color');
$w3themel3backgroundcolor = $plxPlugin->getParam('w3themel3backgroundcolor');
$w3themel2color = $plxPlugin->getParam('w3themel2color');
$w3themel2backgroundcolor = $plxPlugin->getParam('w3themel2backgroundcolor');
$w3themel1color = $plxPlugin->getParam('w3themel1color');
$w3themel1backgroundcolor = $plxPlugin->getParam('w3themel1backgroundcolor');

$w3themecolor = $plxPlugin->getParam('w3themecolor');
$w3themebackgroundcolor = $plxPlugin->getParam('w3themebackgroundcolor');

$w3themed5color = $plxPlugin->getParam('w3themed5color');
$w3themed5backgroundcolor = $plxPlugin->getParam('w3themed5backgroundcolor');
$w3themed4color = $plxPlugin->getParam('w3themed4color');
$w3themed4backgroundcolor = $plxPlugin->getParam('w3themed4backgroundcolor');
$w3themed3color = $plxPlugin->getParam('w3themed3color');
$w3themed3backgroundcolor = $plxPlugin->getParam('w3themed3backgroundcolor');
$w3themed2color = $plxPlugin->getParam('w3themed2color');
$w3themed2backgroundcolor = $plxPlugin->getParam('w3themed2backgroundcolor');
$w3themed1color = $plxPlugin->getParam('w3themed1color');
$w3themed1backgroundcolor = $plxPlugin->getParam('w3themed1backgroundcolor');

$w3themelightcolor = $plxPlugin->getParam('w3themelightcolor');
$w3themelightbackgroundcolor = $plxPlugin->getParam('w3themelightbackgroundcolor');
$w3themedarkcolor = $plxPlugin->getParam('w3themedarkcolor');
$w3themedarkbackgroundcolor = $plxPlugin->getParam('w3themedarkbackgroundcolor');
$w3themeactioncolor = $plxPlugin->getParam('w3themeactioncolor');
$w3themeactionbackgroundcolor = $plxPlugin->getParam('w3themeactionbackgroundcolor');

$w3themehovercolor = $plxPlugin->getParam('w3themehovercolor');
$w3themehoverbackgroundcolor = $plxPlugin->getParam('w3themehoverbackgroundcolor');

$w3themetextcolor = $plxPlugin->getParam('w3themetextcolor');
$w3themehovertext = $plxPlugin->getParam('w3themehovertext');
$w3themebordercolor = $plxPlugin->getParam('w3themebordercolor');
$w3themehoverborder = $plxPlugin->getParam('w3themehoverborder');

$contenu = <<<CSS

/* Couleurs */

.w3-theme-l5 {color:$w3themel5color !important; background-color:$w3themel5backgroundcolor !important}
.w3-theme-l4 {color:$w3themel4color !important; background-color:$w3themel4backgroundcolor !important}
.w3-theme-l3 {color:$w3themel3color !important; background-color:$w3themel3backgroundcolor !important}
.w3-theme-l2 {color:$w3themel2color !important; background-color:$w3themel2backgroundcolor !important}
.w3-theme-l1 {color:$w3themel1color !important; background-color:$w3themel1backgroundcolor !important}
.w3-theme-d1 {color:$w3themed1color !important; background-color:$w3themed1backgroundcolor !important}
.w3-theme-d2 {color:$w3themed2color !important; background-color:$w3themed2backgroundcolor !important}
.w3-theme-d3 {color:$w3themed3color !important; background-color:$w3themed3backgroundcolor !important}
.w3-theme-d4 {color:$w3themed4color !important; background-color:$w3themed4backgroundcolor !important}
.w3-theme-d5 {color:$w3themed5color !important; background-color:$w3themed5backgroundcolor !important}

.w3-theme-light {color:$w3themelightcolor !important; background-color:$w3themelightbackgroundcolor  !important}
.w3-theme-dark {color:$w3themedarkcolor !important; background-color:$w3themedarkbackgroundcolor !important}
.w3-theme-action {color:$w3themeactioncolor !important; background-color:$w3themeactionbackgroundcolor !important}

.w3-theme {color:$w3themecolor !important; background-color:$w3themebackgroundcolor !important}
.w3-text-theme {color:$w3themetextcolor !important}
.w3-theme-border {border-color:$w3themebordercolor !important}

.w3-hover-theme:hover {color:$w3themehovercolor !important; background-color:$w3themehoverbackgroundcolor !important}
.w3-hover-text-theme:hover {color:$w3themehovertext !important}
.w3-hover-border-theme:hover {border-color:$w3themehoverborder !important}

/* Liens */
article a, .comments .content_com a {color:$w3themetextcolor;} 
a, nav a:hover {text-decoration: none;}
a:hover, a:focus {text-decoration: underline;}

/* Images */
img {height: auto; max-width: 100%;}

/* Menu */
nav .menu li {display: block;}
.menu .menu-large  {margin: 0; padding: 0;}
.menu ul li#static-home {color:$w3themel5color; background-color:$w3themel5backgroundcolor;}
.w3-dropdown-hover:hover > .w3-button:first-child,.w3-dropdown-click:hover > .w3-button:first-child{background-color:$w3themel3backgroundcolor ;color:$w3themel3color}
.menu .menu-small {margin:0; padding:0;}
.menu .menu-small div div a.w3-button {padding-left: 48px}
.menu span.w3-button:hover {cursor: default;}
.menu li a.w3-button {border-bottom: 1px solid transparent;} 
.menu li a.w3-button:hover {border-bottom: 1px solid ; border-bottom-color: $w3themel4backgroundcolor;}

/* Pagination */
.pagination div.w3-border {border-color: $w3themed5backgroundcolor !important;} /* w3-theme-d5 */
.pagination .p_page {cursor: default;}
.pagination span.p_first a {border-right: 1px solid;}
.pagination span.p_last a {border-left: 1px solid;}

/** Icones dans le menu **/
.menu ul.menu-large li#static-home a:before {font-family: ForkAwesome; content:"\\f015   ";}
.menu ul.menu-large li span.menu-group:after, .menu ul.menu-small li span.menu-group:after {font-family: ForkAwesome;  content:"    \\f0d7"}
.menu ul.menu-large li#static-search a:before, .menu ul.menu-small li#alt_static-search a:before {font-family: ForkAwesome; content:"\\f002   ";}
/* .menu ul.menu-large li#static-1 a:before, .menu ul.menu-small li#alt_static-1 a:before {font-family: ForkAwesome; content:"\\f06c   ";} /* exemple */

/** Titres **/
header h1, aside h3 {font-weight:bolder;}
header .header h2 {font-style: italic;}
article section h1, article section h2, article section h3, article section h4, article section h5, article section h6 {font-weight: bold;}

/** Header **/
article.article header h1 {margin-bottom: 0;}
article.article header .article-info-header {margin-top: 0;}

/** Commentaires **/
.comments .level-0 {}
.comments .level-1 {margin-left: 32px;}
.comments .level-2 {margin-left: 64px;}
.comments .level-3 {margin-left: 96px;}
.comments .level-4 {margin-left: 128px;}
.comments .level-5 {margin-left: 160px;}
.comments .level-6 {margin-left: 192px;}
#id_answer .comment-link-reply {display: none;}

/** Tags **/
aside ul.tag-list li {display: inline-block; margin-right:1rem;}
.tag-size-1   	{font-size: 1em;}
.tag-size-2   	{font-size: 1.1em;}
.tag-size-3   	{font-size: 1.2em;}
.tag-size-4   	{font-size: 1.3em;}
.tag-size-5   	{font-size: 1.4em;}
.tag-size-6   	{font-size: 1.5em;}
.tag-size-7   	{font-size: 1.6em;}
.tag-size-8   	{font-size: 1.7em;}
.tag-size-9   	{font-size: 1.8em;}
.tag-size-10  	{font-size: 1.9em;}
.tag-size-max 	{font-size: 2.0em;}
.tag-size-1 a 	{color: $w3themel2backgroundcolor;} /* theme-l2 */
.tag-size-2 a 	{color: $w3themel1backgroundcolor;} /* theme-l1 */
.tag-size-3 a 	{color: $w3themebackgroundcolor;} /* theme */
.tag-size-4 a 	{color: $w3themed1backgroundcolor;} /* theme-d1 */
.tag-size-5 a 	{color: $w3themed1backgroundcolor;} /* theme-d1 */
.tag-size-6 a 	{color: $w3themed2backgroundcolor;} /* theme-d2 */
.tag-size-7 a 	{color: $w3themed2backgroundcolor;} /* theme-d2 */
.tag-size-8 a 	{color: $w3themed3backgroundcolor;} /* theme-d3 */
.tag-size-9 a 	{color: $w3themed3backgroundcolor;} /* theme-d3 */
.tag-size-10 a	{color: $w3themed4backgroundcolor;} /* theme-d4 */
.tag-size-max a {color: $w3themed5backgroundcolor;} /* theme-d5 */

/** Cacher le lien "Lire la suite" **/
article section p.more {display: none;}

CSS;


	if (file_exists($emplacement)) {
	$file = fopen($emplacement, "w");
	fwrite($file,$contenu);
	fclose($file);	} 
	else {
		echo "<h2>Il y a un gros problème !</h2>";
	}
	?>


	
<p class="in-action-bar"><?php echo plxToken::getTokenPostMethod() ?><input type="submit" name="submit" value="Enregistrer" /></p>
	

</form>


<h2>Thèmes disponibles</h2>

<?php 

$themesTest = array (
	"deepOrange" => 		array (	"name"							=> "DeepOrange",
									"w3-theme-l5-color"    			=> "#000",
									"w3-theme-l5-background-color"	=> "#fff5f2",
									"w3-theme-l4-color"    			=> "#000",
									"w3-theme-l4-background-color"	=> "#ffddd3",
									"w3-theme-l3-color"    			=> "#000",
									"w3-theme-l3-background-color"	=> "#ffbca7",
									"w3-theme-l2-color"    			=> "#000",
									"w3-theme-l2-background-color"	=> "#ff9a7b",    
									"w3-theme-l1-color"    			=> "#fff",
									"w3-theme-l1-background-color"	=> "#ff7850",
									"w3-theme-color"    			=> "#fff",
									"w3-theme-background-color"		=> "#ff5722",
									"w3-theme-d1-color"    			=> "#fff",
									"w3-theme-d1-background-color"	=> "#ff4107",
									"w3-theme-d2-color"    			=> "#fff",
									"w3-theme-d2-background-color"	=> "#e93600",
									"w3-theme-d3-color"    			=> "#fff",
									"w3-theme-d3-background-color"	=> "#cb2f00",
									"w3-theme-d4-color"    			=> "#fff",
									"w3-theme-d4-background-color"	=> "#ae2900",
									"w3-theme-d5-color"    			=> "#fff",
									"w3-theme-d5-background-color"	=> "#912200",

									"w3-theme-light-color"    			=> "#000",
									"w3-theme-light-background-color"	=> "#fff5f2",
									"w3-theme-dark-color"    			=> "#fff",
									"w3-theme-dark-background-color"	=> "#912200",
									"w3-theme-action-color"    			=> "#fff",
									"w3-theme-action-background-color"	=> "#912200",
									"w3-hover-theme-color"    			=> "#fff",
									"w3-hover-theme-background-color"	=> "#ff5722",
									
									"w3-text-theme-color"    			=> "#ff5722",
									"w3-hover-text-theme-color"			=> "#ff5722",
									"w3-border-theme-color"    			=> "#ff5722",
									"w3-hover-border-theme-color"		=> "#ff5722",
								),	
	"cyan" => 				array (	"name"							=> "Cyan",
									"w3-theme-l5-color"    			=> "#000",
									"w3-theme-l5-background-color"	=> "#edfdff",
									"w3-theme-l4-color"    			=> "#000",
									"w3-theme-l4-background-color"	=> "#c4f8ff",
									"w3-theme-l3-color"    			=> "#000",
									"w3-theme-l3-background-color"	=> "#89f1ff",
									"w3-theme-l2-color"    			=> "#000",
									"w3-theme-l2-background-color"	=> "#4eeaff",    
									"w3-theme-l1-color"    			=> "#fff",
									"w3-theme-l1-background-color"	=> "#12e3ff",
									"w3-theme-color"    			=> "#fff",
									"w3-theme-background-color"		=> "#00bcd4",
									"w3-theme-d1-color"    			=> "#fff",
									"w3-theme-d1-background-color"	=> "#00aac1",
									"w3-theme-d2-color"    			=> "#fff",
									"w3-theme-d2-background-color"	=> "#0097ab",
									"w3-theme-d3-color"    			=> "#fff",
									"w3-theme-d3-background-color"	=> "#008496",
									"w3-theme-d4-color"    			=> "#fff",
									"w3-theme-d4-background-color"	=> "#007281",
									"w3-theme-d5-color"    			=> "#fff",
									"w3-theme-d5-background-color"	=> "#005f6b",		

									"w3-theme-light-color"    			=> "#000",
									"w3-theme-light-background-color"	=> "#edfdff",
									"w3-theme-dark-color"    			=> "#fff",
									"w3-theme-dark-background-color"	=> "#005f6b",
									"w3-theme-action-color"    			=> "#fff",
									"w3-theme-action-background-color"	=> "#005f6b",
									"w3-hover-theme-color"    			=> "#fff",
									"w3-hover-theme-background-color"	=> "#00bcd4",
									
									"w3-text-theme-color"    			=> "#00bcd4",
									"w3-hover-text-theme-color"			=> "#00bcd4",
									"w3-border-theme-color"    			=> "#00bcd4",
									"w3-hover-border-theme-color"		=> "#00bcd4",
								),

	"green" => 				array (	"name"							=> "Green",
									"w3-theme-l5-color"    			=> "#000",
									"w3-theme-l5-background-color"	=> "#f4faf4",
									"w3-theme-l4-color"    			=> "#000",
									"w3-theme-l4-background-color"	=> "#dbefdc",
									"w3-theme-l3-color"    			=> "#000",
									"w3-theme-l3-background-color"	=> "#b7dfb8",
									"w3-theme-l2-color"    			=> "#000",
									"w3-theme-l2-background-color"	=> "#93cf95",    
									"w3-theme-l1-color"    			=> "#fff",
									"w3-theme-l1-background-color"	=> "#6ec071",
									"w3-theme-color"    			=> "#fff",
									"w3-theme-background-color"		=> "#4caf50",
									"w3-theme-d1-color"    			=> "#fff",
									"w3-theme-d1-background-color"	=> "#459c48",
									"w3-theme-d2-color"    			=> "#fff",
									"w3-theme-d2-background-color"	=> "#3d8b40",
									"w3-theme-d3-color"    			=> "#fff",
									"w3-theme-d3-background-color"	=> "#357a38",
									"w3-theme-d4-color"    			=> "#fff",
									"w3-theme-d4-background-color"	=> "#2e6830",
									"w3-theme-d5-color"    			=> "#fff",
									"w3-theme-d5-background-color"	=> "#265728",		

									"w3-theme-light-color"    			=> "#000",
									"w3-theme-light-background-color"	=> "#f4faf4",
									"w3-theme-dark-color"    			=> "#fff",
									"w3-theme-dark-background-color"	=> "#265728",
									"w3-theme-action-color"    			=> "#fff",
									"w3-theme-action-background-color"	=> "#265728",
									"w3-hover-theme-color"    			=> "#fff",
									"w3-hover-theme-background-color"	=> "#4caf50",
									
									"w3-text-theme-color"    			=> "#4caf50",
									"w3-hover-text-theme-color"			=> "#4caf50",
									"w3-border-theme-color"    			=> "#4caf50",
									"w3-hover-border-theme-color"		=> "#4caf50",
								),

	"blueGrey" => 			array (	"name"							=> "BlueGrey",
									"w3-theme-l5-color"    			=> "#000",
									"w3-theme-l5-background-color"	=> "#f5f7f8",
									"w3-theme-l4-color"    			=> "#000",
									"w3-theme-l4-background-color"	=> "#dfe5e8",
									"w3-theme-l3-color"    			=> "#000",
									"w3-theme-l3-background-color"	=> "#becbd2",
									"w3-theme-l2-color"    			=> "#000",
									"w3-theme-l2-background-color"	=> "#9eb1bb",    
									"w3-theme-l1-color"    			=> "#fff",
									"w3-theme-l1-background-color"	=> "#7d97a5",
									"w3-theme-color"    			=> "#fff",
									"w3-theme-background-color"		=> "#607d8b",
									"w3-theme-d1-color"    			=> "#fff",
									"w3-theme-d1-background-color"	=> "#57707d",
									"w3-theme-d2-color"    			=> "#fff",
									"w3-theme-d2-background-color"	=> "#4d636f",
									"w3-theme-d3-color"    			=> "#fff",
									"w3-theme-d3-background-color"	=> "#435761",
									"w3-theme-d4-color"    			=> "#fff",
									"w3-theme-d4-background-color"	=> "#3a4b53",
									"w3-theme-d5-color"    			=> "#fff",
									"w3-theme-d5-background-color"	=> "#303e45",		

									"w3-theme-light-color"    			=> "#000",
									"w3-theme-light-background-color"	=> "#f5f7f8",
									"w3-theme-dark-color"    			=> "#fff",
									"w3-theme-dark-background-color"	=> "#303e45",
									"w3-theme-action-color"    			=> "#fff",
									"w3-theme-action-background-color"	=> "#303e45",
									"w3-hover-theme-color"    			=> "#fff",
									"w3-hover-theme-background-color"	=> "#607d8b",
									
									"w3-text-theme-color"    			=> "#607d8b",
									"w3-hover-text-theme-color"			=> "#607d8b",
									"w3-border-theme-color"    			=> "#607d8b",
									"w3-hover-border-theme-color"		=> "#607d8b",
								),

	"pink" => 				array (	"name"							=> "Pink",
									"w3-theme-l5-color"    			=> "#000",
									"w3-theme-l5-background-color"	=> "#fef2f6",
									"w3-theme-l4-color"    			=> "#000",
									"w3-theme-l4-background-color"	=> "#fbd2e0",
									"w3-theme-l3-color"    			=> "#000",
									"w3-theme-l3-background-color"	=> "#f6a6c1",
									"w3-theme-l2-color"    			=> "#fff",
									"w3-theme-l2-background-color"	=> "#f279a1",    
									"w3-theme-l1-color"    			=> "#fff",
									"w3-theme-l1-background-color"	=> "#ed4d82",
									"w3-theme-color"    			=> "#fff",
									"w3-theme-background-color"		=> "#e91e63",
									"w3-theme-d1-color"    			=> "#fff",
									"w3-theme-d1-background-color"	=> "#d91557",
									"w3-theme-d2-color"    			=> "#fff",
									"w3-theme-d2-background-color"	=> "#c1134d",
									"w3-theme-d3-color"    			=> "#fff",
									"w3-theme-d3-background-color"	=> "#a91143",
									"w3-theme-d4-color"    			=> "#fff",
									"w3-theme-d4-background-color"	=> "#910e3a",
									"w3-theme-d5-color"    			=> "#fff",
									"w3-theme-d5-background-color"	=> "#790c30",		

									"w3-theme-light-color"    			=> "#000",
									"w3-theme-light-background-color"	=> "#fef2f6",
									"w3-theme-dark-color"    			=> "#fff",
									"w3-theme-dark-background-color"	=> "#790c30",
									"w3-theme-action-color"    			=> "#fff",
									"w3-theme-action-background-color"	=> "#790c30",
									"w3-hover-theme-color"    			=> "#fff",
									"w3-hover-theme-background-color"	=> "#e91e63",
									
									"w3-text-theme-color"    			=> "#e91e63",
									"w3-hover-text-theme-color"			=> "#e91e63",
									"w3-border-theme-color"    			=> "#e91e63",
									"w3-hover-border-theme-color"		=> "#e91e63",
								),
/*
	"test" => 				array (	"name"					=> "Teston",
									"w3-theme-l5-color"    			=> "aa",
									"w3-theme-l5-background-color"	=> "bb",
									"w3-theme-l4-color"    			=> "cc",
									"w3-theme-l4-background-color"	=> "dd",
									"w3-theme-l3-color"    			=> "ee",
									"w3-theme-l3-background-color"	=> "ff",
									"w3-theme-l2-color"    			=> "gg",
									"w3-theme-l2-background-color"	=> "hh",    
									"w3-theme-l1-color"    			=> "ii",
									"w3-theme-l1-background-color"	=> "jj",
									"w3-theme-color"    			=> "kk",
									"w3-theme-background-color"		=> "ll",
									"w3-theme-d1-color"    			=> "mm",
									"w3-theme-d1-background-color"	=> "nn",
									"w3-theme-d2-color"    			=> "oo",
									"w3-theme-d2-background-color"	=> "pp",
									"w3-theme-d3-color"    			=> "qq",
									"w3-theme-d3-background-color"	=> "rr",
									"w3-theme-d4-color"    			=> "ss",
									"w3-theme-d4-background-color"	=> "tt",
									"w3-theme-d5-color"    			=> "uu",
									"w3-theme-d5-background-color"	=> "vv",		
									
									"w3-theme-light-color"    			=> "ww",
									"w3-theme-light-background-color"	=> "xx",
									"w3-theme-dark-color"    			=> "yy",
									"w3-theme-dark-background-color"	=> "zz",
									"w3-theme-action-color"    			=> "11",
									"w3-theme-action-background-color"	=> "22",
									"w3-hover-theme-color"    			=> "33",
									"w3-hover-theme-background-color"	=> "44",
									"w3-text-theme-color"    			=> "55",
									"w3-hover-text-theme-color"			=> "66",
									"w3-border-theme-color"    			=> "77",
									"w3-hover-border-theme-color"		=> "88",									
									
								),
//*/
/*
	"test2" => 				array (	"name"					=> "Teston2",
									"w3-theme-l5-color"    			=> "Red",
									"w3-theme-l5-background-color"	=> "AliceBlue",
									"w3-theme-l4-color"    			=> "Pink",
									"w3-theme-l4-background-color"	=> "AntiqueWhite",
									"w3-theme-l3-color"    			=> "Blue",
									"w3-theme-l3-background-color"	=> "Azure",
									"w3-theme-l2-color"    			=> "Purple",
									"w3-theme-l2-background-color"	=> "Chartreuse",    
									"w3-theme-l1-color"    			=> "yellow",
									"w3-theme-l1-background-color"	=> "DarkBlue",
									"w3-theme-color"    			=> "Orange",
									"w3-theme-background-color"		=> "DarkGreen",
									"w3-theme-d1-color"    			=> "CadetBlue",
									"w3-theme-d1-background-color"	=> "DarkSeaGreen",
									"w3-theme-d2-color"    			=> "DarkGgreen",
									"w3-theme-d2-background-color"	=> "HotPink",
									"w3-theme-d3-color"    			=> "DeepPink ",
									"w3-theme-d3-background-color"	=> "Indigo",
									"w3-theme-d4-color"    			=> "ForestGreen",
									"w3-theme-d4-background-color"	=> "LightCoral",
									"w3-theme-d5-color"    			=> "Navy",
									"w3-theme-d5-background-color"	=> "LightCyan",		

									"w3-theme-light-color"    			=> "LightGoldenRodYellow",
									"w3-theme-light-background-color"	=> "LightSeaGreen",
									"w3-theme-dark-color"    			=> "LightSlateGray",
									"w3-theme-dark-background-color"	=> "LightYellow",
									"w3-theme-action-color"    			=> "MediumTurquoise",
									"w3-theme-action-background-color"	=> "MistyRose",
									"w3-hover-theme-color"    			=> "Olive",
									"w3-hover-theme-background-color"	=> "OrangeRed",
									"w3-text-theme-color"    			=> "PaleGreen",
									"w3-hover-text-theme-color"			=> "Peru",
									"w3-border-theme-color"    			=> "SaddleBrown",
									"w3-hover-border-theme-color"		=> "SpringGreen",									
									
								),
//*/
/*
	"test3" => 			array (		"name"							=> "Teston3",
									"w3-theme-l5-color"    			=> "w3-theme-l5-color",
									"w3-theme-l5-background-color"	=> "w3-theme-l5-background-color",
									"w3-theme-l4-color"    			=> "w3-theme-l4-color",
									"w3-theme-l4-background-color"	=> "w3-theme-l4-background-color",
									"w3-theme-l3-color"    			=> "w3-theme-l3-color",
									"w3-theme-l3-background-color"	=> "w3-theme-l3-background-color",
									"w3-theme-l2-color"    			=> "w3-theme-l2-color",
									"w3-theme-l2-background-color"	=> "w3-theme-l2-background-color",    
									"w3-theme-l1-color"    			=> "w3-theme-l1-color",
									"w3-theme-l1-background-color"	=> "w3-theme-l1-background-color",
									"w3-theme-color"    			=> "w3-theme-color",
									"w3-theme-background-color"		=> "w3-theme-background-color",
									"w3-theme-d1-color"    			=> "w3-theme-d1-color",
									"w3-theme-d1-background-color"	=> "w3-theme-d1-background-color",
									"w3-theme-d2-color"    			=> "w3-theme-d2-color",
									"w3-theme-d2-background-color"	=> "w3-theme-d2-background-color",
									"w3-theme-d3-color"    			=> "w3-theme-d3-color",
									"w3-theme-d3-background-color"	=> "w3-theme-d3-background-color",
									"w3-theme-d4-color"    			=> "w3-theme-d4-color",
									"w3-theme-d4-background-color"	=> "w3-theme-d4-background-color",
									"w3-theme-d5-color"    			=> "w3-theme-d5-color",
									"w3-theme-d5-background-color"	=> "w3-theme-d5-background-color",

									"w3-theme-light-color"    			=> "w3-theme-light-color",
									"w3-theme-light-background-color"	=> "w3-theme-light-background-color",
									"w3-theme-dark-color"    			=> "w3-theme-dark-color",
									"w3-theme-dark-background-color"	=> "w3-theme-dark-background-color",
									"w3-theme-action-color"    			=> "w3-theme-action-color",
									"w3-theme-action-background-color"	=> "w3-theme-action-background-color",
									"w3-hover-theme-color"    			=> "w3-hover-theme-color",
									"w3-hover-theme-background-color"	=> "w3-hover-theme-background-color",
									"w3-text-theme-color"    			=> "w3-text-theme-color",
									"w3-hover-text-theme-color"			=> "w3-hover-text-theme-color",
									"w3-border-theme-color"    			=> "w3-border-theme-color",
									"w3-hover-border-theme-color"		=> "w3-hover-border-theme-color",
								),	
//*/
);

echo '<h3>Liste des thèmes</h3><ul style="display:initial"><li> <ul> ';
foreach ($themesTest as $theme => $t) {
echo '<li style=" display: inline;margin-right: 3em; "><a href="#theme'.$t["name"].'">'.$t["name"].'</a></li> ';

}
echo "</ul></li></ul> <h3>Visualiser les thèmes</h3><p>Pour ajouter un thème cliquer sur le bouton ⊕ </p>";

foreach ($themesTest as $theme => $t) {

echo <<<EOF
<div id="theme{$t["name"]}" style="display: table;width: 100%;">

<!--
<a href="javascript:void(0)" onclick="addTheme{$t["name"]}()">bouton</a>
-->
<script type="text/javascript">
function addTheme{$t["name"]}(){
	document.getElementById('id_w3themel5color').value = document.getElementById('theme{$t["name"]}w3themel5color').innerHTML;
	document.getElementById('id_w3themel5backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themel5backgroundcolor').innerHTML;
	document.getElementById('id_w3themel4color').value = document.getElementById('theme{$t["name"]}w3themel4color').innerHTML;
	document.getElementById('id_w3themel4backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themel4backgroundcolor').innerHTML;
	document.getElementById('id_w3themel3color').value = document.getElementById('theme{$t["name"]}w3themel3color').innerHTML;
	document.getElementById('id_w3themel3backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themel3backgroundcolor').innerHTML;
	document.getElementById('id_w3themel2color').value = document.getElementById('theme{$t["name"]}w3themel2color').innerHTML;
	document.getElementById('id_w3themel2backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themel2backgroundcolor').innerHTML;
	document.getElementById('id_w3themel1color').value = document.getElementById('theme{$t["name"]}w3themel1color').innerHTML;
	document.getElementById('id_w3themel1backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themel1backgroundcolor').innerHTML;
	
	document.getElementById('id_w3themecolor').value = document.getElementById('theme{$t["name"]}w3themecolor').innerHTML;
	document.getElementById('id_w3themebackgroundcolor').value = document.getElementById('theme{$t["name"]}w3themebackgroundcolor').innerHTML;
	
	document.getElementById('id_w3themed5color').value = document.getElementById('theme{$t["name"]}w3themed5color').innerHTML;
	document.getElementById('id_w3themed5backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themed5backgroundcolor').innerHTML;
	document.getElementById('id_w3themed4color').value = document.getElementById('theme{$t["name"]}w3themed4color').innerHTML;
	document.getElementById('id_w3themed4backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themed4backgroundcolor').innerHTML;
	document.getElementById('id_w3themed3color').value = document.getElementById('theme{$t["name"]}w3themed3color').innerHTML;
	document.getElementById('id_w3themed3backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themed3backgroundcolor').innerHTML;
	document.getElementById('id_w3themed2color').value = document.getElementById('theme{$t["name"]}w3themed2color').innerHTML;
	document.getElementById('id_w3themed2backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themed2backgroundcolor').innerHTML;
	document.getElementById('id_w3themed1color').value = document.getElementById('theme{$t["name"]}w3themed1color').innerHTML;
	document.getElementById('id_w3themed1backgroundcolor').value = document.getElementById('theme{$t["name"]}w3themed1backgroundcolor').innerHTML;


	document.getElementById('id_w3themelightcolor').value = document.getElementById('theme{$t["name"]}w3themelightcolor').innerHTML;
	document.getElementById('id_w3themelightbackgroundcolor').value = document.getElementById('theme{$t["name"]}w3themelightbackgroundcolor').innerHTML;
	document.getElementById('id_w3themedarkcolor').value = document.getElementById('theme{$t["name"]}w3themedarkcolor').innerHTML;
	document.getElementById('id_w3themedarkbackgroundcolor').value = document.getElementById('theme{$t["name"]}w3themedarkbackgroundcolor').innerHTML;
	document.getElementById('id_w3themeactioncolor').value = document.getElementById('theme{$t["name"]}w3themeactioncolor').innerHTML;
	document.getElementById('id_w3themeactionbackgroundcolor').value = document.getElementById('theme{$t["name"]}w3themeactionbackgroundcolor').innerHTML;
		

	document.getElementById('id_w3themehovercolor').value = document.getElementById('theme{$t["name"]}w3themehovercolor').innerHTML;
	document.getElementById('id_w3themehoverbackgroundcolor').value = document.getElementById('theme{$t["name"]}w3themehoverbackgroundcolor').innerHTML;
	document.getElementById('id_w3themetextcolor').value = document.getElementById('theme{$t["name"]}w3themetextcolor').innerHTML;
	document.getElementById('id_w3themehovertext').value = document.getElementById('theme{$t["name"]}w3themehovertext').innerHTML;
	document.getElementById('id_w3themebordercolor').value = document.getElementById('theme{$t["name"]}w3themebordercolor').innerHTML;
	document.getElementById('id_w3themehoverborder').value = document.getElementById('theme{$t["name"]}w3themehoverborder').innerHTML;
		

		previewTheme();
	    window.scrollTo(0, 0);
}
</script>

<ul style="display: none;">
	<li >w3-theme-l5-color: <span id="theme{$t["name"]}w3themel5color">{$t["w3-theme-l5-color"]}</span></li>
	<li >w3-theme-l5-background-color: <span id="theme{$t["name"]}w3themel5backgroundcolor">{$t["w3-theme-l5-background-color"]}</span></li>
	<li >w3-theme-l4-color: <span id="theme{$t["name"]}w3themel4color">{$t["w3-theme-l4-color"]}</span></li>
	<li >w3-theme-l4-background-color: <span id="theme{$t["name"]}w3themel4backgroundcolor">{$t["w3-theme-l4-background-color"]}</span></li>
	<li >w3-theme-l3-color: <span id="theme{$t["name"]}w3themel3color">{$t["w3-theme-l3-color"]}</span></li>
	<li >w3-theme-l3-background-color: <span id="theme{$t["name"]}w3themel3backgroundcolor">{$t["w3-theme-l3-background-color"]}</span></li>
	<li >w3-theme-l2-color: <span id="theme{$t["name"]}w3themel2color">{$t["w3-theme-l2-color"]}</span></li>
	<li >w3-theme-l2-background-color: <span id="theme{$t["name"]}w3themel2backgroundcolor">{$t["w3-theme-l2-background-color"]}</span></li>
	<li >w3-theme-l1-color: <span id="theme{$t["name"]}w3themel1color">{$t["w3-theme-l1-color"]}</span></li>
	<li >w3-theme-l1-background-color: <span id="theme{$t["name"]}w3themel1backgroundcolor">{$t["w3-theme-l1-background-color"]}</span></li>

	<li >w3-theme-color: <span id="theme{$t["name"]}w3themecolor">{$t["w3-theme-color"]}</span></li>
	<li >w3-theme-color: <span id="theme{$t["name"]}w3themebackgroundcolor">{$t["w3-theme-background-color"]}</span></li>

	<li >w3-theme-d1-color: <span id="theme{$t["name"]}w3themed1color">{$t["w3-theme-d1-color"]}</span></li>
	<li >w3-theme-d1-background-color: <span id="theme{$t["name"]}w3themed1backgroundcolor">{$t["w3-theme-d1-background-color"]}</span></li>
	<li >w3-theme-d2-color: <span id="theme{$t["name"]}w3themed2color">{$t["w3-theme-d2-color"]}</span></li>
	<li >w3-theme-d2-background-color: <span id="theme{$t["name"]}w3themed2backgroundcolor">{$t["w3-theme-d2-background-color"]}</span></li>
	<li >w3-theme-d3-color: <span id="theme{$t["name"]}w3themed3color">{$t["w3-theme-d3-color"]}</span></li>
	<li >w3-theme-d3-background-color: <span id="theme{$t["name"]}w3themed3backgroundcolor">{$t["w3-theme-d3-background-color"]}</span></li>
	<li >w3-theme-d4-color: <span id="theme{$t["name"]}w3themed4color">{$t["w3-theme-d4-color"]}</span></li>
	<li >w3-theme-d4-background-color: <span id="theme{$t["name"]}w3themed4backgroundcolor">{$t["w3-theme-d4-background-color"]}</span></li>
	<li >w3-theme-d5-color: <span id="theme{$t["name"]}w3themed5color">{$t["w3-theme-d5-color"]}</span></li>
	<li >w3-theme-d5-background-color: <span id="theme{$t["name"]}w3themed5backgroundcolor">{$t["w3-theme-d5-background-color"]}</span></li>

	<li >w3-theme-light-color: <span id="theme{$t["name"]}w3themelightcolor">{$t["w3-theme-light-color"]}</span></li>
	<li >w3-theme-light-background-color: <span id="theme{$t["name"]}w3themelightbackgroundcolor">{$t["w3-theme-light-background-color"]}</span></li>
	<li >w3-theme-dark-color: <span id="theme{$t["name"]}w3themedarkcolor">{$t["w3-theme-dark-color"]}</span></li>
	<li >w3-theme-dark-background-color: <span id="theme{$t["name"]}w3themedarkbackgroundcolor">{$t["w3-theme-dark-background-color"]}</span></li>
	<li >w3-theme-action-color: <span id="theme{$t["name"]}w3themeactioncolor">{$t["w3-theme-action-color"]}</span></li>
	<li >w3-theme-action-background-color: <span id="theme{$t["name"]}w3themeactionbackgroundcolor">{$t["w3-theme-action-background-color"]}</span></li>

	<li >w3-hover-theme-color: <span id="theme{$t["name"]}w3themehovercolor">{$t["w3-hover-theme-color"]}</span></li>
	<li >w3-hover-theme-background-color: <span id="theme{$t["name"]}w3themehoverbackgroundcolor">{$t["w3-hover-theme-background-color"]}</span></li>
	<li >w3-text-theme: <span id="theme{$t["name"]}w3themetextcolor">{$t["w3-text-theme-color"]}</span></li>
	<li >w3-text-theme-hover: <span id="theme{$t["name"]}w3themehovertext">{$t["w3-hover-text-theme-color"]}</span></li>
	<li >w3-border-theme: <span id="theme{$t["name"]}w3themebordercolor">{$t["w3-border-theme-color"]}</span></li>
	<li >w3-border-theme-hover: <span id="theme{$t["name"]}w3themehoverborder">{$t["w3-hover-border-theme-color"]}</span></li>


</ul>	

	<div class="w3-card-4" style="width: 45%;  float:left; border: solid 1px black;">
			
		<div class="w3-container w3-theme w3-card" style="background-color: {$t["w3-theme-background-color"]}; color: {$t["w3-theme-d5-color"]}; border-bottom: 1px solid #ddd; margin:0; padding: 0.5em;">
		  <h2>{$t["name"]}</h2>
		</div>

		<a href="javascript:void(0)" onclick="addTheme{$t["name"]}()"><span  id="mt1-action" class="w3-button w3-xlarge w3-circle w3-right" style="position: relative; float:right; top: -1em; right: 1em; background-color: {$t["w3-theme-d5-background-color"]}; color: rgb(255, 255, 255);font-size: xx-large;padding: 0.25em;border-radius: 50%; border: 1px solid rgb(221, 221, 221); width: 2em;text-align: center;">+</span></a>

		<div class="w3-container w3-text-theme" style="background-color: initial; color: {$t["w3-theme-background-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd;">
			<p>w3-text-theme</p>
		</div>

		<div class="w3-theme-l5" style="background-color: {$t["w3-theme-l5-background-color"]}; color: {$t["w3-theme-l5-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			  <p>w3-theme-l5 (w3-theme-light)</p>
		</div>
		
		<div class="w3-theme-l4" style="background-color: {$t["w3-theme-l4-background-color"]}; color: {$t["w3-theme-l4-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			<p>w3-theme-l4</p>
		</div>
		
		<div class="w3-theme-l3"  style="background-color: {$t["w3-theme-l3-background-color"]}; color: {$t["w3-theme-l3-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			<p>w3-theme-l3</p>
		</div>
		
		<div class="w3-theme-l2" style="background-color: {$t["w3-theme-l2-background-color"]}; color: {$t["w3-theme-l2-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			<p>w3-theme-l2</p>
		</div>
		
		<div class="w3-theme-l1" style="background-color: {$t["w3-theme-l1-background-color"]}; color: {$t["w3-theme-l1-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			<p>w3-theme-l1</p>
		</div>
		
		<div class="w3-theme" style="background-color: {$t["w3-theme-background-color"]}; color: {$t["w3-theme-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			<p>w3-theme</p>
		</div>
		
		<div class="w3-theme-d1" style="background-color: {$t["w3-theme-d1-background-color"]}; color: {$t["w3-theme-d1-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			<p>w3-theme-d1</p>
		</div>
		
		<div class="w3-theme-d2" style="background-color: {$t["w3-theme-d2-background-color"]}; color: {$t["w3-theme-d2-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			<p>w3-theme-d2</p>
		</div>
		
		<div class="w3-theme-d3" style="background-color: {$t["w3-theme-d3-background-color"]}; color: {$t["w3-theme-d3-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			<p>w3-theme-d3</p>
		</div>
		
		<div class="w3-theme-d4" style="background-color: {$t["w3-theme-d4-background-color"]}; color: {$t["w3-theme-d4-color"]}; margin:0; padding: 0.5em; border-bottom: 1px solid #ddd; ">
			<p>w3-theme-d4</p>
		</div>
		
		<div class="w3-theme-d5" style="background-color: {$t["w3-theme-d5-background-color"]}; color: {$t["w3-theme-d5-color"]}; margin:0; padding: 0.5em; border-bottom: 0px solid #ddd; ">
			<p>w3-theme-d5 (w3-theme-dark)</p>
		</div>
		
	</div>
	

 
	<div class="w3-border" style="width: 45%; float:right; border: solid 1px black;">
		
		<div id="mt1-top" class="w3-container w3-padding-small" style="margin:0; padding: 1em; background-color: {$t["w3-theme-d5-background-color"]}; color: #fff; ">
			<div class="w3-right" style="text-align:right;">
			<i class="fa fa-cube"></i>
			<i class="fa fa-sort"></i>
			<i class="fa fa-trash"></i>
			12:30</div>
		</div>
		
		<header id="mt1-header" class="w3-container" style="margin:0; padding: 1em;background-color: {$t["w3-theme-background-color"]}; color: #fff;">
			<h2>{$t["name"]}</h2>
		</header>
		
		<div id="mt1-back" class="w3-container w3-padding-16" style="position: relative; min-height: 465px; background-color: {$t["w3-theme-l5-background-color"]};">
		
		<a href="javascript:void(0)" onclick="addTheme{$t["name"]}()"><span  id="mt1-action"  testonmouseover="this.style.backgroundColor='pink';"  testonmouseout="this.style.backgroundColor='yellow';"  class="w3-button w3-xlarge w3-circle w3-right" style="position: absolute; top: -1em; right: 1em; background-color: {$t["w3-theme-d5-background-color"]}; color: rgb(255, 255, 255);font-size: xx-large;padding: 0.25em;border-radius: 50%;width: 2em;text-align: center; border: 1px solid #ddd;">+</span></a>

		<div class="w3-row">
			
		<div class="w3-col" style="margin:0; padding: 1em; display:inline-block;">
		   <i class="fa fa-empire" style="font-size:96px;color: {$t["w3-theme-background-color"]};"></i>
		</div>
		
		<div class="w3-rest w3-container" style="margin:0; padding: 1em;display:inline-block;">
		  <h3 id="mt1-h1" style="color: {$t["w3-theme-background-color"]};">Empire</h3>
		  <p>Provident et autem nam nam fugiat iusto et est.</p>
		</div>
		</div>  
		
		<hr>
		
		<div class="w3-row">
		<div id="mt1-graphic" class="w3-col" style="margin:0; padding: 1em; display:inline-block;">
		   <i class="fa fa-rebel" style="font-size:96px;color: {$t["w3-theme-background-color"]};"></i>
		</div>
		<div class="w3-rest w3-container" style="margin:0; padding: 1em; display:inline-block;">
		  <h3 id="mt1-h2" style="color: {$t["w3-theme-background-color"]};">Rebel</h3>
		  <p>Veritatis assumenda distinctio ut iusto. </p>
		</div>
		</div>
		
		<hr>
		
		<div class="w3-row">
		<div class="w3-col" style="margin:0; padding: 1em; display:inline-block;">
		   <i class="fa fa-first-order" style="font-size:96px;color: {$t["w3-theme-background-color"]};"></i>
		</div>
		<div class="w3-rest w3-container" style="margin:0; padding: 1em; display:inline-block;">
		  <h3 id="mt1-h3" style="color: {$t["w3-theme-background-color"]};">First Order</h3>
		  <p>Sit eum officia facere. Sunt nihil non harum in.</p>
		</div>
		</div>
		
		</div>
		<div id="mt1-footer" class="w3-container" style="margin:0; padding: 0.5em;background-color: {$t["w3-theme-background-color"]}; color: #fff;font-size: x-large;">
		<p>W3Schools 2016</p>
		</div>
		<div id="mt1-bottom" class="w3-container w3-xlarge" style="margin:0; padding: 0.5em;background-color: {$t["w3-theme-d5-background-color"]}; color: #fff;font-size: x-large;">
			<span class="" style="">«</span>

			<span class="w3-right" style="float: right;">»</span>
		</div>

	</div>

</div>
<hr  style="border:2px solid transparent"/>
EOF;

}
?>
