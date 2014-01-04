(function() {
  var $, container, injector,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module('wordlift.tinymce.plugin.config', []);

  angular.module('wordlift.tinymce.plugin.services', ['wordlift.tinymce.plugin.config']).service('EditorService', [
    'AnnotationService', '$rootScope', function(AnnotationService, $rootScope) {
      $rootScope.$on('AnnotationService.annotations', function(event, annotations) {
        var currentHtmlContent, isDirty, regexp, replace, selectionHead, selectionTail, textAnnotation, _i, _len, _results;
        console.log('I received some annotations');
        currentHtmlContent = tinyMCE.get('content').getContent({
          format: 'raw'
        });
        _results = [];
        for (_i = 0, _len = annotations.length; _i < _len; _i++) {
          textAnnotation = annotations[_i];
          console.log(textAnnotation);
          selectionHead = textAnnotation['enhancer:selection-prefix']['@value'].replace('\(', '\\(').replace('\)', '\\)');
          selectionTail = textAnnotation['enhancer:selection-suffix']['@value'].replace('\(', '\\(').replace('\)', '\\)');
          regexp = new RegExp("(\\W)(" + textAnnotation['enhancer:selected-text']['@value'] + ")(\\W)(?![^>]*\")");
          replace = "$1<strong id=\"" + textAnnotation['@id'] + "\" class=\"textannotation\"          typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</strong>$3";
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
      });
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
    '$rootScope', function($rootScope) {
      return {
        analyze: function(content) {
          console.log("ajaxurl: " + ajaxurl);
          return $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
              action: 'wordlift_analyze',
              body: content
            },
            success: function(data) {
              var r, textAnnotations;
              console.log(data);
              r = $.parseJSON(data);
              textAnnotations = r['@graph'].filter(function(item) {
                return __indexOf.call(item['@type'], 'enhancer:TextAnnotation') >= 0 && (item['enhancer:selection-prefix'] != null);
              });
              console.log(textAnnotations);
              return $rootScope.$apply($rootScope.$broadcast('AnnotationService.annotations', textAnnotations));
            }
          });
        }
      };
    }
  ]);

  angular.module('wordlift.tinymce.plugin.controllers', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services']).controller('HelloController', [
    'AnnotationService', '$scope', function(AnnotationService, $scope) {
      $scope.hello = 'Ciao Marcello!';
      $scope.annotations = [];
      return $scope.$on('AnnotationService.annotations', function(event, annotations) {
        console.log('I received some annotations too');
        $scope.annotations = annotations;
        return console.log($scope.annotations);
      });
    }
  ]);

  $ = jQuery;

  angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers']);

  $(container = $('<div id="wordlift-tinymce-plugin" ng-controller="HelloController">{{hello}}\n  <ul>\n    <li ng-repeat="annotation in annotations">\n      <div>annotation</div>\n      <div ng-bind="annotation[\'@id\']"></div>\n    </li>\n  </ul>\n</div>').appendTo('body').width(1000).height(1000), injector = angular.bootstrap(container, ['wordlift.tinymce.plugin']), tinymce.PluginManager.add('wordlift', function(editor, url) {
    return editor.addButton('wordlift', {
      text: 'WordLift',
      icon: false,
      onclick: function() {
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