describe 'TinyMCE: more tests', ->
  beforeEach module('wordlift.tinymce.plugin.services')

  # A reference to the TinyMCE editor.
  ed = undefined

  beforeEach inject((AnalysisService) ->
    ed = tinyMCE.get('content')
    AnalysisService.setKnownTypes window.wordlift.types
    AnalysisService.setEntities window.wordlift.entities
  )

  afterEach inject(($httpBackend) ->
    $httpBackend.verifyNoOutstandingExpectation()
    $httpBackend.verifyNoOutstandingRequest()
    # Clean up editor after each test
    ed.setContent ''
  )

  it 'doesn\'t mess up with the text', inject((AnalysisService, EditorService, $httpBackend, $rootScope) ->

    text_url = 'base/app/assets/questa_volta_parliamo_di_seo.txt'
    analysis_1_url = 'base/app/assets/questa_volta_parliamo_di_seo_1.json'
    analysis_2_url = 'base/app/assets/questa_volta_parliamo_di_seo_2.json'
    andrea_volpini_text_annotation_urn = 'urn:enhancement-439132af-8fe7-044d-101b-79b06885da05'
    andrea_volpini_entity_annotation_urn = 'urn:enhancement-b0ec16f1-9505-80e8-01d0-618080b6e1cd'

    # Spy on the analyze method of the AnalysisService
    spyOn(AnalysisService, 'analyze').and.callThrough()

    # Spy on the editor embedAnalysis method of the EditorService
    spyOn(EditorService, 'embedAnalysis').and.callThrough()

    # Spy on the root scope.
    spyOn($rootScope, '$broadcast').and.callThrough()

    # Set the editor contents.
    $.ajax( text_url , async: false).done (data) -> ed.setContent data, format: 'raw'

    # Load the analysis results.
    json_1 = ''
    $.ajax( analysis_1_url, async: false).done (data) -> json_1 = data

    json_2 = ''
    $.ajax( analysis_1_url, async: false).done (data) -> json_2 = data

    # Get the html content of the editor.
    html = ed.getContent format: 'raw'

    # Get the text content from the Html.
    text = Traslator.create(html).getText()

    expect(text).not.toBe ''

    # Catch all the requests to Freebase.
    $httpBackend.expectPOST(/.*wordlift_analyze$/).respond 200, json_1

    # Send the text content for analysis.
    EditorService.analyze text

    $httpBackend.flush()

    expect($rootScope.$broadcast).toHaveBeenCalledWith 'analysisReceived', jasmine.any(Object)

    # Create a reference to the analysis.
    analysis = $rootScope.$broadcast.calls.mostRecent().args[1]

    # Create a refernce to the text-annotation and entity-annotation.
    textAnnotation = analysis.textAnnotations[andrea_volpini_text_annotation_urn]
    entityAnnotation = analysis.entityAnnotations[andrea_volpini_entity_annotation_urn]

    expect(textAnnotation).not.toBe undefined
    expect(entityAnnotation).not.toBe undefined

    $rootScope.$broadcast 'selectEntity', ta: textAnnotation, ea: entityAnnotation

    # Catch all the requests to Freebase.
    $httpBackend.expectPOST(/.*wordlift_analyze$/).respond 200, json_2

    # Send the text content for analysis.
    EditorService.analyze text

    $httpBackend.flush()

    # TODO: what we expect here?

  )