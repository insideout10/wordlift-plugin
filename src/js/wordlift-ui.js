var $;

$ = jQuery;

$.fn.extend({
  chord: function(options) {
    var buildChord, container, init, log, retrieveChordData, settings;
    settings = {
      dataEndpoint: void 0,
      mainColor: '#777',
      depth: 2,
      maxLabelLength: 30,
      maxWordLength: 5,
      debug: false
    };
    settings = $.extend(settings, options);
    container = $(this);
    retrieveChordData = function() {
      return $.ajax({
        type: 'GET',
        url: settings.dataEndpoint,
        data: {
          depth: settings.depth
        },
        success: function(response) {
          return buildChord(response);
        }
      });
    };
    buildChord = function(data) {
      var arc, beautifyLabel, chord, colorLuminance, e, entity, getEntityIndex, height, innerRadius, j, k, len, len1, matrix, outerRadius, rad2deg, ref, ref1, relation, rotate, sign, size, tooltip, translate, viz, width, x, y;
      if ((data.entities == null) || data.entities.length < 2) {
        container.hide();
        log("No data found for the chord.");
        return;
      }
      translate = function(x, y, sizeX, sizeY) {
        return 'translate(' + x * sizeX + ',' + y * sizeY + ')';
      };
      rotate = function(x) {
        return "rotate(" + x + ")";
      };
      rad2deg = function(a) {
        return (a / (2 * Math.PI)) * 360;
      };
      sign = function(n) {
        if (n >= 0.0) {
          return 1;
        } else {
          return -1;
        }
      };
      beautifyLabel = function(words) {
        var j, n, ref, w;
        if (words.length > settings.maxLabelLength) {
          words = words.substring(0, settings.maxLabelLength) + '...';
        }
        words = words.split(/\s/);
        n = [];
        for (w = j = 0, ref = words.length - 1; 0 <= ref ? j <= ref : j >= ref; w = 0 <= ref ? ++j : --j) {
          if (words[w].length > settings.maxWordLength || w === words.length - 1) {
            n.push(words[w]);
          } else {
            words[w + 1] = words[w] + ' ' + words[w + 1];
          }
        }
        return n;
      };
      colorLuminance = function(hex, lum) {
        var c, i, j, rgb;
        hex = String(hex).replace(/[^0-9a-f]/gi, '');
        if (hex.length < 6) {
          hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        lum = lum || 0;
        rgb = "#";
        c = void 0;
        i = void 0;
        for (i = j = 0; j <= 3; i = ++j) {
          c = parseInt(hex.substr(i * 2, 2), 16);
          c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
          rgb += ("00" + c).substr(c.length);
        }
        return rgb;
      };
      getEntityIndex = function(uri) {
        var i, j, ref;
        for (i = j = 0, ref = data.entities.length; 0 <= ref ? j <= ref : j >= ref; i = 0 <= ref ? ++j : --j) {
          if (data.entities[i].uri === uri) {
            return i;
          }
        }
        return -1;
      };
      matrix = [];
      ref = data.entities;
      for (j = 0, len = ref.length; j < len; j++) {
        entity = ref[j];
        matrix.push((function() {
          var k, len1, ref1, results;
          ref1 = data.entities;
          results = [];
          for (k = 0, len1 = ref1.length; k < len1; k++) {
            e = ref1[k];
            results.push(0);
          }
          return results;
        })());
      }
      ref1 = data.relations;
      for (k = 0, len1 = ref1.length; k < len1; k++) {
        relation = ref1[k];
        x = getEntityIndex(relation.s);
        y = getEntityIndex(relation.o);
        matrix[x][y] = 1;
        matrix[y][x] = 1;
      }
      viz = d3.select('#' + container.attr('id')).append('svg');
      viz.attr('width', '100%').attr('height', '100%');
      width = parseInt(viz.style('width'));
      height = parseInt(viz.style('height'));
      size = height < width ? height : width;
      innerRadius = size * 0.2;
      outerRadius = size * 0.25;
      arc = d3.svg.arc().innerRadius(innerRadius).outerRadius(outerRadius);
      chord = d3.layout.chord().padding(0.3).matrix(matrix);
      viz.selectAll('chords').data(chord.chords).enter().append('path').attr('class', 'relation').attr('d', d3.svg.chord().radius(innerRadius)).attr('transform', translate(0.5, 0.5, width, height)).style('opacity', 0.2).on('mouseover', function() {
        return d3.select(this).style('opacity', 0.8);
      }).on('mouseout', function() {
        return d3.select(this).style('opacity', 0.2);
      });
      viz.selectAll('arcs').data(chord.groups).enter().append('path').attr('class', function(d) {
        return "entity " + data.entities[d.index].css_class;
      }).attr('d', arc).attr('transform', translate(0.5, 0.5, width, height)).style('fill', function(d) {
        var baseColor, type;
        baseColor = settings.mainColor;
        type = data.entities[d.index].type;
        if (type === 'post') {
          return baseColor;
        }
        if (type === 'entity') {
          return colorLuminance(baseColor, -0.5);
        }
        return colorLuminance(baseColor, 0.5);
      });
      viz.selectAll('arcs_labels').data(chord.groups).enter().append('text').attr('class', 'wl-chord-label').attr('font-size', function() {
        var fontSize;
        fontSize = parseInt(size / 35);
        if (fontSize < 8) {
          fontSize = 8;
        }
        return fontSize + 'px';
      }).each(function(d) {
        var i, l, n, ref2, text;
        n = beautifyLabel(data.entities[d.index].label);
        text = d3.select(this).attr("dy", n.length / 3 - (n.length - 1) * 0.9 + 'em').text(n[0]);
        for (i = l = 1, ref2 = n.length; 1 <= ref2 ? l <= ref2 : l >= ref2; i = 1 <= ref2 ? ++l : --l) {
          text.append("tspan").attr('x', 0).attr('dy', '1em').text(n[i]);
        }
        return text.attr('transform', function(d) {
          var alpha, labelAngle, labelWidth, rX, rY;
          alpha = d.startAngle - Math.PI / 2 + Math.abs((d.endAngle - d.startAngle) / 2);
          labelWidth = 3;
          labelAngle = void 0;
          if (alpha > Math.PI / 2) {
            labelAngle = alpha - Math.PI;
            labelWidth += d3.select(this)[0][0].clientWidth;
          } else {
            labelAngle = alpha;
          }
          labelAngle = rad2deg(labelAngle);
          rX = (outerRadius + labelWidth) / width;
          rY = (outerRadius + labelWidth) / height;
          x = 0.5 + (rX * Math.cos(alpha));
          y = 0.5 + (rY * Math.sin(alpha));
          return translate(x, y, width, height) + rotate(labelAngle);
        }).attr('text-anchor', function(d) {
          var alpha, isFirefox;
          isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
          alpha = d.startAngle + Math.abs((d.endAngle - d.startAngle) / 2);
          if (isFirefox && alpha > Math.PI) {
            return 'end';
          }
          return null;
        });
      });
      tooltip = d3.select('body').append('div').attr('class', 'tooltip').style('background-color', 'white').style('color', 'black').style('opacity', 0.0).style('position', 'absolute').style('z-index', 100);
      return viz.selectAll('.entity, .label').on('mouseover', function(c) {
        d3.select(this).attr('cursor', 'pointer');
        viz.selectAll('.relation').filter(function(d, i) {
          return d.source.index === c.index || d.target.index === c.index;
        }).style('opacity', 0.8);
        return tooltip.text(data.entities[c.index].label).style('opacity', 1.0);
      }).on('mouseout', function(c) {
        viz.selectAll('.relation').filter(function(d, i) {
          return d.source.index === c.index || d.target.index === c.index;
        }).style('opacity', 0.2);
        return tooltip.style('opacity', 0.0);
      }).on('mousemove', function() {
        return tooltip.style("left", d3.event.pageX + "px").style("top", (d3.event.pageY - 30) + "px");
      }).on('click', function(d) {
        var url;
        url = data.entities[d.index].url;
        return window.location = url;
      });
    };
    init = function() {
      return retrieveChordData();
    };
    log = function(msg) {
      if (settings.debug) {
        return typeof console !== "undefined" && console !== null ? console.log(msg) : void 0;
      }
    };
    return init();
  }
});

jQuery(function($) {
  return $('.wl-chord').each(function() {
    var element, params, url;
    element = $(this);
    params = element.data();
    $.extend(params, wl_chord_params);
    url = (params.ajax_url + "?") + $.param({
      'action': params.action,
      'post_id': params.postId,
      'wl_chord_nonce': params.wl_chord_nonce
    });
    return element.chord({
      dataEndpoint: url,
      depth: params.depth,
      mainColor: params.mainColor
    });
  });
});

$ = jQuery;

$.fn.extend({
  timeline: function(options) {
    var buildTimeline, container, init;
    options = $.extend({
      dataEndpoint: null,
      settings: {}
    }, options);
    container = $(this);
    buildTimeline = function(data) {
      if (data.timeline == null) {
        container.parent().hide();
        return;
      }
      return new TL.Timeline(container.attr('id'), data.timeline, options.settings);
    };
    init = function() {
      return $.ajax({
        type: 'GET',
        url: options.dataEndpoint,
        success: function(response) {
          return buildTimeline(response);
        }
      });
    };
    return init();
  }
});

jQuery(function($) {
  return $('.wl-timeline').each(function() {
    var element, params, url;
    element = $(this);
    params = element.data();
    $.extend(params, wl_timeline_params);
    url = (params.ajax_url + "?") + $.param({
      'action': params.action,
      'post_id': params.postId,
      '_wpnonce': params.wl_timeline_nonce,
      'display_images_as': params.display_images_as,
      'excerpt_length': params.excerpt_length
    });
    return $(this).timeline({
      dataEndpoint: url,
      settings: params.settings
    });
  });
});

$ = jQuery;

$.fn.extend({
  geomap: function(options) {
    var buildGeomap, container, init, log, retrieveGeomapData, settings;
    settings = {
      dataEndpoint: void 0,
      zoom: 13,
      debug: false
    };
    settings = $.extend(settings, options);
    container = $(this);
    init = function() {
      return retrieveGeomapData();
    };
    retrieveGeomapData = function() {
      return $.ajax({
        type: 'GET',
        url: settings.dataEndpoint,
        success: function(response) {
          return buildGeomap(response);
        }
      });
    };
    buildGeomap = function(data) {
      var map, ref, ref1;
      if ((data.features == null) || ((ref = data.features) != null ? ref.length : void 0) === 0) {
        container.hide();
        log("Features missing: geomap cannot be rendered");
        return;
      }
      map = L.map(container.attr('id'));
      if (((ref1 = data.boundaries) != null ? ref1.length : void 0) === 1) {
        map.setView(data.boundaries[0], settings.zoom);
      } else {
        map.fitBounds(L.latLngBounds(data.boundaries));
      }
      L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);
      return L.geoJson(data.features, {
        pointToLayer: function(feature, latlng) {
          return L.marker(latlng, {});
        },
        onEachFeature: function(feature, layer) {
          var ref2;
          if ((ref2 = feature.properties) != null ? ref2.popupContent : void 0) {
            return layer.bindPopup(feature.properties.popupContent);
          }
        }
      }).addTo(map);
    };
    log = function(msg) {
      if (settings.debug) {
        return typeof console !== "undefined" && console !== null ? console.log(msg) : void 0;
      }
    };
    return init();
  }
});

jQuery(function($) {
  return $('.wl-geomap').each(function() {
    var element, params, url;
    element = $(this);
    params = element.data();
    $.extend(params, wl_geomap_params);
    url = (params.ajax_url + "?") + $.param({
      'action': params.action,
      'post_id': params.postId,
      '_wpnonce': params.wl_geomap_nonce
    });
    return element.geomap({
      dataEndpoint: url
    });
  });
});

angular.module('wordlift.ui.carousel', ['ngTouch']).directive('wlCarousel', [
  '$window', '$log', function($window, $log) {
    return {
      restrict: 'A',
      scope: true,
      transclude: true,
      template: "<div class=\"wl-carousel\" ng-class=\"{ 'active' : areControlsVisible }\" ng-show=\"panes.length > 0\" ng-mouseover=\"showControls()\" ng-mouseleave=\"hideControls()\">\n  <div class=\"wl-panes\" ng-style=\"{ width: panesWidth, left: position }\" ng-transclude ng-swipe-left=\"next()\" ng-swipe-right=\"prev()\" ></div>\n  <div class=\"wl-carousel-arrows\" ng-show=\"areControlsVisible\" ng-class=\"{ 'active' : isActive() }\">\n    <i class=\"wl-angle left\" ng-click=\"prev()\" ng-show=\"isPrevArrowVisible()\" />\n    <i class=\"wl-angle right\" ng-click=\"next()\" ng-show=\"isNextArrowVisible()\" />\n  </div>\n</div>",
      controller: [
        '$scope', '$element', '$attrs', '$log', function($scope, $element, $attrs, $log) {
          var ctrl, resizeFn, w;
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
          resizeFn = function() {
            var j, len, pane, ref;
            $scope.itemWidth = $scope.setItemWidth();
            $scope.setPanesWrapperWidth();
            ref = $scope.panes;
            for (j = 0, len = ref.length; j < len; j++) {
              pane = ref[j];
              pane.scope.setWidth($scope.itemWidth);
            }
            return $scope.$apply();
          };
          w.bind('resize', function() {
            return resizeFn;
          });
          w.bind('load', function() {
            return resizeFn;
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
      template: "<span\n  class=\"wl-widget-post-link\"\n  ng-class=\"{'wl-widget-post-link-copied' : $copied}\"\n  ng-click=\"copyToClipboard()\">\n  <ng-transclude></ng-transclude>\n  <input type=\"text\" ng-value=\"text\" />\n</span>",
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

angular.module('wordlift.navigator.widget', ['wordlift.ui.carousel', 'wordlift.utils.directives']).provider("configuration", function() {
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
}).directive('wlNavigatorItems', [
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
        return "<div class=\"wl-posts\">\n  <div class=\"" + wrapperClasses + "\" " + wrapperAttrs + ">\n    <div class=\"" + itemWrapperClasses + "\" ng-repeat=\"item in items\"" + itemWrapperAttrs + ">\n      <div class=\"wl-card-header wl-entity-wrapper\"> \n        <h6>\n          <a ng-href=\"{{item.entity.permalink}}\">{{item.entity.label}}</a>\n        </h6>\n      </div>\n      <div class=\"" + thumbClasses + "\"> \n        <a ng-href=\"{{item.post.permalink}}\" style=\"background: url({{item.post.thumbnail}}) no-repeat center center;background-size:cover;\"></a>\n      </div>\n      <div class=\"wl-card-title\"> \n        <a ng-href=\"{{item.post.permalink}}\">{{item.post.title}}</a>\n      </div>\n    </div>\n  </div>\n</div>";
      }
    };
  }
]).controller('NavigatorWidgetController', [
  'DataRetrieverService', 'configuration', '$scope', '$log', function(DataRetrieverService, configuration, $scope, $log) {
    $scope.items = [];
    $scope.configuration = configuration;
    return $scope.$on("itemsLoaded", function(event, items) {
      return $scope.items = items;
    });
  }
]).service('DataRetrieverService', [
  'configuration', '$log', '$http', '$rootScope', function(configuration, $log, $http, $rootScope) {
    var service;
    service = {};
    service.load = function() {
      var uri;
      uri = configuration.ajax_url + "?action=" + configuration.action + "&post_id=" + configuration.post_id;
      $log.debug("Going to load navigator items from " + uri);
      return $http({
        method: 'get',
        url: uri
      }).success(function(data) {
        return $rootScope.$broadcast("itemsLoaded", data);
      }).error(function(data, status) {
        return $log.warn("Error loading items, statut " + status);
      });
    };
    return service;
  }
]).config([
  'configurationProvider', function(configurationProvider) {
    return configurationProvider.setConfiguration(window.wl_navigator_params);
  }
]);

jQuery(function($) {
  var injector;
  $("<div ng-controller=\"NavigatorWidgetController\" ng-show=\"items.length > 0\">\n  <h4 class=\"wl-headline\">{{configuration.attrs.title}}</h4>\n  <wl-navigator-items></wl-navigator-items>\n</div>").appendTo('.wl-navigator-widget');
  if (0 < $('.wl-navigator-widget').length) {
    injector = angular.bootstrap($('.wl-navigator-widget'), ['wordlift.navigator.widget']);
    return injector.invoke([
      'DataRetrieverService', '$rootScope', '$log', function(DataRetrieverService, $rootScope, $log) {
        return $rootScope.$apply(function() {
          return DataRetrieverService.load();
        });
      }
    ]);
  }
});

//# sourceMappingURL=wordlift-ui.js.map
