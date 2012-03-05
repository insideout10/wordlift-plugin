
jQuery(window).ready(function($) {

	var EntityModel 		= Backbone.Model.extend({
	});

	var EntityCollection 	= Backbone.Collection.extend({
		model: EntityModel,
		url  : WORDLIFT_20_URL+'entities.php',
		parse: function(response) {
			return response.entities;
		}
	});

	var EntitiesView		= Backbone.View.extend({
		el: 		$('#entities-container'),
		template: 	_.template($('#entities-template').html()),
		render: 	function() {

			var container = $(this.el);
			container.isotope('destroy');
			container.empty();

			container.isotope({
				itemSelector : '.isotope-item',
				layoutMode : 'masonry'
			});

			var entitiesToAdd = [];
			this.collection.each(function(element, index, list) {
				if (0 < container.children('div[data-reference='+element.reference+']').length) {
					console.log('entity found, not adding.');
				} else {
					console.log('entity not found, adding.');
					entitiesToAdd[entitiesToAdd.length] = element;
				}
			});

			container.isotope('insert', $( this.template({entities: this.collection.toJSON()})) );

			setTimeout( function() {
				container.isotope('reLayout', function() {console.log('relayout');} );
			}, 1000);

			return this;
		}
	});

	var entities 			= new EntityCollection;

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
	setInterval(updateEntities, 30000);

});