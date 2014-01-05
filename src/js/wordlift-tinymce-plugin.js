(function() {
  var $, container, injector,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module('wordlift.tinymce.plugin.config', []);

  angular.module('wordlift.tinymce.plugin.services', ['wordlift.tinymce.plugin.config']).service('EditorService', [
    'AnnotationService', '$rootScope', function(AnnotationService, $rootScope) {
      $rootScope.$on('AnnotationService.annotations', function(event, annotations) {
        var currentHtmlContent, isDirty, regexp, replace, selectionHead, selectionTail, textAnnotation, _i, _len;
        console.log('I received some annotations');
        currentHtmlContent = tinyMCE.get('content').getContent({
          format: 'raw'
        });
        for (_i = 0, _len = annotations.length; _i < _len; _i++) {
          textAnnotation = annotations[_i];
          console.log(textAnnotation);
          selectionHead = textAnnotation['enhancer:selection-prefix']['@value'].replace('\(', '\\(').replace('\)', '\\)');
          selectionTail = textAnnotation['enhancer:selection-suffix']['@value'].replace('\(', '\\(').replace('\)', '\\)');
          regexp = new RegExp("(\\W|^)(" + textAnnotation['enhancer:selected-text']['@value'] + ")(\\W|$)(?![^<]*\">?)");
          console.log(regexp);
          replace = "$1<strong id=\"" + textAnnotation['@id'] + "\" class=\"textannotation\"          typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</strong>$3";
          currentHtmlContent = currentHtmlContent.replace(regexp, replace);
          isDirty = tinyMCE.get("content").isDirty();
          tinyMCE.get("content").setContent(currentHtmlContent);
          if (!isDirty) {
            tinyMCE.get("content").isNotDirty = 1;
          }
        }
        return tinyMCE.get("content").onClick.add(function(editor, e) {
          return $rootScope.$apply(console.log("Click within the editor on element with id " + e.target.id), $rootScope.$broadcast('EditorService.annotationClick', e.target.id));
        });
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
    '$rootScope', '$http', function($rootScope, $http) {
      var currentAnalysis, findEntitiesForAnnotation, notifyAnnotations;
      $rootScope.$on('EditorService.annotationClick', function(event, id) {
        console.log("Ops!! Element with id " + id + " was clicked!");
        return findEntitiesForAnnotation(id);
      });
      currentAnalysis = {};
      notifyAnnotations = function() {
        var textAnnotations;
        textAnnotations = currentAnalysis['@graph'].filter(function(item) {
          return __indexOf.call(item['@type'], 'enhancer:TextAnnotation') >= 0 && (item['enhancer:selection-prefix'] != null);
        });
        return $rootScope.$broadcast('AnnotationService.annotations', textAnnotations);
      };
      findEntitiesForAnnotation = function(annotationId) {
        var entityAnnotations;
        console.log("Going to find entities for annotation with ID " + annotationId);
        entityAnnotations = currentAnalysis['@graph'].filter(function(item) {
          return __indexOf.call(item['@type'], 'enhancer:EntityAnnotation') >= 0 && item['dc:relation'] === annotationId;
        });
        return $rootScope.$broadcast('AnnotationService.entityAnnotations', entityAnnotations);
      };
      return {
        analyze: function(content) {
          $http({
            method: 'POST',
            url: ajaxurl,
            params: {
              action: 'wordlift_analyze'
            },
            data: content
          }).success(function(data, status, headers, config) {
            currentAnalysis = data;
            return notifyAnnotations();
          });
          return true;
        }
      };
    }
  ]);

  angular.module('wordlift.tinymce.plugin.controllers', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services']).controller('HelloController', [
    'AnnotationService', '$scope', function(AnnotationService, $scope) {
      $scope.hello = 'Ciao Marcello!';
      $scope.annotations = [];
      return $scope.$on('AnnotationService.entityAnnotations', function(event, annotations) {
        console.log('I received entity annotations too');
        console.log(annotations);
        return $scope.annotations = annotations;
      });
    }
  ]);

  $ = jQuery;

  angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers']);

  $(container = $('<div id="wordlift-tinymce-plugin" ng-controller="HelloController">{{hello}}\n  <h2>Debug</h2>\n  <ul>\n    <li ng-repeat="annotation in annotations">\n      <div>annotation</div>\n      <div ng-bind="annotation[\'enhancer:entity-reference\']"></div>\n    </li>\n  </ul>\n</div>').appendTo('body').width(1000).height(1000), injector = angular.bootstrap(container, ['wordlift.tinymce.plugin']), tinymce.PluginManager.add('wordlift', function(editor, url) {
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