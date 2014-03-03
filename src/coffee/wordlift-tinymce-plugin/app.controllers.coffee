angular.module('wordlift.tinymce.plugin.controllers', [ 'wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services' ])
  .filter('orderObjectBy', ->
    (items, field, reverse) ->
      filtered = []

      angular.forEach items, (item) -> filtered.push(item)

      filtered.sort (a, b) -> a[field] > b[field]

      filtered.reverse() if reverse

      filtered
  )
  .controller('HelloController', ['EditorService', 'EntityService', '$log', '$scope', 'Configuration', (EditorService, EntityService, $log, $scope, Configuration) ->

    # holds a reference to the current analysis results.
    $scope.analysis       = null

    # holds a reference to the selected text annotation.
    $scope.textAnnotation = null
    # holds a reference to the selected text annotation span.
    $scope.textAnnotationSpan = null


    $scope.annotations = []
    $scope.selectedEntity = undefined
    
    $scope.selectedEntitiesMapping = {}


    $scope.getSelectedEntities = () ->
      entities = []
      for key, value of $scope.selectedEntitiesMapping
        entities.push value
      entities

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

#    DEPRECATED: the stylesheet is now applied using in the template. The selection is done using the selected attribute.
#    $scope.currentCssClass = (entityIndex, entityAnnotation) ->
#      currentItemId = $scope.textAnnotationSpan.attr("itemid")
#      return "#{entityAnnotation.entity.type} selected" if entityAnnotation.entity.id == currentItemId
#      return "#{entityAnnotation.entity.type} selected" if entityIndex == $scope.selectedEntity
#      return "#{entityAnnotation.entity.type}"

    # This event is raised when an entity is selected from the entities popover.
    $scope.onEntityClicked = (entityIndex, entityAnnotation) ->
      $scope.selectedEntity = entityIndex
      $scope.selectedEntitiesMapping[entityAnnotation.relation.id] = entityAnnotation.entity

      # Set the annotation selected/unselected.
      entityAnnotation.selected = !entityAnnotation.selected

      # Select (or unselect) the specified entity annotation.
      if entityAnnotation.selected
        EntityService.select entityAnnotation
      else
        EntityService.deselect entityAnnotation

      $scope.$emit 'DisambiguationWidget.entitySelected', entityAnnotation

    # receive the analysis results and store them in the local scope.
    $scope.$on 'analysisReceived', (event, analysis) ->
      $scope.analysis = analysis

    # When a text annotation is clicked, open the disambiguation popover.
    $scope.$on 'textAnnotationClicked', (event, id, sourceElement) ->
      # Set or reset properly $scope.selectedEntity
      $scope.selectedEntity = undefined
      # Get the text annotation with the provided id.
      $scope.textAnnotationSpan = angular.element(sourceElement.target)
      
      $scope.textAnnotation = $scope.analysis.textAnnotations[id]

      # hide the popover if there are no entities.
      if 0 is $scope.textAnnotation?.entityAnnotations?.length
        $('#wordlift-disambiguation-popover').hide()
      # show the popover.
      else
        # get the position of the clicked element.
        pos = EditorService.getWinPos(sourceElement)
        # set the popover arrow to the element position.
        setArrowTop(pos.top - 50)
        # show the popover.
        $('#wordlift-disambiguation-popover').show()

  ])
