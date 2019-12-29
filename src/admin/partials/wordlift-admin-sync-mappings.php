<div id="container" style="width: 80%;">
</div>
<?php

	wp_enqueue_script( 'wl-sync-mappings-script' );
	$taxonomy_options = array();
	$term_options     = array();
	$taxonomies       = get_object_taxonomies( 'post' );
	foreach ( $taxonomies as $taxonomy ) {
		array_push(
			$taxonomy_options,
			array(
				'label' => $taxonomy,
				'value' => $taxonomy,
			)
		);
		// Version compatibility for get_terms.
		// ( https://developer.wordpress.org/reference/functions/get_terms/ ).
		if ( version_compare( get_bloginfo( 'version' ), '4.5', '>=' ) ) {
			$terms = get_terms(
				array(
					'taxonomy' => $taxonomy,
				)
			);		
		}
		else {

			$terms = get_terms( $taxonomy );
		}
		
		foreach ( $terms as $term ) {
			array_push(
				$term_options,
				array(
					'label'    => $term->name,
					'value'    => $term->name,
					'taxonomy' => $taxonomy,
				)
			);
		}
	}
	print_r( $taxonomy_options );
	echo '<br/><br/><br/>';
	print_r( $term_options );
	
?>