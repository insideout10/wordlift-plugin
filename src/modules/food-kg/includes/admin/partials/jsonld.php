<textarea id="jsonld">
	<?php

	/** @var string $value */
	$json = wp_json_encode( json_decode( $value ), JSON_PRETTY_PRINT );
	echo esc_html( $json );

	?>
</textarea>
