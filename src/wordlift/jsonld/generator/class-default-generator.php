<?php

namespace Wordlift\Jsonld\Generator;

use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift_Jsonld_Service;

class Default_Generator implements Generator {
	/**
	 * @var Wordlift_Jsonld_Service
	 */
	private $jsonld_service;


	public function __construct( $jsonld_service ) {
		$this->jsonld_service = $jsonld_service;
	}

	function generate() {
		// Determine whether this is the home page or whether we're displaying a single post.
		$is_homepage = is_home() || is_front_page();
		$post_id     = is_singular() ? get_the_ID() : null;

		// Get the JSON-LD.
		$jsonld = json_encode( $this->jsonld_service->get_jsonld( $is_homepage, $post_id, Jsonld_Context_Enum::PAGE ) );
		// Finally print the JSON-LD out.
		$jsonld_post_html_output = <<<EOF
        <script type="application/ld+json" id="wl-jsonld">$jsonld</script>
EOF;
		$jsonld_post_html_output = apply_filters( 'wl_jsonld_post_html_output', $jsonld_post_html_output, $post_id );

		echo $jsonld_post_html_output;
	}
}