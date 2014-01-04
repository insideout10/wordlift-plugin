(function() {
  var $, container, injector,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module('wordlift.tinymce.plugin.config', []);

  angular.module('wordlift.tinymce.plugin.services', ['wordlift.tinymce.plugin.config']).service('EditorService', [
    'AnnotationService', function(AnnotationService) {
      return {
        ping: function(message) {
          return console.log(message);
        },
        analyze: function(content) {
          return AnnotationService.analyze(content);
        }
      };
    }
  ]).service('AnnotationService', [
    function() {
      return {
        analyze: function(content) {
          console.log("ajaxurl: " + ajaxurl);
          return $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: content,
            success: function(data) {
              var r, textAnnotations;
              console.log(data);
              r = $.parseJSON(data);
              textAnnotations = r['@graph'].filter(function(item) {
                return __indexOf.call(item['@type'], 'enhancer:TextAnnotation') >= 0 && (item['enhancer:selection-prefix'] != null);
              });
              return console.log(textAnnotations);
            }
          });
        }
      };
    }
  ]);

  angular.module('wordlift.tinymce.plugin.controllers', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services']).controller('HelloController', [
    'EditorService', '$scope', function(EditorService, $scope) {
      return $scope.hello = 'Ciao Marcello!';
    }
  ]);

  $ = jQuery;

  angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers']);

  $(container = $('<div id="wordlift-tinymce-plugin" ng-controller="HelloController">{{hello}}</div>').appendTo('body').width(1000).height(1000), injector = angular.bootstrap(container, ['wordlift.tinymce.plugin']), tinymce.PluginManager.add('wordlift', function(editor, url) {
    return editor.addButton('wordlift', {
      text: 'WordLift',
      icon: false,
      onclick: function() {
        alert(tinyMCE.activeEditor.getContent({
          format: 'text'
        }));
        return injector.invoke([
          'EditorService', function(EditorService) {
            return EditorService.analyze(tinyMCE.activeEditor.getContent({
              format: 'text'
            }));
          }
        ]);
      }
    });
  }));

}).call(this);

/*
//@ sourceMappingURL=wordlift-tinymce-plugin.js.map
*/