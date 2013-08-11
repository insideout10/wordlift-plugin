<?php
/*
Plugin Name: WordLift In-Depth
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: {version}
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/


function indepth_post_class( $classes, $class, $id ) {

	array_push( $classes, '" itemscope itemtype="http://schema.org/Article' );

	return $classes;
}

function indepth_the_title( $title, $id ) {

	return "<span itemprop='name'>$title</span>";
}

function indepth_the_content( $content ) {

	return "<span itemprop='text'>$content</span>";
}

function indepth_the_author( $author ) {

	return $author;
	// return ( is_single() ? "by $author" : $author );
}

function indepth_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

	$url = wp_get_attachment_url( $post_thumbnail_id );
	return "<a href='$url' itemprop='image'>$html</a>";
}

function indepth_add_extra_profile_fields( $user ) {

	$googleplus = esc_attr( get_the_author_meta( 'googleplus', $user->ID ) );

	echo <<<EOF

		<h3>WordLift In-Depth</h3>
		<table class="form-table">
		<tbody>
			<tr>
				<th><label for="googleplus">Google+</label></th>
				<td><input type="text" name="googleplus" id="googleplus" value="$googleplus" class="regular-text code"></td>
			</tr>
		</tbody>
		</table>
EOF;
}

function indepth_save_extra_user_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }

	update_user_meta( $user_id, 'googleplus', $_POST['googleplus'] );
}

function indepth_start() {

	$post         = get_post( get_the_ID() );
	$author_id    = $post->post_author;
	$google_plus  = get_the_author_meta( 'googleplus', $author_id) ;

	if ( ! empty( $google_plus ) ) {
		$display_name = esc_html( get_the_author_meta( 'display_name', $author_id) );

		echo "<link href='http://plus.google.com/$google_plus?rel=author'>$display_name</link>";
	}
	
	ob_start();
}

function indepth_end() {
	$content = ob_get_contents(); ob_clean();

	// add the itemprop date published to time tags.
	$content = preg_replace( '/<time ([^>]*)>/i', '<time itemprop="datePublished" $1>', $content );

	// force display of authorship.
	// $content = preg_replace( '/<span class="author vcard">/i', '<span style="display:inline;" class="author vcard">', $content );

	echo $content;

	ob_end_flush();
}

add_filter( 'post_class',  'indepth_post_class',  1000, 3 );
add_filter( 'the_title',   'indepth_the_title',   1000, 2 );
add_filter( 'the_content', 'indepth_the_content', 1000, 1 );
add_filter( 'the_author',  'indepth_the_author',  1000, 1 );
add_filter( 'post_thumbnail_html', 'indepth_post_thumbnail_html', 1000, 5 );

add_action( 'show_user_profile',        'indepth_add_extra_profile_fields');
add_action( 'edit_user_profile',        'indepth_add_extra_profile_fields');
add_action( 'personal_options_update',  'indepth_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'indepth_save_extra_user_profile_fields' );

add_action( 'wp_head',   'indepth_start', 1,    1 );
add_action( 'wp_footer', 'indepth_end',   9999, 1 );

?>