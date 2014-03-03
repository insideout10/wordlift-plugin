angular.module('wordlift.tinymce.plugin.services.EntityService', ['wordlift.tinymce.plugin.config'])
  .service('EditorService', ['$log', ($log) ->

    select : (entity) ->
      $log.info 'select'

    deselect : (entity) ->
      $log.info 'deselect'
  ])
