$ = jQuery

# Add a timeline plugin object to jQuery
$.fn.extend

  timeline: (options) ->

    #
    options = $.extend {dataEndpoint: null, settings: {}}, options

    # Create a reference to dom wrapper element
    container = $(@)

    # Build a Timeline obj via TimelineJS
    # See: https://github.com/NUKnightLab/TimelineJS
    buildTimeline = (data) ->
      if not data.timeline?
        container.parent().hide()
        return

      # TimelineJS v3 constructor.
      new TL.Timeline(container.attr('id'), data.timeline, options.settings)

    # Initialization method
    init = ->
      $.ajax
        type: 'GET'
        url: options.dataEndpoint
        success: (response) ->
          buildTimeline response

    init()

jQuery ($) ->
  $('.wl-timeline').each ->
    element = $(@)

    params = element.data()
    $.extend params, wl_timeline_params

    url = "#{params.ajax_url}?" + $.param(
      'action': params.action,
      'post_id': params.postId,
      '_wpnonce': params.wl_timeline_nonce
      'display_images_as': params.display_images_as,
      'excerpt_length': params.excerpt_length)

    $(this).timeline
      dataEndpoint: url
      settings: params.settings