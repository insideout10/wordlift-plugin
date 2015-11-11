describe "GeoMap Ui Component Unit Test", ->
  domElement = undefined
  
  # Tests set-up.
  beforeEach inject(->
    jasmine.getJSONFixtures().fixturesPath = "base/app/assets/"
    domElement = $('<div id="map"></div>')
    $(document.body).append $(domElement)
  )

  afterEach inject(-> 
    $( "#map" ).remove()
  )

  it "creates successfully a map with 2 features", ->

    fakeResponse = getJSONFixture("geomap_0.json")
    # Set a mock object to replace jquery Ajax POST with fake / mock results
    spyOn(jQuery, "ajax").and.callFake((request)->
    	request.success fakeResponse
    )
    # Initialize the plugin 
    domElement.geomap()

    # Jquery post() has been called during the initialization
    expect(jQuery.ajax).toHaveBeenCalled()
    # Check if the map is visible
    expect(domElement.is(":visible")).toBeTruthy()
    # Check that 2 features are selected accordingly to the json file
    expect(domElement.find('.leaflet-clickable').length).toEqual(2)

  it "fails to create a map with an empty features array", ->

    fakeResponse = getJSONFixture("geomap_1.json")
    # Set a mock object to replace jquery Ajax POST with fake / mock results
    spyOn(jQuery, "ajax").and.callFake((request)->
    	request.success fakeResponse
    )
    # Initialize the plugin 
    domElement.geomap()
    # Check if the map is visible
    expect(domElement.is(":visible")).toBeFalsy()
    # Check that 2 features are selected accordingly to the json file
    expect(domElement.find('.leaflet-clickable').length).toEqual(0)

  it "fails to create a map with an undefined features array", ->
    fakeResponse = getJSONFixture("geomap_2.json")
    # Set a mock object to replace jquery Ajax POST with fake / mock results
    spyOn(jQuery, "ajax").and.callFake((request)->
    	request.success fakeResponse
    )
    # Initialize the plugin 
    domElement.geomap()
    # Check if the map is visible
    expect(domElement.is(":visible")).toBeFalsy()
    # Check that 2 features are selected accordingly to the json file
    expect(domElement.find('.leaflet-clickable').length).toEqual(0)
