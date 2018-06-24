		/* ************ Begin of Emmet functions ************** */
		function emmetHint(mode, editor) {
			// Tell `show-hint` module that current helper will provide completions
			return !!editor.getEmmetAbbreviation();
		}

		function emmetHelper(editor, options) {
			// Activate auto-popup, if disabled (see below)
			const marker = editor.findEmmetMarker();
			if (!marker) {
				return;
			}

			clearTimeout(marker.popupDisableTimer);
			marker.autoPopupDisabled = false;

			const completions = editor.getEmmetCompletions();
			return completions && {
				from: completions.from,
				to: completions.to,
				// Transform Emmet completions to ones that supported by `show-hint`
				list: completions.list.map(function(completion) {
					return {
						from: completion.range.from,
						to: completion.range.to,
						render: function(elt) {
							var content = document.createDocumentFragment();
							var label = document.createElement('span');
							label.className = 'emmet-label';

							var preview = document.createElement('span');
							preview.className = 'emmet-preview';

							content.appendChild(label);
							content.appendChild(preview);

							if (completion.type === 'expanded-abbreviation') {
								// It’s an expanded abbreviation completion:
								// render preview for it
								label.className += ' emmet-label__expand';
								label.textContent = 'Expand abbreviation';

								preview.className += ' emmet-preview__expand';
								// Replace tab with a few spaces so preview would take
								// lesser space
								preview.textContent = completion.preview.replace(/\t/g, '  ');
							} else {
								// A regular snippet: render completion abbreviation
								// and its preview
								label.textContent = completion.label;
								preview.textContent = completion.preview;
							}

							elt.appendChild(content);
						},
						hint: function() {
							// Use completions’ `insert()` method to properly
							// insert Emmet completion
							completion.insert();
						}
					};
				})
			};
		}

		// Automatically display Emmet completions when cursor enters abbreviation
		// marker if `markEmmetAbbreviation` option was enabled (true by default)
		function emmetCursorActivity(editor) {
			if (editor.getOption('markEmmetAbbreviation')) {
				const marker = editor.findEmmetMarker();
				if (marker && !marker.autoPopupDisabled) {
					editor.showHint({ completeSingle: false });
				}
			}
		}

		// Automatic popup with expanded Emmet abbreviation might be very annoying
		// since almost any latin word can be Emmet abbreviation.
		// So when user hides completion popup with Escape key, we should mark
		// Emmet abbreviation marker under cursor as one that shouldn’t receive
		// automatic completion popup.
		// Since CodeMirror API does not allow us (easily) to detect if completion
		// popup was hidden because of user interaction (Esc key) or because it
		// must recalculate completions on user typing, we will use a timer hack
		function emmetStartCompletion(editor) {
			var marker = editor.findEmmetMarker();
			if (marker) {
				clearTimeout(marker.popupDisableTimer);
				marker.popupDisableTimer = null;
			}
		}

		function emmetEndCompletion(editor) {
			var marker = editor.findEmmetMarker();
			if (marker) {
				clearTimeout(marker.popupDisableTimer);
				marker.popupDisableTimer = setTimeout(function() {
					marker.autoPopupDisabled = true;
				}, 30);
			}
		}
		/* ************ End of Emmet functions ************** */

		if('emmet' in CODEMIRROR_OPTIONS) {

			// Add completions provider for CodeMirror’s `show-hint` addon
			CodeMirror.registerGlobalHelper(
				'hint', 'emmet', emmetHint, emmetHelper
			);

			CodeMirror.defaults['emmet'] = {
				markupSnippets : { foo : 'div.foo[bar=baz]' },
				stylesheetSnippets : { myp: 'my-super: property' }
			};
			const extraKeys = {
				'Ctrl-Space' : 'autocomplete',
				Tab : 'emmetExpandAbbreviation',
				Enter : 'emmetInsertLineBreak'
			}
			for(var k in extraKeys) {
				CodeMirror.defaults.extraKeys[k] = extraKeys[k];
			}
		}


				/* Emmet */
				ed.on('startCompletion', emmetStartCompletion);
				ed.on('endCompletion', emmetEndCompletion);


<?php
		if(!empty($this->getParam('emmet'))) {
?>
	<!-- script type="text/javascript" src="<?php echo $this->pluginRoot; ?>emmet/emmet-codemirror-plugin.js"></script -->
<?php
		}
?>