$ = jQuery

# Add a chord plugin object to jQuery
$.fn.extend
  chord: (options) ->
    settings = {
      dataEndpoint: undefined
      mainColor: '#777'
      depth: 2
      maxLabelLength: 30
      maxWordLength: 5
      debug: false
    }

    # Merge default settings with options.
    settings = $.extend settings, options

    # Create a reference to dom wrapper element
    container = $(@)

    retrieveChordData = ->
      $.ajax
        type: 'GET'
        url: settings.dataEndpoint
        data:
          depth: settings.depth
        success: (response) ->
          buildChord response

    buildChord = (data) ->
      if not data.entities? or data.entities.length < 2
        container.hide()
        log "No data found for the chord."
        return

      # define some service functions, then build the chord

      translate = (x, y, sizeX, sizeY) -> 'translate(' + x * sizeX + ',' + y * sizeY + ')'

      rotate = (x) -> "rotate(#{x})"

      rad2deg = (a) -> ( a / (2 * Math.PI)) * 360

      sign = (n) -> if n >= 0.0 then 1 else -1

      beautifyLabel = (words) ->
# when labels are way too long, show only the first part of them
# (the whole label will be displayed in the tooltip at mouseover)
        if words.length > settings.maxLabelLength
          words = words.substring(0, settings.maxLabelLength) + '...'

        # split in words
        words = words.split(/\s/)

        # group shortest words
        n = []
        for w in [0..words.length - 1]
          if words[w].length > settings.maxWordLength or w is words.length - 1
            n.push words[w]
          else
            words[w + 1] = words[w] + ' ' + words[w + 1]

        return n

      colorLuminance = (hex, lum) ->
# Validate hex string.
        hex = String(hex).replace(/[^0-9a-f]/gi, '')

        if (hex.length < 6)
          hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2]

        lum = lum or 0

        # Convert to decimal and change luminosity.
        rgb = "#"
        c = undefined
        i = undefined

        for i in [0..3]
          c = parseInt(hex.substr(i * 2, 2), 16)
          c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16)
          rgb += ("00" + c).substr(c.length)

        rgb

      getEntityIndex = (uri) ->
        return i for i in [0..data.entities.length] when data.entities[i].uri is uri
        -1

      # Build adiacency matrix.
      matrix = []
      matrix.push (0 for e in data.entities) for entity in data.entities

      for relation in data.relations
        x = getEntityIndex relation.s
        y = getEntityIndex relation.o
        matrix[x][y] = 1
        matrix[y][x] = 1

      viz = d3.select('#' + container.attr('id')).append('svg')
      viz.attr('width', '100%').attr('height', '100%')

      # Getting dimensions in pixels.
      width = parseInt(viz.style('width'))
      height = parseInt(viz.style('height'))
      size = if height < width then height else width
      innerRadius = size * 0.2
      outerRadius = size * 0.25
      arc = d3.svg.arc().innerRadius(innerRadius).outerRadius(outerRadius)

      chord = d3.layout.chord()
      .padding(0.3)
      .matrix(matrix)

      # Draw relations.
      viz.selectAll('chords')
      .data(chord.chords)
      .enter()
      .append('path')
      .attr('class', 'relation')
      .attr('d', d3.svg.chord().radius(innerRadius))
      .attr('transform', translate(0.5, 0.5, width, height))
      .style('opacity', 0.2)
      .on('mouseover', -> d3.select(this).style('opacity', 0.8))
      .on('mouseout', -> d3.select(this).style('opacity', 0.2))

      # Draw entities.
      viz.selectAll('arcs')
      .data(chord.groups)
      .enter()
      .append('path')
      .attr('class', (d) ->
        return "entity #{data.entities[d.index].css_class}"
      )
      .attr('d', arc)
      .attr('transform', translate(0.5, 0.5, width, height))
      .style('fill', (d) ->
        baseColor = settings.mainColor;
        type = data.entities[d.index].type
        return baseColor if(type == 'post')
        return colorLuminance(baseColor, -0.5) if type is 'entity'
        colorLuminance(baseColor, 0.5)
      )

      # Draw entity labels.
      viz.selectAll('arcs_labels')
      .data(chord.groups)
      .enter()
      .append('text')
      .attr('class', 'wl-chord-label')
      .attr('font-size', ->
        fontSize = parseInt(size / 35)
        fontSize = 8 if(fontSize < 8)
        fontSize + 'px'
      )
      .each((d) ->

        # beautify label
        n = beautifyLabel(data.entities[d.index].label)

        # get the current element
        text = d3.select(this)
        .attr("dy", n.length / 3 - (n.length - 1) * 0.9 + 'em')
        .text(n[0])

        # now loop
        for i in [1..n.length]
          text.append("tspan")
          .attr('x', 0)
          .attr('dy', '1em')
          .text(n[i])

        text.attr('transform', (d) ->
          alpha = d.startAngle - Math.PI / 2 + Math.abs((d.endAngle - d.startAngle) / 2)
          labelWidth = 3
          labelAngle = undefined
          if(alpha > Math.PI / 2)
            labelAngle = alpha - Math.PI
            labelWidth += d3.select(this)[0][0].clientWidth
          else
            labelAngle = alpha

          labelAngle = rad2deg(labelAngle)

          rX = (outerRadius + labelWidth) / width
          rY = (outerRadius + labelWidth) / height
          x = 0.5 + ( rX * Math.cos(alpha) )
          y = 0.5 + ( rY * Math.sin(alpha) )

          translate(x, y, width, height) + rotate(labelAngle)
        )
        .attr('text-anchor', (d) ->
          isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1
          alpha = d.startAngle + Math.abs((d.endAngle - d.startAngle) / 2)

          if isFirefox and alpha > Math.PI
            return 'end'

          return null
        )
      )

      # Creating an hidden tooltip.
      tooltip = d3.select('body').append('div')
      .attr('class', 'tooltip')
      .style('background-color', 'white')
      .style('color', 'black')
      .style('opacity', 0.0)
      .style('position', 'absolute')
      .style('z-index', 100)

      #  Dynamic behavior for entities.
      viz.selectAll('.entity, .label')
      .on('mouseover', (c) ->
        d3.select(this).attr('cursor', 'pointer')
        viz.selectAll('.relation')
        .filter((d, i) -> d.source.index is c.index or d.target.index is c.index)
        .style('opacity', 0.8)

        # Show tooltip.
        tooltip.text(data.entities[c.index].label).style('opacity', 1.0)
      )
      .on('mouseout', (c) ->
        viz.selectAll('.relation')
        .filter((d, i) -> d.source.index is c.index or d.target.index is c.index)
        .style('opacity', 0.2)

        # Hide tooltip.
        tooltip.style('opacity', 0.0)
      )
      .on('mousemove', ->
# Change tooltip position.
        tooltip.style("left", (d3.event.pageX) + "px")
        .style("top", (d3.event.pageY - 30) + "px")
      )
      .on('click', (d) ->
        url = data.entities[d.index].url
        window.location = url
      )

    # Initialization method
    init = ->
      retrieveChordData()

    # Simple logger
    log = (msg) ->
      console?.log msg if settings.debug

    # start chord operations
    init()

jQuery ($) ->
  $('.wl-chord').each ->
    element = $(@)

    params = element.data()
    $.extend params, wl_chord_params

    url = "#{params.ajax_url}?" + $.param('action': params.action, 'post_id': params.postId, 'wl_chord_nonce':params.wl_chord_nonce)

    # Launch chord.
    element.chord
      dataEndpoint: url
      depth: params.depth
      mainColor: params.mainColor

$ = jQuery

# Add a timeline plugin object to jQuery
$.fn.extend

  timeline: (options) ->

    #
    options = $.extend {dataEndpoint: null, settings: {}}, options

    # Create a reference to dom wrapper element
    container = $(@)

    # Build a Timeline obj via TimelineJS
    # See: https://github.com/NUKnightLab/TimelineJS
    buildTimeline = (data) ->
      if not data.timeline?
        container.parent().hide()
        return

      # TimelineJS v3 constructor.
      new TL.Timeline(container.attr('id'), data.timeline, options.settings)

    # Initialization method
    init = ->
      $.ajax
        type: 'GET'
        url: options.dataEndpoint
        success: (response) ->
          buildTimeline response

    init()

jQuery ($) ->
  $('.wl-timeline').each ->
    element = $(@)

    params = element.data()
    $.extend params, wl_timeline_params

    url = "#{params.ajax_url}?" + $.param(
      'action': params.action,
      'post_id': params.postId,
      '_wpnonce': params.wl_timeline_nonce
      'display_images_as': params.display_images_as,
      'excerpt_length': params.excerpt_length)

    $(this).timeline
      dataEndpoint: url
      settings: params.settings
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
      L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png',
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
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
# TODO we should think about how to initialize the whole wordlift ui layer
jQuery ($) ->
  $('.wl-geomap').each ->
    element = $(@)

    params = element.data()
    $.extend params, wl_geomap_params

    url = "#{params.ajax_url}?" + $.param( 'action': params.action, 'post_id': params.postId, '_wpnonce': params.wl_geomap_nonce )

    element.geomap
      dataEndpoint: url

angular.module('wordlift.ui.carousel', ['ngTouch'])
.directive('wlCarousel', ['$window', '$log', ($window, $log)->
  restrict: 'A'
  scope: true
  transclude: true      
  template: """
      <div class="wl-carousel" ng-class="{ 'active' : areControlsVisible }" ng-show="panes.length > 0" ng-mouseover="showControls()" ng-mouseleave="hideControls()">
        <div class="wl-panes" ng-style="{ width: panesWidth, left: position }" ng-transclude ng-swipe-left="next()" ng-swipe-right="prev()" ></div>
        <div class="wl-carousel-arrows" ng-show="areControlsVisible" ng-class="{ 'active' : isActive() }">
          <i class="wl-angle left" ng-click="prev()" ng-show="isPrevArrowVisible()" />
          <i class="wl-angle right" ng-click="next()" ng-show="isNextArrowVisible()" />
        </div>
      </div>
  """
  controller: [ '$scope', '$element', '$attrs', '$log', ($scope, $element, $attrs, $log) ->
      
    w = angular.element $window

    $scope.setItemWidth = ()->
      $element.width() / $scope.visibleElements() 

    $scope.showControls = ()->
      $scope.areControlsVisible = true

    $scope.hideControls = ()->
      $scope.areControlsVisible = false

    $scope.visibleElements = ()->
      if $element.width() > 460
        return 4
      return 1

    $scope.isActive = ()->
      $scope.isPrevArrowVisible() or $scope.isNextArrowVisible()
        
    $scope.isPrevArrowVisible = ()->
      ($scope.currentPaneIndex > 0)
    
    $scope.isNextArrowVisible = ()->
      ($scope.panes.length - $scope.currentPaneIndex) > $scope.visibleElements()
    
    $scope.next = ()->
      if ( $scope.currentPaneIndex + $scope.visibleElements() + 1 ) > $scope.panes.length
        return 
      $scope.position = $scope.position - $scope.itemWidth
      $scope.currentPaneIndex = $scope.currentPaneIndex + 1

    $scope.prev = ()->
      if $scope.currentPaneIndex is 0
        return 
      $scope.position = $scope.position + $scope.itemWidth
      $scope.currentPaneIndex = $scope.currentPaneIndex - 1
    
    $scope.setPanesWrapperWidth = ()->
      # console.debug { "Setting panes wrapper width...", Object.assign( {}, $scope ) }
      $scope.panesWidth = ( $scope.panes.length * $scope.itemWidth ) 
      $scope.position = 0;
      $scope.currentPaneIndex = 0

    $scope.itemWidth =  $scope.setItemWidth()
    $scope.panesWidth = undefined
    $scope.panes = []
    $scope.position = 0;
    $scope.currentPaneIndex = 0
    $scope.areControlsVisible = false

    # The resize function is called when the window is resized to recalculate
    # sizes. It is also called at load for the first calculation.
    resizeFn = () ->
      $scope.itemWidth = $scope.setItemWidth();
      $scope.setPanesWrapperWidth()
      for pane in $scope.panes
        pane.scope.setWidth $scope.itemWidth
      $scope.$apply()

    # Bind the window resize event.
    w.bind 'resize', ()-> resizeFn
    w.bind 'load', ()-> resizeFn

    ctrl = @
    ctrl.registerPane = (scope, element, first)->
      # Set the proper width for the element
      scope.setWidth $scope.itemWidth
        
      pane =
        'scope': scope
        'element': element

      $scope.panes.push pane
      $scope.setPanesWrapperWidth()
      
      #if first
      #  $log.debug "Eccolo"
      #  $log.debug $scope.panes.length
      #  $scope.position = $scope.panes.length * $scope.itemWidth
      #  $scope.currentPaneIndex = $scope.panes.length

    ctrl.unregisterPane = (scope)->
        
      unregisterPaneIndex = undefined
      for pane, index in $scope.panes
        if pane.scope.$id is scope.$id
          unregisterPaneIndex = index

      $scope.panes.splice unregisterPaneIndex, 1
      $scope.setPanesWrapperWidth()
  ]
])
.directive('wlCarouselPane', ['$log', ($log)->
  require: '^wlCarousel'
  restrict: 'EA'
  scope:
    wlFirstPane: '='
  transclude: true 
  template: """
      <div ng-transclude></div>
  """
  link: ($scope, $element, $attrs, $ctrl) ->

    $element.addClass "wl-carousel-item"
    $scope.isFirst = $scope.wlFirstPane || false

    $scope.setWidth = (size)->
      $element.css('width', "#{size}px")

    $scope.$on '$destroy', ()->
      $log.debug "Destroy #{$scope.$id}"
      $ctrl.unregisterPane $scope

    $ctrl.registerPane $scope, $element, $scope.isFirst
])
angular.module('wordlift.utils.directives', [])
# See https://github.com/angular/angular.js/blob/master/src/ng/directive/ngEventDirs.js
.directive('wlOnError', ['$parse', '$window', '$log', ($parse, $window, $log)->
  restrict: 'A'
  compile: ($element, $attrs) ->
    return (scope, element)->
      fn = $parse($attrs.wlOnError)
      element.on('error', (event)->
        callback = ()->
      	  fn(scope, { $event: event })
        scope.$apply(callback)
      )
])
.directive('wlFallback', ['$window', '$log', ($window, $log)->
  restrict: 'A'
  priority: 99 # it needs to run after the attributes are interpolated
  link: ($scope, $element, $attrs, $ctrl) ->
    $element.bind('error', ()->
      unless $attrs.src is $attrs.wlFallback
        $log.warn "Error on #{$attrs.src}! Going to fallback on #{$attrs.wlFallback}"
        $attrs.$set 'src', $attrs.wlFallback
    )
])
.directive('wlHideAfter', ['$timeout', '$log', ($timeout, $log)->
  restrict: 'A'
  link: ($scope, $element, $attrs, $ctrl) ->
    delay = +$attrs.wlHideAfter
    $timeout(()->
      $log.debug "Remove msg after #{delay} ms"
      $element.hide()
    , delay)
])
.directive('wlClipboard', ['$timeout', '$document', '$log', ($timeout, $document, $log)->
  restrict: 'E'
  scope:
    text: '='
    onCopied: '&'
  transclude: true
  template: """
    <span
      class="wl-widget-post-link"
      ng-class="{'wl-widget-post-link-copied' : $copied}"
      ng-click="copyToClipboard()">
      <ng-transclude></ng-transclude>
      <input type="text" ng-value="text" />
    </span>
  """
  link: ($scope, $element, $attrs, $ctrl) ->

    $scope.$copied = false

    $scope.node = $element.find 'input'
    $scope.node.css 'position', 'absolute'
    $scope.node.css 'left', '-10000px'

    # $element
    $scope.copyToClipboard = ()->
      try

        # Set inline style to override css styles
        $document[0].body.style.webkitUserSelect = 'initial'
        selection = $document[0].getSelection()
        selection.removeAllRanges()
        # Fake node selection
        $scope.node.select()
        # Perform the task
        unless $document[0].execCommand 'copy'
           $log.warn "Error on clipboard copy for #{text}"
        selection.removeAllRanges()
        # Update copied status and reset after 3 seconds
        $scope.$copied = true
        $timeout(()->
          $log.debug "Going to reset $copied status"
          $scope.$copied = false
        , 3000)

        # Execute onCopied callback
        if angular.isFunction($scope.onCopied)
          $scope.$evalAsync $scope.onCopied()

      finally
        $document[0].body.style.webkitUserSelect = ''
])
# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.navigator.widget', ['wordlift.ui.carousel', 'wordlift.utils.directives'])
.provider("configuration", ()->
  _configuration = undefined

  provider =
    setConfiguration: (configuration)->
      _configuration = configuration
    $get: ()->
      _configuration

  provider
)
.directive('wlNavigatorItems', ['configuration', '$window', '$log', (configuration, $window, $log)->
  restrict: 'E'
  scope: true
  template: (tElement, tAttrs)->
    wrapperClasses = 'wl-wrapper'
    wrapperAttrs = ' wl-carousel'
    itemWrapperClasses = 'wl-post wl-card wl-item-wrapper'
    itemWrapperAttrs = ' wl-carousel-pane'
    thumbClasses = 'wl-card-image'

    unless configuration.attrs.with_carousel
      wrapperClasses = 'wl-floating-wrapper'
      wrapperAttrs = ''
      itemWrapperClasses = 'wl-post wl-card wl-floating-item-wrapper'
      itemWrapperAttrs = ''

    if configuration.attrs.squared_thumbs
      thumbClasses = 'wl-card-image wl-square'

    """
      <div class="wl-posts">
        <div class="#{wrapperClasses}" #{wrapperAttrs}>
          <div class="#{itemWrapperClasses}" ng-repeat="item in items"#{itemWrapperAttrs}>
            <div class="wl-card-header wl-entity-wrapper"> 
              <h6>
                <a ng-href="{{item.entity.permalink}}">{{item.entity.label}}</a>
              </h6>
            </div>
            <div class="#{thumbClasses}"> 
              <a ng-href="{{item.post.permalink}}" style="background: url({{item.post.thumbnail}}) no-repeat center center;background-size:cover;"></a>
            </div>
            <div class="wl-card-title"> 
              <a ng-href="{{item.post.permalink}}">{{item.post.title}}</a>
            </div>
          </div>
        </div>
      </div>
  """

])
.controller('NavigatorWidgetController', ['DataRetrieverService', 'configuration', '$scope', '$log',
  (DataRetrieverService, configuration, $scope, $log)->
    $scope.items = []
    $scope.configuration = configuration

    $scope.$on "itemsLoaded", (event, items) ->
      $scope.items = items

])
# Retrieve post
.service('DataRetrieverService', ['configuration', '$log', '$http', '$rootScope',
  (configuration, $log, $http, $rootScope)->
    service = {}
    service.load = ()->
      uri = "#{configuration.ajax_url}?action=#{configuration.action}&post_id=#{configuration.post_id}"
      $log.debug "Going to load navigator items from #{uri}"

      $http(
        method: 'get'
        url: uri
      )
# If successful, broadcast an *analysisReceived* event.
      .success (data) ->
        $rootScope.$broadcast "itemsLoaded", data
      .error (data, status) ->
        $log.warn "Error loading items, statut #{status}"

    service

])
# Configuration provider
.config(['configurationProvider', (configurationProvider)->
  configurationProvider.setConfiguration window.wl_navigator_params
])


jQuery( ($) ->

  $("""
  <div ng-controller="NavigatorWidgetController" ng-show="items.length > 0">
    <h4 class="wl-headline">{{configuration.attrs.title}}</h4>
    <wl-navigator-items></wl-navigator-items>
  </div>
""")
    .appendTo('.wl-navigator-widget')

  # If there are navigator widgets on the page activate them.
  if 0 < $('.wl-navigator-widget').length
    injector = angular.bootstrap $('.wl-navigator-widget'), ['wordlift.navigator.widget']
    injector.invoke(['DataRetrieverService', '$rootScope', '$log', (DataRetrieverService, $rootScope, $log) ->
      # execute the following commands in the angular js context.
      $rootScope.$apply(-> DataRetrieverService.load())
    ])

)


