angular.module('wordlift.editpost.widget.services.GeoLocationService', ['geolocation'])
# Retrieve GeoLocation coordinates and process them trough reverse geocoding
.service('GeoLocationService', [ 'geolocation', '$log', '$rootScope', '$document', '$q', '$timeout', ( geolocation, $log, $rootScope, $document, $q, $timeout )-> 
  
  GOOGLE_MAPS_LEVEL = 'locality'
  GOOGLE_MAPS_KEY = 'AIzaSyAhsajbqNVd7ABlkZvskWIPdiX6M3OaaNM'
  GOOGLE_MAPS_API_ENDPOINT = 'https://maps.googleapis.com/maps/api/js?key=' + GOOGLE_MAPS_KEY
  
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

  service = {}
  
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
                  $rootScope.$broadcast "currentUserLocalityDetected", result.formatted_address                                   
                  return    
             
  service

])

