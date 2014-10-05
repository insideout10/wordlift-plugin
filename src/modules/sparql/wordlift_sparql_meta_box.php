<?php

// Define locally used constants.
define( 'WL_SPARQL_QUERY_META_BOX_NONCE_ACTION', 'wl_sparql_query_meta_box' );
define( 'WL_SPARQL_QUERY_META_BOX_NONCE_NAME', 'wl_sparql_query_meta_box_nonce' );
define( 'WL_SPARQL_QUERY_META_BOX_FIELD_NAME', 'wl_sparql_query' );

/**
 * Adds a box to the SPARQL Query entity type.
 *
 * @since 3.0.0
 */
function wl_sparql_query_add_meta_box() {

	add_meta_box(
		'wl_sparql_query',
		__( 'SPARQL Query', 'wordlift' ),
		'wl_sparql_query_meta_box_callback',
		WL_SPARQL_QUERY_POST_TYPE,
		'normal', // The part of the page where the edit screen section should be shown.
		'high'    // The priority within the context where the boxes should show.
	);

}

add_action( 'add_meta_boxes', 'wl_sparql_query_add_meta_box' );


/**
 * This function is called by the *wl_sparql_query_add_meta_box* callback in order to display the metabox contents.
 *
 * @since 3.0.0
 *
 * @param object $post The post instance.
 */
function wl_sparql_query_meta_box_callback( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( WL_SPARQL_QUERY_META_BOX_NONCE_ACTION, WL_SPARQL_QUERY_META_BOX_NONCE_NAME );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value   = get_post_meta( $post->ID, WL_SPARQL_QUERY_META_KEY, true );
	$value_j = json_encode( $value );
	$value_h = esc_html( $value );

	// TODO: see if we can use Squebi
//    echo<<<EOF
//    <script type='text/javascript'>
//        SQUEBI = {
//            "configurable": false,
//            "selectService": "http://wordpress391.localhost/wp-admin/admin-ajax.php?action=wl_sparql_select",
//            "updateService": "http://wordpress391.localhost/wp-admin/admin-ajax.php?action=wl_sparql_update",
//            "samples": [ $value_j ]
//        };
//    </script>
//    <div id="squebi">
//        <div style="position: relative" ng-controller="QueryCtrl" class="jumbotron">
//                <a href="https://github.com/tkurz/squebi"><img style="position: absolute; top: 0; right: 0; border: 0;z-index: 20" src="https://camo.githubusercontent.com/652c5b9acfaddf3a9c326fa6bde407b87f7be0f4/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6f72616e67655f6666373630302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_orange_ff7600.png"></a>
//
//                <div id="query-container">
//                    <textarea id="input" name="wl_sparql_query" ng-model="query" ui-codemirror="editorOptions"></textarea>
//                </div>
//                <a class="btn btn-lg btn-success btn-query" ng-click="triggerQuery()">
//                    <i class="fa fa-play-circle"></i> Run
//                </a>
//                <a id="redlink" href="http://redlink.co" style="position: absolute;right:40px;bottom:98px">
//                    <div style="color:#888;padding-right: 5px;display: inline; font-size: 16px">powered by</div><img title="Redlink" style="width: 25px;margin-top:-5px;" src="R-logo.png">
//                </a>
//            </div>
//
//    </div>
//EOF;

	echo '<label for="wl_sparql_query">';
	_e( 'Edit the SPARQL Query', 'wordlift' );
	echo '</label><br/>';
	echo '<textarea style="width: 100%; height: 200px;" id="' . WL_SPARQL_QUERY_META_BOX_FIELD_NAME . '" name="' . WL_SPARQL_QUERY_META_BOX_FIELD_NAME . '" ng-model="query">' . $value_h . '</textarea>';
//    echo '<input type="text" id="myplugin_new_field" name="myplugin_new_field" value="' . esc_attr( $value ) . '" size="25" />';

//    add_action( 'wp_print_scripts', 'wl_sparql_meta_box_print_scripts' );
}

//function wl_sparql_meta_box_print_scripts() {
//
//    if ( ! is_admin() ) return; // only for admin area
//
//    $requirejs_url = plugins_url( 'sparql/squebi/bower_components/requirejs/require.js', __FILE__ );
//    $main_url      = plugins_url( 'sparql/squebi/main', __FILE__ );
//
//    echo "<script data-main='$main_url' src='$requirejs_url'></script>";
//
//}


/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function wl_sparql_query_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST[ WL_SPARQL_QUERY_META_BOX_NONCE_NAME ] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST[ WL_SPARQL_QUERY_META_BOX_NONCE_NAME ], WL_SPARQL_QUERY_META_BOX_NONCE_ACTION ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	/* OK, it's safe for us to save the data now. */

	// Make sure that it is set.
	if ( ! isset( $_POST[ WL_SPARQL_QUERY_META_BOX_FIELD_NAME ] ) ) {
		return;
	}

	// Update the meta field in the database.
	update_post_meta( $post_id, WL_SPARQL_QUERY_META_KEY, $_POST[ WL_SPARQL_QUERY_META_BOX_FIELD_NAME ] );

}

add_action( 'save_post', 'wl_sparql_query_save_meta_box_data' );