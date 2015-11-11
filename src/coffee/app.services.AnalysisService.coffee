# The AnalysisService aim is to parse the Analysis response from an analysis process
# and create a data structure that's is suitable for displaying in the UI.
# The main method of the AnalysisService is parse. The parse method includes some
# helpful functions.
# The return is a structure like this:
#  * language : the language code for the specified post.
#  * languages: an array of languages (and related confidence) identified for the provided text.
#  * entities : the list of entities for the post, each entity provides:
#     * label      : the label in the post language.
#     * description: the description in the post language.
#     * type       : the known type for the entity
#     * types      : a list of types as provided by the entity
#     * thumbnails : URL to thumbnail images

angular.module('AnalysisService', ['wordlift.tinymce.plugin.services.EntityService', 'wordlift.tinymce.plugin.services.Helpers', 'LoggerService'])
.service('AnalysisService',
    [ 'EntityAnnotationService', 'EntityService', 'Helpers', 'LoggerService', 'TextAnnotationService', '$filter', '$http', '$q',
      '$rootScope', '$log',
      (EntityAnnotationService, EntityService, h, logger, TextAnnotationService, $filter, $http, $q, $rootScope, $log) ->

        service =
          _knownTypes: []
          _entities: {}
        # Holds the analysis promise, used to abort the analysis.
          promise: undefined

        # If true, an analysis is running.
          isRunning: false


        # Add an entity to the local collection of entities.
        service.addEntity = (entity) ->
          @_entities[entity.id] = entity

        # Set the local entity collection.
        service.setEntities = (entities) ->
          @_entities = entities
        # Get the local entity collection.
        service.getEntities = () ->
          @_entities

        # Set the known types.
        service.setKnownTypes = (types) ->
          @_knownTypes = types
          $rootScope.$broadcast CONFIGURATION_TYPES_EVENT, types
          @_knownTypes
          
        # Get the known types.
        service.getKnownTypes = () ->
          @_knownTypes

        # Abort a running analysis.
        service.abort = ->
          # Abort the analysis if an analysis is running and there's a reference to its promise.
          @promise.resolve() if @isRunning and @promise?

        # Enhance analysis with a new text annotation
        service.addTextAnnotation = (analysis, textAnnotation)->
          analysis.textAnnotations[textAnnotation.id] = textAnnotation
          analysis
        
        # Create a fake analysis
        service.createAnEmptyAnalysis = ()->
          {
          language: ''
          entities: {}
          entityAnnotations: {}
          textAnnotations: {}
          languages: []
          }

        # Enhance analysis with a new entity annotation
        service.enhance = (analysis, textAnnotation, entityAnnotation)->
          
          # Look for an existing entityAnnotation for the current uri
          entityAnnotations = EntityAnnotationService.find textAnnotation.entityAnnotations, uri: entityAnnotation.entity.id
          if 0 is entityAnnotations.length
            # Add the current entity to the current analysis
            analysis.entities[entityAnnotation.entity.id] = entityAnnotation.entity
            # Unflag selected entityAnnotations for the current textAnnotation
            for id, ea of textAnnotation.entityAnnotations
              ea.selected = false
            # Flag the current entityAnnotation as selected
            entityAnnotation.selected = true          
            # Add the current entityAnnotation to the current analysis
            analysis.entityAnnotations[entityAnnotation.id] = entityAnnotation
            # Add a reference to the current textAnnotation
            textAnnotation.entityAnnotations[entityAnnotation.id] = analysis.entityAnnotations[entityAnnotation.id]
            # Return true
            return true
          # Return false 
          false

        # Preselect entity annotations in the provided analysis using the provided collection of annotations.
        service.preselect = (analysis, annotations) ->

          # Find the existing entities in the html
          for annotation in annotations
            textAnnotation = TextAnnotationService.findOrCreate analysis.textAnnotations, annotation
            entityAnnotations = EntityAnnotationService.find textAnnotation.entityAnnotations, uri: annotation.uri
            if 0 < entityAnnotations.length
              # We don't expect more than one entity annotation for an URI inside a text annotation.
              entityAnnotations[0].selected = true
            else
              # Retrieve entity from analysis or from the entity storage if needed
              entities = EntityService.find analysis.entities, uri: annotation.uri
              entities = EntityService.find @_entities, uri: annotation.uri if 0 is entities.length

              # If the entity is missing skip the current text annotation
              if 0 is entities.length
                $log.warn "Missing entity in window.wordlift.entities collection!"
                $log.info annotation
                continue

              # Use the first found entity
              analysis.entities[annotation.uri] = entities[0]
              # Create the new entityAssociation
              ea = EntityAnnotationService.create
                label: annotation.label
                confidence: 1
                entity: analysis.entities[annotation.uri]
                relation: analysis.textAnnotations[textAnnotation.id]
                selected: true

              analysis.entityAnnotations[ea.id] = ea
              # Add a reference to the current textAssociation
              textAnnotation.entityAnnotations[ea.id] = analysis.entityAnnotations[ea.id]

        # <a name="analyze"></a>
        # Analyze the provided content. Only one analysis at a time is run.
        # The merge parameter is passed to the parse call and merges together entities related via sameAs.
        service.analyze = (content, merge = false) ->
          # dump "AnalysisService.analyze [ content :: #{content} ][ is running :: #{@isRunning} ][ merge :: #{merge} ]"
          # Exit if an analysis is already running.
          return if service.isRunning

          # Set that an analysis is running.
          service.isRunning = true

          # Store the promise in the class to allow interrupting the request.
          service.promise = $q.defer()

          $http(
            method: 'post'
            url: ajaxurl + '?action=wordlift_analyze'
            data: content
            timeout: service.promise.promise
          )
          # If successful, broadcast an *analysisReceived* event.
          .success (data) ->
              $rootScope.$broadcast ANALYSIS_EVENT, service.parse(data, merge)
              # Set that the analysis is complete.
              service.isRunning = false

          .error (data, status) ->
              # Set that the analysis is complete.
              service.isRunning = false
              $rootScope.$broadcast ANALYSIS_EVENT, undefined

              return if 0 is status # analysis aborted.
              $rootScope.$broadcast 'error', 'An error occurred while requesting an analysis.'

        # Parse the response data from the analysis request (Redlink).
        # If *merge* is set to true, entity annotations and entities with matching sameAs will be merged.
        service.parse = (data, merge = false) ->
          languages = []
          textAnnotations = {}
          entityAnnotations = {}
          entities = {}

          createLanguage = (item) ->
            {
            code: h.get "#{DCTERMS}language", item, context
            confidence: h.get FISE_ONT_CONFIDENCE, item, context
            _item: item
            }

          # Check that the response is valid.
          if not ( data[CONTEXT]? and data[GRAPH]? )
            $rootScope.$broadcast 'error', 'The analysis response is invalid. Please try again later.'
            return false

          # data is split in a context and a graph.
          context = data[CONTEXT]
          graph = data[GRAPH]

          for item in graph
            id = item['@id']
            #        console.log "[ id :: #{id} ]"

            types = item['@type']
            dctype = h.get "#{DCTERMS}type", item, context

#            console.log "[ id :: #{id} ][ dc:type :: #{dctype} ]"

            # TextAnnotation/LinguisticSystem
#            console.log "[ FISE_ONT_TEXT_ANNOTATION :: #{FISE_ONT_TEXT_ANNOTATION} ][ DCTERMS :: #{DCTERMS} ]"
            if h.containsOrEquals(FISE_ONT_TEXT_ANNOTATION, types, context) and h.containsOrEquals("#{DCTERMS}LinguisticSystem", dctype, context)
              # dump "language [ id :: #{id} ][ dc:type :: #{dctype} ]"
              languages.push createLanguage(item)

              # TextAnnotation
            else if h.containsOrEquals(FISE_ONT_TEXT_ANNOTATION, types, context)
              #          $log.debug "TextAnnotation [ @id :: #{id} ][ types :: #{types} ]"
              textAnnotations[id] = item

              # EntityAnnotation
            else if h.containsOrEquals(FISE_ONT_ENTITY_ANNOTATION, types, context)
              #          $log.debug "EntityAnnotation [ @id :: #{id} ][ types :: #{types} ]"
              entityAnnotations[id] = item

              # Entity
            else
              #          $log.debug "Entity [ @id :: #{id} ][ types :: #{types} ]"
              entities[id] = item

          # sort the languages by confidence.
          languages.sort (a, b) ->
            if a.confidence < b.confidence
              return -1
            if a.confidence > b.confidence
              return 1
            0

          # create a reference to the default language.
          language = languages[0].code

          # Create entities instances in the entities array.
          entities[id] = EntityService.create(item, language, service._knownTypes, context) for id, item of entities

          # Cycle in every entity.
          logger.debug "AnalysisService : merge", { entity: entity, entities: entities }
          EntityService.merge(entity, entities) for id, entity of entities if merge
          EntityService.merge(entity, entities) for id, entity of @_entities if merge

          # Create text annotation instances.
          textAnnotations[id] = TextAnnotationService.build(item, context) for id, item of textAnnotations

          # Create entity annotations instances.
          for id, item of entityAnnotations
            entityAnnotations[entityAnnotation.id] = entityAnnotation for entityAnnotation in EntityAnnotationService.build(item, language, entities, textAnnotations, context)

          # For every text annotation delete entity annotations that refer to the same entity (after merging).
          if merge
            # Cycle in text annotations.
            for textAnnotationId, textAnnotation of textAnnotations
              # Cycle in entity annotations.
              for id, entityAnnotation of textAnnotation.entityAnnotations
                #            console.log "[ text-annotation id :: #{textAnnotationId} ][ entity-annotation id :: #{entityAnnotation.id} ]"
                # Check if there are entity annotations referring to the same entity, and if so, delete it.
                for anotherId, anotherEntityAnnotation of textAnnotation.entityAnnotations when id isnt anotherId and entityAnnotation.entity is anotherEntityAnnotation.entity
                  #              console.log "[ id :: #{id} ][ another id :: #{anotherId} ]"
                  delete textAnnotation.entityAnnotations[anotherId]

          # return the analysis result.
          {
          language: language
          entities: entities
          entityAnnotations: entityAnnotations
          textAnnotations: textAnnotations
          languages: languages
          }

        # Return the service instance
        service
    ])
