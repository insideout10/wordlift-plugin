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
        <i ng-class="{ 'wl-more': contentClassificationOpened == false, 'wl-less': contentClassificationOpened == true }" ng-click="toggleCurrentSection()"></i>      
        <span ng-show="isRunning" class="wl-spinner"></span>
      </h3>
     <div ng-show="contentClassificationOpened">
      
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
          <wl-entity-tile show-confidence="false" is-selected="isEntitySelected(entity, box)" on-entity-select="onSelectedEntityTile(entity, box)" entity="entity" ng-repeat="entity in analysis.entities | filterEntitiesByTypesAndRelevance:box.registeredTypes"></wl-entity>
        </div>  
        <div ng-show="annotation" class="wl-with-annotation">
          <wl-entity-tile show-confidence="false" is-selected="isLinkedToCurrentAnnotation(entity)" on-entity-select="onSelectedEntityTile(entity, box)" entity="entity" ng-repeat="entity in analysis.annotations[annotation].entities | filterEntitiesByTypes:box.registeredTypes"" ></wl-entity>
        </div>  
      </wl-classification-box>

    </div>

      <h3 class="wl-widget-headline">
        <span>Article metadata</span>
        <i ng-class="{ 'wl-more': articleMetadataOpened == false, 'wl-less': articleMetadataOpened == true }" ng-click="toggleCurrentSection()"></i>
        <span ng-show="isRunning" class="wl-spinner"></span>
      </h3>
      <div ng-show="articleMetadataOpened">
      <h5 class="wl-widget-sub-headline">What <small>Topic</small></h5>
      <div class="wl-without-annotation">
        <wl-entity-tile show-confidence="true" is-selected="isTopic(topic)" on-entity-select="onTopicSelected(topic)" entity="topic" ng-repeat="topic in analysis.topics | orderBy :'-confidence'"></wl-entity-tile>
      </div>

      <h5 class="wl-widget-sub-headline">Who <small>Author</small></h5>
      <div class="wl-widget-wrapper">
        <i class="wl-toggle-on wl-disabled" />
        <span class="entity wl-person"><i class="type" />
          {{configuration.currentUser}}
        </span>
      </div>  

      <h5 class="wl-widget-sub-headline">Where <small>Publishing Place</small></h5>
      <div class="wl-widget-wrapper" ng-hide="hasPublishedPlace()">
        <i class="wl-toggle-off" />
        <span class="entity wl-place"><i class="type" />
          <span ng-click="getLocation()" class="wl-cta-location">Get current location</span>
        </span>
      </div> 
      <div class="wl-without-annotation">
        <wl-entity-tile show-confidence="false" is-selected="isPublishedPlace(entity)" on-entity-select="onPublishedPlaceSelected(entity)" entity="entity" ng-repeat="entity in suggestedPlaces"></wl-entity-tile>
      </div>

      <h5 class="wl-widget-sub-headline">When <small>Publishing Date</small></h5>
      <div class="wl-widget-wrapper">
        <i class="wl-toggle-on wl-disabled" />
        <span class="entity wl-event"><i class="type" />
          {{configuration.publishedDate}}
        </span>
      </div>
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
        <wl-entity-input-box entity="entity" ng-repeat="entity in analysis.entities | isEntitySelected"></wl-entity-input-box>
        <wl-entity-input-box entity="topic" ng-if="topic"></wl-entity-input-box>
        <wl-entity-input-box entity="publishedPlace" ng-if="publishedPlace"></wl-entity-input-box>
        <div ng-repeat="(box, entities) in selectedEntities">
          <input type='text' name='wl_boxes[{{box}}][]' value='{{id}}' ng-repeat="(id, entity) in entities">
        </div>
        <input type='text' name='wl_metadata[wl_topic]' value='{{topic.id}}' ng-if="topic">
        <input type='text' name='wl_metadata[wl_location_created]' value='{{publishedPlace.id}}' ng-if="publishedPlace">
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
