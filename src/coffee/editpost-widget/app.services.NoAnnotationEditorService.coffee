# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.editpost.widget.services.NoAnnotationEditorService', [
  'wordlift.editpost.widget.services.EditorAdapter',
  'wordlift.editpost.widget.services.AnalysisService'
])
# Manage redlink analysis responses
  .service('EditorService', ['configuration', 'AnalysisService',
  'EditorAdapter', '$log', '$http', '$rootScope',
  (configuration, AnalysisService, EditorAdapter, $log, $http, $rootScope)->
    # @deprecated use EditorAdapter.getEditor()
    editor = ->
      tinyMCE.get('content')

    $rootScope.$on "analysisPerformed", (event, analysis) ->
      service.embedAnalysis analysis if analysis? and analysis.annotations?

    # Disambiguate a single annotation or every entity related ones
    # Discarded entities are considered too
    $rootScope.$on "entitySelected", (event, entity, annotationId) ->
      $log.debug '[ app.services.EditorService ] `entitySelected` event received on no annotation editor.', event, entity, annotationId
      # dont do annotation operations.
      # we supply a placeholder annotation, because entities with one occurrences show as selected on ui.
      $rootScope.$broadcast "updateOccurencesForEntity", entity.id, ["placeholder-annotation"]

    $rootScope.$on "entityDeselected", (event, entity, annotationId) ->
      # dont do annotation operations.
      $rootScope.$broadcast "updateOccurencesForEntity", entity.id, []

    service =
# Detect if there is a current selection
      hasSelection: ()->
# A reference to the editor.
        ed = EditorAdapter.getEditor()
        if ed?
          if ed.selection.isCollapsed()
            return false
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
        # Send a message about the new textAnnotation.
        $rootScope.$broadcast 'textAnnotationAdded', { id: "placeholder-annotation", start: -1, end:-1}

# Select annotation with a id annotationId if available
      selectAnnotation: (annotationId)->
        $log.info "Select annotation for editor service complete"
        $rootScope.$broadcast 'textAnnotationClicked', undefined
        # do nothing, we dont want to create annotations.

# Embed the provided analysis in the editor.
      embedAnalysis: (analysis) =>
        # do nothing, we cant add annotations here.
        $rootScope.$broadcast "analysisEmbedded"

    service
])
