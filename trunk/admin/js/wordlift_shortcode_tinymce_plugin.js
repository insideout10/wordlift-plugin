(
	function() {

		tinymce.create( 'tinymce.plugins.wl_shortcodes', {

			// Custom function for the Chord
			chordClick: function() {
				var chord_shortcode_text = '[wl_chord]';
				top.tinymce.activeEditor.execCommand( 'mceInsertContent', false, chord_shortcode_text );
			},

			// Custom function for the Timeline
			timelineClick: function() {
				var timeline_shortcode_text = '[wl_timeline]';
				top.tinymce.activeEditor.execCommand( 'mceInsertContent', false, timeline_shortcode_text );
			},

			geomapClick: function() {
				var geomap_shortcode_text = '[wl_geomap]';
				top.tinymce.activeEditor.execCommand( 'mceInsertContent', false, geomap_shortcode_text );
			},

			navigatorClick: function() {
				var navigator_shortcode_text = '[wl_navigator]';
				top.tinymce.activeEditor.execCommand( 'mceInsertContent', false, navigator_shortcode_text );
			},

			facetedSearchClick: function() {
				var faceted_search_shortcode_text = '[wl_faceted_search]';
				top.tinymce.activeEditor.execCommand( 'mceInsertContent', false, faceted_search_shortcode_text );
			},

			cloudClick: function() {
				var cloud_shortcode_text = '[wl_cloud]';
				top.tinymce.activeEditor.execCommand( 'mceInsertContent', false, cloud_shortcode_text );
			},

			/**
			 * @param {tinymce.Editor} ed Editor instance that the plugin is
			 *     initialized in.
			 * @param {string} url Absolute URL to where the plugin is located.
			 */
			init: function( ed, url ) {

				// Store url inside plugin (needed for the createControl()
				// function.
				tinymce.plugins.wl_shortcodes.pluginUrl = url;

				// Check tinyMCE version. If the version is 4 the menu will be
				// built here, otherwise in the createControl() function.
				if ( tinymce.majorVersion == 4 ) {

					var menu4 = [
						{
							text: 'Chord',
							onclick: this.chordClick,
						},
						{
							text: 'Timeline',
							onclick: this.timelineClick,
						},
						{
							text: 'GeoMap',
							onclick: this.geomapClick,
						},
						{
							text: 'Navigator',
							onclick: this.navigatorClick,
						},
						{
							text: 'Faceted Search',
							onclick: this.facetedSearchClick,
						},
						{
							text: 'Entity Cloud',
							onclick: this.cloudClick,
						},
					];

					var btn = ed.addButton( 'wl_shortcodes_menu', {
						type: 'menubutton',
						title: 'Widgets',
						text: 'Widgets',
						classes: 'wl-button',
						// Must define onclick to avoid error
						onclick: function() { /* OPEN YOURSELF ?? */
						},
						menu: menu4
					} );
				}
			},

			/**
			 * Creates control instances based in the incomming name. This
			 * method is normally not needed since the addButton method of the
			 * tinymce.Editor class is a more easy way of adding buttons but
			 * you sometimes need to create more complex controls like
			 * listboxes, split buttons etc then this method can be used to
			 * create those.
			 *
			 * @param {String} n Name of the control to create.
			 * @param {tinymce.ControlManager} cm Control manager to use
			 *     inorder to create new control.
			 * @return {tinymce.ui.Control} New control instance or null if no
			 *     control was created.
			 */
			createControl: function( n, cm ) {

				// Taking a reference to the plugin object.
				pluginRef = this;

				// Check tinyMCE version and build menu.
				if ( tinymce.majorVersion == 3 && n == 'wl_shortcodes_menu' ) {

					//var c = cm.createSplitButton('wl_shortcodes_menu',
					// {	//split button not working properly
					var c = cm.createMenuButton( 'wl_shortcodes_menu', {
						title: 'WordLift Widgets',
						image: tinymce.plugins.wl_shortcodes.pluginUrl + '/../images/svg/wl-logo-icon.svg?ver=3.12.0',
						// If SplitButton, must define onclick to avoid error
						//onclick: function(){ /* OPEN YOURSELF ?? */  }
					} );

					c.onRenderMenu.add( function( c, m ) {

						m.add( {
								   title: 'Chord',
								   onclick: pluginRef.chordClick,
							   } );

						m.add( {
								   title: 'Timeline',
								   onclick: pluginRef.timelineClick,
							   } );

						m.add( {
								   title: 'GeoMap',
								   onclick: pluginRef.geomapClick,
							   } );

						m.add( {
								   title: 'Navigator',
								   onclick: pluginRef.navigatorClick,
							   } );

						m.add( {
								   title: 'Faceted Search',
								   onclick: pluginRef.facetedSearchClick,
							   } );

						m.add( {
								   title: 'Entities Cloud',
								   onclick: pluginRef.cloudClick,
							   } );

					} );

					// Return the new menubutton instance
					return c;
				}
				return null;
			},

			/**
			 * Returns information about the plugin as a name/value array.
			 * The current keys are longname, author, authorurl, infourl and
			 * version.
			 *
			 * @return {Object} Name/value array containing information about
			 *     the plugin.
			 */
			getInfo: function() {
				return {
					longname: 'WordLift Shortcodes',
					author: 'WordLift',
					version: '3.12.1'
				};
			}
		} );

		// Register plugin
		tinymce.PluginManager.add( 'wl_shortcodes', tinymce.plugins.wl_shortcodes );
	}
)();
