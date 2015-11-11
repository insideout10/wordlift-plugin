# TODO this code has to be integrated within angular app
jQuery ($) ->
  $("body").append '''
    <div id="wordlift_chord_dialog">
    <form>
    <p>    
      <input value="2" id="wordlift_chord_depth_field" readonly size="3"> 
      Depth: Max degree of separtation between entities.
    </p>
    <div id="wordlift_chord_depth_slider"></div>
    <p>
      Base to generate the color palette of the Graph.<br />
      <input type="text" value="#22f" id="wordlift_chord_color_field" size="4">
    </p>
    <p>
      <input value="500" id="wordlift_chord_width_field" size="4">
      Width of the Graph in pixels
    </p>
    <p>
      <input value="520" id="wordlift_chord_height_field" size="4">
      Height of the Graph in pixels.
    </p>
    <p>
      <input id="wordlift_chord_dialog_ok" type="button" value="Ok" width="100">
    </p>
    </form>
    </div>
  '''
  
  # Set up color picker
  $("#wordlift_chord_color_field").wpColorPicker hide:true
  
  # Set up depth slider
  $("#wordlift_chord_depth_slider").slider
    range: "max"
    min: 1
    max: 5
    value: 2
    slide: (event, ui) ->
      $("#wordlift_chord_depth_field").val ui.value
      return

  $("#wordlift_chord_dialog").hide()
  
  # Generatr shortcode.
  $("#wordlift_chord_dialog_ok").on "click", ->
    
    # We should get default parameters from the php
    width = $("#wordlift_chord_width_field").val()
    height = $("#wordlift_chord_height_field").val()
    main_color = $("#wordlift_chord_color_field").val()
    depth = $("#wordlift_chord_depth_field").val()

    shortcode_text = "[wl-chord width=#{width}px height= #{height}px main_color=#{main_color} depth=#{depth}]"
    
    # Send shortcode to the editor								  
    # TODO this code should be managed trough EditorService
    top.tinymce.activeEditor.execCommand "mceInsertContent", false, shortcode_text
    $("#wordlift_chord_dialog").dialog "close"
    return

  return
