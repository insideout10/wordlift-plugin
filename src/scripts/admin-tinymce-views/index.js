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
import Navigator from '../navigator';

/**
 * Define the `wl_navigator` view.
 *
 * @since 3.12.0
 */
const view = _.extend( {}, {
	/**
	 * @inheritDoc
	 */
	getContent: function() {
		// Something is required here otherwise wp.media won't bind our node.
		return _wlNavigator.l10n[ 'Loading Preview...' ];
	},

	/**
	 * @inheritDoc
	 */
	bindNode: function( editor, node ) {
		Navigator( node, _wlSettings.postId, _wlNavigator.l10n );
	},

	/**
	 * @inheritDoc
	 */
	unbindNode: function( editor, node ) {
		ReactDOM.unmountComponentAtNode( node );
	}
} );

// Finally register the `wl_navigator` view.
wp.mce.views.register( 'wl_navigator', view );
