<?php
/**
 * Installs: Install Version 3.18.0.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_3_18_0} interface.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_3_18_0 extends Wordlift_Install {
	/**
	 * @inheritdoc
	 */
	protected static $version = '3.18.0';

	/**
	 * @inheritdoc
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'wl_async_wl_push_references', array(
			$this,
			'push_references',
		) );
	}

	/**
	 * @inheritdoc
	 */
	public function install() {
		do_action( 'wl_push_references' );
	}

	/**
	 * Creates references for articles *referencing* entities
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function push_references() {
		// Get relations.
		$relations = Wordlift_Relation_Service::get_instance()->find_all_grouped_by_subject_id();

		$entity_service = Wordlift_Entity_Service::get_instance();

		// Loop through all relations and push the references.
		foreach ( $relations as $relation ) {

			$post = get_post( $relation->subject_id );

			// Bail out if it's an entity: we're only interested in articles
			// *referencing* entities.
			if ( $entity_service->is_entity( $post->ID ) ) {
				continue;
			}

			// Push the references.
			Wordlift_Linked_Data_Service::get_instance()->push( $post->ID );
		}

	}

}
