angular.module('wordlift.tinymce.plugin.controllers', [
	'wordlift.tinymce.plugin.config', 
	'wordlift.tinymce.plugin.services'
	])
  .controller('HelloController', ['AnnotationService', '$scope', (AnnotationService, $scope) ->

    $scope.hello       = 'Ciao Marcello!'
    $scope.annotations = []

    $scope.$on 'AnnotationService.entityAnnotations', (event, annotations) ->
      console.log 'I received entity annotations too'
      console.log annotations
      $scope.annotations = annotations
    
  ])