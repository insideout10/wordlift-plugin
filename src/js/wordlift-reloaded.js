(function() {
  var $, Traslator, container, injector, spinner,
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

    Traslator.version = '1.0.0';

    function Traslator(html) {
      this._html = html;
    }

    Traslator.prototype.parse = function() {
      var htmlElem, htmlLength, htmlPost, htmlPre, htmlProcessed, match, pattern, ref, textLength, textPost, textPre;
      this._htmlPositions = [];
      this._textPositions = [];
      this._text = '';
      pattern = /([^&<>]*)(&[^&;]*;|<[!\/]?(?:[\w-]+|\[cdata\[.*?]])(?: [\w_-]+(?:="[^"]*")?)*>)([^&<>]*)/gim;
      textLength = 0;
      htmlLength = 0;
      while ((match = pattern.exec(this._html)) != null) {
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
      if ('' === this._text && !pattern.match(this._html)) {
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
  ]).directive('wlHideAfter', [
    '$timeout', '$log', function($timeout, $log) {
      return {
        restrict: 'A',
        link: function($scope, $element, $attrs, $ctrl) {
          var delay;
          delay = +$attrs.wlHideAfter;
          return $timeout(function() {
            $log.debug("Remove msg after " + delay + " ms");
            return $element.hide();
          }, delay);
        }
      };
    }
  ]).directive('wlClipboard', [
    '$timeout', '$document', '$log', function($timeout, $document, $log) {
      return {
        restrict: 'E',
        scope: {
          text: '=',
          onCopied: '&'
        },
        transclude: true,
        template: "<span \n  class=\"wl-widget-post-link\" \n  ng-class=\"{'wl-widget-post-link-copied' : $copied}\"\n  ng-click=\"copyToClipboard()\">\n  <ng-transclude></ng-transclude>\n  <input type=\"text\" ng-value=\"text\" />\n</span>",
        link: function($scope, $element, $attrs, $ctrl) {
          $scope.$copied = false;
          $scope.node = $element.find('input');
          $scope.node.css('position', 'absolute');
          $scope.node.css('left', '-10000px');
          return $scope.copyToClipboard = function() {
            var selection;
            try {
              $document[0].body.style.webkitUserSelect = 'initial';
              selection = $document[0].getSelection();
              selection.removeAllRanges();
              $scope.node.select();
              if (!$document[0].execCommand('copy')) {
                $log.warn("Error on clipboard copy for " + text);
              }
              selection.removeAllRanges();
              $scope.$copied = true;
              $timeout(function() {
                $log.debug("Going to reset $copied status");
                return $scope.$copied = false;
              }, 3000);
              if (angular.isFunction($scope.onCopied)) {
                return $scope.$evalAsync($scope.onCopied());
              }
            } finally {
              $document[0].body.style.webkitUserSelect = '';
            }
          };
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
        template: "<div class=\"wl-carousel\" ng-class=\"{ 'active' : areControlsVisible }\" ng-show=\"panes.length > 0\" ng-mouseover=\"showControls()\" ng-mouseleave=\"hideControls()\">\n  <div class=\"wl-panes\" ng-style=\"{ width: panesWidth, left: position }\" ng-transclude ng-swipe-left=\"next()\" ng-swipe-right=\"prev()\" ></div>\n  <div class=\"wl-carousel-arrows\" ng-show=\"areControlsVisible\" ng-class=\"{ 'active' : isActive() }\">\n    <i class=\"wl-angle left\" ng-click=\"prev()\" ng-show=\"isPrevArrowVisible()\" />\n    <i class=\"wl-angle right\" ng-click=\"next()\" ng-show=\"isNextArrowVisible()\" />\n  </div>\n</div>",
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
            $scope.isActive = function() {
              return $scope.isPrevArrowVisible() || $scope.isNextArrowVisible();
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
            ctrl.registerPane = function(scope, element, first) {
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
        scope: {
          wlFirstPane: '='
        },
        transclude: true,
        template: "<div ng-transclude></div>",
        link: function($scope, $element, $attrs, $ctrl) {
          $element.addClass("wl-carousel-item");
          $scope.isFirst = $scope.wlFirstPane || false;
          $scope.setWidth = function(size) {
            return $element.css('width', size + "px");
          };
          $scope.$on('$destroy', function() {
            $log.debug("Destroy " + $scope.$id);
            return $ctrl.unregisterPane($scope);
          });
          return $ctrl.registerPane($scope, $element, $scope.isFirst);
        }
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.controllers.EditPostWidgetController', ['wordlift.editpost.widget.services.AnalysisService', 'wordlift.editpost.widget.services.EditorService', 'wordlift.editpost.widget.services.GeoLocationService', 'wordlift.editpost.widget.providers.ConfigurationProvider']).filter('filterEntitiesByTypesAndRelevance', [
    'configuration', '$log', function(configuration, $log) {
      return function(items, types) {
        var entity, filtered, id, ref, treshold;
        filtered = [];
        if (items == null) {
          return filtered;
        }
        treshold = Math.floor(((1 / 120) * Object.keys(items).length) + 0.75);
        for (id in items) {
          entity = items[id];
          if (ref = entity.mainType, indexOf.call(types, ref) >= 0) {
            filtered.push(entity);
          }
        }
        return filtered;
      };
    }
  ]).filter('filterTruncate', [
    '$log', function($log) {
      return function(input, words) {
        var inputWords;
        if (isNaN(words)) {
          return input;
        }
        if (words <= 0) {
          return '';
        }
        if (input) {
          inputWords = input.split(/\s+/);
          if (inputWords.length > words) {
            input = inputWords.slice(0, words).join(' ') + '…';
          }
        }
        return input;
      };
    }
  ]).filter('filterSplitInRows', [
    '$log', function($log) {
      return function(arrayLength) {
        var arr, j, ref, results1;
        if (arrayLength) {
          arrayLength = Math.ceil(arrayLength);
          arr = (function() {
            results1 = [];
            for (var j = 0, ref = arrayLength - 1; 0 <= ref ? j <= ref : j >= ref; 0 <= ref ? j++ : j--){ results1.push(j); }
            return results1;
          }).apply(this);
          return arr;
        }
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
    'GeoLocationService', 'RelatedPostDataRetrieverService', 'EditorService', 'AnalysisService', 'configuration', '$log', '$scope', '$rootScope', function(GeoLocationService, RelatedPostDataRetrieverService, EditorService, AnalysisService, configuration, $log, $scope, $rootScope) {
      var box, j, len, ref;
      $scope.isRunning = false;
      $scope.isGeolocationRunning = false;
      $scope.analysis = void 0;
      $scope.relatedPosts = void 0;
      $scope.currentEntity = void 0;
      $scope.currentEntityType = void 0;
      $scope.setCurrentEntity = function(entity, entityType) {
        var annotation;
        $scope.currentEntity = entity;
        $scope.currentEntityType = entityType;
        switch (entityType) {
          case 'entity':
            return $log.debug("An existing entity. Nothing to do");
          default:
            $log.debug("A new entity");
            $scope.currentEntity = AnalysisService.createEntity();
            if (!$scope.isThereASelection && ($scope.annotation == null)) {
              $scope.addMsg('Select a text or an existing annotation in order to create a new entity. Text selections are valid only if they do not overlap other existing annotation', 'error');
              $scope.unsetCurrentEntity();
              return;
            }
            if ($scope.annotation != null) {
              annotation = $scope.analysis.annotations[$scope.annotation];
              $scope.currentEntity.label = annotation.text;
              return;
            }
            return EditorService.createTextAnnotationFromCurrentSelection();
        }
      };
      $scope.unsetCurrentEntity = function() {
        $scope.currentEntity = void 0;
        return $scope.currentEntityType = void 0;
      };
      $scope.storeCurrentEntity = function() {
        if (!$scope.currentEntity.mainType) {
          $scope.addMsg('Please do not forgive to specify a type for this entity!', 'error');
          return;
        }
        switch ($scope.currentEntityType) {
          case 'entity':
            $scope.analysis.entities[$scope.currentEntity.id] = $scope.currentEntity;
            $scope.addMsg('The entity was updated!', 'positive');
            break;
          default:
            $log.debug('Unset a new entity');
            $scope.addNewEntityToAnalysis();
            $scope.addMsg('The entity was created!', 'positive');
        }
        $scope.unsetCurrentEntity();
        return wp.wordlift.trigger('analysis.result', $scope.analysis);
      };
      $scope.selectedEntities = {};
      $scope.currentSection = void 0;
      $scope.toggleCurrentSection = function(section) {
        if ($scope.currentSection === section) {
          return $scope.currentSection = void 0;
        } else {
          return $scope.currentSection = section;
        }
      };
      $scope.isCurrentSection = function(section) {
        return $scope.currentSection === section;
      };
      $scope.suggestedPlaces = void 0;
      $scope.publishedPlace = configuration.publishedPlace;
      $scope.topic = void 0;
      if (configuration.publishedPlace != null) {
        $scope.suggestedPlaces = {};
        $scope.suggestedPlaces[configuration.publishedPlace.id] = configuration.publishedPlace;
      }
      $scope.annotation = void 0;
      $scope.boxes = [];
      $scope.images = [];
      $scope.isThereASelection = false;
      $scope.configuration = configuration;
      $scope.messages = [];
      RelatedPostDataRetrieverService.load(Object.keys($scope.configuration.entities));
      $rootScope.$on("analysisFailed", function(event, errorMsg) {
        return $scope.addMsg(errorMsg, 'error');
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
      $scope.removeMsg = function(index) {
        return $scope.messages.splice(index, 1);
      };
      $scope.addMsg = function(msg, level) {
        return $scope.messages.unshift({
          level: level,
          text: msg
        });
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
      $scope.addNewEntityToAnalysis = function() {
        var annotation;
        delete $scope.currentEntity.suggestedSameAs;
        $scope.analysis.entities[$scope.currentEntity.id] = $scope.currentEntity;
        annotation = $scope.analysis.annotations[$scope.annotation];
        annotation.entityMatches.push({
          entityId: $scope.currentEntity.id,
          confidence: 1
        });
        $scope.analysis.entities[$scope.currentEntity.id].annotations[annotation.id] = annotation;
        $scope.analysis.annotations[$scope.annotation].entities[$scope.currentEntity.id] = $scope.currentEntity;
        return $scope.onSelectedEntityTile($scope.analysis.entities[$scope.currentEntity.id]);
      };
      $scope.$on("updateOccurencesForEntity", function(event, entityId, occurrences) {
        var entities, ref1, results1;
        $scope.analysis.entities[entityId].occurrences = occurrences;
        wp.wordlift.trigger('updateOccurrencesForEntity', {
          entityId: entityId,
          occurrences: occurrences
        });
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
        if ($scope.currentEntity != null) {
          annotation = $scope.analysis.annotations[newAnnotationId];
          $scope.currentEntity.label = annotation.text;
          return AnalysisService.getSuggestedSameAs(annotation.text);
        }
      });
      $scope.$on("textAnnotationClicked", function(event, annotationId) {
        $scope.annotation = annotationId;
        return $scope.unsetCurrentEntity();
      });
      $scope.$on("textAnnotationAdded", function(event, annotation) {
        $log.debug("added a new annotation with Id " + annotation.id);
        $scope.analysis.annotations[annotation.id] = annotation;
        return $scope.annotation = annotation.id;
      });
      $scope.$on("sameAsRetrieved", function(event, sameAs) {
        return $scope.currentEntity.suggestedSameAs = sameAs;
      });
      $scope.$on("relatedPostsLoaded", function(event, posts) {
        return $scope.relatedPosts = posts;
      });
      $scope.$on("analysisPerformed", function(event, analysis) {
        var entity, entityId, image, k, l, len1, len2, len3, len4, m, n, ref1, ref2, ref3, ref4, ref5, topic;
        $scope.analysis = analysis;
        if ($scope.configuration.topic != null) {
          ref1 = analysis.topics;
          for (k = 0, len1 = ref1.length; k < len1; k++) {
            topic = ref1[k];
            if (ref2 = topic.id, indexOf.call($scope.configuration.topic.sameAs, ref2) >= 0) {
              $scope.topic = topic;
            }
          }
        }
        ref3 = $scope.configuration.classificationBoxes;
        for (l = 0, len2 = ref3.length; l < len2; l++) {
          box = ref3[l];
          ref4 = box.selectedEntities;
          for (m = 0, len3 = ref4.length; m < len3; m++) {
            entityId = ref4[m];
            if (entity = analysis.entities[entityId]) {
              if (entity.occurrences.length === 0) {
                $log.warn("Entity " + entityId + " selected as " + box.label + " without valid occurences!");
                continue;
              }
              $scope.selectedEntities[box.id][entityId] = analysis.entities[entityId];
              ref5 = entity.images;
              for (n = 0, len4 = ref5.length; n < len4; n++) {
                image = ref5[n];
                if (indexOf.call($scope.images, image) < 0) {
                  $scope.images.push(image);
                }
              }
            } else {
              $log.warn("Entity with id " + entityId + " should be linked to " + box.id + " but is missing");
            }
          }
        }
        return $scope.currentSection = 'content-classification';
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
      $scope.onSelectedEntityTile = function(entity) {
        var action, image, k, len1, ref1, ref2, scopeId;
        action = 'entitySelected';
        if ($scope.annotation != null) {
          if (ref1 = $scope.annotation, indexOf.call(entity.occurrences, ref1) >= 0) {
            action = 'entityDeselected';
          }
        } else {
          if (entity.occurrences.length > 0) {
            action = 'entityDeselected';
          }
        }
        scopeId = configuration.getCategoryForType(entity.mainType);
        $log.debug("Action '" + action + "' on entity " + entity.id + " within " + scopeId + " scope");
        if (action === 'entitySelected') {
          $scope.selectedEntities[scopeId][entity.id] = entity;
          ref2 = entity.images;
          for (k = 0, len1 = ref2.length; k < len1; k++) {
            image = ref2[k];
            if (indexOf.call($scope.images, image) < 0) {
              $scope.images.push(image);
            }
          }
        } else {
          $scope.images = $scope.images.filter(function(img) {
            return indexOf.call(entity.images, img) < 0;
          });
        }
        $scope.$emit(action, entity, $scope.annotation);
        wp.wordlift.trigger(action, {
          entity: entity,
          annotation: $scope.annotation
        });
        $scope.updateRelatedPosts();
        return $scope.selectAnnotation(void 0);
      };
      $scope.isGeoLocationAllowed = function() {
        return GeoLocationService.isAllowed();
      };
      $scope.getLocation = function() {
        $scope.isGeolocationRunning = true;
        $rootScope.$broadcast('geoLocationStatusUpdated', $scope.isGeolocationRunning);
        return GeoLocationService.getLocation();
      };
      $scope.isPublishedPlace = function(entity) {
        var ref1;
        return entity.id === ((ref1 = $scope.publishedPlace) != null ? ref1.id : void 0);
      };
      $scope.hasPublishedPlace = function() {
        return ($scope.publishedPlace != null) || ($scope.suggestedPlaces != null);
      };
      $scope.onPublishedPlaceSelected = function(entity) {
        var ref1;
        if (((ref1 = $scope.publishedPlace) != null ? ref1.id : void 0) === entity.id) {
          $scope.publishedPlace = void 0;
          $scope.suggestedPlaces = void 0;
          return;
        }
        return $scope.publishedPlace = entity;
      };
      $scope.$on("currentUserLocalityDetected", function(event, match, locality) {
        $log.debug("Looking for entities matching " + match + " for locality " + locality);
        return AnalysisService._innerPerform(match).then(function(response) {
          var entity, id, ref1;
          $scope.suggestedPlaces = {};
          ref1 = response.data.entities;
          for (id in ref1) {
            entity = ref1[id];
            if ('place' === entity.mainType && locality === entity.label) {
              entity.id = id;
              $scope.onPublishedPlaceSelected(entity);
            }
          }
          $scope.isGeolocationRunning = false;
          return $rootScope.$broadcast('geoLocationStatusUpdated', $scope.isGeolocationRunning);
        });
      });
      $scope.$on("geoLocationError", function(event, msg) {
        $scope.addMsg("Sorry. Looks like something went wrong and WordLift cannot detect your current position. Make sure the ​location services​ of your browser are turned on.", 'error');
        $scope.isGeolocationRunning = false;
        return $rootScope.$broadcast('geoLocationStatusUpdated', $scope.isGeolocationRunning);
      });
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
    'configuration', '$log', function(configuration, $log) {
      return {
        restrict: 'E',
        scope: true,
        transclude: true,
        templateUrl: function() {
          return configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-classification-box.html';
        },
        link: function($scope, $element, $attrs, $ctrl) {
          $scope.hasSelectedEntities = function() {
            return Object.keys($scope.selectedEntities[$scope.box.id]).length > 0;
          };
          return wp.wordlift.trigger('wlClassificationBox.loaded', $scope);
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

  angular.module('wordlift.editpost.widget.directives.wlEntityList', []).directive('wlEntityList', [
    '$log', function($log) {
      return {
        restrict: 'A',
        link: function() {
          return wp.wordlift.trigger('wlEntityList.loaded');
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
          onReset: '&',
          box: '='
        },
        templateUrl: function() {
          return configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-entity-form.html';
        },
        link: function($scope, $element, $attrs, $ctrl) {
          $scope.configuration = configuration;
          $scope.currentCategory = void 0;
          $scope.$watch('entity.id', function(entityId) {
            var category, ref;
            if (entityId != null) {
              $log.debug("Entity updated to " + entityId);
              category = configuration.getCategoryForType((ref = $scope.entity) != null ? ref.mainType : void 0);
              $log.debug("Going to update current category to " + category);
              return $scope.currentCategory = category;
            }
          });
          $scope.onSubmitWrapper = function(e) {
            e.preventDefault();
            return $scope.onSubmit();
          };
          $scope.onResetWrapper = function(e) {
            e.preventDefault();
            return $scope.onReset();
          };
          $scope.setCurrentCategory = function(categoryId) {
            var types;
            $scope.currentCategory = categoryId;
            types = configuration.getTypesForCategoryId(categoryId);
            $log.debug("Going to check types");
            $log.debug(types);
            if (types.length === 1) {
              return $scope.setType(types[0]);
            }
          };
          $scope.unsetCurrentCategory = function() {
            var ref;
            $scope.currentCategory = void 0;
            return (ref = $scope.entity) != null ? ref.mainType = void 0 : void 0;
          };
          $scope.isSameAsOf = function(sameAs) {
            var ref;
            return ref = sameAs.id, indexOf.call($scope.entity.sameAs, ref) >= 0;
          };
          $scope.addSameAs = function(sameAs) {
            var index, ref, ref1, ref2, ref3;
            if (!((ref = $scope.entity) != null ? ref.sameAs : void 0)) {
              if ((ref1 = $scope.entity) != null) {
                ref1.sameAs = [];
              }
            }
            if (ref2 = sameAs.id, indexOf.call($scope.entity.sameAs, ref2) >= 0) {
              index = $scope.entity.sameAs.indexOf(sameAs.id);
              return $scope.entity.sameAs.splice(index, 1);
            } else {
              return (ref3 = $scope.entity) != null ? ref3.sameAs.push(sameAs.id) : void 0;
            }
          };
          $scope.setType = function(entityType) {
            var ref, ref1;
            if (entityType === ((ref = $scope.entity) != null ? ref.mainType : void 0)) {
              return;
            }
            return (ref1 = $scope.entity) != null ? ref1.mainType = entityType : void 0;
          };
          $scope.isCurrentType = function(entityType) {
            var ref;
            return ((ref = $scope.entity) != null ? ref.mainType : void 0) === entityType;
          };
          $scope.getAvailableTypes = function() {
            return configuration.getTypesForCategoryId($scope.currentCategory);
          };
          $scope.removeCurrentImage = function(index) {
            var removed;
            removed = $scope.entity.images.splice(index, 1);
            return $log.warn("Removed " + removed + " from entity " + $scope.entity.id + " images collection");
          };
          $scope.linkToEdit = function(e) {
            e.preventDefault();
            return $window.location.href = ajaxurl + '?action=wordlift_redirect&uri=' + $window.encodeURIComponent($scope.entity.id) + "&to=edit";
          };
          $scope.hasOccurences = function() {
            var ref;
            return ((ref = $scope.entity.occurrences) != null ? ref.length : void 0) > 0;
          };
          $scope.setSameAs = function(uri) {
            return $scope.entity.sameAs = uri;
          };
          $scope.isInternal = function() {
            var ref;
            return configuration.isInternal((ref = $scope.entity) != null ? ref.id : void 0);
          };
          return $scope.isNew = function(uri) {
            var ref;
            return !/^(f|ht)tps?:\/\//i.test((ref = $scope.entity) != null ? ref.id : void 0);
          };
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
          onSelect: '&',
          onMore: '&'
        },
        templateUrl: function() {
          return configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-entity-tile.html';
        },
        link: function($scope, $element, $attrs, $boxCtrl) {
          $scope.configuration = configuration;
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

  angular.module('wordlift.editpost.widget.directives.wlEntityInputBox', []).directive('wlEntityInputBox', [
    'configuration', '$log', function(configuration, $log) {
      return {
        restrict: 'E',
        scope: {
          entity: '='
        },
        templateUrl: function() {
          return configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-entity-input-box.html';
        }
      };
    }
  ]);

  angular.module('wordlift.editpost.widget.services.EditorAdapter', ['wordlift.editpost.widget.services.EditorAdapter']).service('EditorAdapter', [
    '$log', function($log) {
      var service;
      service = {
        getEditor: function(id) {
          if (id == null) {
            id = 'content';
          }
          return tinyMCE.get(id);
        },
        getHTML: function(id) {
          return service.getEditor(id).getContent({
            format: 'raw'
          });
        }
      };
      return service;
    }
  ]);

  angular.module('wordlift.editpost.widget.services.AnnotationParser', []).service('AnnotationParser', [
    '$log', function($log) {
      var service;
      service = {
        parse: function(html) {
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
        }
      };
      return service;
    }
  ]);

  angular.module('wordlift.editpost.widget.services.AnalysisService', ['wordlift.editpost.widget.services.AnnotationParser', 'wordlift.editpost.widget.services.EditorAdapter']).service('AnalysisService', [
    'AnnotationParser', 'EditorAdapter', 'configuration', '$log', '$http', '$rootScope', function(AnnotationParser, EditorAdapter, configuration, $log, $http, $rootScope) {
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
          mainType: '',
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
        var annotation, annotationId, dt, ea, em, entity, id, index, l, len2, len3, localEntity, local_confidence, m, ref10, ref11, ref2, ref3, ref4, ref5, ref6, ref7, ref8, ref9;
        dt = this._defaultType;
        data.topics = data.topics.map(function(topic) {
          topic.id = topic.uri;
          topic.occurrences = [];
          topic.mainType = dt;
          return topic;
        });
        ref2 = configuration.entities;
        for (id in ref2) {
          localEntity = ref2[id];
          data.entities[id] = localEntity;
        }
        ref3 = data.entities;
        for (id in ref3) {
          entity = ref3[id];
          if (configuration.currentPostUri === id) {
            delete data.entities[id];
            continue;
          }
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
            var l, len2, ref8, results1;
            ref8 = annotation.entityMatches;
            results1 = [];
            for (l = 0, len2 = ref8.length; l < len2; l++) {
              ea = ref8[l];
              if (ea.entityId in data.entities) {
                results1.push(ea);
              }
            }
            return results1;
          })();
          if (0 === data.annotations[id].entityMatches.length) {
            delete data.annotations[id];
            continue;
          }
          ref8 = data.annotations[id].entityMatches;
          for (index = l = 0, len2 = ref8.length; l < len2; index = ++l) {
            ea = ref8[index];
            if (!data.entities[ea.entityId].label) {
              data.entities[ea.entityId].label = annotation.text;
              $log.debug("Missing label retrieved from related annotation for entity " + ea.entityId);
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
            for (m = 0, len3 = ref11.length; m < len3; m++) {
              em = ref11[m];
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
        var entity, id, matches, promise, ref2, suggestions;
        promise = this._innerPerform(content).then(function(response) {});
        suggestions = [];
        ref2 = response.data.entities;
        for (id in ref2) {
          entity = ref2[id];
          if (matches = id.match(/^https?\:\/\/([^\/?#]+)(?:[\/?#]|$)/i)) {
            suggestions.push({
              id: id,
              label: entity.label,
              mainType: entity.mainType,
              source: matches[1]
            });
          }
        }
        $log.debug(suggestions);
        return $rootScope.$broadcast("sameAsRetrieved", suggestions);
      };
      service._innerPerform = function(content, annotations) {
        var args;
        if (annotations == null) {
          annotations = [];
        }
        args = {
          method: 'post',
          url: ajaxurl + '?action=wordlift_analyze'
        };
        args.headers = {
          'Content-Type': 'application/json'
        };
        args.data = {
          content: content,
          annotations: annotations,
          contentType: 'text/html',
          version: Traslator.version
        };
        if ((typeof wlSettings !== "undefined" && wlSettings !== null)) {
          if ((wlSettings.language != null)) {
            args.data.contentLanguage = wlSettings.language;
          }
          if ((wlSettings.itemId != null)) {
            args.data.exclude = [wlSettings.itemId];
          }
        }
        return $http(args);
      };
      service._updateStatus = function(status) {
        service._isRunning = status;
        return $rootScope.$broadcast("analysisServiceStatusUpdated", status);
      };
      service.perform = function(content) {
        var annotations, promise;
        if (service._currentAnalysis) {
          $log.warn("Analysis already run! Nothing to do ...");
          service._updateStatus(false);
          return;
        }
        service._updateStatus(true);
        annotations = AnnotationParser.parse(EditorAdapter.getHTML());
        $log.debug('Requesting analysis...');
        promise = this._innerPerform(content, annotations);
        promise.then(function(response) {
          var result;
          service._currentAnalysis = response.data;
          result = service.parse(response.data);
          $rootScope.$broadcast("analysisPerformed", result);
          return wp.wordlift.trigger('analysis.result', result);
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
        $log.debug("Selecting entity annotations (" + annotations.length + ")...");
        results1 = [];
        for (l = 0, len2 = annotations.length; l < len2; l++) {
          annotation = annotations[l];
          if (annotation.start === annotation.end) {
            $log.warn("There is a broken empty annotation for entityId " + annotation.uri);
            continue;
          }
          textAnnotation = findAnnotation(analysis.annotations, annotation.start, annotation.end);
          if (textAnnotation == null) {
            $log.warn("Text annotation " + annotation.start + ":" + annotation.end + " for entityId " + annotation.uri + " misses in the analysis");
            textAnnotation = this.createAnnotation({
              start: annotation.start,
              end: annotation.end,
              text: annotation.label,
              cssClass: annotation.cssClass != null ? annotation.cssClass : void 0
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

  angular.module('wordlift.editpost.widget.services.EditorService', ['wordlift.editpost.widget.services.EditorAdapter', 'wordlift.editpost.widget.services.AnalysisService']).service('EditorService', [
    'configuration', 'AnalysisService', 'EditorAdapter', '$log', '$http', '$rootScope', function(configuration, AnalysisService, EditorAdapter, $log, $http, $rootScope) {
      var INVISIBLE_CHAR, currentOccurencesForEntity, dedisambiguate, disambiguate, editor, findEntities, findPositions, service;
      INVISIBLE_CHAR = '\uFEFF';
      findEntities = function(html) {
        var annotation, match, pattern, results1, traslator;
        traslator = Traslator.create(html);
        pattern = /<(\w+)[^>]*\sclass="([^"]+)"\sitemid="([^"]+)"[^>]*>([^<]*)<\/\1>/gim;
        results1 = [];
        while (match = pattern.exec(html)) {
          annotation = {
            start: traslator.html2text(match.index),
            end: traslator.html2text(match.index + match[0].length),
            uri: match[3],
            label: match[4],
            cssClass: match[2]
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
      disambiguate = function(annotationId, entity) {
        var discardedItemId, ed, j, len, ref, type;
        ed = EditorAdapter.getEditor();
        ed.dom.addClass(annotationId, "disambiguated");
        ref = configuration.types;
        for (j = 0, len = ref.length; j < len; j++) {
          type = ref[j];
          ed.dom.removeClass(annotationId, type.css);
        }
        ed.dom.removeClass(annotationId, "unlinked");
        ed.dom.addClass(annotationId, "wl-" + entity.mainType);
        discardedItemId = ed.dom.getAttrib(annotationId, "itemid");
        ed.dom.setAttrib(annotationId, "itemid", entity.id);
        return discardedItemId;
      };
      dedisambiguate = function(annotationId, entity) {
        var discardedItemId, ed;
        ed = EditorAdapter.getEditor();
        ed.dom.removeClass(annotationId, "disambiguated");
        ed.dom.removeClass(annotationId, "wl-" + entity.mainType);
        discardedItemId = ed.dom.getAttrib(annotationId, "itemid");
        ed.dom.setAttrib(annotationId, "itemid", "");
        return discardedItemId;
      };
      currentOccurencesForEntity = function(entityId) {
        var annotation, annotations, ed, itemId, j, len, occurrences;
        ed = EditorAdapter.getEditor();
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
          discarded.push(disambiguate(annotationId, entity));
        } else {
          ref = entity.annotations;
          for (id in ref) {
            annotation = ref[id];
            discarded.push(disambiguate(annotation.id, entity));
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
        var annotation, id, occurrences, ref;
        if (annotationId != null) {
          dedisambiguate(annotationId, entity);
        } else {
          ref = entity.annotations;
          for (id in ref) {
            annotation = ref[id];
            dedisambiguate(annotation.id, entity);
          }
        }
        occurrences = currentOccurencesForEntity(entity.id);
        return $rootScope.$broadcast("updateOccurencesForEntity", entity.id, occurrences);
      });
      service = {
        hasSelection: function() {
          var ed, pattern;
          ed = EditorAdapter.getEditor();
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
          ed = EditorAdapter.getEditor();
          return ed.id === editor.id;
        },
        updateContentEditableStatus: function(status) {
          var ed;
          ed = EditorAdapter.getEditor();
          return ed.getBody().setAttribute('contenteditable', status);
        },
        createTextAnnotationFromCurrentSelection: function() {
          var content, ed, htmlPosition, text, textAnnotation, textAnnotationSpan, textPosition, traslator;
          ed = EditorAdapter.getEditor();
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
          content = EditorAdapter.getHTML();
          traslator = Traslator.create(content);
          htmlPosition = content.indexOf(textAnnotationSpan);
          textPosition = traslator.html2text(htmlPosition);
          textAnnotation.start = textPosition;
          textAnnotation.end = textAnnotation.start + text.length;
          return $rootScope.$broadcast('textAnnotationAdded', textAnnotation);
        },
        selectAnnotation: function(annotationId) {
          var annotation, ed, j, len, ref;
          ed = EditorAdapter.getEditor();
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
            var annotation, annotationId, ed, element, em, entities, entity, html, isDirty, j, len, ref, ref1, ref2, traslator;
            ed = EditorAdapter.getEditor();
            html = EditorAdapter.getHTML();
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
              if (-1 < ((ref1 = annotation.cssClass) != null ? ref1.indexOf('wl-no-link') : void 0)) {
                element += ' wl-no-link';
              }
              ref2 = annotation.entityMatches;
              for (j = 0, len = ref2.length; j < len; j++) {
                em = ref2[j];
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
        return $http({
          method: 'post',
          url: uri,
          data: entityIds
        }).success(function(data) {
          return $rootScope.$broadcast("relatedPostsLoaded", data);
        }).error(function(data, status) {
          return $log.warn("Error loading related posts");
        });
      };
      return service;
    }
  ]);

  angular.module('wordlift.editpost.widget.services.GeoLocationService', ['geolocation']).service('GeoLocationService', [
    'configuration', 'geolocation', '$log', '$rootScope', '$document', '$q', '$timeout', '$window', function(configuration, geolocation, $log, $rootScope, $document, $q, $timeout, $window) {
      var GOOGLE_MAPS_API_ENDPOINT, GOOGLE_MAPS_KEY, GOOGLE_MAPS_LEVEL, currentBrowser, loadGoogleAPI, service;
      GOOGLE_MAPS_LEVEL = 'locality';
      GOOGLE_MAPS_KEY = 'AIzaSyAhsajbqNVd7ABlkZvskWIPdiX6M3OaaNM';
      GOOGLE_MAPS_API_ENDPOINT = "https://maps.googleapis.com/maps/api/js?language=" + configuration.currentLanguage + "&key=" + GOOGLE_MAPS_KEY;
      $rootScope.$on('error', function(event, msg) {
        $log.warn("Geolocation error: " + msg);
        return $rootScope.$broadcast('geoLocationError', msg);
      });
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
      currentBrowser = function() {
        var browsers, key, userAgent;
        userAgent = $window.navigator.userAgent;
        browsers = {
          chrome: /chrome/i,
          safari: /safari/i,
          firefox: /firefox/i,
          ie: /internet explorer/i
        };
        for (key in browsers) {
          if (browsers[key].test(userAgent)) {
            return key;
          }
        }
        return 'unknown';
      };
      service = {};
      service.isAllowed = function() {
        if (currentBrowser() === 'chrome') {
          return $window.location.protocol === 'https:';
        }
        return true;
      };
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
              var ac, j, k, len, len1, ref, result;
              if (status === google.maps.GeocoderStatus.OK) {
                for (j = 0, len = results.length; j < len; j++) {
                  result = results[j];
                  if (indexOf.call(result.types, GOOGLE_MAPS_LEVEL) >= 0) {
                    ref = result.address_components;
                    for (k = 0, len1 = ref.length; k < len1; k++) {
                      ac = ref[k];
                      if (indexOf.call(ac.types, GOOGLE_MAPS_LEVEL) >= 0) {
                        $rootScope.$broadcast("currentUserLocalityDetected", result.formatted_address, ac.long_name);
                        return;
                      }
                    }
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
        _configuration = configuration;
        _configuration.getCategoryForType = function(entityType) {
          var category, j, len, ref;
          if (!entityType) {
            return void 0;
          }
          ref = this.classificationBoxes;
          for (j = 0, len = ref.length; j < len; j++) {
            category = ref[j];
            if (indexOf.call(category.registeredTypes, entityType) >= 0) {
              return category.id;
            }
          }
        };
        _configuration.getTypesForCategoryId = function(categoryId) {
          var category, j, len, ref;
          if (!categoryId) {
            return [];
          }
          ref = this.classificationBoxes;
          for (j = 0, len = ref.length; j < len; j++) {
            category = ref[j];
            if (categoryId === category.id) {
              return category.registeredTypes;
            }
          }
        };
        _configuration.isInternal = function(uri) {
          return uri != null ? uri.startsWith(this.datasetUri) : void 0;
        };
        return _configuration.getUriForType = function(mainType) {
          var j, len, ref, type;
          ref = this.types;
          for (j = 0, len = ref.length; j < len; j++) {
            type = ref[j];
            if (type.css === ("wl-" + mainType)) {
              return type.uri;
            }
          }
        };
      },
      $get: function() {
        return _configuration;
      }
    };
    return provider;
  });

  $ = jQuery;

  angular.module('wordlift.editpost.widget', ['ngAnimate', 'wordlift.ui.carousel', 'wordlift.utils.directives', 'wordlift.editpost.widget.providers.ConfigurationProvider', 'wordlift.editpost.widget.controllers.EditPostWidgetController', 'wordlift.editpost.widget.directives.wlClassificationBox', 'wordlift.editpost.widget.directives.wlEntityList', 'wordlift.editpost.widget.directives.wlEntityForm', 'wordlift.editpost.widget.directives.wlEntityTile', 'wordlift.editpost.widget.directives.wlEntityInputBox', 'wordlift.editpost.widget.services.AnalysisService', 'wordlift.editpost.widget.services.EditorService', 'wordlift.editpost.widget.services.RelatedPostDataRetrieverService']).config(function(configurationProvider) {
    return configurationProvider.setConfiguration(window.wordlift);
  });

  $(container = $("<div\n      id=\"wordlift-edit-post-wrapper\"\n      ng-controller=\"EditPostWidgetController\"\n      ng-include=\"configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-editpost-widget.html'\">\n    </div>").appendTo('#wordlift-edit-post-outer-wrapper'), spinner = $("<div class=\"wl-widget-spinner\">\n  <svg transform-origin=\"10 10\" id=\"wl-widget-spinner-blogger\">\n    <circle cx=\"10\" cy=\"10\" r=\"6\" class=\"wl-blogger-shape\"></circle>\n  </svg>\n  <svg transform-origin=\"10 10\" id=\"wl-widget-spinner-editorial\">\n    <rect x=\"4\" y=\"4\" width=\"12\" height=\"12\" class=\"wl-editorial-shape\"></rect>\n  </svg>\n  <svg transform-origin=\"10 10\" id=\"wl-widget-spinner-enterprise\">\n    <polygon points=\"3,10 6.5,4 13.4,4 16.9,10 13.4,16 6.5,16\" class=\"wl-enterprise-shape\"></polygon>\n  </svg>\n</div> ").appendTo('#wordlift_entities_box .ui-sortable-handle'), injector = angular.bootstrap($('#wordlift-edit-post-wrapper'), ['wordlift.editpost.widget']), injector.invoke([
    '$rootScope', '$log', function($rootScope, $log) {
      $rootScope.$on('analysisServiceStatusUpdated', function(event, status) {
        var css;
        css = status ? 'wl-spinner-running' : '';
        return $('.wl-widget-spinner svg').attr('class', css);
      });
      return $rootScope.$on('geoLocationStatusUpdated', function(event, status) {
        var css;
        css = status ? 'wl-spinner-running' : '';
        return $('.wl-widget-spinner svg').attr('class', css);
      });
    }
  ]), tinymce.PluginManager.add('wordlift', function(editor, url) {
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
        if (wp.autosave != null) {
          wp.autosave.server.postChanged = function() {
            return false;
          };
        }
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
            var html;
            html = editor.getContent({
              format: 'raw'
            });
            if ("" !== html) {
              EditorService.updateContentEditableStatus(false);
              return AnalysisService.perform(html);
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
