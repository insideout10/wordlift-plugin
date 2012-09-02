$ = jQuery

angular.module( "wordlift.disambiguate", [])
  .controller "DisambiguationsController", ( $scope, $http ) ->

    # get the post ID.
    postID = $( "#post_ID" ).val()

    # initialize the disambiguations array.
    $scope.disambiguations = [];

    $scope.clearAndBind = ( disambiguation, entity ) ->
      disambiguation.working = true
      method = if entity.selected then "DELETE" else "POST"

      $http
        "method": method
        "url": "admin-ajax.php"
        "params":
          "action": "wordlift.disambiguations"
          "entity": entity.about
        "data":
          "clear": disambiguation.textAnnotations
          "bind": entity.textAnnotations
      .success (data) ->
        disambiguationEntity.selected = false for disambiguationEntity in disambiguation.entities
        entity.selected = true if "POST" is method
        disambiguation.working = false
      .error ->
        disambiguation.working = false



    $scope.getTypeName = ( typeURI ) ->
        typeURI.match( /.+\/(.+$)/ )[1].toLowerCase()

      # get the disambiguations.
      $scope.getDisambiguations = ->
        $http
          "method": "GET"
          "url": "admin-ajax.php"
          "params":
            "action": "wordlift.disambiguation"
            "postID": postID
        .success (data) ->
          $scope.disambiguations = data
      # .error (data, status, headers, config) ->

    $scope.$on "job-complete", ->
      $scope.getDisambiguations()

    $scope.getDisambiguations()


  .controller "JobController", ($scope, $rootScope, $http, $timeout ) ->

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


angular.bootstrap $( "#wordlift-disambiguate" ), [ "wordlift.disambiguate" ]