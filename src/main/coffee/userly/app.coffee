angular
	.module( "userlyApp", [ "userly" ] )
	.constant( "baseUrl", "../wp-content/plugins/wordlift/" )
	.constant( "consumerKeyOptionName", "wordlift_consumer_key" )
	.config( [ "applicationId", "baseUrl", "$httpProvider", "$locationProvider", "$routeProvider", ( applicationId, baseUrl, $httpProvider, $locationProvider, $routeProvider ) ->

		$locationProvider.html5Mode false

		$routeProvider
			.when "/:sid/:tok"
				templateUrl: "#{baseUrl}/html/userly/loggedin.html"
				controller: "LoggedInCtrl"
			.when "/userlogin"				
				templateUrl: "#{baseUrl}/html/userly/login.html"
				controller: "LoginCtrl"
			.when "/register"				
				templateUrl: "#{baseUrl}/html/userly/register.html"
				controller: "RegisterCtrl"
			.when "/activate/:activationKey"
				templateUrl: "#{baseUrl}/html/userly/login.html"
				controller: "UserActivationCtrl"
			.otherwise
				redirectTo: "/userlogin"

	])
	.service( "SessionApiService", [ "ApiService", "$http", "$q", "$location", "$window", "$log", ( ApiService, $http, $q, $location, $window, $log ) ->

		applicationKey: "I1ijAG2PklODaWaqQOyp"
		sessionId: null
		sessionToken: null

		call: ( sessionId = null, sessionToken = null ) ->
			deferred = $q.defer()

			if null isnt sessionId and null isnt sessionToken
				@sessionId = sessionId
				@sessionToken = sessionToken

			that = @

			$http(
					method: "GET"
					cache: false
					url: ApiService.getUrl() + "user/me"
					headers:
						"Application-Key": @applicationKey
						"Session-Id": @sessionId
						"Session-Token": @sessionToken
				)
				.success (data, status, headers) ->
					that.sessionToken = headers( "Session-Token" )
					deferred.resolve data
				.error (data, status, headers) ->
					$log.info headers()
					$log.info headers( "sessionToken" )
					deferred.reject data

			deferred.promise

		clear: ->
			@sessionId = @sessionToken = null
			$location.path("")		
	])
	.service( "WordPressOptionsService", [ "$http", "$q", "$rootScope", "$log", ( $http, $q, $rootScope, $log ) ->

		getOption: ( name ) ->
			deferred = $q.defer()

			$http(
					method: "GET"
					url: "admin-ajax.php"
					params:
						action: "wordpress.option"
						name: name
				)
				.success (data, status) ->
					deferred.resolve data
				.error (data, status) ->
					deferred.reject data

			deferred.promise

		setOption: ( name, value ) ->
			deferred = $q.defer()

			$http(
					method: "PUT"
					url: "admin-ajax.php"
					params:
						action: "wordpress.option"
						name: name
						value: value
				)
				.success (data, status) ->
					deferred.resolve data
					$rootScope.$broadcast "WordPressOptionsService.setOption", name, value
				.error (data, status) ->
					deferred.reject data

			deferred.promise
	])
	.service( "SpinnerService", [ "$log", ( $log ) ->
		opts:
			lines: 15 # The number of lines to draw
			length: 25 # The length of each line
			width: 8 # The line thickness
			radius: 28 # The radius of the inner circle
			corners: 1 # Corner roundness (0..1)
			rotate: 0 # The rotation offset
			color: '#000' # #rgb or #rrggbb
			speed: 1 # Rounds per second
			trail: 79 # Afterglow percentage
			shadow: true # Whether to render a shadow
			hwaccel: true # Whether to use hardware acceleration
			className: 'spinner' # The CSS class to assign to the spinner
			zIndex: 2e9 # The z-index (defaults to 2000000000)
			top: 'auto' # Top position relative to parent in px
			left: 'auto' # Left position relative to parent in px

		spin: ( elementId ) ->
			target = document.getElementById elementId
			jQuery( target ).data( "spinner", new Spinner( @opts ).spin( target ) )

		stop: ( elementId ) ->
			target = document.getElementById elementId
			jQuery( target ).data( "spinner" ).stop()
	])
	.controller( "HomeCtrl", [ "AuthenticationService", "$scope", "$log", ( AuthenticationService, $scope, $log ) ->

		$scope.$on "AuthenticationService.isLoggedIn", ( loggedIn ) ->
			$log.info "Auth Serv is #{AuthenticationService.isLoggedIn}"
			$scope.isLoggedIn = AuthenticationService.isLoggedIn

	])
	.controller( "ConsumerKeyCtrl", [ "consumerKeyOptionName", "MessageService", "WordPressOptionsService", "$scope", "$log", ( consumerKeyOptionName, MessageService, WordPressOptionsService, $scope, $log ) ->

		$scope.$on "WordPressOptionsService.setOption", ( event, name, value ) ->
			$scope.consumerKey = value if name is consumerKeyOptionName

		$scope.getConsumerKey = ->
			WordPressOptionsService.getOption( consumerKeyOptionName )
				.then ( data ) ->
					$scope.consumerKey = data
				, ( data ) ->
					MessageService.error "An error occurred: #{data.message}\n(#{data.simpleName})."

		$scope.setConsumerKey = ->
			WordPressOptionsService.setOption( consumerKeyOptionName, $scope.consumerKey )
				.then ->
					$scope.alertClass = "alert-success"
					$scope.alert = "<strong>Perfect!</strong> Your consumer key has been set."
				, ( data ) ->
					$scope.alertClass = "alert-error"
					$scope.alert = "<strong>Ooops!</strong> Cannot set the consumer key: #{data.message}\n<small>(#{data.simpleName})</small>."

		$scope.getConsumerKey()
	])
	.controller( "LoginCtrl", [ "consumerKeyOptionName", "ApiService", "AuthenticationService", "MessageService", "SessionApiService", "SpinnerService", "WordPressOptionsService", "$window", "$scope", "$log", ( consumerKeyOptionName, ApiService, AuthenticationService, MessageService, SessionApiService, SpinnerService, WordPressOptionsService, $window, $scope, $log ) ->

		$scope.login = ->
			SpinnerService.spin "loginForm"
			password = $scope.password
			$scope.password = ""
			AuthenticationService.login( $scope.userName, password )
				.then ( data ) ->
					$scope.alertClass = "alert-success"
					$scope.alert = "<strong>Good!</strong> Authentication is successful, you're consumer key has been set and you're ready to go."
					WordPressOptionsService.setOption consumerKeyOptionName, data.consumerKey
					SpinnerService.stop "loginForm"

				, ( data ) ->
					$scope.alertClass = "alert-error"
					$scope.alert = "<strong>Ouch!</strong> Authentication failed: #{data.message}\n<small>(#{data.simpleName})</small>"
					SpinnerService.stop "loginForm"

		$scope.signIn = ( provider ) ->
			$window.location.href = "admin-ajax.php?action=wordlift.idntikit&url=" + escape( ApiService.getUrl() + "provider/wordlift/#{provider}" ) # ApiService.getUrl() + "provider/wordlift/#{provider}"

	])
	.controller( "LoggedInCtrl", [ "consumerKeyOptionName", "SessionApiService", "SpinnerService", "WordPressOptionsService", "$routeParams", "$scope", "$log", ( consumerKeyOptionName, SessionApiService, SpinnerService, WordPressOptionsService, $routeParams, $scope, $log ) ->
		$log.info $routeParams

		SpinnerService.spin "spinnable"
		SessionApiService.call( $routeParams.sid, $routeParams.tok )
			.then ( data ) ->
				$scope.alertClass = "alert-success"
				$scope.alert = "<strong>Good!</strong> Authentication is successful, you're consumer key has been set and you're ready to go."
				WordPressOptionsService.setOption consumerKeyOptionName, data.consumerKey
				SpinnerService.stop "spinnable"

			, ( data ) ->
				$scope.alertClass = "alert-error"
				$scope.alert = "<strong>Ouch!</strong> Authentication failed: #{data.message}\n<small>(#{data.simpleName})</small>"
				SpinnerService.stop "spinnable"

		$scope.logout = ->
			SessionApiService.clear()
	])
	.controller( "RegisterCtrl", [ "applicationId", "consumerKeyOptionName", "MessageService", "SpinnerService", "UserRegistrationService", "WordPressOptionsService", "$scope" ,"$log", ( applicationId, consumerKeyOptionName, MessageService, SpinnerService, UserRegistrationService, WordPressOptionsService, $scope, $log ) ->

		$scope.register = ->
			if $scope.registerForm.$valid
				SpinnerService.spin "registerForm"
				UserRegistrationService.register(
						userName: $scope.username
						password: $scope.password
						confirmPassword: $scope.passwordControl
						email: $scope.email
					)
					.then ( data ) ->
						SpinnerService.stop "registerForm"
						if (data.userName?)
							$scope.alertClass = "alert-success"
							$scope.alert = "<strong>Good!</strong> Registration is successful, your consumer key has been set and you're ready to go. <strong>Don't forget to activate you're account though</strong>, by clicking the link we sent to your e-mail address.<br/><br/><strong>Warning</strong>: for the time being, if successful, the activation page will be blank, nothing will be displayed. We're working on that."
							WordPressOptionsService.setOption consumerKeyOptionName, data.consumerKey
						else
							$scope.alertClass = "alert-error"
							$scope.alert = "<strong>Ouch!</strong> Registration failed, please try with a different username."

					, ( data ) ->
						$scope.alertClass = "alert-error"
						$scope.alert = "<strong>Ouch!</strong> Registration failed: #{data.message}\n<small>(#{data.simpleName})</small>"
						SpinnerService.stop "registerForm"
	])
