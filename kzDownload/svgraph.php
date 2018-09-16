<?php

class SVGraph extends DOMDocument {

	// Default values
	const GRAPH_AREA_WIDTH = 1000;
	const GRAPH_AREA_HEIGHT = 500;
	const PADDING_TOP = 30;
	const PADDING_RIGHT = 60;
	const PADDING_BOTTOM = 20;
	const PADDING_LEFT = 30;
	const COLORS_MAX = 16; // Look at your stylesheet

	const MARK_PATTERN = 'mark-%02d';
	const COLOR_PATTERN = 'graph-%02d';

	// Area for plotting curves
	public $graphArea = array(
		'width'		=> self::GRAPH_AREA_WIDTH,
		'height'	=> self::GRAPH_AREA_HEIGHT
	);
	// Area around graphArea for adding extras like labels or heads on the scales
	public $paddings = array(
		'top'		=> self::PADDING_TOP,
		'right'		=> self::PADDING_RIGHT,
		'bottom'	=> self::PADDING_BOTTOM,
		'left'		=> self::PADDING_LEFT
	);

	public $labels = false;
	public $series = false;
	public $definitions = false; // for symbols
	private $marksCount;

	public function __construct($graphArea=false, $paddings=false, $stylesheet=false) {
		parent::__construct('1.0', 'utf-8');

		if(empty($stylesheet)) {
			$filename = preg_replace('@\.php$@', '.css', __FILE__);
			if(file_exists($filename)) {
				$stylesheet = basename($filename);
			}
		}
		if(!empty($stylesheet) and filter_has_var(INPUT_SERVER, 'HTTP_HOST')) {
			$this->appendChild(
				parent::createProcessingInstruction('xml-stylesheet', 'type="text/css" href="'.$stylesheet.'"')
			);
		}

		if(is_array($graphArea)) {
			$entry = filter_var_array($graphArea, FILTER_VALIDATE_INT);
			foreach(array_keys($this->graphArea) as $fieldName) {
				if(!empty($entry[$fieldName])) {
					$this->graphArea[$fieldName] = $entry[$fieldName];
				}
			}
		}

		if(is_array($paddings)) {
			/* Same order like CSS */
			$entry = filter_var_array($paddings, FILTER_VALIDATE_INT);
			foreach(array_keys($this->paddings) as $fieldName) {
				if(!empty($entry[$fieldName])) {
					$this->paddings[$fieldName] = $entry[$fieldName];
				}
			}
		}

		$this->root = self::createElement('svg');
		foreach(array(
			'version'		=> '1.1',
			'xmlns'			=> 'http://www.w3.org/2000/svg',
			'xmlns:xlink'	=> 'http://www.w3.org/1999/xlink',
			'width'			=> '100%',
			'viewBox'		=> implode(' ', array(
				-$this->paddings['left'],
				-$this->paddings['top'],
				$this->graphArea['width'] + $this->paddings['right'],
				$this->graphArea['height'] + $this->paddings['top'] + $this->paddings['bottom']
			)),
			'preserveAspectRatio' => 'xMidYMin meet',
		) as $attribute=>$value) {
			$this->root->setAttribute($attribute, $value);
		}

		if(!empty($stylesheet) and !filter_has_var(INPUT_SERVER, 'HTTP_HOST')) {
			$this->root->appendChild(self::__setInternalCSS($stylesheet));
		}

		$definitions = self::__createDefinitions();
		$this->root->appendChild($definitions);
		$this->definitions = $definitions;

		$this->appendChild($this->root);
	}

	private function __setInternalCSS($stylesheet) {
		$element = self::createElement('style');
		$element->setAttribute('type', 'text/css');
		$data = $this->createCDATASection(file_get_contents($stylesheet));
		$element->appendChild($data);
		return $element;
	}

	private function __createElementWithAttributes($name, $attributes, $value='') {
		$element = self::createElement($name, $value);
		foreach($attributes as $key=>$value) {
			$element->setAttribute($key, $value);
		}
		if(in_array($name, explode(' ', 'polygon rect circle'))) {
			$element->setAttribute('stroke', 'none');
		}
		return $element;
	}

	private function __polygonePoints($pointsCnt, $rayon) {
		$angle = pi() / -2;
		$step = pi() * 2 / $pointsCnt;
		$points = array();
		for($i = 0; $i < $pointsCnt; $i++) {
			$points[] = round($rayon * cos($angle), 2).','.round($rayon * sin($angle), 2);
			$angle += $step;
		}
		return $points;
	}

	private function __starPoints($rayon) {
		$coef = 0.4;
		$angle = pi() / -2;
		$step = pi() / 5; // for angle
		$points = array();
		for($i=0; $i<10; $i++) {
			$r = (($i & 1) == 0) ? $rayon : $rayon * $coef;
			$points[] = round($r * cos($angle), 2).','.round($r * sin($angle), 2);
			$angle += $step;
		}
		return $points;
	}

	private function __createCross($length) {
		return self::__createElementWithAttributes('path',
			array('d' => implode(' ', array(
				"M-$length,0",
				"H$length",
				"M0,-$length",
				"V$length"
			)))
		);
	}

	private function __createDiagonal($length) {
		return self::__createElementWithAttributes('path',
			array('d' => implode(' ', array(
				"M-$length,-$length",
				"L$length,$length",
				"M-$length,$length",
				"L$length,-$length"
			)))
		);
	}

	private function __createMarks($parent) {
		$r0 = $this->graphArea['height'];
		$halfSize = round($r0 / 120, 2); // for square
		$this->marksCount = 0;
		foreach(array(
			'triangle'	=> self::__createElementWithAttributes('polygon', array( // 00
				'points' => implode(' ', self::__polygonePoints(3, $r0 / 75))
			)),
			'carreau'	=> self::__createElementWithAttributes('polygon', array( // 01
				'points' => implode(' ', self::__polygonePoints(4, $r0 / 90))
			)),
			'hexagon'	=> self::__createElementWithAttributes('polygon', array( // 02
				'points' => implode(' ', self::__polygonePoints(6, $r0 / 100))
			)),
			'star'		=> self::__createElementWithAttributes('polygon', array( // 03
				'points' => implode(' ', self::__starPoints($r0 / 70))
			)),
			'square'	=> self::__createElementWithAttributes('rect', array( // 04
				'x' => -$halfSize, 'y' => -$halfSize, 'width' => $halfSize * 2, 'height' => $halfSize * 2
			)),
			'circle'	=> self::__createElementWithAttributes('circle', array( // 05
				'cx' => 0, 'cy' => 0, 'r' => round($r0 / 100, 2)
			)),
			'cross'	=> self::__createCross(round($r0 / 100, 2)),  // 06
			'diagonal'	=> self::__createDiagonal(round($r0 / 120, 2)) // 07
		) as $form=>$node) {
			$node->setAttribute('id', sprintf(self::MARK_PATTERN, $this->marksCount));
			$node->setAttribute('data-shape', $form);
			$parent->appendChild($node);
			$this->marksCount++;
		}
	}

	private function __createGridHorizontal($parent, $heads) {
		$xH2 =  $this->graphArea['width'] + $this->paddings['right'] - $this->paddings['left'] - 2;
		$xH1 = $xH2 - $heads['y'];

		$parent->appendChild(self::__createElementWithAttributes('line', array(
			'id' => 'grid-h', 'x1' => -10, 'y1' => 0, 'x2' => $xH2, 'y2' => 0
		)));

		$parent->appendChild(self::__createElementWithAttributes('path', array(
			'id' => 'grid-h0',
			'd' => implode(' ', array(
				"M-10,0",
				"H$xH2",
				"M$xH1,-{$heads['x']}",
				"L$xH2,0",
				"$xH1,{$heads['x']}"
			)),
			'fill' => 'none'
		)));
	}

	private function __createGridVertical($parent, $heads) {
		$yV2 = -$this->paddings['top'] + 2;
		$yV1 = $yV2 + $heads['y'];
		$y2 = $this->graphArea['height'] + 5;

		$parent->appendChild(self::__createElementWithAttributes('line', array(
			'id' => 'grid-v', 'x1' => 0, 'y1' => $yV2, 'x2' => 0, 'y2' => $y2
		)));

		$parent->appendChild(self::__createElementWithAttributes('path', array(
			'id' => 'grid-v0',
			'd' => implode(' ', array(
				"M0,$yV2",
				"V$y2",
				"M-{$heads['x']},$yV1",
				"L0,$yV2",
				"L{$heads['x']},$yV1"
			)),
			'fill' => 'none'
		)));
	}

	private function __createGridDefinitions($parent) {
		$length = $this->graphArea['height'] / 30;
		$angle = pi() * 10 / 180; // 10deg
		$heads = array('x' => round($length * sin($angle), 2), 'y' => round($length * cos($angle), 2));
		self::__createGridHorizontal($parent, $heads);
		self::__createGridVertical($parent, $heads);
	}

	private function __createDefinitions() {
		$definitions = parent::createElement('defs');
		self::__createMarks($definitions);
		$this->__createGridDefinitions($definitions);
		return $definitions;
	}

	private function __createGrids($labels, $min, $max) {
		$grids = self::createElement('g');
		$grids->setAttribute('id', 'grid');

		// vertical grid
		$labelsCount = count($labels);
		$yText = $this->graphArea['height'] + $this->paddings['bottom'] - 2;
		for($i=0; $i<$labelsCount; $i++) {
			$x = round($i * $this->scale['x'], 2);
			$href = ($i == 0) ? '#grid-v0' : '#grid-v';
			$grids->appendChild(self::__createElementWithAttributes(
				'use',
				array('x' => $x, 'y' => 0, 'xlink:href' => $href)
			));
			$grids->appendChild(self::__createElementWithAttributes(
				'text',
				array('x' => $x, 'y' => $yText, 'text-anchor' => 'middle'),
				$labels[$i]
			));
		}

		// horizontal grid
		$dY = round(($max - $min) / 10);
		if($dY < 1) { $dY = 1; }
		$value = $min;
		// Using while(..) {...} is hazardeous
		for($i=0; $i < 50; $i++) {
			$y = round($this->graphArea['height'] - $value * $this->scale['y'], 2);
			$href = ($i == 0) ? '#grid-h0' :'#grid-h';

			$grids->appendChild(self::__createElementWithAttributes(
				'use',
				array('x' => 0, 'y' => $y, 'xlink:href' => $href)
			));
			$grids->appendChild(self::__createElementWithAttributes(
				'text',
				array('x' => -5, 'y' => $y - 5, 'text-anchor' => 'end'),
				$value
			));
			$value += $dY;
			if($value > $max) { break; }
		}

		$this->root->appendChild($grids);
	}

	private function __plotter($points, $start=0) {
		$start = 0;
		foreach($points as $i=>$y) {
			if($y !== null) {
				$start = $i;
				break;
			}
		}

		if(count($points) - $start < 2) {
			return '';
		}

		$stepX = $this->scale['x'];
		// Just a strait line for only two points
		if(count($points) - $start == 2) {
			return implode(' ', array(
				'M'.round($start * $stepX, 2).','.round($points[$i], 2),
				'L'.round(($start + 1) * $stepX, 2).','.round($points[$i + 1], 2)
			));
		}

		// Computes angle of slope for each point
		$slopes = array();
		$iMax = count($points) - 1;
		for($i=$start+1; $i<$iMax; $i++) {
			$angle1 = atan(($points[$i] - $points[$i-1]) / $stepX);
			$angle2 = atan(($points[$i+1] - $points[$i]) / $stepX);
			$slopes[] = ($angle1 + $angle2) / 2;
		}
		$radius = $stepX / 2.5; // Pressure for Bézier curve
		// First point
		$previousX = $start * $stepX;
		$previousY = $points[$start];
		$previousAngle = false;
		$commands = array('M'.round($previousX, 2).','.round($previousY, 2));
		// Next points
		for($i=$start+1; $i <= $iMax; $i++) {
			$x = $i * $stepX;
			$y = $points[$i];
			if($i == $start+1) {
				$currentAngle = $slopes[0];
				$xy = array(
					round($previousX, 2).','.round($previousY, 2),
					round($x - $radius * cos($currentAngle), 2).','.round($y - $radius * sin($currentAngle), 2)
				);
			} else {
				// Compute the two points of inflexion for the bézier curve
				if($i != $iMax) {
					$currentAngle = $slopes[$i - ($start + 1)];
					$xy = array(
						round($previousX + $radius * cos($previousAngle), 2).','.round($previousY + $radius * sin($previousAngle), 2),
						round($x - $radius * cos($currentAngle), 2).','.round($y - $radius * sin($currentAngle), 2)
					);
				} else {
					// Last point
					$xy = array(
						round($previousX + $radius * cos($previousAngle), 2).','.round($previousY + $radius * sin($previousAngle), 2),
						round($x, 2).','.round($y, 2)
					);
				}
			}
			$xy[] = round($x, 2).','.round($y, 2); // target point for a bézier curve
			$commands[] = 'C'.implode(' ', $xy);
			$previousX = $x;
			$previousY = $y;
			$previousAngle = $currentAngle;
		}

		return implode(' ', $commands);
	}

	private function __createGraphs($series, $labels, $colorsCount=1, $min=0, $noMore) {
		$graphs = self::__createElementWithAttributes('g',
			array('id' => 'graphs')
		);

		$color = rand(0, $colorsCount - 1);
		$mark = rand(0, $this->marksCount - 1);
		$labelStepY = $this->graphArea['height'] / 25;
		$labelY = 5;
		foreach($series as $name=>$coordsY) {
			$colorStr = sprintf(self::COLOR_PATTERN, $color % $colorsCount);
			$markStr = sprintf('#'.self::MARK_PATTERN, $mark % $this->marksCount);
			$graph = self::__createElementWithAttributes('g',
				array('class' => $colorStr, 'data-name' => $name)
			);
			// legend
			$legend = self::__createElementWithAttributes('g',
				array('class' => 'legend')
			);
			$legend->appendChild(self::__createElementWithAttributes('use',
				array('x' => $this->paddings['left'] / 2, 'y' => $labelY - 5, 'xlink:href' => $markStr)
			));
			$legend->appendChild(self::__createElementWithAttributes('text',
				array(
					'x' => $this->paddings['left'] / 2 + 10,
					'y' => $labelY,
					'baseline-shift' => '-50%'
				),
				$name
			));
			$graph->appendChild($legend);

			$scaleY = $this->scale['y'];
			$height = $this->graphArea['height'];
			$points = array_map(
				function($value) use($min, $scaleY, $height) {
					if($value === null) { return $value; }
					return $height - ($value - $min) * $scaleY;
				},
				$coordsY
			);

			// draws one bézier curve or line, first !
			$curve = self::__createElementWithAttributes('path',
				array('fill' => 'none', 'd' => self::__plotter($points))
			);
			$curve->appendChild(parent::createElement('title', $name));
			$graph->appendChild($curve);

			// draws marks
			foreach($points as $step=>$yPoint) {
				if($yPoint !== null) {
					$node = self::__createElementWithAttributes('use',
						array(
							'x' => round($step * $this->scale['x'], 2),
							'y' => round($yPoint, 2),
							'xlink:href' => $markStr,
							'data-graph' => "{$labels[$step]},{$coordsY[$step]}")
					);
					$node->appendChild(parent::createElement('title', "{$labels[$step]}\nvalue: {$coordsY[$step]}"));
					$graph->appendChild($node);
				}
			}

			$graphs->appendChild($graph);

			$labelY += $labelStepY;
			$color++;
			$mark++;
		}
		$this->root->appendChild($graphs);

		if(!$noMore) {
			// Trace marks and colors not in use for curves
			$notInUse = parent::createElement('g');
			$notInUse->setAttribute('id', 'not-in-use');
			$iMax = max(array($this->marksCount, self::COLORS_MAX));
			for($i=count($series); $i <$iMax; $i++) {
				$colorStr = sprintf(self::COLOR_PATTERN, $color % $colorsCount);
				$markStr = sprintf('#'.self::MARK_PATTERN, $mark % $this->marksCount);
				$notInUse->appendChild(self::__createElementWithAttributes('use', array(
					'x' => $this->paddings['left'] / 2, 'y' => $labelY - 5,
					'class' => $colorStr,
					'xlink:href' => $markStr
				)));
				$labelY += $labelStepY;
				$color++;
				$mark++;
			}
			$this->root->appendChild($notInUse);
		}

		$script = <<< SCRIPT

'use strict';
var graphs = document.getElementById('graphs');
var labels = graphs.getElementsByTagName('g');
var myToggle = function(event) {
	event.preventDefault();
	this.parentElement.classList.toggle('active');
}

for(var i=0, iMax=labels.length; i<iMax; i++) {
	if(labels[i].classList.contains('legend')) {
		labels[i].addEventListener('click', myToggle);
	}
}\n
SCRIPT;

		$node = self::__createElementWithAttributes(
			'script',
			array('type' => 'text/javascript')
		);
		$node->appendChild(parent::createCDATASection($script));
		$this->root->appendChild($node);
	}

	public function setGraph($labels, $series, $title=false, $noMore=false) {
		if(!empty($title)) {
			$this->root->appendChild($this->createElement('title', $title));
		}

		$min = PHP_INT_MAX;
		$max = PHP_INT_MIN;
		foreach($series as $key=> $values) {
			$m = max(array_values($values));
			$n = min(array_values($values));
			if($max < $m) { $max = $m; }
			if($min > $n) { $min = $n; }
		}

		if(count($labels) <= 1 or $min == $max) {
			// Division by zero
			return;
		}

		$this->scale = array(
			'x' => $this->graphArea['width'] / (count($labels) - 1),
			'y' => $this->graphArea['height'] / ($max - $min)
		);

		$this->__createGrids($labels, $min, $max);
		$this->__createGraphs($series, $labels, self::COLORS_MAX, $min, $noMore);
	}

} // End of class SVGraph
?>
