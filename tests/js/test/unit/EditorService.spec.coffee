describe "EditorService tests", ->
  beforeEach module('wordlift.tinymce.plugin.services')
  beforeEach module('AnalysisService')

  # Global references
  ed = undefined

  # Tests set-up.
  beforeEach inject(['$rootScope', ($rootScope) ->
    ed = tinyMCE.get('content')
    ed.setContent('')
    # Spy on the root scope.
    spyOn($rootScope, '$broadcast').and.callThrough()
  ])
  
  it "Create a TextAnnotation from the current editor selection", inject((EditorService, $httpBackend, $rootScope) ->
   # Check if editor content is blank
   expect(ed.getContent()).toBe ''
   # Set a fake content
   content = "Just a simple text about <em>New York</em> and <em>Los Angeles</em>"
   
   ed.setContent(content)
   # Set a fake selection (first <em> tag)
   expect(ed.selection.getContent()).toBe '' 
   ed.selection.select(ed.dom.select('em')[0]) 
   expect(ed.selection.getContent()).toBe '<em>New York</em>'  

   # Create a TextAnnotation from the current editor selection 
   EditorService.createTextAnnotationFromCurrentSelection()
   # Check if textAnnotationAdded event is properly fired
   expect($rootScope.$broadcast).toHaveBeenCalledWith 'textAnnotationAdded', jasmine.any(Object)
   # Retrieve the textAnnotation
   textAnnotation = $rootScope.$broadcast.calls.mostRecent().args[1]
   expect(textAnnotation.start).toBe 25
   expect(textAnnotation.end).toBe 33
   expect(textAnnotation.text).toBe 'New York'
   # Check if the editor content is properly updated
   expect(ed.getContent()).toBe "<p>Just a simple text about <span id=\"#{textAnnotation.id}\" class=\"textannotation\"><em>#{textAnnotation.text}</em></span> and <em>Los Angeles</em></p>"
          
  )

  it "Create a TextAnnotation from the current editor selection when it's blank", inject((EditorService, $httpBackend, $rootScope) ->
   # Check if editor content is blank
   expect(ed.getContent()).toBe ''
   # Set a fake selection (first <em> tag)
   expect(ed.selection.getContent()).toBe '' 
  
   # Create a TextAnnotation from the current editor selection 
   EditorService.createTextAnnotationFromCurrentSelection()
   # Verify that textAnnotationAdded event is NOT fired for a blank selection
   expect($rootScope.$broadcast).not.toHaveBeenCalledWith 'textAnnotationAdded', jasmine.any(Object)
          
  )

  it "Simulate an analysis object from a blank content", inject((AnalysisService, EditorService, $httpBackend, $rootScope) ->
   # Check if editor content is blank
   expect(ed.getContent()).toBe ''
   # Spy on EditorService.
   spyOn(EditorService, 'embedAnalysis').and.callThrough()
   
   # Create a TextAnnotation from the current editor selection 
   EditorService.createDefaultAnalysis()
   # Verify an analysisReceived event is simulated by the EditorService
   expect($rootScope.$broadcast).toHaveBeenCalledWith 'analysisReceived', jasmine.any(Object)
   # Retrieve analysis object
   analysis = $rootScope.$broadcast.calls.mostRecent().args[1]
   # Check that the broadcasted object is an empty analysis
   expect(analysis).toEqual(AnalysisService.createAnEmptyAnalysis())      
   # Verify embed analysis is called too
   expect(EditorService.embedAnalysis).toHaveBeenCalledWith analysis
   
  )
  it "Simulate an analysis object from a content with a disambiguated missing within the local storage", inject((AnalysisService, EditorService, $httpBackend, $rootScope) ->
   # Check if editor content is blank
   expect(window.wordlift.entities).toEqual {}
   # Set a fake content with a broken disambiguated entity
   content = '''
     Just a simple text about 
     <span id="urn:local-text-annotation-7824y9j6afez5minjhpvy286esqyqfr1" class="textannotation highlight wl-place" itemid="http://data.redlink.io/685/dataset-for-fun/entity/New_York">
     New York</span> and <em>Los Angeles</em>
   '''
   ed.setContent(content)

   # Check there is 1 annotation within the text
   expect(ed.dom.select('span').length).toBe 1
   # Create a TextAnnotation from the current editor selection 
   EditorService.createDefaultAnalysis()
   # Verify an analysisReceived event is simulated by the EditorService
   expect($rootScope.$broadcast).toHaveBeenCalledWith 'analysisReceived', jasmine.any(Object)
   # Retrieve analysis object
   analysis = $rootScope.$broadcast.calls.mostRecent().args[1]
   # Check there are no more annotation within the text: broken text annotation are removed by embed analysis process
   expect(ed.dom.select('span').length).toBe 0
   
  )