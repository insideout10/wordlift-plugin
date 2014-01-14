describe "Test configuration dependency", () ->

	service = null
	configuration = null
	
	# Create a mock service just for test purposes
	angular.module("wordlift.unittest",["wordlift.tinymce.plugin.config"])
		.service("MockService", ['Configuration', (Configuration)->
			getConfig:()->
				Configuration
		])
		
	beforeEach () ->
		module("wordlift.tinymce.plugin.config")
		module("wordlift.unittest")
		
		inject (MockService, Configuration) ->
			service = MockService
			configuration = Configuration

	it "Tests if configuration object is available within the service", () ->
		expect(service.getConfig()).toEqual configuration
	
		return