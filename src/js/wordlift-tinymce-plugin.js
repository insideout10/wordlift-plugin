(function() {
  tinymce.PluginManager.add('wordlift', function(editor, url) {
    return editor.addButton('wordlift', {
      text: 'WordLift',
      icon: false,
      onclick: function() {
        var content;
        content = tinyMCE.activeEditor.getContent({
          format: 'raw'
        });
        return alert(content);
      }
    });
  });

}).call(this);
