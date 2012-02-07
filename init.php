<?php
require_once( 'wp-load.php' );

$post_type = 'ioio_entity';
 
register_post_type( $post_type,
		array(
			'labels' => array(
				'name' => __( 'Entities' ),
				'singular_name' => __( 'Entity' )
			),
		'public' => true,
		'has_archive' => true,
		)
	);

echo(post_type_exists( $post_type ));
?>
