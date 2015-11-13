describe "Chord Ui Component Unit Test", ->
  domElement = undefined
  
  # Tests set-up.
  beforeEach inject(->
    jasmine.getJSONFixtures().fixturesPath = "base/app/assets/"
    domElement = $('<div class="wl_chord" id="wl_chord_global" style="width:500px;height:500px"></div>')
    $(document.body).append $(domElement)
  )

  afterEach inject(->
    $( ".wl-chord" ).remove()
  )

  it "does not create chord if receives empty data", ->

    fakeResponse = getJSONFixture("chord_0.json")
    # Set a mock object to replace jquery Ajax POST with fake / mock results
    spyOn(jQuery, "ajax").and.callFake((request)->
      request.success fakeResponse
    )
    # Initialize the plugin
    domElement.chord
      widget_id: "wl_chord_global"

    # Jquery post() has been called during the initialization
    expect(jQuery.ajax).toHaveBeenCalled()
    # HTML container is hidden
    expect(domElement.is(":visible")).not.toBeTruthy()
    # Check that there are no entities in the chord
    expect( d3.selectAll(".entity")[0].length ).toEqual(0)

  it "creates successfully a chord with three entities and three relations", ->
    fakeResponse = getJSONFixture("chord_1.json")
    # Set a mock object to replace jquery Ajax POST with fake / mock results
    spyOn(jQuery, "ajax").and.callFake((request)->
      request.success fakeResponse
    )
        
    # Initialize the plugin
    domElement.chord
      widget_id: "wl_chord_global"
    
    # Jquery post() has been called during the initialization
    expect(jQuery.ajax).toHaveBeenCalled()
    # HTML container is visible
    expect(domElement.is(":visible")).toBeTruthy()
    # Check the presence of three relations and three entities
    relations = d3.selectAll(".relation")[0].length
    expect( relations ).toEqual(3)
    entities = d3.selectAll(".entity")[0].length
    expect( entities ).toEqual(3)
 
