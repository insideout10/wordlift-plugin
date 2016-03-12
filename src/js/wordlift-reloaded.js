(function() {
  var $, Traslator, container, injector,
    indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

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
      var htmlElem, htmlLength, htmlPost, htmlPre, htmlProcessed, match, pattern, ref, textLength, textPost, textPre;
      this._htmlPositions = [];
      this._textPositions = [];
      this._text = '';
      pattern = /([^&<>]*)(&[^&;]*;|<[^>]*>)([^&<>]*)/gim;
      textLength = 0;
      htmlLength = 0;
      while (match = pattern.exec(this._html)) {
        htmlPre = match[1];
        htmlElem = match[2];
        htmlPost = match[3];
        textPre = htmlPre + ((ref = htmlElem.toLowerCase()) === '</p>' || ref === '</li>' ? '\n\n' : '');
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
      var htmlPos, i, j, ref, textPos;
      htmlPos = 0;
      textPos = 0;
      for (i = j = 0, ref = this._textPositions.length; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
        if (pos < this._textPositions[i]) {
          break;
        }
        htmlPos = this._htmlPositions[i];
        textPos = this._textPositions[i];
      }
      return htmlPos + pos - textPos;
    };

    Traslator.prototype.html2text = function(pos) {
      var htmlPos, i, j, ref, textPos;
      if (pos < this._htmlPositions[0]) {
        return 0;
      }
      htmlPos = 0;
      textPos = 0;
      for (i = j = 0, ref = this._htmlPositions.length; 0 <= ref ? j < ref : j > ref; i = 0 <= ref ? ++j : --j) {
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

  angular.module('wordlift.utils.directives', []).directive('wlOnError', [
    '$parse', '$window', '$log', function($parse, $window, $log) {
      return {
        restrict: 'A',
        compile: function($element, $attrs) {
          return function(scope, element) {
            var fn;
            fn = $parse($attrs.wlOnError);
            return element.on('error', function(event) {
              var callback;
              callback = function() {
                return fn(scope, {
                  $event: event
                });
              };
              return scope.$apply(callback);
            });
          };
        }
      };
    }
  ]).directive('wlFallback', [
    '$window', '$log', function($window, $log) {
      return {
        restrict: 'A',
        priority: 99,
        link: function($scope, $element, $attrs, $ctrl) {
          return $element.bind('error', function() {
            if ($attrs.src !== $attrs.wlFallback) {
              $log.warn("Error on " + $attrs.src + "! Going to fallback on " + $attrs.wlFallback);
              return $attrs.$set('src', $attrs.wlFallback);
            }
          });
        }
      };
    }
  ]);

  angular.module('wordlift.ui.carousel', ['ngTouch']).directive('wlCarousel', [
    '$window', '$log', function($window, $log) {
      return {
        restrict: 'A',
        scope: true,
        transclude: true,
        template: "<div class=\"wl-carousel\" ng-class=\"{ 'active' : areControlsVisible }\" ng-show=\"panes.length > 0\" ng-mouseover=\"showControls()\" ng-mouseleave=\"hideControls()\">\n  <div class=\"wl-panes\" ng-style=\"{ width: panesWidth, left: position }\" ng-transclude ng-swipe-left=\"next()\" ng-swipe-right=\"prev()\" ></div>\n  <div class=\"wl-carousel-arrows\" ng-show=\"areControlsVisible\" ng-class=\"{ 'active' : ( panes.length > 1 ) }\">\n    <i class=\"wl-angle left\" ng-click=\"prev()\" ng-show=\"isPrevArrowVisible()\" />\n    <i class=\"wl-angle right\" ng-click=\"next()\" ng-show=\"isNextArrowVisible()\" />\n  </div>\n</div>",
        controller: [
          '$scope', '$element', '$attrs', '$log', function($scope, $element, $attrs, $log) {
            var ctrl, w;
            w = angular.element($window);
            $scope.setItemWidth = function() {
              return $element.width() / $scope.visibleElements();
            };
            $scope.showControls = function() {
              return $scope.areControlsVisible = true;
            };
            $scope.hideControls = function() {
              return $scope.areControlsVisible = false;
            };
            $scope.visibleElements = function() {
              if ($element.width() > 460) {
                return 4;
              }
              return 1;
            };
            $scope.isPrevArrowVisible = function() {
              return $scope.currentPaneIndex > 0;
            };
            $scope.isNextArrowVisible = function() {
              return ($scope.panes.length - $scope.currentPaneIndex) > $scope.visibleElements();
            };
            $scope.next = function() {
              if (($scope.currentPaneIndex + $scope.visibleElements() + 1) > $scope.panes.length) {
                return;
              }
              $scope.position = $scope.position - $scope.itemWidth;
              return $scope.currentPaneIndex = $scope.currentPaneIndex + 1;
            };
            $scope.prev = function() {
              if ($scope.currentPaneIndex === 0) {
                return;
              }
              $scope.position = $scope.position + $scope.itemWidth;
              return $scope.currentPaneIndex = $scope.currentPaneIndex - 1;
            };
            $scope.setPanesWrapperWidth = function() {
              $scope.panesWidth = $scope.panes.length * $scope.itemWidth;
              $scope.position = 0;
              return $scope.currentPaneIndex = 0;
            };
            $scope.itemWidth = $scope.setItemWidth();
            $scope.panesWidth = void 0;
            $scope.panes = [];
            $scope.position = 0;
            $scope.currentPaneIndex = 0;
            $scope.areControlsVisible = false;
            w.bind('resize', function() {
              var j, len, pane, ref;
              $scope.itemWidth = $scope.setItemWidth();
              $scope.setPanesWrapperWidth();
              ref = $scope.panes;
              for (j = 0, len = ref.length; j < len; j++) {
                pane = ref[j];
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
              var index, j, len, pane, ref, unregisterPaneIndex;
              unregisterPaneIndex = void 0;
              ref = $scope.panes;
              for (index = j = 0, len = ref.length; j < len; index = ++j) {
                pane = ref[index];
                if (pane.scope.$id === scope.$id) {
                  unregisterPaneIndex = index;
                }
              }
              $scope.panes.splice(unregisterPaneIndex, 1);
              return $scope.setPanesWrapperWidth();
            };
          }
        ]
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
          $element.addClass("wl-carousel-item");
          $scope.setWidth = function(size) {
            return $element.css('width', size + "px");
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

  angular.module('wordlift.editpost.widget.controllers.EditPostWidgetController', ['wordlift.editpost.widget.services.AnalysisService', 'wordlift.editpost.widget.services.EditorService', 'wordlift.editpost.widget.services.GeoLocationService', 'wordlift.editpost.widget.providers.ConfigurationProvider']).filter('filterEntitiesByTypesAndRelevance', [
    'configuration', '$log', function(configuration, $log) {
      return function(items, types) {
        var annotations_count, entity, filtered, id, ref, treshold;
        filtered = [];
        if (items == null) {
          return filtered;
        }
        treshold = Math.floor(((1 / 120) * Object.keys(items).length) + 0.75);
        for (id in items) {
          entity = items[id];
          if (ref = entity.mainType, indexOf.call(types, ref) >= 0) {
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
        var entity, filtered, id, ref;
        filtered = [];
        for (id in items) {
          entity = items[id];
          if (ref = entity.mainType, indexOf.call(types, ref) >= 0) {
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
    'GeoLocationService', 'RelatedPostDataRetrieverService', 'EditorService', 'AnalysisService', 'configuration', '$log', '$scope', '$rootScope', '$compile', function(GeoLocationService, RelatedPostDataRetrieverService, EditorService, AnalysisService, configuration, $log, $scope, $rootScope, $compile) {
      var box, j, len, ref;
      $scope.isRunning = false;
      $scope.analysis = void 0;
      $scope.relatedPosts = void 0;
      $scope.newEntity = AnalysisService.createEntity();
      $scope.selectedEntities = {};
      $scope.suggestedPlaces = {};
      $scope.publishedPlace = void 0;
      $scope.topic = void 0;
      $scope.annotation = void 0;
      $scope.boxes = [];
      $scope.images = {};
      $scope.isThereASelection = false;
      $scope.configuration = configuration;
      $scope.errors = [];
      RelatedPostDataRetrieverService.load(Object.keys($scope.configuration.entities));
      $rootScope.$on("analysisFailed", function(event, errorMsg) {
        return $scope.addError(errorMsg);
      });
      $rootScope.$on("analysisServiceStatusUpdated", function(event, newStatus) {
        $scope.isRunning = newStatus;
        return EditorService.updateContentEditableStatus(!newStatus);
      });
      $rootScope.$watch('selectionStatus', function() {
        return $scope.isThereASelection = $rootScope.selectionStatus;
      });
      ref = $scope.configuration.classificationBoxes;
      for (j = 0, len = ref.length; j < len; j++) {
        box = ref[j];
        $scope.selectedEntities[box.id] = {};
      }
      $scope.addError = function(errorMsg) {
        return $scope.errors.unshift({
          type: 'error',
          msg: errorMsg
        });
      };
      $scope.createTextAnnotationFromCurrentSelection = function() {
        return EditorService.createTextAnnotationFromCurrentSelection();
      };
      $scope.selectAnnotation = function(annotationId) {
        return EditorService.selectAnnotation(annotationId);
      };
      $scope.hasAnalysis = function() {
        return $scope.analysis != null;
      };
      $scope.isEntitySelected = function(entity, box) {
        return $scope.selectedEntities[box.id][entity.id] != null;
      };
      $scope.isLinkedToCurrentAnnotation = function(entity) {
        var ref1;
        return (ref1 = $scope.annotation, indexOf.call(entity.occurrences, ref1) >= 0);
      };
      $scope.addNewEntityToAnalysis = function(scope) {
        var annotation;
        if ($scope.newEntity.sameAs) {
          $scope.newEntity.sameAs = [$scope.newEntity.sameAs];
        }
        delete $scope.newEntity.suggestedSameAs;
        $scope.analysis.entities[$scope.newEntity.id] = $scope.newEntity;
        annotation = $scope.analysis.annotations[$scope.annotation];
        annotation.entityMatches.push({
          entityId: $scope.newEntity.id,
          confidence: 1
        });
        $scope.analysis.entities[$scope.newEntity.id].annotations[annotation.id] = annotation;
        $scope.analysis.annotations[$scope.annotation].entities[$scope.newEntity.id] = $scope.newEntity;
        return $scope.onSelectedEntityTile($scope.analysis.entities[$scope.newEntity.id], scope);
      };
      $scope.$on("updateOccurencesForEntity", function(event, entityId, occurrences) {
        var entities, ref1, results1;
        $log.debug("Occurrences " + occurrences.length + " for " + entityId);
        $scope.analysis.entities[entityId].occurrences = occurrences;
        if (occurrences.length === 0) {
          ref1 = $scope.selectedEntities;
          results1 = [];
          for (box in ref1) {
            entities = ref1[box];
            results1.push(delete $scope.selectedEntities[box][entityId]);
          }
          return results1;
        }
      });
      $scope.$watch("annotation", function(newAnnotationId) {
        var annotation;
        $log.debug("Current annotation id changed to " + newAnnotationId);
        if ($scope.isRunning) {
          return;
        }
        if (newAnnotationId == null) {
          return;
        }
        $scope.newEntity = AnalysisService.createEntity();
        annotation = $scope.analysis.annotations[newAnnotationId];
        $scope.newEntity.label = annotation.text;
        return AnalysisService.getSuggestedSameAs(annotation.text);
      });
      $scope.$on("currentUserLocalityDetected", function(event, locality) {
        $log.debug("Looking for entities matching with " + locality);
        return AnalysisService._innerPerform(locality).then(function(response) {
          var entity, id, ref1, results1;
          ref1 = response.data.entities;
          results1 = [];
          for (id in ref1) {
            entity = ref1[id];
            if ('place' === entity.mainType) {
              entity.id = id;
              results1.push($scope.suggestedPlaces[id] = entity);
            } else {
              results1.push(void 0);
            }
          }
          return results1;
        });
      });
      $scope.$on("textAnnotationClicked", function(event, annotationId) {
        var id, ref1, results1;
        $scope.annotation = annotationId;
        ref1 = $scope.boxes;
        results1 = [];
        for (id in ref1) {
          box = ref1[id];
          results1.push(box.addEntityFormIsVisible = false);
        }
        return results1;
      });
      $scope.$on("textAnnotationAdded", function(event, annotation) {
        $log.debug("added a new annotation with Id " + annotation.id);
        $scope.analysis.annotations[annotation.id] = annotation;
        return $scope.annotation = annotation.id;
      });
      $scope.$on("sameAsRetrieved", function(event, sameAs) {
        return $scope.newEntity.suggestedSameAs = sameAs;
      });
      $scope.$on("relatedPostsLoaded", function(event, posts) {
        return $scope.relatedPosts = posts;
      });
      $scope.$on("analysisPerformed", function(event, analysis) {
        var entity, entityId, k, len1, ref1, results1, uri;
        $scope.analysis = analysis;
        ref1 = $scope.configuration.classificationBoxes;
        results1 = [];
        for (k = 0, len1 = ref1.length; k < len1; k++) {
          box = ref1[k];
          results1.push((function() {
            var l, len2, ref2, results2;
            ref2 = box.selectedEntities;
            results2 = [];
            for (l = 0, len2 = ref2.length; l < len2; l++) {
              entityId = ref2[l];
              if (entity = analysis.entities[entityId]) {
                if (entity.occurrences.length === 0) {
                  $log.warn("Entity " + entityId + " selected as " + box.label + " without valid occurences!");
                  continue;
                }
                $scope.selectedEntities[box.id][entityId] = analysis.entities[entityId];
                results2.push((function() {
                  var len3, m, ref3, results3;
                  ref3 = entity.images;
                  results3 = [];
                  for (m = 0, len3 = ref3.length; m < len3; m++) {
                    uri = ref3[m];
                    results3.push($scope.images[uri] = entity.label);
                  }
                  return results3;
                })());
              } else {
                results2.push($log.warn("Entity with id " + entityId + " should be linked to " + box.id + " but is missing"));
              }
            }
            return results2;
          })());
        }
        return results1;
      });
      $scope.updateRelatedPosts = function() {
        var entities, entity, entityIds, id, ref1;
        $log.debug("Going to update related posts box ...");
        entityIds = [];
        ref1 = $scope.selectedEntities;
        for (box in ref1) {
          entities = ref1[box];
          for (id in entities) {
            entity = entities[id];
            entityIds.push(id);
          }
        }
        return RelatedPostDataRetrieverService.load(entityIds);
      };
      $scope.onSelectedEntityTile = function(entity, scope) {
        var k, l, len1, len2, ref1, ref2, uri;
        $log.debug("Entity tile selected for entity " + entity.id + " within '" + scope.id + "' scope");
        $log.debug(entity);
        $log.debug(scope);
        if ($scope.selectedEntities[scope.id][entity.id] == null) {
          $scope.selectedEntities[scope.id][entity.id] = entity;
          ref1 = entity.images;
          for (k = 0, len1 = ref1.length; k < len1; k++) {
            uri = ref1[k];
            $scope.images[uri] = entity.label;
          }
          $scope.$emit("entitySelected", entity, $scope.annotation);
          $scope.selectAnnotation(void 0);
        } else {
          ref2 = entity.images;
          for (l = 0, len2 = ref2.length; l < len2; l++) {
            uri = ref2[l];
            delete $scope.images[uri];
          }
          $scope.$emit("entityDeselected", entity, $scope.annotation);
        }
        return $scope.updateRelatedPosts();
      };
      $scope.getLocation = function() {
        return GeoLocationService.getLocation();
      };
      $scope.isPublishedPlace = function(entity) {
        var ref1;
        return entity.id === ((ref1 = $scope.publishedPlace) != null ? ref1.id : void 0);
      };
      $scope.onPublishedPlaceSelected = function(entity) {
        var ref1;
        if (((ref1 = $scope.publishedPlace) != null ? ref1.id : void 0) === entity.id) {
          $scope.publishedPlace = void 0;
          return;
        }
        return $scope.publishedPlace = entity;
      };
      $scope.isTopic = function(topic) {
        var ref1;
        return topic.id === ((ref1 = $scope.topic) != null ? ref1.id : void 0);
      };
      return $scope.onTopicSelected = function(topic) {
        var ref1;
        if (((ref1 = $scope.topic) != null ? ref1.id : void 0) === topic.id) {
          $scope.topic = void 0;
          return;
        }
        return $scope.topic = topic;
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.directives.wlClassificationBox', []).directive('wlClassificationBox', [
    '$log', function($log) {
      return {
        restrict: 'E',
        scope: true,
        transclude: true,
        template: "<div class=\"classification-box\">\n	<div class=\"box-header\">\n          <h5 class=\"label\">\n            {{box.label}}\n            <span ng-hide=\"addEntityFormIsVisible\" ng-click=\"openAddEntityForm()\" class=\"button\" ng-class=\"{ 'button-primary selected wl-button' : hasAnalysis(), 'preview' : !hasAnalysis() }\">Add entity</span>\n          </h5>\n          <wl-entity-form ng-show=\"addEntityFormIsVisible\" entity=\"newEntity\" box=\"box\" on-submit=\"closeAddEntityForm()\"></wl-entity-form>\n          <div class=\"wl-selected-items-wrapper\" ng-show=\"annotation\">\n            <span ng-class=\"'wl-' + entity.mainType\" ng-repeat=\"(id, entity) in selectedEntities[box.id]\" class=\"wl-selected-item\">\n              {{ entity.label}}\n              <i class=\"wl-deselect\" ng-click=\"onSelectedEntityTile(entity, box)\"></i>\n            </span>\n          </div>\n        </div>\n  			<div class=\"box-tiles\">\n          <div ng-transclude></div>\n  		  </div>\n      </div>	",
        link: function($scope, $element, $attrs, $ctrl) {
          $scope.addEntityFormIsVisible = false;
          $scope.openAddEntityForm = function() {
            if (!$scope.isThereASelection && ($scope.annotation == null)) {
              $scope.addError("Select a text or an existing annotation in order to create a new entity. Text selections are valid only if they do not overlap other existing annotations.");
              return;
            }
            $scope.addEntityFormIsVisible = true;
            if ($scope.annotation != null) {
              $log.debug("There is a current annotation already. Nothing to do");
              return;
            }
            return $scope.createTextAnnotationFromCurrentSelection();
          };
          $scope.closeAddEntityForm = function() {
            $scope.addEntityFormIsVisible = false;
            return $scope.addNewEntityToAnalysis($scope.box);
          };
          return $scope.hasSelectedEntities = function() {
            return Object.keys($scope.selectedEntities[$scope.box.id]).length > 0;
          };
        },
        controller: function($scope, $element, $attrs) {
          var ctrl;
          $scope.tiles = [];
          $scope.boxes[$scope.box.id] = $scope;
          ctrl = this;
          ctrl.addTile = function(tile) {
            return $scope.tiles.push(tile);
          };
          return ctrl.closeTiles = function() {
            var j, len, ref, results1, tile;
            ref = $scope.tiles;
            results1 = [];
            for (j = 0, len = ref.length; j < len; j++) {
              tile = ref[j];
              results1.push(tile.isOpened = false);
            }
            return results1;
          };
        }
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.directives.wlEntityForm', []).directive('wlEntityForm', [
    'configuration', '$window', '$log', function(configuration, $window, $log) {
      return {
        restrict: 'E',
        scope: {
          entity: '=',
          onSubmit: '&',
          box: '='
        },
        template: "<div name=\"wordlift\" class=\"wl-entity-form\">\n<div ng-show=\"entity.images.length > 0\">\n    <img ng-src=\"{{entity.images[0]}}\" wl-on-error=\"removeCurrentImage()\" />\n</div>\n<div>\n    <label class=\"wl-required\">Entity label</label>\n    <input type=\"text\" ng-model=\"entity.label\" ng-disabled=\"checkEntityId(entity.id)\" />\n</div>\n<div ng-hide=\"isInternal()\">\n    <label class=\"wl-required\">Entity type</label>\n    <select ng-hide=\"hasOccurences()\" ng-model=\"entity.mainType\" ng-options=\"type.id as type.name for type in supportedTypes\" ></select>\n    <input ng-show=\"hasOccurences()\" type=\"text\" ng-value=\"getCurrentTypeUri()\" disabled=\"true\" />\n</div>\n<div>\n    <label class=\"wl-required\">Entity Description</label>\n    <textarea ng-model=\"entity.description\" rows=\"6\" ng-disabled=\"isInternal()\"></textarea>\n</div>\n<div ng-hide=\"isInternal()\">\n    <label ng-show=\"checkEntityId(entity.id)\" class=\"wl-required\">Entity Id</label>\n    <input ng-show=\"checkEntityId(entity.id)\" type=\"text\" ng-model=\"entity.id\" disabled=\"true\" />\n</div>\n<div ng-hide=\"isInternal()\">\n    <label>Entity Same as</label>\n    <input type=\"text\" ng-model=\"entity.sameAs\" />\n    <div ng-show=\"entity.suggestedSameAs.length > 0\" class=\"wl-suggested-sameas-wrapper\">\n      <h5>same as suggestions</h5>\n      <div ng-click=\"setSameAs(sameAs)\" ng-class=\"{ 'active': entity.sameAs == sameAs }\" class=\"wl-sameas\" ng-repeat=\"sameAs in entity.suggestedSameAs\">{{sameAs}}</div>\n    </div>\n</div>\n<div ng-hide=\"isInternal()\" class=\"wl-buttons-wrapper\">\n  <span class=\"button button-primary wl-button\" ng-click=\"onSubmit()\">Add</span>\n</div>\n<div ng-show=\"isInternal()\" class=\"wl-buttons-wrapper\">\n  <span class=\"button button-primary wl-button\" ng-click=\"linkTo('lod')\">View Linked Data<i class=\"wl-link\"></i></span>\n  <span class=\"button button-primary wl-button\" ng-click=\"linkTo('edit')\">Edit<i class=\"wl-link\"></i></span>\n</div>\n</div>",
        link: function($scope, $element, $attrs, $ctrl) {
          var availableTypes, j, len, ref, type;
          $scope.configuration = configuration;
          $scope.removeCurrentImage = function() {
            var removed;
            removed = $scope.entity.images.shift();
            return $log.warn("Removed " + removed + " from entity " + $scope.entity.id + " images collection");
          };
          $scope.getCurrentTypeUri = function() {
            var j, len, ref, type;
            ref = configuration.types;
            for (j = 0, len = ref.length; j < len; j++) {
              type = ref[j];
              if (type.css === ("wl-" + $scope.entity.mainType)) {
                return type.uri;
              }
            }
          };
          $scope.isInternal = function() {
            if ($scope.entity.id.startsWith(configuration.datasetUri)) {
              return true;
            }
            return false;
          };
          $scope.linkTo = function(linkType) {
            return $window.location.href = ajaxurl + '?action=wordlift_redirect&uri=' + $window.encodeURIComponent($scope.entity.id) + "&to=" + linkType;
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
          availableTypes = [];
          ref = configuration.types;
          for (j = 0, len = ref.length; j < len; j++) {
            type = ref[j];
            availableTypes[type.css.replace('wl-', '')] = type.uri;
          }
          $scope.supportedTypes = (function() {
            var k, len1, ref1, results1;
            ref1 = configuration.types;
            results1 = [];
            for (k = 0, len1 = ref1.length; k < len1; k++) {
              type = ref1[k];
              results1.push({
                id: type.css.replace('wl-', ''),
                name: type.uri
              });
            }
            return results1;
          })();
          if ($scope.box) {
            return $scope.supportedTypes = (function() {
              var k, len1, ref1, results1;
              ref1 = $scope.box.registeredTypes;
              results1 = [];
              for (k = 0, len1 = ref1.length; k < len1; k++) {
                type = ref1[k];
                results1.push({
                  id: type,
                  name: availableTypes[type]
                });
              }
              return results1;
            })();
          }
        }
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.directives.wlEntityTile', []).directive('wlEntityTile', [
    'configuration', '$log', function(configuration, $log) {
      return {
        require: '?^wlClassificationBox',
        restrict: 'E',
        scope: {
          entity: '=',
          isSelected: '=',
          showConfidence: '=',
          onEntitySelect: '&'
        },
        template: "<div ng-class=\"'wl-' + entity.mainType\" class=\"entity\">\n        <div class=\"entity-header\">\n    \n          <i ng-click=\"onEntitySelect()\" ng-hide=\"annotation\" ng-class=\"{ 'wl-selected' : isSelected, 'wl-unselected' : !isSelected }\"></i>\n          <i ng-click=\"onEntitySelect()\" class=\"type\"></i>\n          <span class=\"label\" ng-click=\"onEntitySelect()\">{{entity.label}}</span>\n\n          <small ng-show=\"entity.occurrences.length > 0\">({{entity.occurrences.length}})</small>\n          <span ng-show=\"isInternal()\" class=\"dashicons dashicons-tag wl-internal\"></span>  \n          \n          <div class=\"wl-progress-background\" ng-show=\"showConfidence\">\n            <div class=\"wl-progress-current\" style=\"width:{{entity.confidence*100}}%\"></div>\n          </div>\n\n          <i ng-class=\"{ 'wl-more': isOpened == false, 'wl-less': isOpened == true }\" ng-click=\"toggle()\"></i>\n  </div>\n        <div class=\"details\" ng-show=\"isOpened\">\n          <wl-entity-form entity=\"entity\" on-submit=\"toggle()\"></wl-entity-form>\n        </div>\n</div>",
        link: function($scope, $element, $attrs, $boxCtrl) {
          if ($boxCtrl != null) {
            $boxCtrl.addTile($scope);
          }
          $scope.isOpened = false;
          $scope.isInternal = function() {
            if ($scope.entity.id.startsWith(configuration.datasetUri)) {
              return true;
            }
            return false;
          };
          return $scope.toggle = function() {
            if (!$scope.isOpened) {
              if ($boxCtrl != null) {
                $boxCtrl.closeTiles();
              }
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
      template: "        <div>\n\n          <input type='text' name='wl_entities[{{entity.id}}][uri]' value='{{entity.id}}'>\n          <input type='text' name='wl_entities[{{entity.id}}][label]' value='{{entity.label}}'>\n          <textarea name='wl_entities[{{entity.id}}][description]'>{{entity.description}}</textarea>\n          <input type='text' name='wl_entities[{{entity.id}}][main_type]' value='wl-{{entity.mainType}}'>\n\n          <input ng-repeat=\"type in entity.types\" type='text'\n          	name='wl_entities[{{entity.id}}][type][]' value='{{type}}' />\n          <input ng-repeat=\"image in entity.images\" type='text'\n            name='wl_entities[{{entity.id}}][image][]' value='{{image}}' />\n          <input ng-repeat=\"sameAs in entity.sameAs\" type='text'\n            name='wl_entities[{{entity.id}}][sameas][]' value='{{sameAs}}' />\n          \n          <div ng-repeat=\"(property, values) in entity.properties\">\n            <input ng-repeat=\"propertyValue in values\" type='text'\n              name='wl_entities[{{entity.id}}][properties][{{property}}][]' value='{{propertyValue}}' />\n          </div>\n         \n</div>"
    };
  });

  angular.module('wordlift.editpost.widget.services.AnalysisService', []).service('AnalysisService', [
    'configuration', '$log', '$http', '$rootScope', function(configuration, $log, $http, $rootScope) {
      var box, extend, findAnnotation, j, k, len, len1, merge, ref, ref1, service, type, uniqueId;
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
        var annotation, annotationId, annotationRange, isOverlapping, j, k, len, pos, ref, ref1, ref2, results1;
        if (positions == null) {
          positions = [];
        }
        ref = analysis.annotations;
        for (annotationId in ref) {
          annotation = ref[annotationId];
          if (annotation.start > 0 && annotation.end > annotation.start) {
            annotationRange = (function() {
              results1 = [];
              for (var j = ref1 = annotation.start, ref2 = annotation.end; ref1 <= ref2 ? j <= ref2 : j >= ref2; ref1 <= ref2 ? j++ : j--){ results1.push(j); }
              return results1;
            }).apply(this);
            isOverlapping = false;
            for (k = 0, len = annotationRange.length; k < len; k++) {
              pos = annotationRange[k];
              if (indexOf.call(positions, pos) >= 0) {
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
      ref = configuration.classificationBoxes;
      for (j = 0, len = ref.length; j < len; j++) {
        box = ref[j];
        ref1 = box.registeredTypes;
        for (k = 0, len1 = ref1.length; k < len1; k++) {
          type = ref1[k];
          if (indexOf.call(service._supportedTypes, type) < 0) {
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
        var ea, index, l, len2, ref2;
        $log.warn("Going to remove overlapping annotation with id " + annotationId);
        if (analysis.annotations[annotationId] != null) {
          ref2 = analysis.annotations[annotationId].entityMatches;
          for (index = l = 0, len2 = ref2.length; l < len2; index = ++l) {
            ea = ref2[index];
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
        var annotation, annotationId, ea, em, entity, id, index, l, len2, len3, len4, localEntity, local_confidence, m, n, originalTopics, ref10, ref11, ref2, ref3, ref4, ref5, ref6, ref7, ref8, ref9, topic;
        originalTopics = data.topics;
        data.topics = {};
        for (l = 0, len2 = originalTopics.length; l < len2; l++) {
          topic = originalTopics[l];
          topic.id = topic.uri;
          topic.occurrences = [];
          topic.mainType = this._defaultType;
          data.topics[topic.id] = topic;
        }
        ref2 = configuration.entities;
        for (id in ref2) {
          localEntity = ref2[id];
          data.entities[id] = localEntity;
        }
        ref3 = data.entities;
        for (id in ref3) {
          entity = ref3[id];
          if (!entity.label) {
            $log.warn("Label missing for entity " + id);
          }
          if (!entity.description) {
            $log.warn("Description missing for entity " + id);
          }
          if (!entity.sameAs) {
            $log.warn("sameAs missing for entity " + id);
            entity.sameAs = [];
            if ((ref4 = configuration.entities[id]) != null) {
              ref4.sameAs = [];
            }
            $log.debug("Schema.org sameAs overridden for entity " + id);
          }
          if (ref5 = entity.mainType, indexOf.call(this._supportedTypes, ref5) < 0) {
            $log.warn("Schema.org type " + entity.mainType + " for entity " + id + " is not supported from current classification boxes configuration");
            entity.mainType = this._defaultType;
            if ((ref6 = configuration.entities[id]) != null) {
              ref6.mainType = this._defaultType;
            }
            $log.debug("Schema.org type overridden for entity " + id);
          }
          entity.id = id;
          entity.occurrences = [];
          entity.annotations = {};
          entity.confidence = 1;
        }
        ref7 = data.annotations;
        for (id in ref7) {
          annotation = ref7[id];
          annotation.id = id;
          annotation.entities = {};
          data.annotations[id].entityMatches = (function() {
            var len3, m, ref8, results1;
            ref8 = annotation.entityMatches;
            results1 = [];
            for (m = 0, len3 = ref8.length; m < len3; m++) {
              ea = ref8[m];
              if (ea.entityId !== configuration.currentPostUri) {
                results1.push(ea);
              }
            }
            return results1;
          })();
          ref8 = data.annotations[id].entityMatches;
          for (index = m = 0, len3 = ref8.length; m < len3; index = ++m) {
            ea = ref8[index];
            if (!data.entities[ea.entityId].label) {
              data.entities[ea.entityId].label = annotation.text;
              $log.debug("Missing label retrived from related annotation for entity " + ea.entityId);
            }
            data.entities[ea.entityId].annotations[id] = annotation;
            data.annotations[id].entities[ea.entityId] = data.entities[ea.entityId];
          }
        }
        ref9 = data.entities;
        for (id in ref9) {
          entity = ref9[id];
          ref10 = data.annotations;
          for (annotationId in ref10) {
            annotation = ref10[annotationId];
            local_confidence = 1;
            ref11 = annotation.entityMatches;
            for (n = 0, len4 = ref11.length; n < len4; n++) {
              em = ref11[n];
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
        return promise = this._innerPerform(content).then(function(response) {
          var entity, id, ref2, suggestions;
          suggestions = [];
          ref2 = response.data.entities;
          for (id in ref2) {
            entity = ref2[id];
            if (id.startsWith('http')) {
              suggestions.push(id);
            }
          }
          return $rootScope.$broadcast("sameAsRetrieved", suggestions);
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
          service._updateStatus(false);
          return;
        }
        service._updateStatus(true);
        promise = this._innerPerform(content);
        promise.then(function(response) {
          service._currentAnalysis = response.data;
          return $rootScope.$broadcast("analysisPerformed", service.parse(response.data));
        });
        promise["catch"](function(response) {
          $log.error(response.data);
          return $rootScope.$broadcast("analysisFailed", response.data);
        });
        return promise["finally"](function(response) {
          return service._updateStatus(false);
        });
      };
      service.preselect = function(analysis, annotations) {
        var annotation, e, entity, id, l, len2, ref2, ref3, results1, textAnnotation;
        $log.debug("Going to perform annotations preselection");
        results1 = [];
        for (l = 0, len2 = annotations.length; l < len2; l++) {
          annotation = annotations[l];
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
          ref2 = configuration.entities;
          for (id in ref2) {
            e = ref2[id];
            if (ref3 = annotation.uri, indexOf.call(e.sameAs, ref3) >= 0) {
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
            results1.push(analysis.annotations[textAnnotation.id].entities[entity.id] = analysis.entities[entity.id]);
          } else {
            results1.push(void 0);
          }
        }
        return results1;
      };
      return service;
    }
  ]);

  angular.module('wordlift.editpost.widget.services.EditorService', ['wordlift.editpost.widget.services.AnalysisService']).service('EditorService', [
    'configuration', 'AnalysisService', '$log', '$http', '$rootScope', function(configuration, AnalysisService, $log, $http, $rootScope) {
      var INVISIBLE_CHAR, currentOccurencesForEntity, dedisambiguate, disambiguate, editor, findEntities, findPositions, service;
      INVISIBLE_CHAR = '\uFEFF';
      findEntities = function(html) {
        var annotation, match, pattern, results1, traslator;
        traslator = Traslator.create(html);
        pattern = /<(\w+)[^>]*\sitemid="([^"]+)"[^>]*>([^<]*)<\/\1>/gim;
        results1 = [];
        while (match = pattern.exec(html)) {
          annotation = {
            start: traslator.html2text(match.index),
            end: traslator.html2text(match.index + match[0].length),
            uri: match[2],
            label: match[3]
          };
          results1.push(annotation);
        }
        return results1;
      };
      findPositions = function(entities) {
        var entityAnnotation, j, k, len, positions, ref, ref1, results1;
        positions = [];
        for (j = 0, len = entities.length; j < len; j++) {
          entityAnnotation = entities[j];
          positions = positions.concat((function() {
            results1 = [];
            for (var k = ref = entityAnnotation.start, ref1 = entityAnnotation.end; ref <= ref1 ? k <= ref1 : k >= ref1; ref <= ref1 ? k++ : k--){ results1.push(k); }
            return results1;
          }).apply(this));
        }
        return positions;
      };
      editor = function() {
        return tinyMCE.get('content');
      };
      disambiguate = function(annotation, entity) {
        var discardedItemId, ed, j, len, ref, type;
        ed = editor();
        ed.dom.addClass(annotation.id, "disambiguated");
        ref = configuration.types;
        for (j = 0, len = ref.length; j < len; j++) {
          type = ref[j];
          ed.dom.removeClass(annotation.id, type.css);
        }
        ed.dom.removeClass(annotation.id, "unlinked");
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
        var annotation, annotations, ed, itemId, j, len, occurrences;
        ed = editor();
        occurrences = [];
        if (entityId === "") {
          return occurrences;
        }
        annotations = ed.dom.select("span.textannotation");
        for (j = 0, len = annotations.length; j < len; j++) {
          annotation = annotations[j];
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
      $rootScope.$on("entitySelected", function(event, entity, annotationId) {
        var annotation, discarded, entityId, id, j, len, occurrences, ref;
        discarded = [];
        if (annotationId != null) {
          discarded.push(disambiguate(entity.annotations[annotationId], entity));
        } else {
          ref = entity.annotations;
          for (id in ref) {
            annotation = ref[id];
            discarded.push(disambiguate(annotation, entity));
          }
        }
        for (j = 0, len = discarded.length; j < len; j++) {
          entityId = discarded[j];
          if (entityId) {
            occurrences = currentOccurencesForEntity(entityId);
            $rootScope.$broadcast("updateOccurencesForEntity", entityId, occurrences);
          }
        }
        occurrences = currentOccurencesForEntity(entity.id);
        return $rootScope.$broadcast("updateOccurencesForEntity", entity.id, occurrences);
      });
      $rootScope.$on("entityDeselected", function(event, entity, annotationId) {
        var annotation, discarded, entityId, id, j, len, occurrences, ref;
        discarded = [];
        if (annotationId != null) {
          dedisambiguate(entity.annotations[annotationId], entity);
        } else {
          ref = entity.annotations;
          for (id in ref) {
            annotation = ref[id];
            dedisambiguate(annotation, entity);
          }
        }
        for (j = 0, len = discarded.length; j < len; j++) {
          entityId = discarded[j];
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
        isEditor: function(editor) {
          var ed;
          ed = editor();
          return ed.id === editor.id;
        },
        updateContentEditableStatus: function(status) {
          var ed;
          ed = editor();
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
          textAnnotationSpan = "<span id=\"" + textAnnotation.id + "\" class=\"textannotation unlinked selected\">" + (ed.selection.getContent()) + "</span>" + INVISIBLE_CHAR;
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
          var annotation, ed, j, len, ref;
          ed = editor();
          ref = ed.dom.select("span.textannotation");
          for (j = 0, len = ref.length; j < len; j++) {
            annotation = ref[j];
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
            var annotation, annotationId, ed, element, em, entities, entity, html, isDirty, j, len, ref, ref1, traslator;
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
            ref = analysis.annotations;
            for (annotationId in ref) {
              annotation = ref[annotationId];
              if (annotation.entityMatches.length === 0) {
                $log.warn("Annotation " + annotation.text + " [" + annotation.start + ":" + annotation.end + "] with id " + annotation.id + " has no entity matches!");
                continue;
              }
              element = "<span id=\"" + annotationId + "\" class=\"textannotation";
              ref1 = annotation.entityMatches;
              for (j = 0, len = ref1.length; j < len; j++) {
                em = ref1[j];
                entity = analysis.entities[em.entityId];
                if (indexOf.call(entity.occurrences, annotationId) >= 0) {
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
            html = traslator.getHtml();
            html = html.replace(/<\/span>/gim, "</span>" + INVISIBLE_CHAR);
            $rootScope.$broadcast("analysisEmbedded");
            isDirty = ed.isDirty();
            ed.setContent(html, {
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

  angular.module('wordlift.editpost.widget.services.GeoLocationService', ['geolocation']).service('GeoLocationService', [
    'geolocation', '$log', '$rootScope', '$document', '$q', '$timeout', function(geolocation, $log, $rootScope, $document, $q, $timeout) {
      var GOOGLE_MAPS_API_ENDPOINT, GOOGLE_MAPS_LEVEL, loadGoogleAPI, service;
      GOOGLE_MAPS_API_ENDPOINT = 'https://maps.googleapis.com/maps/api/js';
      GOOGLE_MAPS_LEVEL = 'locality';
      this.googleApiLoaded = false;
      this.googleApiPromise = void 0;
      loadGoogleAPI = function() {
        var callback, deferred, element;
        if (this.googleApiPromise != null) {
          return this.googleApiPromise;
        }
        deferred = $q.defer();
        element = $document[0].createElement('script');
        element.src = GOOGLE_MAPS_API_ENDPOINT;
        $document[0].body.appendChild(element);
        callback = function(e) {
          var ref;
          if (element.readyState && ((ref = element.readyState) !== 'complete' && ref !== 'loaded')) {
            return;
          }
          return $timeout(function() {
            return deferred.resolve(e);
          });
        };
        element.onload = callback;
        element.onreadystatechange = callback;
        element.onerror = function(e) {
          return $timeout(function() {
            return deferred.reject(e);
          });
        };
        this.googleApiPromise = deferred.promise;
        return this.googleApiPromise;
      };
      service = {};
      service.getLocation = function() {
        return geolocation.getLocation().then(function(data) {
          $log.debug("Detected position: latitude " + data.coords.latitude + ", longitude " + data.coords.longitude);
          return loadGoogleAPI().then(function() {
            var geocoder;
            geocoder = new google.maps.Geocoder();
            return geocoder.geocode({
              'location': {
                'lat': data.coords.latitude,
                'lng': data.coords.longitude
              }
            }, function(results, status) {
              var j, len, result;
              if (status === google.maps.GeocoderStatus.OK) {
                for (j = 0, len = results.length; j < len; j++) {
                  result = results[j];
                  if (indexOf.call(result.types, GOOGLE_MAPS_LEVEL) >= 0) {
                    $rootScope.$broadcast("currentUserLocalityDetected", result.formatted_address);
                    return;
                  }
                }
              }
            });
          });
        });
      };
      return service;
    }
  ]);

  angular.module('wordlift.editpost.widget.providers.ConfigurationProvider', []).provider("configuration", function() {
    var _configuration, provider;
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

  $(container = $("<div id=\"wordlift-edit-post-wrapper\" ng-controller=\"EditPostWidgetController\">\n	<div class=\"wl-error\" ng-repeat=\"item in errors\">\n        <span class=\"wl-msg\">{{ item.msg }}</span>\n      </div>\n\n      <h3 class=\"wl-widget-headline\">\n        <span>Content classification</span>\n        <span ng-show=\"isRunning\" class=\"wl-spinner\"></span>\n      </h3>\n    \n      <div ng-show=\"annotation\">\n        <h4 class=\"wl-annotation-label\">\n          <i class=\"wl-annotation-label-icon\"></i>\n          {{ analysis.annotations[ annotation ].text }} \n          <small>[ {{ analysis.annotations[ annotation ].start }}, {{ analysis.annotations[ annotation ].end }} ]</small>\n          <i class=\"wl-annotation-label-remove-icon\" ng-click=\"selectAnnotation(undefined)\"></i>\n        </h4>\n      </div>\n\n      <wl-classification-box ng-repeat=\"box in configuration.classificationBoxes\">\n        <div ng-hide=\"annotation\" class=\"wl-without-annotation\">\n          <wl-entity-tile show-confidence=\"false\" is-selected=\"isEntitySelected(entity, box)\" on-entity-select=\"onSelectedEntityTile(entity, box)\" entity=\"entity\" ng-repeat=\"entity in analysis.entities | filterEntitiesByTypesAndRelevance:box.registeredTypes\"></wl-entity>\n        </div>  \n        <div ng-show=\"annotation\" class=\"wl-with-annotation\">\n          <wl-entity-tile show-confidence=\"false\" is-selected=\"isLinkedToCurrentAnnotation(entity)\" on-entity-select=\"onSelectedEntityTile(entity, box)\" entity=\"entity\" ng-repeat=\"entity in analysis.annotations[annotation].entities | filterEntitiesByTypes:box.registeredTypes\"\" ></wl-entity>\n        </div>  \n      </wl-classification-box>\n\n      <h3 class=\"wl-widget-headline\">\n        <span>Article metadata</span>\n      </h3>\n\n      <h5 class=\"wl-widget-sub-headline\">What <small>Topic</small></h5>\n      <div class=\"wl-without-annotation\">\n        <wl-entity-tile show-confidence=\"true\" is-selected=\"isTopic(topic)\" on-entity-select=\"onTopicSelected(topic)\" entity=\"topic\" ng-repeat=\"topic in analysis.topics | orderBy :'-confidence'\"></wl-entity-tile>\n      </div>\n\n      <h5 class=\"wl-widget-sub-headline\">Who <small>Author</small></h5>\n      <div class=\"wl-widget-wrapper\">\n        <i class=\"wl-toggle-on wl-disabled\" />\n        <span class=\"entity wl-person\"><i class=\"type\" />\n          {{configuration.currentUser}}\n        </span>\n      </div>  \n\n      <h5 class=\"wl-widget-sub-headline\">Where <small>Publishing Place</small></h5>\n      <i class=\"wl-location-arrow\" ng-click=\"getLocation()\"></i>\n      <div class=\"wl-without-annotation\">\n        <wl-entity-tile show-confidence=\"false\" is-selected=\"isPublishedPlace(entity)\" on-entity-select=\"onPublishedPlaceSelected(entity)\" entity=\"entity\" ng-repeat=\"entity in suggestedPlaces\"></wl-entity-tile>\n      </div>\n\n      <h5 class=\"wl-widget-sub-headline\">When <small>Publishing Date</small></h5>\n      <div class=\"wl-widget-wrapper\">\n        <i class=\"wl-toggle-on wl-disabled\" />\n        <span class=\"entity wl-event\"><i class=\"type\" />\n          {{configuration.publishedDate}}\n        </span>\n      </div>\n\n      <h3 class=\"wl-widget-headline\"><span>Suggested images</span></h3>\n      <div wl-carousel>\n        <div ng-repeat=\"(image, label) in images\" class=\"wl-card\" wl-carousel-pane>\n          <div class=\"wl-card-image\"> \n            <img ng-src=\"{{image}}\" wl-fallback=\"{{configuration.defaultThumbnailPath}}\" />\n          </div>\n        </div>\n      </div>\n\n      <h3 class=\"wl-widget-headline\"><span>Related posts</span></h3>\n      <div wl-carousel>\n        <div ng-repeat=\"post in relatedPosts\" class=\"wl-card\" wl-carousel-pane>\n          <div class=\"wl-card-image\"> \n            <img ng-src=\"{{post.thumbnail}}\" wl-fallback=\"{{configuration.defaultThumbnailPath}}\" />\n          </div>\n          <div class=\"wl-card-title\">\n            <a ng-href=\"{{post.link}}\">{{post.post_title}}</a>\n          </div>\n        </div>\n      </div>\n      \n      <div class=\"wl-entity-input-boxes\">\n        <wl-entity-input-box entity=\"entity\" ng-repeat=\"entity in analysis.entities | isEntitySelected\"></wl-entity-input-box>\n        <wl-entity-input-box entity=\"topic\" ng-if=\"topic\"></wl-entity-input-box>\n        <wl-entity-input-box entity=\"publishedPlace\" ng-if=\"publishedPlace\"></wl-entity-input-box>\n        <div ng-repeat=\"(box, entities) in selectedEntities\">\n          <input type='text' name='wl_boxes[{{box}}][]' value='{{id}}' ng-repeat=\"(id, entity) in entities\">\n        </div> \n      </div>   \n    </div>").appendTo('#wordlift-edit-post-outer-wrapper'), injector = angular.bootstrap($('#wordlift-edit-post-wrapper'), ['wordlift.editpost.widget']), tinymce.PluginManager.add('wordlift', function(editor, url) {
    var fireEvent;
    if (editor.id !== "content") {
      return;
    }
    fireEvent = function(editor, eventName, callback) {
      switch (tinymce.majorVersion) {
        case '4':
          return editor.on(eventName, callback);
        case '3':
          return editor["on" + eventName].add(callback);
      }
    };
    injector.invoke([
      'EditorService', '$rootScope', '$log', function(EditorService, $rootScope, $log) {
        var j, len, method, originalMethod, ref, results1;
        ref = ['setMarkers', 'toViews'];
        results1 = [];
        for (j = 0, len = ref.length; j < len; j++) {
          method = ref[j];
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
            $rootScope.$on("analysisFailed", function(event) {
              $log.info("Going to restore wp.mce.views method " + method + "()");
              return wp.mce.views[method] = originalMethod;
            });
            break;
          } else {
            results1.push(void 0);
          }
        }
        return results1;
      }
    ]);
    fireEvent(editor, "LoadContent", function(e) {
      return injector.invoke([
        'AnalysisService', 'EditorService', '$rootScope', '$log', function(AnalysisService, EditorService, $rootScope, $log) {
          return $rootScope.$apply(function() {
            var html, text;
            html = editor.getContent({
              format: 'raw'
            });
            text = Traslator.create(html).getText();
            if (text.match(/[a-zA-Z0-9]+/)) {
              EditorService.updateContentEditableStatus(false);
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
          if (AnalysisService._currentAnalysis) {
            $rootScope.$apply(function() {
              return $rootScope.selectionStatus = EditorService.hasSelection();
            });
          }
          return true;
        }
      ]);
    });
    return fireEvent(editor, "Click", function(e) {
      return injector.invoke([
        'AnalysisService', 'EditorService', '$rootScope', '$log', function(AnalysisService, EditorService, $rootScope, $log) {
          if (AnalysisService._currentAnalysis) {
            $rootScope.$apply(function() {
              return EditorService.selectAnnotation(e.target.id);
            });
          }
          return true;
        }
      ]);
    });
  }));

}).call(this);

//# sourceMappingURL=wordlift-reloaded.js.map
