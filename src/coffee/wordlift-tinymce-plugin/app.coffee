$ = jQuery

angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers'])

$(
  container = $('<div id="wordlift-tinymce-plugin" ng-controller="HelloController">{{hello}}</div>')
    .appendTo('body')
    .width(1000)
    .height(1000)

  injector = angular.bootstrap(container, ['wordlift.tinymce.plugin']);

  tinymce.PluginManager.add 'wordlift', (editor, url) ->
    # Add a button that opens a window
    editor.addButton 'wordlift',
    text   : 'WordLift'
    icon   : false
    onclick: ->
      alert(tinyMCE.activeEditor.getContent({format : 'text'}))
      injector.invoke(['EditorService', (EditorService) ->
        EditorService.analyze tinyMCE.activeEditor.getContent({format : 'text'})
      ])
#      content = tinyMCE.activeEditor.getContent({format : 'text'})
#      data =
#        action: 'wordlift_analyze'
#        body: content

)


