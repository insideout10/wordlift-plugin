describe "Test di prova", () ->    

	beforeEach(module("wordlift.tinymce.plugin"))
	beforeEach(module("wordlift.tinymce.plugin.config"))	
	beforeEach(module("wordlift.tinymce.plugin.services"))
	beforeEach(module("wordlift.tinymce.plugin.controllers"))

	describe "HelloController", () ->

	scope = null
	
	beforeEach inject ($controller, $rootScope) ->
		scope = $rootScope.$new()
		$controller 'HelloController', { $scope: scope }	
		return

	it "Tests if scope.annotations is a blank array", () ->
		expect(scope.annotations).toEqual [] 
		return
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