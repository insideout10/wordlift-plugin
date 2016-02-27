(function() {
  var $, container, injector,
    indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  angular.module('wordlift.ui.carousel', []).directive('wlCarousel', [
    '$window', '$log', function($window, $log) {
      return {
        restrict: 'A',
        scope: true,
        transclude: true,
        template: "<div class=\"wl-carousel\" ng-show=\"panes.length > 0\">\n  <div class=\"wl-panes\" ng-style=\"{ width: panesWidth, left: position }\" ng-transclude ng-swipe-right=\"next()\"></div>\n  <div class=\"wl-carousel-arrow wl-prev\" ng-click=\"prev()\" ng-show=\"currentPaneIndex > 0\">\n    <i class=\"wl-angle-left\" />\n  </div>\n  <div class=\"wl-carousel-arrow wl-next\" ng-click=\"next()\" ng-show=\"isNextArrowVisible()\">\n    <i class=\"wl-angle-right\" />\n  </div>\n</div>",
        controller: [
          '$scope', '$element', '$attrs', function($scope, $element, $attrs) {
            var ctrl, w;
            w = angular.element($window);
            $scope.visibleElements = function() {
              if ($element.width() > 460) {
                return 3;
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
              var i, len, pane, ref;
              $scope.itemWidth = $scope.setItemWidth();
              $scope.setPanesWrapperWidth();
              ref = $scope.panes;
              for (i = 0, len = ref.length; i < len; i++) {
                pane = ref[i];
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
              var i, index, len, pane, ref, unregisterPaneIndex;
              unregisterPaneIndex = void 0;
              ref = $scope.panes;
              for (index = i = 0, len = ref.length; i < len; index = ++i) {
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
          $log.debug("Going to add carousel pane with id " + $scope.$id + " to carousel");
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

  $ = jQuery;

  angular.module('wordlift.facetedsearch.widget', ['wordlift.ui.carousel', 'wordlift.utils.directives']).provider("configuration", function() {
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
  }).filter('filterEntitiesByType', [
    '$log', 'configuration', function($log, configuration) {
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
  ]).directive('wlFacetedPosts', [
    'configuration', '$window', '$log', function(configuration, $window, $log) {
      return {
        restrict: 'E',
        scope: true,
        template: function(tElement, tAttrs) {
          var itemWrapperAttrs, itemWrapperClasses, thumbClasses, wrapperAttrs, wrapperClasses;
          wrapperClasses = 'wl-wrapper';
          wrapperAttrs = ' wl-carousel';
          itemWrapperClasses = 'wl-post wl-card wl-item-wrapper';
          itemWrapperAttrs = ' wl-carousel-pane';
          thumbClasses = 'wl-card-image';
          if (!configuration.attrs.with_carousel) {
            wrapperClasses = 'wl-floating-wrapper';
            wrapperAttrs = '';
            itemWrapperClasses = 'wl-post wl-card wl-floating-item-wrapper';
            itemWrapperAttrs = '';
          }
          if (configuration.attrs.squared_thumbs) {
            thumbClasses = 'wl-card-image wl-square';
          }
          return "<div class=\"wl-posts\">\n  <div class=\"" + wrapperClasses + "\" " + wrapperAttrs + ">\n    <div class=\"" + itemWrapperClasses + "\" ng-repeat=\"post in posts\"" + itemWrapperAttrs + ">\n      <div class=\"" + thumbClasses + "\"> \n        <span style=\"background: url({{post.thumbnail}}) no-repeat center center; background-size: cover;\"></span>\n      </div>\n      <div class=\"wl-card-title\"> \n        <a ng-href=\"{{post.permalink}}\">{{post.post_title}}</a>\n      </div>\n    </div>\n  </div>\n</div>";
        }
      };
    }
  ]).controller('FacetedSearchWidgetController', [
    'DataRetrieverService', 'configuration', '$scope', '$log', function(DataRetrieverService, configuration, $scope, $log) {
      $scope.entity = void 0;
      $scope.posts = [];
      $scope.facets = [];
      $scope.conditions = {};
      $scope.entityLimit = 5;
      $scope.supportedTypes = [
        {
          'scope': 'what',
          'types': ['thing', 'creative-work']
        }, {
          'scope': 'who',
          'types': ['person', 'organization', 'local-business']
        }, {
          'scope': 'where',
          'types': ['place']
        }, {
          'scope': 'when',
          'types': ['event']
        }
      ];
      $scope.configuration = configuration;
      $scope.filteringEnabled = true;
      $scope.toggleFacets = function() {
        $log.debug("Clicked!");
        return $scope.configuration.attrs.show_facets = !$scope.configuration.attrs.show_facets;
      };
      $scope.isInConditions = function(entity) {
        if ($scope.conditions[entity.id]) {
          return true;
        }
        return false;
      };
      $scope.addCondition = function(entity) {
        $log.debug("Add entity " + entity.id + " to conditions array");
        if ($scope.conditions[entity.id]) {
          delete $scope.conditions[entity.id];
        } else {
          $scope.conditions[entity.id] = entity;
        }
        return DataRetrieverService.load('posts', Object.keys($scope.conditions));
      };
      $scope.$on("postsLoaded", function(event, posts) {
        $log.debug("Referencing posts for item " + configuration.post_id + " ...");
        return $scope.posts = posts;
      });
      return $scope.$on("facetsLoaded", function(event, facets) {
        $log.debug("Referencing facets for item " + configuration.post_id + " ...");
        return $scope.facets = facets;
      });
    }
  ]).service('DataRetrieverService', [
    'configuration', '$log', '$http', '$rootScope', function(configuration, $log, $http, $rootScope) {
      var service;
      service = {};
      service.load = function(type, conditions) {
        var uri;
        if (conditions == null) {
          conditions = [];
        }
        uri = configuration.ajax_url + "?action=" + configuration.action + "&post_id=" + configuration.post_id + "&type=" + type;
        $log.debug("Going to search " + type + " with conditions");
        return $http({
          method: 'post',
          url: uri,
          data: conditions
        }).success(function(data) {
          return $rootScope.$broadcast(type + "Loaded", data);
        }).error(function(data, status) {
          return $log.warn("Error loading " + type + ", statut " + status);
        });
      };
      return service;
    }
  ]).config([
    'configurationProvider', function(configurationProvider) {
      return configurationProvider.setConfiguration(window.wl_faceted_search_params);
    }
  ]);

  $(container = $("<div ng-controller=\"FacetedSearchWidgetController\" ng-show=\"posts.length > 0\">\n      <h4 class=\"wl-headline\">\n        {{configuration.attrs.title}}\n        <i class=\"wl-toggle-on\" ng-hide=\"configuration.attrs.show_facets\" ng-click=\"toggleFacets()\"></i>\n        <i class=\"wl-toggle-off\" ng-show=\"configuration.attrs.show_facets\" ng-click=\"toggleFacets()\"></i>\n      </h4>\n      <div ng-show=\"configuration.attrs.show_facets\" class=\"wl-facets\" ng-show=\"filteringEnabled\">\n        <div class=\"wl-facets-container\" ng-repeat=\"box in supportedTypes\">\n          <h6>{{box.scope}}</h6>\n          <ul>\n            <li class=\"entity\" ng-repeat=\"entity in facets | orderBy:[ '-counter', '-createdAt' ] | filterEntitiesByType:box.types | limitTo:entityLimit\" ng-click=\"addCondition(entity)\">     \n                <i class=\"wl-checkbox\" ng-class=\" { 'selected' : isInConditions(entity) }\"></i>\n                <span class=\"wl-label\" ng-class=\" { 'selected' : isInConditions(entity) }\">\n                  {{entity.label}}\n                </span>\n            </li>\n          </ul>\n        </div>\n      </div>\n      <wl-faceted-posts></wl-faceted-posts>\n      \n    </div>").appendTo('#wordlift-faceted-entity-search-widget'), injector = angular.bootstrap($('#wordlift-faceted-entity-search-widget'), ['wordlift.facetedsearch.widget']), injector.invoke([
    'DataRetrieverService', '$rootScope', '$log', function(DataRetrieverService, $rootScope, $log) {
      return $rootScope.$apply(function() {
        DataRetrieverService.load('posts');
        return DataRetrieverService.load('facets');
      });
    }
  ]));

}).call(this);

//# sourceMappingURL=wordlift-faceted-entity-search-widget.js.map
