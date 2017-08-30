/**
 * Tabs.
 *
 * Create a tabbed UI.
 *
 * @since 3.11.0
 */

// Set a reference to jQuery.
const $ = jQuery;

/**
 * Create a tabbed UI on the element with the specified selector.
 *
 * @since 3.11.0
 * @param {string} selector The selector.
 * @constructor
 */
const Tabs = ( selector ) => {
	// Although in jQuery UI 1.12 it's possible to configure the css
	// classes, WP 4.2 uses jQuery 1.11.
	$( selector ).each( function() {
		//
		const $this = $( this );

		// Create the tabs and set the default active element.
		$this.tabs( { active: $this.data( 'active' ) } );
	} );
};

// Finally export `Tabs`.
export default Tabs;
