/**
 * @fileOverview This file has functions related to IKS for WordPress plug-in
 * @author <a href="mailto:david@insideout.io">David Riccitelli</a>
 * @version 0.0.1
 */
(function($) {

	/**
	 * @namespace The base namespace for ioio
	 */
	$.ioio = {
		/**
		 * @namespace Holds functionality for the IKS for WordPress plug-in
		 */
		ikswp : {}
	}

	/**
	 * @class Default settings
	 * @property
	 */
	$.ioio.ikswp.settings = {
		proxy : '../wp-content/plugins/wordlift/utils/proxy/proxy.php',
		stanbol : 'http://stanbol.insideout.io/engines/',
		entityhub : 'http://stanbol.insideout.io/entityhub/sites/entity?id='
	}

	/**
	 * @class Namespaces management class.
	 * @property {array} prefixes An array of prefixes and namespaces
	 */
	$.ioio.ikswp.namespaces = {
		prefixes : {
			rdf : 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
			rdfs : 'http://www.w3.org/2000/01/rdf-schema#',
			google : 'http://rdf.data-vocabulary.org/#',
			nie : 'http://www.semanticdesktop.org/ontologies/2007/01/19/nie#',
			owl : 'http://www.w3.org/2002/07/owl#',
			dc : 'http://purl.org/dc/terms/',
			nfo : 'http://www.semanticdesktop.org/ontologies/2007/03/22/nfo#',
			dbpedia : 'http://dbpedia.org/ontology/',
			dbprop : 'http://dbpedia.org/property/',
			yago : 'http://dbpedia.org/class/yago/',
			fise : 'http://fise.iks-project.eu/ontology/',
			foaf : 'http://xmlns.com/foaf/0.1/'
		},
		/**
		 * Applies the prefixes to the provided rdf instance.
		 * 
		 * @param {$.rdf}
		 *            rdf An instance of a jQuery.rdfquery rdf object
		 */
		applyToRdf : function(rdf) {
			for (prefix in this.prefixes) {
				rdf.prefix(prefix, this.prefixes[prefix]);
			}
			;
			return rdf;
		},
		/**
		 * Applies the prefixes to the provided rules instance.
		 * 
		 * @param {ruleset}
		 *            rules An instance of a jQuery.rdfquery rules object
		 */
		applyToRules : function(rules) {
			return this.applyToRdf(rules);
		}
	}

	/**
	 * This class retrieves entities from Stanbol.
	 * 
	 * @param {element}
	 *            resultElement The element that will be filled with all the
	 *            results.
	 */
	$.ioio.ikswp.stanbol = function(resultElement) {
		return new $.ioio.ikswp.stanbol.fn.init(resultElement);
	}

	$.ioio.ikswp.stanbol.fn = $.ioio.ikswp.stanbol.prototype = {
		_eventSource : undefined,
		_resultElement : undefined,
		_entities : undefined,
		_references : undefined,
		init : function(resultElement) {
			this._eventSource = $('<div></div>');
			this._resultElement = resultElement;
			this._entities = [];
			this._references = [];

			return this;
		},
		fragment : function(rdf, entity) {
			return $.ioio.ikswp.namespaces.applyToRdf($.rdf().load(
					rdf.about('<' + entity + '>').dump()));
		},
		analyze : function(content) {
			this._eventSource.trigger('loading');
			var stanbol = this;
			$
					.ajax({
						timeout : 300000,
						async : true,
						type : "POST",
						success : function(data) {
							debug.group('$.ioio.ikswp.stanbol.analyze');

							var rdf = $.ioio.ikswp.namespaces.applyToRdf($
									.rdf().load(data));

							// create the entities and their renderers
							var types = [ 'Person', 'Organization', 'Place' ];
							for ( var t in types) {
								debug.debug('type: ' + types[t]);
								rdf
										.where('?entity a google:' + types[t])
										.each(
												function() {
													$.ioio.ikswp.renderers
															.renderer(
																	stanbol._resultElement,
																	new $.ioio.ikswp.entities.entity(
																			stanbol
																					.fragment(
																							rdf,
																							this.entity.value),
																			types[t]));
												});
							}

							stanbol._eventSource.trigger('loaded');

							debug.groupEnd();

						},
						error : function(jqXHR, textStatus, errorThrown) {
							debug.error('An error has occured [textStatus:'
									+ textStatus + '][errorThrown:'
									+ errorThrown + '.');
							alert('The remote server returned an error: '
									+ errorThrown);
							stanbol._eventSource.trigger('loaded');
						},
						url : $.ioio.ikswp.settings.proxy,
						data : {
							proxy_url : $.ioio.ikswp.settings.stanbol,
							/*
							 * the following option is available:
							 * "http://localhost:8080/engines/",
							 * http://localhost:10088/enel/rdfa/sample_stanbol.rdf
							 * "http://localhost:10088/enel/rdfa/sample_stanbol.002.rdf"
							 */
							content : content,
							verb : "POST",
							format : "application/rdf+xml"
						}
					});
		},
		bind : function(event, callback) {
			this._eventSource.bind(event, callback);
		}
	}

	$.ioio.ikswp.stanbol.fn.init.prototype = $.ioio.ikswp.stanbol.prototype;

	/**
	 * Entities and Models
	 */
	$.ioio.ikswp.entities = {
		models : {
			Person : {
				names : 'google:name',
				photos : 'google:photo',
				urls : 'google:url',
				comments : 'rdfs:comment',
				affiliations : 'google:affiliation',
				roles : 'google:role'
			},
			Organization : {
				names : 'google:name',
				photos : 'google:photo',
				urls : 'google:url',
				comments : 'rdfs:comment'
			},
			Place : {
				names : 'google:name',
				photos : 'google:photo',
				urls : 'google:url',
				comments : 'rdfs:comment'
			},
			Unknown : {
				names : 'google:name'
			}
		}
	};

	/**
	 * Entity
	 */
	$.ioio.ikswp.entities.entity = function(entity, type) {
		debug.debug('creating a new entity of type [' + type + ']');
		return new $.ioio.ikswp.entities.entity.fn.init(entity, type);
	};
	$.ioio.ikswp.entities.entity.fn = $.ioio.ikswp.entities.entity.prototype = {
		_entity : undefined,
		_type : undefined,
		_model : undefined,
		_rdf : undefined,
		init : function(entity, type) {

			debug.group('[ioio.ikswp.entities.entity:init]');
			debug.debug('entity: ', entity);
			debug.debug('type: ' + type);

			this._entity = entity;
			this._type = type;
			this._model = ($.ioio.ikswp.entities.models[this._type] ? $.ioio.ikswp.entities.models[this._type]
					: $.ioio.ikswp.entities.models['Unknown']);

			debug.debug('model: ' + this._model);

			for ( var p in this._model) {
				debug.debug('property: ' + p);
				this[p] = $.ioio.ikswp.property(this._entity.where(
						'?subject ' + this._model[p] + ' ?' + p).group(p).map(
						function() {
							return this[p];
						}));
			}

			debug.groupEnd();
		}
	}
	$.ioio.ikswp.entities.entity.fn.init.prototype = $.ioio.ikswp.entities.entity.prototype;

	/**
	 * Renderers
	 */
	$.ioio.ikswp.renderers = {}

	/**
	 * Renderer
	 */
	$.ioio.ikswp.renderers.renderer = function(element, entity) {
		return new $.ioio.ikswp.renderers.renderer.fn.init(element, entity);
	};
	$.ioio.ikswp.renderers.renderer.fn = $.ioio.ikswp.renderers.renderer.prototype = {
		_element : undefined,
		_entity : undefined,
		init : function(element, entity) {
			debug.group('[ioio.ikswp.renderer:init]');
			debug.debug('element: ', element);
			debug.debug('entity: ', entity);

			this._element = $('<div class="entity"></div>').appendTo(element);
			this._entity = entity;

			this.render(this._element);

			debug.groupEnd();
		},
		render : function(element) {
			element.empty();

			var itemscope = $('<div itemscope itemtype="http://data-vocabulary.org/'
					+ this._entity._type + '"></div>');

			if (this._entity.photos.valueAt(0) !== undefined) {
				itemscope.append('<img itemprop="photo" class="photo" src="'
						+ this._entity.photos.valueAt(0) + '" />');
			}
			if (this._entity.names !== undefined) {
				var names = $('<div class="name"></div>').appendTo(itemscope);
				$.each(this._entity.names._property, function() {
					names.append('<div itemprop="name">' + this.value
							+ '</div>');
				});

			}
			if (this._entity.affiliations !== undefined) {
				itemscope
						.append('<div itemprop="affiliation" class="affiliation">'
								+ this._entity.affiliations.join(' and ')
								+ '</div>');
			}
			if (this._entity.roles !== undefined) {
				itemscope.append('<div itemprop="role" class="roles">'
						+ this._entity.roles.join('<br/>') + '</div>');
			}
			if (this._entity.urls !== undefined) {
				var urls = $('<div class="url"></div>').appendTo(itemscope);
				$.each(this._entity.urls._property, function() {
					urls.append('<a itemprop="url" href="' + this.value + '">'
							+ this.value + '</a>');
				});
			}

			// finally append the itemscope to the container element
			element.append(itemscope);
		}
	};
	$.ioio.ikswp.renderers.renderer.fn.init.prototype = $.ioio.ikswp.renderers.renderer.prototype;

	/**
	 * Creates a new dualRendererAdapter which renders either the base
	 * entityAnnotation or the snippetAnnotation (when it becomes available).
	 * 
	 * @class A dualRendererAdapter is an adapter that displayes in a document
	 *        element the entityAnnotation information. At the same time it
	 *        listens for events from the snippetAnnotation so that, when it
	 *        becomes available, information from the snippetAnnotation are
	 *        shown.
	 * @param {entityAnnotation}
	 *            entityAnnotation An instance of an entityAnnotation.
	 * @param {snippetAnnotation}
	 *            snippetAnnotation An instance of a snippetAnnotation.
	 * @param {element}
	 *            element An instance of a DOM element.
	 */
	$.ioio.ikswp.dualRendererAdapter = function(entityAnnotation,
			snippetAnnotation, element) {
		return new $.ioio.ikswp.dualRendererAdapter.fn.init(entityAnnotation,
				snippetAnnotation, element);
	}

	$.ioio.ikswp.dualRendererAdapter.fn = $.ioio.ikswp.dualRendererAdapter.prototype = {
		_entityAnnotation : undefined,
		_snippetAnnotation : undefined,
		_element : undefined,
		/**
		 * @param {entityAnnotation}
		 *            entityAnnotation An instance of an entityAnnotation.
		 * @param {snippetAnnotation}
		 *            snippetAnnotation An instance of a snippetAnnotation.
		 * @param {element}
		 *            element An instance of a DOM element.
		 */
		init : function(entityAnnotation, snippetAnnotation, element) {
			this._entityAnnotation = entityAnnotation;
			this._snippetAnnotation = snippetAnnotation;
			this._element = $('<div class="entity"></div>').appendTo(element);

			// subscribe to the entity change events
			var dualRendererAdapter = this;
			this._entityAnnotation.bind('change', function(args) {
				dualRendererAdapter.render();
			});
			this._snippetAnnotation.bind('change', function(args) {
				dualRendererAdapter.render();
			});

			this.render();

			return this;
		},
		/**
		 * Renders the current data
		 */
		render : function() {
			this._snippetAnnotation.renderer !== undefined ? this
					.renderSnippet() : this.renderEntity();
		},
		renderEntity : function() {
			this._element.empty().append(
					'<div class="about" id="' + this._entityAnnotation.about
							+ '">' + this._entityAnnotation.about + '</div>');
			if (this._entityAnnotation.labels !== undefined) {
				this._element.append('<div class="labels">'
						+ this._entityAnnotation.labels.join(' aka ')
						+ '</div>');
			}
			if (this._entityAnnotation.references !== undefined) {
				this._element.append('<div class="references">'
						+ this._entityAnnotation.references.join('<br/>')
						+ '</div>');
			}
			if (this._entityAnnotation.types !== undefined) {
				this._element
						.append('<div class="types">'
								+ this._entityAnnotation.types.join('<br/>')
								+ '</div>');
			}

			this._element.hide('slow');
		},
		renderSnippet : function() {
			this._snippetAnnotation.renderer.render(this._element);
			this._element.show('slow');
		}

	}

	$.ioio.ikswp.dualRendererAdapter.fn.init.prototype = $.ioio.ikswp.dualRendererAdapter.prototype;

	/**
	 * A class that renders a Place snippet.
	 * 
	 * @class A class that renders a Place snippet.
	 */
	$.ioio.ikswp.placeSnippetRenderer = function(entityAnnotation,
			snippetAnnotation) {
		return new $.ioio.ikswp.placeSnippetRenderer.fn.init(entityAnnotation,
				snippetAnnotation);
	}

	$.ioio.ikswp.placeSnippetRenderer.fn = $.ioio.ikswp.placeSnippetRenderer.prototype = {
		_entityAnnotation : undefined,
		_snippetAnnotation : undefined,
		init : function(entityAnnotation, snippetAnnotation) {
			this._entityAnnotation = entityAnnotation;
			this._snippetAnnotation = snippetAnnotation;

			return this;
		},
		render : function(element) {
			element.empty();

			var itemscope = $('<div itemscope itemtype="http://data-vocabulary.org/Place"></div>');

			var labels = this._entityAnnotation.labels.join(' aka ');

			if (this._snippetAnnotation.photos.valueAt(0) !== undefined) {
				itemscope.append('<img itemprop="photo" class="photo" alt="'
						+ labels + '" src="'
						+ this._snippetAnnotation.photos.valueAt(0) + '" />');
			}
			if (this._entityAnnotation.labels !== undefined) {
				itemscope.append('<div itemprop="name" class="labels">'
						+ labels + '</div>');
			}
			if (this._snippetAnnotation.urls !== undefined) {
				var urls = $('<div class="url"></div>').appendTo(itemscope);
				$.each(this._snippetAnnotation.urls._property, function() {
					urls.append('<a itemprop="url" href="' + this.value + '">'
							+ this.value + '</a>');
				});
			}

			/*
			 * if (this._snippetAnnotation.comments !== undefined) {
			 * element.append('<div class="comments">' +
			 * this._snippetAnnotation.comments.join('<br/>') + '</div>'); }
			 */

			// finally append the itemscope to the container element
			element.append(itemscope);

		}
	}

	$.ioio.ikswp.placeSnippetRenderer.fn.init.prototype = $.ioio.ikswp.placeSnippetRenderer.prototype;

	/**
	 * A class that renders a Organisation snippet.
	 * 
	 * @class A class that renders a Organisation snippet.
	 */
	$.ioio.ikswp.organisationSnippetRenderer = function(entityAnnotation,
			snippetAnnotation) {
		return new $.ioio.ikswp.organisationSnippetRenderer.fn.init(
				entityAnnotation, snippetAnnotation);
	}

	$.ioio.ikswp.organisationSnippetRenderer.fn = $.ioio.ikswp.organisationSnippetRenderer.prototype = {
		_entityAnnotation : undefined,
		_snippetAnnotation : undefined,
		init : function(entityAnnotation, snippetAnnotation) {
			this._entityAnnotation = entityAnnotation;
			this._snippetAnnotation = snippetAnnotation;

			return this;
		},
		render : function(element) {
			element.empty();

			var itemscope = $('<div itemscope itemtype="http://data-vocabulary.org/Organization"></div>');

			var labels = this._entityAnnotation.labels.join(' aka ');

			if (this._snippetAnnotation.photos.valueAt(0) !== undefined) {
				itemscope.append('<img itemprop="photo" class="photo" alt="'
						+ labels + '" src="'
						+ this._snippetAnnotation.photos.valueAt(0) + '" />');
			}
			if (this._entityAnnotation.labels !== undefined) {
				itemscope.append('<div itemprop="name" class="labels">'
						+ labels + '</div>');
			}
			if (this._snippetAnnotation.urls !== undefined) {
				var urls = $('<div class="url"></div>').appendTo(itemscope);
				$.each(this._snippetAnnotation.urls._property, function() {
					urls.append('<a itemprop="url" href="' + this.value + '">'
							+ this.value + '</a>');
				});
			}

			/*
			 * if (this._snippetAnnotation.comments !== undefined) {
			 * element.append('<div class="comments">' +
			 * this._snippetAnnotation.comments.join('<br/>') + '</div>'); }
			 */

			// finally append the itemscope to the container element
			element.append(itemscope);
		}
	}

	$.ioio.ikswp.organisationSnippetRenderer.fn.init.prototype = $.ioio.ikswp.organisationSnippetRenderer.prototype;

	/**
	 * A class that renders a Person snippet.
	 * 
	 * @class A class that renders a Person snippet.
	 */
	$.ioio.ikswp.personSnippetRenderer = function(entityAnnotation,
			snippetAnnotation) {
		return new $.ioio.ikswp.personSnippetRenderer.fn.init(entityAnnotation,
				snippetAnnotation);
	}

	$.ioio.ikswp.personSnippetRenderer.fn = $.ioio.ikswp.personSnippetRenderer.prototype = {
		_entityAnnotation : undefined,
		_snippetAnnotation : undefined,
		init : function(entityAnnotation, snippetAnnotation) {
			this._entityAnnotation = entityAnnotation;
			this._snippetAnnotation = snippetAnnotation;

			return this;
		},
		render : function(element) {
			element.empty();

			var itemscope = $('<div itemscope itemtype="http://data-vocabulary.org/Person"></div>');

			var labels = this._entityAnnotation.labels.join(' aka ');

			if (this._snippetAnnotation.photos.valueAt(0) !== undefined) {
				itemscope.append('<img itemprop="photo" class="photo" alt="'
						+ labels + '" src="'
						+ this._snippetAnnotation.photos.valueAt(0) + '" />');
			}
			if (this._entityAnnotation.labels !== undefined) {
				itemscope.append('<div itemprop="name" class="labels">'
						+ labels + '</div>');
			}
			if (this._snippetAnnotation.affiliations !== undefined) {
				itemscope
						.append('<div itemprop="affiliation" class="affiliation">'
								+ this._snippetAnnotation.affiliations
										.join(' and ') + '</div>');
			}
			if (this._snippetAnnotation.roles !== undefined) {
				itemscope.append('<div itemprop="role" class="roles">'
						+ this._snippetAnnotation.roles.join('<br/>')
						+ '</div>');
			}
			if (this._snippetAnnotation.urls !== undefined) {
				var urls = $('<div class="url"></div>').appendTo(itemscope);
				$.each(this._snippetAnnotation.urls._property, function() {
					urls.append('<a itemprop="url" href="' + this.value + '">'
							+ this.value + '</a>');
				});
			}

			/*
			 * if (this._snippetAnnotation.comments !== undefined) {
			 * element.append('<div class="comments">' +
			 * this._snippetAnnotation.comments.join('<br/>') + '</div>'); }
			 */

			// finally append the itemscope to the container element
			element.append(itemscope);

		}
	}

	$.ioio.ikswp.personSnippetRenderer.fn.init.prototype = $.ioio.ikswp.personSnippetRenderer.prototype;

	/**
	 * A class representing a multi-valued property.
	 * 
	 * @class A multi-valued property.
	 * @param {object}
	 *            property A property instance that has one ore more value
	 *            attributes.
	 */
	$.ioio.ikswp.property = function(property) {
		return new $.ioio.ikswp.property.fn.init(property);
	}

	$.ioio.ikswp.property.fn = $.ioio.ikswp.property.prototype = {
		_property : undefined,
		/**
		 * Creates an instance of a class representing a multi-valued property.
		 * 
		 * @param {object}
		 *            property A property instance that has one ore more value
		 *            attributes.
		 */
		init : function(property) {
			this._property = property;

			return this;
		},
		/**
		 * Joins together the values of the properties.
		 * 
		 * @param {string}
		 *            joinWord The word to use when joining the values.
		 */
		join : function(joinWord) {
			var s = '';
			$.each(this._property, function() {
				s += (s === '' ? '' : joinWord) + this.value;
			});
			return s;
		},
		valueAt : function(index) {
			if (index > this._property.length
					|| this._property[index] === undefined)
				return undefined;

			return this._property[index].value;
		}
	}

	$.ioio.ikswp.property.fn.init.prototype = $.ioio.ikswp.property.prototype;

})(jQuery);
