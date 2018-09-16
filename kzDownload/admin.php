<?php
if(!defined('PLX_ADMIN') or !defined('PLX_ROOT')) { exit; }

plxToken::validateFormToken();

if(filter_has_var(INPUT_POST, 'filenames')) {
	$plxPlugin->deleteStats('filenames');
	header("Location: plugin.php?p=$plugin");
	exit;
}

$chartCaption = $plxPlugin->getLang('CHART');
?>
<form id="<?php echo $plugin; ?>-form" method="post">
	<div class="scrollable-table">
		<table id="<?php echo $plugin; ?>-table">
			<caption><?php $plxPlugin->lang('DOWNLOADS'); ?></caption>
			<thead>
				<tr>
					<th><input id="<?php echo $plugin; ?>-chkbox1" type="checkbox" /></th>
					<th><?php $plxPlugin->lang('FILENAME'); ?></th>
					<th><?php $plxPlugin->lang('PUBLISHED'); ?></th>
					<th class="number"><?php $plxPlugin->lang('SUM'); ?></th>
					<th class="number"><?php $plxPlugin->lang('AVERAGE'); ?></th>
<?php
$now = time();
$weeks = array();
$caption = $plxPlugin->getLang('WEEK');
for($i=0; $i<kzDownload::WEEKS_MAX; $i++) {
	$w = date(kzDownload::DATE_FORMAT, $now - kzDownload::WEEK_DURATION * $i);
	$weeks[] = $w;
	$numero = explode('W', $w)[1];
	echo <<< CELL
					<th class="number">$caption$numero</th>\n
CELL;
}
?>
				</tr>
			</thead>
			<tbody>
<?php
$datas = trim($plxPlugin->getParam('stats'));
if(empty($datas)) {
	$cnt = kzDownload::WEEKS_MAX + 5;
	$caption = $plxPlugin->getLang('NO_STAT');
	echo "<td colspan=\"$cnt\">$caption</td>\n";
} else {
	# kzDownload::stats(...)
	$stats = (function_exists('json_decode')) ? json_decode($datas, true) : $stats = unserialize($datas);
	$colSep = <<< COL_SEP
</td>
				<td>
COL_SEP;
	foreach($stats as $filename=>$infos) {
		$cnt = 0;
		$average = 0;
		$cells = array();
		foreach($weeks as $w) {
			if(!empty($infos['weeks'][$w])) {
				$cells[] = $infos['weeks'][$w];
				$average += intval($infos['weeks'][$w]);
				$cnt++;
			} else {
				$cells[] = '&nbsp;';
			}
		}
		$average = ($cnt > 0) ? intval($average / $cnt) : '&nbsp;';
		$allWeeks = implode($colSep, $cells);
		echo <<< ROW
				<tr>
					<td><input type="checkbox" name="filenames[]" value="$filename" /></td>
					<td>$filename</td>
					<td>{$infos['published']}</td>
					<td>{$infos['cumul']}</td>
					<td>$average</td>
					<td>$allWeeks</td>
				</tr>
ROW;
	}
	$enabledChart = true;
}
?>
			</tbody>
		</table>
	</div>
<?php if(!empty($enabledChart)) {
?>
	<div class="in-action-bar">
		<?php echo plxToken::getTokenPostMethod()."\n"; ?>
		<input type="submit" value="<?php $plxPlugin->lang('DELETE'); ?>" />
		<button id="chart-btn" type="button"><?php echo $chartCaption; ?></button>
	</div>
<?php
}
?>
</form>
<?php
if(!empty($enabledChart)) {
	include 'svgraph.php';
	if(class_exists('SVGraph')) {
?>
<div id="<?php echo $plugin; ?>-overlay" class="overlay">
	<div id="<?php echo $plugin; ?>-chart">
<?php
$today = time();
$weeks = array();
for($t=$today - kzDownload::STATS_PERIODE; $t < $today; $t += kzDownload::WEEK_DURATION) {
	$weeks[date('y\WW', $t)] = date('d/m/y', $t);
}
$series = array();
foreach($stats as $filename=>$infos) {
	$serie = array();
	foreach(array_keys($weeks) as $week) {
		$serie[] = (array_key_exists($week, $infos['weeks'])) ? $infos['weeks'][$week] : 0;
	}
	$series[$filename] = $serie;
}
if(true) {
	// Debugging
	$dbg_labels = implode("', '", array_values($weeks));
	$buffer = array();
	foreach($series as $name=>$datas){
		$buffer[]  = "\t'$name' => array(".implode(", ", $datas).")";
	}
	$dbg_series = implode(",\n", $buffer);
	/*
	echo <<< DEBUG
<!--
// Datas for SVGraph
\$labels = array('$dbg_labels');
\$series = array(
$dbg_series
);
-->\n
DEBUG;
	* */
}
$graph = new SVGraph();
$graph->setGraph(array_values($weeks), $series, $plxPlugin->getLang('DOWNLOADS'));
$graph->normalizeDocument();
echo $graph->saveXML($graph->root)."\n";
?>
	</div>
	<button>X</button>
</div>
<script type="text/javascript">
	(function() {
		'use strict';

		const myCheckBox = document.getElementById('<?php echo $plugin; ?>-chkbox1');
		if(myCheckBox != null) {
			myCheckBox.addEventListener('change', function(event) {
				event.preventDefault();
				const chks = document.querySelectorAll('#<?php echo $plugin; ?>-table tbody tr td:first-of-type input[type="checkbox"]');
				for(var i=0, iMax=chks.length; i<iMax; i++) {
					chks[i].checked = this.checked;
				}
			});
		}

		const chartBtn = document.getElementById('chart-btn');
		const graphClass = 'kzdownload-graph';
		if(chartBtn != null) {
			chartBtn.addEventListener('click', function(event) {
				event.preventDefault();
				document.body.classList.add(graphClass);
			});
		}

		const closeBtn = document.body.querySelector('#<?php echo $plugin; ?>-overlay button');
		if(closeBtn != null) {
			closeBtn.addEventListener('click', function(event) {
				event.preventDefault();
				document.body.classList.remove(graphClass);
			});
		}
	})();
</script>
<?php
	}
}
?>
