(function(global,$,Backbone) {

	var wordlift = {
		VERSION: '0.0.1'
	};

	wordlift.client 	= {
		setupUI : function() {
			var container = $( "#entities" );

			container.isotope({
				itemSelector : '.isotope-item',
				layoutMode : 'masonry'
			});

			setTimeout( function() {
				container.isotope('reLayout', function() {} );
			}, 5000);

		}
	};

	wordlift.domain 	= {};

	wordlift.domain.EntitiesCollection = Backbone.Collection.extend({
		model: Backbone.Model.extend({}),
		url  : WORDLIFT_20_URL+'api/entities.php',
		parse: function(response) {
			return response.entities;
		},
		findByReference: function(reference) {
			return this.find(function(element) {
				return (reference === element.get('reference'));
			});
		}
	});

	wordlift.views 		= {};

	wordlift.views.EntitiesViews = Backbone.View.extend({
		render 	: 	function() {
			$(this.el).append( this.options.template({entities: this.collection.toJSON()}) );
			return this;
		}
	});

	wordlift.services 	= {};

	wordlift.services.entityService = {

		loading : false, 

		// load all the entities.
		getAll 	: function() {
			var entities = new wordlift.domain.EntitiesCollection;

			this.get(entities, 10, 0);
		},

		// load the specified number of elements.
		get  	: function(entities, limit, offset) {
			console.log('get [limit:'+limit+'][offset:'+offset+']');

			var me  	= this;

			// if (true == me.loading)
			// 	console.log('exiting [loading:'+me.loading+']');

			// me.loading  = true;

			me.trigger('loading', entities, limit, offset);

			// save this for the next convience method.
			me.entities = entities;
			me.limit 	= limit;

			entities.fetch({
				data: {limit: limit, offset: offset},
				success: function (collection, response) {
					console.log('loading offset ['+offset+']');

					me.offset 	= offset;
					me.total = response.total;
					me.trigger('loaded', collection, response, me);
				}
			});
		},

		// convenience method.
		next 	: function() {
			this.get(this.entities, this.limit, this.offset+this.limit);
		},

		hasNext : function() {
			return (this.total > this.offset+this.limit);
		}

	};

	// extend the entityService with support for events.
	_.extend(wordlift.services.entityService, Backbone.Events);

	if (global.wordlift) {
		throw new Error('wordlift has already been defined');
	} else {
		global.wordlift = wordlift;
	}

})((typeof window === 'undefined' ? this : window),jQuery,Backbone);


jQuery(window).ready(function($) {

	var limit    = 50,
		offset   = 0,
		entities = new wordlift.domain.EntitiesCollection;

	// jQuery extension.
	$.fn.relativeOffset = function() {
		var $element 		= $(this);
		var $container 		= $element.parent();

		var elementHeight 	= $element.outerHeight();
		var containerHeight = $container.innerHeight();
		var relativeTop 	= ($element.offset().top - $container.offset().top);
		var relativeBottom 	= (relativeTop + elementHeight - containerHeight);			

		return {'top': relativeTop, 'bottom': relativeBottom};
	};

	$('#entities-container').scroll(function(eventObject){
		var $element  		= $(eventObject.target).children().last();

		if (100 > $element.relativeOffset().bottom && true == wordlift.services.entityService.hasNext())
			wordlift.services.entityService.next();
	});

	wordlift.services.entityService.on('loading', function() {
		$('#loading-wheel').css('display','block');
	});

	wordlift.services.entityService.on('loaded', function(collection) {
		var entitiesViews = new wordlift.views.EntitiesViews({
			el 			: $('#entities-container'),
			template 	: _.template( (0 < $('#entities-template').length ? $('#entities-template').html() : '') ),
			collection 	: collection  
		});

		entitiesViews.render();

		$('#loading-wheel').css('display','none');
	});

	wordlift.services.entityService.get(entities, limit, offset);
	wordlift.services.entityService.offset = offset;

});

// wordlift.view
// initialize the isotope.


// wordlift.controller
// load all the entities.

// entities are too many, load paginated.

// get the first batch, and read the total number of entities.

// trigger an event that the batch has been loaded.

// load the next batch, get the total number of entities.

// trigger an event that the batch has been loaded.

// continue loading until the offset+limit < total number of entities.

// wordlift.view
// trap batch events and display them in the isotope.