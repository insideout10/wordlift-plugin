class Traslator

  # Hold the html and textual positions.
  _htmlPositions: []
  _textPositions: []

  # Hold the html and text contents.
  _html: ''
  _text: ''

  decodeHtml = (html)-> 
    txt = document.createElement("textarea")
    txt.innerHTML = html
    txt.value

  # Create an instance of the traslator.
  @create: (html) ->
    traslator = new Traslator(html)
    traslator.parse()
    traslator

  constructor: (html) ->
    @_html = html

  parse: ->
    @_htmlPositions = []
    @_textPositions = []
    @_text = ''

    # OLD pattern = /([^<]*)(<[^>]*>)([^<]*)/gim
    pattern = /([^&<>]*)(&[^&;]*;|<[^>]*>)([^&<>]*)/gim
     
    textLength = 0
    htmlLength = 0

    while match = pattern.exec @_html

      # Get the text pre/post and the html element
      htmlPre = match[1]
      htmlElem = match[2]
      htmlPost = match[3]

      # Get the text pre/post w/o new lines.
      textPre = htmlPre + (if '</p>' is htmlElem.toLowerCase() then '\n\n' else '')
#      dump "[ htmlPre length :: #{htmlPre.length} ][ textPre length :: #{textPre.length} ]"
      textPost = htmlPost

      # Sum the lengths to the existing lengths.
      textLength += textPre.length

      if /^&[^&;]*;$/gim.test htmlElem
       textLength += 1

      # For html add the length of the html element.
      htmlLength += htmlPre.length + htmlElem.length

      # Add the position.
      @_htmlPositions.push htmlLength
      @_textPositions.push textLength

      textLength += textPost.length
      htmlLength += htmlPost.length

      htmlProcessed = ''
      if /^&[^&;]*;$/gim.test htmlElem
        htmlProcessed = decodeHtml htmlElem

      # Add the textual parts to the text.
      @_text += textPre + htmlProcessed + textPost


    # In case the regex didn't find any tag, copy the html over the text.
    @_text = new String(@_html) if '' is @_text and '' isnt @_html

    # Add text position 0 if it's not already set.
    if 0 is @_textPositions.length or 0 isnt @_textPositions[0]
      @_htmlPositions.unshift 0
      @_textPositions.unshift 0

#    console.log '=============================='
#    console.log @_html
#    console.log @_text
#    console.log @_htmlPositions
#    console.log @_textPositions
#    console.log '=============================='

  # Get the html position, given a text position.
  text2html: (pos) ->
    htmlPos = 0
    textPos = 0

    for i in [0...@_textPositions.length]
      break if pos < @_textPositions[i]
      htmlPos = @_htmlPositions[i]
      textPos = @_textPositions[i]

    #    dump "#{htmlPos} + #{pos} - #{textPos}"
    htmlPos + pos - textPos

  # Get the text position, given an html position.
  html2text: (pos) ->
#    dump @_htmlPositions
#    dump @_textPositions

    # Return 0 if the specified html position is less than the first HTML position.
    return 0 if pos < @_htmlPositions[0]

    htmlPos = 0
    textPos = 0

    for i in [0...@_htmlPositions.length]
      break if pos < @_htmlPositions[i]
      htmlPos = @_htmlPositions[i]
      textPos = @_textPositions[i]

#    console.log "#{textPos} + #{pos} - #{htmlPos}"
    textPos + pos - htmlPos

  # Insert an Html fragment at the specified location.
  insertHtml: (fragment, pos) ->

#    dump @_htmlPositions
#    dump @_textPositions
#    console.log "[ fragment :: #{fragment} ][ pos text :: #{pos.text} ]"

    htmlPos = @text2html pos.text

    @_html = @_html.substring(0, htmlPos) + fragment + @_html.substring(htmlPos)

    # Reparse
    @parse()

  # Return the html.
  getHtml: ->
    @_html

  # Return the text.
  getText: ->
    @_text
window.Traslator = Traslator
# Constants
CONTEXT = '@context'
GRAPH = '@graph'
VALUE = '@value'

ANALYSIS_EVENT = 'analysisReceived'
CONFIGURATION_TYPES_EVENT = 'configurationTypesLoaded'

RDFS = 'http://www.w3.org/2000/01/rdf-schema#'
RDFS_LABEL = "#{RDFS}label"
RDFS_COMMENT = "#{RDFS}comment"

FREEBASE = 'freebase'
FREEBASE_COM = "http://rdf.#{FREEBASE}.com/"
FREEBASE_NS = "#{FREEBASE_COM}ns/"
FREEBASE_NS_DESCRIPTION = "#{FREEBASE_NS}common.topic.description"

SCHEMA_ORG = 'http://schema.org/'
SCHEMA_ORG_DESCRIPTION = "#{SCHEMA_ORG}description"

FISE_ONT = 'http://fise.iks-project.eu/ontology/'
FISE_ONT_ENTITY_ANNOTATION = "#{FISE_ONT}EntityAnnotation"
FISE_ONT_TEXT_ANNOTATION = "#{FISE_ONT}TextAnnotation"
FISE_ONT_CONFIDENCE = "#{FISE_ONT}confidence"

DCTERMS = 'http://purl.org/dc/terms/'

DBPEDIA = 'dbpedia'
DBPEDIA_ORG = "http://#{DBPEDIA}.org/"
DBPEDIA_ORG_REGEX = "http://(\\w{2}\\.)?#{DBPEDIA}.org/"

WORDLIFT = 'wordlift'

WGS84_POS = 'http://www.w3.org/2003/01/geo/wgs84_pos#'

# Define some constants for commonly used strings.
EDITOR_ID = 'content'
TEXT_ANNOTATION = 'textannotation'
CONTENT_IFRAME = '#content_ifr'
RUNNING_CLASS = 'running'
MCE_WORDLIFT = '.mce_wordlift, .mce-wordlift button'
CONTENT_EDITABLE = 'contenteditable'
TEXT_HTML_NODE_TYPE = 3

DEFAULT_ENTITY_ANNOTATION_CONFIDENCE_LEVEL = 1.0

angular.module('wordlift.tinymce.plugin.config', [])
#	.constant 'Configuration',
#		supportedTypes: [
#			'schema:Place'
#			'schema:Event'
#			'schema:CreativeWork'
#			'schema:Product'
#			'schema:Person'
#			'schema:Organization'
#		]
#		entityLabels:
#			'entityLabel': 'enhancer:entity-label'
#			'entityType': 'enhancer:entity-type'
#			'entityReference': 'enhancer:entity-reference'
#			'textAnnotation': 'enhancer:TextAnnotation'
#			'entityAnnotation': 'enhancer:EntityAnnotation'
#			'selectionPrefix': 'enhancer:selection-prefix'
#			'selectionSuffix': 'enhancer:selection-suffix'
#			'selectedText': 'enhancer:selected-text'
#			'confidence': 'enhancer:confidence'
#			'relation':	'dc:relation'
#      'entityLabel':      'entity-label'
#      'entityType':       'entity-type'
#      'entityReference':  'entity-reference'
#      'textAnnotation':   'TextAnnotation'
#      'entityAnnotation': 'EntityAnnotation'
#      'selectionPrefix':  'selection-prefix'
#      'selectionSuffix':  'selection-suffix'
#      'selectedText':     'selected-text'
#      'confidence':       'confidence'
#      'relation':	        'relation'
angular.module('wordlift.directives.wlEntityProps', [])
.directive('wlEntityProps', ->
    restrict: 'E'
    scope:
      textAnnotations: '='
    template: """
      <div class="wl-entity-props" ng-repeat="textAnnotation in textAnnotations">
        <div ng-repeat="ea in textAnnotation.entityAnnotations | filterObjectBy:'selected':true">
          <div ng-repeat="(k, ps) in ea.entity.props">
            <input ng-repeat="p in ps" name="wl_props[{{ea.entity.id}}][{{k}}][]" ng-value="p" type="text" />
          </div>
        </div>
      </div>
    """
  )
angular.module('wordlift.tinymce.plugin.directives', ['wordlift.directives.wlEntityProps','wordlift.tinymce.plugin.controllers'])
# The wlEntities directive provides a UI for disambiguating the entities for a provided text annotation.
.directive('wlEntities', ->
    # Restrict the directive to elements only (<wl-entities text-annotation="..."></wl-entities>)
    restrict: 'E'
    # Create a separate scope
    scope:
    # Get the text annotation from the text-annotation attribute.
      textAnnotation: '='
      onSelect: '&'
    # Create the link function in order to bind to children elements events.
    link: (scope, element, attrs) ->

      scope.select = (item) ->

        # Set the selected flag on each annotation.
        for id, entityAnnotation of scope.textAnnotation.entityAnnotations
          # The selected flag is set to false for each annotation which is not the selected one.
          # For the selected one is set to true only if the entity is not selected already, otherwise it is deselected.
          entityAnnotation.selected = item.id is entityAnnotation.id && !entityAnnotation.selected

        # Call the select function with the textAnnotation and the selected entityAnnotation or null.
        scope.onSelect
          textAnnotation: scope.textAnnotation
          entityAnnotation: if item.selected then item else null

    template: """
      <div>
        <ul>
          <li ng-repeat="entityAnnotation in textAnnotation.entityAnnotations | orderObjectBy:'confidence':true">
            <wl-entity on-select="select(entityAnnotation)" entity-annotation="entityAnnotation"></wl-entity>
          </li>
        </ul>
      </div>
    """
  )
# The wlEntity directive shows a tile for a provided entityAnnotation. 
# When a tile is clicked the function provided in the select attribute is called.
.directive('wlEntity', ['$log','$compile', ($log, $compile)->
    restrict: 'E'
    scope:
      entityAnnotation: '='
      onSelect: '&'
    # Create the link function in order to bind to children elements events.
    link: (scope, element, attrs) ->
      # Holds a reference to the current entity 
      scope.entity = scope.entityAnnotation?.entity

      template = """
        <div class="entity {{entityAnnotation.entity.css}}" ng-class="{selected: true==entityAnnotation.selected}" ng-click="onSelect()" ng-show="entity.label">
          <div class="thumbnail" ng-show="entity.thumbnail" title="{{entity.id}}" ng-attr-style="background-image: url({{entity.thumbnail}})"></div>
          <div class="thumbnail empty" ng-hide="entity.thumbnail" title="{{entity.id}}"></div>
          <div class="confidence" ng-bind="entityAnnotation.confidence"></div>
          <div class="label" ng-bind="entity.label"></div>
          <div class="#{scope.entity?.css}-info url" entity="entity"></div>
          <div class="type"></div>
          <div class="source" ng-class="entity.source" ng-bind="entity.source"></div>     
        </div>
      """

      element.html(template).show();
      $compile(element.contents())(scope);

  ])
.directive('wlEventInfo', ['$interpolate', ($interpolate)->
    # Restrict the usage to the class attribute
    restrict: 'C'
    scope:
      entity: '='
    # Create the link function in order to bind to children elements events.
    link: (scope, element, attrs) ->

      # TODO ...
      scope.startDate = scope.entity?.props['http://www.w3.org/2002/12/cal#dtstart']?[0]
      scope.endDate = scope.entity?.props['http://www.w3.org/2002/12/cal#dtend']?[0]
      scope.place = scope.entity?.props['http://www.w3.org/2006/vcard/ns#locality']
      
      scope.renderDate = () ->
#        console.log scope.startDate
        return scope.startDate if scope.startDate is scope.endDate
        return $interpolate('{{startDate}} - {{endDate}}',false, null, true)(scope)

    template: """
      <span class="place" ng-bind="place"></span> <span class="date" ng-bind="renderDate()" title="{{renderDate()}}"></span>
    """
  ])

# The wlEntityInputBoxes prints the inputs and textareas with entities data.
.directive('wlEntityInputBoxes', ->
    restrict: 'E'
    scope:
      textAnnotations: '='
    template: """
      <div class="wl-entity-input-boxes" ng-repeat="textAnnotation in textAnnotations">
        <div ng-repeat="entityAnnotation in textAnnotation.entityAnnotations | filterObjectBy:'selected':true">

          <input type='text' name='wl_entities[{{entityAnnotation.entity.id}}][uri]' value='{{entityAnnotation.entity.id}}'>
          <input type='text' name='wl_entities[{{entityAnnotation.entity.id}}][label]' value='{{entityAnnotation.entity.label}}'>
          <textarea name='wl_entities[{{entityAnnotation.entity.id}}][description]'>{{entityAnnotation.entity.description}}</textarea>

          <input type='text' name='wl_entities[{{entityAnnotation.entity.id}}][main_type]' value='{{entityAnnotation.entity.type}}'>

          <input ng-repeat="type in entityAnnotation.entity.types" type='text'
          	name='wl_entities[{{entityAnnotation.entity.id}}][type][]' value='{{type}}'>

          <input ng-repeat="image in entityAnnotation.entity.thumbnails" type='text'
            name='wl_entities[{{entityAnnotation.entity.id}}][image][]' value='{{image}}'>
          <input ng-repeat="sameAs in entityAnnotation.entity.sameAs" type='text'
            name='wl_entities[{{entityAnnotation.entity.id}}][sameas][]' value='{{sameAs}}'>

          <input type='text' name='wl_entities[{{entityAnnotation.entity.id}}][latitude]' value='{{entityAnnotation.entity.latitude}}'>
          <input type='text' name='wl_entities[{{entityAnnotation.entity.id}}][longitude]' value='{{entityAnnotation.entity.longitude}}'>

        </div>
      </div>
    """
  )
.directive('autocomplete', ['$compile', '$q', '$log', ($compile, $q, $log) ->
    restrict: "A",
    scope:
      source: '&'
      onSelect: '&'
    link: (originalScope, elem, attrs, ctrl) ->
      templateHtml = '<wl-entity on-select="select(entityAnnotation)" entity-annotation="entityAnnotation"></wl-entity>'

      elem.autocomplete
        source: (request, response) ->
          locals = { $viewValue: request.term }
          $q.when(originalScope.source(locals)).then (matches) ->
            response matches
        minLength: 3
        open: () ->
          originalScope.$emit('autocompleteOpened')
        close: () ->
          originalScope.$emit('autocompleteClosed')
      
      .data("ui-autocomplete")._renderItem = (ul, ea) ->
        
        scope = originalScope.$new();
        scope.entityAnnotation = ea
        
        scope.select = (entityAnnotation) ->
          # Set the higher priority for this item
          entityAnnotation.confidence = 1.0
          # Reset autocomplete field & hide results
          angular.element(elem).val('')
          angular.element(ul).hide()
          originalScope.$emit('autocompleteClosed')
      
          # Call the onSelect callback
          originalScope.onSelect
            entityAnnotation: entityAnnotation
         
        originalScope.$on '$destroy', ()->
          scope.$destroy();
        el = angular.element(templateHtml)
        compiled = $compile(el)

        $("<li>").append(el).appendTo(ul)
        compiled(scope)
      
  ])


angular.module('LoggerService', ['wordlift.tinymce.plugin.services.Helpers'])
.service('LoggerService', [ '$log', ($log) ->

    # Prepare the service instance.
    service = {}

    # Parse the function name.
    getFunctionName = (caller) ->
      switch match = /function ([^(]*)/i.exec caller.toString()
        when null then 'unknown'
        else
          if '' is match[1] then 'anonymous' else match[1]

    ###*
     * Log an information.
     *
     * @param {string} The message to log.
     ###
    service.debug = (message, params) ->
      $log.debug "#{getFunctionName(arguments.callee.caller)} - #{message}"

      if params?
        ($log.debug "[ #{key} :: "; $log.debug value; $log.debug "]") for key, value of params

    # return the service
    service
  ])

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

angular.module('wordlift.tinymce.plugin.services.EditorService', ['wordlift.tinymce.plugin.config', 'AnalysisService', 'LoggerService'])
.service('EditorService',
    ['AnalysisService', 'EntityService', 'EntityAnnotationService', 'LoggerService', 'TextAnnotationService', '$rootScope', '$log', (AnalysisService, EntityService, EntityAnnotationService, logger, TextAnnotationService, $rootScope, $log) ->

      editor = ->
        tinyMCE.get(EDITOR_ID)

      # Find existing entities selected in the html content (by looking for *itemid* attributes).
      findEntities = (html) ->

        # Prepare a traslator instance that will traslate Html and Text positions.
        traslator = Traslator.create html

        # Set the pattern to look for *itemid* attributes.
        pattern = /<(\w+)[^>]*\sitemid="([^"]+)"[^>]*>([^<]+)<\/\1>/gim

        # Get the matches and return them.
        (while match = pattern.exec html
#          console.log "findEntities [ html index :: #{match.index} ][ text index :: #{traslator.html2text match.index} ]"
          {
            start: traslator.html2text match.index
            end: traslator.html2text (match.index + match[0].length)
            uri: match[2]
            label: match[3]
          }
        )

      # Define the EditorService.
      service =
        # Create a textAnnotation starting from the current selection
        createTextAnnotationFromCurrentSelection: ()->
          # A reference to the editor.
          ed = editor()
          # If the current selection is collapsed / blank, then nothing to do
          if ed.selection.isCollapsed()
            $log.warn "Invalid selection! The text annotation cannot be created"
            return 
          # Retrieve the selected text
          # Notice that toString() method of browser native selection obj is used
          text = "#{ed.selection.getSel()}"
          # Create the text annotation
          textAnnotation = TextAnnotationService.create { 
            text: text
          }
          # Prepare span wrapper for the new text annotation
          textAnnotationSpan = "<span id=\"#{textAnnotation.id}\" class=\"#{TEXT_ANNOTATION}\">#{ed.selection.getContent()}</span>"
          # Update the content within the editor
          ed.selection.setContent(textAnnotationSpan)
          # Retrieve the current heml content
          content = ed.getContent({format: "html"})
          # Create a Traslator instance
          traslator =  Traslator.create content
          # Retrieve the index position of the new span
          htmlPosition = content.indexOf(textAnnotationSpan);
          # Detect the coresponding text position
          textPosition = traslator.html2text(htmlPosition)
          
          # Set start & end text annotation properties
          textAnnotation.start = textPosition 
          textAnnotation.end = textAnnotation.start + text.length
          
          $log.debug "New text annotation created!"
          $log.debug textAnnotation
          
          # Send a message about the new textAnnotation.
          $rootScope.$broadcast 'textAnnotationAdded', textAnnotation

        # Create an analysis obj representing disambiguated entities in the editor text
        createDefaultAnalysis: ()->

          # A reference to the editor.
          ed = editor()
          # Get the TinyMCE editor html content.
          html = ed.getContent format: 'raw'
          # Create an empty analysis analysis
          analysis = AnalysisService.createAnEmptyAnalysis()
          # Hold a reference to local entity storage
          entities = AnalysisService.getEntities()
          # For each entity detected in the editor text ...
          for inTextEntity in findEntities(html)
            # Retrieve related entity obj from the storage
            localEntities = EntityService.find entities, uri: inTextEntity.uri              
            # Check if the current text annotation has its coresponding entity within wordlift.entities local storage
            if localEntities.length > 0
              # Add a text annotation to the analysis
              ta = TextAnnotationService.findOrCreate analysis.textAnnotations, inTextEntity
              # Create an entity annotation 
              ea = EntityAnnotationService.create { 'entity': localEntities[0] }
              # Enhance current analysis properly 
              AnalysisService.enhance(analysis, ta, ea)
            else
              $log.warn "Missing entity in wordlift.entities collection matching text annotation #{inTextEntity.uri}" 
              $log.debug inTextEntity

          # Fire analysis to controller 
          $rootScope.$broadcast ANALYSIS_EVENT, analysis
          # Return the analysis
          analysis

        # Embed the provided analysis in the editor.
        embedAnalysis: (analysis) =>
          #return true
          # A reference to the editor.
          ed = editor()
          # Get the TinyMCE editor html content.
          html = ed.getContent format: 'raw'
          # Find existing entities.
          entities = findEntities html

          # Preselect entities found in html.
          AnalysisService.preselect analysis, entities

          # Remove existing text annotations (the while-match is necessary to remove nested spans).
          while html.match(/<(\w+)[^>]*\sclass="textannotation[^"]*"[^>]*>([^<]+)<\/\1>/gim, '$2')
            html = html.replace(/<(\w+)[^>]*\sclass="textannotation[^"]*"[^>]*>([^<]+)<\/\1>/gim, '$2')

          # Prepare a traslator instance that will traslate Html and Text positions.
          traslator = Traslator.create html

          # Add text annotations to the html (skip those text annotations that don't have entity annotations).
          for textAnnotationId, textAnnotation of analysis.textAnnotations when 0 < Object.keys(textAnnotation.entityAnnotations).length

            # Start the element.
            element = "<span id=\"#{textAnnotationId}\" class=\"#{TEXT_ANNOTATION}"

            # Insert the Html fragments before and after the selected text.
            entityAnnotations = EntityAnnotationService.find textAnnotation.entityAnnotations, selected: true
            if 0 < entityAnnotations.length and entityAnnotations[0].entity?
              # We deal only with the first entityAnnotation.
#              console.log entityAnnotations[0] if not entityAnnotations[0].entity
              entity = entityAnnotations[0].entity
              element += " highlight #{entity.css}\" itemid=\"#{entity.id}"

            # Close the element.
            element += '">'

            # Finally insert the HTML code.
#            console.log textAnnotation
            traslator.insertHtml element, text: textAnnotation.start
            traslator.insertHtml '</span>', text: textAnnotation.end


#          $log.info "embedAnalysis\n[ pre html :: #{html} ]\n[ post html :: #{traslator.getHtml()} ]\n[ text :: #{traslator.getText()} ]"

          # Update the editor Html code.
          isDirty = ed.isDirty()
          ed.setContent traslator.getHtml(), format: 'raw'
          ed.isNotDirty = not isDirty

      # <a name="analyze"></a>
      # Send the provided content for analysis using the [AnalysisService.analyze](app.services.AnalysisService.html#analyze) method.
        analyze: (content) ->
          # $log.info "EditorService.analyze [ content :: #{content} ]"
          # If the service is running abort the current request.
          return AnalysisService.abort() if AnalysisService.isRunning

          # Disable the button and set the spinner while analysis is running.
          $(MCE_WORDLIFT).addClass RUNNING_CLASS

          # Make the editor read-obly.
          editor().getBody().setAttribute CONTENT_EDITABLE, false

          # Call the [AnalysisService](AnalysisService.html) to analyze the provided content, asking to merge sameAs related entities.
          AnalysisService.analyze content, true

      # get the window position of an element inside the editor.
      # @param element elem The element.
        getWinPos: (textAnnotationId) ->
          # get a reference to the editor and its body
          ed = editor()
          # Calculate textAnnotation absolute position within the editor
          textAnnotationPos = ed.dom.getPos(textAnnotationId)
          # Return the coordinates.
          {
            top: $(CONTENT_IFRAME).offset().top - $('body').scrollTop() + textAnnotationPos.y - $(ed.getBody()).scrollTop()
            left: $(CONTENT_IFRAME).offset().left - $('body').scrollLeft() + textAnnotationPos.x - $(ed.getBody()).scrollLeft()
          }


      # Hook the service to the events. This event is captured when an entity is selected in the disambiguation popover.
      $rootScope.$on 'selectEntity', (event, args) ->

        # create a reference to the TinyMCE editor dom.
        dom = editor().dom

        # the element id containing the attributes for the text annotation.
        id = args.ta.id

        # Preset the stylesheet class.
        cls = TEXT_ANNOTATION

        # If an entity annotation is selected then prepare the values, otherwise set them null (i.e. remove).
        if args.ea?
          # Set a reference to the entity.
          entity = args.ea.entity
          cls +=  " highlight #{entity.css}"
          itemscope = 'itemscope'
          itemid = entity.id

          # Add the selected entity to the Analysis Service stored entities.
          AnalysisService.addEntity entity

        else
            itemscope = null
            itemid = null

        # Apply changes to the dom.
        dom.setAttrib id, 'class', cls
        dom.setAttrib id, 'itemscope', itemscope
        dom.setAttrib id, 'itemid', itemid

      # Receive annotations from the analysis (there is a mirror method in PHP for testing purposes, please try to keep
      # the two aligned - tests/functions.php *wl_embed_analysis* )
      # When an analysis is completed, remove the *running* class from the WordLift toolbar button.
      # (The button is set to running when [an analysis is called](#analyze).
      $rootScope.$on ANALYSIS_EVENT, (event, analysis) ->

        logger.debug "EditorService : Analysis Event", analysis: analysis

        service.embedAnalysis analysis if analysis? and analysis.textAnnotations?

        # Remove the *running* class.
        $(MCE_WORDLIFT).removeClass RUNNING_CLASS

        # Make the editor read/write.
        editor().getBody().setAttribute CONTENT_EDITABLE, true

      # Return the service definition.
      service
    ])

angular.module('wordlift.tinymce.plugin.services.EntityAnnotationService', [])
.service('EntityAnnotationService', [ 'EntityAnnotationConfidenceService', 'Helpers', 'LoggerService', (EntityAnnotationConfidenceService, h, LoggerService) ->
      
    service = {}

    # Create an entity annotation using the provided params.
    service.create = (params) ->

      defaults =
        id: 'uri:local-entity-annotation-' + h.uniqueId(32)
        label: ''
        confidence: EntityAnnotationConfidenceService.getDefault()
        entity: null
        relation: null
        selected: false
        _item: null

      # Copy over the label from the entity annotation if the label is not set on the entity.
      params.entity.label = params.label if params.entity? and not params.entity.label?

      # Merge the params with the default settings.
      entityAnnotation = h.merge defaults, params
      # Enhance confidence rate depending on related entity properties
      EntityAnnotationConfidenceService.enhanceConfidenceFor entityAnnotation

      entityAnnotation

    ###*
     * Create an entity annotation. An entity annotation is created for each related text-annotation.
     * @param {object} Entity raw data.
     * @param {string} The language code.
     * @return {array} An array of entity annotations.
     ###
    service.build = (item, language, entities, tas, context) ->
      # Get the reference to the entity.
      reference = h.get "#{FISE_ONT}entity-reference", item, context
      # If the referenced entity is not found, return null
      return [] if not entities[reference]?

      # Prepare the return array.
      annotations = []

      # get the related text annotation.
      relations = h.get "#{DCTERMS}relation", item, context
      # Ensure we're dealing with an array.
      relations = if angular.isArray relations then relations else [ relations ]

      # For each text annotation bound to this entity annotation, create an entity annotation and add it to the text annotation.
      for relation in relations
        textAnnotation = tas[relation]

        # Create an entity annotation.
        entityAnnotation = service.create
          id: h.get '@id', item, context
          label: h.getLanguage "#{FISE_ONT}entity-label", item, language, context
          confidence: h.get FISE_ONT_CONFIDENCE, item, context
          entity: entities[reference]
          relation: textAnnotation
          _item: item

        # Create a binding from the textannotation to the entity annotation.
        textAnnotation.entityAnnotations[entityAnnotation.id] = entityAnnotation if textAnnotation?

        # Accumulate the annotations.
        annotations.push entityAnnotation

      # Return the entity annotations.
      annotations

    # Find an entity annotation with the provided filters.
    service.find = (entityAnnotations, filter) ->
      if filter.uri?
        return (entityAnnotation for id, entityAnnotation of entityAnnotations when filter.uri is entityAnnotation.entity.id or filter.uri in entityAnnotation.entity.sameAs)

      if filter.selected?
        return (entityAnnotation for id, entityAnnotation of entityAnnotations when entityAnnotation.selected is filter.selected)


    # Return the service instance
    service
  ])
angular.module('wordlift.tinymce.plugin.services.EntityAnnotationConfidenceService', [])
.service('EntityAnnotationConfidenceService', [ 'EntityService','Helpers', '$log', (EntityService, h, $log) ->
  
  service = 
  	_entities: {}

  # Set the local entity collection.
  service.setEntities = (entities) ->
  	@_entities = entities

  service.getDefault = ()->
  	DEFAULT_ENTITY_ANNOTATION_CONFIDENCE_LEVEL

  # Enhanche entity annotation confidence following these criteria:
  # Add x if the related entity is described in more than 1 vocabulary
  # Add x if the related entity is described within Wordlift vocabulary
  # Add x if the related entity is described only within Wordlift vocabulary
  # Add x if the related entity is related to the current post 
  service.enhanceConfidenceFor = (entityAnnotation)->
    
    delta = 0
 	
    if entityAnnotation.entity.sources.length > 1
      delta += 0.20
    if WORDLIFT in entityAnnotation.entity.sources
      delta += 0.20
    if entityAnnotation.entity.source is WORDLIFT 
      delta += 1.0
    if EntityService.checkIfIsIncluded(@_entities, uri: entityAnnotation.entity.id) is true
      delta += 1.0

    $log.debug "Entity annotation #{entityAnnotation.id} enhancement: going to add #{delta} to confidence #{entityAnnotation.confidence}"
    entityAnnotation.confidence += delta
    $log.debug entityAnnotation
    entityAnnotation
    
  service
])
angular.module('wordlift.tinymce.plugin.services.EntityService', ['wordlift.tinymce.plugin.services.Helpers', 'LoggerService'])
.service('EntityService', [ 'Helpers', 'LoggerService', '$filter', (h, logger, $filter) ->
    service = {}

    # Find an entity in the provided entities collection using the provided filters.
    service.find = (entities, filter) ->
      if filter.uri?
        return (entity for entityId, entity of entities when filter.uri is entity?.id or filter.uri in entity?.sameAs)

    # Find first entity in the provided entities collection using the provided filters.
    service.checkIfIsIncluded = (entities, filter) ->
      entities = @find(entities, filter)
      if entities.length > 0 then return true else false

    ###*
     * Create an entity using the provided data and context.
     * @param {object} An item object containing the entity raw data.
     * @param {object} A context instance with prefix -> URL key-value pairs.
     * @return {object} An entity instance.
     ###
    service.create = (item, language, kt, context) ->
      # console.log "[ item :: #{item} ][ language :: #{language} ][ kt :: #{kt} ][ context :: #{context} ]"
      id = h.get '@id', item, context
      # Get the types expanding the type URI.
      types = h.get '@type', item, context, (ts) ->
        ts = if angular.isArray ts then ts else [ ts ]
        (h.expand(t, context) for t in ts)

      sameAs = h.get 'http://www.w3.org/2002/07/owl#sameAs', item, context
      sameAs = if angular.isArray sameAs then sameAs else [ sameAs ]

      fn = (values) ->
        values = if angular.isArray values then values else [ values ]
        for value in values
          match = /m\.(.*)$/i.exec value
          if null is match
            value
          else
            # If it's a Freebase URL normalize the link to the image.
            "https://usercontent.googleapis.com/#{FREEBASE}/v1/image/m/#{match[1]}?maxwidth=4096&maxheight=4096"

      # Get all the thumbnails; for each thumbnail execute the provided function.
      thumbnails = h.get ['http://xmlns.com/foaf/0.1/depiction',  "#{FREEBASE_NS}common.topic.image", "#{SCHEMA_ORG}image"], item, context, fn

      # Get the known types.
      #            $log.info "AnalysisService.parse [ known types :: "
      #            $log.info service
      #            $log.info service._knownTypes
      #            $log.info " ]"
      knownTypes = service.getKnownTypes types, kt, context
      # Get the stylesheet classes.
      css = knownTypes[0].type.css

      # create the entity model.
      entity =
        id: id
        thumbnail: if 0 < thumbnails.length then thumbnails[0] else null
        thumbnails: thumbnails
        css: css
        type: knownTypes[0].type.uri # This is the main type for the entity.
        types: types
        label: h.getLanguage RDFS_LABEL, item, language, context
        labels: h.get RDFS_LABEL, item, context
        sameAs: sameAs
        source: if id.match("^#{FREEBASE_COM}.*$")
          FREEBASE
        else if id.match("^#{DBPEDIA_ORG_REGEX}.*$")
          DBPEDIA
        else
          'wordlift'
        _item: item
        props: service.createProps item, context

      # Add sources as an array.
      entity.sources = [ entity.source ]

      entity.description = h.getLanguage [ RDFS_COMMENT, FREEBASE_NS_DESCRIPTION, SCHEMA_ORG_DESCRIPTION ], item, language, context
      entity.descriptions = h.get [ RDFS_COMMENT, FREEBASE_NS_DESCRIPTION, SCHEMA_ORG_DESCRIPTION ], item, context

      # Avoid null in entity description.
      entity.description = '' if not entity.description?

      entity.latitude = h.get "#{WGS84_POS}lat", item, context
      entity.longitude = h.get "#{WGS84_POS}long", item, context
      if 0 is entity.latitude.length or 0 is entity.longitude.length
        entity.latitude = ''
        entity.longitude = ''

      # Check if thumbnails exists.
      #        if thumbnails? and angular.isArray thumbnails
      #          $q.all(($http.head thumbnail for thumbnail in thumbnails))
      #            .then (results) ->
      #              # Populate the thumbnails array only with existing images (those that return *status code* 200).
      #              entity.thumbnails = (result.config.url for result in results when 200 is result.status)
      #              # Set the main thumbnail as the first.
      #              # TODO: use the lightest image as first.
      #              entity.thumbnail  = entity.thumbnails[0] if 0 < entity.thumbnails.length'

      # return the entity.
      #        console.log "createEntity [ entity id :: #{entity.id} ][ language :: #{language} ][ types :: #{types} ][ sameAs :: #{sameAs} ]"
      entity

    ###*
     * Merge the specified entity with the provided entities.
     *
     * @param {object} The entity to merge.
     * @param {object} A collection of entities to use for merging.
     *
     * @return {object} The merged entity.
     ###
    service.merge = (entity, entities) ->

      for sameAs in entity.sameAs
        if entities[sameAs]? and entities[sameAs] isnt entity

          existing = entities[sameAs]

          logger.debug "EntityService.merge : found a match [ entity 1 :: #{entity.id} ][ entity 2 :: #{existing.id} ]"

          h.mergeUnique entity.sameAs, existing.sameAs
          h.mergeUnique entity.thumbnails, existing.thumbnails
          h.mergeUnique entity.sources, existing.sources
          h.mergeUnique entity.types, existing.types

          entity.css = existing.css if not entity.css?
          entity.source = entity.sources.join(', ')
          # Prefer the DBpedia description.
          # TODO: have a user-set priority.
          entity.description = existing.description if DBPEDIA is existing.source
          entity.longitude = existing.longitude if DBPEDIA is existing.source and existing.longitude?
          entity.latitude = existing.latitude if DBPEDIA is existing.source and existing.latitude?

          # Delete the sameAs entity from the index.
          entities[sameAs] = entity
          service.merge entity, entities

      logger.debug "EntityService.merge [ id :: #{entity.id} ]", entity: entity

      entity


    ###*
     * Get the known type given the specified types.
     * @param {array} An array of types.
     * @param {object} An object representing the known types.
     * @return {object} The default type.
     ###
    service.getKnownTypes = (types, knownTypes, context) ->

      # An array with known types according to the specified types.
      returnTypes = []
      defaultType = undefined
      for kt in knownTypes
        # Set the default type, identified by an asterisk (*) in the sameAs values.
        defaultType = [
          { type: kt }
        ] if '*' in kt.sameAs
        # Get all the URIs associated to this known type.
        uris = kt.sameAs.concat kt.uri
        # If there is 1+ uri in common between the known types and the provided types, then add the known type.
        matches = (uri for uri in uris when h.containsOrEquals(uri, types, context))
        returnTypes.push { matches: matches, type: kt } if 0 < matches.length


      # Return the defaul type if not known types have been found.
      return defaultType if 0 is returnTypes.length

      # Sort and return the match types.
      $filter('orderBy') returnTypes, 'matches', true
      returnTypes

    ###*
     * Create a key-values pair of properties.
     * @param {object} An item object containing the entity raw data.
     * @param {object} A context instance with prefix -> URL key-value pairs.
     * @return {object} A key-values pair of entity properties.
     ###
    service.createProps = (item, context) ->
      # Initialize the props
      # console.log "createProps [ item :: #{item} ][ context :: #{context} ]"
      props = {}
      # Populate the props.
      for key, value of item
        # Ignore properties with object (most likely strings with the language code.
        # TODO: enable multilanguge WordLift here.
        continue if angular.isObject value
        expKey = h.expand key, context
        # console.log "createProps [ key :: #{key} ][ expKey :: #{expKey} ][ value :: #{value} ]"
        # Initialize the array.
        props[expKey] = [] if not props[expKey]?
        # Add the value to the array.
        props[expKey].push h.expand(value, context)

      # Return the props.
      props

    # Return the service instance.
    service
  ])

angular.module('wordlift.tinymce.plugin.services.Helpers', [])
.service('Helpers', [ ->
    service = {}

    # Merges two objects by copying overrides param onto the options.
    service.merge = (options, overrides) ->
      @extend (@extend {}, options), overrides

    service.extend = (object, properties) ->
      for key, val of properties
        object[key] = val
      object

    # Creates a unique ID of the specified length (default 8).
    service.uniqueId = (length = 8) ->
      id = ''
      id += Math.random().toString(36).substr(2) while id.length < length
      id.substr 0, length

    ###*
     * Expand a string using the provided context.
     * @param {string} A content string to be expanded.
     * @param {object} A context providing prefix -> URL key-value pairs
     * @return {string} An expanded string.
     ###
    service._expand = (content, context) ->
      # console.log "_expand [ content :: #{content} ][ context :: #{context} ]"
      return if not content?
      # if there's no prefix, return the original string.
      if null is matches = "#{content}".match(/([\w|\d]+):(.*)/)
        prefix = content
        path = ''
      else
        # get the prefix and the path.
        prefix = matches[1]
        path = matches[2]

      # if the prefix is unknown, leave it.
      return content if not context[prefix]?

      prepend = if angular.isString context[prefix] then context[prefix] else context[prefix]['@id']

      #      console.log "_expand [ content :: #{content} ][ prepend :: #{prepend} ][ path :: #{path} ]"

      # return the full path.
      prepend + path

    ###*
     * Expand the specified content using the prefixes in the provided context.
     * @param {string|array} The content string or an array of strings.
     * @param {object} A context made of prefix -> URLs value pairs.
     * @return {string|array} An expanded string or an array of expanded strings.
     ###
    service.expand = (content, context) ->
      if angular.isArray content
        return (service.expand(c, context) for c in content)

      service._expand content, context

    # Get the values associated with the specified key(s). Keys are expanded.
    service.get = (what, container, context, filter) ->
      # If it's a single key, call getA
      return service.getA(what, container, context, filter) if not angular.isArray what

      # Prepare the return array.
      values = []

      # For each key, add the result.
      for key in what
        add = service.getA key, container, context, filter
        # Ensure the result is an array.
        add = if angular.isArray add then add else [ add ]
        # Merge unique the results.
        service.mergeUnique values, add

      # Return the result array.
      values

    # Get the values associated with the specified key. Keys are expanded.
    service.getA = (what, container, context, filter = ((a) -> a)) ->
      # expand the what key.
      whatExp = service.expand what, context
      # return the value bound to the specified key.
      #        console.log "[ what exp :: #{whatExp} ][ key :: #{expand key} ][ value :: #{value} ][ match :: #{whatExp is expand(key)} ]" for key, value of container
      return filter(value) for key, value of container when whatExp is service.expand(key, context)
      []

    # get the value for specified property (what) in the provided container in the specified language.
    # items must conform to {'@language':..., '@value':...} format.
    service.getLanguage = (what, container, language, context) ->
      # if there's no item return null.
      return if null is items = service.get(what, container, context)
      # transform to an array if it's not already.
      items = if angular.isArray items then items else [ items ]
      # cycle through the array.
      return item[VALUE] for item in items when language is item['@language']
      # if not found return the english value.
      return item[VALUE] for item in items when 'en' is item['@language']

    service.mergeUnique = (array1, array2) ->
      array1 = [] if not array1?
      array1.push item for item in array2 when item not in array1

    service.containsOrEquals = (what, where, context) ->
      return false if not where?
      # ensure the where argument is an array.
      whereArray = if angular.isArray where then where else [ where ]
      # expand the what string.
      whatExp = service.expand what, context
      # return true if the string is found.
      return true for item in whereArray when whatExp is service.expand(item, context)
      # otherwise false.
      false

    # Return the services.
    service

  ])
angular.module('wordlift.tinymce.plugin.services.TextAnnotationService', [])
.service('TextAnnotationService', [ 'Helpers', (h)->
    service = {}

    ###*
     * Create a text annotation using the specified parameters.
     * @param {object} An object containing the parameters to set.
     * @return {object} A text annotation instance.
     ###
    service.create = (params = {}) ->

      # Set the defalut values.
      defaults =
        id: 'urn:local-text-annotation-' + h.uniqueId 32
        text: ''
        start: 0
        end: 0
        confidence: 0.0
        entityAnnotations: {}
        _item: null

      # Return the Text Annotation structure by merging the defaults with the provided params.
      h.merge defaults, params

    ###*
     * Create a text annotation.
     * @param {object} The text annotation raw data.
     * @param {object} The context data holding prefix -> URL key-value pairs.
     * @return {object} A text annotation.
     ###
    service.build = (item, context) ->
      # console.log "[ item :: #{item} ][ context :: #{context} ]"
      service.create
        id: h.get '@id', item, context
        text: h.get("#{FISE_ONT}selected-text", item, context)[VALUE]
        start: h.get "#{FISE_ONT}start", item, context
        end: h.get "#{FISE_ONT}end", item, context
        confidence: h.get FISE_ONT_CONFIDENCE, item, context
        entityAnnotations: {}
        _item: item

    # Find a text annotation in the provided collection given its start and end parameters.
    service.find = (textAnnotations, start, end) ->
      return textAnnotation for textAnnotationId, textAnnotation of textAnnotations when textAnnotation.start is start and textAnnotation.end is end


    ###*
     * Find a text annotation in the provided collection which matches the start and end values.
     * @param {object} A collection of text annotations.
     * @param {object} Text annotation used for search or to create a new text annotation.
     * @return {object} The text annotation matching the parameters or a new text annotation with those parameters.
     ###
    service.findOrCreate = (textAnnotations, textAnnotation) ->
        # Return the text annotation if existing.
        ta = service.find textAnnotations, textAnnotation.start, textAnnotation.end
        return ta if ta?

        # Create a new text annotation.
        ta = service.create
          text: textAnnotation.label
          start: textAnnotation.start
          end: textAnnotation.end
          confidence: 1.0

        textAnnotations[ta.id] = ta
        ta


    # Return the service instance.
    service
])


angular.module('wordlift.tinymce.plugin.services', [
  'wordlift.tinymce.plugin.config'
  'LoggerService'
  'wordlift.tinymce.plugin.services.EditorService'
  'wordlift.tinymce.plugin.services.EntityService'
  'wordlift.tinymce.plugin.services.EntityAnnotationService'
  'wordlift.tinymce.plugin.services.EntityAnnotationConfidenceService'
  'wordlift.tinymce.plugin.services.TextAnnotationService'
  'wordlift.tinymce.plugin.services.Helpers'
  'AnalysisService'
])

angular.module('wordlift.tinymce.plugin.controllers',
  [ 'wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services' ])
.filter('orderObjectBy', ->
    (items, field, reverse) ->
      filtered = []

      angular.forEach items, (item) ->
        filtered.push(item)

      filtered.sort (a, b) ->
        a[field] > b[field]

      filtered.reverse() if reverse

      filtered
  )
.filter('filterObjectBy', ->
    (items, field, value) ->
      filtered = []

      angular.forEach items, (item) ->
        filtered.push(item) if item[field] is value

      filtered
  )
.controller('EntitiesController', ['AnalysisService','EntityAnnotationService','EditorService', '$http', '$log', '$scope', '$rootScope', (AnalysisService, EntityAnnotationService, EditorService, $http, $log, $scope, $rootScope) ->

    $scope.isRunning = false
    # holds a reference to the current analysis results.
    $scope.analysis = AnalysisService.createAnEmptyAnalysis()
    # holds a reference to the selected text annotation.
    $scope.textAnnotation = null
    # holds a reference to the selected text annotation span.
    $scope.textAnnotationSpan = null
    # new entity model
    $scope.newEntity = {
      label: null
      type: null
    }

    # Toolbar
    $scope.activeToolbarTab = 'Add new entity'
    $scope.isActiveToolbarTab  = (tab)->
      $scope.activeToolbarTab is tab
    $scope.setActiveToolbarTab  = (tab)->
      if $scope.activeToolbarTab is tab
        return 
      $scope.autocompleteOpened = false
      $scope.activeToolbarTab = tab
    
    $scope.autocompleteOpened = false
  
    # holds a reference to the knows types from AnalysisService
    $scope.knownTypes = null
    
    setArrowTop = (top) ->
      $('head').append('<style>#wordlift-disambiguation-popover .postbox:before,#wordlift-disambiguation-popover .postbox:after{top:' + top + 'px;}</style>');

    # a reference to the current text annotation span in the editor.
    el = undefined
    scroll = ->
      return if not el?
      # get the position of the clicked element.
      pos = EditorService.getWinPos(el)
      # set the popover arrow to the element position.
      setArrowTop(pos.top - 50)

    # TODO: move these hooks on the popover, in order to hook/unhook the events.
    $(window).scroll(scroll)
    $('#content_ifr').contents().scroll(scroll)
    

    # Search for entities server side
    $scope.onSearch = (term) ->
      return $http
        method: 'post'
        url: ajaxurl + '?action=wordlift_search'
        data: { 'term' : term }
      .then (response) ->
        # Create a fake entity annotation for each entity
        response.data.map (entity)->
          EntityAnnotationService.create { 'entity': entity }
    
    # Create a new entity from the disambiguation widget
    $scope.onNewEntityCreate = (entity) ->
      $scope.isRunning = true
    
      $http
        method: 'post'
        url: ajaxurl + '?action=wordlift_add_entity'
        data: $scope.newEntity
      .success (data, status, headers, config) ->
        $scope.isRunning = false
        # Create a fake entity annotation for each entity
        entityAnnotation = EntityAnnotationService.create { 'entity': data }
        # Enhance current analysis with the selected entity if needed 
        if AnalysisService.enhance($scope.analysis, $scope.textAnnotation, entityAnnotation) is true
          # Update the editor accordingly 
          $scope.$emit 'selectEntity', ta: $scope.textAnnotation, ea: entityAnnotation
      .error (data, status, headers, config) ->
        $scope.isRunning = false
        $log.debug "Got en error on onNewEntityCreate"

    # Search for entities server side
    $scope.onSearchedEntitySelected = (entityAnnotation) ->
      # Enhance current analysis with the selected entity if needed

      if AnalysisService.enhance($scope.analysis, $scope.textAnnotation, entityAnnotation) is true
        # Update the editor accordingly 
        $scope.$emit 'selectEntity', ta: $scope.textAnnotation, ea: entityAnnotation

    # On entity click emit a selectEntity event 
    $scope.onEntitySelected = (textAnnotation, entityAnnotation) ->
      $scope.$emit 'selectEntity', ta: textAnnotation, ea: entityAnnotation

    # Receive the analysis results and store them in the local scope.
    $scope.$on 'analysisReceived', (event, analysis) ->
      $scope.analysis = analysis

    $scope.$on 'autocompleteOpened', (event) ->
      $scope.autocompleteOpened = true

    $scope.$on 'autocompleteClosed', (event) ->
      $scope.autocompleteOpened = false
    
    $scope.$on 'configurationTypesLoaded', (event, types)->
      $scope.knownTypes = types

    # When a text annotation is added, enhance current analysis 
    # and open the disambiguation popover.
    $scope.$on 'textAnnotationAdded', (event, textAnnotation) ->
      # Add the text annotation to the current analysis
      AnalysisService.addTextAnnotation $scope.analysis, textAnnotation
      # Simulate a text annotation click event in order to open the popover
      $scope.$broadcast 'textAnnotationClicked', textAnnotation.id
     
    # When a text annotation is clicked, open the disambiguation popover.
    $scope.$on 'textAnnotationClicked', (event, textAnnotationId) ->

      # Set the current text annotation to the one specified.
      $scope.textAnnotation = $scope.analysis?.textAnnotations[textAnnotationId]
      # Set default new entity label accordingly to the current textAnnotation Text
      $scope.newEntity.label = $scope.textAnnotation?.text

      # hide the popover if there are no entities.
      if not $scope.textAnnotation?.entityAnnotations? 
        $('#wordlift-disambiguation-popover').hide()
        # show the popover.
      else

        # get the position of the clicked element.
        pos = EditorService.getWinPos(textAnnotationId)
        # set the popover arrow to the element position.
        setArrowTop(pos.top - 50)
        # show the popover.
        $('#wordlift-disambiguation-popover').show()

  ])
.controller('ErrorController', ['$element', '$scope', '$log', ($element, $scope, $log) ->

    # Set the element as a jQuery UI Dialog.
    element = $($element).dialog
      title: 'WordLift'
      dialogClass: 'wp-dialog'
      modal: true
      autoOpen: false
      closeOnEscape: true
      buttons:
        Ok: ->
          $(this).dialog 'close'

    # Show the dialog box when an error is raised.
    $scope.$on 'error', (event, message) ->
      $scope.message = message
      element.dialog 'open'

  ])

# Set the well-known $ reference to jQuery.
$ = jQuery

# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers', 'wordlift.tinymce.plugin.directives'])

# Create the HTML fragment for the disambiguation popover that shows when a user clicks on a text annotation.
$(
  container = $('''
    <div id="wl-app" class="wl-app">
      <div id="wl-error-controller" class="wl-error-controller" ng-controller="ErrorController">
        <p ng-bind="message"></p>
      </div>
      <div id="wordlift-disambiguation-popover" class="metabox-holder" ng-controller="EntitiesController">
        <div class="postbox">
          <div class="handlediv" title="Click to toggle"><br></div>
          <h3 class="hndle"><span>Entity Reconciliation</span></h3>
          <div class="ui-widget toolbar">
            <span class="wl-active-tab" ng-bind="activeToolbarTab" />
            <i ng-class="{'selected' : isActiveToolbarTab('Search for entities')}" ng-click="setActiveToolbarTab('Search for entities')" class="wl-search-toolbar-icon" />
            <i ng-class="{'selected' : isActiveToolbarTab('Add new entity')}" ng-click="setActiveToolbarTab('Add new entity')" class="wl-add-entity-toolbar-icon" />
          </div>
          <div class="inside">
            <form role="form">
              <div class="form-group">
                <div ng-show="isActiveToolbarTab('Search for entities')" class="tab">
                  <div class="ui-widget">
                    <input type="text" class="form-control" id="search" placeholder="search for entities" autocomplete on-select="onSearchedEntitySelected(entityAnnotation)" source="onSearch($viewValue)">
                  </div>       
                </div>
                <div ng-show="isActiveToolbarTab('Add new entity')" class="tab">
                  <div class="ui-widget">
                    <input ng-model="newEntity.label" type="text" class="form-control" id="label" placeholder="label">
                  </div>
                  <div class="ui-widget">
                    <select ng-model="newEntity.type" ng-options="type.uri as type.label for type in knownTypes" placeholder="type">
                      <option value="" disabled selected>Select the entity type</option>
                    </select>
                  </div>
                  <div class="ui-widget button-container">
                    <i class="wl-spinner" ng-show="isRunning"></i>
                    <button ng-click="onNewEntityCreate(newEntity)">Save Entity</button>
                  </div>
                </div>
              </div>
              <div id="wl-entities-wrapper" ng-hide="autocompleteOpened">
                <wl-entities on-select="onEntitySelected(textAnnotation, entityAnnotation)" text-annotation="textAnnotation"></wl-entities>
              </div>
            </form>
            
            <wl-entity-input-boxes text-annotations="analysis.textAnnotations"></wl-entity-input-boxes>
            <wl-entity-props text-annotations="analysis.textAnnotations"></wl-entity-props>
          </div>
        </div>
      </div>
    </div>
    ''')
  .appendTo('form[name=post]')

  $('#wordlift-disambiguation-popover')
  .css(
      display: 'none'
      height: $('body').height() - $('#wpadminbar').height() + 12
      top: $('#wpadminbar').height() - 1
      right: 20
    )
  .draggable()

  # When the user clicks on the handle, hide the popover.
  $('#wordlift-disambiguation-popover .handlediv').click (e) ->
    $('#wordlift-disambiguation-popover').hide()

  # Declare the whole document as bootstrap scope.
  injector = angular.bootstrap $('#wl-app'), ['wordlift.tinymce.plugin']
  injector.invoke ['AnalysisService', 'EntityAnnotationConfidenceService', (AnalysisService, EntityAnnotationConfidenceService) ->
    if window.wordlift?
      AnalysisService.setKnownTypes window.wordlift.types
      AnalysisService.setEntities window.wordlift.entities
      EntityAnnotationConfidenceService.setEntities window.wordlift.entities
  ]

  # Add WordLift as a plugin of the TinyMCE editor.
  tinymce.PluginManager.add 'wordlift', (editor, url) ->
    editor.onLoadContent.add((ed, o) ->
      injector.invoke(['EditorService', (EditorService) ->
        EditorService.createDefaultAnalysis()
      ])
    )
    # Add a WordLift button the TinyMCE editor.
    # TODO Disable the new button as default
    editor.addButton 'wordlift_add_entity',
      classes: 'widget btn wordlift_add_entity'
      text: ' ' # the space is necessary to avoid right spacing on TinyMCE 4
      tooltip: 'Insert entity'
      onclick: ->

        injector.invoke(['EditorService','$rootScope', (EditorService, $rootScope) ->
          # execute the following commands in the angular js context.
          $rootScope.$apply(->
            EditorService.createTextAnnotationFromCurrentSelection()
          )
        ])

    # Add a WordLift button the TinyMCE editor.
    editor.addButton 'wordlift',
      classes: 'widget btn wordlift'
      text: ' ' # the space is necessary to avoid right spacing on TinyMCE 4
      tooltip: 'Analyse'

    # When the editor is clicked, the [EditorService.analyze](app.services.EditorService.html#analyze) method is invoked.
      onclick: ->
        injector.invoke(['EditorService', '$rootScope', '$log', (EditorService, $rootScope, $log) ->
          $rootScope.$apply(->
            # Get the html content of the editor.
            html = editor.getContent format: 'raw'

            # Get the text content from the Html.
            text = Traslator.create(html).getText()

            # $log.info "onclick [ html :: #{html} ][ text :: #{text} ]"
            # Send the text content for analysis.
            EditorService.analyze text
          )
        ])

    # TODO: move this outside of this method.
    # this event is raised when a textannotation is selected in the TinyMCE editor.
    editor.onClick.add (editor, e) ->
      injector.invoke(['$rootScope', ($rootScope) ->
        # execute the following commands in the angular js context.
        $rootScope.$apply(->
          # send a message about the currently clicked annotation.
          $rootScope.$broadcast 'textAnnotationClicked', e.target.id
        )
      ])
)

$wlEntityDisplayAsSelect = $('#wl-entity-display-as-select')
$wlEntityDisplayAsSelect.siblings('a.wl-edit-entity-display-as').click (event) ->
  if $wlEntityDisplayAsSelect.is ':hidden'
    $wlEntityDisplayAsSelect.slideDown('fast').find('select').focus()
    $(this).hide()

  event.preventDefault()

$wlEntityDisplayAsSelect.find('.wl-save-entity-display-as').click (event) ->

  $wlEntityDisplayAsSelect.slideUp('fast').siblings('a.wl-edit-entity-display-as').show()

  $('#hidden_wl_entity_display_as').val $('#wl_entity_display_as').val()
  $('#wl-entity-display-as').html $('#wl_entity_display_as option:selected').text()

  event.preventDefault()


$wlEntityDisplayAsSelect.find('.wl-cancel-entity-display-as').click ( event ) ->

  $('#wl-entity-display-as-select').slideUp('fast').siblings( 'a.wl-edit-entity-display-as' ).show().focus()

  $('#wl_entity_display_as').val( $('#hidden_wl_entity_display_as').val() )

  event.preventDefault()
# TODO this code has to be integrated within angular app
jQuery ($) ->
  $("body").append '''
    <div id="wordlift_chord_dialog">
    <form>
    <p>    
      <input value="2" id="wordlift_chord_depth_field" readonly size="3"> 
      Depth: Max degree of separtation between entities.
    </p>
    <div id="wordlift_chord_depth_slider"></div>
    <p>
      Base to generate the color palette of the Graph.<br />
      <input type="text" value="#22f" id="wordlift_chord_color_field" size="4">
    </p>
    <p>
      <input value="500" id="wordlift_chord_width_field" size="4">
      Width of the Graph in pixels
    </p>
    <p>
      <input value="520" id="wordlift_chord_height_field" size="4">
      Height of the Graph in pixels.
    </p>
    <p>
      <input id="wordlift_chord_dialog_ok" type="button" value="Ok" width="100">
    </p>
    </form>
    </div>
  '''
  
  # Set up color picker
  $("#wordlift_chord_color_field").wpColorPicker hide:true
  
  # Set up depth slider
  $("#wordlift_chord_depth_slider").slider
    range: "max"
    min: 1
    max: 5
    value: 2
    slide: (event, ui) ->
      $("#wordlift_chord_depth_field").val ui.value
      return

  $("#wordlift_chord_dialog").hide()
  
  # Generatr shortcode.
  $("#wordlift_chord_dialog_ok").on "click", ->
    
    # We should get default parameters from the php
    width = $("#wordlift_chord_width_field").val()
    height = $("#wordlift_chord_height_field").val()
    main_color = $("#wordlift_chord_color_field").val()
    depth = $("#wordlift_chord_depth_field").val()

    shortcode_text = "[wl-chord width=#{width}px height= #{height}px main_color=#{main_color} depth=#{depth}]"
    
    # Send shortcode to the editor								  
    # TODO this code should be managed trough EditorService
    top.tinymce.activeEditor.execCommand "mceInsertContent", false, shortcode_text
    $("#wordlift_chord_dialog").dialog "close"
    return

  return
