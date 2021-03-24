<?php

use Wordlift\Vocabulary\Analysis_Background_Service;

/**
 * @group vocabulary
 * Class Analysis_Progress_Endpoint_Test
 */

class Vocabulary_Admin_Dashboard_Widget_Test extends \Wordlift_Vocabulary_Unit_Test_Case {


	public function test_should_render_the_widget_on_admin_dashboard_action() {

		$this->create_tags(2);

		$expected_html = <<<EOF
        <div id="wl-todays-tip" class="wl-dashboard__block wl-dashboard__block--todays-tip">
            <header>
                <h3>Match terms</h3>
            </header>
            <article>
                <p>
                <strong>2 term(s) waiting to be matched with entities.</strong>	
                </p>
        </div>
EOF;

		ob_start();
		do_action('wl_admin_dashboard_widgets');
		$contents = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $contents, $expected_html );

	}


}