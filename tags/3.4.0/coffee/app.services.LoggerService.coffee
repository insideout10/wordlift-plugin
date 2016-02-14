angular.module('LoggerService', ['wordlift.tinymce.plugin.services.Helpers'])
.service('LoggerService', [ '$log', ($log) ->

    # Prepare the service instance.
    service = {}

    # Parse the function name.
    getFunctionName = (caller) ->
      switch match = /function ([^(]*)/i.exec caller.toString()
        when null then 'unknown'
        else
          if '' is match[1] then 'anonymous' else match[1]

    ###*
     * Log an information.
     *
     * @param {string} The message to log.
     ###
    service.debug = (message, params) ->
      $log.debug "#{getFunctionName(arguments.callee.caller)} - #{message}"

      if params?
        ($log.debug "[ #{key} :: "; $log.debug value; $log.debug "]") for key, value of params

    # return the service
    service
  ])
