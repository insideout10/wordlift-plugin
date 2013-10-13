$ = jQuery

angular
  .module( "wordlift.disambiguate", [])
  .controller "DisambiguationsController", ( $scope, $http, $timeout, $log ) ->

    # get the post ID.
    postID = $( "#post_ID" ).val()

    # initialize the disambiguations array.
    $scope.disambiguations = [];

    $scope.highlight = ( entity ) ->
      return if not tinyMCE?

      $( tinyMCE.get('content').dom.select(".textannotation") ).removeClass( "strong" )

      return if not entity?.textAnnotations?
      return if 0 is entity.textAnnotations.length

      $( tinyMCE.get('content').dom.select( "##{textAnnotation.replace(':','\\:')}" ) ).addClass( "strong" ) for textAnnotation in entity.textAnnotations
      $( tinyMCE.get('content').dom.select( '.mceContentBody' ) ).animate
        "scrollTop": $( tinyMCE.get('content').dom.select( "##{entity.textAnnotations[0].replace(':','\\:')}" ) ).position().top

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
          "postID": postID
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

    $scope.getSimpleName = ( about ) ->
        about.match( /.+\/(.+$)/ )[1]      

    # get the disambiguations.
    $scope.getDisambiguations = ->
      $http
        "method": "GET"
        "url": "admin-ajax.php"
        "params":
          "action": "wordlift.disambiguation"
          "postID": postID
      .success (data) ->
        $log.info data
        $scope.disambiguations = data
    # .error (data, status, headers, config) ->

    $scope.$on "job-complete", ->
      $scope.getDisambiguations()

    $scope.getDisambiguations()

    $scope.$watch "disambiguations", ->

      if tinyMCE?.get( "content" )?
        content = tinyMCE.get( "content" ).getContent()
      else
        content = $( "#content" ).html()

      # remove existing annotations.
      content = content.replace( /<span .*? typeof="http:\/\/fise.iks-project.eu\/ontology\/TextAnnotation">(.*?)<\/span>/g, '$1' )

      for disambiguation in $scope.disambiguations
        for textAnnotation in disambiguation.textAnnotations
          selectionHead = textAnnotation.selectionHead
            .replace( '\(', '\\(' )
            .replace( '\)', '\\)' )
          selectionTail = textAnnotation.selectionTail
            .replace( '\(', '\\(' )
            .replace( '\)', '\\)' )
          # regexp = new RegExp( "(#{selectionHead})(<[^>]*>[\\W\\D]*)*(#{textAnnotation.selectedText})(<[^>]*>[\\W\\D]*)*(#{selectionTail})" )
          regexp = new RegExp( "(\\W)(#{textAnnotation.selectedText})(\\W)(?![^>]*\")" )
          replace = "$1<span id=\"#{textAnnotation.about}\" class=\"textannotation\"
           typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\"
           about=\"#{textAnnotation.about}\">$2</span>$3"
          content = content.replace( regexp, replace )

      if tinyMCE?.get( "content" )?
        isDirty = tinyMCE.get( "content").isDirty()
        tinyMCE.get( "content").setContent( content )
        tinyMCE.get( "content").isNotDirty = 1 if not isDirty
      else
        $( "#content" ).html( content )

      $( tinyMCE.get( "content").dom.select('.textannotation') ).toggleClass('highlight') if tinyMCE?


  .controller "JobController", ($scope, $rootScope, $http, $timeout ) ->

    # $scope.state = "click to analyze"

    postID = $( "#post_ID" ).val()

    $scope.getJob = ->
      $http
        "method": "GET"
        "url": "admin-ajax.php"
        "params":
          "action": "wordlift.job"
          "postID": postID
      .success (data, status, headers, config) ->
        $scope.state = if data.jobState? then data.jobState else "click to analyze"
      .error (data, status, headers, config) ->
        # console.log( status )

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


$ angular.bootstrap $( "#wordlift-disambiguate" ), [ "wordlift.disambiguate" ]
