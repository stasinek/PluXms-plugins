<?php

if (!defined('PLX_ROOT')) exit;

// http://keith-wood.name/datepickRef.html

class datePicker extends plxPlugin {

	private $config = false;

	public $params = array('theme'=>'string');
	public $default_values = array('theme'=>'jquery');

	public function __construct($default_lang) {
		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# droits pour accéder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);
		$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
		$this->config = (basename($_SERVER["SCRIPT_NAME"], '.php') == 'parametres_plugin');
		if ($this->config) {
			$this->addHook('AdminFootEndBody', 'configFootEndBody');
		}
	}

	public function pluginRoot() {
		global $plxAdmin;
		return $plxAdmin->racine.$plxAdmin->aConf['racine_plugins'].__CLASS__.'/';
	}

	public function setCss($theme) {
		global $plxAdmin;

		$src = dirname(__FILE__).'/datepick/'.$theme.'.datepick.css';
		if (is_readable($src)) {
			$backendCssFilename = PLX_ROOT.PLX_CONFIG_PATH.'plugins/'.basename(__CLASS__).'.admin.css';
			$contents = file_get_contents($src);
			$contents .= <<< 'CONTENTS'
/* config.php */
#form_datePicker { border: 1px solid #222; width: 250px; border-radius: 10px; }
#form_datePicker label { display: inline-block; width: 80px; margin: 0 5px 0 0; text-align: right;}
#form_datePicker p:last-of-type { text-align: center; text-indent: none; }
CONTENTS;
			if 	(
					plxUtils::write(trim($contents), $backendCssFilename) and
					$plxAdmin->plxPlugins->cssCache('admin')
				) {
				plxMsg::Info(L_SAVE_FILE_SUCCESSFULLY);
			} else {
				plxMsg::Error(L_SAVE_FILE_ERROR);
			}
		}
	}

	public function OnActivate() {
		foreach($this->params as $field=>$type1) {
			$value = $this->default_values[$field];
			if ($type1 == 'numeric') {
				$value = intval($value);
			}
			$this->setParam($field, $value, $type1);
		}
		$this->saveParams();
		$this->setCss($this->default_values['theme']);
	}

	public function AdminTopEndHead($paramsl) {
		global $plxAdmin;

		$scriptname = basename($_SERVER["SCRIPT_NAME"], '.php');
		if (in_array($scriptname, array('article', 'statique', 'comment')) || $this->config) {
			$lang = $plxAdmin->aConf['default_lang'];
			if (! defined('JQUERY_LOADED')) { ?>
	<script type="text/javascript"> <!-- datepicker plugin -->
		if (typeof jQuery === 'undefined')
			document.write('<scr'+'ipt type="text/javascript" src='+'"<?php echo JQUERY_SRC; ?>"></scr'+'ipt>');
	</script>
<?php			define('JQUERY_LOADED', true);
			} ?>
	<script type="text/javascript" src="<?php echo $this->pluginRoot(); ?>datepick/jquery.plugin.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->pluginRoot(); ?>datepick/jquery.datepick.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->pluginRoot(); ?>datepick/jquery.datepick-<?php echo $lang; ?>.js"></script>
	<script type="text/javascript">
		<!--
		$(function() {
<?php	if (! $this->config) { ?>
			$.datepick.setDefaults({
				dateFormat: 'dd',
				altFormat: 'mm',
				changeMonth: true,
				changeYear: true,
				selectDefaultDate: true,
				showOtherMonths: true,
				showButtonPanel: true,
				onChangeMonthYear: function(year1, month1) {
					if ((typeof this.update === 'undefined') || ! this.update) {
						// take care about recursivity
						this.update = true;
						$('#'+this.id.replace(/day$/i, 'year')).val(year1);
						$('#'+this.id).datepick('option', 'defaultDate', new Date(
							year1,
							month1-1,
							parseInt($('#'+this.id).val())
						));
						this.update = false;
					}
				},
				onClose: function(dates) {
					if (dates.length > 0) {
						$('#'+this.id).datepick('option', {defaultDate: dates[0]});
					}
				}
			});

			'id id_date_publication id_date_creation id_date_update'.split(' ').forEach(function (id) {
				if (document.getElementById(id+'_day')) {
					var y = $('#'+id+'_year').val(),
						m = $('#'+id+'_month').val(),
						date1;
					if ((y.length == 0) || (m.length == 0) || isNaN(y) || isNaN(m)) {
						date1 = new Date();
						$('#'+id+'_year').val(date1.getFullYear());
					} else {
						var d = $('#'+id+'_day').val();
						if ((d.length == 0) || isNaN(d)) {
							date1 = new Date(parseInt(y), parseInt(m)-1);
						} else {
							date1 = new Date(parseInt(y), parseInt(m)-1, parseInt(d));
						}
					}
					var options = {
						defaultDate: date1,
						altField: '#'+id+'_month'
					};
					$('#'+id+'_day').datepick(options);
					$('#'+id+'_month, #'+id+'_year').focus(function(params) {
						var selector = this.id.replace(/(month|year)$/, 'day');
						$('#'+selector).datepick('show');
					});
				}
			});
<?php	} ?>
		});
	// -->
	</script>
<?php	}
	}

	public function configFootEndBody() {
		global $plxAdmin;

		$pluginRoot = $this->pluginRoot();
		$theme = $this->getParam('theme');
		$lang = $plxAdmin->aConf['default_lang'];
		$content = <<< CONTENT
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<base href="{$pluginRoot}" />
	<link id="theme-datepicker" type="text/css" rel="stylesheet" href="datepick/{$theme}.datepick.css" />
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="datepick/jquery.plugin.min.js"></script>
	<script type="text/javascript" src="datepick/jquery.datepick.min.js"></script>
	<script type="text/javascript" src="datepick/jquery.datepick-{$lang}.js"></script>
	<title>Demo datepick</title>
</head>
<body>
	<div id="demo-datepicker"></div>
	<script type="text/javascript">
		var	selector = "#demo-datepicker",
			options = {onSelect: function(date) {
				alert(date.toLocaleString());
			}};
		\$(selector).datepick(options);
	</script>
</body>
</html>
*/
CONTENT;
?>
	<script type="text/javascript">
		<!--
		function heredoc (f) { return f.toString().match(/\/\*\s*([\s\S]*?)\s*\*\//m)[1]; };

		var myFrame = document.getElementById('myFrame');
		if (myFrame) {
			var doc = myFrame.contentWindow.document;
			doc.open();
			doc.write(heredoc(function() {
<?php echo $content; ?>
			}));
			doc.close();
			var select = document.getElementById('id_theme');
			if (select) {
				select.addEventListener('change', function (event) {
					event.preventDefault();
					var	myFrame = document.getElementById('myFrame'),
						link1 = myFrame.contentWindow.document.getElementById('theme-datepicker'),
						href = 'datepick/'+this.value+'.datepick.css';
					link1.setAttribute('href', href);
				});
			}
		}
		// -->
	</script>
<?php
	}

}
/*
 *
function updateLinked(dates) {
    $('#linkedMonth').val(dates.length ? dates[0].getMonth() + 1 : '');
    $('#linkedDay').val(dates.length ? dates[0].getDate() : '');
    $('#linkedYear').val(dates.length ? dates[0].getFullYear() : '');
}

$('#linkedPicker').datepick({
    alignment: 'bottomRight', onSelect: updateLinked,
    showTrigger: '#calImg'});

$('#linkedMonth,#linkedDay,#linkedYear').change(function() {
    $('#linkedPicker').datepick('setDate', new Date(
        parseInt($('#linkedYear').val(), 10),
        parseInt($('#linkedMonth').val(), 10) - 1,
        parseInt($('#linkedDay').val(), 10)));
});
* */
?>
