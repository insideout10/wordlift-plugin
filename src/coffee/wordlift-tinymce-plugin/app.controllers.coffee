angular.module('wordlift.tinymce.plugin.controllers', [
	'wordlift.tinymce.plugin.config', 
	'wordlift.tinymce.plugin.services'
	])
  .controller('HelloController', ['AnnotationService', 'EditorService', '$scope', (AnnotationService, EditorService, $scope) ->

    $scope.annotations = []
    $scope.selectedEntity = undefined
    
    $scope.sortByConfidence = (entity) ->
    	entity['enhancer:confidence']

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
      $scope.$emit 'DisambiguationWidget.entitySelected', entity

    # this event is fired when entities are found for a selected text annotation.
    $scope.$on 'AnnotationService.entityAnnotations', (event, entities, elem) ->
      # set the entities in the local scope.
      $scope.entities = entities

      if 0 is $scope.entities.length
        $('#wordlift-disambiguation-popover').hide()
      else
        # save a reference to the element.
        el = elem
        # get the position of the clicked element.
        pos = EditorService.getWinPos(elem)
        # set the popover arrow to the element position.
        setArrowTop(pos.top - 50)
        # show the popover.
        $('#wordlift-disambiguation-popover').show()

  ])
