<?php
/**
 * The Mappings page.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/admin/partials
 */

$mapping_service = Wordlift_Mapping_Service::get_instance();

?>
<div id="wpbody-content" aria-label="Main content" tabindex="0">
    <div class="wrap">
        <h1><?php echo esc_html__( 'Mappings', 'wordlift' ); ?></h1>
    </div>

	<?php

	$mappings = array_reduce(
	// The list of valid post types.
		Wordlift_Entity_Service::valid_entity_post_types(),

		function ( $carry, $post_type ) use ( $mapping_service ) {
			$entity_types = array_filter( Wordlift_Entity_Type_Adapter::get_entity_types( $post_type ),
				function ( $item ) {
					return 'http://schema.org/Article' !== $item;
				} );

			if ( empty( $entity_types ) ) {
				return $carry;
			}

			return $carry + array(
					$post_type => array(
						'entity_types' => $entity_types,
						'count'        => $mapping_service->count( $post_type, $entity_types ),
					),
				);
		},
		array() );

	$mappings['_nonce'] = wp_create_nonce( 'update_post_type_entity_types' );


	$json = wp_json_encode( $mappings );
	echo "<script type='application/javascript'>var wlMappings = $json;</script>";

	?>
    <div class="clear"></div>
</div>
