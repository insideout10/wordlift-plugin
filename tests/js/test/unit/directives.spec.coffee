'use strict';

# Test directives.
describe 'directives', ->
  beforeEach module('wordlift.tinymce.plugin.directives')
  beforeEach module('AnalysisService')

  beforeEach inject( (AnalysisService) ->
    AnalysisService.setKnownTypes window.wordlift.types
  )

  # Test the wlEntity directive.
  describe 'wlEntity', ->
    scope = undefined
    element = undefined

    # Get the root scope and create a wl-entities element.
    beforeEach inject(($rootScope) ->

      scope = $rootScope.$new()
      scope.currentTypeDefinition = window.wordlift.types[0]

      # The wlEntities directive gets the annotation from the text-annotation attribute.
      element = angular.element '<wl-entity type-definition="currentTypeDefinition" on-select="select(entityAnnotation)" entity-annotation="entityAnnotation"></wl-entities>'
    )

    it 'fires the select method with the entityAnnotation', inject(($compile) ->
      # Create a mock entity annotation
      scope.entityAnnotation = {}
      # Create a mock select method.
      scope.select = (item) -> # Do nothing
      spyOn scope, 'select'

      # Compile the directive.
      $compile(element)(scope)
      scope.$digest()

      # Simulate the click on the element.
      element.children()[0].click()

      # Check that the select event has been called.
      expect(scope.select).toHaveBeenCalledWith(scope.entityAnnotation)
    )


  # Test the wlEntities directive.
  describe 'wlEntities', ->
    scope = undefined
    element = undefined
    EntitiesController = undefined

    # Get the root scope and create a wl-entities element.
    beforeEach inject(($rootScope, $controller) ->

      # Create a new scope, the scope will be shared between the entities controller and the wlEntities directive,
      # as in the HTML the textAnnotation is passed using the 'textAnnotation' property of the EntitiesController
      # scope.
      scope = $rootScope.$new()
      scope.knowTypes = window.wordlift.types

      # Create the EntitiesController with the new scope.
      EntitiesController = $controller 'EntitiesController', { $scope: scope }

      # The wlEntities directive gets the annotation from the text-annotation attribute.
      element = angular.element '<wl-entities entity-types="knowTypes" on-select="select(textAnnotation, entityAnnotation)" text-annotation="textAnnotation"></wl-entities>'
    )

    # Test for the entity to empty.
    it 'should be empty', inject(($compile) ->

      # Compile the directive.
      $compile(element)(scope)
      scope.$digest()

      # Check that there's an empty list.
      expect(element.find('ul').length).toEqual 1
      expect(element.find('li').length).toEqual 0
    )

    # Test entity is not empty.
    it 'should not be empty', inject(($compile, $rootScope, AnalysisService, $httpBackend) ->

      # Create a mock select method.
      scope.select = (ta, ea) -> # Do nothing
      spyOn scope, 'select'

      # Compile the directive.
      $compile(element)(scope)
      scope.$digest()

      # Get the mock-up analysis.
      $.ajax('base/app/assets/english.json', async: false).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        $rootScope.$broadcast 'analysisReceived', AnalysisService.parse data

        # Create a fake textAnnotation element (the textAnnotation exists in the mockup data).
        textAnnotation = angular.element '<span id="urn:enhancement-5adbf3ee-54d0-2445-6fdc-c09fec19c76f" class="textannotation">Rome</span>'

        # Simulate event broadcasted by EditorService on annotation click
        $rootScope.$broadcast 'textAnnotationClicked', textAnnotation.attr('id'), { target: textAnnotation }

        # Process changes.
        scope.$digest()

        entitiesElems = element.find('wl-entity > div')
        expect(entitiesElems.length).toEqual 7

        # Set the ID of the entity annotations (from the mock file).
        id1 = 'urn:enhancement-9e5931fc-7a09-567d-65ff-05d34638b8e5'
        id2 = 'urn:enhancement-3ca7b689-4704-25e0-6e9f-b8cad136be17'

#        for e in entitiesElems
#          dump e

        # Click the first entity.
        entitiesElems[1].click()
        
        expect(scope.textAnnotation.entityAnnotations[id1].selected).toBe true
        expect(scope.textAnnotation.entityAnnotations[id2].selected).toBe false
        # Check that the select event has been called.
        expect(scope.select).toHaveBeenCalledWith(scope.textAnnotation, scope.textAnnotation.entityAnnotations[id1])

        # Click on the second entity.
        entitiesElems[2].click()
        expect(scope.textAnnotation.entityAnnotations[id1].selected).toBe false
        expect(scope.textAnnotation.entityAnnotations[id2].selected).toBe true
        # Check that the select event has been called.
        expect(scope.select).toHaveBeenCalledWith(scope.textAnnotation, scope.textAnnotation.entityAnnotations[id2])

        # Click again on the second entity.
        entitiesElems[2].click()
        expect(scope.textAnnotation.entityAnnotations[id1].selected).toBe false
        expect(scope.textAnnotation.entityAnnotations[id2].selected).toBe false
        # Check that the select event has been called.
        expect(scope.select).toHaveBeenCalledWith(scope.textAnnotation, null)

    )

    # Test entity is not empty.
    it 'works well with entity annotations that relate to one or more text annotation', inject(($compile, $rootScope, AnalysisService, $httpBackend) ->
      # Create a mock select method.
      scope.select = (ta, ea) -> # Do nothing
      spyOn scope, 'select'

      # Compile the directive.
      $compile(element)(scope)
      scope.$digest()

      # Get the mock-up analysis.
      $.ajax('base/app/assets/sparql.json',
        async: false
      ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        $rootScope.$broadcast 'analysisReceived', AnalysisService.parse data, true

        # Create a fake textAnnotation element (the textAnnotation exists in the mockup data).
        elements = []
        elements[0] = angular.element '<span id="urn:enhancement-d791d926-23e9-61f9-7b67-6414586bc49e" class="textannotation">Lorem Ipsum</span>'
        elements[1] = angular.element '<span id="urn:enhancement-9de365a0-3312-4927-0cbd-8735d460901d" class="textannotation">Lorem Ipsum</span>'
        elements[2] = angular.element '<span id="urn:enhancement-aaf7f73e-910a-3516-1f58-d5c41c42ab0b" class="textannotation">Lorem Ipsum</span>'

        entityAnnotations = []
        entityAnnotations[0] = 'urn:enhancement-ee057616-a5b7-e0c1-1111-24d2304417ff'
        entityAnnotations[1] = 'urn:enhancement-1c03bb72-6cfe-3dfc-ad7f-3082a5ce086b'

        # Simulate event broadcasted by EditorService on annotation click
        $rootScope.$broadcast 'textAnnotationClicked', elements[0].attr('id'), { target: elements[0] }
        scope.$digest()

        entities = element.find('wl-entity > div')
        expect(entities.length).toEqual 2

        entities[0].click()

        # Simulate event broadcasted by EditorService on annotation click
        $rootScope.$broadcast 'textAnnotationClicked', elements[1].attr('id'), { target: elements[1] }
        scope.$digest()

        entities = element.find('wl-entity > div')
        expect(entities.length).toEqual 1

        entities[0].click()

        # Simulate event broadcasted by EditorService on annotation click
        $rootScope.$broadcast 'textAnnotationClicked', elements[2].attr('id'), { target: elements[2] }
        scope.$digest()

        entities = element.find('wl-entity > div')
        expect(entities.length).toEqual 3

        for id, entityAnnotation of scope.textAnnotation.entityAnnotations
          expect(entityAnnotation.selected).toBe false

    )

  describe 'wlEntityInputBoxes', ->
    scope = undefined
    element = undefined
    EntitiesController = undefined

    # Get the root scope and create a wl-entities element.
    beforeEach inject(($rootScope, $controller) ->

      # Create a new scope, the scope will be shared between the entities controller and the wlEntities directive,
      # as in the HTML the textAnnotation is passed using the 'textAnnotation' property of the EntitiesController
      # scope.
      scope = $rootScope.$new()

      # Create the EntitiesController with the new scope.
      EntitiesController = $controller 'EntitiesController', { $scope: scope }

      # The wlEntities directive gets the annotation from the text-annotation attribute.
      element = angular.element '<wl-entity-input-boxes text-annotations="analysis.textAnnotations"></wl-entity-input-boxes>'
    )

    # Test for the entity to empty.
    it 'creates input boxes and textareas with entity data (non-merged)', inject((AnalysisService, $compile, $httpBackend, $rootScope) ->

      # Compile the directive.
      $compile(element)(scope)
      scope.$digest()

      # Get the mock-up analysis.
      $.ajax('base/app/assets/english.json',
        async: false
      ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        $rootScope.$broadcast 'analysisReceived', AnalysisService.parse data

        # Check that the analysis is set.
        expect(scope.analysis).not.toBe undefined
        expect(scope.analysis.textAnnotations).not.toBe undefined

        # Check that there are no input boxes (no entities selected).
        expect(element.find('input').length).toEqual 0
        expect(element.find('textarea').length).toEqual 0

        # Select a text annotation.
        textAnnotation1 = scope.analysis.textAnnotations['urn:enhancement-5adbf3ee-54d0-2445-6fdc-c09fec19c76f']
        expect(textAnnotation1).not.toBe undefined

        # Select an entity annotation in the first text annotation.
        entityAnnotation1 = textAnnotation1.entityAnnotations['urn:enhancement-3ca7b689-4704-25e0-6e9f-b8cad136be17']
        expect(entityAnnotation1).not.toBe undefined

        # Select one entity.
        entityAnnotation1.selected = true
        scope.$digest()

        # Check that there are no input boxes (no entities selected).
        fieldName1 = "wl_entities\\[#{entityAnnotation1.entity.id}\\]"
        expect(element.find('input').length).toEqual 40
        expect(element.find('textarea').length).toEqual 1

        expect(element.find("input[name='#{fieldName1}\\[uri\\]']")[0].value).toEqual entityAnnotation1.entity.id
        expect(element.find("input[name='#{fieldName1}\\[label\\]']")[0].value).toEqual entityAnnotation1.entity.label
        expect(element.find("input[name='#{fieldName1}\\[main_type\\]']")[0].value).toEqual entityAnnotation1.entity.type
        expect(element.find("input[name='#{fieldName1}\\[image\\]\\[\\]']")[0].value).toEqual entityAnnotation1.entity.thumbnails[0]
#        expect(element.find("input[name='#{fieldName1}\\[image\\]\\[\\]']")[1].value).toEqual entityAnnotation1.entity.thumbnails[1]
#        expect(element.find("input[name='#{fieldName1}\\[image\\]\\[\\]']")[2].value).toEqual entityAnnotation1.entity.thumbnails[2]
#        expect(element.find("input[name='#{fieldName1}\\[image\\]\\[\\]']")[3].value).toEqual entityAnnotation1.entity.thumbnails[3]

        # Get the decoded description and check it against the entity.
        description = $(element.find("textarea[name='#{fieldName1}\\[description\\]']")[0]).text()
        expect(description).toEqual entityAnnotation1.entity.description

        # Deselect the entity.
        entityAnnotation1.selected = false
        scope.$digest()

        # Check that no inputs are selected.
        expect(element.find('input').length).toEqual 0
        expect(element.find('textarea').length).toEqual 0

        # Reselect the entity.
        entityAnnotation1.selected = true
        scope.$digest()

        # Select a text annotation.
        textAnnotation2 = scope.analysis.textAnnotations['urn:enhancement-4acc5839-64c3-25e9-aa3a-ee8d039bf2c8']
        expect(textAnnotation2).not.toBe undefined

        # Select an entity annotation in the first text annotation.
        entityAnnotation2 = textAnnotation2.entityAnnotations['urn:enhancement-ad6add3f-d70d-7955-57ec-13d47474ad02']
        expect(entityAnnotation2).not.toBe undefined

        # Select another entity in the same text annotation.
        entityAnnotation2.selected = true
        scope.$digest()

        # Check that the number of inputs matches.
        expect(element.find('input').length).toEqual 48
        expect(element.find('textarea').length).toEqual 2

        # Check that there are no input boxes (no entities selected).
        fieldName2 = "wl_entities\\[#{entityAnnotation2.entity.id}\\]"

        expect(element.find("input[name='#{fieldName2}\\[uri\\]']")[0].value).toEqual entityAnnotation2.entity.id
        expect(element.find("input[name='#{fieldName2}\\[label\\]']")[0].value).toEqual entityAnnotation2.entity.label
        expect(element.find("textarea[name='#{fieldName2}\\[description\\]']")[0].innerHTML).toEqual entityAnnotation2.entity.description
        expect(element.find("input[name='#{fieldName2}\\[main_type\\]']")[0].value).toEqual entityAnnotation2.entity.type
#        expect(element.find("input[name='#{fieldName2}\\[image\\]\\[\\]']")[0].value).toEqual entityAnnotation2.entity.thumbnails[0]

    )

    # Test for the entity to empty.
    it 'creates input boxes and textareas with entity data (merged)', inject((AnalysisService, $compile, $httpBackend, $rootScope) ->

      # Compile the directive.
      $compile(element)(scope)
      scope.$digest()

      # Get the mock-up analysis.
      $.ajax('base/app/assets/english.json', async: false).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        $rootScope.$broadcast 'analysisReceived', AnalysisService.parse data, true

        # Check that the analysis is set.
        expect(scope.analysis).not.toBe undefined
        expect(scope.analysis.textAnnotations).not.toBe undefined

        # Check that there are no input boxes (no entities selected).
        expect(element.find('input').length).toEqual 0
        expect(element.find('textarea').length).toEqual 0

        # Select a text annotation.
        textAnnotation1 = scope.analysis.textAnnotations['urn:enhancement-4acc5839-64c3-25e9-aa3a-ee8d039bf2c8']
        expect(textAnnotation1).not.toBe undefined

#        dump textAnnotation1
        # Select an entity annotation in the first text annotation.
        entityAnnotation1 = textAnnotation1.entityAnnotations['urn:enhancement-3b34f9e4-0722-bb99-2d8d-a94eacd2bf7d']
        expect(entityAnnotation1).not.toBe undefined

        # Select one entity.
        entityAnnotation1.selected = true
        scope.$digest()

        # Check that there are no input boxes (no entities selected).
        fieldName1 = "wl_entities\\[#{entityAnnotation1.entity.id}\\]"
        expect(element.find('input').length).toEqual 21
        expect(element.find('textarea').length).toEqual 1

        expect(element.find("input[name='#{fieldName1}\\[uri\\]']")[0].value).toEqual entityAnnotation1.entity.id
        expect(element.find("input[name='#{fieldName1}\\[label\\]']")[0].value).toEqual entityAnnotation1.entity.label
        expect(element.find("input[name='#{fieldName1}\\[main_type\\]']")[0].value).toEqual entityAnnotation1.entity.type

        for i in [0...entityAnnotation1.entity.thumbnails.length]
          expect(element.find("input[name='#{fieldName1}\\[image\\]\\[\\]']")[i].value).toEqual entityAnnotation1.entity.thumbnails[i]

        for i in [0...entityAnnotation1.entity.sameAs.length]
          expect(element.find("input[name='#{fieldName1}\\[sameas\\]\\[\\]']")[i].value).toEqual entityAnnotation1.entity.sameAs[i]

        # Get the decoded description and check it against the entity.
        description = $(element.find("textarea[name='#{fieldName1}\\[description\\]']")[0]).text()
        expect(description).toEqual entityAnnotation1.entity.description

        # Deselect the entity.
        entityAnnotation1.selected = false
        scope.$digest()

        # Check that no inputs are selected.
        expect(element.find('input').length).toEqual 0
        expect(element.find('textarea').length).toEqual 0

        # Reselect the entity.
        entityAnnotation1.selected = true
        scope.$digest()

        # Select a text annotation.
        textAnnotation2 = scope.analysis.textAnnotations['urn:enhancement-5a999e26-6a15-5619-e00e-515dd3b1344b']
        expect(textAnnotation2).not.toBe undefined

#        dump "[ id :: #{id} ][ entity id :: #{entityAnnotation.entity.id} ]" for id, entityAnnotation of textAnnotation2.entityAnnotations

        # Select an entity annotation in the first text annotation.
        entityAnnotation2 = textAnnotation2.entityAnnotations['urn:enhancement-3c8e9479-032b-27ee-2447-aa080c0aa19d']
        expect(entityAnnotation2).not.toBe undefined
        expect(entityAnnotation2.entity).not.toBe undefined

        # Select another entity in the same text annotation.
        entityAnnotation2.selected = true
        scope.$digest()

        # Check that the number of inputs matches.
        expect(element.find('input').length).toEqual 33
        expect(element.find('textarea').length).toEqual 2

        # Check that there are no input boxes (no entities selected).
        fieldName2 = "wl_entities\\[#{entityAnnotation2.entity.id}\\]"

        expect(element.find("input[name='#{fieldName2}\\[uri\\]']")[0].value).toEqual entityAnnotation2.entity.id
        expect(element.find("input[name='#{fieldName2}\\[label\\]']")[0].value).toEqual entityAnnotation2.entity.label
        expect(element.find("textarea[name='#{fieldName2}\\[description\\]']")[0].innerHTML).toEqual entityAnnotation2.entity.description
        expect(element.find("input[name='#{fieldName2}\\[main_type\\]']")[0].value).toEqual entityAnnotation2.entity.type
#        expect(element.find("input[name='#{fieldName2}\\[image\\]\\[\\]']")[0].value).toEqual entityAnnotation2.entity.thumbnails[0]

    )

    # Test for the entity to empty.
    it 'creates input boxes and textareas with entity data (non-merged)', inject((AnalysisService, $compile, $httpBackend, $rootScope) ->

      # Compile the directive.
      $compile(element)(scope)
      scope.$digest()

      # Get the mock-up analysis.
      $.ajax('base/app/assets/english.json', async: false ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        $rootScope.$broadcast 'analysisReceived', AnalysisService.parse data

        # Check that the analysis is set.
        expect(scope.analysis).not.toBe undefined
        expect(scope.analysis.textAnnotations).not.toBe undefined

        # Check that there are no input boxes (no entities selected).
        expect(element.find('input').length).toEqual 0
        expect(element.find('textarea').length).toEqual 0

        # Select a text annotation.
        textAnnotation1 = scope.analysis.textAnnotations['urn:enhancement-5a999e26-6a15-5619-e00e-515dd3b1344b']
        expect(textAnnotation1).not.toBe undefined

#        dump textAnnotation1

        # Select an entity annotation in the first text annotation.
        entityAnnotation1 = textAnnotation1.entityAnnotations['urn:enhancement-3c8e9479-032b-27ee-2447-aa080c0aa19d']
        expect(entityAnnotation1).not.toBe undefined

        # Select one entity.
        entityAnnotation1.selected = true
        scope.$digest()

        # Check that there are no input boxes (no entities selected).
        fieldName1 = "wl_entities\\[#{entityAnnotation1.entity.id}\\]"
        expect(element.find('input').length).toEqual 12
        expect(element.find('textarea').length).toEqual 1

        expect(element.find("input[name='#{fieldName1}\\[uri\\]']")[0].value).toEqual entityAnnotation1.entity.id
        expect(element.find("input[name='#{fieldName1}\\[label\\]']")[0].value).toEqual entityAnnotation1.entity.label
        expect(element.find("input[name='#{fieldName1}\\[main_type\\]']")[0].value).toEqual entityAnnotation1.entity.type
#        expect(element.find("input[name='#{fieldName1}\\[image\\]\\[\\]']")[0].value).toEqual entityAnnotation1.entity.thumbnails[0]
#        expect(element.find("input[name='#{fieldName1}\\[image\\]\\[\\]']")[1].value).toEqual entityAnnotation1.entity.thumbnails[1]
#        expect(element.find("input[name='#{fieldName1}\\[image\\]\\[\\]']")[2].value).toEqual entityAnnotation1.entity.thumbnails[2]
#        expect(element.find("input[name='#{fieldName1}\\[image\\]\\[\\]']")[3].value).toEqual entityAnnotation1.entity.thumbnails[3]

        # Get the decoded description and check it against the entity.
        description = $(element.find("textarea[name='#{fieldName1}\\[description\\]']")[0]).text()
        expect(description).toEqual entityAnnotation1.entity.description

        # Deselect the entity.
        entityAnnotation1.selected = false
        scope.$digest()

        # Check that no inputs are selected.
        expect(element.find('input').length).toEqual 0
        expect(element.find('textarea').length).toEqual 0

        # Reselect the entity.
        entityAnnotation1.selected = true
        scope.$digest()

#        dump scope.analysis.textAnnotations
        # Select a text annotation.
        textAnnotation2 = scope.analysis.textAnnotations['urn:enhancement-b083b4d7-00e4-e076-86c3-e93d20113622']
        expect(textAnnotation2).not.toBe undefined

#        dump textAnnotation2

        # Select an entity annotation in the first text annotation.
        entityAnnotation2 = textAnnotation2.entityAnnotations['urn:enhancement-e7a7c0a2-226f-0a5b-ce9c-95a2e5e3e469']
        expect(entityAnnotation2).not.toBe undefined

        # Select another entity in the same text annotation.
        entityAnnotation2.selected = true
        scope.$digest()

        # Check that the number of inputs matches.
        expect(element.find('input').length).toEqual 18
        expect(element.find('textarea').length).toEqual 2

        # Check that there are no input boxes (no entities selected).
        fieldName2 = "wl_entities\\[#{entityAnnotation2.entity.id}\\]"

        expect(element.find("input[name='#{fieldName2}\\[uri\\]']")[0].value).toEqual entityAnnotation2.entity.id
        expect(element.find("input[name='#{fieldName2}\\[label\\]']")[0].value).toEqual entityAnnotation2.entity.label
        expect(element.find("textarea[name='#{fieldName2}\\[description\\]']")[0].innerHTML).toEqual entityAnnotation2.entity.description
        expect(element.find("input[name='#{fieldName2}\\[main_type\\]']")[0].value).toEqual entityAnnotation2.entity.type
#        expect(element.find("input[name='#{fieldName2}\\[image\\]\\[\\]']")[0].value).toEqual entityAnnotation2.entity.thumbnails[0]

    )

    # Test for the entity to empty.
    it 'creates input boxes and textareas with entity data (merged) with many sameAs', inject((AnalysisService, $compile, $httpBackend, $rootScope) ->

      # Compile the directive.
      $compile(element)(scope)
      scope.$digest()

      # Get the mock-up analysis.
      $.ajax('base/app/assets/english.002.json',
        async: false
      ).done (data) ->

        # Catch all the requests to Freebase.
        $httpBackend.when('HEAD', /.*/).respond(200, '')

        # Simulate event broadcasted by AnalysisService
        $rootScope.$broadcast 'analysisReceived', AnalysisService.parse data, true

        # Check that the analysis is set.
        expect(scope.analysis).not.toBe undefined
        expect(scope.analysis.textAnnotations).not.toBe undefined

        # Check that there are no input boxes (no entities selected).
        expect(element.find('input').length).toEqual 0
        expect(element.find('textarea').length).toEqual 0

        # Get a Text Annotation and three entities that related to that Text Annotation.
        textAnnotationId = 'urn:enhancement-a6bb446e-6e95-d6be-e91c-32833aa58b32'
        entityAnnotationId = 'urn:enhancement-663e0cfd-c482-f695-674e-cae98e42dd18'

        # Select a text annotation.
        textAnnotation = scope.analysis.textAnnotations[textAnnotationId]
        expect(textAnnotation).not.toBe undefined

#        dump "[ id :: #{id} ][ entity id :: #{entityAnnotation.entity.id} ]" for id, entityAnnotation of textAnnotation.entityAnnotations

        # Select an entity annotation in the first text annotation.
        entityAnnotation = textAnnotation.entityAnnotations[entityAnnotationId]
        expect(entityAnnotation).not.toBe undefined

        # Select one entity.
        entityAnnotation.selected = true
        scope.$digest()

        # Check that there are no input boxes (no entities selected).
        fieldName = "wl_entities\\[#{entityAnnotation.entity.id}\\]"

#        inputs = element.find('input')
#        for i in [0...inputs.length]
#          dump "#{i} #{inputs[i].name} :: #{inputs[i].value}"

        expect(element.find('input').length).toEqual 93
        expect(element.find('textarea').length).toEqual 1

        expect(element.find("input[name='#{fieldName}\\[uri\\]']")[0].value).toEqual entityAnnotation.entity.id
        expect(element.find("input[name='#{fieldName}\\[label\\]']")[0].value).toEqual entityAnnotation.entity.label
        for i in [0...entityAnnotation.entity.types.length]
          expect(element.find("input[name='#{fieldName}\\[type\\]\\[\\]']")[i].value).toEqual entityAnnotation.entity.types[i]

        for i in [0...entityAnnotation.entity.thumbnails.length]
          expect(element.find("input[name='#{fieldName}\\[image\\]\\[\\]']")[i].value).toEqual entityAnnotation.entity.thumbnails[i]

        for i in [0...entityAnnotation.entity.sameAs.length]
          expect(element.find("input[name='#{fieldName}\\[sameas\\]\\[\\]']")[i].value).toEqual entityAnnotation.entity.sameAs[i]

        # Get the decoded description and check it against the entity.
        description = $(element.find("textarea[name='#{fieldName}\\[description\\]']")[0]).text()
        expect(description).toEqual entityAnnotation.entity.description

    )
