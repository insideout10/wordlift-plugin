# Set the well-known $ reference to jQuery.
$ = jQuery

# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.navigator.widget', [ 'wordlift.ui.carousel', 'wordlift.utils.directives' ])
.provider("configuration", ()->
  
  _configuration = undefined
  
  provider =
    setConfiguration: (configuration)->
      _configuration = configuration
    $get: ()->
      _configuration

  provider
)

.controller('NavigatorWidgetController', [ 'DataRetrieverService', 'configuration', '$scope', '$log', (DataRetrieverService, configuration, $scope, $log)-> 

    $scope.items = []
    $scope.configuration = configuration
        
    $scope.$on "itemsLoaded", (event, items) -> 
      $log.debug "Rertieved items for post #{configuration.post_id} ..."
      $log.debug items
      $scope.items = items
      
])
# Retrieve post
.service('DataRetrieverService', [ 'configuration', '$log', '$http', '$rootScope', (configuration, $log, $http, $rootScope)-> 
  
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
.config([ 'configurationProvider', (configurationProvider)->
  configurationProvider.setConfiguration window.wl_navigator_params
])

$(
  container = $("""
  	<div ng-controller="NavigatorWidgetController" ng-show="items.length > 0">
      <div class="wl-posts">
        <div wl-carousel>
          <div class="wl-post wl-card" ng-repeat="item in items" wl-carousel-pane>
            <div class="wl-card-image"> 
              <img ng-src="{{item.post.thumbnail}}" />
            </div>
            <div class="wl-card-title"> 
              <a ng-href="{{item.post.permalink}}">{{item.post.title}}</a>
            </div>
            <div class="wl-card-subtitle"> 
              <a ng-href="{{item.entity.permalink}}">{{item.entity.label}}</a>
            </div>
          </div>
        </div>
  
      </div>
     
    </div>
  """)
  .appendTo('.wl-navigator-widget')

injector = angular.bootstrap $('.wl-navigator-widget'), ['wordlift.navigator.widget'] 
injector.invoke(['DataRetrieverService', '$rootScope', '$log', (DataRetrieverService, $rootScope, $log) ->
  # execute the following commands in the angular js context.
  $rootScope.$apply(->    
    DataRetrieverService.load() 
  )
])

)


