/**
 * Widgets: Navigator Widget.
 *
 * The Navigator Widget.
 *
 * @since 3.12
 */

/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';

/**
 * Internal dependencies
 */
import { $, post, _wlSettings, _wlNavigator } from '../common/wordpress';
import App from './components/App';
import LocalizationProvider from '../common/localization/LocalizationProvider';

const Navigator = ( selector, postId, l10n ) => {
	// Bail out if there are no elements where to attach.
	if ( 0 === $( selector ).length ) {
		return;
	}

	// Call the ajax action.
	// eslint-disable-next-line camelcase
	post( 'wl_navigator_get', { post_id: postId } ).done( function( data ) {
		// Get all the Navigator elements (marked up with the
		// `data-wl-navigator` attribute).
		$( selector ).each( function() {
			// Render the `React` tree.
			ReactDOM.render(
				<LocalizationProvider l10n={ l10n }>
					<App data={ data } />
				</LocalizationProvider>,
				this
			);
		} );
	} );
};

// Initialize the default Navigator instance.
Navigator( '[data-wl-navigator]', _wlSettings.postId, _wlNavigator.l10n );

// Finally export the `Navigator` function.
export default Navigator;
