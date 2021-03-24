<?php

namespace Wordlift\Vocabulary\Dashboard;
use Wordlift\Vocabulary\Data\Term_Count\Term_Count;

/**
 * This class adds the term matches widget to the admin dashboard
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Term_Matches_Widget {
	/**
	 * @var Term_Count
	 */
	private $term_count;

	/**
	 * Term_Matches_Widget constructor.
	 *
	 * @param $term_count Term_Count
	 */
	public function __construct( $term_count ) {
		$this->term_count = $term_count;
	}

	public function connect_hook() {
		add_action( 'wl_admin_dashboard_widgets', array( $this, 'render_widget' ) );
	}

	public function render_widget() {
		$term_count  = $this->term_count->get_term_count();
		if ( $term_count <= 0 ) {
			return;
		}

		$match_terms_url = menu_page_url('wl-vocabulary-match-terms');
		$term_count_link = "<a href='$match_terms_url'>" . $term_count . " term(s)</a>";
		$match_terms = __('Match terms', 'wordlift');
		$additional_text = __(' waiting to be matched with entities.', 'wordlift');
		echo <<<EOF
        <div id="wl-match-terms" class="wl-dashboard__block wl-dashboard__block--match-terms">
            <header>
                <h3>$match_terms</h3>
            </header>
                <p>
                	<strong>$term_count_link $additional_text</strong>	
                </p>
        </div>
EOF;
	}

}