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
# Set the well-known $ reference to jQuery.
$ = jQuery

# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.facetedsearch.widget', ['wordlift.ui.carousel', 'wordlift.utils.directives'])
.provider("configuration", ()->
  _configuration = undefined

  provider =
    setConfiguration: (configuration)->
      _configuration = configuration
    $get: ()->
      _configuration

  provider
)
.filter('filterEntitiesByType', ['$log', 'configuration', ($log, configuration)->
  return (items, types)->
    filtered = []
    for id, entity of items
      if entity.mainType in types
        filtered.push entity
    filtered

])
.directive('wlFacetedPosts', ['configuration', '$window', '$log', (configuration, $window, $log)->
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
          <div class="#{itemWrapperClasses}" ng-repeat="post in posts"#{itemWrapperAttrs}>
            <div class="#{thumbClasses}">
              <a ng-href="{{post.permalink}}" style="background: url('{{post.thumbnail}}') no-repeat center center; background-size: cover;"></a>
            </div>
            <div class="wl-card-title">
              <a ng-href="{{post.permalink}}">{{post.post_title}}</a>
            </div>
          </div>
        </div>
      </div>
  """

])

.controller('FacetedSearchWidgetController', ['DataRetrieverService', 'configuration', '$scope', 'filterEntitiesByTypeFilter', '$log',
  (DataRetrieverService, configuration, $scope, filterEntitiesByTypeFilter, $log)->
    $scope.entity = undefined
    $scope.posts = []
    $scope.facets = []
    $scope.conditions = {}
    $scope.entityLimit = 5

    # TODO Load dynamically
    $scope.supportedTypes = [
      {'scope': 'what', 'types': ['thing', 'creative-work', 'recipe']}
      {'scope': 'who', 'types': ['person', 'organization', 'local-business']}
      {'scope': 'where', 'types': ['place']}
      {'scope': 'when', 'types': ['event']}
    ]

    $scope.configuration = configuration
    $scope.filteringEnabled = true

    $scope.toggleFacets = ()->
      $scope.configuration.attrs.show_facets = !$scope.configuration.attrs.show_facets
      # Reset conditions
      $scope.conditions = {}
      DataRetrieverService.load('posts')


    $scope.isInConditions = (entity)->
      if Object.keys($scope.conditions).length is 0
        return true
      if $scope.conditions[entity.id]
        return true
      return false

    $scope.addCondition = (entity)->
      $log.debug "Add entity #{entity.id} to conditions array"

      if $scope.conditions[entity.id]
        delete $scope.conditions[entity.id]
      else
        $scope.conditions[entity.id] = entity

      DataRetrieverService.load('posts', Object.keys($scope.conditions))

    $scope.$on "postsLoaded", (event, posts) ->
      $log.debug "Referencing posts for item #{configuration.post_id} ..."
      $scope.posts = posts

    $scope.$on "facetsLoaded", (event, facets) ->
      $log.debug "Referencing facets for item #{configuration.post_id} ..."
      $scope.facets = facets

    # When the facets are updated, recalculate the list for each box.
    $scope.$watch 'facets', (facets) ->
      type.entities = filterEntitiesByTypeFilter( facets, type.types ) for type in $scope.supportedTypes

])
# Retrieve post
.service('DataRetrieverService', ['configuration', '$log', '$http', '$rootScope',
  (configuration, $log, $http, $rootScope)->
    service = {}
    service.load = (type, conditions = [])->
      uri = "#{configuration.ajax_url}?action=#{configuration.action}&post_id=#{configuration.post_id}&limit=#{configuration.limit}&type=#{type}"

      $log.debug "Going to search #{type} with conditions"

      $http(
        method: 'post'
        url: uri
        data: conditions
      )
# If successful, broadcast an *analysisReceived* event.
      .success (data) ->
        $rootScope.$broadcast "#{type}Loaded", data
      .error (data, status) ->
        $log.warn "Error loading #{type}, statut #{status}"

    service

])
# Configuration provider
.config(['configurationProvider', (configurationProvider)->
  configurationProvider.setConfiguration window.wl_faceted_search_params
])

$(
  container = $("""
  	<div ng-controller="FacetedSearchWidgetController" ng-show="posts.length > 0">
      <h4 class="wl-headline">
        {{configuration.attrs.title}}
        <i class="wl-toggle-on" ng-hide="configuration.attrs.show_facets" ng-click="toggleFacets()"></i>
        <i class="wl-toggle-off" ng-show="configuration.attrs.show_facets" ng-click="toggleFacets()"></i>
      </h4>
      <div ng-show="configuration.attrs.show_facets" class="wl-facets" ng-show="filteringEnabled">
        <div class="wl-facets-container" ng-repeat="box in supportedTypes" ng-hide="0 === box.entities.length">
          <h5>{{configuration.l10n[box.scope]}}</h5>
          <ul>
            <li class="entity" ng-repeat="entity in box.entities | orderBy:[ '-counter', '-createdAt' ] | limitTo:entityLimit" ng-click="addCondition(entity)">
                <span class="wl-label" ng-class=" { 'selected' : isInConditions(entity) }">
                  {{entity.label}}
                </span>
            </li>
          </ul>
        </div>
      </div>
      <wl-faceted-posts></wl-faceted-posts>

    </div>
  """)
  .appendTo('#wordlift-faceted-entity-search-widget')

  injector = angular.bootstrap $('#wordlift-faceted-entity-search-widget'), ['wordlift.facetedsearch.widget']
  injector.invoke(['DataRetrieverService', '$rootScope', '$log', (DataRetrieverService, $rootScope, $log) ->
# execute the following commands in the angular js context.
    $rootScope.$apply(->
      DataRetrieverService.load('posts')
      DataRetrieverService.load('facets')
    )
  ])
)


