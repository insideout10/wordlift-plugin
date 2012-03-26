var io;

jQuery(window).ready(function($) {

	console.log('WordLift initializing...');

	io = {
		insideout : {
			wordlift : {
				models 	: {},
				views 	: {},
				services: {}
			}
		}
	};

	io.insideout.wordlift.models 	= {
		entityCollection: Backbone.Collection.extend({
			model: Backbone.Model.extend({}),
			url  : WORDLIFT_20_URL+'api/entities.php',
			parse: function(response) {
				return response.entities;
			},
			findByReference: function(reference) {
				return this.find(function(element) {
					return (reference === element.get('about'));
				});
			}
		})
	};

	io.insideout.wordlift.views 	= {
		entitiesView	: Backbone.View.extend({
			el: 		$('#entities-container'),
			template: 	_.template( ( 0 < $('#entities-template').length ? $('#entities-template').html() : '') ),
			render: 	function() {
				var container 			= $(this.el);
				var entitiesToAdd 		= [];
				var elementsToRemove 	= [];
				var me 					= this;

				container.children().each(function(index,element) {
					if (null == me.collection.findByReference($(element).data('about')))
						elementsToRemove[elementsToRemove.length] = element;
				});

				this.collection.each(function(element, index, list) {
					if (0 == container.children('[data-about="'+element.get('about')+'"]').length) {
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

				$('.entity-toolbar > .accepted')
					.unbind('click')
					.click(function(eventObject) {
					var me  		= $(eventObject.target);
					var entityId 	= me.parents('.entity-item').data('post-id');

					$.ajax({
						url 	: WORDLIFT_20_URL+'accept.php',
						data 	: {
							'post_id'	: WORDLIFT_20_POST_ID,
							'entity_id'	: entityId
						},
						success	: function(data, textStatus, jqXHR) {
							me.parents('.entity-item')
								.removeClass('rejected')
								.addClass('accepted');
							me.parent().siblings('.rejected')
								.removeClass('selected')
								.addClass('deselected');
							me.parent()
								.removeClass('deselected')
								.addClass('selected');
						},
						error 	: function(jqXHR, textStatus, errorThrown) {
						}
					});
				});

				$('.entity-toolbar > .rejected')
					.unbind('click')
					.click(function(eventObject) {
					var me  		= $(eventObject.target);
					var entityId 	= me.parents('.entity-item').data('post-id');

					$.ajax({
						url 	: WORDLIFT_20_URL+'reject.php',
						data 	: {
							'post_id'	: WORDLIFT_20_POST_ID,
							'entity_id'	: entityId
						},
						success	: function(data, textStatus, jqXHR) {
							me.parents('.entity-item')
								.removeClass('accepted')
								.addClass('rejected');
							me.parent().siblings('.accepted')
								.removeClass('selected')
								.addClass('deselected');
							me.parent()
								.removeClass('deselected')
								.addClass('selected');
						},
						error 	: function(jqXHR, textStatus, errorThrown) {
						}
					});
				});

				return this;
			}
		})
	};

	(function($,$wl) {

		io.insideout.wordlift.services 	= {
			entityService : {
				getAll 		: function(container) {

					container.isotope({
						itemSelector : '.isotope-item',
						layoutMode : 'masonry'
					});

					var limit    	= 10;
					var offset 		= 0;
					var entities 	= new $wl.models.entityCollection;

					this.fetch(container, entities, limit, offset);

				},
				fetch 		: function(container, entities, limit, offset) {
					var me  = this;
					entities.fetch({
						data: {limit: limit, offset: offset},
						success: function (collection, response) {
							var view = new $wl.views.entitiesView({collection: collection});
							container.isotope('insert', $( view.template({entities: view.collection.toJSON()}) ) );

							if (offset < 100)
								me.fetch(container, entities, limit, offset+limit);
						}
					});
				}
			},
			loadEntities	: function() {

				var WORDLIFT_20_REFRESH_INTERVAL = 10000;

				var entities 			= new $wl.models.entityCollection;

				$('#entities-container').isotope({
					itemSelector : '.isotope-item',
					layoutMode : 'masonry'
				});

				var updateEntities = function() {
					entities.fetch({
						data: {id: WORDLIFT_20_POST_ID},
						success: function (collection, response) {
							var view = new $wl.views.entitiesView({collection: collection});
							view.render();
						}
					});
				}

				updateEntities();
				setInterval(updateEntities, WORDLIFT_20_REFRESH_INTERVAL);
			}
		};

	})(jQuery,io.insideout.wordlift);

});