<?php

namespace Wordlift\Vocabulary\Dashboard;

use Wordlift\Vocabulary\Data\Term_Count\Term_Count;
use Wordlift\Vocabulary\Menu\Badge\Badge_Generator;

/**
 * This class adds the term matches widget to the admin dashboard
 *
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
		$term_count = $this->term_count->get_term_count();
		if ( $term_count <= 0 ) {
			return;
		}

		$match_terms_url = menu_page_url( 'wl-vocabulary-match-terms', false );

		?>
		<div id="wl-match-terms" class="wl-dashboard__block wl-dashboard__block--match-terms">
			<header>
				<h3><?php esc_html_e( 'Match terms', 'wordlift' ); ?></h3>
			</header>
			<p>
				<strong><a href='<?php echo esc_attr( $match_terms_url ); ?>'><?php echo esc_html( Badge_Generator::get_formatted_count_string( $term_count ) ); ?> term(s)</a></strong> <?php esc_html_e( ' waiting to be matched with entities.', 'wordlift' ); ?>
			</p>
		</div>
		<?php
	}

}
