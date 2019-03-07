<?php
/**
 * Pages: Admin Settings / Search Keywords page.
 *
 * @since   3.20.0
 * @package Wordlift/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the existing terms.
$existing = get_terms( Wordlift_Search_Keyword_Taxonomy::TAXONOMY_NAME, array(
	'hide_empty'             => false,
	'fields'                 => 'id=>name',
	'update_term_meta_cache' => false,
) );

$log = Wordlift_Log_Service::get_logger( 'wordlift-admin-settings-search-keywords-page.php' );

// Save the settings.
if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

	// Get the requested terms.
	$requested = preg_split( '/\r\n|\r|\n/', filter_input( INPUT_POST, 'keywords' ) );

	// Remove terms.
	foreach ( $existing as $id => $name ) {
		if ( ! in_array( $name, $requested ) ) {
			$log->debug( "Deleting term $name..." );
			wp_delete_term( $id, Wordlift_Search_Keyword_Taxonomy::TAXONOMY_NAME );
		}
	}

	// Add terms.
	foreach ( $requested as $name ) {
		if ( ! empty( $name ) && ! in_array( $name, $existing ) ) {
			$log->debug( "Creating term $name..." );
			wp_insert_term( $name, Wordlift_Search_Keyword_Taxonomy::TAXONOMY_NAME );
		}
	}

	// Refresh the `$existing` variable.
	$existing = get_terms( Wordlift_Search_Keyword_Taxonomy::TAXONOMY_NAME, array(
		'hide_empty'             => false,
		'fields'                 => 'id=>name',
		'update_term_meta_cache' => false,
	) );

}
?>

<form method="post">
	<?php wp_nonce_field( 'update_search_keywords' );

	$terms = get_terms( Wordlift_Search_Keyword_Taxonomy::TAXONOMY_NAME );
	?>

    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label for="keywords"><?php echo esc_html( __( 'Add keywords to track', 'wordlift' ) ); ?></label>
            </th>
            <td><textarea name="keywords" id="keywords" rows="5"
                          cols="30"><?php echo implode( "\n", $existing ); ?></textarea>
                <p class="description"><?php echo esc_html( __( 'Type the list of search keywords, one per line.', 'wordlift' ) ); ?></p>
            </td>
        </tr>

        </tbody>
    </table>

    <p class="submit">
        <input type="submit" class="button button-primary"
               value="<?php echo esc_attr( __( 'Add', 'wordlift' ) ); ?>"/>
    </p>
</form>