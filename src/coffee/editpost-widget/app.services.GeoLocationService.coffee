angular.module('wordlift.editpost.widget.services.GeoLocationService', ['geolocation'])
# Retrieve GeoLocation coordinates and process them trough reverse geocoding
.service('GeoLocationService', [ 'configuration', 'geolocation', '$log', '$rootScope', '$document', '$q', '$timeout', '$window', ( configuration, geolocation, $log, $rootScope, $document, $q, $timeout, $window)-> 
  
  GOOGLE_MAPS_LEVEL = 'locality'
  GOOGLE_MAPS_KEY = 'AIzaSyAhsajbqNVd7ABlkZvskWIPdiX6M3OaaNM'
  GOOGLE_MAPS_API_ENDPOINT = "https://maps.googleapis.com/maps/api/js?language=#{configuration.currentLanguage}&key=#{GOOGLE_MAPS_KEY}"
  
  $rootScope.$on 'error', (event, msg)->
    $log.warn "Geolocation error: #{msg}"
    $rootScope.$broadcast 'geoLocationError', msg

  # Following code is inspired by
  # https://github.com/urish/angular-load/blob/master/angular-load.js

  @googleApiLoaded = false
  @googleApiPromise = undefined

  loadGoogleAPI = ()->

    if @googleApiPromise?
      return @googleApiPromise

    deferred = $q.defer()
    # Load Google API asynchronously
    element = $document[0].createElement('script')
    # $log.debug "Going to load #{GOOGLE_MAPS_API_ENDPOINT}"
    element.src = GOOGLE_MAPS_API_ENDPOINT
    $document[0].body.appendChild element
    

    callback = (e) ->  
      if element.readyState and element.readyState not in ['complete', 'loaded'] 
        return
      
      $timeout(()->
        deferred.resolve(e)
      )

    element.onload = callback
    element.onreadystatechange = callback
    element.onerror = (e)->

      $timeout(()-> 
        deferred.reject(e)
      )

    @googleApiPromise = deferred.promise
    @googleApiPromise

  # Detect Current Browser
  currentBrowser = ()->
    userAgent = $window.navigator.userAgent
    browsers = 
      chrome: /chrome/i
      safari: /safari/i
      firefox: /firefox/i
      ie: /internet explorer/i
    for key of browsers
      if browsers[key].test(userAgent)
        return key
    'unknown'

  service = {}
  
  # Used to temporaly manage this scenario 
  # https://developers.google.com/web/updates/2016/04/geolocation-on-secure-contexts-only?hl=en
  service.isAllowed = ()->
    # $log.debug "Current browser #{currentBrowser()}, current protocol: #{$window.location.protocol}"
    if currentBrowser() is 'chrome'
      return $window.location.protocol is 'https:'
    true
    
  service.getLocation = ()->

    geolocation.getLocation()
    .then (data) ->
      
      $log.debug "Detected position: latitude #{data.coords.latitude}, longitude #{data.coords.longitude}"
      loadGoogleAPI()
      .then ()->

        geocoder = new google.maps.Geocoder()
        # Perform reverse geocode
        geocoder.geocode
          'location':
             'lat': data.coords.latitude
             'lng': data.coords.longitude
          , (results, status)->
            
            if status is google.maps.GeocoderStatus.OK
              for result in results
                if GOOGLE_MAPS_LEVEL in result.types
                  for ac in result.address_components
                    if GOOGLE_MAPS_LEVEL in ac.types
                      $rootScope.$broadcast "currentUserLocalityDetected", result.formatted_address, ac.long_name                                   
                      return    
             
  service

])

