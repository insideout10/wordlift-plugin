angular.module('wordlift.ui.carousel', [])
.directive('wlCarousel', ['$window', '$log', ($window, $log)->
  restrict: 'A'
  scope: true
  transclude: true      
  template: """
      <div class="wl-carousel" ng-show="panes.length > 0">
        <div class="wl-panes" ng-style="{ width: panesWidth, left: position }" ng-transclude ng-swipe-right="next()"></div>
        <div class="wl-carousel-arrow wl-prev" ng-click="prev()" ng-show="currentPaneIndex > 0">
          <i class="wl-angle-left" />
        </div>
        <div class="wl-carousel-arrow wl-next" ng-click="next()" ng-show="isNextArrowVisible()">
          <i class="wl-angle-right" />
        </div>
      </div>
  """
  controller: [ '$scope', '$element', '$attrs', ($scope, $element, $attrs) ->
      
    w = angular.element $window

    $scope.visibleElements = ()->
      if $element.width() > 460
        return 3
      return 1

    $scope.setItemWidth = ()->
      $element.width() / $scope.visibleElements() 

    $scope.itemWidth =  $scope.setItemWidth()
    $scope.panesWidth = undefined
    $scope.panes = []
    $scope.position = 0;
    $scope.currentPaneIndex = 0

    $scope.isNextArrowVisible = ()->
      ($scope.panes.length - $scope.currentPaneIndex) > $scope.visibleElements()
    
    $scope.next = ()->
      $scope.position = $scope.position - $scope.itemWidth
      $scope.currentPaneIndex = $scope.currentPaneIndex + 1
    $scope.prev = ()->
      $scope.position = $scope.position + $scope.itemWidth
      $scope.currentPaneIndex = $scope.currentPaneIndex - 1
    
    $scope.setPanesWrapperWidth = ()->
      $scope.panesWidth = ( $scope.panes.length * $scope.itemWidth ) 
      $scope.position = 0;
      $scope.currentPaneIndex = 0

    w.bind 'resize', ()->
        
      $scope.itemWidth = $scope.setItemWidth();
      $scope.setPanesWrapperWidth()
      for pane in $scope.panes
        pane.scope.setWidth $scope.itemWidth
      $scope.$apply()

    ctrl = @
    ctrl.registerPane = (scope, element)->
      # Set the proper width for the element
      scope.setWidth $scope.itemWidth
        
      pane =
        'scope': scope
        'element': element

      $scope.panes.push pane
      $scope.setPanesWrapperWidth()

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
  transclude: true 
  template: """
      <div ng-transclude></div>
  """
  link: ($scope, $element, $attrs, $ctrl) ->

    $log.debug "Going to add carousel pane with id #{$scope.$id} to carousel"
    $element.addClass "wl-carousel-item"
      
    $scope.setWidth = (size)->
      $element.css('width', "#{size}px")

    $scope.$on '$destroy', ()->
      $log.debug "Destroy #{$scope.$id}"
      $ctrl.unregisterPane $scope

    $ctrl.registerPane $scope, $element
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
# Set the well-known $ reference to jQuery.
$ = jQuery

# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.facetedsearch.widget', [ 'wordlift.ui.carousel', 'wordlift.utils.directives' ])
.provider("configuration", ()->
  
  _configuration = undefined
  
  provider =
    setConfiguration: (configuration)->
      _configuration = configuration
    $get: ()->
      _configuration

  provider
)
.filter('filterEntitiesByType', [ '$log', 'configuration', ($log, configuration)->
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
              <span style="background: url({{post.thumbnail}}) no-repeat center center; background-size: cover;"></span>
            </div>
            <div class="wl-card-title"> 
              <a ng-href="{{post.permalink}}">{{post.post_title}}</a>
            </div>
          </div>
        </div>
      </div>
  """

])

.controller('FacetedSearchWidgetController', [ 'DataRetrieverService', 'configuration', '$scope', '$log', (DataRetrieverService, configuration, $scope, $log)-> 

    $scope.entity = undefined
    $scope.posts = []
    $scope.facets = []
    $scope.conditions = {}
    $scope.entityLimit = 5

    # TODO Load dynamically 
    $scope.supportedTypes = [
      { 'scope' : 'what', 'types' : [ 'thing', 'creative-work' ] }
      { 'scope' : 'who', 'types' : [ 'person', 'organization', 'local-business' ] }
      { 'scope' : 'where', 'types' : [ 'place' ] }
      { 'scope' : 'when', 'types' : [ 'event' ] }
    ]
      
    $scope.configuration = configuration
    $scope.filteringEnabled = true

    $scope.toggleFacets = ()->
      $log.debug "Clicked!"
      $scope.configuration.attrs.show_facets = !$scope.configuration.attrs.show_facets

    $scope.isInConditions = (entity)->
      if $scope.conditions[ entity.id ]
        return true
      return false

    $scope.addCondition = (entity)->
      $log.debug "Add entity #{entity.id} to conditions array"

      if $scope.conditions[ entity.id ]
        delete $scope.conditions[ entity.id ]
      else
        $scope.conditions[ entity.id ] = entity
      
      DataRetrieverService.load( 'posts', Object.keys( $scope.conditions ) )

        
    $scope.$on "postsLoaded", (event, posts) -> 
      $log.debug "Referencing posts for item #{configuration.post_id} ..."
      $scope.posts = posts
      
    $scope.$on "facetsLoaded", (event, facets) -> 
      $log.debug "Referencing facets for item #{configuration.post_id} ..."
      $scope.facets = facets

])
# Retrieve post
.service('DataRetrieverService', [ 'configuration', '$log', '$http', '$rootScope', (configuration, $log, $http, $rootScope)-> 
  
  service = {}
  service.load = ( type, conditions = [] )->
    uri = "#{configuration.ajax_url}?action=#{configuration.action}&post_id=#{configuration.post_id}&type=#{type}"
    
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
.config([ 'configurationProvider', (configurationProvider)->
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
        <div class="wl-facets-container" ng-repeat="box in supportedTypes">
          <h6>{{box.scope}}</h6>
          <ul>
            <li class="entity" ng-repeat="entity in facets | orderBy:[ '-counter', '-createdAt' ] | filterEntitiesByType:box.types | limitTo:entityLimit" ng-click="addCondition(entity)">     
                <span class="wl-label" ng-class=" { 'selected' : isInConditions(entity) }">
                  <i class="wl-checkbox"></i>
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


