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

    $rootScope.$on 'EditorService.annotationClick', (event, id) ->
      console.log "Ops!! Element with id #{id} was clicked!"
      findEntitiesForAnnotation(id)

    # Intersection
    intersection = (a, b) ->
      [a, b] = [b, a] if a.length > b.length
      value for value in a when value in b

    # Find all text annotation for the current analyzed text
    findAllAnnotations = () ->
      textAnnotations = currentAnalysis['@graph'].filter (item) ->
        'enhancer:TextAnnotation' in item['@type'] and item['enhancer:selection-prefix']?
      $rootScope.$broadcast 'AnnotationService.annotations', textAnnotations

    # Find all Entities for a certain text annotation identified by 'annotationId'
    findEntitiesForAnnotation = (annotationId) ->
      console.log "Going to find entities for annotation with ID #{annotationId}"

      entityAnnotations = currentAnalysis['@graph'].filter (item) ->
        'enhancer:EntityAnnotation' in item['@type'] and item['dc:relation'] == annotationId
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

      $rootScope.$broadcast 'AnnotationService.entityAnnotations', entityAnnotations

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