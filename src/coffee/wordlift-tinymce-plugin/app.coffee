$ = jQuery

angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers'])

$(
  container = $('''
    <div id="wordlift-disambiguation-popover" class="bootstrap" ng-controller="HelloController">
      <div class="content">
        <div class="handlediv" title="Click to toggle"></div>
        <h3 class="hndle"><span>Semantic Web</span></h3>
        <div class="inside">
          <form role="form">
            <div class="form-group">
              <input type="text" class="form-control" id="search" placeholder="search or create">
            </div>
            <ul>
              <li ng-repeat="entity in entities | orderBy:sortByConfidence:true" ng-class="{ 'active': $index == selectedEntity }">
                <strong class="{{entity['wordlift:cssClasses']}}" ng-click="onEntityClicked($index, entity)" ng-bind="entity['enhancer:entity-label']['@value']"></strong><br />
                <small><a ng-href="{{entity['enhancer:entity-reference']}}" target="blank">{{entity['enhancer:entity-reference']}}</a><small><br />
                <small>[ Confidence Rate: <strong>{{entity['enhancer:confidence']}}</strong> ]</small>

              </li>
            </ul>
          </form>
        </div>
      </div>

      <div class="bubble-arrow-border"></div>
      <div class="bubble-arrow"></div>
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


