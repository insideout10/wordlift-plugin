$ = jQuery

angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers'])

$(
  container = $('''
    <div id="wordlift-disambiguation-popover" class="bootstrap" ng-controller="HelloController">
      <div class="handlediv" title="Click to toggle"></div>
      <h3 class="hndle"><span>Semantic Web</span></h3>
      <div class="inside">
        <form role="form">
          <div class="form-group">
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


