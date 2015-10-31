'use strict';

# Test services.
describe 'services', ->
  beforeEach module('wordlift.tinymce.plugin.services')
  beforeEach module('AnalysisService')

  beforeEach inject((AnalysisService) ->
    AnalysisService.setKnownTypes window.wordlift.types
  )

  # Test the wlEntity directive.
  describe 'AnalysisService', ->
    it 'parses analysis data', inject((AnalysisService, $httpBackend, $rootScope) ->

      json = undefined

      # Get the mock-up analysis.
      $.ajax('base/app/assets/addtl_properties_1.json', async: false).done (data) -> json = data

      # Catch all the requests to Freebase.
      $httpBackend.when('HEAD', /.*/).respond(200, '')

      # Simulate event broadcasted by AnalysisService
      analysis = AnalysisService.parse json, true

      # Check that the analysis results conform.
      expect(analysis).toEqual jasmine.any(Object)

      for entityId, entity of analysis.entities
        # dump "[ entityId :: #{entityId} ][ props :: #{entity.props} ][ entity :: #{entity} ]"
        expect(entity.props).toEqual jasmine.any(Object)

        for propId, propValue of entity.props
          # dump "[ propId :: #{propId} ][ propValue :: #{propValue} ]"
          expect(propValue).not.toBe undefined

    )
