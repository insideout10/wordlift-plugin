
//CHORD INTRO:
//http://www.delimited.io/blog/2013/12/8/chord-diagrams-in-d3

getChordData(wl_chord_params);

function getChordData(wl_chord_params){
	jQuery.post(
		wl_chord_params.ajax_url, 
		{
		    action: wl_chord_params.action,
		    post_id: wl_chord_params.post_id,
		    depth: wl_chord_params.depth
		},
		function(response){
			var data  = JSON.parse(response);
		    //console.log( data );
		    buildChord(data, wl_chord_params);
		}
	);
}

/*
console.log("initiating D3 ajax request");
d3.json( wl_chord_params.ajax_url )
	.header("Content-Type", "application/json")
	.post(
		JSON.stringify({action:"wl_ajax_chord_widget"}),
		function(error, response){ 
			console.log(response);
		}
	);
*/

function buildChord(dataMock, wl_chord_params) {
	var entities = dataMock.entities;
	var relations = dataMock.relations;
	
	//build matrix
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
		//anche la label!!!
	}
	
	var viz = d3.select( '#' + wl_chord_params.widget_id ).append('svg');
	viz.attr('width', '100%').attr('height', '100%');

	//getting dimensions in pixels
	var width = parseInt(viz.style('width'));
	var height = parseInt(viz.style('width'));
	var innerRadius = width*0.2;
	var outerRadius = width*0.25;
	var arc = d3.svg.arc().innerRadius(innerRadius).outerRadius(outerRadius);

	var chord = d3.layout.chord()
				.padding(0.3)
				.matrix(matrix);

	//draw relations
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

	//draw entities
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
	
	//draw entity labels
	viz.selectAll('arcs_labels')
		.data(chord.groups)
		.enter()
		.append('text')
		.attr('class', 'label')
		.html( function(d){
			return entities[d.index].label;
		})
		.attr('font-size', '12px')
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
			
			var r = (outerRadius + labelWidth)/width;
			var x = 0.5 + ( r * Math.cos(alpha) );
			var y = 0.5 + ( r * Math.sin(alpha) );
		
			
			return translate(x, y) + rotate( labelAngle );
		});
		
		viz.selectAll('.entity, .label')
			.on('mouseover', function(c) {
				d3.select(this).attr('cursor','pointer');
				viz.selectAll('.relation')
		            .filter(function(d, i) {
		            	return d.source.index == c.index || d.target.index == c.index;
		            })
		            .style("opacity", 0.8);
				})
			.on('mouseout', function(c) {
				viz.selectAll('.relation')
		            .filter(function(d, i) {
		            	return d.source.index == c.index || d.target.index == c.index;
		            })
		            .style("opacity", 0.2);
				})
			.on('click', function(d){
				var url = entities[d.index].url;
				window.location = url;
			});

	function translate(x, y) {
		return 'translate(' + x*width + ',' + y*height +')';
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
	
	/*
	function beautifyLabel(txt) {
		var newtxt = '';
		var words = txt.split(' ');
		for(var w=0; w<words.length; w++) {
			newtxt += '<tspan x="0" dy="20">' + words[w] + '</tspan>';
		}
		return newtxt;
	}
	*/
	
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
	
	// thanks to http://www.sitepoint.com/javascript-generate-lighter-darker-color/
	function colorLuminance(hex, lum) {
		// validate hex string
		hex = String(hex).replace(/[^0-9a-f]/gi, '');
		if (hex.length < 6) {
			hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
		}
		lum = lum || 0;
		// convert to decimal and change luminosity
		var rgb = "#", c, i;
		for (i = 0; i < 3; i++) {
			c = parseInt(hex.substr(i*2,2), 16);
			c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
			rgb += ("00"+c).substr(c.length);
		}
		return rgb;
	}
}
