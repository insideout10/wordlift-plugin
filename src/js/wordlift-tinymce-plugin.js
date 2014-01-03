(function() {
  tinymce.PluginManager.add('wordlift', function(editor, url) {
    return editor.addButton('wordlift', {
      text: 'WordLift',
      icon: false,
      onclick: function() {
        var content, data;
        content = tinyMCE.activeEditor.getContent({
          format: 'text'
        });
        data = {
          action: 'wordlift_analyze',
          body: content
        };
        return jQuery.post(ajaxurl, data, function(response) {
          return alert('Risposta del server ' + response);
        });
      }
    });
  });

}).call(this);
