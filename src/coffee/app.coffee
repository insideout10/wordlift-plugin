# Set the well-known $ reference to jQuery.
$ = jQuery

# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers', 'wordlift.tinymce.plugin.directives'])

# Create the HTML fragment for the disambiguation popover that shows when a user clicks on a text annotation.
$(
  container = $('''
    <div id="wl-app" class="wl-app">
      <div id="wl-error-controller" class="wl-error-controller" ng-controller="ErrorController">
        <p ng-bind="message"></p>
      </div>
      <div id="wordlift-disambiguation-popover" class="metabox-holder" ng-controller="EntitiesController">
        <div class="postbox">
          <div class="handlediv" title="Click to toggle"><br></div>
          <h3 class="hndle"><span>Entity Reconciliation</span></h3>
          <div class="ui-widget toolbar">
            <span class="wl-active-tab" ng-bind="activeToolbarTab" />
            <i ng-class="{'selected' : isActiveToolbarTab('Search for entities')}" ng-click="setActiveToolbarTab('Search for entities')" class="wl-search-toolbar-icon" />
            <i ng-class="{'selected' : isActiveToolbarTab('Add new entity')}" ng-click="setActiveToolbarTab('Add new entity')" class="wl-add-entity-toolbar-icon" />
          </div>
          <div class="inside">
            <form role="form">
              <div class="form-group">
                <div ng-show="isActiveToolbarTab('Search for entities')" class="tab">
                  <div class="ui-widget">
                    <input type="text" class="form-control" id="search" placeholder="search for entities" autocomplete on-select="onSearchedEntitySelected(entityAnnotation)" source="onSearch($viewValue)">
                  </div>       
                </div>
                <div ng-show="isActiveToolbarTab('Add new entity')" class="tab">
                  <div class="ui-widget">
                    <input ng-model="newEntity.label" type="text" class="form-control" id="label" placeholder="label">
                  </div>
                  <div class="ui-widget">
                    <select ng-model="newEntity.type" ng-options="type.uri as type.label for type in knownTypes" placeholder="type">
                      <option value="" disabled selected>Select the entity type</option>
                    </select>
                  </div>
                  <div class="ui-widget button-container">
                    <i class="wl-spinner" ng-show="isRunning"></i>
                    <button ng-click="onNewEntityCreate(newEntity)">Save Entity</button>
                  </div>
                </div>
              </div>
              <div id="wl-entities-wrapper" ng-hide="autocompleteOpened">
                <wl-entities on-select="onEntitySelected(textAnnotation, entityAnnotation)" text-annotation="textAnnotation"></wl-entities>
              </div>
            </form>
            
            <wl-entity-input-boxes text-annotations="analysis.textAnnotations"></wl-entity-input-boxes>
            <wl-entity-props text-annotations="analysis.textAnnotations"></wl-entity-props>
          </div>
        </div>
      </div>
    </div>
    ''')
  .appendTo('form[name=post]')

  $('#wordlift-disambiguation-popover')
  .css(
      display: 'none'
      height: $('body').height() - $('#wpadminbar').height() + 12
      top: $('#wpadminbar').height() - 1
      right: 20
    )
  .draggable()

  # When the user clicks on the handle, hide the popover.
  $('#wordlift-disambiguation-popover .handlediv').click (e) ->
    $('#wordlift-disambiguation-popover').hide()

  # Declare the whole document as bootstrap scope.
  injector = angular.bootstrap $('#wl-app'), ['wordlift.tinymce.plugin']
  injector.invoke ['AnalysisService', 'EntityAnnotationConfidenceService', (AnalysisService, EntityAnnotationConfidenceService) ->
    if window.wordlift?
      AnalysisService.setKnownTypes window.wordlift.types
      AnalysisService.setEntities window.wordlift.entities
      EntityAnnotationConfidenceService.setEntities window.wordlift.entities
  ]

  # Add WordLift as a plugin of the TinyMCE editor.
  tinymce.PluginManager.add 'wordlift', (editor, url) ->
    editor.onLoadContent.add((ed, o) ->
      injector.invoke(['EditorService', (EditorService) ->
        EditorService.createDefaultAnalysis()
      ])
    )
    # Add a WordLift button the TinyMCE editor.
    # TODO Disable the new button as default
    editor.addButton 'wordlift_add_entity',
      classes: 'widget btn wordlift_add_entity'
      text: ' ' # the space is necessary to avoid right spacing on TinyMCE 4
      tooltip: 'Insert entity'
      onclick: ->

        injector.invoke(['EditorService','$rootScope', (EditorService, $rootScope) ->
          # execute the following commands in the angular js context.
          $rootScope.$apply(->
            EditorService.createTextAnnotationFromCurrentSelection()
          )
        ])

    # Add a WordLift button the TinyMCE editor.
    editor.addButton 'wordlift',
      classes: 'widget btn wordlift'
      text: ' ' # the space is necessary to avoid right spacing on TinyMCE 4
      tooltip: 'Analyse'

    # When the editor is clicked, the [EditorService.analyze](app.services.EditorService.html#analyze) method is invoked.
      onclick: ->
        injector.invoke(['EditorService', '$rootScope', '$log', (EditorService, $rootScope, $log) ->
          $rootScope.$apply(->
            # Get the html content of the editor.
            html = editor.getContent format: 'raw'

            # Get the text content from the Html.
            text = Traslator.create(html).getText()

            # $log.info "onclick [ html :: #{html} ][ text :: #{text} ]"
            # Send the text content for analysis.
            EditorService.analyze text
          )
        ])

    # TODO: move this outside of this method.
    # this event is raised when a textannotation is selected in the TinyMCE editor.
    editor.onClick.add (editor, e) ->
      injector.invoke(['$rootScope', ($rootScope) ->
        # execute the following commands in the angular js context.
        $rootScope.$apply(->
          # send a message about the currently clicked annotation.
          $rootScope.$broadcast 'textAnnotationClicked', e.target.id
        )
      ])
)

$wlEntityDisplayAsSelect = $('#wl-entity-display-as-select')
$wlEntityDisplayAsSelect.siblings('a.wl-edit-entity-display-as').click (event) ->
  if $wlEntityDisplayAsSelect.is ':hidden'
    $wlEntityDisplayAsSelect.slideDown('fast').find('select').focus()
    $(this).hide()

  event.preventDefault()

$wlEntityDisplayAsSelect.find('.wl-save-entity-display-as').click (event) ->

  $wlEntityDisplayAsSelect.slideUp('fast').siblings('a.wl-edit-entity-display-as').show()

  $('#hidden_wl_entity_display_as').val $('#wl_entity_display_as').val()
  $('#wl-entity-display-as').html $('#wl_entity_display_as option:selected').text()

  event.preventDefault()


$wlEntityDisplayAsSelect.find('.wl-cancel-entity-display-as').click ( event ) ->

  $('#wl-entity-display-as-select').slideUp('fast').siblings( 'a.wl-edit-entity-display-as' ).show().focus()

  $('#wl_entity_display_as').val( $('#hidden_wl_entity_display_as').val() )

  event.preventDefault()