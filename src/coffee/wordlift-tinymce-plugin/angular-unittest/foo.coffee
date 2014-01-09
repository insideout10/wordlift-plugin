describe "Test di prova", () ->    

	beforeEach(module("wordlift.tinymce.plugin"))
	beforeEach(module("wordlift.tinymce.plugin.services"))
	beforeEach(module("wordlift.tinymce.plugin.controllers"))

	describe "HelloController", () ->

	scope = null
	
	beforeEach inject ($controller, $rootScope) ->
		scope = $rootScope.$new()
		$controller 'HelloController', { $scope: scope }	
		return

	it "Test if hello returns 'Ciao Marcello'", () ->
		expect(scope.hello).toBe "Ciao Marcello!" 
		return