(function() {
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
          console.log(d);
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
        viz.selectAll('arcs_labels').data(chord.groups).enter().append('text').attr('class', 'label').attr('font-size', function() {
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
        tooltip = d3.select('body').append('div').attr('class', 'tooltip').style('background-color', 'white').style('opacity', 0.0).style('position', 'absolute').style('z-index', 100);
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
        'post_id': params.postId
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
      var buildTimeline, container, init, log, retrieveTimelineData, settings;
      settings = {
        dataEndpoint: void 0,
        width: '100%',
        height: '600',
        debug: false
      };
      settings = $.extend(settings, options);
      container = $(this);
      retrieveTimelineData = function() {
        return $.ajax({
          type: 'GET',
          url: settings.dataEndpoint,
          success: function(response) {
            return buildTimeline(response);
          }
        });
      };
      buildTimeline = function(data) {
        if (data.timeline != null) {
          return createStoryJS({
            type: 'timeline',
            width: settings.width,
            height: settings.height,
            source: data,
            embed_id: container.attr('id'),
            start_at_slide: data.startAtSlide
          });
        } else {
          container.hide();
          log("Timeline data missing: timeline cannot be rendered");
        }
      };
      init = function() {
        return retrieveTimelineData();
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
    return $('.wl-timeline').each(function() {
      var element, params, url;
      element = $(this);
      params = element.data();
      $.extend(params, wl_timeline_params);
      url = (params.ajax_url + "?") + $.param({
        'action': params.action,
        'post_id': params.postId
      });
      return $(this).timeline({
        dataEndpoint: url
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
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
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
        'post_id': params.postId
      });
      return element.geomap({
        dataEndpoint: url
      });
    });
  });

}).call(this);

//# sourceMappingURL=wordlift-ui.js.map
