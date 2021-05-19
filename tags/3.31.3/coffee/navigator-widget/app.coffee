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


