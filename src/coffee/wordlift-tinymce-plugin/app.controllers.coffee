angular.module('wordlift.tinymce.plugin.controllers', [
	'wordlift.tinymce.plugin.config', 
	'wordlift.tinymce.plugin.services'
	])
  .controller('HelloController', ['AnnotationService', 'EditorService', '$scope', (AnnotationService, EditorService, $scope) ->

    $scope.hello       = 'Ciao Marcello!'
    $scope.annotations = []


    $scope.selectedEntity = undefined
    
    $scope.sortByConfidence = (entity) ->
    	entity['enhancer:confidence']

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
        # get the position of the clicked element.
        pos = EditorService.getWinPos(elem)
        # set the popover arrow to the element position.
        setArrowTop(pos.top - 50)
        # show the popover.
        $('#wordlift-disambiguation-popover').show()


    setArrowTop = (top) ->
      $('head').append('<style>#wordlift-disambiguation-popover .postbox:before,#wordlift-disambiguation-popover .postbox:after{top:' + top + 'px;}</style>');
  ])