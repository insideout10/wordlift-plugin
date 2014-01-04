(function() {
  var $,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  $ = jQuery;

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
        return $.ajax({
          type: "POST",
          url: ajaxurl,
          data: data,
          success: function(data) {
            var currentHtmlContent, isDirty, r, regexp, replace, selectionHead, selectionTail, textAnnotation, textAnnotations, _i, _len, _results;
            r = $.parseJSON(data);
            textAnnotations = r['@graph'].filter(function(item) {
              return __indexOf.call(item['@type'], 'enhancer:TextAnnotation') >= 0 && (item['enhancer:selection-prefix'] != null);
            });
            currentHtmlContent = tinyMCE.get('content').getContent({
              format: 'raw'
            });
            _results = [];
            for (_i = 0, _len = textAnnotations.length; _i < _len; _i++) {
              textAnnotation = textAnnotations[_i];
              console.log(textAnnotation);
              selectionHead = textAnnotation['enhancer:selection-prefix']['@value'].replace('\(', '\\(').replace('\)', '\\)');
              selectionTail = textAnnotation['enhancer:selection-suffix']['@value'].replace('\(', '\\(').replace('\)', '\\)');
              regexp = new RegExp("(\\W)(" + textAnnotation['enhancer:selected-text']['@value'] + ")(\\W)(?![^>]*\")");
              replace = "$1<strong id=\"" + textAnnotation['@id'] + "\" class=\"textannotation\"							typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</strong>$3";
              currentHtmlContent = currentHtmlContent.replace(regexp, replace);
              isDirty = tinyMCE.get("content").isDirty();
              tinyMCE.get("content").setContent(currentHtmlContent);
              if (!isDirty) {
                _results.push(tinyMCE.get("content").isNotDirty = 1);
              } else {
                _results.push(void 0);
              }
            }
            return _results;
          }
        });
      }
    });
  });

}).call(this);

/*
//@ sourceMappingURL=wordlift-tinymce-plugin.js.map
*/