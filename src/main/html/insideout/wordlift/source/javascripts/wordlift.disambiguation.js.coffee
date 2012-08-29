$ = jQuery

app = angular.module "wordlift.disambiguation", []

app.controller "AnnotationCtrl", ($scope, $http ) ->

  postID = $( "#post_ID" ).val()

  $http
    "method": "GET"
    "url": "admin-ajax.php",
    "params":
      "action": "wordlift.dump"
      "postID": postID
  .success (data) ->
    $scope.annotations= data.annotations
    $scope.entities = data.entities

  images =
    "http://schema.org/Place": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_462.svg",
    "http://schema.org/Organization": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_921.svg",
    "http://schema.org/CreativeWork": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_2572.svg",
    "http://schema.org/Person": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_419%202.svg"

  $scope.getImageURL = (type) ->
    images[ type ]

  $scope.bindEntityToPost = ( annotation, entity ) ->
    annotation.binding = true

    method = if entity.selected then "DELETE" else "POST"

    $http
      "method": method
      "url": "admin-ajax.php"
      "params":
        "action": "wordlift.post/entities"
        "postID": 8633
        "entity": entity.entityReference
        "textAnnotation": annotation.subject
    .success (data, status, headers, config) ->
      e.selected = false for e in $scope.entities when e.relation is annotation.subject
      entity.selected = true if method is "POST"
      annotation.binding = false

    .error (data, status, headers, config) ->
      annotation.binding = false

angular.bootstrap $( "#wordlift-disambiguation" ), [ "wordlift.disambiguation" ]