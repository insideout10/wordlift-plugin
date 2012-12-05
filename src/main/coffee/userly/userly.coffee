angular
	.module("userly", [])
	.constant( "applicationId", "wordlift" )
	.config( [ "applicationId", "$httpProvider", "$locationProvider", "$routeProvider", ( applicationId, $httpProvider, $locationProvider, $routeProvider ) ->
		$httpProvider.defaults.headers.common["Application-Id"] = applicationId
		$httpProvider.defaults.withCredentials = true
	])
	.directive( "passwordConfirmation", [ "$log", ( $log ) ->
		require: "?ngModel"
		link: ( scope, elm, attr, ctrl ) ->
			return if not ctrl?

			validator = ( value ) ->
				if scope.password isnt value
					$log.error "password don't match"
					ctrl.$setValidity "passwordConfirmation", false
					return

				ctrl.$setValidity "passwordConfirmation", true
				value

			# ctrl.$formatters.push validator
			ctrl.$parsers.unshift validator

			# attr.$observe "passwordConfirmation", ->
			# 	validator ctrl.$viewValue
	])
	.service( "BasicAuthenticationService", [ "$log", ( $log ) ->
		getHeaderValue: ( userName, password ) ->
			"Basic #{window.btoa userName + ":" + password}"
	])
	.service( "ApiService", [ "BasicAuthenticationService", "$http", "$q", "$log", ( BasicAuthenticationService, $http, $q, $log ) ->

		authToken: null

		clearAuthToken: ->
			@authToken = null

		getUrl: ->
			"http://api.idntik.it:8081/api/"
			# "http://localizeme.dyndns.org:8081/api/"

		execute: ( method, path, userName = null, password  = null, storeAuthToken = false, data = null) ->
			deferred = $q.defer()

			authToken = @authToken
			authToken = BasicAuthenticationService.getHeaderValue( userName, password ) if userName and password

			that = @

			$http(
					method: method
					url: @getUrl() + path
					headers:
						Authorization: authToken
					data: data
				)
				.success (data, status, headers, config) ->
					that.authToken = authToken if storeAuthToken
					deferred.resolve data
				.error (data, status, headers, config) ->
					deferred.reject data

			deferred.promise			

	])
	.service( "MessageService", [ "$log", ( $log ) ->
		info: ( message ) ->
			$( "#message" )
				.removeClass("text-error")
				.addClass("text-info")
				.html( message )

		error: ( message ) ->
			$( "#message" )
				.removeClass("text-info")
				.addClass("text-error")
				.html( message )
	])
	.service( "AuthenticationService", [ "applicationId", "ApiService", "$q", "$rootScope", "$log", ( applicationId, ApiService, $q, $rootScope, $log ) ->

		isLoggedIn: false

		setLoggedIn: ( loggedIn ) ->
			@isLoggedIn = loggedIn
			$rootScope.$broadcast "AuthenticationService.isLoggedIn", loggedIn

		login: ( userName, password ) ->
			deferred = $q.defer()

			ApiService.execute( "GET", "user/me", userName, password, true )
				.then ( data ) =>
					@setLoggedIn true		
					deferred.resolve data
				, ( data ) =>
					@setLoggedIn false
					deferred.reject data

			deferred.promise

		logout: ->
			ApiService.clearAuthToken()
			@setLoggedIn false

	])
	.service( "UserRegistrationService", [ "ApiService", "MessageService", "$q", "$log", ( ApiService, MessageService, $q, $log ) ->

		activate: ( activationKey ) ->
			deferred = $q.defer()

			ApiService.execute( "GET", "user/activate/#{activationKey}" )
				.then ( data ) ->
					deferred.resolve data
				, ( data ) ->
					deferred.reject data

			deferred.promise

		register: ( data ) ->
			deferred = $q.defer()

			ApiService.execute( "POST", "user", null, null, false, data )
				.then ( data ) ->
					deferred.resolve data
				, ( data ) ->
					deferred.reject data

			deferred.promise
	])
	.controller( "AuthenticationCtrl", [ "AuthenticationService", "ApiService", "MessageService", "$location", "$http", "$scope", "$log", ( AuthenticationService, ApiService, MessageService, $location, $http, $scope, $log ) -> 

		$scope.$on "AuthenticationService.isLoggedIn", ( loggedIn ) ->
			$log.info "Auth Serv is #{AuthenticationService.isLoggedIn}"
			$scope.isLoggedIn = AuthenticationService.isLoggedIn

		$scope.login = ->
			password = $scope.password
			# clear the password
			$scope.password = ""
			AuthenticationService.login( $scope.username, password )
				.then ->
					MessageService.info "You successfully authenticated!"	
				, ( data ) ->
					MessageService.error "#{data.message}\n(#{data.simpleName})"

		$scope.logout = ->
			AuthenticationService.logout()
			MessageService.info "You logged out!"

		$scope.ping = ->
			ApiService.execute( "GET", "ping" )
				.then (data) ->
					MessageService.info data
				, ( data ) ->
					MessageService.error "#{data.message}\n(#{data.simpleName})"

		$scope.register = ->
			$location.path "/register"

	])
	.controller( "UserRegistrationCtrl", [ "applicationId", "UserRegistrationService", "$location", "$scope" ,"$log", ( applicationId, UserRegistrationService, $location, $scope, $log ) ->

		$scope.openLogin = ->
			$location.path "/"

		$scope.register = ->
			if $scope.registerForm.$valid
				UserRegistrationService.register
					application:
						applicationId: applicationId
					userName: $scope.username
					password: $scope.password
					email: $scope.email
	])
	.controller( "UserActivationCtrl", [ "applicationId", "MessageService", "UserRegistrationService", "$location", "$routeParams", "$scope" ,"$log", ( applicationId, MessageService, UserRegistrationService, $location, $routeParams, $scope, $log ) ->

		$log.info $routeParams

		$scope.activate = ( activationKey ) ->
			UserRegistrationService.activate( activationKey )
				.then ( data ) ->
					MessageService.info "Activation completed successfully!"
				, ( data ) ->
					MessageService.error "Uh oh: #{data.message}\n(#{data.simpleName})"

		$scope.goToLogin = ->
			$location.path "/"

		$scope.activate $routeParams.activationKey if $routeParams.activationKey?
	])



