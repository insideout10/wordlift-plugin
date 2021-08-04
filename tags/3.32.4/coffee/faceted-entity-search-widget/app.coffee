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


