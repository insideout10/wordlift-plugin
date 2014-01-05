$ = jQuery

angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers'])

$(
  container = $('''
    <div id="wordlift-tinymce-plugin" ng-controller="HelloController">{{hello}}
      <ul>
        <li ng-repeat="annotation in annotations">
          <div>annotation</div>
          <div ng-bind="annotation['@id']"></div>
        </li>
      </ul>
    </div>
    ''')
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
      injector.invoke(['EditorService', (EditorService) ->
        EditorService.analyze tinyMCE.activeEditor.getContent({format : 'text'})
      ])


)


