/**
 * Check for duplicate titles.
 *
 * @since 3.10.0
 *
 * @param {Object} $ A jQuery instance.
 * @param {Object} ajax A `wp.ajax` class used to perform `post` requests to
 *     `admin-ajax.php`.
 * @param {String} title The title to check for duplicates.
 * @param {Number} postId The current post id, excluded from the duplicates
 *     results.
 * @param {String} message The error message to display in case there are
 *     duplicates.
 * @param {Function} callback A callback function to call to deliver the
 *     results.
 */
const check = ( $, ajax, title, postId, message, callback ) => {
	// Use `wp.ajax` to post a request to find an existing entity with the
	// specified title.
	ajax.post( 'entity_by_title', { title: title } )
		.done( function( response ) {
			// Prepare the html code to show in the error div.
			const html = $.map( response.results, function( item ) {
				// If the item is the current post, ignore it.
				if ( item.id === postId ) {
					return '';
				}

				// Create the edit link.
				const editLink = response.edit_link.replace( '%d', item.id );

				// Return the html code.
				return message + '<a target="_blank" href="' + editLink + '">' +
					   item.title + '</a><br />';
			} ).join( '' ); // Join the html codes together.

			// Call the callback function.
			callback( html );
		} );
};

// Finally export the `check` function.
export default check;
