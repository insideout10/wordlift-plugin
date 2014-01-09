$ = jQuery

angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers'])

$(
  container = $('''
    <div id="wordlift-disambiguation-popover" class="metabox-holder" ng-controller="HelloController">

      <div class="postbox">
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 class="hndle"><span>Semantic Web</span></h3>
        <div class="inside">
          <form role="form">
            <div class="form-group">
              <input type="text" class="form-control" id="search" placeholder="search or create">
            </div>
            <ul>
              <li ng-repeat="entity in entities | orderBy:sortByConfidence:true" ng-class="{ 'active': $index == selectedEntity }">
                <strong class="{{entity['wordlift:cssClasses']}}" ng-click="onEntityClicked($index, entity)" ng-bind="entity['entity-label']['@value']"></strong><br />
                <small><a ng-href="{{entity['entity-reference']}}" target="blank">{{entity['entity-reference']}}</a><small><br />
                <small>[ Confidence Rate: <strong>{{entity['confidence']}}</strong> ]</small>

              </li>
            </ul>
          </form>
        </div>
      </div>
    </div>
    ''')
    .appendTo('body')
    .css(
      height: $('body').height() - $('#wpadminbar').height() + 32
      top: $('#wpadminbar').height() - 1
      right: 0
    )
    .draggable()

  # when the user clicks on the handle, hide the popover.
  $('#wordlift-disambiguation-popover .handlediv').click (e) -> container.hide()

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


