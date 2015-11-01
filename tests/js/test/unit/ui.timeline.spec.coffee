describe "Timeline Ui Component Unit Test", ->
  domElement = undefined
  
  # Tests set-up.
  beforeEach inject(->
    jasmine.getJSONFixtures().fixturesPath = "base/app/assets/"
    domElement = $('<div id="timeline"></div>')
    $(document.body).append $(domElement)
  )

  afterEach inject(->
    $( "#timeline" ).remove()
  )
  
  it "does not create timeline if receives empty data", ->

    fakeResponse = getJSONFixture("timeline_0.json")
    # Set a mock object to replace jquery Ajax POST with fake / mock results
    spyOn(jQuery, "ajax").and.callFake((request)->
      request.success fakeResponse
    )
    # Initialize the plugin
    domElement.timeline()
 
    # Jquery post() has been called during the initialization
    expect(jQuery.ajax).toHaveBeenCalled()
    # HTML container is hidden
    expect(domElement.is(":visible")).not.toBeTruthy()
    
  it "create a timeline with three events", ->

    fakeResponse = getJSONFixture("timeline_1.json")
    # Set a mock object to replace jquery Ajax POST with fake / mock results
    spyOn(jQuery, "ajax").and.callFake((request)->
      request.success fakeResponse
    )
    # Initialize the plugin
    domElement.timeline()
 
    # Jquery post() has been called during the initialization
    expect(jQuery.ajax).toHaveBeenCalled()
    # HTML container is hidden
    expect(domElement.is(":visible")).toBeTruthy()
    
