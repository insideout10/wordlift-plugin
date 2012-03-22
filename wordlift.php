<?php
/*
Plugin Name: WordLift 2.0
Plugin URI: http://wordlift.insideout.io
Description: WordLift 2.0
Version: 0.2.0
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

require_once('constants.php');
require_once('private/config/wordlift.php');
require_once('log4php.php');

require_once('classes/WordLiftSetup.php');
require_once('classes/TextJobRequest.php');
require_once('classes/EnhancerJobService.php');
require_once('classes/WordLift.php');
require_once('classes/EntityService.php');
require_once('classes/SlugService.php');
require_once('classes/EntitiesBoxService.php');
require_once('classes/EntityBoxService.php');

function display_the_content($content){
	global $logger, $entity_service, $slug_service;

	$post = $GLOBALS['post'];

	// we only add entities to posts.
	if ('post' != $post->post_type) return $content;

	$entities 	= $entity_service->get_accepted_entities_by_post_id( $post->ID );

	$terms_body .= '<div style="width: 100%;" id="entities">';
	foreach ($entities as $entity) {

		$terms_body .= '<div class="isotope-item entity-item itemscope itemtype="http://schema.org/'.$entity->type.'">';
		if (NULL != $entity->properties['thumbnail']) {
			$terms_body .= '<div><img style="width: 100%;" alt="" onerror="jQuery(this).remove();" src="'.$entity->properties['thumbnail'][0].'" /></div>';
		}

		$terms_body .= '<div class="entity-caption-outer">';
		$terms_body .= '<div class="entity-caption-inner"><a itemprop="name" href="'.get_permalink( $entity->post_id ).'">'.$entity->text.'</a></div>';
		$terms_body .= '</div>';

		// if (NULL != $entity->reference) 	$terms_body .= '<span itemprop="url">'.$entity->reference.'</span>';
		// if (NULL != $entity->properties['description']) 	$terms_body .= '<span itemprop="description">'.$entity->properties['description'][0].'</span>';
		// if (NULL != $entity->properties['thumbnail']) 	$terms_body .= '<img itemprop="thumbnail" src="'.$entity->properties['thumbnail'][0].'" />';

		$terms_body .= '</div>';
	}

	$terms_body .= '</div>';
	$terms_body .= '<script type="text/javascript">jQuery(window).ready(function(){wordlift.client.setupUI();});</script>';

	return $content . $terms_body . '<br/><br/>'; ;
}

function create_admin_menu() {
	global $logger;

	$logger->debug('create_admin_menu');

	// add_menu_page('custom menu title', 'custom menu', 'administrator', 'myplugin/myplugin-index.php', '', plugins_url('myplugin/images/icon.png'), 6);
	$admin_menu_slug			= WORDLIFT_20_PLUGIN_DIR.'admin_menu.php';
	$admin_menu_add_new_slug	= WORDLIFT_20_PLUGIN_DIR.'admin_menu_add_new.php';

	add_menu_page( 'Entities', 'Entities', 'edit_posts', $admin_menu_slug, 'create_admin', plugins_url('images/semantic-box-14x16.png', WORDLIFT_20_ROOT_PATH), 6);
	add_submenu_page( $admin_menu_slug, 'Entities', 'Add New', 'edit_posts', $admin_menu_add_new_slug, 'create_admin_add_new');
	// add_submenu_page('options-general.php', 'wpautop-control', 'wpautop control', 'manage_options', 'wpautop-control-menu', 'wpautop_control_options');

}

function create_admin() {
	global $logger, $entity_service;

	$logger->debug('create_admin');

?>
	<style type="text/css">
		.entity-row {
			min-height: 40px;
			border-bottom: solid 1px gray;
			overflow: auto;
			cursor: pointer;
		}

		.entity-row:hover {
			background: lightgray;
		}
		
		#loading-wheel {
			display: none;
			position: absolute;
			top: 0;
			width: 100%;
			height: 100%;
			background: white;
			opacity: 0.75;
			-moz-opacity: 0.75;
			filter:alpha(opacity=75);
		}

		#loading-wheel img {
			position: absolute;
			top: 50%;
			left: 50%;
			margin-top: -150px;
			margin-left: -150px;
		}

	</style>

	<script type="text/javascript">
		var editEntity = function(element) {
			(function($) {
				var $element 	= $(element);
				var postId 		= $element.data('post-id');

				window.location.href = 'post.php?post='+postId+'&action=edit';
			})(jQuery);
		};
	</script>

	<script type="text/template" id="entities-template">
		<% _.each(entities, function(entity) { %>
			<div class="entity-row" data-post-id="<%= entity.post_id %>" onclick="editEntity(this);">
				<div style="width: 80px;">
					<% if (entity.properties['thumbnail']) { %>
						<img style="width: 100%; float: left;" alt="" onerror="this.src=WORDLIFT_20_NO_PREVIEW_URL;" src="<%= entity.properties['thumbnail'] %>" />
					<% } else { %>
						<img style="width: 100%; float: left;" alt="" src="<%= WORDLIFT_20_NO_PREVIEW_URL %>" />
					<% } %>
				</div>
				<div style="width: 200px; float: left; padding: 4px;">
					<div style="font-weight: bold; font-size: 1.2em;"><a style="text-decoration: none; color: black;" href="post.php?post=<%= entity.post_id %>&action=edit"><%= entity.text %></a></div>
				</div>
				<div style="width: 150px; float: left; padding: 4px;">
					<div><%= entity.type %></div>
				</div>
				<div style="width: 150px; float: left; padding: 4px;">
					<div>Posts: <%= (entity.properties['posts'] ? entity.properties['posts'].length : '0') %></div>
				</div>
			</div>
		<% }); %>
	</script>

	<div class="wrap">
		<h2><img src="../wp-content/plugins/wordlift/images/sw-cube.png" style="width: 32px;" /> Entities <a href="" class="add-new-h2">Add New</a> </h2>

		<ul class='subsubsub'>
			<li class='all'><a href='' class="current">All <span class="count">(11)</span></a> |</li>
			<li class='publish'><a href=''>Published <span class="count">(3)</span></a> |</li>
			<li class='draft'><a href=''>Drafts <span class="count">(8)</span></a></li>
		</ul>

		<br clear="all" />

		<div style="height: 600px;">
			<div id="entities-container" class="isotope" style="width: 100%; height: 100%; border: solid 1px black; overflow: auto;"></div>
			<div id="loading-wheel"><img src="../wp-content/plugins/wordlift/images/loading.gif" /></div>
		</div>
	</div>

<?php
}

function create_admin_add_new() {
	global $logger;

	$logger->debug('create_admin_add_new');
}

add_action('init', 					array('wordliftsetup', 			'setup'));
add_action('admin_enqueue_scripts',	array('wordliftsetup', 			'admin_enqueue_scripts'));
add_action('wp_enqueue_scripts',	array('wordliftsetup', 			'enqueue_scripts'));
add_action('edit_post', 			array( $wordlift, 				'analyze_text'));
add_action('edit_post', 			array( $entity_service, 		'save_entity_from_post_edit'));
add_filter('the_content', 			'display_the_content' );
add_action('add_meta_boxes', 		array( $entities_box_service, 	'create_custom_box'));
// uncomment this to enable a custom menu.
// add_action('admin_menu', 			'create_admin_menu');
?>