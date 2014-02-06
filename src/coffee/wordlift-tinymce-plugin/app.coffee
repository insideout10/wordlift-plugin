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
              <li ng-repeat="(id, entityAnnotation) in textAnnotation.entityAnnotations | orderObjectBy:'confidence':true">
                <div class="entity" ng-show="entityAnnotation.entity.label" ng-class="entityAnnotation.entity.type">
                  <div class="thumbnail" title="{{entityAnnotation.entity.id}}" style="background-image: url({{entityAnnotation.entity.thumbnail}})"></div>
                  <div class="confidence" ng-bind="entityAnnotation.confidence"></div>
                  <div class="label" ng-bind="entityAnnotation.entity.label"></div>
                  <div class="type"></div>
                  <div class="source" ng-class="entityAnnotation.entity.source" ng-bind="entityAnnotation.entity.source"></div>
                </div>
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


