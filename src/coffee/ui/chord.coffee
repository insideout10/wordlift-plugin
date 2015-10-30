$ = jQuery

# Add a chord plugin object to jQuery
$.fn.extend
  chord: (options) ->
    
    settings = {
      dataEndpoint: undefined
      mainColor: '#777'
      depth: 2
      maxLabelLength: 30
      maxWordLength: 5
      debug: false
    }

    # Merge default settings with options.
    settings = $.extend settings, options
    
    # Create a reference to dom wrapper element
    container = $(@)
  
    retrieveChordData = ->
      $.ajax
        type: 'GET'
        url: settings.dataEndpoint
        data:
          depth: settings.depth
        success: (response) ->
          buildChord response
    
    buildChord = (data) ->
      if not data.entities? or data.entities.length < 2
        container.hide()
        log "No data found for the chord."
        return
      
      # define some service functions, then build the chord
      
      translate = (x, y, sizeX, sizeY) -> 'translate(' + x * sizeX + ',' + y * sizeY + ')'
    
      rotate = (x) -> "rotate(#{x})"
    
      rad2deg = (a) ->  ( a / (2*Math.PI)) * 360
    
      sign = (n) -> if n >= 0.0 then 1 else -1
      
      beautifyLabel = (words) ->
        # when labels are way too long, show only the first part of them
        # (the whole label will be displayed in the tooltip at mouseover) 
        if words.length > settings.maxLabelLength 
          words = words.substring(0, settings.maxLabelLength) + '...'
        
        # split in words
        words = words.split(/\s/)
        
        # group shortest words
        n = []
        for w in [0..words.length-1]
          if words[w].length > settings.maxWordLength or w is words.length-1
            n.push words[w]
          else
            words[w+1] = words[w] + ' ' + words[w+1]
        
        return n
    
      colorLuminance = (hex, lum) ->
        # Validate hex string.
        hex = String(hex).replace(/[^0-9a-f]/gi, '')
    
        if (hex.length < 6)
          hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2]
    
        lum = lum or 0
    
        # Convert to decimal and change luminosity.
        rgb = "#"
        c = undefined
        i = undefined
    
        for i in [0..3]
          c = parseInt(hex.substr(i*2,2), 16)
          c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16)
          rgb += ("00"+c).substr(c.length)
    
        rgb
    
      getEntityIndex = (uri) ->
        return i for i in [0..data.entities.length] when data.entities[i].uri is uri
        -1
    
      # Build adiacency matrix.
      matrix = []
      matrix.push (0 for e in data.entities) for entity in data.entities
    
      for relation in data.relations
        x = getEntityIndex relation.s
        y = getEntityIndex relation.o
        matrix[x][y] = 1
        matrix[y][x] = 1
      
      viz = d3.select('#' + container.attr('id') ).append('svg')
      viz.attr('width', '100%').attr('height', '100%')
    
      # Getting dimensions in pixels.
      width = parseInt(viz.style('width'))
      height = parseInt(viz.style('height'))
      size =  if height < width then height else width
      innerRadius = size*0.2
      outerRadius = size*0.25
      arc = d3.svg.arc().innerRadius(innerRadius).outerRadius(outerRadius)
    
      chord = d3.layout.chord()
        .padding(0.3)
        .matrix(matrix)
    
      # Draw relations.
      viz.selectAll('chords')
        .data(chord.chords)
        .enter()
        .append('path')
        .attr('class', 'relation')
        .attr('d', d3.svg.chord().radius(innerRadius))
        .attr('transform', translate(0.5, 0.5, width, height) )
        .style('opacity', 0.2)
        .on('mouseover', ->  d3.select(this).style('opacity', 0.8) )
        .on('mouseout', ->  d3.select(this).style('opacity', 0.2) )
    
      # Draw entities.
      viz.selectAll('arcs')
        .data(chord.groups)
        .enter()
        .append('path')
        .attr('class', (d) ->
          console.log d
          return "entity #{data.entities[d.index].css_class}"
        )
        .attr('d', arc)
        .attr('transform', translate(0.5, 0.5, width, height))
        .style('fill', (d) ->
          baseColor = settings.mainColor;
          type = data.entities[d.index].type
          return baseColor if(type == 'post')
          return colorLuminance( baseColor, -0.5) if type is 'entity'
          colorLuminance( baseColor, 0.5 )
        )
    
      # Draw entity labels.
      viz.selectAll('arcs_labels')
        .data(chord.groups)
        .enter()
        .append('text')
        .attr('class', 'label')
        .attr('font-size', ->
          fontSize = parseInt( size/35 )
          fontSize = 8 if(fontSize < 8)
          fontSize + 'px'
        )
        .each( (d) ->
          
          # beautify label
          n = beautifyLabel( data.entities[d.index].label )
          
          # get the current element
          text = d3.select(this)
            .attr("dy", n.length / 3 - (n.length-1) * 0.9 + 'em')
            .text(n[0])
    
          # now loop
          for i in [1..n.length]
            text.append("tspan")
            .attr('x', 0)
            .attr('dy', '1em')
            .text(n[i])
    
          text.attr('transform', (d) ->
            alpha = d.startAngle - Math.PI/2 + Math.abs((d.endAngle - d.startAngle)/2)
            labelWidth = 3
            labelAngle = undefined
            if(alpha > Math.PI/2)
              labelAngle = alpha - Math.PI
              labelWidth += d3.select(this)[0][0].clientWidth
            else
              labelAngle = alpha
    
            labelAngle = rad2deg( labelAngle )
    
            rX = (outerRadius + labelWidth)/width
            rY = (outerRadius + labelWidth)/height
            x = 0.5 + ( rX * Math.cos(alpha) )
            y = 0.5 + ( rY * Math.sin(alpha) )
    
            translate(x, y, width, height) + rotate( labelAngle )
          )
          .attr('text-anchor', (d) ->

            isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1
            alpha = d.startAngle + Math.abs((d.endAngle - d.startAngle)/2)

            if isFirefox and alpha > Math.PI
              return 'end'

            return null
          )
    
      )
    
      # Creating an hidden tooltip.
      tooltip = d3.select('body').append('div')
        .attr('class', 'tooltip')
        .style('background-color', 'white')
        .style('opacity', 0.0)
        .style('position', 'absolute')
        .style('z-index', 100)
    
      #  Dynamic behavior for entities.
      viz.selectAll('.entity, .label')
        .on('mouseover', (c) ->
          d3.select(this).attr('cursor','pointer')
          viz.selectAll('.relation')
            .filter( (d, i) -> d.source.index is c.index or d.target.index is c.index )
            .style( 'opacity', 0.8)
    
          # Show tooltip.
          tooltip.text( data.entities[c.index].label ).style('opacity', 1.0 )
        )
        .on('mouseout', (c) ->
          viz.selectAll('.relation')
            .filter( (d, i) -> d.source.index is c.index or d.target.index is c.index )
            .style( 'opacity', 0.2 )
    
            # Hide tooltip.
            tooltip.style('opacity', 0.0)
        )
        .on('mousemove', ->
          # Change tooltip position.
          tooltip.style("left", (d3.event.pageX) + "px")
            .style("top", (d3.event.pageY - 30) + "px")
        )
        .on('click', (d) ->
          url = data.entities[d.index].url
          window.location = url
        )
     
     # Initialization method
     init = ->
      retrieveChordData()

     # Simple logger 
     log = (msg) ->
      console?.log msg if settings.debug
     
     # start chord operations   
     init()

jQuery ($) ->
  $('.wl-chord').each ->
    
    element = $(@)
    
    params = element.data()
    $.extend params, wl_chord_params
    
    url = "#{params.ajax_url}?" + $.param( 'action': params.action, 'post_id': params.postId )

    # Launch chord.
    element.chord
      dataEndpoint: url
      depth: params.depth
      mainColor: params.mainColor
