angular.module('wordlift.editpost.widget.services.AnalysisService', [
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
            @.deleteAnnotation analysis, annotationId
          else
            positions = positions.concat annotationRange

      return analysis

    # Retrieve supported type from current classification boxes configuration
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

# Add local entities
# Add id to entity obj
# Add id to annotation obj
# Add occurences as a blank array
# Add annotation references to each entity

# TMP ... Should be done on WLS side
#      unless data.topics?
#        data.topics = []
#      dt = @._defaultType

#      if data.topics?
#        data.topics = data.topics.map (topic)->
#          topic.id = topic.uri
#          topic.occurrences = []
#          topic.mainType = dt
#          topic

#      $log.debug "Found #{Object.keys(configuration.entities).length} entities in configuration...", configuration

      # This isn't needed anymore as it is delegated to the WP analysis end-point to merge disambiguated entities.
#      for id, localEntity of configuration.entities
#        data.entities[id] = localEntity

      for id, entity of data.entities

# Remove the current entity from the proposed entities.
#
# See https://github.com/insideout10/wordlift-plugin/issues/437
# See https://github.com/insideout10/wordlift-plugin/issues/345
#        if configuration.currentPostUri is id
#          delete data.entities[id]
#          continue

#        if not entity.label
#          $log.warn "Label missing for entity #{id}"
#
#        if not entity.description
#          $log.warn "Description missing for entity #{id}"

#        if not entity.sameAs
#          $log.warn "sameAs missing for entity #{id}"
#          entity.sameAs = []
#          configuration.entities[id]?.sameAs = []
#          $log.debug "Schema.org sameAs overridden for entity #{id}"

#        if entity.mainType not in @._supportedTypes
#          $log.warn "Schema.org type #{entity.mainType} for entity #{id} is not supported from current classification boxes configuration"
#          entity.mainType = @._defaultType
#          configuration.entities[id]?.mainType = @._defaultType
#          $log.debug "Schema.org type overridden for entity #{id}"

        entity.id = id
#        # This is not necessary anymore because Analysis_Response_Ops (in PHP) populates it.
        entity.occurrences = [] if not entity.occurrences?
        entity.annotations = {} if not entity.annotations?
        # See #550: the confidence is set by the server.
        # entity.confidence = 1

      for id, annotation of data.annotations
        annotation.id = id
        annotation.entities = {}

        # Filter out annotations that don't have a corresponding entity. The entities list might be filtered, in order
        # to remove the local entity.
        #  data.annotations[id].entityMatches = (ea for ea in annotation.entityMatches when ea.entityId of data.entities)

        # Remove the annotation if there's no entity matches left.
        #
        # See https://github.com/insideout10/wordlift-plugin/issues/437
        # See https://github.com/insideout10/wordlift-plugin/issues/345
        # if 0 is data.annotations[id].entityMatches.length
        #   delete data.annotations[id]
        #   continue

        for ea, index in data.annotations[id].entityMatches

          # if not data.entities[ea.entityId].label
          #   data.entities[ea.entityId].label = annotation.text
          #   $log.debug "Missing label retrieved from related annotation for entity #{ea.entityId}"

          if not data.entities[ea.entityId]?
            $log.warn "#{ea.entityId} not found in `entities`, skipping."
            continue

          data.entities[ea.entityId].annotations = {} if not data.entities[ea.entityId].annotations?
          data.entities[ea.entityId].annotations[id] = annotation
          data.annotations[id].entities[ea.entityId] = data.entities[ea.entityId]

#      # TODO move this calculation on the server
#      for id, entity of data.entities
#        for annotationId, annotation of data.annotations
#          local_confidence = 1
#          for em in annotation.entityMatches
#            if em.entityId? and em.entityId is id
#              local_confidence = em.confidence
#          entity.confidence = entity.confidence * local_confidence

      $log.debug 'Parsed data: ', data

      data

    service.getSuggestedSameAs = (content)->
      promise = @._innerPerform content
# If successful, broadcast an *sameAsReceived* event.
        .then (response) ->
      suggestions = []

      for id, entity of response.data.entities

        if matches = id.match /^https?\:\/\/([^\/?#]+)(?:[\/?#]|$)/i
          suggestions.push {
            id: id
            label: entity.label
            mainType: entity.mainType
            source: matches[1]
          }
      $log.debug suggestions
      $rootScope.$broadcast "sameAsRetrieved", suggestions

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
          data: JSON.stringify( data )
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

      # remove bookmarks from the content.
      content = content.replaceAll /<span.+?class="mce_SELRES_start.+?><\/span>/gm, ''
      content = content.replaceAll /<span.+?class="mce_SELRES_end.+?><\/span>/gm, ''
      $log.debug 'Requesting analysis...'

      promise = @._innerPerform content, {}
      # If successful, broadcast an *analysisPerformed* event.
      promise.then (response) ->
        data = response

#        # Catch wp_json_send_error responses.
#        if response.data.success? and !response.data.success
#          # Yes `data.data`, the first one to get the body of the response, the
#          # second for the body internal structure.
#          $rootScope.$broadcast "analysisFailed", response.data.data.message
#          return

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

    # Preselect entity annotations in the provided analysis using the provided collection of annotations.
    service.preselect = (analysis, annotations) ->

      $log.debug "Selecting #{annotations.length} entity annotation(s)..."

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

          $log.warn "Text annotation #{annotation.start}:#{annotation.end} for entityId #{annotation.uri} misses in the analysis"

          textAnnotation = @createAnnotation({
            start: annotation.start
            end: annotation.end
            text: annotation.label
            # The css class of the original text annotation (now removed from the
            # body. The css class is useful because we store there the `wl-no-link`
            # class.
            cssClass: annotation.cssClass if annotation.cssClass?
          })
          analysis.annotations[textAnnotation.id] = textAnnotation

        # Look for the entity in the current analysis result
        # Local entities are merged previously during the analysis parsing
        entity = analysis.entities[annotation.uri]
        for id, e of configuration.entities
          entity = analysis.entities[e.id] if annotation.uri in e.sameAs

        # If no entity is found we have a problem
        if not entity?
          $log.warn "Entity with uri #{annotation.uri} is missing both in analysis results and in local storage"
          continue
        # Enhance analysis accordingly
        analysis.entities[entity.id].occurrences.push textAnnotation.id
        if not analysis.entities[entity.id].annotations[textAnnotation.id]?
          analysis.entities[entity.id].annotations[textAnnotation.id] = textAnnotation
          analysis.annotations[textAnnotation.id].entityMatches.push {entityId: entity.id, confidence: 1}
          analysis.annotations[textAnnotation.id].entities[entity.id] = analysis.entities[entity.id]

    # Set the scope according to the user permissions.
    #
    # See https://github.com/insideout10/wordlift-plugin/issues/561
    service.canCreateEntities = wlSettings['can_create_entities']? and 'yes' is wlSettings['can_create_entities']

    service

])
