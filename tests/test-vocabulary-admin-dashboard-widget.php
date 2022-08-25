<?php

use Wordlift\Vocabulary\Analysis_Background_Service;

/**
 * @group vocabulary
 * Class Analysis_Progress_Endpoint_Test
 */
class Vocabulary_Admin_Dashboard_Widget_Test extends \Wordlift_Vocabulary_Unit_Test_Case {


	public function test_should_render_the_widget_on_admin_dashboard_action() {

		$this->create_unmatched_tags( 2 );

		$expected_html = <<<EOF
        <div id="wl-match-terms" class="wl-dashboard__block wl-dashboard__block--match-terms">
            <header>
                <h3>Match terms</h3>
            </header>
                <p>
                	<strong><a href=''>2 term(s)</a></strong>  waiting to be matched with entities.	
                </p>
        </div>
EOF;

		ob_start();
		do_action( 'wl_admin_dashboard_widgets' );
		$contents = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $this->remove_spaces( $contents ),  $this->remove_spaces( $expected_html ) );

	}

	private function remove_spaces( $string ) {
		return str_replace( ' ', '', preg_replace('/\s+/S', " ", $string ) );
	}


	public function test_when_zero_tags_present_should_not_print_widget() {
		ob_start();
		do_action( 'wl_admin_dashboard_widgets' );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( $contents, "" );
	}


}