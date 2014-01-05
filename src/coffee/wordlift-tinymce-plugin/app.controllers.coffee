angular.module('wordlift.tinymce.plugin.controllers', [
	'wordlift.tinymce.plugin.config', 
	'wordlift.tinymce.plugin.services'
	])
  .controller('HelloController', ['AnnotationService', '$scope', (AnnotationService, $scope) ->

    $scope.hello       = 'Ciao Marcello!'
    $scope.annotations = []

    $scope.selectedEntity = undefined
    
    $scope.onEntityClicked = (entityIndex, entity) ->
    	$scope.selectedEntity = entityIndex
    	console.log "Going to update markup for textAnnotation #{entity['dc:relation']}"
    	$scope.$emit 'DisambiguationWidget.entitySelected', entity
    
    $scope.$on 'AnnotationService.entityAnnotations', (event, entities) ->
      console.log 'I received entity annotations too'
      $scope.entities = entities
    
  ])