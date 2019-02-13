<?php
/**
 * @see https://github.com/insideout10/wordlift-plugin/issues/879
 *
 * @since 3.20.0
 */
?>
<style>
    /* WordLift Dashboard */
    #wl-dashboard-v2 header {
        padding: .5em;
        background: #f1f1f1;
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    #wl-dashboard-v2 header h3 {
        margin: 0;
        font-weight: 500;
    }

    #wl-dashboard-v2 .dashicons {
        color: #007aff;
    }

    #wl-dashboard-v2 .inside > div {
        border: 1px solid #eee;
        padding: 2px;
        margin-bottom: 12px;
    }

    #wl-dashboard-v2 header ~ * {
        margin: .5em;
    }

    /* Remove the margin from `p`, use the margin on the container */
    #wl-dashboard-v2 header ~ * p {
        margin: 0;
    }
</style>
<?php
// Print out the Today's Tip block.
Wordlift_Admin_Dashboard_V2::get_todays_tip_block();

// Backlinks isn't ready yet.
// <h3> ... echo __( 'Backlinks', 'wordlift' ); ... </h3>
?>


<?php
$country_code = Wordlift_Configuration_Service::get_instance()->get_country_code()
?>
<div>
    <header>
        <span class="dashicons dashicons-editor-help"></span>
        <h3><?php echo __( 'Search rankings', 'wordlift' ); ?></h3>
        <div class="pull-right">
			<?php echo esc_html( __( Wordlift_Countries::get_country_name( $country_code ) ) ); ?>
            <img width="16" height="16" src="<?php echo Wordlift_Countries::get_flag_url( $country_code ); ?>">
            <img width="16" height="16"
                 src="<?php echo plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . '/images/woorank-16x16.png'; ?>">
        </div>
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

<div>
    <header>
        <span class="dashicons dashicons-editor-help"></span>
        <h3><?php echo __( 'Enriched posts', 'wordlift' ); ?></h3>
    </header>
    <div>
		<?php echo $this->dashboard_service->count_annotated_posts(); ?>
        / <?php echo $this->dashboard_service->count_posts(); ?>

        <a href=""><?php echo esc_html( _x( 'Enrich', 'Dashboard', 'wordlift' ) ); ?></a>
    </div>
</div>

<div>
    <header>
        <span class="dashicons dashicons-editor-help"></span>
        <h3><?php echo __( 'Created entities', 'wordlift' ); ?></h3>
    </header>
    <div>
		<?php echo $this->entity_service->count(); ?>

        <a href=""><?php echo esc_html( _x( 'Vocabulary', 'Dashboard', 'wordlift' ) ); ?></a>
    </div>
</div>

<div>
    <header>
        <span class="dashicons dashicons-editor-help"></span>
        <h3><?php echo __( 'Average entity rating', 'wordlift' ); ?></h3>
    </header>
    <div>
		<?php echo $this->dashboard_service->average_entities_rating(); ?>

        <a href=""><?php echo esc_html( _x( 'Pimp', 'Dashboard', 'wordlift' ) ); ?></a>
    </div>
</div>

<div>
    <header>
        <span class="dashicons dashicons-editor-help"></span>
        <h3><?php echo __( 'Graph data', 'wordlift' ); ?></h3>
    </header>
    <div>
		<?php echo esc_html( _x( 'Created triples', 'Dashboard', 'wordlift' ) ); ?>
        : <?php echo $this->dashboard_service->count_triples(); ?><br/>
		<?php echo esc_html( _x( 'Ratio on Wikidata', 'Dashboard', 'wordlift' ) ); ?>:
		<?php echo $this->dashboard_service->count_triples() * 100 / 947690143; ?>
    </div>
</div>
