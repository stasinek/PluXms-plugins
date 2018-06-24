<!--
/**
 * tinyEditor
 *
 * @author	Jean-Pierre Pourrez
 *
 * Some parts come from plxEditor (http://wiki.pluxml.org/index.php?page=Plugins+officiels)
 **/

function E$(id){return document.getElementById(id)}

TINYEDITOR = {
	settings: {
		urlBase: null,
		popupWarning: 'Unable to open a window popup',
		insertMedia: "Insert the media",
		cancel: 'Cancel',
		submit: 'Submit',
		musttextmode: 'Switch in text mode for saving',
		missingFormat: 'Your browser don\'t support #1# format',
		disabledCmd: 'This command is disabled in html mode'
		},
	editors: {},
	activeEditor: null,

	/* **************** dialog object ************* */
	dialog: function() {
		return {
			close:function(obj){
				var dialog = E$(obj);
				if(dialog!=null) {
					document.body.removeChild(dialog); return;
				}
			},

			getPosition: function getPosition(element) {
			    var xPosition = 0;
			    var yPosition = 0;

			    while(element) {
			        xPosition += (element.offsetLeft - element.scrollLeft + element.clientLeft);
			        yPosition += (element.offsetTop - element.scrollTop + element.clientTop);
			        element = element.offsetParent;
			    }
			    return { x: xPosition, y: yPosition };
			},

			setPosition: function(element, origin) {
				var position = this.getPosition(origin);
				element.style.left = position.x + 'px';
				element.style.top = (position.y + 22) +'px';
			}
		}
	}(),

	/* **************** cpicker object ************ */
	cpicker: function(){

		function create(editor, button, action){
			this.editor = editor;
			this.button = button;
			this.action = action;
			if(E$('tinyEditor-cpicker')) return TINYEDITOR.dialog.close('tinyEditor-cpicker');
			this.displayPanel();
		}

		create.prototype.displayPanel=function(){
			var	elemDiv = document.createElement('div');
			elemDiv.id = 'tinyEditor-cpicker';
			elemDiv.className = 'tinyEditor-popup';
			elemDiv.editor = this.editor;
			elemDiv.action = this.action;
			elemDiv.innerHTML = this.panel();
			elemDiv.addEventListener('click', function(event) {
				if (event.target.tagName == 'A') {
					TINYEDITOR.editors[this.editor].execCommand(this.action, event.target.title);
					TINYEDITOR.dialog.close('tinyEditor-cpicker');
				}
			});
			TINYEDITOR.dialog.setPosition(elemDiv, this.button);
			document.body.appendChild(elemDiv);
		};

		create.prototype.panel=function() {
			var colors = [
				'ffffff ffcccc ffcc99 ffff99 ffffcc 99ff99 99ffff ccffff ccccff ffccff',
				'cccccc ff6666 ff9966 ffff66 ffff33 66ff99 33ffff 66ffff 9999ff ff99ff',
				'c0c0c0 ff0000 ff9900 ffcc66 ffff00 33ff33 66cccc 33ccff 6666cc cc66cc',
				'999999 cc0000 ff6600 ffcc33 ffcc00 33cc00 00cccc 3366ff 6633ff cc33cc',
				'666666 990000 cc6600 cc9933 999900 009900 339999 3333ff 6600cc 993399',
				'333333 660000 993300 996633 666600 006600 336666 000099 333399 663366',
				'000000 330000 663300 663333 333300 003300 003333 000066 330099 330033'
			];

			var table = '<table>';
			colors.forEach(function (row, y, rows) {
				table += '<tr>';
				row.split(' ').forEach(function (color, x, cells){
					table += '<td><a href="#" title="#'+color+'" style="background-color: #'+color+';">&nbsp;</a></td>';
				});
				table += '</tr>';
			});
			table += '</table>';
			return table;
		};

		return { create:create }
	}(),

	/* **************** smilies object ************ */
	smilies: function(){

		function create(editor, button, action){
			this.editor = editor;
			this.button = button;
			this.action = action;
			if(E$('tinyEditor-smilies')) return TINYEDITOR.dialog.close('tinyEditor-smilies');
			this.displayPanel();
		}

		create.prototype.displayPanel=function(){
			var elemDiv = document.createElement('div');
			elemDiv.id = 'tinyEditor-smilies';
			elemDiv.className = 'tinyEditor-popup';
			elemDiv.editor = this.editor;
			elemDiv.innerHTML = this.panel();
			elemDiv.addEventListener('click', function(event) {
				var target = event.target;
				if (target.tagName == 'A') {
					event.preventDefault();
					var smiley = target.getAttribute('data-smiley');
					if (smiley) {
						TINYEDITOR.editors[this.editor].execCommand(
							'InsertImage',
							TINYEDITOR.settings.smiliesPath+smiley
						);
					}
					TINYEDITOR.dialog.close('tinyEditor-smilies');
				}
			});
			TINYEDITOR.dialog.setPosition(elemDiv, this.button);
			document.body.appendChild(elemDiv);
		};

		create.prototype.panel=function() {
			var smilies = [
				'big_smile.png cool.png hmm.png icon_arrow.gif icon_eek.gif icon_exclaim.gif',
				'icon_question.gif icon_redface.gif icon_twisted.gif lol.png mad.png neutral.png',
				'roll.png sad.png smile.png tongue.png wink.png yikes.png'
			];

			var html = '<table>';
			smilies.forEach(function(row, y, rowsList) {
				html += '<tr>';
				row.split(' ').forEach(function (cell, x, cellsList) {
					html += '<td><a href="#" title="'+TINYEDITOR.i18n(cell.replace(/\.\w+$/, ''))+'" data-smiley="'+cell+'">&nbsp;</a></td>';
				});
				html += '</tr>';
			});
			html += '</table>';
			return html;
		};

		return { create: create }
	}(),

	/* **************** linker object ************* */
	linker: function() {

		function create(editor, button, selection){
			this.editor = editor;
			this.button = button;
			this.selection = selection;
			if(E$('tinyEditor-linker')) return TINYEDITOR.dialog.close('tinyEditor-linker');
			this.showPanel();
		}

		create.prototype.showPanel=function(){
			var elemDiv = document.createElement('div');
			elemDiv.id = 'tinyEditor-linker';
			elemDiv.editor = this.editor;
			elemDiv.className = 'tinyEditor-popup';
			elemDiv.innerHTML = this.panel();
			elemDiv.addEventListener('click', function (event) {
				var target = event.target;
				if ((target.tagName == 'INPUT') && (target.type == 'submit') &&/^(?:save|cancel)$/i.test(target.name)) {
					event.preventDefault();
					if (/^save$/i.test(target.name)) {
						TINYEDITOR.linker.setLink(TINYEDITOR.editors[this.editor]);
					}
					TINYEDITOR.dialog.close('tinyEditor-linker');
				}
			});
			TINYEDITOR.dialog.setPosition(elemDiv, this.button);
			document.body.appendChild(elemDiv);
		};

		create.prototype.panel=function() {
			var href = (this.selection.href) ? this.selection.href : 'http://',
				text = (this.selection.text) ? this.selection.text : '',
				title = (this.selection.title) ? this.selection.title : '',
				className = (this.selection.className) ? this.selection.className : '',
				rel = (this.selection.rel) ? this.selection.rel : '',
				target = (this.selection.target) ? this.selection.target : '',
				optionsRel = '<option value=""></option>',
				optionsTarget = '<option value=""></option>';
			'\
alternate author bookmark contact external help license next nofollow \
noreferrer prefetch prev search tag'.split(' ').forEach(function(value) {
				var selected = (value == rel) ? ' selected' : '';
				optionsRel += '<option value"'+value+'"'+selected+'>'+value+'</option>';
			});
			['_blank', '_parent', '_top'].forEach(function(value) {
				var selected = (value == target) ? ' selected' : '';
				optionsTarget += '<option value"'+value+'"'+selected+'>'+value+'</option>';
				});
			return '\
<form>\
	<p><label for="txt_href">Href</label><input type="text" value="'+href+'" id="txt_href" /></p>\
	<p><label for="txt_text">Text</label><input type="text" value="'+text+'" id="txt_text" /></p>\
	<p><label for="txt_title">Title</label><input type="text" value="'+title+'" id="txt_title" /></p>\
	<p><label for="txt_class">Class</label><input type="text" value="'+className+'" id="txt_class" /></p>\
	<p><label for="txt_rel">Rel</label><select id="txt_rel">'+optionsRel+'</select></p>\
	<p><label for="txt_target">Target</label><select id="txt_target">'+optionsTarget+'</select></p>\
	<p><input type="submit" name="save" value="'+TINYEDITOR.i18n('submit')+'" /><input type="submit" name="cancel" value="'+TINYEDITOR.i18n('cancel')+'" /></p>\
</form>';
		};

		return {
			create: create,
			setLink: function(editor) {
				var href = E$('txt_href') ? E$('txt_href').value.trim() : '',
					sTtext	= E$('txt_text') ? E$('txt_text').value.trim() : '';
				if(sTtext=='' || TINYEDITOR.linker.isUrl(href)==false) {
					alert('href et text obligatoires');
					return;
				}
				var	attrs = ' href="'+href+'"';
				['title', 'class', 'rel', 'target'].forEach(
					function (fieldName) { attrs += TINYEDITOR.linker.getValue(fieldName); }
				);
				editor.execCommand('inserthtml', '<a'+attrs+'>'+sTtext+'</a> ');
			},
			getValue: function(name) {
				var elm = E$('txt_'+name);
				return (elm && elm.value && (elm.value.length > 0)) ? ' '+name+'="'+elm.value+'"' : '';
			},
			isUrl: function(s) {
				return /(ftp|https?):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/.test(s);
			}
		}
	}(),

	/* **************** editor object ************* */
	editor: function() {

		function create(name){
			this.editor = name;
			this.popup = null;
			this.viewSource = false;
			this.viewFullscreen = false;
			// browser detection
			var ie = 0;
			try {
				ie = navigator.userAgent.match( /(MSIE |Trident.*rv[ :])([0-9]+)/ )[ 2 ];
			}
			catch(e){
			}
			this.browser = {
				"ie": ie,
				"gecko" : (navigator.userAgent.toLowerCase().indexOf("gecko") != -1)
			}
			var textpad = E$('id_'+this.editor);
			// Add event to update the textarea with content in iframe when user submits form
			var aForm = textpad.form;
			if (typeof aForm.editors == 'undefined') {
				aForm.editors = name;
				aForm.addEventListener('submit', function (event) {
					// Don't set event.preventDefault() here. The event must go on playing.
					this.editors.split(' ').forEach(function (ed1) {
						TINYEDITOR.editors[ed1].updateTextArea();
						});
					var context = {}
					for (ed in TINYEDITOR.editors) {
						var	fs = TINYEDITOR.editors[ed].viewFullscreen,
							ifr = E$('id_'+ed+'-iframe'),
							ht = (ifr && (ifr.offsetHeight > 0)) ? ifr.offsetHeight+'px' : '';
						context[ed] = {viewFullscreen: fs, iframe: ht};
					}
					sessionStorage.setItem('TINYEDITOR', JSON.stringify(context));
				});
			} else {
				aForm.editors += ' '+name;
				}
			// EDITOR
			var editor = document.createElement("div");
			editor.id = 'id_'+this.editor+"-wysiwyg";
			editor.className = 'tinyEditor';
			editor.innerHTML = this.getEditorHtml();
			// Hack for resizable iframes
			var grid = textpad.parentNode.parentNode;
			if (this.editor == 'chapo') {
				grid = grid.parentNode;
			}
			if (grid.className) {
				grid.className += ' tinyEditor-grid';
			} else {
				grid.className = ' tinyEditor-grid';
			}
			if (! TINYEDITOR.timer1) {
				TINYEDITOR.timer1 = setInterval(TINYEDITOR.resize, 400); // millisecondes
			}
			// get the initial data
			this.textareaValue = textpad.value;
			textpad.parentNode.replaceChild(editor, textpad);
			// play with toolbar
			editor.querySelector('.tinyEditor-toolbar').editor = this.editor;
			editor.querySelector('.tinyEditor-toolbar').addEventListener('click', function(event) {
				var target = event.target;
				if ((target.tagName == 'A') && (target.hasAttribute('data-tool'))) {
					event.preventDefault();
					var	cmd = target.getAttribute('data-tool');
						editor = TINYEDITOR.editors[this.editor];
					if (! this.viewSource || /^(html|fullscreen)$/.test(cmd)) {
						editor.execCommand(cmd, null, target);
					} else {
						alert(TINYEDITOR.i18n('disabledCmd'));
					}
				}
			});
			editor.querySelector('select').editor = this.editor;
			editor.querySelector('select').addEventListener('change', function(event) {
				event.preventDefault();
				var target = event.target;
				TINYEDITOR.editors[this.editor].execCommand('formatblock', '<'+event.target.value+'>');
				event.target.selectedIndex=0;
			});
			// FRAME
			this.frame = E$('id_'+this.editor+"-iframe").contentWindow;
			this.frame.document.open();
			this.frame.document.write(this.getFrameHtml());
			this.frame.document.close();
			this.frame.document.designMode = "on";
			this.setFrameContent();
			// count the words in the frame
			this.frame.document.body.editor = this.editor;
			this.frame.document.body.addEventListener('keyup', function(event) {
				TINYEDITOR.editors[this.editor].updateWordsCounter();
			});
			// shortcut key for saving document
			this.frame.document.body.addEventListener('keypress', function(event) {
				if (event.ctrlKey && (event.key == 's')) {
					event.preventDefault();
					TINYEDITOR.editors[this.editor].documentSave();
				}
			});
			this.updateWordsCounter();
		}

		create.prototype.documentSave=function() {
			if (this.viewSource) {
				alert(TINYEDITOR.i18n('musttextmode'));
			} else {
				var formObj = E$('id_'+this.editor).form;
				if (formObj) {
					var result = ['update', 'draft', 'create'].every(function(name) {
						if (formObj[name]) {
							formObj[name].click();
							return false;
						} else {
							return true;
						}
					});
					if (result) {
						var elm = formObj.querySelector("input[type='submit']");
						if (elm) {
							elm.click();
						}
					}
				}
			}
		}

		create.prototype.updateWordsCounter=function(evt) {
			if(this.viewSource === false) {
				// words counter
				var text = null;
				if (document.body.innerText) {
					txt = this.frame.document.body.innerText;
				} else {
					txt = this.frame.document.body.innerHTML.replace(/<br>/gi,'\n');
					txt = txt.replace(/<\/?[^>]+(>|$)/g, '');
				}
				var count = txt!=undefined ? txt.split(/\b\S+\b/g).length - 1 : 0;
				E$('id_'+this.editor+'-footer').innerHTML = (count > 1) ? count + ' ' + TINYEDITOR.settings.words : '';
			}
		};

		create.prototype.getEditorHtml=function() {

			var	tools = '\
bold italic underline strikethrough | \
image forecolor backcolor link unlink removeformat | \
justifyleft justifycenter justifyright | \
insertorderedlist insertunorderedlist | \
outdent indent | subscript superscript smilies | html fullscreen',
				html = '\
<input type="hidden" id="id_'+this.editor+'" name="'+this.editor+'" value="" />\
<div class="tinyEditor-toolbar">\
	<select>\
		<option value="">Style</option>\
		<option value="h1">H1</option>\
		<option value="h2">H2</option>\
		<option value="h3">H3</option>\
		<option value="h4">H4</option>\
		<option value="h5">H5</option>\
		<option value="h6">H6</option>\
		<option value="div">Div</option>\
		<option value="p">P</option>\
		<option value="pre">Pre</option>\
	</select>';
			tools.split(' ').forEach(function (tool, index, list1) {
				if (tool == '|') {
					html += '<span>|</span>';
				} else {
					html += '<a id="'+tool+'-tool" href="#" data-tool="'+tool+'" title="'+TINYEDITOR.i18n(tool)+'">&nbsp;</a>';
				}
			});
			// iframe
			html += '\
</div>\
<iframe id="id_'+this.editor+'-iframe" class="iframe resizable" frameborder="0"></iframe>\
<div id="id_'+this.editor+'-footer" class="tinyEditor-footer"></div>';
			return html;
		};

		create.prototype.getselection=function() {
			var win = this.frame;
			if (this.browser.ie && this.browser.ie < 11) {
				// old browser
				try {
					var	doc = win.document,
						sel = doc.selection;
					return sel.createRange();
				} catch (e2) {
					return win.getSelection();
				}
			} else {
				var selection = win.getSelection();
				var node = selection.getRangeAt(0);
				if (node.collapsed) {
					return {text: ''};
				} else {
					if (node.startContainer.nodeType == Node.ELEMENT_NODE) {
						var child = node.startContainer.childNodes[node.startOffset];
						if (child.tagName == 'A') {
							var result = { text: node.toString() };
							['href', 'title', 'className', 'rel', 'target'].forEach(function(attr) {
								if (child.hasAttribute(attr)) { result[attr] = child.getAttribute(attr); }
							});
							return result;
						} else {
							return { text: node.toString() }
							}
					} else {
						return { text: node.toString() };
					}
				}
			}
		};

		create.prototype.pasteHTML=function(html) {
			var sel = this.frame.document.getSelection();
			if (sel.getRangeAt && sel.rangeCount) {
				range = sel.getRangeAt(0);
				range.deleteContents();
			}

			var el = this.frame.document.createElement("div");
			el.innerHTML = html;
			var frag = this.frame.document.createDocumentFragment(), node, lastNode;
			while ( (node = el.firstChild) ) {
				lastNode = frag.appendChild(node);
			}

			var firstNode = frag.firstChild;
			range.insertNode(frag);

			if (lastNode) {
				range = range.cloneRange();
				range.setStartAfter(lastNode);
				if (selectPastedContent) {
					range.setStartBefore(firstNode);
				} else {
					range.collapse(true);
				}
					sel.removeAllRanges();
					sel.addRange(range);
			}
		};

		// dispatcher for each command editor
		create.prototype.execCommand=function(cmd, value, button) {
			if (! this.viewSource || /^(html|fullscreen)$/.test(cmd)) {
				this.frame.focus();
				if (cmd == "image" && !value) {
					activeEditor = TINYEDITOR.editors[this.editor];
					this.openPopup(TINYEDITOR.settings.mediasManager, this.editor, 760, 580);
				} else if (cmd == "link" && !value) {
					new TINYEDITOR.linker.create(this.editor, button, this.getselection());
				} else if (/(?:forecolor|backcolor)/.test(cmd) && !value) {
					new TINYEDITOR.cpicker.create(this.editor, button, cmd);
				} else if (cmd == "smilies" && !value) {
					new TINYEDITOR.smilies.create(this.editor, button, cmd);
				} else if (cmd == "html" && !value) {
					this.toggleSource(button);
				} else if (cmd == "fullscreen" && !value) {
					this.toggleFullscreen(button);
				} else if (cmd == "inserthtml" && this.browser.ie) { // IE
					if(this.viewSource==true) return;
					this.pasteHTML(value);
				} else {
					if(this.viewSource==true) return;
					this.frame.document.execCommand(cmd, false, value);
				}
				this.frame.focus();
			} else {
				alert(TINYEDITOR.i18n('disabledCmd'));
			}
		};

		create.prototype.updateTextArea=function() {
			if(this.viewSource) {
				this.toggleSource();
			}
			var txt = this.frame.document.body.innerHTML;
			// transforms absolute url in local url
			txt = txt.replace('="'+TINYEDITOR.settings.urlBase, '="');
			txt = this.toXHTML(txt);
			// send final value
			E$('id_'+this.editor).value = txt;
		};

		create.prototype.setFrameContent=function () {
			try {
				this.frame.document.body.innerHTML = this.textareaValue;
				this.frame.document.editor = this.editor;
			} catch (e) {
				setTimeout(this.setFrameContent, 10);
			}
		};

		create.prototype.getFrameHtml=function() {
			// don't use src attribute in iframe, except waiting of end of page is loading (event load)
			return '\
<!DOCTYPE html>\
<html>\
	<head>\
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">\
		<style type="text/css">\
			html, body { font-family: helvetica, arial, sans-serif; cursor: text; }\
			body { margin: 0.5em; padding: 0; } img { border:none; }\
			pre { background-color: #fff; padding: 0.75em 1.5em; border: 1px solid #dddddd;* }\
			body.view-source { font: \'Courier New\' 13px; }\
			.view-source .tag { color:#000099; margin:0; display:block; }\
			.view-source blockquote { margin:0 0 0 15px; }\
			.view-source b { font-weight:bold; }\
			.view-source .literal { color:#0000FF; }\
			.view-source .comment {	color:#CCCCCC;	display:block; }\
		</style>\
	</head>\
	<body></body>\
</html>';
		};

		create.prototype.toggleFullscreen = function(button) {
			this.viewFullscreen = ! this.viewFullscreen;
			var f = this.viewFullscreen;
			['#id_'+this.editor+'-wysiwyg', 'body > main'].forEach(function (rule) {
				var elm = document.querySelector(rule);
				if (elm) {
					if (elm.className) {
						if (f) {
							if (! /\bfullscreen\b/.test(elm.className)) { elm.className += ' fullscreen';}
						} else {
							elm.className = elm.className.replace(/\s+fullscreen/, '');
						}
					} else if (f) {
						elm.className = 'fullscreen';
					}
				}
			});
			this.frame.focus();
		};

		create.prototype.getViewportHeight=function() {
			var height;
			if (window.innerHeight!=window.undefined) {
				height=window.innerHeight;
			} else if (document.compatMode=='CSS1Compat') {
				height=document.documentElement.clientHeight;
			} else if (document.body) {
				height=document.body.clientHeight;
			}
			return height-100;
		};

		create.prototype.toggleSource=function(button) {
			// button is unused
			// flip-flop
			this.viewSource = ! this.viewSource;
			var txt;
			if (this.viewSource) {
				txt = this.frame.document.body.innerHTML;
				txt = this.toXHTML(txt);
				txt = this.formatHTML(txt);
				this.frame.document.body.innerHTML = txt.toString();
			} else {
				if (this.browser.ie) {
					txt = this.frame.document.body.innerText;
					this.frame.document.body.innerHTML = txt;
				} else {
					var html = this.frame.document.body.ownerDocument.createRange();
					html.selectNodeContents(this.frame.document.body);
					// txt = this.convertLinks(html.toString(), 0);
					txt = html.toString();
					this.frame.document.body.innerHTML = txt;
				}
			}
			// change icon image on the toolbar
			var	toolbar = document.querySelector('#id_'+this.editor+'-wysiwyg div.tinyEditor-toolbar'),
				select = document.querySelector('#id_'+this.editor+'-wysiwyg select');
			if (this.viewSource) {
				toolbar.className = toolbar.className + ' viewSource';
			} else {
				toolbar.className = toolbar.className.replace(/\s+viewSource/gi, '');
			}
			select.disabled = this.viewSource;
			// button.className = (this.viewSource) ? '' : 'text';
			// set stylesheet rules
			this.frame.document.body.className = (this.viewSource) ? 'view-source' : '';
		};

		create.prototype.toXHTML=function(v) {
			function lc(str){return str.toLowerCase()}
			function sa(str){return str.replace(/("|;)\s*[A-Z-]+\s*:/g,lc);}
			v=v.replace(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\)/gi, function toHex($1,$2,$3,$4) { return '#' + (1 << 24 | $2 << 16 | $3 << 8 | $4).toString(16).substr(1); });
			v=v.replace(/<span class="apple-style-span">(.*)<\/span>/gi,'$1');
			v=v.replace(/s*class="apple-style-span"/gi,'');
			v=v.replace(/s*class="webkit-indent-blockquote"/gi,'');
			v=v.replace(/<span style="">/gi,'');
			v=v.replace(/<b\b[^>]*>(.*?)<\/b[^>]*>/gi,'<strong>$1</strong>');
			v=v.replace(/<i\b[^>]*>(.*?)<\/i[^>]*>/gi,'<em>$1</em>');
			v=v.replace(/<(s|strike)\b[^>]*>(.*?)<\/(s|strike)[^>]*>/gi,'<span style="text-decoration: line-through;">$2</span>');
			v=v.replace(/<u\b[^>]*>(.*?)<\/u[^>]*>/gi,'<span style="text-decoration:underline">$1</span>');
			v=v.replace(/<(b|strong|em|i|u) style="font-weight: normal;?">(.*)<\/(b|strong|em|i|u)>/gi,'$2');
			v=v.replace(/<(b|strong|em|i|u) style="(.*)">(.*)<\/(b|strong|em|i|u)>/gi,'<span style="$2"><$4>$3</$4></span>');
			v=v.replace(/<blockquote .*?>(.*?)<\/blockquote>/gi,'<blockquote>$1<\/blockquote>');
			v=v.replace(/<span style="font-weight: normal;?">(.*?)<\/span>/gi,'$1');
			v=v.replace(/<span style="font-weight: bold;?">(.*?)<\/span>/gi,'<strong>$1</strong>');
			v=v.replace(/<span style="font-style: italic;?">(.*?)<\/span>/gi,'<em>$1</em>');
			v=v.replace(/<span style="font-weight: bold;?">(.*?)<\/span>|<b\b[^>]*>(.*?)<\/b[^>]*>/gi,'<strong>$1</strong>')
			v=v.replace(/BACKGROUND-COLOR/gi,'background-color');
			//v=v.replace(/<div><br \/><\/div>/gi, '<p></p>');
			v=v.replace(/<(IMG|INPUT|BR|HR|LINK|META)([^>]*)>/gi,"<$1$2 />") //self-close tags
			v=v.replace(/(<\/?[A-Z]*)/g,lc) // lowercase tags
			v=v.replace(/STYLE="[^"]*"/gi,sa); //lc style atts
			return v;
		};

		create.prototype.formatHTML=function(html) {
			//strip white space
			html = html.replace(/\s/g, ' ');
			//convert html to text
			html = html.replace(/&/g, '&amp;');
			html = html.replace(/</g, '&lt;');
			html = html.replace(/>/g, '&gt;');
			//change all attributes " to &quot; so they can be distinguished from the html we are adding
			html = html.replace(/="/g, '=&quot;');
			html = html.replace(/=&quot;(.*?)"/g, '=&quot;$1&quot;');
			//search for opening tags
			html = html.replace(/&lt;([a-z](?:[^&|^<]+|&(?!gt;))*?)&gt;/gi, "<span class=\"tag\">&lt;$1&gt;</span><blockquote>");
			//Search for closing tags
			html = html.replace(/&lt;\/([a-z].*?)&gt;/gi, "</blockquote><span class=\"tag\">&lt;/$1&gt;</span>");
			//search for self closing tags
			html = html.replace(/\/&gt;<\/span><blockquote>/gi, "/&gt;</span>");
			//Search for values
			html = html.replace(/&quot;(.*?)&quot;/gi, "<span class=\"literal\">\"$1\"</span>");
			//search for comments
			html = html.replace(/&lt;!--(.*?)--&gt;/gi, "<span class=\"comment\">&lt;!--$1--&gt;</span>");
			//search for html entities
			html = html.replace(/&amp;(.*?);/g, '<b>&amp;$1;</b>');
			return html;
		};

		create.prototype.openPopup=function(fichier, editor, width, height) {
			// https://developer.mozilla.org/fr/docs/Web/API/Window/open

			var	left = parseInt((window.screen.width - width) / 2),
				top = parseInt((window.screen.height - height) / 2),
				options = 'location=no, resizable=no, scrollbars=yes, modal=yes, dialog=yes, width='+width+' , height='+height+', left='+left+', top='+top;
			var win = window.open(unescape(fichier) , 'tinyEditor-medias', options);
			if(win) {
				TINYEDITOR.activeEditor = editor;
				win.focus();
			} else {
				alert(TINYEDITOR.settings[popupWarning]);
			}
			return;
		};

		return {create: create}
	}(),

	init : function(selector, options) {
		for (o in options) {
			this.settings[o] = options[o];
		}
		this.activeEditor = null;
		this.editors = {};
		var targets = document.querySelectorAll(selector);
		for (i=0, iMax=targets.length; i<iMax; i++) {
			var	textArea = targets[i],
				name = textArea.name;
			this.editors[name] = this.editor.create(name);
		}

	},

	insertMedia: function(html) {
		var editor = this.editors[this.activeEditor]
		editor.execCommand('inserthtml', html);
	},

	i18n: function(text) {
		return (text in this.settings) ? this.settings[text] : text;
	},

	resize: function() {
		var isViewFullscreen = ! Object.keys(TINYEDITOR.editors).every(function(ed) { return ! TINYEDITOR.editors[ed].viewFullscreen})
		var containers = document.querySelectorAll('.tinyEditor-grid > div');
		for (i=0, iMax=containers.length; i<iMax; i++) {
			var	container = containers[i],
				chapo = container.querySelector('#toggle_chapo');
			// Here is a toggle chapo
			if (chapo && (chapo.style.display == 'none')) {
				container.parentNode.style.height = '';
			}
			else {
				var	h = container.getBoundingClientRect().height,
					ifr = container.querySelector('iframe');
				if (ifr) {
					if (isViewFullscreen) {
						ifr.style.height = '';
						container.prevHeight = null;
					} else {
						if (! container.prevHeight || (Math.abs(container.prevHeight - h) > 4)) {
							container.prevHeight = h;
							// compute the empty space height
							var children = container.children;
							for (j=0, jMax=children.length; j<jMax; j++) {
								h -= children[j].offsetHeight;
							}
							ifr.style.height = (ifr.offsetHeight + h - 4) + 'px';
						}
					}
				}
			}
		}
	}
};

// used by medias.php
function mediasSet(selector) {
	if (window.opener && window.opener.TINYEDITOR && /^.*\/(?:article|statique)\.php(\?[^/]*)?/.test(window.opener.location.href)) {
		var style = document.createElement('style');
		style.type = 'text/css';
		style.innerHTML = '\
aside { display: none; } \
.main.grid { margin-left: 0; width: 100%; } \
.main.grid section { margin-left: 0; width: 100%; } \
#form_medias > div:first-of-type { left: 0; } \
#files_list { min-height: 6em; padding: 5px; border: 1px solid #999; } \
#files_list + div { display: flex; } \
';
		document.head.appendChild(style);
		var tbody = document.querySelector(selector);
		if (tbody) {
			tbody.message = window.opener.TINYEDITOR.i18n('insertMedia');
			tbody.urlBase = window.opener.TINYEDITOR.settings.urlBase;
			tbody.addEventListener('click', function (event) {
				var target = event.target;
				if (target.tagName == 'A') {
					var href = target.href;
					var motif = /^.*\/([^\/]+\.(?:jpg|jpeg|png|gif|epub|7z|aiff|asf|avi|csv|doc|docx|epub|fla|flv|gz|gzip|mid|mov|mp3|mp4|mpc|mpeg|mpg|ods|odt|odp|ogg|pdf|ppt|pptx|pxd|qt|ram|rar|rm|rmi|rmvb|rtf|swf|sxc|sxw|tar|tgz|txt|wav|wma|wmv|xls|xlsx|zip))$/i;
					if (motif.test(href)) {
						event.preventDefault();
						if (confirm(this.message+'\n'+href.substr(this.urlBase.length))) {
							var
								title = href.replace(/^.*\//, ''),
								alt = title.replace(/\.tb\./, '.').replace(/\.w+$/, ''),
								html;
								if (/^.*\/([^\/]+\.(?:jpg|jpeg|png|gif))$/i.test(href)) {
									html = '<img src="'+href+'" title="'+title+'" alt="'+alt+'" />';
								} else if (/^.*\/([^\/]+\.(?:mp3|ogg|wav))$/i.test(href)) {
									var format = href.replace(/^.*\/\./, '');
									html = '<audio src="'+href+'" controls autoplay>'+TINYEDITOR.i18n('missingFormat').replace(/#1#/, format)+'</audio>';
								} else {
									html = 'a href="'+href+'" target="_blank" title="'+title+'">'+alt+'</a>';
								}
							window.opener.TINYEDITOR.insertMedia(html);
							window.close();
						}
					}
				}
			});
		} else {
			console.log(selector+' element not found');
		}
	}
}
