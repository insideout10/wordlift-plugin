angular.module('wordlift.tinymce.plugin.services.AnnotationService', ['wordlift.tinymce.plugin.config'])
  .service('AnnotationService', ['$log', '$rootScope', '$http', 'AnalysisService', 'Configuration', ($log, $rootScope, $http, AnalysisService, Configuration) ->

#
#    currentAnalysis = {}
#
#    # this event is raised when a text annotation is clicked in the editor.
#    # the id parameter contains the text annotation unique id.
##    $rootScope.$on 'textAnnotationClicked', (event, id, elem) -> findEntitiesForAnnotation id, elem
#
#    # Intersection
#    intersection = (a, b) ->
#      [a, b] = [b, a] if a.length > b.length
#      value for value in a when value in b
#
#    # Find all text annotation for the current analyzed text.
#    foo = () ->
#      console.log "foo"
#    findAllAnnotations = () ->
#      $log.info "AnnotationService: finding annotations ..."
#
#      textAnnotations = currentAnalysis['@graph'].filter (item) ->
#        item['@type']? and Configuration.entityLabels.textAnnotation in item['@type'] and item[Configuration.entityLabels.selectionPrefix]?
#      $rootScope.$broadcast 'AnnotationService.annotations', textAnnotations

    # Call redlink api trough Wp Ajax bridge in order to perform the semantic analysis
    analyze: (content) ->
      $http
        method: 'POST'
#        url: '/wp-content/plugins/wordlift/tests/english.json' # ajaxurl
        url: ajaxurl
        params:
          action: 'wordlift_analyze'
        data: content
      .success (data, status, headers, config) ->
          # broadcast the analysis to the application.
          $rootScope.$broadcast 'analysisReceived', AnalysisService.parse data

  ])