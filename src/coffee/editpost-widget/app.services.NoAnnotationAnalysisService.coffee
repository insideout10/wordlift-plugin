angular.module('wordlift.editpost.widget.services.NoAnnotationAnalysisService', [
  'wordlift.editpost.widget.services.AnnotationParser',
  'wordlift.editpost.widget.services.EditorAdapter',
])
# Manage redlink analysis responses
# @since 1.0.0
  .service('AnalysisService', ['AnnotationParser', 'EditorAdapter', 'configuration', '$log', '$http', '$rootScope', '$q'
  (AnnotationParser, EditorAdapter, configuration, $log, $http, $rootScope, $q)->

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

    service =
      _isRunning: false
      _currentAnalysis: undefined
      _supportedTypes: []
      _defaultType: "thing"

    service.cleanAnnotations = (analysis, positions = []) ->
      # inactive, since no annotations will be present.

    if configuration.classificationBoxes?
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
        mainType: '' # No DefaultType
        types: []
        images: []
        confidence: 1
        occurrences: []
        annotations: {}

      merge defaults, params

    # Delete an annotation from a given analyis and an annotationId
    service.deleteAnnotation = (analysis, annotationId)->
      $log.warn "Going to remove overlapping annotation with id #{annotationId}"

      if analysis.annotations[annotationId]?
        for ea, index in analysis.annotations[annotationId].entityMatches
          delete analysis.entities[ea.entityId].annotations[annotationId]
        delete analysis.annotations[annotationId]

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
      $log.debug 'Parsing data...', data
      for id, entity of data.entities
        entity.id = id
#        # This is not necessary anymore because Analysis_Response_Ops (in PHP) populates it.
        entity.occurrences = [] if not entity.occurrences?
        entity.annotations = {} if not entity.annotations?
        # See #550: the confidence is set by the server.
        # entity.confidence = 1

      for id, annotation of data.annotations
        annotation.id = id
        annotation.entities = {}
        for ea, index in data.annotations[id].entityMatches

          if not data.entities[ea.entityId]?
            $log.warn "#{ea.entityId} not found in `entities`, skipping."
            continue

          data.entities[ea.entityId].annotations = {} if not data.entities[ea.entityId].annotations?
          data.entities[ea.entityId].annotations[id] = annotation
          data.annotations[id].entities[ea.entityId] = data.entities[ea.entityId]
      $log.debug 'Parsed data: ', data

      data

    service.getSuggestedSameAs = (content)->
      # do nothing

    service._innerPerform = (content, annotations = [])->

      # Set the data as two parameters, content and annotations.
      data = { content: content, annotations: annotations, contentType: 'text/html', version: Traslator.version }

      if (wlSettings?)
        if (wlSettings.language?) then data.contentLanguage = wlSettings.language
        # We set the current entity URI as exclude from the analysis results.
        #
        # See https://github.com/insideout10/wordlift-plugin/issues/345
        if (wlSettings.itemId?) then data.exclude = [wlSettings.itemId]
        # Set the scope according to the user capability.
        if @canCreateEntities then data.scope = 'all' else data.scope = 'local'

      return $q( (resolve, reject) ->
        wp.ajax.post( 'wl_analyze', {
          _wpnonce: wlSettings['analysis']['_wpnonce'],
          data: JSON.stringify( data ),
          postId: wlSettings['post_id']
        })
          .done( ( response ) -> resolve( response ) )
          .fail( ( response ) -> reject( response ) )
      )

    service._updateStatus = (status)->
      service._isRunning = status
      $rootScope.$broadcast "analysisServiceStatusUpdated", status

    service.perform = (content)->
      if service._currentAnalysis
        $log.warn "Analysis already run! Nothing to do ..."
        service._updateStatus false

        return

      service._updateStatus true

      # Get the existing annotations in the text.
      # annotations = AnnotationParser.parse(EditorAdapter.getHTML())

      $log.debug 'Requesting analysis...'

      promise = @._innerPerform content, {}
      # If successful, broadcast an *analysisPerformed* event.
      promise.then (response) ->
        data = response
        # Store current analysis obj
        service._currentAnalysis = data

        result = service.parse(data)
        $rootScope.$broadcast "analysisPerformed", result
        wp.wordlift.trigger 'analysis.result', result

      # On failure, broadcast an *analysisFailed* event.
      promise.catch (response) ->
        $log.error response.data
        $rootScope.$broadcast "analysisFailed", response.data

      # Update service running status in each case
      promise.finally (response) ->
        service._updateStatus false


    # Set the scope according to the user permissions.
    #
    # See https://github.com/insideout10/wordlift-plugin/issues/561
    service.canCreateEntities = wlSettings['can_create_entities']? and 'yes' is wlSettings['can_create_entities']

    service

])
