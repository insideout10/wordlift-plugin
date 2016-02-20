# Set the well-known $ reference to jQuery.
$ = jQuery

# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.editpost.widget', [
  'wordlift.ui.carousel'
  'wordlift.utils.directives'
  'wordlift.editpost.widget.providers.ConfigurationProvider',
  'wordlift.editpost.widget.controllers.EditPostWidgetController',
  'wordlift.editpost.widget.directives.wlClassificationBox',
  'wordlift.editpost.widget.directives.wlEntityForm',
  'wordlift.editpost.widget.directives.wlEntityTile',
  'wordlift.editpost.widget.directives.wlEntityInputBox',
  'wordlift.editpost.widget.services.AnalysisService',
  'wordlift.editpost.widget.services.EditorService',
  'wordlift.editpost.widget.services.RelatedPostDataRetrieverService'

])

.config((configurationProvider)->
  configurationProvider.setConfiguration window.wordlift
)

$(
  container = $("""
  	<div id="wordlift-edit-post-wrapper" ng-controller="EditPostWidgetController">
  		
      <div class="wl-error" ng-repeat="item in errors">
        <span class="wl-msg">{{ item.msg }}</span>
      </div>

      <h3 class="wl-widget-headline">
        <span>Content classification</span>
        <span ng-show="isRunning" class="wl-spinner"></span>
      </h3>
      
      <div ng-show="annotation">
        <h4 class="wl-annotation-label">
          <i class="wl-annotation-label-icon"></i>
          {{ analysis.annotations[ annotation ].text }} 
          <small>[ {{ analysis.annotations[ annotation ].start }}, {{ analysis.annotations[ annotation ].end }} ]</small>
          <i class="wl-annotation-label-remove-icon" ng-click="selectAnnotation(undefined)"></i>
        </h4>
      </div>

      <wl-classification-box ng-repeat="box in configuration.classificationBoxes">
        <div ng-hide="annotation" class="wl-without-annotation">
          <wl-entity-tile is-selected="isEntitySelected(entity, box)" on-entity-select="onSelectedEntityTile(entity, box)" entity="entity" ng-repeat="entity in analysis.entities | filterEntitiesByTypesAndRelevance:box.registeredTypes"></wl-entity>
        </div>  
        <div ng-show="annotation" class="wl-with-annotation">
          <wl-entity-tile is-selected="isLinkedToCurrentAnnotation(entity)" on-entity-select="onSelectedEntityTile(entity, box)" entity="entity" ng-repeat="entity in analysis.annotations[annotation].entities | filterEntitiesByTypes:box.registeredTypes"" ></wl-entity>
        </div>  
      </wl-classification-box>

      <h3 class="wl-widget-headline">
        <span>Article Details</span>
      </h3>

      <h5 class="wl-widget-sub-headline">What</h5>
      <div class="wl-widget-wrapper">
        <div ng-repeat="category in analysis.categories | orderBy :'-relevance'" class="wl-category-wrapper">
          <i class="wl-toggle-off" />
          <span class="entity wl-thing"><i class="type" />
            {{category.label}}
          </span>
          <div class="wl-category-progress-background">
            <div class="wl-category-progress-current" style="width:{{category.relevance*100}}%"></div>
          </div>      
        </div>
      </div>  
      <h5 class="wl-widget-sub-headline">Who</h5>
      <div class="wl-widget-wrapper">
        <i class="wl-toggle-on" />
        <span class="entity wl-person"><i class="type" />
          {{configuration.currentUser}}
          <span class="wl-role">author</span>
        </span>
      </div>  
      <h5 class="wl-widget-sub-headline">Where</h5>
      <div class="wl-widget-wrapper">
        <i class="wl-toggle-off" />
        <span class="entity wl-place"><i class="type" />
          <span ng-show="configuration.publishedPlace">{{configuration.publishedPlace}}</span>
          <span ng-hide="configuration.publishedPlace" class="wl-geolocation-cta">Get Current Location</span>
          <span class="wl-role">publishing place</span>
        </span>
      </div>
      <h5 class="wl-widget-sub-headline">When</h5>
      <div class="wl-widget-wrapper">
        <i class="wl-toggle-on" />
        <span class="entity wl-event"><i class="type" />
          {{configuration.publishedDate}}
          <span class="wl-role">publishing date</span>
        </span>
      </div>

      <h3 class="wl-widget-headline"><span>Suggested images</span></h3>
      <div wl-carousel>
        <div ng-repeat="(image, label) in images" class="wl-card" wl-carousel-pane>
          <div class="wl-card-image"> 
            <img ng-src="{{image}}" wl-fallback="{{configuration.defaultThumbnailPath}}" />
          </div>
        </div>
      </div>

      <h3 class="wl-widget-headline"><span>Related posts</span></h3>
      <div wl-carousel>
        <div ng-repeat="post in relatedPosts" class="wl-card" wl-carousel-pane>
          <div class="wl-card-image"> 
            <img ng-src="{{post.thumbnail}}" wl-fallback="{{configuration.defaultThumbnailPath}}" />
          </div>
          <div class="wl-card-title">
            <a ng-href="{{post.link}}">{{post.post_title}}</a>
          </div>
        </div>
      </div>
      
      <div class="wl-entity-input-boxes">
        <wl-entity-input-box annotation="annotation" entity="entity" ng-repeat="entity in analysis.entities | isEntitySelected"></wl-entity-input-box>
        <div ng-repeat="(box, entities) in selectedEntities">
          <input type='text' name='wl_boxes[{{box}}][]' value='{{id}}' ng-repeat="(id, entity) in entities">
        </div> 
      </div>   
    </div>
  """)
  .appendTo('#wordlift-edit-post-outer-wrapper')

  injector = angular.bootstrap $('#wordlift-edit-post-wrapper'), ['wordlift.editpost.widget']

# Add WordLift as a plugin of the TinyMCE editor.
  tinymce.PluginManager.add 'wordlift', (editor, url) ->

# This plugin has to be loaded only with the main WP "content" editor
    return unless editor.id is "content"

    # Register event depending on tinymce major version
    fireEvent = (editor, eventName, callback)->
      switch tinymce.majorVersion
        when '4' then editor.on eventName, callback
        when '3' then editor["on#{eventName}"].add callback

    # Hack wp.mce.views to prevent shorcodes rendering
    # starts before the analysis is properly embedded
    injector.invoke(['EditorService', '$rootScope', '$log', (EditorService, $rootScope, $log) ->

# wp.mce.views uses toViews() method from WP 3.8 to 4.1
# and setMarkers() method from WP 4.2 to 4.3 to replace
# available shortcodes with coresponding views markup
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

    # Perform analysis once tinymce is loaded
    fireEvent(editor, "LoadContent", (e) ->
      injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log'
        (AnalysisService, EditorService, $rootScope, $log) ->
# execute the following commands in the angular js context.
          $rootScope.$apply(->
# Get the html content of the editor.
            html = editor.getContent format: 'raw'
            # Get the text content from the Html.
            text = Traslator.create(html).getText()
            if text.match /[a-zA-Z0-9]+/
# Disable tinymce editing
              EditorService.updateContentEditableStatus false
              AnalysisService.perform text
            else
              $log.warn "Blank content: nothing to do!"
          )
      ])
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
)
