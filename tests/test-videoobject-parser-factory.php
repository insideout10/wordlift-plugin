<?php

use Wordlift\Videoobject\Parser\Block_Editor_Parser;
use Wordlift\Videoobject\Parser\Classic_Editor_Parser;
use Wordlift\Videoobject\Parser\Parser_Factory;

/**
 * Class Videoobject_Jsonld_Test
 * @group videoobject
 */
class Videoobject_Parser_Factory_Test extends \Wordlift_Unit_Test_Case {

	public function test_should_provide_block_editor_parser_if_no_blocks_in_post_content() {
		if ( ! function_exists('parse_blocks') ) {
			$this->markTestSkipped('Skipped because WP < 5.0 doesnt have parse_blocks function');
		}
		$post_content = <<<EOF
<!-- wp:embed {"url":"https://vimeo.com/162427937","type":"video","providerNameSlug":"vimeo","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-vimeo wp-block-embed-vimeo wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://vimeo.com/162427937
</div></figure>
<!-- /wp:embed -->
EOF;

		$parser = Parser_Factory::get_parser_from_content( $post_content );
		$this->assertTrue( $parser instanceof Block_Editor_Parser );
	}

	public function test_should_provide_classic_editor_parser_if_no_blocks_in_post_content() {
		$post_content = <<<EOF
https://www.youtube.com/watch?v=fJAPDAK4GiI
EOF;
		$parser = Parser_Factory::get_parser_from_content( $post_content );
		$this->assertTrue( $parser instanceof Classic_Editor_Parser );
	}

}