<?php
/*
Plugin Name: WordLift Prefixes
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: 3.0.0-SNAPSHOT
Author: InsideOut10
Author URI: http://www.insideout.io
License: APL
*/

require_once( 'class-wl-prefixes-list-table.php' );

/**
 * Add the specified prefix / namespace.
 *
 * @since 3.0.0
 *
 * @param string $prefix The prefix
 * @param string $namespace The namespace
 */
function wl_prefixes_add( $prefix, $namespace ) {

	// Get the items, ensure that the current $prefix is not there.
	$items = wl_prefixes_delete( $prefix );

	array_push( $items, array( 'prefix' => $prefix, 'namespace' => $namespace ) );
	update_option( 'wl_option_prefixes', $items );

}


/**
 * Delete the specified prefix.
 *
 * @see 3.0.0
 *
 * @param string $prefix The prefix to delete.
 *
 * @return array The updated prefixes array.
 */
function wl_prefixes_delete( $prefix ) {

	$items = get_option( 'wl_option_prefixes', array() );

	// Ensure $items is an array.
	if ( ! is_array( $items ) ) {
		$items = array();
	}

	foreach ( $items as $key => $item ) {
		if ( $prefix === $item['prefix'] ) {
			unset ( $items[ $key ] );
		}
	}
	update_option( 'wl_option_prefixes', $items );

	return $items;

}


/**
 * Get the list of prefixes.
 *
 * @since 3.0.0
 *
 * @return array An array of prefixes, each made of a *prefix* and *namespace* key-values.
 */
function wl_prefixes_list() {

	// If the parameter is false, default prefixes have never been installed.
	if ( false === ( $prefixes = get_option( 'wl_option_prefixes' ) ) ) {

		$prefixes = array(
			array( 'prefix' => 'geo', 'namespace' => 'http://www.w3.org/2003/01/geo/wgs84_pos#' ),
			array( 'prefix' => 'dct', 'namespace' => 'http://purl.org/dc/terms/' ),
			array( 'prefix' => 'rdfs', 'namespace' => 'http://www.w3.org/2000/01/rdf-schema#' ),
			array( 'prefix' => 'owl', 'namespace' => 'http://www.w3.org/2002/07/owl#' ),
			array( 'prefix' => 'schema', 'namespace' => 'http://schema.org/' ),
                        array( 'prefix' => 'xsd', 'namespace' => 'http://www.w3.org/2001/XMLSchema#' )
		);
		add_option( 'wl_option_prefixes', $prefixes );
	}

	return $prefixes;

}

/**
 * Compacts the provided URI by replacing the namespaces with prefixes.
 *
 * @since 3.0.0
 *
 * @param string $uri The uri to compact
 *
 * @return string The compacted uri.
 */
function wl_prefixes_compact( $uri ) {

	foreach ( wl_prefixes_list() as $prefix ) {
		if ( 0 === strpos( $uri, $prefix['namespace'] ) ) {
			// Return the URI with the prefix.
			return $prefix['prefix'] . ':' . substr( $uri, strlen( $prefix['namespace'] ) );
		}
	}

	// Return the normal URI.
	return $uri;

}


/**
 * Expands the provided URI by replacing the prefixes with namespaces.
 *
 * @since 3.0.0
 *
 * @param string $uri The uri to expand
 *
 * @return string The expanded uri.
 */
function wl_prefixes_expand( $uri ) {

	foreach ( wl_prefixes_list() as $prefix ) {
		if ( 0 === strpos( $uri, $prefix['prefix'] . ':' ) ) {
			// Return the URI with the prefix.
			return $prefix['namespace'] . substr( $uri, strlen( $prefix['prefix'] ) + 1 );
		}
	}

	// Return the normal URI.
	return $uri;

}

/**
 * Get the namespace for a prefix.
 *
 * @since 3.0.0
 *
 * @param string $prefix
 *
 * @return string|false The namespace or false if not found.
 */
function wl_prefixes_get( $prefix ) {

	// Get the namespace.
	foreach ( wl_prefixes_list() as $item ) {
		if ( $prefix === $item['prefix'] ) {
			return $item['namespace'];
		}
	}

	// Return false if the prefix is not found.
	return false;

}

/**
 * This function is called by the *wl_admin_menu* hook which is raised when WordLift builds the admin_menu.
 *
 * @since 3.0.0
 *
 * @param string $parent_slug The parent slug for the menu.
 * @param string $capability The required capability to access the page.
 */
function wl_prefixes_admin_menu( $parent_slug, $capability ) {

	// see http://codex.wordpress.org/Function_Reference/add_submenu_page
	add_submenu_page(
		$parent_slug, // The parent menu slug, provided by the calling hook.
		__( 'Prefixes', 'wordlift' ),  // page title
		__( 'Prefixes', 'wordlift' ),  // menu title
		$capability,                   // The required capability, provided by the calling hook.
		'wl_prefixes_admin_menu',      // the menu slug
		'wl_prefixes_admin_menu_callback' // the menu callback for displaying the page content
	);

}

add_action( 'wl_admin_menu', 'wl_prefixes_admin_menu', 10, 2 );

/**
 * Displays the page content.
 *
 * @since 3.0.0
 *
 * @param boolean $display_page_title If true, prints out the page title.
 */
function wl_prefixes_admin_menu_callback( $display_page_title = true ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$page_title = __( 'Prefixes', 'wordlift' );

	echo '<div class="wrap">';

	if ( $display_page_title ) {
		echo "<h2>$page_title</h2>";
	}

	echo <<<EOF
    <br class="clear">

    <div id="col-container">

        <div id="col-right">
            <div class="col-wrap">
EOF;

	// Create the List Table.
	$wl_prefixes_table = new WL_Prefixes_List_Table();

	// See if we have an action.
	switch ( $wl_prefixes_table->current_action() ) {

		// Add the prefix using the provided data.
		case 'add-prefix':

			check_admin_referer( 'add-prefix', '_wpnonce_add-prefix' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; uh?' ) );
			}

			wl_prefixes_add( $_POST['prefix'], $_POST['namespace'] );
			break;

		// Delete a prefix.
		case 'delete':

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; uh?' ) );
			}

			wl_prefixes_delete( $_GET['prefix'] );

			break;

		case 'bulk-delete':

			$prefixes = (array) $_REQUEST['prefix'];
			foreach ( $prefixes as $prefix ) {
				wl_prefixes_delete( $prefix );
			}

			break;
	}


	$wl_prefixes_table->prepare_items();
	$wl_prefixes_table->display();

	?>
	</div>
	</div><!-- /col-right -->

	<div id="col-left">
		<div class="col-wrap">

			<div class="form-wrap">
				<h3>Add New Prefix</h3>

				<form id="addprefix" method="post" class="validate">
					<input type="hidden" name="action" value="add-prefix">
					<?php wp_nonce_field( 'add-prefix', '_wpnonce_add-prefix' ); ?>

					<div class="form-field form-required">
						<label for="prefix"><?php _e( 'Prefix', 'wordlift' ); ?></label>
						<input name="prefix" id="prefix" type="text" value="" size="40" aria-required="true">

						<p><?php __( 'The namespace prefix.', 'wordlift' ); ?></p>
					</div>
					<div class="form-field">
						<label for="namespace"><?php _e( 'Namespace', 'wordlift' ); ?></label>
						<input name="namespace" id="namespace" type="text" value="" size="128">

						<p><?php __( 'The namespace URL.', 'wordlift' ); ?></p>
					</div>

					<?php
					submit_button( __( 'Add New Prefix', 'wordlift' ) );
					?>

				</form>
			</div>
		</div>
	</div><!-- /col-left -->

	</div><!-- /col-container -->
	</div>

<?php
}