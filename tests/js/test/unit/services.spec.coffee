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

      # Get the mock-up analysis.
      $.ajax('base/app/assets/english.json', async: false).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        analysis = AnalysisService.parse data

        # Check that the analysis results conform.
        expect(analysis).toEqual jasmine.any(Object)
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

        # Get a Text Annotation and three entities that related to that Text Annotation.
        textAnnotationId = 'urn:enhancement-5adbf3ee-54d0-2445-6fdc-c09fec19c76f'
        entityAnnotation1Id = 'urn:enhancement-916d5d56-42b5-fc61-eae9-c282233d8b5f'
        entityAnnotation2Id = 'urn:enhancement-9430a584-c692-d419-b19f-c2bcbacba678'
        entityAnnotation3Id = 'urn:enhancement-9e5931fc-7a09-567d-65ff-05d34638b8e5'

        textAnnotation = analysis.textAnnotations[textAnnotationId]
        expect(textAnnotation).not.toBe undefined

        entityAnnotation1 = textAnnotation.entityAnnotations[entityAnnotation1Id]
        expect(entityAnnotation1).not.toBe undefined
        expect(entityAnnotation1.entity.sameAs).not.toBe undefined

        entityAnnotation2 = textAnnotation.entityAnnotations[entityAnnotation2Id]
        expect(entityAnnotation2).not.toBe undefined
        expect(entityAnnotation2.entity.sameAs).not.toBe undefined

        entityAnnotation3 = textAnnotation.entityAnnotations[entityAnnotation3Id]
        expect(entityAnnotation3).not.toBe undefined
        expect(angular.isArray(entityAnnotation3.entity.sameAs)).toBe true
    )

    it 'parses and merges analysis data', inject((AnalysisService, $httpBackend, $rootScope) ->

      # Get the mock-up analysis.
      $.ajax('base/app/assets/english.json', async: false).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        analysis = AnalysisService.parse data, true

        # Check that the analysis results conform.
        expect(analysis).toEqual jasmine.any(Object)
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

        # Get a Text Annotation and three entities that related to that Text Annotation.
        textAnnotationId = 'urn:enhancement-5adbf3ee-54d0-2445-6fdc-c09fec19c76f'
        entityAnnotationId = 'urn:enhancement-2b7ce8df-2bce-cdb8-faa8-f8b7d785b807'

        # Set a reference to the text annotation.
        textAnnotation = analysis.textAnnotations[textAnnotationId]
        expect(textAnnotation).not.toBe undefined

        entityAnnotation = textAnnotation.entityAnnotations[entityAnnotationId]
        expect(entityAnnotation).not.toBe undefined
        expect(entityAnnotation.entity).not.toBe undefined
        expect(entityAnnotation.entity.sameAs).not.toBe undefined

        # Set a reference to the entity.
        entity = entityAnnotation.entity
        expect(entity.thumbnails.length).toEqual 1
        expect(entity.thumbnails[0]).toEqual 'https://usercontent.googleapis.com/freebase/v1/image/m/05thd8b?maxwidth=4096&maxheight=4096'
        #        expect(entity.thumbnails[1]).toEqual 'https://usercontent.googleapis.com/freebase/v1/image/m/04js6kc?maxwidth=4096&maxheight=4096'
        #        expect(entity.thumbnails[2]).toEqual 'https://usercontent.googleapis.com/freebase/v1/image/m/04js6kq?maxwidth=4096&maxheight=4096'
        #        expect(entity.thumbnails[3]).toEqual 'https://usercontent.googleapis.com/freebase/v1/image/m/04mn0b4?maxwidth=4096&maxheight=4096'
        #        expect(entity.thumbnails[4]).toEqual 'https://usercontent.googleapis.com/freebase/v1/image/m/04mn0bt?maxwidth=4096&maxheight=4096'
        #
        expect(entityAnnotation.entity).not.toBe undefined for id, entityAnnotation of analysis.entityAnnotations

        for id, textAnnotation of analysis.textAnnotations
          for id, entityAnnotation of textAnnotation.entityAnnotations
            expect(entityAnnotation.entity).not.toBe undefined

    )

    it 'merges data while keeping sameAs', inject((AnalysisService, $httpBackend, $rootScope) ->

      url = 'base/app/assets/english.002.json'

      # dump "[ url :: #{url} ]"

      # Get the mock-up analysis.
      $.ajax(url, async: false).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        analysis = AnalysisService.parse data, true

        # Check that the analysis results conform.
        expect(analysis).toEqual jasmine.any(Object)
        expect(analysis.language).not.toBe undefined
        expect(analysis.entities).not.toBe undefined
        expect(analysis.entityAnnotations).not.toBe undefined
        expect(analysis.textAnnotations).not.toBe undefined
        expect(analysis.languages).not.toBe undefined
        expect(analysis.language).toEqual 'en'
        expect(Object.keys(analysis.entities).length).toEqual 18
        expect(Object.keys(analysis.entityAnnotations).length).toEqual 19
        expect(Object.keys(analysis.textAnnotations).length).toEqual 10
        expect(Object.keys(analysis.languages).length).toEqual 1

        # Get a Text Annotation and three entities that related to that Text Annotation.
        textAnnotationId = 'urn:enhancement-a6bb446e-6e95-d6be-e91c-32833aa58b32'
        entityAnnotationId = 'urn:enhancement-663e0cfd-c482-f695-674e-cae98e42dd18'

        # Set a reference to the text annotation.
        textAnnotation = analysis.textAnnotations[textAnnotationId]
        expect(textAnnotation).not.toBe undefined

        #        dump "[ #{id} ][ entity id :: #{entityAnnotation.entity.id} ][ #{entityAnnotation.entity.sameAs.length} ]" for id, entityAnnotation of textAnnotation.entityAnnotations

        # Set a reference to the entity annotation.
        entityAnnotation = textAnnotation.entityAnnotations[entityAnnotationId]

        expect(entityAnnotation).not.toBe undefined
        expect(entityAnnotation.entity).not.toBe undefined
        expect(entityAnnotation.entity.sameAs).not.toBe undefined

        # Set a reference to the entity.
        entity = entityAnnotation.entity
        expect(entity).not.toBe undefined
        # dump "[ entity id :: #{entity.id} ][ sameAs :: #{entity.sameAs} ][ thumbnails :: #{entity.thumbnails} ]"
        expect(entity.thumbnails.length).toEqual 9
        for i in [0...entity.thumbnails.length]
          expect(entity.thumbnails[i]).toEqual entity.thumbnails[i]

        expect(entityAnnotation.entity).not.toBe undefined for id, entityAnnotation of analysis.entityAnnotations

        for id, textAnnotation of analysis.textAnnotations
          for id, entityAnnotation of textAnnotation.entityAnnotations
            expect(entityAnnotation.entity).not.toBe undefined

    )

    it 'parses correctly analysis result without prefixes', inject((AnalysisService, $httpBackend, $rootScope) ->

      # Get the mock-up analysis.
      $.ajax('base/app/assets/tim_berners-lee.json',
        async: false
      ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        analysis = AnalysisService.parse data, true

        # Check that the analysis results conform.
        expect(analysis).toEqual jasmine.any(Object)
        expect(analysis.language).not.toBe undefined
        expect(analysis.entities).not.toBe undefined
        expect(analysis.entityAnnotations).not.toBe undefined
        expect(analysis.textAnnotations).not.toBe undefined
        expect(analysis.languages).not.toBe undefined
        expect(analysis.language).toEqual 'en'
        expect(Object.keys(analysis.entities).length).toEqual 9
        expect(Object.keys(analysis.entityAnnotations).length).toEqual 9
        expect(Object.keys(analysis.textAnnotations).length).toEqual 3
        expect(Object.keys(analysis.languages).length).toEqual 1
    )

    it 'finds entities for all the text annotations', inject((AnalysisService, $httpBackend, $rootScope) ->

      # Get the mock-up analysis.
      $.ajax('base/app/assets/eight_players_joined.json', async: false ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        analysis = AnalysisService.parse data, true

        # Check that the analysis results conform.
        expect(analysis).toEqual jasmine.any(Object)
        expect(analysis.language).not.toBe undefined
        expect(analysis.entities).not.toBe undefined
        expect(analysis.entityAnnotations).not.toBe undefined
        expect(analysis.textAnnotations).not.toBe undefined
        expect(analysis.languages).not.toBe undefined
        expect(analysis.language).toEqual 'en'
        expect(Object.keys(analysis.entities).length).toEqual 44
        expect(Object.keys(analysis.entityAnnotations).length).toEqual 46
        expect(Object.keys(analysis.textAnnotations).length).toEqual 18
        expect(Object.keys(analysis.languages).length).toEqual 1

        for id, textAnnotation of analysis.textAnnotations
          expect(Object.keys(textAnnotation.entityAnnotations).length).toBeGreaterThan 0
    )

    it 'parses correctly entity annotations that are related to more than one text annotation', inject((AnalysisService, $httpBackend, $rootScope) ->

      # Get the mock-up analysis.
      $.ajax('base/app/assets/sparql.json',
        async: false
      ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        analysis = AnalysisService.parse data, true

        # Check that the analysis results conform.
        expect(analysis).toEqual jasmine.any(Object)
        expect(analysis.language).not.toBe undefined
        expect(analysis.entities).not.toBe undefined
        expect(analysis.entityAnnotations).not.toBe undefined
        expect(analysis.textAnnotations).not.toBe undefined
        expect(analysis.languages).not.toBe undefined
        expect(analysis.language).toEqual 'en'
        #        expect(Object.keys(analysis.entities).length).toEqual 44
        expect(Object.keys(analysis.entityAnnotations).length).toEqual 21
        expect(Object.keys(analysis.textAnnotations).length).toEqual 12
        #        expect(Object.keys(analysis.languages).length).toEqual 1

        for textAnnotationId, textAnnotation of analysis.textAnnotations
#          dump "[ text-annotation id :: #{textAnnotationId} ][ selected text :: #{textAnnotation.selectedText} ][ entity annotations count :: #{Object.keys(textAnnotation.entityAnnotations).length} ]"
          expect(Object.keys(textAnnotation.entityAnnotations).length).toBeGreaterThan 0
        #          for entityAnnotationId, entityAnnotation of textAnnotation.entityAnnotations
        #            dump "[ entity-annotation id :: #{entityAnnotationId} ][ entity id :: #{entityAnnotation.entity.id} ][ confidence :: #{entityAnnotation.confidence} ]"

        entityAnnotation1 = analysis.textAnnotations['urn:enhancement-9de365a0-3312-4927-0cbd-8735d460901d']
        .entityAnnotations['urn:enhancement-1c03bb72-6cfe-3dfc-ad7f-3082a5ce086b']
        entityAnnotation2 = analysis.textAnnotations['urn:enhancement-d791d926-23e9-61f9-7b67-6414586bc49e']
        .entityAnnotations['urn:enhancement-1c03bb72-6cfe-3dfc-ad7f-3082a5ce086b']

        expect(entityAnnotation1).not.toBe undefined
        expect(entityAnnotation2).not.toBe undefined

        expect(entityAnnotation1).not.toBe entityAnnotation2
        expect(entityAnnotation1.selected).toBe false
        expect(entityAnnotation2.selected).toBe false

        entityAnnotation1.selected = true
        expect(entityAnnotation1.selected).toBe true
        expect(entityAnnotation2.selected).toBe false
    )

    it 'handles invalid responses', inject((AnalysisService, $httpBackend) ->

      # Get the mock-up analysis.
      $.ajax('base/app/assets/error.html',
        async: false
      ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        result = AnalysisService.parse data

        # Check that the analysis results conform.
        expect(result).toEqual false
    )

    it "parses the geo-location data", inject((AnalysisService, $httpBackend) ->

      # Get the mock-up analysis.
      $.ajax('base/app/assets/rome.json',
        async: false
      ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        analysis = AnalysisService.parse data, true

        # Check that the analysis results conform.
        expect(analysis).toEqual jasmine.any(Object)
        expect(analysis.language).not.toBe undefined
        expect(analysis.entities).not.toBe undefined
        expect(analysis.entityAnnotations).not.toBe undefined
        expect(analysis.textAnnotations).not.toBe undefined
        expect(analysis.languages).not.toBe undefined
        expect(analysis.language).toEqual 'en'

        uri1 = 'http://rdf.freebase.com/ns/m.06c62'
        uri2 = 'http://dbpedia.org/resource/Rome'

        entity1 = analysis.entities[uri1]
        entity2 = analysis.entities[uri2]

        expect(entity1).not.toBe undefined
        expect(entity2).not.toBe undefined

        expect(entity1.latitude).not.toBe undefined
        expect(entity1.longitude).not.toBe undefined

        expect(entity2.latitude).not.toBe undefined
        expect(entity2.longitude).not.toBe undefined

        expect(entity1.latitude).toEqual 41.9
        expect(entity1.longitude).toEqual 12.5

        expect(entity2.latitude).toEqual 41.9
        expect(entity2.longitude).toEqual 12.5

        expect(entity1).toBe entity2
    )


    it 'uses the text annotation selected text when an entity is missing a label', inject((AnalysisService) ->

      # The entity under testing.
      id = 'http://rdf.freebase.com/ns/m.0g4zp0c'

      # Will hold the analysis results.
      analysis = ''

      # Get the mock-up analysis.
      $.ajax('base/app/assets/kim_renberg.json', async: false).done (data) ->
        # Parse the analysis results and merge the results.
        analysis = AnalysisService.parse data, true


      # Get a reference to the entity.
      entity = analysis.entities[id]

      expect(entity).not.toBe undefined

      expect(entity.label).toBe 'Kim Renberg'
    )


  describe 'EditorService', ->
    it "embeds analysis results also when there are parentheses in the selected text", inject((AnalysisService, EditorService, $httpBackend) ->

      # Get the mock-up analysis.
      $.ajax('base/app/assets/tim_berners-lee_2.json',
        async: false
      ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        analysis = AnalysisService.parse data, true
        # Check that the analysis results conform.
        expect(analysis).toEqual jasmine.any(Object)

        EditorService.embedAnalysis analysis

    )

  # Test Helper functions.
  describe 'Helpers', ->

    # Test the uniqueId method.
    it 'returns unique IDs using the uniqueId method', inject((Helpers) ->
      expect(Helpers.uniqueId().length).toEqual 8
      expect(Helpers.uniqueId(16).length).toEqual 16
      expect(Helpers.uniqueId(32).length).toEqual 32

      expect(Helpers.uniqueId()).not.toEqual Helpers.uniqueId()
      expect(Helpers.uniqueId(16)).not.toEqual Helpers.uniqueId(16)
      expect(Helpers.uniqueId(32)).not.toEqual Helpers.uniqueId(32)

    )

    # Test object merging.
    it 'merges some params with default params', inject((Helpers) ->
      defaults =
        property_1: 'default_1'
        property_2: 'default_2'
        property_3: 'default_3'

      params =
        property_2: 'custom_2'
        property_4: 'custom_4'

      merged = Helpers.merge defaults, params

      expect(merged.property_1).toEqual 'default_1'
      expect(merged.property_2).toEqual 'custom_2'
      expect(merged.property_3).toEqual 'default_3'
      expect(merged.property_4).toEqual 'custom_4'

    )

  # Test the TextAnnotationService
  describe 'TextAnnotationService', ->

    # Test the creation of a Text Annotation using the defaults.
    it 'creates a TextAnnotation using defaults', inject((TextAnnotationService) ->

      # Create a Text Annotation with default data.
      ta_1 = TextAnnotationService.create()
      ta_2 = TextAnnotationService.create()

      expect(ta_1).not.toBe undefined
      expect(ta_2).not.toBe undefined

      expect(ta_1.id).not.toEqual ta_2.id
    )

    # Test the creation of a Text Annotation using the defaults.
    it 'creates a TextAnnotation using custom params', inject((TextAnnotationService) ->

      # Create a Text Annotation with default data.
      ta_1 = TextAnnotationService.create text: 'David Riccitelli', start: 10, end: 26, confidence: 0.75

      expect(ta_1).not.toBe undefined
      expect(ta_1.id).not.toBe undefined
      expect(ta_1.text).toEqual 'David Riccitelli'
      expect(ta_1.start).toEqual 10
      expect(ta_1.end).toEqual 26
      expect(ta_1.confidence).toEqual 0.75
      expect(ta_1.entityAnnotations).toEqual {}
      expect(ta_1._item).toBe null
    )

    it 'finds a TextAnnotation in the provided collection', inject((AnalysisService, TextAnnotationService) ->
      json = undefined

      # Get the mock-up analysis.
      $.ajax('base/app/assets/tim_berners-lee_2.json', async: false).done (data) ->
        json = data

      # Simulate event broadcasted by AnalysisService
      analysis = AnalysisService.parse json, true

      # Expected annotations.
      annotations = [
        { start: 44, end: 69, id: 'urn:enhancement-088ad932-6102-ce55-d7e4-bd817d0982e1', text: 'World Wide Web Consortium', confidence: 1 },
        { start: 249, end: 315, id: 'urn:enhancement-0dbbb1b6-0c74-6571-77eb-69098ee7719e', text: 'MIT Computer Science and Artificial Intelligence Laboratory (CSAIL', confidence: 0.98507464 },
        { start: 343, end: 374, id: 'urn:enhancement-1532e941-6e97-409a-3d3a-a8e7b10f9b06', text: 'Web Science Research Initiative', confidence: 1 },
        { start: 92, end: 101, id: 'urn:enhancement-4c0466cd-7156-c2ce-be65-bb1a8fd7639e', text: 'the Web\'s', confidence: 0.7777778 },
        { start: 249, end: 308, id: 'urn:enhancement-5bd990a1-a68d-e5b9-eeb6-74205293965a', text: 'MIT Computer Science and Artificial Intelligence Laboratory', confidence: 1 },
        { start: 92, end: 99, id: 'urn:enhancement-6e889961-1509-3108-c90e-20e414387b37', text: 'the Web', confidence: 0.85714287 },
        { start: 155, end: 180, id: 'urn:enhancement-6fb63cde-06fe-ea7c-665f-a1eac3ceb2e7', text: 'World Wide Web Foundation', confidence: 1 },
        { start: 347, end: 363, id: 'urn:enhancement-974f1919-5e6a-41ad-ea62-412329bb26d6', text: 'Science Research', confidence: 0.9375 },
        { start: 425, end: 463, id: 'urn:enhancement-d9dc798a-5e41-7d8d-5b3e-177bbed7a810', text: 'MIT Center for Collective Intelligence', confidence: 1 }
      ]

      # Check that each text annotation exists.
      for annotation in annotations
        textAnnotation = TextAnnotationService.find analysis.textAnnotations, annotation.start, annotation.end
        expect(textAnnotation).not.toBe undefined
        expect(textAnnotation.id).toEqual annotation.id
        expect(textAnnotation.start).toEqual annotation.start
        expect(textAnnotation.end).toEqual annotation.end
        expect(textAnnotation.text).toEqual annotation.text
        expect(textAnnotation.confidence).toEqual annotation.confidence
        expect(textAnnotation.entityAnnotations).toEqual jasmine.any(Object)
        expect(textAnnotation._item).toEqual jasmine.any(Object)

      # Test for a bogus annotation.
      textAnnotation = TextAnnotationService.find analysis.textAnnotations, 0, 100
      expect(textAnnotation).toBe undefined

#      for textAnnotationId, textAnnotation of analysis.textAnnotations
#        dump "[ textAnnotation :: "
#        dump textAnnotation
#        dump " ]"
#        dump "[ start :: #{textAnnotation.start} ][ end :: #{textAnnotation.end} ][ id :: #{textAnnotation.id} ][ text :: #{textAnnotation.text} ][ confidence :: #{textAnnotation.confidence} ]"

    )

  # Test the EntityAnnotationService
  describe 'EntityAnnotationService', ->
    it 'finds an EntityAnnotation in the provided collection using the selected switch', inject((AnalysisService, EntityAnnotationService) ->
      json = undefined

      # Get the mock-up analysis.
      $.ajax('base/app/assets/tim_berners-lee_2.json', async: false).done (data) ->
        json = data

      # Simulate event broadcasted by AnalysisService
      analysis = AnalysisService.parse json, true

      # Check that there are no selected entities and 16 non-selected entities.
      expect(EntityAnnotationService.find analysis.entityAnnotations, selected: true).toEqual []
      expect((EntityAnnotationService.find analysis.entityAnnotations, selected: false).length).toEqual 16
    )

    it 'finds an EntityAnnotation in the provided collection using the uri switch', inject((AnalysisService, EntityAnnotationService) ->
      json = undefined

      # Get the mock-up analysis.
      $.ajax('base/app/assets/tim_berners-lee_2.json', async: false).done (data) ->
        json = data

      # Simulate event broadcasted by AnalysisService
      analysis = AnalysisService.parse json, true

      # Expected annotations.
      annotations = [
        { id: 'urn:enhancement-0058ab06-872e-87c2-b842-a31c1cc709ea', taId: 'urn:enhancement-d9dc798a-5e41-7d8d-5b3e-177bbed7a810', uri: 'http://dbpedia.org/resource/MIT_Center_for_Collective_Intelligence' },
        { id: 'urn:enhancement-0f4452cc-865f-a826-9643-a3c14cbce17d', taId: 'urn:enhancement-088ad932-6102-ce55-d7e4-bd817d0982e1', uri: 'http://rdf.freebase.com/ns/m.05vsnz4' },
        { id: 'urn:enhancement-10f38ec8-5a67-7679-3208-9785138c38ff', taId: 'urn:enhancement-974f1919-5e6a-41ad-ea62-412329bb26d6', uri: 'http://rdf.freebase.com/ns/m.03qbp5p' },
        { id: 'urn:enhancement-17b7acd9-5a0e-4148-4a2b-d04194644759', taId: 'urn:enhancement-6e889961-1509-3108-c90e-20e414387b37', uri: 'http://dbpedia.org/resource/World_Wide_Web' },
        { id: 'urn:enhancement-2be1807b-40ee-6234-86a5-616e57a6f65e', taId: 'urn:enhancement-088ad932-6102-ce55-d7e4-bd817d0982e1', uri: 'http://dbpedia.org/resource/World_Wide_Web_Consortium' },
        { id: 'urn:enhancement-2f8adc47-8682-a6b8-60f9-e48d82cb2f6c', taId: 'urn:enhancement-6e889961-1509-3108-c90e-20e414387b37', uri: 'http://dbpedia.org/resource/The_Web_(film)' },
        { id: 'urn:enhancement-34c8b63f-eb73-f66d-12c6-980d5647ff73', taId: 'urn:enhancement-5bd990a1-a68d-e5b9-eeb6-74205293965a', uri: 'http://dbpedia.org/resource/MIT_Computer_Science_and_Artificial_Intelligence_Laboratory' },
        { id: 'urn:enhancement-966a3108-71e5-a2c4-9aa8-4ed8612bbcf0', taId: 'urn:enhancement-6fb63cde-06fe-ea7c-665f-a1eac3ceb2e7', uri: 'http://dbpedia.org/resource/World_Wide_Web_Foundation' },
#        { id: 'urn:enhancement-a4a46550-d539-31af-4c75-38974004d522', taId: 'urn:enhancement-088ad932-6102-ce55-d7e4-bd817d0982e1', uri: 'http://dbpedia.org/resource/World_Wide_Web_Consortium' },
        { id: 'urn:enhancement-af9b08a7-6113-6a53-585f-d42616f7a7e9', taId: 'urn:enhancement-4c0466cd-7156-c2ce-be65-bb1a8fd7639e', uri: 'http://rdf.freebase.com/ns/m.01w4jzb' },
        { id: 'urn:enhancement-b18dfa64-5315-de60-adf5-a39c4bb02065', taId: 'urn:enhancement-4c0466cd-7156-c2ce-be65-bb1a8fd7639e', uri: 'http://rdf.freebase.com/ns/m.01wf0c1' },
        { id: 'urn:enhancement-d670437e-e622-4be2-4ee1-01b617859fe0', taId: 'urn:enhancement-0dbbb1b6-0c74-6571-77eb-69098ee7719e', uri: 'http://dbpedia.org/resource/MIT_Computer_Science_and_Artificial_Intelligence_Laboratory' },
        { id: 'urn:enhancement-d7509c9e-c583-2059-3994-8e6ce1920225', taId: 'urn:enhancement-1532e941-6e97-409a-3d3a-a8e7b10f9b06', uri: 'http://dbpedia.org/resource/Web_Science_Trust' },
#        { id: 'urn:enhancement-f0f4f413-32a1-4dfb-57ef-de5f5b42af94', taId: 'urn:enhancement-6fb63cde-06fe-ea7c-665f-a1eac3ceb2e7', uri: 'http://dbpedia.org/resource/World_Wide_Web_Foundation' },
        { id: 'urn:enhancement-f4283ac3-1f2c-f252-071a-5087c911ba88', taId: 'urn:enhancement-4c0466cd-7156-c2ce-be65-bb1a8fd7639e', uri: 'http://dbpedia.org/resource/The_Web_(TV_series)' },
        { id: 'urn:enhancement-f8b9e18c-e8a1-5bb1-877d-0c33f53e8e50', taId: 'urn:enhancement-6e889961-1509-3108-c90e-20e414387b37', uri: 'http://dbpedia.org/resource/The_Web_(TV_series)' }
      ]

      # Check that each entity annotation matches.
      for annotation in annotations
        entityAnnotations = EntityAnnotationService.find analysis.textAnnotations[annotation.taId].entityAnnotations, uri: annotation.uri
        expect(entityAnnotations.length).toEqual 1
        expect(entityAnnotations[0].id).toEqual annotation.id

      # Test for a bogus annotation.
      entityAnnotation = EntityAnnotationService.find analysis.entityAnnotations, uri: 'http://example.org/bogus'
      expect(entityAnnotation).toEqual []

#      for entityAnnotationId, entityAnnotation of analysis.entityAnnotations
#        dump "[ entityAnnotation :: "
#        dump entityAnnotation
#        dump " ]"
#        dump "[ id :: #{entityAnnotation.id} ][ entity id :: #{entityAnnotation.entity.id} ][ relation id :: #{entityAnnotation.relation.id} ][ selected :: #{entityAnnotation.selected} ]"

    )