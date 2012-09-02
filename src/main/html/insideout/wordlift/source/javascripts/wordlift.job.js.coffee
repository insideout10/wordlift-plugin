$ = jQuery

app = angular.module "wordlift.job", []

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


angular.bootstrap $( "#wordlift-job" ), [ "wordlift.job" ]