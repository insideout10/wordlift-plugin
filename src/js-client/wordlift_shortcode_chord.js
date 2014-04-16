
//CHORD INTRO:
//http://www.delimited.io/blog/2013/12/8/chord-diagrams-in-d3

//JSON MOCK-UP
var dataMock = [

	//first triple
	{
		//subject
		subject: {id:1, title:'Andrea', type:'person', description:'...'},
		//predicate
		predicate: {id:2, title:'knows', type:'friendship', description:'...'},
		//object
		object: {id:3, title:'David', type:'person', description:'...'}
	},
	
	//second triple
	{
		subject: {id:3, title:'David', type:'person', description:'...'},
		predicate: {id:2, title:'knows', type:'friendship', description:'...'},
		object: {id:1, title:'Andrea', type:'person', description:'...'}
	},
	
	//third triple
	{
		subject: {id:1, title:'Andrea', type:'person', description:'...'},
		predicate: {id:4, title:'works at', type:'worksat' , description:'...'},
		object: {id:8, title:'InsideOut', type:'organization', description:'...'}
	},
	
	//...
	{
		subject: {id:3, title:'David', type:'person' , description:'...'},
		predicate: {id:4, title:'works at', type:'worksat', description:'...'},
		object: {id:8, title:'InsideOut', type:'organization', description:'...'}
	},
	
	{
		subject: {id:5, title:'Hello world', type:'post' , description:'...'},
		predicate: {id:6, title:'written by', type:'authorship', description:'...'},
		object: {id:1, title:'Andrea', type:'person', description:'...'}
	},
	
	{
		subject: {id:7, title:'We rock!', type:'post' , description:'...'},
		predicate: {id:6, title:'written by', type:'authorship', description:'...'},
		object: {id:3, title:'David', type:'person' , description:'...'}
	}
	];

var matrix = [];
var entities = [];
var relations = [];

//enumerate entities and relations
for(var i=0; i<dataMock.length; i++) {
	if(entities.indexOf(dataMock[i].subject.id ) == -1)
		entities.push(dataMock[i].subject.id);
	if(entities.indexOf(dataMock[i].object.id ) == -1)
		entities.push(dataMock[i].object.id);
	relations.push({
		ids: dataMock[i].subject.id,
		idp: dataMock[i].predicate.id,
		ido: dataMock[i].object.id
	});
}

//build matrix
for(var i=0; i<entities.length; i++) {
	matrix.push([]);
	for(var j=0; j<entities.length; j++) {
		matrix[i].push(0);
	}
}
for(var i=0; i<relations.length; i++) {
	var x = entities.indexOf( relations[i].ids );
	var y = entities.indexOf( relations[i].ido );
	matrix[x][y] = 1;
	matrix[y][x] = 1;
	//anche la label!!!
}

var viz = d3.select('#wl_chord_widget').append('svg');
viz.attr('width', '100%').attr('height', '100%');

//getting dimensions in pixels
var width = parseInt(viz.style('width'));
var height = parseInt(viz.style('height'));
var innerRadius = width*0.35;
var outerRadius = width*0.4;
var arc = d3.svg.arc().innerRadius(innerRadius).outerRadius(outerRadius);

var chord = d3.layout.chord()
			.padding(0.5)
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
	.style('fill', 'red');
	
//draw entity labels
viz.selectAll('arcs_labels')
	.data(chord.groups)
	.enter()
	.append('text')
	.attr('transform', function(d){
		var alpha = d.startAngle - Math.PI/2 + ((d.endAngle - d.startAngle)/2);
		var r = outerRadius*1.1/width;
		var x = 0.5 + ( r * Math.cos(alpha) );
		var y = 0.5 + ( r * Math.sin(alpha) );
		
		alpha = rad2deg( alpha ) - 90;
		return translate(x, y);// + rotate(alpha);
	})
	.text( function(d){
		return retrieveTitle( entities[d.index] );
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

function retrieveTitle(id) {
	for(var i=0; i<dataMock.length; i++) {
		if(dataMock[i].subject.id == id)
			return dataMock[i].subject.title;
		if(dataMock[i].predicate.id == id)
			return dataMock[i].predicate.title;
		if(dataMock[i].object.id == id)
			return dataMock[i].object.title;
	}
}

function debug(d) {
	console.log(d);
}
