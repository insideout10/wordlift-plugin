(function() {
  var $, Traslator, container, injector,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  Traslator = (function() {
    var decodeHtml;

    Traslator.prototype._htmlPositions = [];

    Traslator.prototype._textPositions = [];

    Traslator.prototype._html = '';

    Traslator.prototype._text = '';

    decodeHtml = function(html) {
      var txt;
      txt = document.createElement("textarea");
      txt.innerHTML = html;
      return txt.value;
    };

    Traslator.create = function(html) {
      var traslator;
      traslator = new Traslator(html);
      traslator.parse();
      return traslator;
    };

    function Traslator(html) {
      this._html = html;
    }

    Traslator.prototype.parse = function() {
      var htmlElem, htmlLength, htmlPost, htmlPre, htmlProcessed, match, pattern, textLength, textPost, textPre;
      this._htmlPositions = [];
      this._textPositions = [];
      this._text = '';
      pattern = /([^&#<>]*)(&[^&;]*;|<[^>]*>)([^&#<>]*)/gim;
      textLength = 0;
      htmlLength = 0;
      while (match = pattern.exec(this._html)) {
        htmlPre = match[1];
        htmlElem = match[2];
        htmlPost = match[3];
        textPre = htmlPre + ('</p>' === htmlElem.toLowerCase() ? '\n\n' : '');
        textPost = htmlPost;
        textLength += textPre.length;
        if (/^&[^&;]*;$/gim.test(htmlElem)) {
          textLength += 1;
        }
        htmlLength += htmlPre.length + htmlElem.length;
        this._htmlPositions.push(htmlLength);
        this._textPositions.push(textLength);
        textLength += textPost.length;
        htmlLength += htmlPost.length;
        htmlProcessed = '';
        if (/^&[^&;]*;$/gim.test(htmlElem)) {
          htmlProcessed = decodeHtml(htmlElem);
        }
        this._text += textPre + htmlProcessed + textPost;
      }
      if ('' === this._text && '' !== this._html) {
        this._text = new String(this._html);
      }
      if (0 === this._textPositions.length || 0 !== this._textPositions[0]) {
        this._htmlPositions.unshift(0);
        return this._textPositions.unshift(0);
      }
    };

    Traslator.prototype.text2html = function(pos) {
      var htmlPos, i, textPos, _i, _ref;
      htmlPos = 0;
      textPos = 0;
      for (i = _i = 0, _ref = this._textPositions.length; 0 <= _ref ? _i < _ref : _i > _ref; i = 0 <= _ref ? ++_i : --_i) {
        if (pos < this._textPositions[i]) {
          break;
        }
        htmlPos = this._htmlPositions[i];
        textPos = this._textPositions[i];
      }
      return htmlPos + pos - textPos;
    };

    Traslator.prototype.html2text = function(pos) {
      var htmlPos, i, textPos, _i, _ref;
      if (pos < this._htmlPositions[0]) {
        return 0;
      }
      htmlPos = 0;
      textPos = 0;
      for (i = _i = 0, _ref = this._htmlPositions.length; 0 <= _ref ? _i < _ref : _i > _ref; i = 0 <= _ref ? ++_i : --_i) {
        if (pos < this._htmlPositions[i]) {
          break;
        }
        htmlPos = this._htmlPositions[i];
        textPos = this._textPositions[i];
      }
      return textPos + pos - htmlPos;
    };

    Traslator.prototype.insertHtml = function(fragment, pos) {
      var htmlPos;
      htmlPos = this.text2html(pos.text);
      this._html = this._html.substring(0, htmlPos) + fragment + this._html.substring(htmlPos);
      return this.parse();
    };

    Traslator.prototype.getHtml = function() {
      return this._html;
    };

    Traslator.prototype.getText = function() {
      return this._text;
    };

    return Traslator;

  })();

  window.Traslator = Traslator;

  angular.module('wordlift.utils.directives', []).directive('wlSrc', [
    '$window', '$log', function($window, $log) {
      return {
        restrict: 'A',
        priority: 99,
        link: function($scope, $element, $attrs, $ctrl) {
          return $element.bind('error', function() {
            if ($attrs.src !== $attrs.wlSrc) {
              $log.warn("Error on " + $attrs.src + "! Going to fallback on " + $attrs.wlSrc);
              return $attrs.$set('src', $attrs.wlSrc);
            }
          });
        }
      };
    }
  ]);

  angular.module('wordlift.ui.carousel', []).directive('wlCarousel', [
    '$window', '$log', function($window, $log) {
      return {
        restrict: 'A',
        scope: true,
        transclude: true,
        template: "<div class=\"wl-carousel\" ng-show=\"panes.length > 0\">\n  <div class=\"wl-panes\" ng-style=\"{ width: panesWidth, left: position }\" ng-transclude ng-swipe-right=\"next()\"></div>\n  <div class=\"wl-carousel-arrow wl-prev\" ng-click=\"prev()\" ng-show=\"currentPaneIndex > 0\">\n    <i class=\"wl-angle-left\" />\n  </div>\n  <div class=\"wl-carousel-arrow wl-next\" ng-click=\"next()\" ng-show=\"isNextArrowVisible()\">\n    <i class=\"wl-angle-right\" />\n  </div>\n</div>",
        controller: function($scope, $element, $attrs) {
          var ctrl, w;
          w = angular.element($window);
          $scope.visibleElements = function() {
            if ($element.width() > 460) {
              return 3;
            }
            if ($element.width() > 1024) {
              return 5;
            }
            return 1;
          };
          $scope.setItemWidth = function() {
            return $element.width() / $scope.visibleElements();
          };
          $scope.itemWidth = $scope.setItemWidth();
          $scope.panesWidth = void 0;
          $scope.panes = [];
          $scope.position = 0;
          $scope.currentPaneIndex = 0;
          $scope.isNextArrowVisible = function() {
            return ($scope.panes.length - $scope.currentPaneIndex) > $scope.visibleElements();
          };
          $scope.next = function() {
            $scope.position = $scope.position - $scope.itemWidth;
            return $scope.currentPaneIndex = $scope.currentPaneIndex + 1;
          };
          $scope.prev = function() {
            $scope.position = $scope.position + $scope.itemWidth;
            return $scope.currentPaneIndex = $scope.currentPaneIndex - 1;
          };
          $scope.setPanesWrapperWidth = function() {
            $scope.panesWidth = $scope.panes.length * $scope.itemWidth;
            $scope.position = 0;
            return $scope.currentPaneIndex = 0;
          };
          w.bind('resize', function() {
            var pane, _i, _len, _ref;
            $scope.itemWidth = $scope.setItemWidth();
            $scope.setPanesWrapperWidth();
            _ref = $scope.panes;
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              pane = _ref[_i];
              pane.scope.setWidth($scope.itemWidth);
            }
            return $scope.$apply();
          });
          ctrl = this;
          ctrl.registerPane = function(scope, element) {
            var pane;
            scope.setWidth($scope.itemWidth);
            pane = {
              'scope': scope,
              'element': element
            };
            $scope.panes.push(pane);
            return $scope.setPanesWrapperWidth();
          };
          return ctrl.unregisterPane = function(scope) {
            var index, pane, unregisterPaneIndex, _i, _len, _ref;
            unregisterPaneIndex = void 0;
            _ref = $scope.panes;
            for (index = _i = 0, _len = _ref.length; _i < _len; index = ++_i) {
              pane = _ref[index];
              if (pane.scope.$id === scope.$id) {
                unregisterPaneIndex = index;
              }
            }
            $scope.panes.splice(unregisterPaneIndex, 1);
            return $scope.setPanesWrapperWidth();
          };
        }
      };
    }
  ]).directive('wlCarouselPane', [
    '$log', function($log) {
      return {
        require: '^wlCarousel',
        restrict: 'EA',
        transclude: true,
        template: "<div ng-transclude></div>",
        link: function($scope, $element, $attrs, $ctrl) {
          $log.debug("Going to add carousel pane with id " + $scope.$id + " to carousel");
          $element.addClass("wl-carousel-item");
          $scope.setWidth = function(size) {
            return $element.css('width', "" + size + "px");
          };
          $scope.$on('$destroy', function() {
            $log.debug("Destroy " + $scope.$id);
            return $ctrl.unregisterPane($scope);
          });
          return $ctrl.registerPane($scope, $element);
        }
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.controllers.EditPostWidgetController', ['wordlift.editpost.widget.services.AnalysisService', 'wordlift.editpost.widget.services.EditorService', 'wordlift.editpost.widget.providers.ConfigurationProvider']).filter('filterEntitiesByTypesAndRelevance', [
    'configuration', '$log', function(configuration, $log) {
      return function(items, types) {
        var annotations_count, entity, filtered, id, treshold, _ref;
        filtered = [];
        if (items == null) {
          return filtered;
        }
        treshold = Math.floor(((1 / 120) * Object.keys(items).length) + 0.75);
        for (id in items) {
          entity = items[id];
          if (_ref = entity.mainType, __indexOf.call(types, _ref) >= 0) {
            annotations_count = Object.keys(entity.annotations).length;
            if (annotations_count === 0) {
              continue;
            }
            if (annotations_count > treshold && entity.confidence === 1) {
              filtered.push(entity);
              continue;
            }
            if (entity.occurrences.length > 0) {
              filtered.push(entity);
              continue;
            }
            if (entity.id.startsWith(configuration.datasetUri)) {
              filtered.push(entity);
            }
          }
        }
        return filtered;
      };
    }
  ]).filter('filterEntitiesByTypes', [
    '$log', function($log) {
      return function(items, types) {
        var entity, filtered, id, _ref;
        filtered = [];
        for (id in items) {
          entity = items[id];
          if (_ref = entity.mainType, __indexOf.call(types, _ref) >= 0) {
            filtered.push(entity);
          }
        }
        return filtered;
      };
    }
  ]).filter('isEntitySelected', [
    '$log', function($log) {
      return function(items) {
        var entity, filtered, id;
        filtered = [];
        for (id in items) {
          entity = items[id];
          if (entity.occurrences.length > 0) {
            filtered.push(entity);
          }
        }
        return filtered;
      };
    }
  ]).controller('EditPostWidgetController', [
    'RelatedPostDataRetrieverService', 'EditorService', 'AnalysisService', 'configuration', '$log', '$scope', '$rootScope', '$injector', function(RelatedPostDataRetrieverService, EditorService, AnalysisService, configuration, $log, $scope, $rootScope, $injector) {
      var box, _i, _len, _ref;
      $scope.isRunning = false;
      $scope.analysis = void 0;
      $scope.relatedPosts = void 0;
      $scope.newEntity = AnalysisService.createEntity();
      $scope.selectedEntities = {};
      $scope.annotation = void 0;
      $scope.boxes = [];
      $scope.images = {};
      $scope.isThereASelection = false;
      $scope.configuration = configuration;
      $rootScope.$on("analysisServiceStatusUpdated", function(event, newStatus) {
        $scope.isRunning = newStatus;
        return EditorService.updateContentEditableStatus(!status);
      });
      $rootScope.$watch('selectionStatus', function() {
        return $scope.isThereASelection = $rootScope.selectionStatus;
      });
      _ref = $scope.configuration.classificationBoxes;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        box = _ref[_i];
        $scope.selectedEntities[box.id] = {};
      }
      $scope.createTextAnnotationFromCurrentSelection = function() {
        return EditorService.createTextAnnotationFromCurrentSelection();
      };
      $scope.selectAnnotation = function(annotationId) {
        return EditorService.selectAnnotation(annotationId);
      };
      $scope.isEntitySelected = function(entity, box) {
        return $scope.selectedEntities[box.id][entity.id] != null;
      };
      $scope.isLinkedToCurrentAnnotation = function(entity) {
        var _ref1;
        return (_ref1 = $scope.annotation, __indexOf.call(entity.occurrences, _ref1) >= 0);
      };
      $scope.addNewEntityToAnalysis = function() {
        var annotation;
        if ($scope.newEntity.sameAs) {
          $scope.newEntity.sameAs = [$scope.newEntity.sameAs];
        }
        delete $scope.newEntity.suggestedSameAs;
        $log.debug($scope.newEntity);
        $scope.analysis.entities[$scope.newEntity.id] = $scope.newEntity;
        annotation = $scope.analysis.annotations[$scope.annotation];
        annotation.entityMatches.push({
          entityId: $scope.newEntity.id,
          confidence: 1
        });
        $scope.analysis.entities[$scope.newEntity.id].annotations[annotation.id] = annotation;
        $scope.analysis.annotations[$scope.annotation].entities[$scope.newEntity.id] = $scope.newEntity;
        return $scope.newEntity = AnalysisService.createEntity();
      };
      $scope.$on("updateOccurencesForEntity", function(event, entityId, occurrences) {
        var entities, _ref1, _results;
        $log.debug("Occurrences " + occurrences.length + " for " + entityId);
        $scope.analysis.entities[entityId].occurrences = occurrences;
        if (occurrences.length === 0) {
          _ref1 = $scope.selectedEntities;
          _results = [];
          for (box in _ref1) {
            entities = _ref1[box];
            _results.push(delete $scope.selectedEntities[box][entityId]);
          }
          return _results;
        }
      });
      $scope.$on("textAnnotationClicked", function(event, annotationId) {
        return $scope.annotation = annotationId;
      });
      $scope.$on("textAnnotationAdded", function(event, annotation) {
        $log.debug("added a new annotation with Id " + annotation.id);
        $scope.analysis.annotations[annotation.id] = annotation;
        $scope.annotation = annotation.id;
        $scope.newEntity.label = annotation.text;
        return AnalysisService.getSuggestedSameAs(annotation.text);
      });
      $scope.$on("sameAsRetrieved", function(event, sameAs) {
        $log.debug("Retrieved sameAs " + sameAs);
        return $scope.newEntity.suggestedSameAs = sameAs;
      });
      $scope.$on("relatedPostsLoaded", function(event, posts) {
        return $scope.relatedPosts = posts;
      });
      $scope.$on("analysisPerformed", function(event, analysis) {
        var entity, entityId, uri, _j, _k, _l, _len1, _len2, _len3, _ref1, _ref2, _ref3;
        $scope.analysis = analysis;
        _ref1 = $scope.configuration.classificationBoxes;
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          box = _ref1[_j];
          _ref2 = box.selectedEntities;
          for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
            entityId = _ref2[_k];
            if (entity = analysis.entities[entityId]) {
              if (entity.occurrences.length === 0) {
                $log.warn("Entity " + entityId + " selected as " + box.label + " without valid occurences!");
                continue;
              }
              $scope.selectedEntities[box.id][entityId] = analysis.entities[entityId];
              _ref3 = entity.images;
              for (_l = 0, _len3 = _ref3.length; _l < _len3; _l++) {
                uri = _ref3[_l];
                $scope.images[uri] = entity.label;
              }
            } else {
              $log.warn("Entity with id " + entityId + " should be linked to " + box.id + " but is missing");
            }
          }
        }
        return $scope.updateRelatedPosts();
      });
      $scope.updateRelatedPosts = function() {
        var entities, entity, entityIds, id, _ref1;
        $log.debug("Going to update related posts box ...");
        entityIds = [];
        _ref1 = $scope.selectedEntities;
        for (box in _ref1) {
          entities = _ref1[box];
          for (id in entities) {
            entity = entities[id];
            entityIds.push(id);
          }
        }
        return RelatedPostDataRetrieverService.load(entityIds);
      };
      return $scope.onSelectedEntityTile = function(entity, scope) {
        var uri, _j, _k, _len1, _len2, _ref1, _ref2;
        $log.debug("Entity tile selected for entity " + entity.id + " within '" + scope.id + "' scope");
        if ($scope.selectedEntities[scope.id][entity.id] == null) {
          $scope.selectedEntities[scope.id][entity.id] = entity;
          _ref1 = entity.images;
          for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
            uri = _ref1[_j];
            $scope.images[uri] = entity.label;
          }
          $scope.$emit("entitySelected", entity, $scope.annotation);
        } else {
          _ref2 = entity.images;
          for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
            uri = _ref2[_k];
            delete $scope.images[uri];
          }
          $scope.$emit("entityDeselected", entity, $scope.annotation);
        }
        return $scope.updateRelatedPosts();
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.directives.wlClassificationBox', []).directive('wlClassificationBox', [
    '$log', function($log) {
      return {
        restrict: 'E',
        scope: true,
        transclude: true,
        template: "<div class=\"classification-box\">\n	<div class=\"box-header\">\n          <h5 class=\"label\">{{box.label}}</h5>\n          <div class=\"wl-selected-items-wrapper\">\n            <span ng-class=\"'wl-' + entity.mainType\" ng-repeat=\"(id, entity) in selectedEntities[box.id]\" class=\"wl-selected-item\">\n              {{ entity.label}}\n              <i class=\"wl-deselect\" ng-click=\"onSelectedEntityTile(entity, box)\"></i>\n            </span>\n          </div>\n        </div>\n  			<div class=\"box-tiles\">\n          <div ng-transclude></div>\n  		  </div>\n      </div>	",
        link: function($scope, $element, $attrs, $ctrl) {
          $scope.currentWidget = void 0;
          $scope.isWidgetOpened = false;
          $scope.closeWidgets = function() {
            $scope.currentWidget = void 0;
            return $scope.isWidgetOpened = false;
          };
          $scope.hasSelectedEntities = function() {
            return Object.keys($scope.selectedEntities[$scope.box.id]).length > 0;
          };
          $scope.embedImageInEditor = function(image) {
            return $scope.$emit("embedImageInEditor", image);
          };
          return $scope.toggleWidget = function(widget) {
            if ($scope.currentWidget === widget) {
              $scope.currentWidget = void 0;
              return $scope.isWidgetOpened = false;
            } else {
              $scope.currentWidget = widget;
              $scope.isWidgetOpened = true;
              return $scope.updateWidget(widget, $scope.box.id);
            }
          };
        },
        controller: function($scope, $element, $attrs) {
          var ctrl;
          $scope.tiles = [];
          $scope.boxes[$scope.box.id] = $scope;
          $scope.$watch("annotation", function(annotationId) {
            $scope.currentWidget = void 0;
            return $scope.isWidgetOpened = false;
          });
          ctrl = this;
          ctrl.addTile = function(tile) {
            return $scope.tiles.push(tile);
          };
          return ctrl.closeTiles = function() {
            var tile, _i, _len, _ref, _results;
            _ref = $scope.tiles;
            _results = [];
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              tile = _ref[_i];
              _results.push(tile.close());
            }
            return _results;
          };
        }
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.directives.wlEntityForm', []).directive('wlEntityForm', [
    'configuration', '$log', function(configuration, $log) {
      return {
        restrict: 'E',
        scope: {
          entity: '=',
          onSubmit: '&'
        },
        template: "<div name=\"wordlift\" class=\"wl-entity-form\">\n<div ng-show=\"entity.images.length > 0\">\n    <img ng-src=\"{{entity.images[0]}}\" wl-src=\"{{configuration.defaultThumbnailPath}}\" />\n</div>\n<div>\n    <label>Entity label</label>\n    <input type=\"text\" ng-model=\"entity.label\" ng-disabled=\"checkEntityId(entity.id)\" />\n</div>\n<div>\n    <label>Entity type</label>\n    <select ng-hide=\"hasOccurences()\" ng-model=\"entity.mainType\" ng-options=\"type.id as type.name for type in supportedTypes\" ></select>\n    <input ng-show=\"hasOccurences()\" type=\"text\" ng-value=\"getCurrentTypeUri()\" disabled=\"true\" />\n</div>\n<div>\n    <label>Entity Description</label>\n    <textarea ng-model=\"entity.description\" rows=\"6\"></textarea>\n</div>\n<div ng-show=\"checkEntityId(entity.id)\">\n    <label>Entity Id</label>\n    <input type=\"text\" ng-model=\"entity.id\" disabled=\"true\" />\n</div>\n<div class=\"wl-suggested-sameas-wrapper\">\n    <label>Entity Same as (*)</label>\n    <input type=\"text\" ng-model=\"entity.sameAs\" />\n    <h5 ng-show=\"entity.suggestedSameAs.length > 0\">same as suggestions</h5>\n    <div ng-click=\"setSameAs(sameAs)\" ng-class=\"{ 'active': entity.sameAs == sameAs }\" class=\"wl-sameas\" ng-repeat=\"sameAs in entity.suggestedSameAs\">\n      {{sameAs}}\n    </div>\n</div>\n\n<div class=\"wl-submit-wrapper\">\n  <span class=\"button button-primary\" ng-click=\"onSubmit()\">Save</span>\n</div>\n\n</div>",
        link: function($scope, $element, $attrs, $ctrl) {
          var type;
          $scope.configuration = configuration;
          $scope.getCurrentTypeUri = function() {
            var type, _i, _len, _ref;
            _ref = configuration.types;
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              type = _ref[_i];
              if (type.css === ("wl-" + $scope.entity.mainType)) {
                return type.uri;
              }
            }
          };
          $scope.hasOccurences = function() {
            return $scope.entity.occurrences.length > 0;
          };
          $scope.setSameAs = function(uri) {
            return $scope.entity.sameAs = uri;
          };
          $scope.checkEntityId = function(uri) {
            return /^(f|ht)tps?:\/\//i.test(uri);
          };
          return $scope.supportedTypes = (function() {
            var _i, _len, _ref, _results;
            _ref = configuration.types;
            _results = [];
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              type = _ref[_i];
              _results.push({
                id: type.css.replace('wl-', ''),
                name: type.uri
              });
            }
            return _results;
          })();
        }
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.directives.wlEntityTile', []).directive('wlEntityTile', [
    'configuration', '$log', function(configuration, $log) {
      return {
        require: '^wlClassificationBox',
        restrict: 'E',
        scope: {
          entity: '=',
          isSelected: '=',
          onEntitySelect: '&'
        },
        template: "<div ng-class=\"'wl-' + entity.mainType\" class=\"entity\">\n        <div class=\"entity-header\">\n    \n          <i ng-click=\"onEntitySelect()\" ng-hide=\"annotation\" ng-class=\"{ 'wl-selected' : isSelected, 'wl-unselected' : !isSelected }\"></i>\n          <i ng-click=\"onEntitySelect()\" class=\"type\"></i>\n          <span class=\"label\" ng-click=\"onEntitySelect()\">{{entity.label}}</span>\n\n          <small ng-show=\"entity.occurrences.length > 0\">({{entity.occurrences.length}})</small>\n          <span ng-show=\"isInternal()\">*</span>  \n          <i ng-class=\"{ 'wl-more': isOpened == false, 'wl-less': isOpened == true }\" ng-click=\"toggle()\"></i>\n  </div>\n        <div class=\"details\" ng-show=\"isOpened\">\n          <wl-entity-form entity=\"entity\" on-submit=\"toggle()\"></wl-entity-form>\n        </div>\n</div>",
        link: function($scope, $element, $attrs, $boxCtrl) {
          $boxCtrl.addTile($scope);
          $scope.isOpened = false;
          $scope.isInternal = function() {
            if ($scope.entity.id.startsWith(configuration.datasetUri)) {
              return true;
            }
            return false;
          };
          $scope.open = function() {
            return $scope.isOpened = true;
          };
          $scope.close = function() {
            return $scope.isOpened = false;
          };
          return $scope.toggle = function() {
            if (!$scope.isOpened) {
              $boxCtrl.closeTiles();
            }
            return $scope.isOpened = !$scope.isOpened;
          };
        }
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.directives.wlEntityInputBox', []).directive('wlEntityInputBox', function() {
    return {
      restrict: 'E',
      scope: {
        entity: '='
      },
      template: "        <div>\n\n          <input type='text' name='wl_entities[{{entity.id}}][uri]' value='{{entity.id}}'>\n          <input type='text' name='wl_entities[{{entity.id}}][label]' value='{{entity.label}}'>\n          <textarea name='wl_entities[{{entity.id}}][description]'>{{entity.description}}</textarea>\n          <input type='text' name='wl_entities[{{entity.id}}][main_type]' value='wl-{{entity.mainType}}'>\n\n          <input ng-repeat=\"type in entity.types\" type='text'\n          	name='wl_entities[{{entity.id}}][type][]' value='{{type}}' />\n          <input ng-repeat=\"image in entity.images\" type='text'\n            name='wl_entities[{{entity.id}}][image][]' value='{{image}}' />\n          <input ng-repeat=\"sameAs in entity.sameAs\" type='text'\n            name='wl_entities[{{entity.id}}][sameas][]' value='{{sameAs}}' />\n\n</div>"
    };
  });

  angular.module('wordlift.editpost.widget.services.AnalysisService', []).service('AnalysisService', [
    'configuration', '$log', '$http', '$rootScope', function(configuration, $log, $http, $rootScope) {
      var box, extend, findAnnotation, merge, service, type, uniqueId, _i, _j, _len, _len1, _ref, _ref1;
      uniqueId = function(length) {
        var id;
        if (length == null) {
          length = 8;
        }
        id = '';
        while (id.length < length) {
          id += Math.random().toString(36).substr(2);
        }
        return id.substr(0, length);
      };
      merge = function(options, overrides) {
        return extend(extend({}, options), overrides);
      };
      extend = function(object, properties) {
        var key, val;
        for (key in properties) {
          val = properties[key];
          object[key] = val;
        }
        return object;
      };
      findAnnotation = function(annotations, start, end) {
        var annotation, id;
        for (id in annotations) {
          annotation = annotations[id];
          if (annotation.start === start && annotation.end === end) {
            return annotation;
          }
        }
      };
      service = {
        _isRunning: false,
        _currentAnalysis: void 0,
        _supportedTypes: [],
        _defaultType: "thing"
      };
      service.cleanAnnotations = function(analysis, positions) {
        var annotation, annotationId, annotationRange, isOverlapping, pos, _i, _j, _len, _ref, _ref1, _ref2, _results;
        if (positions == null) {
          positions = [];
        }
        _ref = analysis.annotations;
        for (annotationId in _ref) {
          annotation = _ref[annotationId];
          if (annotation.start > 0 && annotation.end > annotation.start) {
            annotationRange = (function() {
              _results = [];
              for (var _i = _ref1 = annotation.start, _ref2 = annotation.end; _ref1 <= _ref2 ? _i <= _ref2 : _i >= _ref2; _ref1 <= _ref2 ? _i++ : _i--){ _results.push(_i); }
              return _results;
            }).apply(this);
            isOverlapping = false;
            for (_j = 0, _len = annotationRange.length; _j < _len; _j++) {
              pos = annotationRange[_j];
              if (__indexOf.call(positions, pos) >= 0) {
                isOverlapping = true;
              }
              break;
            }
            if (isOverlapping) {
              $log.warn("Annotation with id: " + annotationId + " start: " + annotation.start + " end: " + annotation.end + " overlaps an existing annotation");
              $log.debug(annotation);
              this.deleteAnnotation(analysis, annotationId);
            } else {
              positions = positions.concat(annotationRange);
            }
          }
        }
        return analysis;
      };
      _ref = configuration.classificationBoxes;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        box = _ref[_i];
        _ref1 = box.registeredTypes;
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          type = _ref1[_j];
          if (__indexOf.call(service._supportedTypes, type) < 0) {
            service._supportedTypes.push(type);
          }
        }
      }
      service.createEntity = function(params) {
        var defaults;
        if (params == null) {
          params = {};
        }
        defaults = {
          id: 'local-entity-' + uniqueId(32),
          label: '',
          description: '',
          mainType: 'thing',
          types: [],
          images: [],
          confidence: 1,
          occurrences: [],
          annotations: {}
        };
        return merge(defaults, params);
      };
      service.deleteAnnotation = function(analysis, annotationId) {
        var ea, index, _k, _len2, _ref2;
        $log.warn("Going to remove overlapping annotation with id " + annotationId);
        if (analysis.annotations[annotationId] != null) {
          _ref2 = analysis.annotations[annotationId].entityMatches;
          for (index = _k = 0, _len2 = _ref2.length; _k < _len2; index = ++_k) {
            ea = _ref2[index];
            delete analysis.entities[ea.entityId].annotations[annotationId];
          }
          delete analysis.annotations[annotationId];
        }
        return analysis;
      };
      service.createAnnotation = function(params) {
        var defaults;
        if (params == null) {
          params = {};
        }
        defaults = {
          id: 'urn:local-text-annotation-' + uniqueId(32),
          text: '',
          start: 0,
          end: 0,
          entities: [],
          entityMatches: []
        };
        return merge(defaults, params);
      };
      service.parse = function(data) {
        var annotation, annotationId, ea, em, entity, id, index, localEntity, local_confidence, _k, _l, _len2, _len3, _ref10, _ref11, _ref2, _ref3, _ref4, _ref5, _ref6, _ref7, _ref8, _ref9;
        $log.debug("Incoming entities");
        $log.debug(data.entities);
        _ref2 = configuration.entities;
        for (id in _ref2) {
          localEntity = _ref2[id];
          data.entities[id] = localEntity;
        }
        _ref3 = data.entities;
        for (id in _ref3) {
          entity = _ref3[id];
          if (!entity.label) {
            $log.warn("Label missing for entity " + id);
          }
          if (!entity.description) {
            $log.warn("Description missing for entity " + id);
          }
          if (!entity.sameAs) {
            $log.warn("sameAs missing for entity " + id);
            entity.sameAs = [];
            if ((_ref4 = configuration.entities[id]) != null) {
              _ref4.sameAs = [];
            }
            $log.debug("Schema.org sameAs overridden for entity " + id);
          }
          if (_ref5 = entity.mainType, __indexOf.call(this._supportedTypes, _ref5) < 0) {
            $log.warn("Schema.org type " + entity.mainType + " for entity " + id + " is not supported from current classification boxes configuration");
            entity.mainType = this._defaultType;
            if ((_ref6 = configuration.entities[id]) != null) {
              _ref6.mainType = this._defaultType;
            }
            $log.debug("Schema.org type overridden for entity " + id);
          }
          entity.id = id;
          entity.occurrences = [];
          entity.annotations = {};
          entity.confidence = 1;
        }
        _ref7 = data.annotations;
        for (id in _ref7) {
          annotation = _ref7[id];
          annotation.id = id;
          annotation.entities = {};
          _ref8 = annotation.entityMatches;
          for (index = _k = 0, _len2 = _ref8.length; _k < _len2; index = ++_k) {
            ea = _ref8[index];
            if (!data.entities[ea.entityId].label) {
              data.entities[ea.entityId].label = annotation.text;
              $log.debug("Missing label retrived from related annotation for entity " + ea.entityId);
            }
            data.entities[ea.entityId].annotations[id] = annotation;
            data.annotations[id].entities[ea.entityId] = data.entities[ea.entityId];
          }
        }
        _ref9 = data.entities;
        for (id in _ref9) {
          entity = _ref9[id];
          _ref10 = data.annotations;
          for (annotationId in _ref10) {
            annotation = _ref10[annotationId];
            local_confidence = 1;
            _ref11 = annotation.entityMatches;
            for (_l = 0, _len3 = _ref11.length; _l < _len3; _l++) {
              em = _ref11[_l];
              if ((em.entityId != null) && em.entityId === id) {
                local_confidence = em.confidence;
              }
            }
            entity.confidence = entity.confidence * local_confidence;
          }
        }
        return data;
      };
      service.getSuggestedSameAs = function(content) {
        var promise;
        return promise = this._innerPerform(content).success(function(data) {
          var entity, id, suggestions, _ref2;
          suggestions = [];
          _ref2 = data.entities;
          for (id in _ref2) {
            entity = _ref2[id];
            if (id.startsWith('http')) {
              suggestions.push(id);
            }
          }
          return $rootScope.$broadcast("sameAsRetrieved", suggestions);
        }).error(function(data, status) {
          $log.warn("Error on same as retrieving, statut " + status);
          return $rootScope.$broadcast("sameAsRetrieved", []);
        });
      };
      service._innerPerform = function(content) {
        $log.info("Start to performing analysis");
        return $http({
          method: 'post',
          url: ajaxurl + '?action=wordlift_analyze',
          data: content
        });
      };
      service._updateStatus = function(status) {
        service._isRunning = status;
        return $rootScope.$broadcast("analysisServiceStatusUpdated", status);
      };
      service.perform = function(content) {
        var promise;
        if (service._currentAnalysis) {
          $log.warn("Analysis already runned! Nothing to do ...");
          return;
        }
        service._updateStatus(true);
        return promise = this._innerPerform(content).success(function(data) {
          service._updateStatus(false);
          if (typeof data === 'string') {
            $log.warn("Invalid data returned");
            $log.debug(data);
            return;
          }
          service._currentAnalysis = data;
          return $rootScope.$broadcast("analysisPerformed", service.parse(data));
        }).error(function(data, status) {
          service._updateStatus(false);
          return $log.warn("Error on analysis, statut " + status);
        });
      };
      service.preselect = function(analysis, annotations) {
        var annotation, e, entity, id, textAnnotation, _k, _len2, _ref2, _ref3, _results;
        $log.debug("Going to perform annotations preselection");
        _results = [];
        for (_k = 0, _len2 = annotations.length; _k < _len2; _k++) {
          annotation = annotations[_k];
          if (annotation.start === annotation.end) {
            $log.warn("There is a broken empty annotation for entityId " + annotation.uri);
            continue;
          }
          textAnnotation = findAnnotation(analysis.annotations, annotation.start, annotation.end);
          if (textAnnotation == null) {
            $log.warn("Annotation " + annotation.start + ":" + annotation.end + " for entityId " + annotation.uri + " misses in the analysis");
            textAnnotation = this.createAnnotation({
              start: annotation.start,
              end: annotation.end,
              text: annotation.label
            });
            analysis.annotations[textAnnotation.id] = textAnnotation;
          }
          entity = analysis.entities[annotation.uri];
          _ref2 = configuration.entities;
          for (id in _ref2) {
            e = _ref2[id];
            if (_ref3 = annotation.uri, __indexOf.call(e.sameAs, _ref3) >= 0) {
              entity = analysis.entities[e.id];
            }
          }
          if (entity == null) {
            $log.warn("Entity with uri " + annotation.uri + " is missing both in analysis results and in local storage");
            continue;
          }
          analysis.entities[entity.id].occurrences.push(textAnnotation.id);
          if (analysis.entities[entity.id].annotations[textAnnotation.id] == null) {
            analysis.entities[entity.id].annotations[textAnnotation.id] = textAnnotation;
            analysis.annotations[textAnnotation.id].entityMatches.push({
              entityId: entity.id,
              confidence: 1
            });
            _results.push(analysis.annotations[textAnnotation.id].entities[entity.id] = analysis.entities[entity.id]);
          } else {
            _results.push(void 0);
          }
        }
        return _results;
      };
      return service;
    }
  ]);

  angular.module('wordlift.editpost.widget.services.EditorService', ['wordlift.editpost.widget.services.AnalysisService']).service('EditorService', [
    'AnalysisService', '$log', '$http', '$rootScope', function(AnalysisService, $log, $http, $rootScope) {
      var currentOccurencesForEntity, dedisambiguate, disambiguate, editor, findEntities, findPositions, service;
      findEntities = function(html) {
        var annotation, match, pattern, traslator, _results;
        traslator = Traslator.create(html);
        pattern = /<(\w+)[^>]*\sitemid="([^"]+)"[^>]*>([^<]*)<\/\1>/gim;
        _results = [];
        while (match = pattern.exec(html)) {
          annotation = {
            start: traslator.html2text(match.index),
            end: traslator.html2text(match.index + match[0].length),
            uri: match[2],
            label: match[3]
          };
          _results.push(annotation);
        }
        return _results;
      };
      findPositions = function(entities) {
        var entityAnnotation, positions, _i, _j, _len, _ref, _ref1, _results;
        positions = [];
        for (_i = 0, _len = entities.length; _i < _len; _i++) {
          entityAnnotation = entities[_i];
          positions = positions.concat((function() {
            _results = [];
            for (var _j = _ref = entityAnnotation.start, _ref1 = entityAnnotation.end; _ref <= _ref1 ? _j <= _ref1 : _j >= _ref1; _ref <= _ref1 ? _j++ : _j--){ _results.push(_j); }
            return _results;
          }).apply(this));
        }
        return positions;
      };
      editor = function() {
        return tinyMCE.get('content');
      };
      disambiguate = function(annotation, entity) {
        var discardedItemId, ed;
        ed = editor();
        ed.dom.addClass(annotation.id, "disambiguated");
        ed.dom.addClass(annotation.id, "wl-" + entity.mainType);
        discardedItemId = ed.dom.getAttrib(annotation.id, "itemid");
        ed.dom.setAttrib(annotation.id, "itemid", entity.id);
        return discardedItemId;
      };
      dedisambiguate = function(annotation, entity) {
        var discardedItemId, ed;
        ed = editor();
        ed.dom.removeClass(annotation.id, "disambiguated");
        ed.dom.removeClass(annotation.id, "wl-" + entity.mainType);
        discardedItemId = ed.dom.getAttrib(annotation.id, "itemid");
        ed.dom.setAttrib(annotation.id, "itemid", "");
        return discardedItemId;
      };
      currentOccurencesForEntity = function(entityId) {
        var annotation, annotations, ed, itemId, occurrences, _i, _len;
        ed = editor();
        occurrences = [];
        if (entityId === "") {
          return occurrences;
        }
        annotations = ed.dom.select("span.textannotation");
        for (_i = 0, _len = annotations.length; _i < _len; _i++) {
          annotation = annotations[_i];
          itemId = ed.dom.getAttrib(annotation.id, "itemid");
          if (itemId === entityId) {
            occurrences.push(annotation.id);
          }
        }
        return occurrences;
      };
      $rootScope.$on("analysisPerformed", function(event, analysis) {
        if ((analysis != null) && (analysis.annotations != null)) {
          return service.embedAnalysis(analysis);
        }
      });
      $rootScope.$on("embedImageInEditor", function(event, image) {
        return tinyMCE.execCommand('mceInsertContent', false, "<img src=\"" + image + "\" width=\"100%\" />");
      });
      $rootScope.$on("entitySelected", function(event, entity, annotationId) {
        var annotation, discarded, entityId, id, occurrences, _i, _len, _ref;
        discarded = [];
        if (annotationId != null) {
          discarded.push(disambiguate(entity.annotations[annotationId], entity));
        } else {
          _ref = entity.annotations;
          for (id in _ref) {
            annotation = _ref[id];
            discarded.push(disambiguate(annotation, entity));
          }
        }
        for (_i = 0, _len = discarded.length; _i < _len; _i++) {
          entityId = discarded[_i];
          if (entityId) {
            occurrences = currentOccurencesForEntity(entityId);
            $rootScope.$broadcast("updateOccurencesForEntity", entityId, occurrences);
          }
        }
        occurrences = currentOccurencesForEntity(entity.id);
        return $rootScope.$broadcast("updateOccurencesForEntity", entity.id, occurrences);
      });
      $rootScope.$on("entityDeselected", function(event, entity, annotationId) {
        var annotation, discarded, entityId, id, occurrences, _i, _len, _ref;
        discarded = [];
        if (annotationId != null) {
          dedisambiguate(entity.annotations[annotationId], entity);
        } else {
          _ref = entity.annotations;
          for (id in _ref) {
            annotation = _ref[id];
            dedisambiguate(annotation, entity);
          }
        }
        for (_i = 0, _len = discarded.length; _i < _len; _i++) {
          entityId = discarded[_i];
          if (entityId) {
            occurrences = currentOccurencesForEntity(entityId);
            $rootScope.$broadcast("updateOccurencesForEntity", entityId, occurrences);
          }
        }
        occurrences = currentOccurencesForEntity(entity.id);
        return $rootScope.$broadcast("updateOccurencesForEntity", entity.id, occurrences);
      });
      service = {
        hasSelection: function() {
          var ed, pattern;
          ed = editor();
          if (ed != null) {
            if (ed.selection.isCollapsed()) {
              return false;
            }
            pattern = /<([\/]*[a-z]+)[^<]*>/;
            if (pattern.test(ed.selection.getContent())) {
              $log.warn("The selection overlaps html code");
              return false;
            }
            return true;
          }
          return false;
        },
        updateContentEditableStatus: function(status) {
          var ed;
          ed = editor();
          $log.debug("Going to set contenteditable attribute on " + status);
          return ed.getBody().setAttribute('contenteditable', status);
        },
        createTextAnnotationFromCurrentSelection: function() {
          var content, ed, htmlPosition, text, textAnnotation, textAnnotationSpan, textPosition, traslator;
          ed = editor();
          if (ed.selection.isCollapsed()) {
            $log.warn("Invalid selection! The text annotation cannot be created");
            return;
          }
          text = "" + (ed.selection.getSel());
          textAnnotation = AnalysisService.createAnnotation({
            text: text
          });
          textAnnotationSpan = "<span id=\"" + textAnnotation.id + "\" class=\"textannotation selected\">" + (ed.selection.getContent()) + "</span>";
          ed.selection.setContent(textAnnotationSpan);
          content = ed.getContent({
            format: 'raw'
          });
          traslator = Traslator.create(content);
          htmlPosition = content.indexOf(textAnnotationSpan);
          textPosition = traslator.html2text(htmlPosition);
          textAnnotation.start = textPosition;
          textAnnotation.end = textAnnotation.start + text.length;
          return $rootScope.$broadcast('textAnnotationAdded', textAnnotation);
        },
        selectAnnotation: function(annotationId) {
          var annotation, ed, _i, _len, _ref;
          ed = editor();
          _ref = ed.dom.select("span.textannotation");
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            annotation = _ref[_i];
            ed.dom.removeClass(annotation.id, "selected");
          }
          $rootScope.$broadcast('textAnnotationClicked', void 0);
          if (ed.dom.hasClass(annotationId, "textannotation")) {
            ed.dom.addClass(annotationId, "selected");
            return $rootScope.$broadcast('textAnnotationClicked', annotationId);
          }
        },
        embedAnalysis: (function(_this) {
          return function(analysis) {
            var annotation, annotationId, ed, element, em, entities, entity, html, isDirty, traslator, _i, _len, _ref, _ref1;
            ed = editor();
            html = ed.getContent({
              format: 'raw'
            });
            entities = findEntities(html);
            AnalysisService.cleanAnnotations(analysis, findPositions(entities));
            AnalysisService.preselect(analysis, entities);
            while (html.match(/<(\w+)[^>]*\sclass="textannotation[^"]*"[^>]*>([^<]+)<\/\1>/gim, '$2')) {
              html = html.replace(/<(\w+)[^>]*\sclass="textannotation[^"]*"[^>]*>([^<]*)<\/\1>/gim, '$2');
            }
            traslator = Traslator.create(html);
            _ref = analysis.annotations;
            for (annotationId in _ref) {
              annotation = _ref[annotationId];
              if (!(0 < annotation.entityMatches.length)) {
                continue;
              }
              element = "<span id=\"" + annotationId + "\" class=\"textannotation";
              _ref1 = annotation.entityMatches;
              for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
                em = _ref1[_i];
                entity = analysis.entities[em.entityId];
                if (__indexOf.call(entity.occurrences, annotationId) >= 0) {
                  element += " disambiguated wl-" + entity.mainType + "\" itemid=\"" + entity.id;
                }
              }
              element += "\">";
              traslator.insertHtml(element, {
                text: annotation.start
              });
              traslator.insertHtml('</span>', {
                text: annotation.end
              });
            }
            $rootScope.$broadcast("analysisEmbedded");
            isDirty = ed.isDirty();
            ed.setContent(traslator.getHtml(), {
              format: 'raw'
            });
            return ed.isNotDirty = !isDirty;
          };
        })(this)
      };
      return service;
    }
  ]);

  angular.module('wordlift.editpost.widget.services.RelatedPostDataRetrieverService', []).service('RelatedPostDataRetrieverService', [
    'configuration', '$log', '$http', '$rootScope', function(configuration, $log, $http, $rootScope) {
      var service;
      service = {};
      service.load = function(entityIds) {
        var uri;
        if (entityIds == null) {
          entityIds = [];
        }
        uri = "admin-ajax.php?action=wordlift_related_posts&post_id=" + configuration.currentPostId;
        $log.debug("Going to find related posts");
        $log.debug(entityIds);
        return $http({
          method: 'post',
          url: uri,
          data: entityIds
        }).success(function(data) {
          $log.debug(data);
          return $rootScope.$broadcast("relatedPostsLoaded", data);
        }).error(function(data, status) {
          return $log.warn("Error loading related posts");
        });
      };
      return service;
    }
  ]);

  angular.module('wordlift.editpost.widget.providers.ConfigurationProvider', []).provider("configuration", function() {
    var provider, _configuration;
    _configuration = void 0;
    provider = {
      setConfiguration: function(configuration) {
        return _configuration = configuration;
      },
      $get: function() {
        return _configuration;
      }
    };
    return provider;
  });

  $ = jQuery;

  angular.module('wordlift.editpost.widget', ['wordlift.ui.carousel', 'wordlift.utils.directives', 'wordlift.editpost.widget.providers.ConfigurationProvider', 'wordlift.editpost.widget.controllers.EditPostWidgetController', 'wordlift.editpost.widget.directives.wlClassificationBox', 'wordlift.editpost.widget.directives.wlEntityForm', 'wordlift.editpost.widget.directives.wlEntityTile', 'wordlift.editpost.widget.directives.wlEntityInputBox', 'wordlift.editpost.widget.services.AnalysisService', 'wordlift.editpost.widget.services.EditorService', 'wordlift.editpost.widget.services.RelatedPostDataRetrieverService']).config(function(configurationProvider) {
    return configurationProvider.setConfiguration(window.wordlift);
  });

  $(container = $("<div id=\"wordlift-edit-post-wrapper\" ng-controller=\"EditPostWidgetController\">\n	\n      <h3 class=\"wl-widget-headline\"><span>Semantic tagging</span> <span ng-show=\"isRunning\" class=\"wl-spinner\"></span></h3>\n      <div ng-click=\"createTextAnnotationFromCurrentSelection()\" id=\"wl-add-entity-button-wrapper\">\n        <span class=\"button\" ng-class=\"{ 'button-primary selected' : isThereASelection, 'preview' : !isThereASelection }\">Add entity</span>\n        <div class=\"clear\" />     \n      </div>\n      \n      <div ng-show=\"annotation\">\n        <h4 class=\"wl-annotation-label\">\n          <i class=\"wl-annotation-label-icon\"></i>\n          {{ analysis.annotations[ annotation ].text }} \n          <small>[ {{ analysis.annotations[ annotation ].start }}, {{ analysis.annotations[ annotation ].end }} ]</small>\n          <i class=\"wl-annotation-label-remove-icon\" ng-click=\"selectAnnotation(undefined)\"></i>\n        </h4>\n        <wl-entity-form entity=\"newEntity\" on-submit=\"addNewEntityToAnalysis()\" ng-show=\"analysis.annotations[annotation].entityMatches.length == 0\"></wl-entity-form>\n      </div>\n\n      <wl-classification-box ng-repeat=\"box in configuration.classificationBoxes\">\n        <div ng-hide=\"annotation\" class=\"wl-without-annotation\">\n          <wl-entity-tile is-selected=\"isEntitySelected(entity, box)\" on-entity-select=\"onSelectedEntityTile(entity, box)\" entity=\"entity\" ng-repeat=\"entity in analysis.entities | filterEntitiesByTypesAndRelevance:box.registeredTypes\"></wl-entity>\n        </div>  \n        <div ng-show=\"annotation\" class=\"wl-with-annotation\">\n          <wl-entity-tile is-selected=\"isLinkedToCurrentAnnotation(entity)\" on-entity-select=\"onSelectedEntityTile(entity, box)\" entity=\"entity\" ng-repeat=\"entity in analysis.annotations[annotation].entities | filterEntitiesByTypes:box.registeredTypes\"\" ></wl-entity>\n        </div>  \n      </wl-classification-box>\n\n      <h3 class=\"wl-widget-headline\"><span>Suggested images</span></h3>\n      <div wl-carousel>\n        <div ng-repeat=\"(image, label) in images\" class=\"wl-card\" wl-carousel-pane>\n          <img ng-src=\"{{image}}\" wl-src=\"{{configuration.defaultThumbnailPath}}\" />\n        </div>\n      </div>\n\n      <h3 class=\"wl-widget-headline\"><span>Related posts</span></h3>\n      <div wl-carousel>\n        <div ng-repeat=\"post in relatedPosts\" class=\"wl-card\" wl-carousel-pane>\n          <img ng-src=\"{{post.thumbnail}}\" wl-src=\"{{configuration.defaultThumbnailPath}}\" />\n          <div class=\"wl-card-title\">\n            <a ng-href=\"{{post.link}}\">{{post.post_title}}</a>\n          </div>\n        </div>\n      </div>\n      \n      <div class=\"wl-entity-input-boxes\">\n        <wl-entity-input-box annotation=\"annotation\" entity=\"entity\" ng-repeat=\"entity in analysis.entities | isEntitySelected\"></wl-entity-input-box>\n        <div ng-repeat=\"(box, entities) in selectedEntities\">\n          <input type='text' name='wl_boxes[{{box}}][]' value='{{id}}' ng-repeat=\"(id, entity) in entities\">\n        </div> \n      </div>   \n    </div>").appendTo('#wordlift-edit-post-outer-wrapper'), injector = angular.bootstrap($('#wordlift-edit-post-wrapper'), ['wordlift.editpost.widget']), tinymce.PluginManager.add('wordlift', function(editor, url) {
    var fireEvent;
    fireEvent = function(editor, eventName, callback) {
      return injector.invoke([
        '$log', function($log) {
          $log.debug("Going to register a callback on " + eventName + " event");
          switch (tinymce.majorVersion) {
            case '4':
              return editor.on(eventName, callback);
            case '3':
              return editor["on" + eventName].add(callback);
          }
        }
      ]);
    };
    injector.invoke([
      '$rootScope', '$log', function($rootScope, $log) {
        var method, originalMethod, _i, _len, _ref, _results;
        if (editor.id === "content") {
          $log.debug("Going to hack wp.mce.views api from editor with id '" + editor.id + "' ...");
          _ref = ['setMarkers', 'toViews'];
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            method = _ref[_i];
            if (wp.mce.views[method] != null) {
              originalMethod = wp.mce.views[method];
              $log.warn("Override wp.mce.views method " + method + "() to prevent shortcodes rendering");
              wp.mce.views[method] = function(content) {
                return content;
              };
              $rootScope.$on("analysisEmbedded", function(event) {
                $log.info("Going to restore wp.mce.views method " + method + "()");
                return wp.mce.views[method] = originalMethod;
              });
              break;
            } else {
              _results.push(void 0);
            }
          }
          return _results;
        }
      }
    ]);
    fireEvent(editor, "LoadContent", function(e) {
      return injector.invoke([
        'AnalysisService', 'EditorService', '$rootScope', '$log', function(AnalysisService, EditorService, $rootScope, $log) {
          return $rootScope.$apply(function() {
            var html, text;
            EditorService.updateContentEditableStatus(false);
            html = editor.getContent({
              format: 'raw'
            });
            text = Traslator.create(html).getText();
            if (text.match(/[a-zA-Z0-9]+/)) {
              return AnalysisService.perform(text);
            } else {
              return $log.warn("Blank content: nothing to do!");
            }
          });
        }
      ]);
    });
    fireEvent(editor, "NodeChange", function(e) {
      return injector.invoke([
        'AnalysisService', 'EditorService', '$rootScope', '$log', function(AnalysisService, EditorService, $rootScope, $log) {
          if (!AnalysisService._currentAnalysis) {
            $log.warn("Analysis not performed! Nothing to do ...");
            return;
          }
          return $rootScope.$apply(function() {
            return $rootScope.selectionStatus = EditorService.hasSelection();
          });
        }
      ]);
    });
    return fireEvent(editor, "Click", function(e) {
      return injector.invoke([
        'AnalysisService', 'EditorService', '$rootScope', '$log', function(AnalysisService, EditorService, $rootScope, $log) {
          if (!AnalysisService._currentAnalysis) {
            $log.warn("Analysis not performed! Nothing to do ...");
            return;
          }
          return $rootScope.$apply(function() {
            return EditorService.selectAnnotation(e.target.id);
          });
        }
      ]);
    });
  }));

}).call(this);

//# sourceMappingURL=wordlift-reloaded.js.map
