$ = jQuery

angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers'])

$(
  container = $('''
    <div id="wordlift-disambiguation-popover" class="bootstrap" ng-controller="HelloController">
    <form role="form">
      <div class="form-group">
        <label for="search">Search</label>
        <input type="text" class="form-control" id="search" placeholder="search or create">
      </div>
      <ul>
        <li ng-repeat="annotation in annotations">
          <div>annotation</div>
          <div ng-bind="annotation['enhancer:entity-reference']"></div>
        </li>
      </ul>
    </form>
    </div>
    ''')
    .appendTo('body')
    .draggable()

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


