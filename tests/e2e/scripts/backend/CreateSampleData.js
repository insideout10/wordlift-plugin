/**
 * Calls the `wl_sample_data_create` AJAX end-point in order to generate sample
 * data.
 *
 * Please note that the browser session must be already authenticated.
 *
 * @since 3.12.0
 *
 * @constructor
 */
const CreateSampleData = () => {
	// Open the login page.
	browser.url( '/wp-admin/admin-ajax.php?action=wl_sample_data_create' );
	browser.pause( 3000 );
};

// Finally export the function.
export default CreateSampleData;
