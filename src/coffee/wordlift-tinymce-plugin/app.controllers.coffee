angular.module('wordlift.tinymce.plugin.controllers', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services'])
  .controller('HelloController', ['AnnotationService', '$scope', (AnnotationService, $scope) ->

    $scope.hello       = 'Ciao Marcello!'
    $scope.annotations = []

    $scope.$on 'AnnotationService.annotations', (event, annotations) ->
      console.log 'I received some annotations too'
      $scope.annotations = annotations
      console.log $scope.annotations
  ])