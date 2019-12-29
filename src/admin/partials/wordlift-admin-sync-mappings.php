<div id="container" style="width: 80%;">
</div>
<?php
    $args = array(
		'public'   => true,
	 );
	print_r( get_post_types( $args ) );
	wp_enqueue_script( 'wl-sync-mappings-script' );
?>