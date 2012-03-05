
jQuery(window).ready(function($) {

	var WORDLIFT_20_REFRESH_INTERVAL = 10000;

	var EntityModel 		= Backbone.Model.extend({
	});

	var EntityCollection 	= Backbone.Collection.extend({
		model: EntityModel,
		url  : WORDLIFT_20_URL+'entities.php',
		parse: function(response) {
			return response.entities;
		},
		findByReference: function(reference) {
			return this.find(function(element) {
				return (reference === element.get('reference'));
			});
		}
	});

	var EntitiesView		= Backbone.View.extend({
		el: 		$('#entities-container'),
		template: 	_.template($('#entities-template').html()),
		render: 	function() {
			var container 			= $(this.el);
			var entitiesToAdd 		= [];
			var elementsToRemove 	= [];
			var me 					= this;

			container.children().each(function(index,element) {
				if (null == me.collection.findByReference($(element).data('reference')))
					elementsToRemove[elementsToRemove.length] = element;
			});

			this.collection.each(function(element, index, list) {
				if (0 == container.children('[data-reference="'+element.get('reference')+'"]').length) {
					entitiesToAdd[entitiesToAdd.length] = element.toJSON();
				}

			});

			console.log('Entities [remove:'+elementsToRemove.length+'][add:'+entitiesToAdd.length+'].');

			if (0 < elementsToRemove.length)
				container.isotope('remove', $(elementsToRemove) );

			if (0 < entitiesToAdd.length)
				container.isotope('insert', $( this.template({entities: entitiesToAdd})) );

			if (0 < elementsToRemove.length || 0 < entitiesToAdd.length)
				setTimeout( function() {
					container.isotope('reLayout', function() {} );
				}, 1000);

			return this;
		}
	});

	var entities 			= new EntityCollection;

	$('#entities-container').isotope({
		itemSelector : '.isotope-item',
		layoutMode : 'masonry'
	});

	var updateEntities = function() {
		entities.fetch({
			data: {id: WORDLIFT_20_POST_ID},
			success: function (collection, response) {
				var view = new EntitiesView({collection: collection});
				view.render();
			}
		});
	}

	updateEntities();
	setInterval(updateEntities, WORDLIFT_20_REFRESH_INTERVAL);

});