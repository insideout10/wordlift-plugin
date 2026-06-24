describe 'EditorService canonical analysis HTML', ->
  beforeEach ->
    window.wlSettings ?= {}
    window.wlSettings.default_editor_id = 'content'
    window.wp ?= {}
    window.wp.wordlift ?= {}
    window.wp.wordlift.trigger ?= jasmine.createSpy('trigger')

  beforeEach ->
    angular.module('wordlift.editpost.widget.test.configuration', [])
    .value 'configuration',
      classificationBoxes: []
      entities: {}
      types: []
      ajax_url: 'admin-ajax.php'

  beforeEach module('wordlift.editpost.widget.test.configuration')
  beforeEach module('wordlift.editpost.widget.services.EditorService')

  ed = undefined

  beforeEach inject((EditorService) ->
    ed = tinyMCE.get('content')
    ed.setContent('')
  )

  it 'embeds returned HTML offsets into the exact content sent to analysis', inject((EditorService) ->
    analysisHtml = '<p>However, when you implement this, you should ensure you watch for key.</p>'
    staleEditorHtml = '<p>However, when you implement this, <span class="mce_SELRES_start"></span>you should ensure you watch for key.</p>'
    start = analysisHtml.indexOf('you should')
    end = start + 'you should'.length

    ed.setContent(staleEditorHtml)

    analysis =
      _analysisHtml: analysisHtml
      entities:
        'https://data.example/entity/you-should':
          id: 'https://data.example/entity/you-should'
          mainType: 'thing'
          occurrences: ['annotation-you-should']
      annotations:
        'annotation-you-should':
          id: 'annotation-you-should'
          text: 'you should'
          start: start
          end: end
          entityMatches: [
            entityId: 'https://data.example/entity/you-should'
            confidence: 1
          ]

    EditorService.embedAnalysis analysis

    html = ed.getContent format: 'raw'
    expect(html).toContain 'when you implement this, <span id="annotation-you-should" class="textannotation disambiguated wl-thing" itemid="https://data.example/entity/you-should">you should</span>'
    expect(html).not.toContain ', you shou</span>ld'
  )

  it 'broadcasts the bookmark-stripped HTML used for analysis', inject((AnalysisService, $q, $rootScope) ->
    sentHtml = undefined
    broadcastAnalysis = undefined
    sourceHtml = '<p>Alpha <span class="mce_SELRES_start"></span>Beta<span class="mce_SELRES_end"></span>\uFEFF Gamma</p>'
    expectedHtml = '<p>Alpha Beta Gamma</p>'

    spyOn(AnalysisService, '_innerPerform').and.callFake((content) ->
      sentHtml = content
      $q.when
        entities: {}
        annotations: {}
    )
    spyOn($rootScope, '$broadcast').and.callFake((event, analysis) ->
      broadcastAnalysis = analysis if event is 'analysisPerformed'
    )

    AnalysisService.perform sourceHtml
    $rootScope.$digest()

    expect(sentHtml).toBe expectedHtml
    expect(broadcastAnalysis._analysisHtml).toBe expectedHtml
  )
