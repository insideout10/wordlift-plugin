<?php
/*
Plugin Name: WordLift In-Depth
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: {version}
Author: InSideOut10
Author URI: http://www.insideout.io
License: GPLv2 or later
*/


function indepth_post_class( $classes, $class, $id ) {

	array_push( $classes, '" itemscope itemtype="http://schema.org/Article' );

	return $classes;
}

function interaction_count() {

	$comments_number = get_comments_number();
	
	return "<meta itemprop='interactionCount' content='UserComments:$comments_number' />";
}

function indepth_the_content( $content ) {

	return "<span itemprop='text'>$content</span>" . interaction_count();
}

function indepth_the_author( $author ) {

	return $author;
}

function indepth_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

	if ( empty( $html ) ) {
		return $html;
	}

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

function indepth_get_link_author() {
	$post         = get_post( get_the_ID() );
	$author_id    = $post->post_author;
	$google_plus  = get_the_author_meta( 'googleplus', $author_id) ;

	if ( ! empty( $google_plus ) ) {
		$display_name = esc_html( get_the_author_meta( 'display_name', $author_id) );

		return "<link href='http://plus.google.com/$google_plus' rel='author' title='$display_name' />";
	}

	return '';
}

function indepth_head() {

	echo indepth_get_link_author();
}

function indepth_start() {
	
	ob_start( 'indepth_ob_callback' );
}

function indepth_ob_callback( $content ) {
	// add the itemprop date published to time tags.
	$content = preg_replace(
		'/<time ([^>]*)>/i',
		'<time itemprop="datePublished" $1>',
		$content
	);

	$content = preg_replace(
		'/<img (.*)alt="logo"/i',
		'<img itemprop="logo" $1alt="logo"',
		$content
	);

	$content = preg_replace(
		'/<a class="logo([^>]*)>(.*)<\/a>/i',
		'<span itemscope itemtype="http://schema.org/Organization"><a itemprop="url" class="$1>$2</a></span>',
		$content
	);

	// avamasys
	$content = preg_replace(
		'/<div class="post-top clearfix">[^<]*<h1>([^<]*)<\/h1>/i',
		'<div class="post-top clearfix"><h1 itemprop="name">$1</h1>',
		$content
	);

	$content = preg_replace(
		'/<h1 class="entry-title">/i',
		'<h1 class="entry-title" itemprop="name">',
		$content
	);

	return $content;
}

function indepth_end() {

	ob_end_flush();
}

add_filter( 'post_class',  'indepth_post_class',  1000, 3 );
add_filter( 'the_content', 'indepth_the_content', 1000, 1 );
add_filter( 'the_author',  'indepth_the_author',  1000, 1 );
add_filter( 'post_thumbnail_html', 'indepth_post_thumbnail_html', 1000, 5 );

add_action( 'show_user_profile',        'indepth_add_extra_profile_fields');
add_action( 'edit_user_profile',        'indepth_add_extra_profile_fields');
add_action( 'personal_options_update',  'indepth_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'indepth_save_extra_user_profile_fields' );

add_action( 'wp_head',   'indepth_head',  -PHP_INT_MAX, 0 );
add_action( 'wp_head',   'indepth_start', PHP_INT_MAX,  0 );
add_action( 'wp_footer', 'indepth_end',   0, 0 );

?>