<?php
/**
 * The Mappings page.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/admin/partials
 */
wp_enqueue_script( 'wp-util' );

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

	<?php

	foreach ( $mappings as $key => $value ) {
		// Continue if this is the nonce.
		if ( '_nonce' == $key ) {
			continue;
		}

		// Continue if the count isn't set or it's zero, i.e. no need to apply the taxonomy.
		if ( ! isset( $value['count'] ) || 0 === $value['count'] ) {
			continue;
		}
		?>
        <div class="post-type-to-entity-types">
            <button data-post-type="<?php echo esc_attr( $key ); ?>"
                    data-entity-types="<?php echo esc_attr( wp_json_encode( $value['entity_types'] ) ); ?>"
            ><?php echo esc_html( $key ); ?></button>
            <div><?php echo esc_html( $value['count'] ); ?></div>
        </div>
		<?php
	}

	?>
    <div class="clear"></div>
</div>


<script type="text/javascript">
  (function($) {

    if (undefined === typeof window["wlMappings"]) {
      return;
    }

    const wlMappings = window["wlMappings"];

    $(".post-type-to-entity-types > button").on("click", function(e) {

      const $target = $(e.target);
      const postType = $target.data("post-type");
      const entityTypes = $target.data("entity-types");

      function doIt ($target, postType, entityTypes) {
        wp.ajax.post("wl_update_post_type_entity_types", {
          "_nonce": wlMappings["_nonce"],
          "post_type": postType,
          "entity_types": entityTypes
        }).done(function(data) {

          wlMappings["_nonce"] = data["_nonce"];

          $target.find("~ div").text(data["count"]);

          if ( 0 < data["count"] ) {
            window.setTimeout(function() { doIt($target, postType, entityTypes); }, 1);
          }
        });
      }

      doIt($target, postType, entityTypes);
    });

  })(jQuery);
</script>