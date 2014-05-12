// TODO: review all this code. It could be changed to a jQuery UI component.

getChordData(wl_chord_params);

function getChordData(wl_chord_params){
	//console.log(wl_chord_params);
	jQuery.post(
		wl_chord_params.ajax_url, 
		{
		    action: wl_chord_params.action,
		    post_id: wl_chord_params.post_id,
		    depth: wl_chord_params.depth
		},
		function(response){
			//console.log(response);
			var data  = JSON.parse(response);
			//console.log(data);
		    buildChord(data, wl_chord_params);
		}
	);
}


/*
 * NOT WORKING AND I CAN'T UNDERSTAND WHY
function getChordData(wl_chord_params) {
	console.log("initiating D3 ajax request to " + wl_chord_params.ajax_url);
	d3.xhr( wl_chord_params.ajax_url )
		.header("Content-Type", "application/json")
		.post(
			JSON.stringify({
				action:wl_chord_params.action,
				post_id: wl_chord_params.post_id,
		    	depth: wl_chord_params.depth
			}),
			function(error, response){ 
				console.log(response);
			}
		);
}
*/

function buildChord(dataMock, wl_chord_params) {
	
	// Manage empty or corrupted data
	var niceData = 'entities' in dataMock && 'relations' in dataMock;
	niceData = niceData && (dataMock.entities.length) >= 2 && (dataMock.relations >= 1);
	if( niceData ) {
		d3.select( '#' + wl_chord_params.widget_id )
			.style('height', '50px')
			.html(' --- WordLift shortcode: No entities found. --- ');
			return;
	}
	
	var entities = dataMock.entities;
	var relations = dataMock.relations;
	
	// Build adiacency matrix.
	var matrix = [];
	for(var i=0; i<entities.length; i++) {
		matrix.push([]);
		for(var j=0; j<entities.length; j++) {
			matrix[i].push(0);
		}
	}

	for(var i=0; i<relations.length; i++) {
		var x = getEntityIndex( relations[i].s );
		var y = getEntityIndex( relations[i].o );
		matrix[x][y] = 1;
		matrix[y][x] = 1;
	}
	
	var viz = d3.select( '#' + wl_chord_params.widget_id ).append('svg');
	viz.attr('width', '100%').attr('height', '100%');

	// Getting dimensions in pixels.
	var width = parseInt(viz.style('width'));
	var height = parseInt(viz.style('height'));
	var size;
	if(height < width)
		size = height;
	else
		size = width;
	var innerRadius = size*0.2;
	var outerRadius = size*0.25;
	var arc = d3.svg.arc().innerRadius(innerRadius).outerRadius(outerRadius);

	var chord = d3.layout.chord()
				.padding(0.3)
				.matrix(matrix);

	// Draw relations.
	viz.selectAll('chords')
		.data(chord.chords)
		.enter()
		.append('path')
		.attr('class', 'relation')
		.attr('d', d3.svg.chord().radius(innerRadius))
		.attr('transform', translate(0.5, 0.5))
		.style('opacity', 0.2)
		.on('mouseover', function(){
			d3.select(this).style('opacity', 0.8);
		})
		.on('mouseout', function(){
			d3.select(this).style('opacity', 0.2);
		});

	// Draw entities.
	viz.selectAll('arcs')
		.data(chord.groups)
		.enter()
		.append('path')
		.attr('class', 'entity')
		.attr('d', arc)
		.attr('transform', translate(0.5, 0.5))
		.style('fill', function(d){
				var baseColor = wl_chord_params.main_color;
				var type = entities[d.index].type;
				if(type == 'post')
					return baseColor;
				if(type == 'entity')
					return colorLuminance( baseColor, -0.5);
				return colorLuminance( baseColor, 0.5);
			}
		);
	
	// Draw entity labels.
	viz.selectAll('arcs_labels')
		.data(chord.groups)
		.enter()
		.append('text')
		.attr('class', 'label')
		.html( function(d){
			var lab = entities[d.index].label;
			return beautifyLabel(lab);
		})
		.attr('font-size', function(){
			var fontSize = parseInt( size/35 );
			if(fontSize < 8)
				fontSize = 8;
			return fontSize + 'px';
		})
		.attr('transform', function(d){
			
			var alpha = d.startAngle - Math.PI/2 + Math.abs((d.endAngle - d.startAngle)/2);
			var labelWidth = 3;
			var labelAngle;
			if(alpha > Math.PI/2){
				labelAngle = alpha - Math.PI;
				labelWidth += d3.select(this)[0][0].clientWidth;
			}
			else {
				labelAngle = alpha;
			}
			labelAngle = rad2deg( labelAngle );
			
			var r = (outerRadius + labelWidth)/size;
			var x = 0.5 + ( r * Math.cos(alpha) );
			var y = 0.5 + ( r * Math.sin(alpha) );
		
			
			return translate(x, y) + rotate( labelAngle );
		});
		
		
		// Creating an hidden tooltip.
		var tooltip = d3.select('body').append('div')
    		.attr('class', 'tooltip')
    		.style('background-color', 'white')
    		.style('opacity', 0.0)
    		.style('position', 'absolute')
    		.style('z-index', 100);
		
		// Dynamic behavior for entities.
		viz.selectAll('.entity, .label')
			.on('mouseover', function(c) {
				d3.select(this).attr('cursor','pointer');
				viz.selectAll('.relation')
		            .filter(function(d, i) {
		            	return d.source.index == c.index || d.target.index == c.index;
		            })
		            .style("opacity", 0.8);
		           
		        // Show tooltip.
	            tooltip.text(entities[c.index].label)
	            	.style('opacity', 1.0);	        			            
			})
			.on('mouseout', function(c) {
				viz.selectAll('.relation')
		            .filter(function(d, i) {
		            	return d.source.index == c.index || d.target.index == c.index;
		            })
		            .style("opacity", 0.2);
		            
		        // Hide tooltip.
		        tooltip.style('opacity', 0.0);	
		        
			})
			.on('mousemove', function(){
				// Change tooltip position.
				tooltip.style("left", (d3.event.pageX) + "px")
  					  .style("top", (d3.event.pageY - 30) + "px");
			})
			.on('click', function(d){
				var url = entities[d.index].url;
				window.location = url;
			});

	function translate(x, y) {
		return 'translate(' + x*size + ',' + y*size +')';
	}

	function rotate(x) {
		return 'rotate(' + x +')';
	}

	function rad2deg(a) {
		return ( a / (2*Math.PI)) * 360;
	}
	
	function sign(n) {
		if(n >= 0.0)
			return 1;
		return -1;
	}
	
	function beautifyLabel(txt) {
		var newtext = txt;
		var maxlength = 12;
		if(newtext.length > maxlength)
			newtext = newtext.substring(0, maxlength) + '...';
			
		/*var words = txt.split(' ');
		for(var w=0; w<words.length; w++) {
			newtxt += '<tspan x="0" dy="20">' + words[w] + '</tspan>';
		}*/
		
		return newtext;
	}
	
	function getEntityIndex(uri) {
		for(var e=0; e<entities.length; e++) {
			if(entities[e].uri == uri) {
				return e;
			}
		}
		return -1;
	}

	function debug(d) {
		console.log(d);
	}
	
	function colorLuminance(hex, lum) {
		// Validate hex string.
		hex = String(hex).replace(/[^0-9a-f]/gi, '');
		if (hex.length < 6) {
			hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
		}
		lum = lum || 0;
		// Convert to decimal and change luminosity.
		var rgb = "#", c, i;
		for (i = 0; i < 3; i++) {
			c = parseInt(hex.substr(i*2,2), 16);
			c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
			rgb += ("00"+c).substr(c.length);
		}
		return rgb;
	}
}
