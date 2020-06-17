(($, angular) =>
#  # Set the well-known $ reference to jQuery.
#  $ = jQuery

  # Create the main AngularJS module, and set it dependent on controllers and directives.
  angular.module('wordlift.editpost.widget', [
    'ngAnimate'
    'wordlift.ui.carousel'
    'wordlift.utils.directives'
    'wordlift.editpost.widget.providers.ConfigurationProvider',
    'wordlift.editpost.widget.controllers.EditPostWidgetController',
    'wordlift.editpost.widget.directives.wlClassificationBox',
    # Beware that while we're not using Angular components, we're using the element to hook the React application.
    # The Classification Box is in fact a React application.
    'wordlift.editpost.widget.directives.wlEntityList',
    'wordlift.editpost.widget.directives.wlEntityForm',
#    'wordlift.editpost.widget.directives.wlEntityTile',
    'wordlift.editpost.widget.directives.wlEntityInputBox',
    'wordlift.editpost.widget.services.AnalysisService',
    'wordlift.editpost.widget.services.EditorService',
    'wordlift.editpost.widget.services.RelatedPostDataRetrieverService'
  ])

  .config((configurationProvider)->
    params = Object.assign({}, window['_wlMetaBoxSettings'].settings, { types: window['_wlEntityTypes'] })
    configurationProvider.setConfiguration params
  )

  container = $("""
    <div
      id="wordlift-edit-post-wrapper"
      ng-controller="EditPostWidgetController"
      ng-include="configuration['ajax_url'] + '?action=wl_templates&name=wordlift-editpost-widget'">
    </div>
  """)
  .appendTo('#wordlift-edit-post-outer-wrapper')

  # Add svg based spinner code
  spinner = $("""
    <div class="wl-widget-spinner">
      <svg transform-origin="10 10" id="wl-widget-spinner-blogger">
        <circle cx="10" cy="10" r="6" class="wl-blogger-shape"></circle>
      </svg>
      <svg transform-origin="10 10" id="wl-widget-spinner-editorial">
        <rect x="4" y="4" width="12" height="12" class="wl-editorial-shape"></rect>
      </svg>
      <svg transform-origin="10 10" id="wl-widget-spinner-enterprise">
        <polygon points="3,10 6.5,4 13.4,4 16.9,10 13.4,16 6.5,16" class="wl-enterprise-shape"></polygon>
      </svg>
    </div>
  """)
  .appendTo('#wordlift_entities_box .ui-sortable-handle')

  console.log "bootstrapping WordLift app..."
  injector = angular.bootstrap $('#wordlift-edit-post-wrapper'), ['wordlift.editpost.widget']

  # Update spinner
  injector.invoke(['$rootScope', '$log', ($rootScope, $log) ->
    $rootScope.$on 'analysisServiceStatusUpdated', (event, status) ->
      css = if status then 'wl-spinner-running' else ''
      $('.wl-widget-spinner svg').attr 'class', css

    $rootScope.$on 'geoLocationStatusUpdated', (event, status) ->
      css = if status then 'wl-spinner-running' else ''
      $('.wl-widget-spinner svg').attr 'class', css

    if wp.wordlift?
      wp.wordlift.on 'loading', ( status ) ->
        css = if status then 'wl-spinner-running' else ''
        $('.wl-widget-spinner svg').attr 'class', css

  ])

  if window['wlSettings']?
    # Add WordLift as a plugin of the TinyMCE editor.
    tinymce.PluginManager.add 'wordlift', (editor, url) ->

      # Get the editor id from the `wlSettings` or use `content`.
      defaultEditorId = if "undefined" != typeof window['wlSettings']['default_editor_id'] then window['wlSettings']['default_editor_id'] else 'content'

      # Allow 3rd parties to change the editor id.
      #
      # @see https://github.com/insideout10/wordlift-plugin/issues/850.
      # @see https://github.com/insideout10/wordlift-plugin/issues/851.
      editorId = wp?.hooks?.applyFilters( 'wl_default_editor_id', defaultEditorId ) ? defaultEditorId

      console.log "Loading WordLift [ default editor :: #{defaultEditorId} ][ target editor :: #{editorId} ][ this editor :: #{editor.id} ]"

      # This plugin has to be loaded only with the main WP "content" editor
      return unless editor.id is editorId

      # The `closed` flag is a very important flag throughout the initialization
      # of WordLift's classification box: in fact if the classification box is
      # closed, WordLift's analysis won't run, until it gets opened.
      closed = $('#wordlift_entities_box').hasClass('closed')

      # Register event depending on tinymce major version
      fireEvent = (editor, eventName, callback)->
        switch tinymce.majorVersion
          when '4' then editor.on eventName, callback
          when '3' then editor["on#{eventName}"].add callback

      # We're going to disable WordPress' own live previews here until the
      # analysis is run, we need to do this as early as possible to avoid WP
      # already calling the live previews. But we need to do this only if the
      # classification box is open, since the analysis won't run if it's closed.
      #
      # See https://github.com/insideout10/wordlift-plugin/issues/700.
      if (!closed)
        injector.invoke(['EditorService', '$rootScope', '$log', (EditorService, $rootScope, $log) ->

          # Override wp.autosave.server.postChanged method
          # in order to avoid unexpected warning to the user
          if wp.autosave?
            wp.autosave.server.postChanged = ()->
              return false

          # Hack wp.mce.views to prevent shortcodes rendering starts before the
          # analysis is properly embedded wp.mce.views uses toViews() method from WP
          # 3.8 to 4.1 and setMarkers() method from WP 4.2 to 4.3 to replace
          # available shortcodes with corresponding views markup.
          for method in ['setMarkers', 'toViews']
            if wp.mce.views[method]?

              originalMethod = wp.mce.views[method]
              $log.warn "Override wp.mce.views method #{method}() to prevent shortcodes rendering"
              wp.mce.views[method] = (content)->
                return content

              $rootScope.$on "analysisEmbedded", (event) ->
                $log.info "Going to restore wp.mce.views method #{method}()"
                wp.mce.views[method] = originalMethod

              $rootScope.$on "analysisFailed", (event) ->
                $log.info "Going to restore wp.mce.views method #{method}()"
                wp.mce.views[method] = originalMethod

              break
        ])

      # Define the callback to call to start the analysis.
      startAnalysis = () ->
        injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log'
          (AnalysisService, EditorService, $rootScope, $log) ->
  # execute the following commands in the angular js context.
            $rootScope.$apply(->
  # Get the html content of the editor.
              html = editor.getContent format: 'raw'

              if "" isnt html
                EditorService.updateContentEditableStatus false
                AnalysisService.perform html
  # Get the text content from the Html.
  #            text = Traslator.create(html).getText()
  #            if text.match /[a-zA-Z0-9]+/
  #              # Disable tinymce editing
  #              EditorService.updateContentEditableStatus false
  #              AnalysisService.perform html
  #            else
  #              $log.warn "Blank content: nothing to do!"
            )
        ])

      addClassToBody = () ->
        # Get the editor body.
        $body = $( editor.getBody() )

        # Whether the postbox is closed.
        closed = $( '#wordlift_entities_box' ).hasClass( 'closed' )

        # Add or remove the class according to the postbox status.
        if closed then $body.addClass( 'wl-postbox-closed' ) else $body.removeClass( 'wl-postbox-closed' )


      # Add a `wl-postbox-closed` class to the editor body when the classification
      # metabox is closed.
      $(document).on( 'postbox-toggled', (e, postbox) ->
        # Bail out if it's not our postbox.
        return if 'wordlift_entities_box' isnt postbox.id

        addClassToBody()
      )

      # Set the initial state on the editor's body.
      editor.on('init', () ->
        addClassToBody()

        # Send a broadcast when the editor selection changes.
        #
        # See https://github.com/insideout10/wordlift-plugin/issues/467
        broadcastEditorSelection = () ->
          selection = editor.selection.getContent({format: 'text'})
          wp.wordlift.trigger 'editorSelectionChanged', { selection, editor, source: "tinymce" }

        editor.on('selectionchange', () -> broadcastEditorSelection() )

# Start the analysis if the postbox isn't closed.
      )

      # We were using the `LoadContent` event to track when content was being loaded into TinyMCE. At the time of this
      # event though the raw content in TinyMCE still has some internal html (mce_SELRES_start / type bookmark).
      #
      # Switching to `init` ensures that the editor is fully initialized and that HTML is removed.
      #
      # @see https://github.com/insideout10/wordlift-plugin/issues/1003
      if !closed then fireEvent( editor, 'init', startAnalysis ) else
# If the postbox is closed, hook to the `postbox-toggled` event and start
# the analysis as soon as the postbox is opened.
      $(document).on( 'postbox-toggled', (e, postbox) ->
# Bail out if it's not our postbox.
        return if 'wordlift_entities_box' isnt postbox.id

        startAnalysis()
      )

      # Fires when the user changes node location using the mouse or keyboard in the TinyMCE editor.
      fireEvent(editor, "NodeChange", (e) ->
        injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log',
          (AnalysisService, EditorService, $rootScope, $log) ->
            if AnalysisService._currentAnalysis
              $rootScope.$apply(->
                $rootScope.selectionStatus = EditorService.hasSelection()
              )
            true

        ])
      )

      # this event is raised when a textannotation is selected in the TinyMCE editor.
      fireEvent(editor, "Click", (e) ->
        injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log',
          (AnalysisService, EditorService, $rootScope, $log) ->
            if AnalysisService._currentAnalysis
              $rootScope.$apply(->
                EditorService.selectAnnotation e.target.id
              )
            true

        ])
      )

)(jQuery, window.angular)
