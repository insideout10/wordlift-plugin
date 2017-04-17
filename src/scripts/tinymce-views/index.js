/**
 * TinyMCE Views: Navigator View.
 *
 * @since 3.12.0
 */

/**
 * External dependencies
 */
import ReactDOM from 'react-dom';

/**
 * Internal dependencies
 */
import { wp, _, _wlSettings, _wlNavigator } from '../common/wordpress';

/**
 * Define the `wl_navigator` view.
 *
 * @since 3.12.0
 */
const view = _.extend( {}, {

	/**
	 * @inheritDoc
	 */
	getContent() {
		// Return some content in order to get the `render` function to call the
		// `bindNode` function. The content hereby displayed is WordPress'
		// standard loading placeholder.
		return '<div class="loading-placeholder">' +
			   '<div class="dashicons dashicons-admin-media"></div>' +
			   '<div class="wpview-loading"><ins></ins></div>' +
			   '</div>';
	},

	/**
	 * @inheritDoc
	 */
	bindNode( editor, node ) {
		wl.Navigator( node, _wlSettings.postId, _wlNavigator.l10n );
	},

	/**
	 * @inheritDoc
	 */
	unbindNode( editor, node ) {
		ReactDOM.unmountComponentAtNode( node );
	},

	/**
	 * @inheritDoc
	 */
	edit() {
		alert( _wlNavigator.l10n[ 'WordLift Navigator has no options.' ] );
	}
} );

// Finally register the `wl_navigator` view.
wp.mce.views.register( 'wl_navigator', view );
