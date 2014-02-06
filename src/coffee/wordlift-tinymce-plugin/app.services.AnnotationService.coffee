angular.module('wordlift.tinymce.plugin.services.AnnotationService', ['wordlift.tinymce.plugin.config'])
  .service('AnnotationService', ['$log', '$rootScope', '$http', 'AnalysisService', 'Configuration', ($log, $rootScope, $http, AnalysisService, Configuration) ->

    currentAnalysis = {}

    # this event is raised when a text annotation is clicked in the editor.
    # the id parameter contains the text annotation unique id.
    $rootScope.$on 'EditorService.annotationClick', (event, id, elem) -> findEntitiesForAnnotation id, elem

    # Intersection
    intersection = (a, b) ->
      [a, b] = [b, a] if a.length > b.length
      value for value in a when value in b

    # Find all text annotation for the current analyzed text.
    foo = () ->
      console.log "foo"
    findAllAnnotations = () ->
      $log.info "AnnotationService: finding annotations ..."

      textAnnotations = currentAnalysis['@graph'].filter (item) ->
        item['@type']? and Configuration.entityLabels.textAnnotation in item['@type'] and item[Configuration.entityLabels.selectionPrefix]?
      $rootScope.$broadcast 'AnnotationService.annotations', textAnnotations

    # Find all Entities for a certain text annotation identified by 'annotationId'
    # @param string annotationId The text annotation id.
    # @param element elem The element source of the event.
    findEntitiesForAnnotation = (annotationId, elem) ->
      $log.debug "Going to find entities for #{annotationId}"
      
      # filter the graph, find all entities related to the specified text annotation id.
      entityAnnotations = currentAnalysis['@graph'].filter (item) ->
        item['@type']? and Configuration.entityLabels.entityAnnotation in item['@type'] and item[Configuration.entityLabels.relation] is annotationId
      # Enhance entity annotations ..
      $log.debug "Entity annotation/s before supported types filtering"
      $log.debug entityAnnotations

      entityAnnotations = entityAnnotations.map (item) ->
        if item[Configuration.entityLabels.entityType]
          i = intersection(Configuration.supportedTypes, item[Configuration.entityLabels.entityType])
          item['wordlift:supportedTypes'] = i.map (type) ->
            "http://schema.org/#{type.replace(/schema:/,'')}"
          item['wordlift:cssClasses'] = i.map (type) ->
            "#{type.replace(/schema:/,'')}".toLowerCase()
          item['wordlift:cssClasses'] = item['wordlift:cssClasses'].join(' ')
        else
          item['wordlift:supportedTypes'] = []
          item['wordlift:cssClasses'] = ''

        item
      # Retrieve related entities ids
      # entityIds = entityAnnotations.map (entityAnnotation) ->
      #  entityAnnotation['enhancer:entity-reference']
      # Retrieve related entities
      # entities = currentAnalysis['@graph'].filter (item) ->
      #  item['@id'] in entityIds

      $rootScope.$broadcast 'AnnotationService.entityAnnotations', entityAnnotations, elem

    # Call redlink api trough Wp Ajax bridge in order to perform the semantic analysis
    analyze: (content) ->
      $http
        method: 'POST'
        url: '/wp-content/plugins/wordlift/tests/english.json' # ajaxurl
        params:
          action: 'wordlift_analyze'
        data: content
      .success (data, status, headers, config) ->

          AnalysisService.parse data

          # Set type
          currentAnalysis = data
          findAllAnnotations()
          true
  ])