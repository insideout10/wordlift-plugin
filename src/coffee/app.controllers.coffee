angular.module('wordlift.tinymce.plugin.controllers',
  [ 'wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services' ])
.filter('orderObjectBy', ->
    (items, field, reverse) ->
      filtered = []

      angular.forEach items, (item) ->
        filtered.push(item)

      filtered.sort (a, b) ->
        a[field] > b[field]

      filtered.reverse() if reverse

      filtered
  )
.filter('filterObjectBy', ->
    (items, field, value) ->
      filtered = []

      angular.forEach items, (item) ->
        filtered.push(item) if item[field] is value

      filtered
  )
.controller('EntitiesController', ['AnalysisService','EntityAnnotationService','EditorService', '$http', '$log', '$scope', '$rootScope', (AnalysisService, EntityAnnotationService, EditorService, $http, $log, $scope, $rootScope) ->

    $scope.isRunning = false
    # holds a reference to the current analysis results.
    $scope.analysis = AnalysisService.createAnEmptyAnalysis()
    # holds a reference to the selected text annotation.
    $scope.textAnnotation = null
    # holds a reference to the selected text annotation span.
    $scope.textAnnotationSpan = null
    # new entity model
    $scope.newEntity = {
      label: null
      type: null
    }

    # Toolbar
    $scope.activeToolbarTab = 'Add new entity'
    $scope.isActiveToolbarTab  = (tab)->
      $scope.activeToolbarTab is tab
    $scope.setActiveToolbarTab  = (tab)->
      if $scope.activeToolbarTab is tab
        return 
      $scope.autocompleteOpened = false
      $scope.activeToolbarTab = tab
    
    $scope.autocompleteOpened = false
  
    # holds a reference to the knows types from AnalysisService
    $scope.knownTypes = null
    
    setArrowTop = (top) ->
      $('head').append('<style>#wordlift-disambiguation-popover .postbox:before,#wordlift-disambiguation-popover .postbox:after{top:' + top + 'px;}</style>');

    # a reference to the current text annotation span in the editor.
    el = undefined
    scroll = ->
      return if not el?
      # get the position of the clicked element.
      pos = EditorService.getWinPos(el)
      # set the popover arrow to the element position.
      setArrowTop(pos.top - 50)

    # TODO: move these hooks on the popover, in order to hook/unhook the events.
    $(window).scroll(scroll)
    $('#content_ifr').contents().scroll(scroll)
    

    # Search for entities server side
    $scope.onSearch = (term) ->
      return $http
        method: 'post'
        url: ajaxurl + '?action=wordlift_search'
        data: { 'term' : term }
      .then (response) ->
        # Create a fake entity annotation for each entity
        response.data.map (entity)->
          EntityAnnotationService.create { 'entity': entity }
    
    # Create a new entity from the disambiguation widget
    $scope.onNewEntityCreate = (entity) ->
      $scope.isRunning = true
    
      $http
        method: 'post'
        url: ajaxurl + '?action=wordlift_add_entity'
        data: $scope.newEntity
      .success (data, status, headers, config) ->
        $scope.isRunning = false
        # Create a fake entity annotation for each entity
        entityAnnotation = EntityAnnotationService.create { 'entity': data }
        # Enhance current analysis with the selected entity if needed 
        if AnalysisService.enhance($scope.analysis, $scope.textAnnotation, entityAnnotation) is true
          # Update the editor accordingly 
          $scope.$emit 'selectEntity', ta: $scope.textAnnotation, ea: entityAnnotation
      .error (data, status, headers, config) ->
        $scope.isRunning = false
        $log.debug "Got en error on onNewEntityCreate"

    # Search for entities server side
    $scope.onSearchedEntitySelected = (entityAnnotation) ->
      # Enhance current analysis with the selected entity if needed

      if AnalysisService.enhance($scope.analysis, $scope.textAnnotation, entityAnnotation) is true
        # Update the editor accordingly 
        $scope.$emit 'selectEntity', ta: $scope.textAnnotation, ea: entityAnnotation

    # On entity click emit a selectEntity event 
    $scope.onEntitySelected = (textAnnotation, entityAnnotation) ->
      $scope.$emit 'selectEntity', ta: textAnnotation, ea: entityAnnotation

    # Receive the analysis results and store them in the local scope.
    $scope.$on 'analysisReceived', (event, analysis) ->
      $scope.analysis = analysis

    $scope.$on 'autocompleteOpened', (event) ->
      $scope.autocompleteOpened = true

    $scope.$on 'autocompleteClosed', (event) ->
      $scope.autocompleteOpened = false
    
    $scope.$on 'configurationTypesLoaded', (event, types)->
      $scope.knownTypes = types

    # When a text annotation is added, enhance current analysis 
    # and open the disambiguation popover.
    $scope.$on 'textAnnotationAdded', (event, textAnnotation) ->
      # Add the text annotation to the current analysis
      AnalysisService.addTextAnnotation $scope.analysis, textAnnotation
      # Simulate a text annotation click event in order to open the popover
      $scope.$broadcast 'textAnnotationClicked', textAnnotation.id
     
    # When a text annotation is clicked, open the disambiguation popover.
    $scope.$on 'textAnnotationClicked', (event, textAnnotationId) ->

      # Set the current text annotation to the one specified.
      $scope.textAnnotation = $scope.analysis?.textAnnotations[textAnnotationId]
      # Set default new entity label accordingly to the current textAnnotation Text
      $scope.newEntity.label = $scope.textAnnotation?.text

      # hide the popover if there are no entities.
      if not $scope.textAnnotation?.entityAnnotations? 
        $('#wordlift-disambiguation-popover').hide()
        # show the popover.
      else

        # get the position of the clicked element.
        pos = EditorService.getWinPos(textAnnotationId)
        # set the popover arrow to the element position.
        setArrowTop(pos.top - 50)
        # show the popover.
        $('#wordlift-disambiguation-popover').show()

  ])
.controller('ErrorController', ['$element', '$scope', '$log', ($element, $scope, $log) ->

    # Set the element as a jQuery UI Dialog.
    element = $($element).dialog
      title: 'WordLift'
      dialogClass: 'wp-dialog'
      modal: true
      autoOpen: false
      closeOnEscape: true
      buttons:
        Ok: ->
          $(this).dialog 'close'

    # Show the dialog box when an error is raised.
    $scope.$on 'error', (event, message) ->
      $scope.message = message
      element.dialog 'open'

  ])
