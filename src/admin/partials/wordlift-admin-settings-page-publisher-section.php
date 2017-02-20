<?php
/**
 * Pages: Publisher section in the Admin Settings page.
 *
 * @since   3.11.0
 * @package Wordlift/admin
 */

// Get all the organizations and persons that might be used as publishers.

$entities_query = new WP_Query( array(
	'post_type'      => Wordlift_Entity_Service::TYPE_NAME,
	'posts_per_page' => $this->max_entities_without_ajax,
	'tax_query'      => array(
		'relation' => 'OR',
		array(
			'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
			'field'    => 'name',
			'terms'    => 'Person',
		),
		array(
			'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
			'field'    => 'name',
			'terms'    => 'Organization',
		),
	),
) );

/*
 * Variable indicating should the select tab (and panel) be displayed.
 * If the wizard is skipped during the install there might not be entities
 * to select from and not point in showing the tab and panel.
 */

$select_panel_displayed = $entities_query->have_posts();
?>
<input type="hidden"
       id="wl-setting-panel"
       autocomplete="off"
       name="wl-setting-panel"
       value="<?php echo $select_panel_displayed ? 'wl-select-entity' : 'wl-create-entity' ?>">
<input type="hidden" id="wl-publisher-logo-id"
       name="wl-publisher-logo-id" autocomplete="off">

<div id="wl-publisher-section"
     class="<?php echo $select_panel_displayed ? 'wl-select-entity-active' : 'wl-create-entity-active' ?>"
     data-tabing-enabled="<?php echo $select_panel_displayed ? 'yes' : 'no' ?>">
	<div class="nav-tab-wrapper">
		<a class="nav-tab <?php echo $select_panel_displayed ? 'nav-tab-active' : '' ?>"
		   data-panel="wl-select-entity"
		   href="#"><?php _e( 'Select existing publisher', 'wordlift' ) ?></a>
		<a class="nav-tab <?php echo $select_panel_displayed ? '' : 'nav-tab-active' ?>"
		   data-panel="wl-create-entity"
		   href="#"><?php _e( 'Create new publisher', 'wordlift' ) ?></a>
	</div>
	<div id="wl-select-entity-panel" class="wl-tab-panel">
		<?php
		// Populate the select only if there are less than max_entities_without_ajax possible entities,
		// Otherwise use AJAX.

		$ajax_params = ( $entities_query->found_posts <= $this->max_entities_without_ajax ) ? '' : ' data-ajax--url="' . parse_url( self_admin_url( 'admin-ajax.php' ), PHP_URL_PATH ) . '/action=wl_possible_publisher" data-ajax--cache="true" ';

		// Show the search box only if there are more entiyies than max_entities_without_search.
		$disable_search_params = ( $entities_query->found_posts > $this->max_entities_without_search ) ? '' : ' data-nosearch="true" ';
		?>
		<select id="wl-select-entity"
		        name="wl_general_settings[<?php echo Wordlift_Configuration_Service::PUBLISHER_ID ?>]"
			<?php echo $ajax_params ?>
			<?php echo $disable_search_params ?>
			    autocomplete="off">
			<?php

			if ( $entities_query->post_count < $this->max_entities_without_ajax ) {
				while ( $entities_query->have_posts() ) {
					$entities_query->the_post();

					/*
					 * Get the thumbnail, the long way around instead of get_the_thumbnail_url
					 * because it is supported only from version 4.4.
					 */

					$thumb             = '';
					$post_thumbnail_id = get_post_thumbnail_id();
					if ( $post_thumbnail_id ) {
						$thumb = wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail' );
					}

					// Get the type of entity.

					$terms = get_the_terms( get_the_ID(), Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

					$entity_type = __( 'Person', 'wordlift' );
					if ( 'Organization' == $terms[0]->name ) {
						$entity_type = __( 'Company', 'wordlift' );
					}

					echo '<option value="' . get_the_ID() . '" ' . selected( $this->configuration_service->get_publisher_id(), get_the_ID(), false ) . ' data-thumb="' . esc_attr( $thumb ) . '" data-type="' . esc_attr( $entity_type ) . '">' . get_the_title() . '</option>';
				}
			} else {
				// Display only the currently selected publisher.

				$post_id = $this->configuration_service->get_publisher_id();
				$post    = get_post( $post_id );

				$thumb             = '';
				$post_thumbnail_id = get_post_thumbnail_id( $post_id );
				if ( $post_thumbnail_id ) {
					$thumb = wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail' );
				}

				// Get the type of entity.

				$terms = get_the_terms( $post_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

				$entity_type = __( 'Person', 'wordlift' );
				if ( 'Organization' == $terms[0]->name ) {
					$entity_type = __( 'Company', 'wordlift' );
				}

				echo '<option value="' . $post_id . '" selected="selected"' . ' data-thumb="' . esc_attr( $thumb ) . '" data-type="' . esc_attr( $entity_type ) . '">' . get_the_title( $post_id ) . '</option>';
			}
			?>
		</select>
	</div>
	<div id="wl-create-entity-panel" class="wl-tab-panel">
		<p>
			<b><?php esc_html_e( 'Are you publishing as an individual or as a company?', 'wordlift' ) ?></b>
		</p>
		<p id="wl-publisher-type">
			<span>
				<input id="wl-publisher-person" type="radio"
				       name="wl-publisher-type" value="person"
				       checked="checcked" autocomplete="off">
				<label
					for="wl-publisher-person"><?php esc_html_e( 'Person', 'wordlift' ) ?></label>
			</span>
			<span>
				<input id="wl-publisher-company" type="radio"
				       name="wl-publisher-type" value="company"
				       autocomplete="off">
				<label
					for="wl-publisher-company"><?php esc_html_e( 'Company', 'wordlift' ) ?></label>
			</span>
		</p>
		<p id="wl-publisher-name">
			<input type="text"
			       placeholder="<?php esc_attr_e( "Publisher's Name", 'wordlift' ) ?>"
			       name="wl-publisher-name">
		</p>
		<div id="wl-publisher-logo">
			<p>
				<b><?php esc_html_e( "Choose the publisher's Logo", 'wordlift' ) ?></b>
			</p>
			<p>
				<img id="wl-publisher-logo-preview"><input type="button"
				                                           class="button"
				                                           value="<?php esc_attr_e( 'Select an existing image or upload a new one', 'wordlift' ); ?>">
			</p>
		</div>
	</div>
</div>
