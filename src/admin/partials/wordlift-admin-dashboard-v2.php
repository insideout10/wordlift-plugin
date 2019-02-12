<?php
/**
 * @see https://github.com/insideout10/wordlift-plugin/issues/879
 *
 * @since 3.20.0
 */

// Print out the Today's Tip block.
Wordlift_Admin_Dashboard_V2::get_todays_tip_block();

// Backlinks isn't ready yet.
// <h3> ... echo __( 'Backlinks', 'wordlift' ); ... </h3>
?>


<div>
    <header>
        <span class="dashicons dashicons-editor-help"></span>
        <h3><?php echo __( 'Search rankings', 'wordlift' ); ?></h3>
        <img width="16" height="16" src="<?php echo Wordlift_Countries::get_flag_url(
			Wordlift_Configuration_Service::get_instance()->get_country_code()
		); ?>">
    </header>

    <div><?php echo esc_html( _x( 'Keywords', 'Dashboard', 'wordlift' ) ); ?>
        : <?php echo wp_count_terms( Wordlift_Search_Keyword_Taxonomy::TAXONOMY_NAME ); ?></div>
    <div><?php echo esc_html( _x( 'Average position', 'Dashboard', 'wordlift' ) ); ?>:
		<?php echo $average_position_string; ?> </div>
</div>

<div>
    <header>
        <span class="dashicons dashicons-editor-help"></span>
        <h3><?php echo __( 'Top entities', 'wordlift' ); ?></h3>
        <span id="dashboard__link_with_entities"><?php echo esc_html( _x( 'Links with entities', 'Dashboard', 'wordlift' ) ); ?></span>
        <span id="dashboard__post_with_entity"><?php echo esc_html( _x( 'Post with entities', 'Dashboard', 'wordlift' ) ); ?></span>
    </header>
    <div>
		<?php foreach ( $this->get_top_entities() as $post ) {

			$permalink = get_permalink( $post->ID );
			$title     = $post->post_title;
			echo '<a href="' . esc_attr( $permalink ) . '">' . esc_html( $title ) . '</a>';
			echo 'entities: ' . $post->entities . ' / ';
			echo 'posts: ' . $post->posts . '<br/>';

		} ?>
    </div>
</div>

<h3><?php echo __( 'Enriched posts', 'wordlift' ); ?></h3>

<h3><?php echo __( 'Created entities', 'wordlift' ); ?></h3>

<h3><?php echo __( 'Average entity rating', 'wordlift' ); ?></h3>

<h3><?php echo __( 'Graph data', 'wordlift' ); ?></h3>
