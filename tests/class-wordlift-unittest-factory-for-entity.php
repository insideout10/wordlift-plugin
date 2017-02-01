<?php
/**
 * Test Factories: Factory for Entity.
 *
 * @since   3.10.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_UnitTest_Factory_For_Entity} class.
 *
 * @since   3.10.0
 * @package Wordlift
 */
class Wordlift_UnitTest_Factory_For_Entity extends WP_UnitTest_Factory_For_Post {

	/**
	 * {@inheritdoc}
	 */
	public function __construct( $factory = null ) {
		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'post_status'  => 'publish',
			'post_title'   => new WP_UnitTest_Generator_Sequence( 'Entity title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'Entity content %s' ),
			'post_excerpt' => new WP_UnitTest_Generator_Sequence( 'Entity excerpt %s' ),
			'post_type'    => 'entity',
		);

	}

}
