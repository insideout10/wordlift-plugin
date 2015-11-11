$ = jQuery

# Add a timeline plugin object to jQuery
$.fn.extend

  timeline: (options) ->
    
    # Default settings
    settings = {
      dataEndpoint: undefined
      width: '100%'
      height: '600'
      debug: false
    }

    # Merge default settings with options.
    settings = $.extend settings, options
    
    # Create a reference to dom wrapper element
    container = $(@)

    # Retrieve data from for timeline rendering
    retrieveTimelineData = ->
      $.ajax
        type: 'GET'
        url: settings.dataEndpoint
        success: (response) ->
          buildTimeline response

    # Build a Timeline obj via TimelineJS
    # See: https://github.com/NUKnightLab/TimelineJS
    buildTimeline = (data) ->
      if data.timeline?
        createStoryJS
          type: 'timeline'
          width: settings.width
          height: settings.height
          source: data
          embed_id: container.attr('id')
          start_at_slide: data.startAtSlide 
      else
        container.hide()
        log "Timeline data missing: timeline cannot be rendered"
        return

    # Initialization method
    init = ->
      retrieveTimelineData()

    # Simple logger 
    log = (msg) ->
      console?.log msg if settings.debug

    init()

jQuery ($) ->
  $('.wl-timeline').each ->
    element = $(@)
    
    params = element.data()
    $.extend params, wl_timeline_params
    
    url = "#{params.ajax_url}?" + $.param( 'action': params.action, 'post_id': params.postId )

    $(this).timeline
      dataEndpoint: url