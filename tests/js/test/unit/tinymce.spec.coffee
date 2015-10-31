describe "TinyMCE tests", ->
  beforeEach module('wordlift.tinymce.plugin.services')
#  beforeEach module('AnalysisService')

  # Global references
  ed = undefined

  # Tests set-up.
  beforeEach inject((AnalysisService) ->
    ed = tinyMCE.get('content')
    AnalysisService.setKnownTypes window.wordlift.types
  )

  afterEach inject ($httpBackend) ->
    $httpBackend.verifyNoOutstandingExpectation()
    $httpBackend.verifyNoOutstandingRequest()
    # Clean up editor after each test
    ed.setContent ''

  it "loads the editor and the WordLift plugin", ->

    # Check that the editor is loaded and that the WordLift plugin is loaded too.
    expect(ed).not.toBe undefined
    expect(ed.plugins.wordlift).not.toBe undefined


  it "loads the content", ->

    # Check that the editor content is empty.
    expect(ed.getContent().length).toEqual 0

    # Load the sample text in the editor.
    $.ajax('base/app/assets/english.txt',
      async: false

    ).done (data) ->
      # Set the editor content
      ed.setContent data
      # Check for the editor content not to be empty.
      expect(ed.getContent().length).toBeGreaterThan 0


  it "doesn't run an analysis when an analysis is already running", inject (AnalysisService, EditorService) ->

    # Spy on the analyze method of the AnalysisService
    spyOn AnalysisService, 'analyze'

    # By default the analysis is running is false
    expect(AnalysisService.isRunning).toEqual false

    # Set the analysis as running
    AnalysisService.isRunning = true

    # Check that the flag is true
    expect(AnalysisService.isRunning).toEqual true

    # Call the analyze method of the editor.
    EditorService.analyze ed.getContent format: 'text'

    # The analysis service shouldn't have been called
    expect(AnalysisService.analyze).not.toHaveBeenCalled()


  it "runs an analysis when an analysis is not running", inject (AnalysisService, EditorService, $httpBackend, $rootScope) ->

    # Spy on the analyze method of the AnalysisService
    spyOn(AnalysisService, 'analyze').and.callThrough()

    # Spy on the editor embedAnalysis method of the EditorService
    spyOn(EditorService, 'embedAnalysis').and.callThrough()

    # Spy on the root scope.
    spyOn($rootScope, '$broadcast').and.callThrough()

    # By default the analysis is running is false
    expect(AnalysisService.isRunning).toEqual false

    # Load the sample response.
    $.ajax('base/app/assets/english.json', async: false).done (data) ->
      $httpBackend.expectPOST('/base/app/assets/english.json?action=wordlift_analyze').respond 200, data
      #
      # Call the analyze method of the editor.
      EditorService.analyze ed.getContent(format: 'text')

      # The analysis service shouldn't have been called with the merge parameter set to true.
      expect(AnalysisService.analyze).toHaveBeenCalledWith(jasmine.any(String), true)

      $httpBackend.flush()

      expect($rootScope.$broadcast).toHaveBeenCalledWith 'analysisReceived', jasmine.any(Object)

      # The analysis service shouldn't have been called
      expect(EditorService.embedAnalysis).toHaveBeenCalledWith jasmine.any(Object)


    it 'sends the analysis results', inject( (AnalysisService, EditorService, $httpBackend, $rootScope) ->

      # Get a reference to the argument passed with the event.
      args = $rootScope.$broadcast.calls.argsFor 0

      # Get a reference to the analysis structure.
      analysis = args[1]

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
    )

describe "TinceMCE editor : analysis abort", ->
  beforeEach module('wordlift.tinymce.plugin.services')
#  beforeEach module('AnalysisService')

  # Global references
  ed = undefined

  # Tests set-up.
  beforeEach ->
    ed = tinyMCE.get('content')

  it 'aborts an analysis when requested', inject((AnalysisService, EditorService, $httpBackend, $rootScope) ->

    # Spy on the analyze method of the AnalysisService
    spyOn(AnalysisService, 'analyze').and.callThrough()
    spyOn($rootScope, '$broadcast').and.callThrough()

    # Check that the editor content is empty.
    expect(ed.getContent().length).toEqual 0

    # Get sample analysis data.
    analysisResults = undefined
    # Load the sample text in the editor.
    $.ajax('base/app/assets/english.json', { async: false }).done (data) ->
      analysisResults = data

    # Load the sample text in the editor.
    $.ajax('base/app/assets/english_disambiguated.txt', { async: false }).done (source) ->

      # Set the editor content
      ed.setContent source

      # Check editor content is set properly
      expect(ed.getContent({format: 'raw'})).toEqual(source)

      $httpBackend.expectPOST(/wordlift_analyze$/).respond (method, url, data, headers) ->
        expect(AnalysisService.isRunning).toBe true
        AnalysisService.abort()
        [ 0, analysisResults, {} ]

      # Call the analyze method of the editor.
      EditorService.analyze ed.getContent { format: 'text' }

      # The analysis service shouldn't have been called with the merge parameter set to true.
      expect(AnalysisService.analyze).toHaveBeenCalledWith(jasmine.any(String), true)

      # Flush the backend requests.
      $httpBackend.flush()

      #      expect($rootScope.$broadcast).not.toHaveBeenCalledWith('analysisReceived', jasmine.any(Object))
      expect($rootScope.$broadcast).not.toHaveBeenCalledWith('error', jasmine.any(Function))
      expect(AnalysisService.isRunning).toBe false

#
#      # The analysis service shouldn't have been called
#      expect(EditorService.embedAnalysis).toHaveBeenCalledWith(jasmine.any(Object))

  )


describe "TinceMCE editor : running an analysis on an already analyzed content", ->
  beforeEach module('wordlift.tinymce.plugin.services')
#  beforeEach module('AnalysisService')

  # Global references
  ed = undefined

  # Tests set-up.
  beforeEach ->
    ed = tinyMCE.get('content')

  it 'analyses a content which has already been analysed', inject((AnalysisService, EditorService, $httpBackend, $rootScope) ->
    html1 = "<span>this is html 1</span>"
    html2 = "<div>this is html 2</div>"

    t1 = Traslator.create(html1)
    t2 = Traslator.create(html2)

    expect(t1.getHtml()).not.toEqual t2.getHtml()
    #    dump "[ t1 html :: #{t1.getHtml()} ][ t2 html :: #{t2.getHtml()} ]"


    html = undefined

    # Load the sample text in the editor.
    $.ajax('base/app/assets/meet_redlink_at_enterprise_search_europe_2014.txt', { async: false }).done (data) ->
      html = data
    #
    #    # Create a Traslator object and check that total lengths match.
    ##    dump "create class"
    traslator = Traslator.create(html)
    ##    dump "get text"
    text = traslator.getText()
    #
    #    dump "[ html length :: #{html.length} ][ text length :: #{text.length} ]"
    #
    #    dump text
    #
    expect(traslator.text2html(text.length)).toEqual html.length
    expect(traslator.html2text(html.length)).toEqual text.length

#    items = ed.dom.select('*[itemid]')
#    dump item.getAttribute('itemid') for item in items

    # dump $('body#tinymce').length

#    pattern = /<(\w+)[^>]*\sitemid="([^"]+)"[^>]*>([^<]+)<\/\1>/gim
#    pattern = /<(\w+).*\sitemid="([^"]+)"[^>]*>([^<]+)(?:<\/\1>)?/gim
#    while match = pattern.exec html
#      textPos = t.html2text match.index
#      dump "[ match index :: #{match.index} ][ text pos :: #{textPos} ]"

#      dump '[ sel-prefix:: ' + match[1] + ' ]'
#      dump '[ element :: ' + match[2] + ' ]'
#      dump '[ item id :: ' + match[3] + ' ]'
#      dump '[ sel-text :: ' + match[4] + ' ]'
#      dump '[ sel-suffix:: ' + match[5] + ' ]'
#      # + match[3] + ' ' + match[4]


#    pattern = /([^<]*)(<[^>]*>)([^<]*)/gim
#    textLength = 0
#    htmlLength = 0
#
#    htmlPositions = [0]
#    textPositions = [0]
#
#    while match = pattern.exec html
#
#      htmlPre  = match[1]
#      htmlElem = match[2]
#      htmlPost = match[3]
#      textPre  = htmlPre.replace('\n', '')
#      textPost = htmlPost.replace('\n', '')
#
#      textLength += textPre.length
#      htmlLength += htmlPre.length + htmlElem.length
#
#      if 0 < htmlPost.length
#        htmlPositions.push htmlLength
#        textPositions.push textLength
#
#      textLength += textPost.length
#      htmlLength += htmlPost.length
#
#
#    dump "[ text length :: #{text.length} ][ html length :: #{html.length} ][ text length (calc) :: #{textLength} ][ html length  (calc) :: #{htmlLength} ]"
#
#    dump htmlPositions
#    dump textPositions
#
#    for i in [0..htmlPositions.length]
#      htmlPos = htmlPositions[i]
#      textPos = textPositions[i]
#      dump '[ text char :: ' + text.charAt(textPos) + ' ][ html char :: ' + html.charAt(htmlPos) + ' ]'


  )

describe 'TinyMCE', ->
  beforeEach module('wordlift.tinymce.plugin.services')
#  beforeEach module('AnalysisService')

  # A reference to the TinyMCE editor.
  ed = undefined

  beforeEach inject((AnalysisService) ->
    ed = tinyMCE.get('content')
    AnalysisService.setKnownTypes window.wordlift.types
    AnalysisService.setEntities window.wordlift.entities
  )

  it 'features embedded annotations', inject((AnalysisService, EditorService) ->

    # The textual content.
    text = ''

    # The analysis results.
    json = ''

    # Load the sample text in the editor.
    $.ajax('base/app/assets/english.txt', async: false).done (data) ->
      text = data

    # Load the sample analysis results.
    $.ajax('base/app/assets/english.json', async: false).done (data) ->
      json = data

    # Set the textual content in the editor.
    ed.setContent text, format: 'raw'

    # Get the analysis instance, by parsing the json and merging the results.
    analysis = AnalysisService.parse json, true

    # Embed the analysis results.
    EditorService.embedAnalysis analysis

    # Expect to find 9 text annotations.
    elements = ed.dom.select('span[class="textannotation"]')
    expect(elements.length).toEqual 9

    # Expected annotations.
    annotations = [
      label: 'Andrea Volpini'
      uri: 'urn:enhancement-6bb3f451-8332-65de-8fe0-6367553bcf8c'
    ,
      label: 'David Riccitelli'
      uri: 'urn:enhancement-fcf5c18e-dfed-104a-2ad2-17e5a031c246'
    ,
      label: 'Insideout10'
      uri: 'urn:enhancement-b083b4d7-00e4-e076-86c3-e93d20113622'
    ,
      label: 'Central Archives of the State'
      uri: 'urn:enhancement-4acc5839-64c3-25e9-aa3a-ee8d039bf2c8'
    ,
      label: 'Rome'
      uri: 'urn:enhancement-5adbf3ee-54d0-2445-6fdc-c09fec19c76f'
    ,
      label: 'Linked Open Data'
      uri: 'urn:enhancement-5a999e26-6a15-5619-e00e-515dd3b1344b'
    ,
      label: 'WordPress'
      uri: 'urn:enhancement-e90c2d93-945d-1d15-104e-eeb675064238'
    ,
      label: 'Semantic Web'
      uri: 'urn:enhancement-5ac3cd31-85fa-eaa8-bb5c-886190c0f683'
    ,
      label: 'Linked Open Data'
      uri: 'urn:enhancement-fbcfb67c-dc95-6c35-91ee-a2e49be98681'
    ]

    # Validate eaach annotation.
    for i in [0...elements.length]
      expect(elements[i].textContent).toEqual annotations[i].label
      expect(elements[i].id).toEqual annotations[i].uri

    # Dump the html output
#    dump ed.getContent(format: 'raw')
  )

  it 'features entities preselections in the analysis results', inject((AnalysisService, EditorService) ->

    # The html content.
    html = ''

    # The analysis results.
    json = ''

    # Load the sample text in the editor.
    $.ajax('base/app/assets/insideout10_with_3_selections_pre.html', async: false).done (data) ->
      html = data

    # Load the sample analysis results.
    $.ajax('base/app/assets/insideout10_with_3_selections.json', async: false).done (data) ->
      json = data

    # Set the textual content in the editor.
    ed.setContent html, format: 'raw'

    # Get the analysis instance, by parsing the json and merging the results.
    analysis = AnalysisService.parse json, true

    # Embed the analysis results.
    EditorService.embedAnalysis analysis

    # We expect these entity annotations to be already selected.
    selected = [
      'urn:enhancement-49b1cf1e-b260-4033-403e-4e494039d241'
      'urn:enhancement-33ff847c-da6a-d889-9f1e-b347f17d6d7a'
      'urn:enhancement-15a22024-9c09-771d-535a-3b00c929717b'
    ]

    # Check for selections.
    expect(analysis.entityAnnotations).not.toBe undefined
    expect(Object.keys(analysis.entityAnnotations).length).toEqual 22
    for entityAnnotationId, entityAnnotation of analysis.entityAnnotations
      expect(entityAnnotation.selected).toBe (entityAnnotation.id in selected)

#    for textAnnotationId, textAnnotation of analysis.textAnnotations
#      for entityAnnotationId, entityAnnotation of textAnnotation.entityAnnotations
#        entity = entityAnnotation.entity
#        dump "[ entityAnnotationId :: #{entityAnnotationId} ][ selected :: #{entityAnnotation.selected} ][ entity id :: #{entity.id} ]"


  )

  it 'features entities preselections in the analysis results (2)', inject((AnalysisService, EditorService) ->

    # The html content.
    html = ''

    # The analysis results.
    json = ''

    # Load the sample text in the editor.
    $.ajax('base/app/assets/insideout10_1.html', async: false).done (data) ->
      html = data

    # Load the sample analysis results.
    $.ajax('base/app/assets/insideout10_1.json', async: false).done (data) ->
      json = data

    # Set the textual content in the editor.
    ed.setContent html, format: 'raw'

    # Get the analysis instance, by parsing the json and merging the results.
    analysis = AnalysisService.parse json, true

    # Embed the analysis results.
#    expect(->
#      EditorService.embedAnalysis analysis).toThrow 'Missing entity in window.wordlift.entities collection!'

    # We expect these entity annotations to be already selected.
    selected = [
      'urn:enhancement-49b1cf1e-b260-4033-403e-4e494039d241'
      'urn:enhancement-33ff847c-da6a-d889-9f1e-b347f17d6d7a'
      'urn:enhancement-15a22024-9c09-771d-535a-3b00c929717b'
    ]

    # Check for selections.
    expect(analysis.entityAnnotations).not.toBe undefined
    expect(Object.keys(analysis.entityAnnotations).length).toEqual 24
    for entityAnnotationId, entityAnnotation of analysis.entityAnnotations
      expect(entityAnnotation.entity).not.toBe undefined

    # selectedEntities = (ea.entity for id, ea of analysis.entityAnnotations when ea.selected and ea.entity.id in selectedEntityIds)
    # expect(selectedEntities.length).toEqual(selected.length)
    # for entity in selectedEntities
    #  expect(entity.id in selected).toBe true  
  )


  it 'features entities preselections in the analysis results on missing entities', inject((AnalysisService, EditorService) ->

    # The html content.
    html = ''
    # The analysis results.
    json = ''

    # Load the sample text in the editor.
    $.ajax('base/app/assets/insideout10_1.html', async: false).done (data) ->
      html = data
    # Load the sample analysis results.
    $.ajax('base/app/assets/insideout10_1.json', async: false).done (data) ->
      json = data

    # Set the textual content in the editor.
    ed.setContent html, format: 'raw'

    # Get the analysis instance, by parsing the json and merging the results.
    analysis = AnalysisService.parse json, true

    #
    # Loads fake entities and populate window.wordlift.entities
    $.ajax('base/app/assets/wordlift_entities_0.json', async: false).done (data) ->
      AnalysisService.setEntities data

    analysis = AnalysisService.parse json, true
    EditorService.embedAnalysis analysis

    # Check for consistency
    expect(analysis.entityAnnotations).not.toBe undefined
    expect(Object.keys(analysis.entityAnnotations).length).toEqual 24
    for entityAnnotationId, entityAnnotation of analysis.entityAnnotations
      expect(entityAnnotation.entity).not.toBe undefined
      expect(entityAnnotation.relation).not.toBe undefined

    # Check for preselected
    selectedEntityIds = [
      'http://data.redlink.io/353/wordlift/entity/David_Riccitelli'
#      'http://data.redlink.io/353/wordlift/entity/Central_Archives_of_the_State_(Italy)'
      'http://data.redlink.io/353/wordlift/entity/WordPress'
    ]
    selectedEntities = (ea.entity for id, ea of analysis.entityAnnotations when ea.selected and ea.entity.id in selectedEntityIds)
    expect(selectedEntities.length).toEqual selectedEntityIds.length
    for entity in selectedEntities
      expect(entity.id in selectedEntityIds).toBe true
  )
