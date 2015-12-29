angular.module('wordlift.editpost.widget.services.AnalysisService', [])
# Manage redlink analysis responses
.service('AnalysisService', [ 'configuration', '$log', '$http', '$rootScope', (configuration, $log, $http, $rootScope)-> 
	
  # Creates a unique ID of the specified length (default 8).
  uniqueId = (length = 8) ->
    id = ''
    id += Math.random().toString(36).substr(2) while id.length < length
    id.substr 0, length

  # Merges two objects by copying overrides param onto the options.
  merge = (options, overrides) ->
    extend (extend {}, options), overrides
  extend = (object, properties) ->
    for key, val of properties
      object[key] = val
    object
 
  findAnnotation = (annotations, start, end) ->
    return annotation for id, annotation of annotations when annotation.start is start and annotation.end is end

  service =
    _isRunning: false
    _currentAnalysis: undefined
    _supportedTypes: []
    _defaultType: "thing"
  
  service.cleanAnnotations = (analysis, positions = []) ->
    # Take existing entities as mandatory 
    for annotationId, annotation of analysis.annotations
      if annotation.start > 0 and annotation.end > annotation.start
        annotationRange = [ annotation.start..annotation.end ]
        # TODO Replace with an Array intersection check
        isOverlapping = false
        for pos in annotationRange
          if pos in positions
            isOverlapping = true
          break
        
        if isOverlapping
          $log.warn "Annotation with id: #{annotationId} start: #{annotation.start} end: #{annotation.end} overlaps an existing annotation"
          $log.debug annotation
          @.deleteAnnotation analysis, annotationId
        else 
          positions = positions.concat annotationRange 

    return analysis

  # Retrieve supported type from current classification boxes configuration
  for box in configuration.classificationBoxes
    for type in box.registeredTypes
      if type not in service._supportedTypes
        service._supportedTypes.push type

  service.createEntity = (params = {}) ->
    # Set the defalut values.
    defaults =
      id: 'local-entity-' + uniqueId 32
      label: ''
      description: ''
      mainType: 'thing' # DefaultType
      types: []
      images: []
      confidence: 1
      occurrences: []
      annotations: {}
    
    merge defaults, params
    
  # Delete an annotation from a given analyis and an annotationId
  service.deleteAnnotation = (analysis, annotationId)->

    $log.warn "Going to remove overlapping annotation with id #{annotationId}"
    
    if analysis.annotations[ annotationId ]?
      for ea, index in analysis.annotations[ annotationId ].entityMatches
        delete analysis.entities[ ea.entityId ].annotations[ annotationId ]
      delete analysis.annotations[ annotationId ]

    analysis

  service.createAnnotation = (params = {}) ->
    # Set the defalut values.
    defaults =
      id: 'urn:local-text-annotation-' + uniqueId 32
      text: ''
      start: 0
      end: 0
      entities: []
      entityMatches: []
    
    merge defaults, params

  service.parse = (data) ->
    
    # Add local entities
    # Add id to entity obj
    # Add id to annotation obj
    # Add occurences as a blank array
    # Add annotation references to each entity
    
    for id, localEntity of configuration.entities
      data.entities[ id ] = localEntity

    for id, entity of data.entities
      
      if not entity.label
        $log.warn "Label missing for entity #{id}"
      if not entity.description
        $log.warn "Description missing for entity #{id}"

      if not entity.sameAs
        $log.warn "sameAs missing for entity #{id}"
        entity.sameAs = []
        configuration.entities[ id ]?.sameAs = []
        $log.debug "Schema.org sameAs overridden for entity #{id}"
    
      if entity.mainType not in @._supportedTypes
        $log.warn "Schema.org type #{entity.mainType} for entity #{id} is not supported from current classification boxes configuration"
        entity.mainType = @._defaultType
        configuration.entities[ id ]?.mainType = @._defaultType
        $log.debug "Schema.org type overridden for entity #{id}"
        
      entity.id = id
      entity.occurrences = []
      entity.annotations = {}
      entity.confidence = 1 

    for id, annotation of data.annotations
      annotation.id = id
      annotation.entities = {}
      
      for ea, index in annotation.entityMatches
        
        if not data.entities[ ea.entityId ].label 
          data.entities[ ea.entityId ].label = annotation.text
          $log.debug "Missing label retrived from related annotation for entity #{ea.entityId}"

        data.entities[ ea.entityId ].annotations[ id ] = annotation
        data.annotations[ id ].entities[ ea.entityId ] = data.entities[ ea.entityId ]

    # TODO move this calculation on the server
    for id, entity of data.entities
      for annotationId, annotation of data.annotations
        local_confidence = 1
        for em in annotation.entityMatches  
          if em.entityId? and em.entityId is id
            local_confidence = em.confidence
        entity.confidence = entity.confidence * local_confidence
    
    data

  service.getSuggestedSameAs = (content)->
  
    promise = @._innerPerform content
    # If successful, broadcast an *sameAsReceived* event.
    .then (response) ->
      
      suggestions = []

      for id, entity of response.data.entities
        if id.startsWith('http')
          suggestions.push id
      
      $rootScope.$broadcast "sameAsRetrieved", suggestions
    
  service._innerPerform = (content)->

    $log.info "Start to performing analysis"

    return $http(
      method: 'post'
      url: ajaxurl + '?action=wordlift_analyze'
      data: content      
    )
  
  service._updateStatus = (status)->
    service._isRunning = status
    $rootScope.$broadcast "analysisServiceStatusUpdated", status

  service.perform = (content)->
    
    if service._currentAnalysis
      $log.warn "Analysis already runned! Nothing to do ..."
      return

    service._updateStatus true

    promise = @._innerPerform content
    # If successful, broadcast an *analysisPerformed* event.
    promise.then (response) ->
      # Store current analysis obj
      service._currentAnalysis = response.data
      $rootScope.$broadcast "analysisPerformed", service.parse( response.data )
    
    # On failure, broadcast an *analysisFailed* event.
    promise.catch (response) ->
      $log.error response.data
      $rootScope.$broadcast "analysisFailed", response.data
    
    # Update service running status in each case
    promise.finally (response) ->
      service._updateStatus false

  # Preselect entity annotations in the provided analysis using the provided collection of annotations.
  service.preselect = (analysis, annotations) ->

    $log.debug "Going to perform annotations preselection"
    # Find the existing entities in the html
    for annotation in annotations

      if annotation.start is annotation.end
        $log.warn "There is a broken empty annotation for entityId #{annotation.uri}"
        continue

      # Find the proper annotation  
      textAnnotation = findAnnotation analysis.annotations, annotation.start, annotation.end
      
      # If there is no textAnnotation then create it and add to the current analysis
      # It can be normal for new entities that are queued for Redlink re-indexing
      if not textAnnotation?
        $log.warn "Annotation #{annotation.start}:#{annotation.end} for entityId #{annotation.uri} misses in the analysis"
        textAnnotation = @createAnnotation({
          start: annotation.start
          end: annotation.end
          text: annotation.label
          })
        analysis.annotations[ textAnnotation.id ] = textAnnotation
        
      # Look for the entity in the current analysis result
      # Local entities are merged previously during the analysis parsing
      entity = analysis.entities[ annotation.uri ]
      for id, e of configuration.entities
        entity = analysis.entities[ e.id ] if annotation.uri in e.sameAs

      # If no entity is found we have a problem
      if not entity?
         $log.warn "Entity with uri #{annotation.uri} is missing both in analysis results and in local storage"
         continue
      # Enhance analysis accordingly
      analysis.entities[ entity.id ].occurrences.push  textAnnotation.id
      if not analysis.entities[ entity.id ].annotations[ textAnnotation.id ]?
        analysis.entities[ entity.id ].annotations[ textAnnotation.id ] = textAnnotation 
        analysis.annotations[ textAnnotation.id ].entityMatches.push { entityId: entity.id, confidence: 1 } 
        analysis.annotations[ textAnnotation.id ].entities[ entity.id ] = analysis.entities[ entity.id ]            
  
  service

])