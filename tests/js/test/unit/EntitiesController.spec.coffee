describe "EditorController tests", ->
  $scope = undefined
  EntitiesController = undefined
  analysis = undefined

  # Tests set-up.
  beforeEach module('wordlift.tinymce.plugin.controllers')
  beforeEach inject(($controller, $rootScope) ->
    # Spy on the scopes.
    spyOn($rootScope, '$broadcast').and.callThrough()

    # Create a scope and spy on it.
    $scope = $rootScope.$new()
    spyOn($scope, '$on').and.callThrough()

    # Set the EntitiesController.
    EntitiesController = $controller 'EntitiesController', {$scope: $scope}
  )

  beforeEach inject( (AnalysisService) ->
    AnalysisService.setKnownTypes window.wordlift.types
  )

  it "loads an empty analysis object on bootstrap", inject((AnalysisService) ->
    # Expect the current analysis id not undefined
    expect($scope.analysis).not.toBe undefined
    # Expect the current analysis is an empty analysis object
    expect($scope.analysis).toEqual AnalysisService.createAnEmptyAnalysis() 
  )

  it "loads an analysis", inject((AnalysisService, $httpBackend, $rootScope) ->

    $.ajax('base/app/assets/english.json', async: false).done (data) ->
      $httpBackend.expectPOST('/base/app/assets/english.json?action=wordlift_analyze')
      .respond 200, data

      $httpBackend.when('HEAD', /.*/).respond(200, '')

      AnalysisService.analyze ''

      $httpBackend.flush()

      # Check that the root scope broadcast method has been called.
      expect($rootScope.$broadcast).toHaveBeenCalledWith('analysisReceived', jasmine.any(Object))

      # Check that the root scope broadcast method has been called.
      expect($rootScope.$broadcast).toHaveBeenCalledWith('configurationTypesLoaded', jasmine.any(Object))

      # Get a reference to the analysis structure.
      args = $rootScope.$broadcast.calls.argsFor 1
      analysis = args[1]

      expect(analysis).not.toBe undefined

      # Check that the analysis results conform.
      expect(analysis.language).not.toBe undefined
      expect(analysis.entities).not.toBe undefined
      expect(analysis.entityAnnotations).not.toBe undefined
      expect(analysis.textAnnotations).not.toBe undefined
      expect(analysis.languages).not.toBe undefined

      expect(analysis.language).toEqual 'en'
      expect(Object.keys(analysis.entities).length).toEqual 18
      expect(Object.keys(analysis.entityAnnotations).length).toEqual 19
      expect(Object.keys(analysis.textAnnotations).length).toEqual 12
      expect(Object.keys(analysis.languages).length).toEqual 1

      # Check that the scope has been called with analysisReceived.
      expect($scope.$on).toHaveBeenCalledWith('analysisReceived', jasmine.any(Function))

      # Check that the analysis saved in the scope equals the one sent by the AnalysisService.
      expect($scope.analysis).toEqual analysis

      # Check that the disambiguation popover is not visible.
      expect($('#wordlift-disambiguation-popover')).not.toBeVisible()

      for id, textAnnotation of analysis.textAnnotations

        # Send the textAnnotationClicked.
        $rootScope.$broadcast 'textAnnotationClicked', id, {target: $('body')[0]}

        # Check that the textAnnotationClicked event has been received.
        expect($scope.$on).toHaveBeenCalledWith('textAnnotationClicked', jasmine.any(Function))

        # Check that information inside the scope are updated accordingly.
        expect($scope.textAnnotation.id).toEqual id
        expect($scope.textAnnotation).toEqual textAnnotation
        entityAnnotations = Object.keys($scope.textAnnotation.entityAnnotations)
        expect(entityAnnotations.length).toBeGreaterThan -1
        
        # TODO If entity annotations are missing it should be a user generated text annotation
        
        # Check that the disambiguation popover is visible.
        # if 0 is entityAnnotations.length
        #  expect($('#wordlift-disambiguation-popover')).not.toBeVisible()
        # else
        expect($('#wordlift-disambiguation-popover')).toBeVisible()
  )

  it "add a new text annotation to the current analysis object", inject((AnalysisService, $rootScope) ->
    # Define a fake textAnnotation object
    textAnnotation = { 
      id: 'foo' 
      start: 10
      end: 20
    }
    
    # Check that there are no text annotations in the current analysis with the given id
    expect($scope.analysis.textAnnotations['foo']).toBe undefined   
    # Simulate the EditorService behaviour
    $rootScope.$broadcast 'textAnnotationAdded', textAnnotation
    # Check if the current analysis has been properly enhanced
    expect($scope.analysis.textAnnotations['foo']).not.toBe undefined
    expect($scope.analysis.textAnnotations['foo']).toEqual textAnnotation
    # Check if textAnnotationAdded event is properly fired
    expect($scope.$broadcast).toHaveBeenCalledWith 'textAnnotationClicked',  textAnnotation.id 
        
  )
