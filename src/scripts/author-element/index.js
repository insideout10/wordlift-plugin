/**
 * Entry: wordlift-admin-user-profile-page.js
 *
 * @since 3.14.0
 */

/**
 * Internal dependencies
 */
import './styles/index.scss';
import Select2 from '../common/select2';

/**
 * UI interactions on the WordLift Settings page
 *
 * @since 3.14.0
 */
(
	( $ ) => {
		$( function() {
			// Create the Select2.
			Select2( '.wl-select2-element',
					 {
//						 containerCssClass: 'wl-select2-container'
//						 dropdownCssClass: 'wl-select2-dropdown'
					 } );
		} );
	}
)( jQuery );
