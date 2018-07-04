<?php
/*
 * Try "php -f test.php > test.svg" and open this .svg image in your browser or
 * a better way is to display directly this page in your browser via the server.
 * In this last case, reload the page many times and enjoy it.
 *
 * For printing in pdf format, use :
 * "php -f test.php > test.svg; inkscape -A test.pdf test.svg"
 * */
require 'svgraph.php';

$title = 'SVG graph in PHP';
$labels = array('05/02/18', '12/02/18', '19/02/18', '26/02/18', '05/03/18', '12/03/18', '19/03/18', '26/03/18', '02/04/18', '09/04/18', '16/04/18', '23/04/18', '30/04/18', '07/05/18');
$series = array(
	'download/kzinstall.php' => array(3, 5, 0, 9, 10, 30, 48, 53, 45, 35, 19, 12, 0, 0),
	'download/turbo-installer.php' => array(0, 0, 1, 1, 9, 10, 30, 48, 53, 45, 15, 0, 1, 0),
	'download/version.php' => array(52, 15, 2, 5, 18, 55, 120, 81, 32, 10, 6, 4, 13, 2),
	'download/kzInstall.php' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 6, 0),
	'download/meetup.ics' => array(null, null, null, null, null, 4, 15, 0, 0, 0, 0, 0, 1),
	'download/affiche.jpg' => array(null, null, null, 12, 15, 0, 0, 0, 0, 1, 5, 7),
	'download/schnaps.zip' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1),
	'download/ufme.sql' => array(0, 0, 2, 6, 3, 2, 0, 0, 0, 0, 0, 0, 0, 1)
);

$graph = new SVGraph(false, false, 'svgraph.css');
$graph->setGraph($labels, $series, $title);
$graph->normalizeDocument();

if(!filter_has_var(INPUT_SERVER, 'HTTP_HOST')) {
	// Outputs the content to an xml file
	$graph->formatOutput = true;
	echo $graph->saveXML();
} else {
	// Displays an HTML page
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<title><?php echo $title; ?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="svgraph.css" />
	<style type="text/css">
		* { margin: 0; padding: 0; }
		body { background: #444a; }
		.container { position: fixed; }
		.graph { margin: 50vh 1rem 0; transform: translateY(-50%); }
	</style>
</head>
<body>
	<div class="container">
		<div class="graph">
<?php echo $graph->saveXML($graph->root)."\n"; ?>
		</div>
	</div>
	<script type="text/javascript">
		// const timer1 = setInterval(function(event) { window.location.reload(); }, 2000);
	</script>
</body>
</html>
<?php
}
?>
