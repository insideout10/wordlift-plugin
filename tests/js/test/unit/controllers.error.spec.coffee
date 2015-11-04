describe "Controllers: ErrorController", ->
  scope = undefined
  controller = undefined

  # Tests set-up.
  beforeEach module('wordlift.tinymce.plugin.controllers')
  beforeEach inject(($controller, $rootScope) ->
    # Create the scope for the controller.
    scope = $rootScope.$new()

    element = angular.element.find '#wl-error-controller'

    # Set-up the spy on the event receiver.
    spyOn(scope, '$on').and.callThrough()

    # Set-up the ErrorController.
    controller = $controller 'ErrorController', {
      $scope: scope
      $element: element
    }

  )

  it "receives errors from failed analysis", inject((AnalysisService, $httpBackend) ->

    # Request the mock up of an errored analysis.
    $.ajax('base/app/assets/error.html',
      async: false
    ).done (data) ->
      $httpBackend.expectPOST('/base/app/assets/english.json?action=wordlift_analyze')
      .respond 200, data

      $httpBackend.when('HEAD', /.*/).respond(200, '')

      # Check for the dialog box not to be visible.
      expect($('#wl-error-controller')).not.toBeVisible()

      # Parse and merge the data.
      AnalysisService.parse data, true

      # Check that the error event has been called.
      expect(scope.$on).toHaveBeenCalledWith('error', jasmine.any(Function))
      scope.$digest()

      # Check that the dialog box is now visible.
      expect($('#wl-error-controller')).toBeVisible()
  )