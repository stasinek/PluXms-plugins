var	editors = [],

	mceGetContent = function(mark) {
		// First, clear any selection
		var cursor = editors[0].getCursor();
		editors[0].setCursor(cursor.line, cursor.ch);
		editors[0].replaceSelection(mark);
		return (editors[0].getValue());
	};

const REQUIRE_CONFIG = JSON.parse(document.body.querySelector('script[data-require-config]').getAttribute('data-require-config'));

requirejs.config(REQUIRE_CONFIG);

requirejs([
		'lib/codemirror',
		'addon/comment/continuecomment',
		'addon/comment/comment',
		'addon/display/fullscreen',
		'addon/edit/closebrackets',
		'addon/edit/matchbrackets',
		'addon/display/panel',
		'addon/edit/closetag',
		'addon/edit/matchtags',
		'addon/edit/trailingspace',
		'addon/fold/brace-fold',
		'addon/fold/comment-fold',
		'addon/fold/foldgutter',
		'addon/fold/foldcode',
		'addon/fold/indent-fold',
		'addon/fold/markdown-fold',
		'addon/lint/lint',
		'addon/lint/javascript-lint',
		'addon/lint/json-lint',
		'addon/lint/css-lint',
		'addon/hint/show-hint',
		'addon/hint/xml-hint',
		'addon/hint/html-hint',
		'addon/hint/css-hint',
		'addon/hint/javascript-hint',
		'addon/search/search',
		'addon/search/jump-to-line',
		'mode/php/php',
		'keymap/vim',
		'keymap/sublime',
		'keymap/emacs'
	],
	function(CodeMirror) {

		'use strict';

		const CODEMIRROR_OPTIONS = JSON.parse(document.body.querySelector('script[data-cm-options]').getAttribute('data-cm-options'));

		function fullscreen(cm) {
			if(window.frameElement == null) {
				cm.setOption("fullScreen", !cm.getOption("fullScreen"));
			} else {
				if(window.mceFullscreen) {
					window.mceFullscreen();
				}
			}
		}

		function helpMe(editor) {
			const panel = document.body.querySelector('.cm-help-content');
			if(panel != null) {
				panel.classList.toggle('active');
			} else {
				console.log('Element with .cm-help-content not found');
			}
		}

		function saveDocument(editor) {
			storeHeights(null);
			editor.getTextArea().form.submit();
		}

		CodeMirror.defaults.matchBrackets = true;
		CodeMirror.defaults.autoCloseBrackets = true;
		CodeMirror.defaults.autoCloseTags = true;
		CodeMirror.defaults.indentUnit = 4;
		CodeMirror.defaults.lineWrapping = true;
		CodeMirror.defaults.foldGutter = true;
		CodeMirror.defaults.gutters = [
			'CodeMirror-linenumbers',
			'CodeMirror-foldgutter',
			'CodeMirror-lint-markers'
		];

		CodeMirror.defaults.allowDropFileTypes = [
			'text/plain',
			'text/javascript',
			'application/json',
			'text/css'
		];

		CodeMirror.defaults.extraKeys = {
			'Alt-Z'	: function(cm) { cm.foldCode(cm.getCursor()); },
			Esc		: function(cm) { if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false); },
			F11		: 'fullscreen',
			'Ctrl-F1': 'helpMe',
			'Ctrl-Space': 'autocomplete',
		};

		for(var option in CODEMIRROR_OPTIONS) {
			if(option != 'emmet') {
				CodeMirror.defaults[option] = CODEMIRROR_OPTIONS[option];
			}
		}

		CodeMirror.commands.fullscreen = fullscreen;
		CodeMirror.commands.helpMe = helpMe;
		CodeMirror.commands.save = saveDocument;

		if('emmet' in CODEMIRROR_OPTIONS) {
			console.log('Emmet required');
		}

		/* ************* Events callbacks ***************** */
		function storeHeights(event) {
			const statusBars = document.body.querySelectorAll('div.statusbar');
			const statusBarsCount = statusBars.length;
			if(statusBarsCount > 0) {
				for(var i=0; i<statusBarsCount; i++) {
					if(typeof statusBars[i].editor != undefined) {
						const editor = statusBars[i].editor;
						const textArea = editor.getTextArea();
						if(textArea != null) {
							const key = getKey(textArea);
							const height = editor.getWrapperElement().style.height;
							if(height.length > 0) {
								sessionStorage.setItem(key, height);
							} else {
								sessionStorage.removeItem(key);
							}
							const cursor = editor.getCursor();
							sessionStorage.setItem(key+'-line', cursor.line);
							sessionStorage.setItem(key+'-ch', cursor.ch);
						}
						if(editor.getOption('fullScreen') === true) {
							sessionStorage.setItem('CM-fullScreen', textArea.name);
						} else {
							sessionStorage.removeItem('CM-fullScreen');
						}
					}
				}
			}
		}

		function getKey(textarea) {
			return 'CM-' + window.location.pathname.replace(/^.*\/(\w+)\.php$/, '$1') + '-' + textarea.id;
		}

		function helpDisplay(event) {
			event.preventDefault();
			const panel = document.querySelector('.cm-help-content');
			if(panel != null) {
				panel.classList.toggle('active');
			}
		}

		function statusbarClick(event) {
			if(event.target.tagName == 'SPAN') {
				if(event.target.hasAttribute('data-command')) {
					event.preventDefault();
					this.editor.execCommand(event.target.getAttribute('data-command'));
				}
			}
		}

		function createStatusbar(editor) {
			const statusbar = document.createElement('DIV');
			// statusbar.id = 'codemirror-' + elmt.id;
			statusbar.classList.add('statusbar');
			var innerHTML =
				'<div>' +
					'<span>Codemirror plugin</span>' +
					'<span data-command="helpMe">Help: Ctrl-F1</span>' +
					'<span data-command="save">Save: Ctrl-S</span>' +
					'<span data-command="fullscreen">Fullscreen: F11</span>';
			if('keyMap' in CODEMIRROR_OPTIONS) {
				innerHTML += '<span>' + CODEMIRROR_OPTIONS.keyMap + '</span>'
			}
			innerHTML +=
					'<span>Codemirror - version ' + CodeMirror.version + '</span>' +
				'</div>' +
				'<div class="spacer"></div>' +
					'<div><span class="posCursor">&nbsp;</span></div>';
			statusbar.innerHTML = innerHTML;

			statusbar.addEventListener('click', statusbarClick);

			statusbar.editor = editor;

			return statusbar;
		}

		const helpPanel = document.body.querySelector('.cm-help-content');
		if(helpPanel != null) {
			helpPanel.addEventListener('click', function(event) {
				event.preventDefault();
				this.classList.toggle('active');
			});
		}

		if(top.tinymce != null) {
			// Intégration dans Tinymce
			var ed = CodeMirror(document.body);
			ed.setOption('fullScreen', true);

			var mceActiveEditor = top.tinymce.activeEditor;
			var content = mceActiveEditor.getContent();
			// supprime la marque du cursor dans Tinymce
			mceActiveEditor.execCommand('Undo');
			ed.setValue(content);
			// remove the mark and place the cursor in Codemirror
			var mceParams = mceActiveEditor.windowManager.getParams();
			var cursor = ed.getSearchCursor(mceParams.mark);
			if(cursor.find()) {
				cursor.replace('');
				ed.setCursor(cursor.from());
			}
			ed.focus();

			editors.push(ed);
		} else {
			const TEXTAREAS = 'content chapo backend frontend sandbox'.split(' ').map(function(item) {
				return item.replace(/^(\w+)$/, 'textarea[name="$1"]')
			});
			const contents = document.body.querySelectorAll(TEXTAREAS);
			const contentsCount = contents.length;
			if(contentsCount > 0) {
				for(var i=0; i<contentsCount; i++) {
					const elmt = contents[i];
					var ed = CodeMirror.fromTextArea(elmt);
					const statusbar = createStatusbar(ed);
					ed.addPanel(
						statusbar,
						{ position: 'bottom'}
					);
					editors.push(ed);
					ed.on(
						'cursorActivity',
						function(editor) {
							if(editor.hasFocus()) {
								const cursor = editor.getCursor();
								const container = editor.getWrapperElement();
								const panel = container.parentElement.querySelector('.statusbar .posCursor');
								panel.innerHTML = (cursor.line + 1) + ':' + (cursor.ch + 1);
							}
							if('emmet' in CODEMIRROR_OPTIONS) {
								ed.on('cursorActivity', emmetCursorActivity);
							}
						}
					);

					const key = getKey(elmt);
					const height = sessionStorage.getItem(key);
					if(height != null) {
						ed.getWrapperElement().style.height = parseInt(height);
					}
					const line = sessionStorage.getItem(key + '-line');
					const ch = sessionStorage.getItem(key + '-ch');
					if(line != null && ch != null) {
						ed.setCursor(parseInt(line), parseInt(ch));
					}
					const fullWrapper = sessionStorage.getItem('CM-fullScreen');
					if(fullWrapper != null && fullWrapper == elmt.name) {
						ed.setOption('fullScreen', true);
					}
					if(elmt.form != null) {
						elmt.form.addEventListener('submit', storeHeights);
					}
				}
				document.body.classList.add('codemirror-plugin');
			}

			// gestion des onglets
			if(document.body.id == 'parametres_plugincss') {
				const PATTERN = 'fieldset div.grid > div > label';
				var ACTIVE = 'active';

				const tabs = document.body.querySelectorAll(PATTERN);
				const tabsCount = tabs.length;
				if(tabsCount > 0) {

					function activeTab(event) {
						event.preventDefault();
						const actives = document.querySelectorAll('fieldset div.grid > div > label.active');
						const activesCount = actives.length;
						if(activesCount > 0) {
							for(var i=0; i<activesCount; i++) {
								actives[i].classList.remove(ACTIVE);
							}
						}
						event.target.classList.add(ACTIVE);
					}

					for(var i=0; i<tabsCount; i++) {
						tabs[i].addEventListener('click', activeTab);
						if(i == 0) {
							tabs[0].classList.add(ACTIVE);
						}
					}
				}
			}
		}
	}
);
