class Traslator

  # Hold the html and textual positions.
  _htmlPositions: []
  _textPositions: []
  _htmlFragments: []

# Hold the html and text contents.
  _html: ''
  _text: ''

  decodeHtml = (html)->
    txt = document.createElement("textarea")
    txt.innerHTML = html
    txt.value

  # Create an instance of the traslator.
  @create: (html) ->
    traslator = new Traslator(html)
    traslator.parse()
    traslator

  @version: '1.0.0'

  constructor: (html) ->
    @_html = html

  parse: ->
    @_htmlPositions = []
    @_textPositions = []
    @_htmlFragments = []
    @_text = ''

    # Changing this regex requires changing the regex also in WLS. Update the Traslator version when changing the regex.
    pattern = /((?:&(?![#\w]))*[^&<>]*)(&[^&;]*;|<[!\/]?(?:[\w-]+|\[cdata\[.*?]])(?: [\w_-]+(?:="[^"]*")?)*[^>]*>)([^&<>]*)/gim

    textLength = 0
    htmlLength = 0

    while (match = pattern.exec @_html)?

# Get the text pre/post and the html element
      htmlPre = match[1]
      htmlElem = match[2]
      htmlPost = match[3]

      # Get the text pre/post w/o new lines.
      # Add \n\n when it's needed depending on last tag
      textPre = htmlPre + (if htmlElem.toLowerCase() in ['</p>', '</li>'] then '\n\n' else '')
      #      dump "[ htmlPre length :: #{htmlPre.length} ][ textPre length :: #{textPre.length} ]"
      textPost = htmlPost

      # Sum the lengths to the existing lengths.
      textLength += textPre.length

      if /^&[^&;]*;$/gim.test htmlElem
        textLength += 1

      htmlLength += htmlPre.length

      if htmlElem.startsWith('<br')
        # console.log "Adding intermediate position: html #{htmlLength}, text #{textLength}."
        @_htmlPositions.push htmlLength
        @_textPositions.push textLength
        @_htmlFragments.push htmlElem

      # For html add the length of the html element.
      htmlLength += htmlElem.length

      # Add the position.
      @_htmlPositions.push htmlLength
      @_textPositions.push textLength
      @_htmlFragments.push htmlElem

      textLength += textPost.length
      htmlLength += htmlPost.length

      htmlProcessed = ''
      if /^&[^&;]*;$/gim.test htmlElem
        htmlProcessed = decodeHtml htmlElem

      # Add the textual parts to the text.
      @_text += textPre + htmlProcessed + textPost


    # In case the regex didn't find any tag, copy the html over the text.
    @_text = new String(@_html) if '' is @_text and !pattern.match @_html

    # Add text position 0 if it's not already set.
    if 0 is @_textPositions.length or 0 isnt @_textPositions[0]
      @_htmlPositions.unshift 0
      @_textPositions.unshift 0

    # console.log 'Parsing complete', { html: @_html, text: @_text, htmlPositions: @_htmlPositions, textPositions: @_textPositions }
#    # console.log '=============================='
#    # console.log @_html
#    # console.log @_text
#    # console.log @_htmlPositions
#    # console.log @_textPositions
#    # console.log '=============================='

# Get the html position, given a text position.
  text2html: (pos) ->
    htmlPos = 0
    textPos = 0

    for i in [0...@_textPositions.length]
      break if pos < @_textPositions[i]
      htmlPos = @_htmlPositions[i]
      textPos = @_textPositions[i]
      break if pos is @_textPositions[i] and @_htmlFragments[i].startsWith('<br')

    # console.log "Text Position #{pos} converted to #{htmlPos + pos - textPos}.", { htmlPosition: htmlPos, textPosition: textPos }

    htmlPos + pos - textPos

# Get the text position, given an html position.
  html2text: (pos) ->
#    dump @_htmlPositions
#    dump @_textPositions

# Return 0 if the specified html position is less than the first HTML position.
    return 0 if pos < @_htmlPositions[0]

    htmlPos = 0
    textPos = 0

    for i in [0...@_htmlPositions.length]
      break if pos < @_htmlPositions[i]
      htmlPos = @_htmlPositions[i]
      textPos = @_textPositions[i]

    #    # console.log "#{textPos} + #{pos} - #{htmlPos}"
    textPos + pos - htmlPos

# Insert an Html fragment at the specified location.
  insertHtml: (fragment, pos) ->

#    dump @_htmlPositions
#    dump @_textPositions
#    # console.log "[ fragment :: #{fragment} ][ pos text :: #{pos.text} ]"
    htmlPos = @text2html pos.text

    @_html = @_html.substring(0, htmlPos) + fragment + @_html.substring(htmlPos)

    # console.debug "Html fragment #{fragment} inserted at #{htmlPos} (text #{pos.text}), new html:\n#{@_html}"

    # Reparse
    @parse()

# Return the html.
  getHtml: ->
    @_html

# Return the text.
  getText: ->
    @_text
window.Traslator = Traslator