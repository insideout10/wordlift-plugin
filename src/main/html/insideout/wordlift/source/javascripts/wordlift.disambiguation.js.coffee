$ = jQuery

app = angular.module "wordlift.metabox", []

app.filter "confidence", ->
  ( entities, confidence) ->
    return if not entities?
    entity for entity in entities when entity.confidence is confidence

app.filter "related", ->
  ( entities, parent ) ->
    return if not entities?
    entity for entity in entities when entity.texts.some (text) -> ~parent.texts.indexOf( text )

app.controller "EntitiesCtrl", ($scope, $http, $filter ) ->

  postID = $( "#post_ID" ).val()

  # http://localizeme.dyndns.org/wordlift/wp-admin/

  images =
    "http://schema.org/Place": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_462.svg",
    "http://schema.org/Organization": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_921.svg",
    "http://schema.org/CreativeWork": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_2572.svg",
    "http://schema.org/Person": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_419%202.svg"

  $scope.getEntities = ->
    $http
      "method": "GET"
      "url": "admin-ajax.php",
      "params":
        "action": "wordlift.dump"
        "postID": postID
    .success (data) ->
      $scope.entities = data

  $scope.getImageURL = (type) ->
    images[ type ]

  $scope.bindEntity = ( entity, box ) ->
    box.binding = true

    method = if entity.selected then "DELETE" else "POST"

    $http
      "method": method
      "url": "admin-ajax.php"
      "params":
        "action": "wordlift.post/entities"
        "postID": postID
        "entity": entity.entity
      "data":
        "texts": entity.texts
    .success (data, status, headers, config) ->
      e.selected = false for e in $filter("related")( $scope.entities, box )
      entity.selected = ( method is "POST" )
      box.binding = false

    .error (data, status, headers, config) ->
      box.binding = false

  $scope.getEntities()
  $scope.$on "job-complete", ->
    console.log( "job-complete" )
    $scope.getEntities()


app.controller "JobCtrl", ($scope, $rootScope, $http, $timeout ) ->

  postID = $( "#post_ID" ).val()

  $scope.getJob = ->
    $http
      "method": "GET"
      "url": "admin-ajax.php"
      "params":
        "action": "wordlift.job"
        "postID": postID
    .success (data, status, headers, config) ->
      $scope.state = data.jobState
    .error (data, status, headers, config) ->
      console.log( status )

  $scope.postJob = ->
    $scope.state = "running"

    $http
      "method": "POST"
      "url": "admin-ajax.php"
      "params":
        "action": "wordlift.job"
        "postID": postID
    .success (data, status, headers, config) ->
      $scope.state = "running"
    .error (data, status, headers, config) ->
      $scope.state = "error"


  $scope.isRunning = ->
    "running" is $scope.state

  $scope.$watch "state", ->
    $scope.watchJob() if $scope.isRunning()

  $scope.watchJob = ->
    return $rootScope.$broadcast( "job-complete" ) if not $scope.isRunning()

    $timeout( ->
      $scope.getJob()
      $scope.watchJob()
    , 5000 )


  $scope.getJob()

#app.controller "AnnotationCtrl", ($scope, $http ) ->
#
#  postID = $( "#post_ID" ).val()
#
#  # http://localizeme.dyndns.org/wordlift/wp-admin/
#
#  $http
#    "method": "GET"
#    "url": "admin-ajax.php",
#    "params":
#      "action": "wordlift.dump"
#      "postID": postID
#  .success (data) ->
#    $scope.annotations= data.annotations
#    $scope.entities = data.entities
#
#  images =
#    "http://schema.org/Place": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_462.svg",
#    "http://schema.org/Organization": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_921.svg",
#    "http://schema.org/CreativeWork": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_2572.svg",
#    "http://schema.org/Person": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_419%202.svg"
#
#  $scope.getImageURL = (type) ->
#    images[ type ]
#
#  $scope.bindEntityToPost = ( annotation, entity ) ->
#    annotation.binding = true
#
#    method = if entity.selected then "DELETE" else "POST"
#
#    $http
#      "method": method
#      "url": "admin-ajax.php"
#      "params":
#        "action": "wordlift.post/entities"
#        "postID": 8633
#        "entity": entity.entityReference
#        "textAnnotation": annotation.subject
#    .success (data, status, headers, config) ->
#      e.selected = false for e in $scope.entities when e.relation is annotation.subject
#      entity.selected = true if method is "POST"
#      annotation.binding = false
#
#    .error (data, status, headers, config) ->
#      annotation.binding = false

angular.bootstrap $( "#wordlift-metabox" ), [ "wordlift.metabox" ]