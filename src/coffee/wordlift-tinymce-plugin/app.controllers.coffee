angular.module('wordlift.tinymce.plugin.controllers', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services'])
  .controller('HelloController', ['EditorService', '$scope', (EditorService, $scope) ->

    $scope.hello = 'Ciao Marcello!'

  ])