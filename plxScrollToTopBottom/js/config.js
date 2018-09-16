/**
 * Plugin for Pluxml
 * @author	DPFPIC
 * Site : http://dpfpic.com
 * Licence GNU_GPL
 **/
function change_tab(name) {
	document.getElementById('tab_'+anc_tab).className = 'tab_0 tab';
	document.getElementById('tab_'+name).className = 'tab_1 tab';
	document.getElementById('content_tab_'+anc_tab).style.display = 'none';
	document.getElementById('content_tab_'+name).style.display = 'block';
	anc_tab = name;
}
