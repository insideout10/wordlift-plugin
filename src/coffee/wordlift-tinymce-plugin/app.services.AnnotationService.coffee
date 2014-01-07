angular.module('wordlift.tinymce.plugin.services.AnnotationService', ['wordlift.tinymce.plugin.config'])
  .service('AnnotationService', ['$rootScope', '$http', ($rootScope, $http) ->

    currentAnalysis = {}
    supportedTypes = [
      'schema:Place'
      'schema:Event'
      'schema:CreativeWork'
      'schema:Product'
      'schema:Person'
      'schema:Organization'
    ]

    # this event is raised when a text annotation is clicked in the editor.
    # the id parameter contains the text annotation unique id.
    $rootScope.$on 'EditorService.annotationClick', (event, id, elem) -> findEntitiesForAnnotation id, elem

    # Intersection
    intersection = (a, b) ->
      [a, b] = [b, a] if a.length > b.length
      value for value in a when value in b

    # Find all text annotation for the current analyzed text.
    findAllAnnotations = () ->
      textAnnotations = currentAnalysis['@graph'].filter (item) ->
        'enhancer:TextAnnotation' in item['@type'] and item['enhancer:selection-prefix']?
      $rootScope.$broadcast 'AnnotationService.annotations', textAnnotations

    # Find all Entities for a certain text annotation identified by 'annotationId'
    # @param string annotationId The text annotation id.
    # @param element elem The element source of the event.
    findEntitiesForAnnotation = (annotationId, elem) ->
      # filter the graph, find all entities related to the specified text annotation id.
      entityAnnotations = currentAnalysis['@graph'].filter (item) ->
        'enhancer:EntityAnnotation' in item['@type'] and item['dc:relation'] is annotationId
      # Enhance entity annotations ..
      entityAnnotations = entityAnnotations.map (item) ->

        if item['enhancer:entity-type']
          i = intersection(supportedTypes, item['enhancer:entity-type'])
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
        url: ajaxurl
        params:
          action: 'wordlift_analyze'
        data: content
      .success (data, status, headers, config) ->
          # Set type
          currentAnalysis = data
          findAllAnnotations()
      true
  ])