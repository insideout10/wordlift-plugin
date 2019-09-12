$ = jQuery

# Add a geomap plugin object to jQuery
$.fn.extend
# Change pluginName to your plugin's name.
  geomap: (options) ->
    # Default settings
    settings =
      dataEndpoint: undefined
      zoom: 13
      debug: false

    # Merge default settings with options.
    settings = $.extend settings, options
    # Create a reference to dom wrapper element
    container = $(@)

    # Initialization method
    init = ->
      retrieveGeomapData()

    # Retrieve data from for map rendering
    retrieveGeomapData = ->
      $.ajax
        type: 'GET'
        url: settings.dataEndpoint
        success: (response) ->
          buildGeomap response

    # Build a geoMap obj via Leaflet.js
    # See: https://github.com/Leaflet/Leaflet
    buildGeomap = (data) ->

      # With features undefined or empty set the container as hidden and log a warning
      if not data.features? or data.features?.length is 0
        container.hide()
        log "Features missing: geomap cannot be rendered"
        return

      # Create a map
      map = L.map container.attr('id')

      # Fit map bounds for our set of points
      # With a single feature sets the map center accordingly to feature coordinates.
      # With more than one feature sets baundaries instead.
      if data.boundaries?.length is 1
        map.setView data.boundaries[0], settings.zoom
      else
        map.fitBounds L.latLngBounds(data.boundaries)

      # Add an OpenStreetMap tile layer
      L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png',
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
      ).addTo map

      L.geoJson(data.features,
        pointToLayer: (feature, latlng) ->
          # TODO: give marker style here
          L.marker latlng, {}
        onEachFeature: (feature, layer) ->
          # On each feature set popupContent if available
          if feature.properties?.popupContent
            layer.bindPopup feature.properties.popupContent
      ).addTo map

    # Simple logger 
    log = (msg) ->
      console?.log msg if settings.debug

    init()
# TODO we should think about how to initilize the whole wordlift ui layer
jQuery ($) ->
  $('.wl-geomap').each ->
    element = $(@)

    params = element.data()
    $.extend params, wl_geomap_params

    url = "#{params.ajax_url}?" + $.param( 'action': params.action, 'post_id': params.postId )

    element.geomap
      dataEndpoint: url
