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
		stanbol : 'http://ziodave.dyndns.org:8080/engines/',
		entityhub : 'http://ziodave.dyndns.org:8080/entityhub/sites/entity?id='
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
		analyze : function(content) {
			this._eventSource.trigger('loading');
			var stanbol = this;
			$
					.ajax({
						async : true,
						type : "POST",
						success : function(data) {
							var rdf = $.ioio.ikswp.namespaces.applyToRdf($
									.rdf().load(data));
							rdf
									.where('?entity a fise:EntityAnnotation')
									.each(
											function(i) {
												var e = $.ioio.ikswp
														.entityAnnotation(
																this.entity.value,
																$
																		.rdf()
																		.load(
																				rdf
																						.about(
																								'<'
																										+ this.entity.value
																										+ '>')
																						.dump()));
												e.populate();

												for ( var i = 0; i < e.references._property.length; i++) {
													var reference = e.references._property[i].value
															.toString();
													if ($
															.inArray(
																	reference,
																	stanbol._references) > -1)
														return;

													stanbol._references
															.push(reference);
												}

												var s = $.ioio.ikswp
														.snippetAnnotation(e);
												var d = $.ioio.ikswp
														.dualRendererAdapter(
																e,
																s,
																stanbol._resultElement);
												e.load();

												stanbol._entities.push(e);
											});
							stanbol._eventSource.trigger('loaded');

						},
						error : function() {
							console.log('An error has occured.');
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
	 * Creates a new entityAnnotation class
	 * 
	 * @param {string}
	 *            about The ID of the entity
	 * @param {rdf}
	 *            rdf The RDF fragment of the entity
	 * 
	 * @class A class that represents a basic entityAnnotation from Stanbol
	 * @property {string} about The ID of the entity
	 * @property {rdf} rdf The rdf fragment containing information about the
	 *           entity
	 * @property {property} labels A multi-valued property with the labels for
	 *           the entity
	 * @property {property} labels A multi-valued property with the external
	 *           references for the entity
	 * @property {property} types A multi-valued property with the entity types
	 */
	$.ioio.ikswp.entityAnnotation = function(about, rdf) {
		return new $.ioio.ikswp.entityAnnotation.fn.init(about, rdf);
	}

	$.ioio.ikswp.entityAnnotation.fn = $.ioio.ikswp.entityAnnotation.prototype = {
		_eventSource : undefined,
		about : undefined,
		rdf : undefined,
		labels : undefined,
		references : undefined,
		types : undefined,
		/**
		 * Initializes an instance of the class
		 * 
		 * @param {string}
		 *            about The ID of the entity
		 * @param {rdf}
		 *            rdf The RDF fragment of the entity
		 */
		init : function(about, rdf) {
			this.about = about;
			this.rdf = $.ioio.ikswp.namespaces.applyToRdf(rdf);

			// create a fake element to manage events
			this._eventSource = $('<div id="event_source_' + this.about
					+ '"></div>');

			return this;
		},
		/**
		 * Binds the function to the local events.
		 * 
		 * @param {string}
		 *            event The name of the event the client is subscribing to.
		 * @param {function}
		 *            callback The method to call when the event is triggered.
		 */
		bind : function(event, callback) {
			this._eventSource.bind(event, callback);
		},
		/**
		 * Populates properties such as labels, references and types from the
		 * available data.
		 */
		populate : function() {
			this.labels = $.ioio.ikswp.property(this.rdf.where(
					'?subject fise:entity-label ?label').map(function() {
				return this.label;
			}));
			this.references = $.ioio.ikswp.property(this.rdf.where(
					'?subject fise:entity-reference ?reference').map(
					function() {
						return this.reference;
					}));
			this.types = $.ioio.ikswp.property(this.rdf.where(
					'?subject a ?type').map(function() {
				return this.type;
			}));

			this._eventSource.trigger('change');
		},
		load : function() {
			this.populate();
			if (this.references === undefined)
				return;

			var entityAnnotation = this;
			$.each(this.references._property, function() {
				entityAnnotation._load(this.value);
			});
		},
		_load : function(reference) {
			var entityAnnotation = this;
			$.ajax({
				async : true,
				type : "POST",
				success : function(data) {
					try {
						entityAnnotation.rdf.load(data, {
							namespaces : $.ioio.ikswp.namespaces.prefixes
						});
					} catch (e) {
						console.log('[entityAnnotation][_load] ' + e);
					}
					entityAnnotation.populate();
				},
				url : $.ioio.ikswp.settings.proxy,
				data : {
					proxy_url : $.ioio.ikswp.settings.entityhub + reference,
					verb : "GET",
					format : "application/rdf+xml"
				}
			});
		}
	}

	$.ioio.ikswp.entityAnnotation.fn.init.prototype = $.ioio.ikswp.entityAnnotation.prototype;

	/**
	 * Creates a new snippetAnnotation instance.
	 * 
	 * @class A snippetAnnotation represents an annotation compatible with
	 *        Google Rich Snippets and is founded on the entityAnnotation.
	 * @param {entityAnnotation}
	 *            entityAnnotation An instance of an entityAnnotation class.
	 */
	$.ioio.ikswp.snippetAnnotation = function(entityAnnotation) {
		return new $.ioio.ikswp.snippetAnnotation.fn.init(entityAnnotation);
	}

	$.ioio.ikswp.snippetAnnotation.fn = $.ioio.ikswp.snippetAnnotation.prototype = {
		_eventSource : undefined,
		_entityAnnotation : undefined,
		renderer : undefined,
		type : undefined,
		/**
		 * Creates a new instance of a snippetAnnotation class.
		 * 
		 * @param {entityAnnotation}
		 *            entityAnnotation An instance of an entityAnnotation class.
		 */
		init : function(entityAnnotation) {
			this._entityAnnotation = entityAnnotation;
			this._eventSource = $('<div id="snippet-annotation-'
					+ this._entityAnnotation.about + '"></div>');
			this.type = this._type();

			// update the snippetAnnotation data when the base entityAnnotation
			// changes
			var snippetAnnotation = this;
			this._entityAnnotation.bind('change', function() {
				// set the snippet type
				snippetAnnotation.type = snippetAnnotation._type();
				// set the snippet renderer for rendering calls on the document
				snippetAnnotation.renderer = snippetAnnotation._renderer();
				// reload snippet-specific properties
				snippetAnnotation._properties();
				// inform clients that the snippet has changed - typically the
				// dualRenderer will show updated information on-screen
				snippetAnnotation._eventSource.trigger('change');
			});

			return this;
		},
		bind : function(event, callback) {
			this._eventSource.bind(event, callback);
		},
		_type : function() {
			if (this._entityAnnotation.types === undefined)
				return undefined;

			for ( var i = 0; i < this._entityAnnotation.types._property.length; i++) {
				var type = this._entityAnnotation.types._property[i].value
						.toString();
				if (type === 'http://dbpedia.org/ontology/Place')
					return 'Place';
				if (type === 'http://dbpedia.org/ontology/Country')
					return 'Place';
				if (type === 'http://dbpedia.org/class/yago/Locations')
					return 'Place';
				if (type === 'http://dbpedia.org/ontology/Organisation')
					return 'Organisation';
				if (type === 'http://dbpedia.org/class/yago/EnergyMinisters')
					return 'Organisation';
				if (type === 'http://dbpedia.org/class/yago/PoliticalPartiesInTheUnitedStates')
					return 'Organisation';
				if (type === 'http://dbpedia.org/class/yago/InternationalOrganizationsOfEurope')
					return 'Organisation';
				if (type === 'http://dbpedia.org/class/yago/LivingPeople')
					return 'Person';
			}

			return undefined;
		},
		_renderer : function() {
			if (this.type === 'Place')
				return $.ioio.ikswp.placeSnippetRenderer(
						this._entityAnnotation, this);
			if (this.type === 'Organisation')
				return $.ioio.ikswp.organisationSnippetRenderer(
						this._entityAnnotation, this);
			if (this.type === 'Person')
				return $.ioio.ikswp.personSnippetRenderer(
						this._entityAnnotation, this);

			return undefined;
		},
		_properties : function() {
			if (this.type === undefined)
				return;

			// save this for reference
			var snippetAnnotation = this;

			if (this.type === 'Person') {
				var rules = $.ioio.ikswp.namespaces.applyToRules($.rdf
						.ruleset());

				rules.add('?subject dbprop:name ?name',
						'?subject google:name ?name').add(
						'?subject dbpedia:thumbnail ?thumbnail',
						'?subject google:photo ?thumbnail').add(
						'?subject foaf:depiction ?depiction',
						'?subject google:photo ?depiction').add(
						'?subject foaf:homepage ?homepage',
						'?subject google:url ?homepage').add(
						'?subject dbprop:currentTeam ?team',
						'?subject google:affiliation ?team').add(
						'?subject dbprop:caption ?caption',
						'?subject google:role ?caption').add(
						'?subject dbpedia:orderInOffice ?office',
						'?subject google:role ?office').add(
						'?subject dbprop:office ?office',
						'?subject google:affiliation ?office').add(
						'?subject dbprop:occupation ?occupation',
						'?subject google:role ?occupation').add(
						'?subject dbprop:office ?office',
						'?subject google:affiliation ?office');
				;

				// the properties for this snippet type
				var properties = {
					names : 'google:name',
					photos : 'google:photo',
					urls : 'google:url',
					comments : 'rdfs:comment',
					affiliations : 'google:affiliation',
					roles : 'google:role'
				};

				// clear existing properties
				$.each(properties, function() {
					snippetAnnotation[this] = undefined;
				});

				var rdf = this._entityAnnotation.rdf.reason(rules);

				for (p in properties) {
					snippetAnnotation[p] = $.ioio.ikswp.property(rdf.where(
							'?subject ' + properties[p] + ' ?' + p).group(p)
							.map(function() {
								return this[p];
							}));
				}

				return;
			}

			if (this.type === 'Organisation') {
				var rules = $.ioio.ikswp.namespaces.applyToRules($.rdf
						.ruleset());

				rules.add('?subject foaf:name ?name',
						'?subject google:name ?name').add(
						'?subject dbprop:companyName ?name',
						'?subject google:name ?name').add(
						'?subject dbprop:name ?name',
						'?subject google:name ?name').add(
						'?subject rdfs:label ?label',
						'?subject google:name ?label').add(
						'?subject foaf:homepage ?homepage',
						'?subject google:url ?homepage').add(
						'?subject dbpedia:thumbnail ?thumbnail',
						'?subject google:photo ?thumbnail');

				// the properties for this snippet type
				var properties = {
					names : 'google:name',
					photos : 'google:photo',
					urls : 'google:url',
					comments : 'rdfs:comment'
				};

				// clear existing properties
				$.each(properties, function() {
					snippetAnnotation[this] = undefined;
				});

				var rdf = this._entityAnnotation.rdf.reason(rules);

				for (p in properties) {
					snippetAnnotation[p] = $.ioio.ikswp.property(rdf.where(
							'?subject ' + properties[p] + ' ?' + p).group(p)
							.map(function() {
								return this[p];
							}));
				}
			}

			if (this.type === 'Place') {
				var rules = $.ioio.ikswp.namespaces.applyToRules($.rdf
						.ruleset());

				rules.add('?subject foaf:name ?name',
						'?subject google:name ?name').add(
						'?subject rdfs:label ?label',
						'?subject google:name ?label').add(
						'?subject foaf:homepage ?homepage',
						'?subject google:url ?homepage').add(
						'?subject dbpedia:thumbnail ?thumbnail',
						'?subject google:photo ?thumbnail');

				// the properties for this snippet type
				var properties = {
					names : 'google:name',
					photos : 'google:photo',
					urls : 'google:url',
					comments : 'rdfs:comment'
				};

				// clear existing properties
				$.each(properties, function() {
					snippetAnnotation[this] = undefined;
				});

				var rdf = this._entityAnnotation.rdf.reason(rules);
				for (p in properties) {
					snippetAnnotation[p] = $.ioio.ikswp.property(rdf.where(
							'?subject ' + properties[p] + ' ?' + p).group(p)
							.map(function() {
								return this[p];
							}));
				}
			}
		}
	}

	$.ioio.ikswp.snippetAnnotation.fn.init.prototype = $.ioio.ikswp.snippetAnnotation.prototype;

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
