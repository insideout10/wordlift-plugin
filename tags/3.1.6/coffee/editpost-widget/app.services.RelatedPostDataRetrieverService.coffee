angular.module('wordlift.editpost.widget.services.RelatedPostDataRetrieverService', [])
# Manage redlink analysis responses
.service('RelatedPostDataRetrieverService', [ 'configuration', '$log', '$http', '$rootScope', (configuration, $log, $http, $rootScope)-> 
  
  service = {}
  service.load = ( entityIds = [] )->
    uri = "admin-ajax.php?action=wordlift_related_posts&post_id=#{configuration.currentPostId}"
    $log.debug "Going to find related posts"
    $log.debug entityIds
    
    $http(
      method: 'post'
      url: uri
      data: entityIds
    )
    # If successful, broadcast an *analysisReceived* event.
    .success (data) ->
      $log.debug data
      $rootScope.$broadcast "relatedPostsLoaded", data
    .error (data, status) ->
      $log.warn "Error loading related posts"

  service

])