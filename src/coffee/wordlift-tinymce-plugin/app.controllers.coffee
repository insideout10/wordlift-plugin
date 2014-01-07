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
      # get the position of the clicked element.
      pos = EditorService.getWinPos(elem)
      # show the popover.
      $('#wordlift-disambiguation-popover')
        .css('left', pos.x + 100)
        .css('top', pos.y - 200)
        .show()
    
  ])