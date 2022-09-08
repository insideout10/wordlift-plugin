<?php
/**
 * Tests: WP-Rocket Adapter.
 *
 * Test the {@link Wordlift_WpRocket_Adapter} class.
 *
 * @since 3.19.4
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_WpRocket_Adapter_Test class.
 *
 * @since 3.19.4
 * @group integrations
 */
class Wordlift_WpRocket_Adapter_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test the `rocket_exclude_defer_js` hook.
	 *
	 * @since 3.23.0 Test also for wordlift-cloud.js.
	 * @since 3.19.4
	 */
	public function test_exclude_defer_js() {

		$excludes = apply_filters( 'rocket_exclude_js', array() );

		$this->assertTrue( is_array( $excludes ), '`$excludes` must be an array.' );
		$this->assertContains( '/wp-content/plugins/app/src/js/dist/bundle.js', $excludes, '`$excludes` must contain `/wp-content/plugins/app/src/js/dist/bundle.js`. [' . var_export( $excludes, true ) . ']' );
		$this->assertContains( '/wp-content/plugins/app/src/js/dist/wordlift-cloud.js', $excludes, '`$excludes` must contain `/wp-content/plugins/app/src/js/dist/wordlift-cloud.js`. [' . var_export( $excludes, true ) . ']' );

	}

}
