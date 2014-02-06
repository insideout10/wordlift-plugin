angular.module('wordlift.tinymce.plugin.controllers', [
	'wordlift.tinymce.plugin.config', 
	'wordlift.tinymce.plugin.services'
	])
  .filter('orderObjectBy', ->
    (items, field, reverse) ->
      filtered = []

      angular.forEach items, (item) -> filtered.push(item)

      filtered.sort (a, b) -> a[field] > b[field]

      filtered.reverse() if reverse

      filtered
  )
  .controller('HelloController', ['AnnotationService', 'EditorService', '$log', '$scope', 'Configuration', (AnnotationService, EditorService, $log, $scope, Configuration) ->

    # holds a reference to the current analysis results.
    $scope.analysis       = null

    # holds a reference to the selected text annotation.
    $scope.textAnnotation = null

    $scope.annotations = []
    $scope.selectedEntity = undefined
    
    $scope.sortByConfidence = (entity) ->
    	entity[Configuration.entityLabels.confidence]

    $scope.getLabelFor = (label) ->
      Configuration.entityLabels[label]

    setArrowTop = (top) ->
      $('head').append('<style>#wordlift-disambiguation-popover .postbox:before,#wordlift-disambiguation-popover .postbox:after{top:' + top + 'px;}</style>');

    # a reference to the current text annotation span in the editor.
    el     = undefined
    scroll = ->
      return if not el?
      # get the position of the clicked element.
      pos = EditorService.getWinPos(el)
      # set the popover arrow to the element position.
      setArrowTop(pos.top - 50)

    # TODO: move these hooks on the popover, in order to hook/unhook the events.
    $(window).scroll(scroll)
    $('#content_ifr').contents().scroll(scroll)

    # this event is raised when an entity is selected from the entities popover.
    $scope.onEntityClicked = (entityIndex, entity) ->
      $scope.selectedEntity = entityIndex
      $log.debug "Disanbiguated entity!"
      $log.debug entity
      $scope.$emit 'DisambiguationWidget.entitySelected', entity

    # receive the analysis results and store them in the local scope.
    $scope.$on 'analysisReceived', (event, analysis) ->
      $log.debug analysis
      $scope.analysis = analysis

    $scope.$on 'textAnnotationClicked', (event, id, sourceElement) ->
      # get the text annotation with the provided id.
      $scope.textAnnotation = $scope.analysis.textAnnotations[id]
      $log.debug $scope.textAnnotation

      # hide the popover if there are no entities.
      if 0 is $scope.textAnnotation.entityAnnotations.length
        $('#wordlift-disambiguation-popover').hide()
      # show the popover.
      else
        # get the position of the clicked element.
        pos = EditorService.getWinPos(sourceElement)
        # set the popover arrow to the element position.
        setArrowTop(pos.top - 50)
        # show the popover.
        $('#wordlift-disambiguation-popover').show()

#    # this event is fired when entities are found for a selected text annotation.
#    $scope.$on 'AnnotationService.entityAnnotations', (event, entities, elem) ->
#      $log.info "received #{entities.length} entity/ies"
#      # set the entities in the local scope.
#      $scope.entities = entities
#
#      if 0 is $scope.entities.length
#        $('#wordlift-disambiguation-popover').hide()
#      else
#        # save a reference to the element.
#        el = elem
#        # get the position of the clicked element.
#        pos = EditorService.getWinPos(elem)
#        # set the popover arrow to the element position.
#        setArrowTop(pos.top - 50)
#        # show the popover.
#        $('#wordlift-disambiguation-popover').show()

  ])
