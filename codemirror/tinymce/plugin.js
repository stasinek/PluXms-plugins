tinymce.PluginManager.add('codemirror', function(editor, url) {
  // Add a button that opens a window
  editor.addButton('codemirror', {
    text: 'Codemirror',
    icon: false,
    onclick: function() {
      // Open window
      editor.windowManager.open({
        title: 'Codemirror',
        body: [
          {type: 'textbox', name: 'title', label: 'Title'}
        ],
        onsubmit: function(e) {
          // Insert content when the window form is submitted
          editor.insertContent('Title: ' + e.data.title);
        }
      });
    }
  });

  // Adds a menu item to the tools menu
  editor.addMenuItem('codemirror', {
    text: 'Codemirror',
    context: 'tools',
    onclick: function() {
      const MARK = 'TheCaret';
      var fullscreenStatus = false;
      editor.focus();
      editor.insertContent(MARK);
      // Open window with a specific url
      const win = editor.windowManager.open({
        title: 'Codemirror',
        url: url + '/index.php?lang=' + editor.settings.language,
        width: 800,
        height: 600,
        buttons: [
			{
				text: 'Fullscreen',
 				onclick: function(param) {
					fullscreenStatus = ! fullscreenStatus;
					win.fullscreen(fullscreenStatus);
				}
			},
			{
				text: 'Cancel',
				onclick: 'close'
			},
			{
				text: 'Save',
				onclick: function(param) {
					var iframe = param.currentTarget.querySelector('iframe');
					var content = iframe.contentWindow.mceGetContent('<span class="cm-caret">|</span>');
					editor.setContent(content);
					win.close();
					var matches = editor.dom.select('span.cm-caret:first-of-type'); // règle CSS !
					editor.selection.scrollIntoView(matches[0]);
					editor.selection.setCursorLocation(matches[0], 0);
					editor.dom.remove(matches[0]);
				}
			}
        ]
      },
      {
		mark: MARK, // marque l'emplacement du curseur
		url: url
	  });
	  win.toggleFullscreen = function() {
		fullscreenStatus = ! fullscreenStatus;
		this.fullscreen(fullscreenStatus);
	  }
    }
  });
});