<?php
define('STANBOL_URL','http://stanbol.insideout.io/enhancerjobs/');

// the type to use when registering/accessing entities in WP (max. 20 characters, can not contain capital letters or spaces).
// for more information, see http://codex.wordpress.org/Function_Reference/register_post_type.
define('POST_CUSTOM_TYPE_ENTITY', 					'wordlift20_entity');
define('WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE', 		'wordlift20_entity');
// The position in the menu order the post type should appear.
define('POST_CUSTOM_TYPE_ENTITY_MENU_POSITION', 21);

define('WORDLIFT_20_POST_META_JOB_ID', 				'wordlift20_job_id');
define('WORDLIFT_20_POST_META_JOB_STATE', 			'wordlift20_job_state');

define('WORDLIFT_20_ENTITY_POSTS', 					'wordlift20_entity_posts');
define('WORDLIFT_20_ENTITY_ACCEPTED', 				'wordlift20_entity_posts_accepted');
define('WORDLIFT_20_ENTITY_REJECTED', 				'wordlift20_entity_posts_rejected');

define('WORDLIFT_20_JOB_SERVICE_ANALYZING',			'in-progress');
define('WORDLIFT_20_JOB_SERVICE_COMPLETED',			'completed');

define('POST_META_ENTITY_ID', 						'wordlift20_entity_id');
define('POST_META_ENTITY_TYPE', 					'wordlift20_entity_type');
define('POST_META_ENTITY_SLUG', 					'wordlift20_entity_slug');
define('POST_META_ENTITY_PREFIX', 					'wordlift20_entity_');
define('WORDLIFT_20_POST_META_ENTITY_PREFIX', 		'wordlift20_entity_');

define('WORDLIFT_20_TAXONOMY_NAME', 				'entities');

define('WORDLIFT_20_TAXONOMY_CREATIVE_WORK', 		'Creative Work');
define('WORDLIFT_20_TAXONOMY_EVENT', 				'Event');
define('WORDLIFT_20_TAXONOMY_ORGANIZATION', 		'Organization');
define('WORDLIFT_20_TAXONOMY_PERSON', 				'Person');
define('WORDLIFT_20_TAXONOMY_PLACE', 				'Place');
define('WORDLIFT_20_TAXONOMY_PRODUCT', 				'Product');
define('WORDLIFT_20_TAXONOMY_OTHER', 				'Other');

define('WORDLIFT_20_TAXONOMY_CREATIVE_WORK_SLUG', 	'creative-work');
define('WORDLIFT_20_TAXONOMY_EVENT_SLUG', 			'event');
define('WORDLIFT_20_TAXONOMY_ORGANIZATION_SLUG', 	'organization');
define('WORDLIFT_20_TAXONOMY_PERSON_SLUG', 			'person');
define('WORDLIFT_20_TAXONOMY_PLACE_SLUG', 			'place');
define('WORDLIFT_20_TAXONOMY_PRODUCT_SLUG',			'product');
define('WORDLIFT_20_TAXONOMY_OTHER_SLUG', 			'other');

define('WORDLIFT_20_ENTITIES_SLUG',					'entities');

// get the base folder for plugins_url translations.
$base = dirname(dirname(__FILE__));

require_once( dirname($base).'/wp-load.php' );

define('WORDLIFT_20_ROOT_PATH', 					$base);
define('WORDLIFT_20_PLUGIN_DIR', 					'wordlift/');
define('ENHANCE_TEXT_URL',							'http://localhost:8081/insideout10/enhance/text');
define('ON_COMPLETE_URL', 							plugins_url('complete.php', $base));
define('ON_PROGRESS_URL', 							plugins_url('progress.php', $base));
define('CHAIN_NAME', 								'default');

?>
