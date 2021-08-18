# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.editpost.widget.services.NoAnnotationEditorService', [
  'wordlift.editpost.widget.services.EditorAdapter',
  'wordlift.editpost.widget.services.AnalysisService'
])
# Manage redlink analysis responses
  .service('EditorService', ['configuration', 'AnalysisService',
  'EditorAdapter', '$log', '$http', '$rootScope',
  (configuration, AnalysisService, EditorAdapter, $log, $http, $rootScope)->
    INVISIBLE_CHAR = '\uFEFF'

    # Find existing entities selected in the html content (by looking for *itemid* attributes).
    findEntities = (html) ->
# Prepare a traslator instance that will traslate Html and Text positions.
#      traslator = Traslator.create html

      # Set the pattern to look for *itemid* attributes.
      # pattern = /<(\w+)[^>]*\sclass="([^"]+)"\sitemid="([^"]+)"[^>]*>([^<]*)<\/\1>/gim
      #
      # Internet Explorer 11 and Edge have cases where the `id` attribute is sorted,
      # after the `class` attribute, so we consider it in the pattern.
      #
      # See https://github.com/insideout10/wordlift-plugin/issues/520
      pattern = /<(\w+)[^>]*\sclass="([^"]+)"\s+(?:id="[^"]+"\s+)?itemid="([^"]+)"[^>]*>([^<]*)<\/\1>/gim

      # Get the matches and return them.
      (while match = pattern.exec html

        annotation =
#          start: traslator.html2text match.index
#          end: traslator.html2text (match.index + match[0].length)
          start: match.index
          end: match.index + match[0].length
          uri: match[3]
          label: match[4]
          cssClass: match[2]

        annotation
      )

    findPositions = (entities) ->
      positions = []
      for entityAnnotation in entities
        positions = positions.concat [ entityAnnotation.start..entityAnnotation.end ]
      positions

    # @deprecated use EditorAdapter.getEditor()
    editor = ->
      tinyMCE.get('content')

    disambiguate = (annotationId, entity)->
      # dont do annotation operations.

    dedisambiguate = (annotationId, entity)->
      # dont do annotation operations.

    # TODO refactoring with regex
    currentOccurrencesForEntity = (entityId) ->
      # dont do annotation operations.

    $rootScope.$on "analysisPerformed", (event, analysis) ->
      service.embedAnalysis analysis if analysis? and analysis.annotations?

    # Disambiguate a single annotation or every entity related ones
    # Discarded entities are considered too
    $rootScope.$on "entitySelected", (event, entity, annotationId) ->

      $log.debug '[ app.services.NoAnnotationEditorService ] `entitySelected` event received.', event, entity, annotationId

      discarded = []
      if annotationId?
        discarded.push disambiguate annotationId, entity
      else
        for id, annotation of entity.annotations
          discarded.push disambiguate annotation.id, entity

      for entityId in discarded
        if entityId
          occurrences = currentOccurrencesForEntity entityId
          $rootScope.$broadcast "updateOccurencesForEntity", entityId, occurrences

      occurrences = currentOccurrencesForEntity entity.id
      $rootScope.$broadcast "updateOccurencesForEntity", entity.id, occurrences

    $rootScope.$on "entityDeselected", (event, entity, annotationId) ->

      console.debug 'EditorService::$rootScope.$on "entityDeselected" (event)', { event, entity, annotationId }

      if annotationId?
        dedisambiguate annotationId, entity
      else
        for id, annotation of entity.annotations
          dedisambiguate annotation.id, entity

      occurrences = currentOccurrencesForEntity entity.id

      console.debug 'EditorService::$rootScope.$on "entityDeselected" (event)', { occurrences }

      $rootScope.$broadcast "updateOccurencesForEntity", entity.id, occurrences

    service =
# Detect if there is a current selection
      hasSelection: ()->
# A reference to the editor.
        ed = EditorAdapter.getEditor()
        if ed?
          if ed.selection.isCollapsed()
            return false

#          if /<([\/]*[a-z]+)[^<]*>/.test ed.selection.getContent()
#            $log.warn "The selection overlaps html code"
#            return false
          return true

        false

# Check if the given editor is the current editor
      isEditor: (editor)->
        ed = EditorAdapter.getEditor()
        ed.id is editor.id

# Update contenteditable status for the editor
      updateContentEditableStatus: (status)->
        # do nothing, we wouldnt have an editor here.

# Create a textAnnotation starting from the current selection
      createTextAnnotationFromCurrentSelection: ()->
# A reference to the editor.
        ed = EditorAdapter.getEditor()

        # If the current selection is collapsed / blank, then nothing to do
        if ed.selection.isCollapsed()
          $log.warn "Invalid selection! The text annotation cannot be created"
          return

        # Retrieve the selected text
        # Notice that toString() method of browser native selection obj is used
        text = "#{ed.selection.getSel()}"

        # Create the text annotation
        textAnnotation = AnalysisService.createAnnotation {
          text: text
        }

        # Prepare span wrapper for the new text annotation
        #
        # @since 3.23.5 we want to remove existing annotations.
        # @see https://github.com/insideout10/wordlift-plugin/issues/993
        textContent = ed.selection.getContent( { format: 'text' } );
        textAnnotationSpan = "<span id=\"#{textAnnotation.id}\" class=\"textannotation unlinked selected\">#{textContent}</span>#{INVISIBLE_CHAR}"

        # Update the content within the editor
        ed.selection.setContent textAnnotationSpan

        # Retrieve the current heml content
        content = EditorAdapter.getHTML() # ed.getContent format: 'raw'

        # Create a Traslator instance
        traslator = Traslator.create content

        # Retrieve the index position of the new span
        htmlPosition = content.indexOf(textAnnotationSpan);

        # Detect the corresponding text position
        textPosition = traslator.html2text htmlPosition

        # Set start & end text annotation properties
        textAnnotation.start = textPosition
        textAnnotation.end = textAnnotation.start + text.length

        # Send a message about the new textAnnotation.
        $rootScope.$broadcast 'textAnnotationAdded', textAnnotation

# Select annotation with a id annotationId if available
      selectAnnotation: (annotationId)->
        $log.info "Select annotation for editor service complete"
        # do nothing, we dont want to create annotations.

# Embed the provided analysis in the editor.
      embedAnalysis: (analysis) =>
        # do nothing, we cant add annotations here.
        $rootScope.$broadcast "analysisEmbedded"

    service
])
