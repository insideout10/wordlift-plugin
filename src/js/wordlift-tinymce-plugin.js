(function() {
  tinymce.PluginManager.add('wordlift', function(editor, url) {
    return editor.addButton('wordlift', {
      text: 'WordLift',
      icon: false,
      onclick: function() {
        return alert('Hello!');
      }
    });
  });

}).call(this);
