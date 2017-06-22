/**
 * Entry: wordlift-admin-settings-page.js
 */

/**
 * Internal dependencies
 */
import './styles/index.scss';
import KeyValidator from '../common/key-validator';
import MediaUploader from '../common/media-uploader';
import Tabs from '../common/tabs';
import Select2 from '../common/select2';

/**
 * UI interactions on the WordLift Settings page
 *
 * @since 3.11.0
 */
(
	( $, settings ) => {
		$( function() {
			// Attach the WL key validator to the `#wl-key` element.
			KeyValidator( '#wl-key' );

			// Attach the Media Uploader to the #wl-publisher-logo
			MediaUploader( '#wl-publisher-media-uploader', {
				title: settings.l10n.logo_selection_title,
				button: settings.l10n.logo_selection_button,
				multiple: false,
				library: { type: 'image' },
			}, ( attachment ) => {
				// Set the selected image as the preview image
				$( '#wl-publisher-media-uploader-preview' ).attr( 'src', attachment.url ).show();

				// Set the logo id.
				$( '#wl-publisher-media-uploader-id' ).val( attachment.id );
			} );

			// Create the tabs.
			Tabs( '.wl-tabs-element' );

			// Create the Select2.
			Select2( '.wl-select2-element',
					 {
						 containerCssClass: 'wl-admin-settings-page-select2',
						 dropdownCssClass: 'wl-admin-settings-page-select2'
					 } );
		} );
	}
)( jQuery, wlSettings );
