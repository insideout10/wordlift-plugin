<?php
/**
 * Services: Sample Data Service.
 *
 * The Sample Data Service preloads contents on the WordPress web site in order
 * to showcase and test WordLift's general features.
 *
 * @since   3.12.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Sample_Data_Service} class.
 *
 * @since   3.12.0
 * @package Wordlift
 */
class Wordlift_Sample_Data_Service {

	/**
	 * An array of sample data.
	 *
	 * @since 3.12.0
	 * @var array $samples An array of sample data.
	 */
	private $samples = array(
		array(
			'post'            => array(
				'post_name'    => 'praesent_imperdiet_odio_sed_lectus_vulputate_finibus',
				'post_title'   => 'Praesent imperdiet odio sed lectus vulputate finibus',
				'post_content' => 'Praesent imperdiet odio sed lectus vulputate finibus. Donec placerat ex arcu, eget fermentum metus ullamcorper vitae. Cras interdum libero a tellus sagittis, sed ultricies sapien tincidunt. Aliquam sit amet vehicula sem. Mauris neque nisl, pellentesque ut molestie id, laoreet nec tortor. Sed tempus ornare est, nec dapibus enim ornare eu. Cras risus ligula, blandit ut faucibus ut, vulputate id ipsum. In vel purus at orci hendrerit cursus. Aliquam interdum lorem id dui maximus volutpat. Vestibulum mi velit, efficitur nec neque eu, posuere porta risus.',
				'post_type'    => 'entity',
				'post_status'  => 'publish',
			),
			'entity_type_uri' => 'http://schema.org/Event',
		),
		array(
			'post'            => array(
				'post_name'    => 'nullam_tempor_lectus_sit_amet_tincidunt_euismod',
				'post_title'   => 'Nullam tempor lectus sit amet tincidunt euismod',
				'post_content' => '<span id="urn:enhancement-da554278-9522-2d83-76ad-8129d2292cb3" class="textannotation disambiguated wl-event" itemid="{dataset-uri}/entity/praesent_imperdiet_odio_sed_lectus_vulputate_finibus">Praesent imperdiet odio sed lectus vulputate finibus</span> Nullam tempor lectus sit amet tincidunt euismod. Nunc posuere libero augue, eu pretium erat interdum id. Vivamus aliquam dui in mauris tempor, vitae vestibulum odio aliquet. Proin quis bibendum diam, nec tempus dui. Pellentesque sit amet justo vitae urna ornare volutpat quis consectetur nisl. Sed hendrerit purus et magna varius, sodales tincidunt velit finibus. Donec malesuada faucibus mattis. Morbi viverra sagittis justo nec luctus. Nullam et justo sed nisi fringilla rutrum sit amet a urna. Integer elementum, risus in condimentum rhoncus, nisi velit cursus tellus, sed sagittis ante tellus hendrerit ante. Donec et semper libero, vitae imperdiet ligula. Donec eleifend iaculis nisi sed mollis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin faucibus magna ac lectus tempor iaculis quis in nisi. Mauris ac nibh lacinia, ultrices erat quis, rhoncus lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.',
				'post_type'    => 'entity',
				'post_status'  => 'publish',
			),
			'entity_type_uri' => 'http://schema.org/Place',
		),
		array(
			'post'            => array(
				'post_name'    => 'praesent_luctus_tincidunt_odio_quis_aliquam',
				'post_title'   => 'Praesent luctus tincidunt odio quis aliquam',
				'post_content' => 'Praesent luctus tincidunt odio quis aliquam. Ut pellentesque odio nec turpis placerat, at rhoncus mauris elementum. Proin vehicula lectus a dolor bibendum, ut pretium lacus volutpat. Integer luctus enim sed odio dapibus tempus. Fusce elementum purus in diam dictum, sit amet ultricies leo molestie. Etiam id nunc tincidunt sapien tristique interdum ac at purus. Nulla eget laoreet turpis. Nullam id cursus nulla.',
				'post_type'    => 'entity',
				'post_status'  => 'publish',
			),
			'entity_type_uri' => 'http://schema.org/Organization',
		),
		array(
			'post'            => array(
				'post_name'    => 'lorem_ipsum_dolor_sit_amet__consectetur_adipiscing_elit',
				'post_title'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
				'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
				'post_type'    => 'entity',
				'post_status'  => 'publish',
			),
			'entity_type_uri' => 'http://schema.org/CreativeWork',
		),
		array(
			'post' =>
				array(
					'post_name'    => 'post_1',
					'post_title'   => 'Praesent imperdiet odio sed lectus vulputate finibus',
					'post_content' => '<span><span id="urn:enhancement-da554278-9522-2d83-76ad-8129d2292cb3" class="textannotation disambiguated wl-event" itemid="{dataset-uri}/entity/praesent_imperdiet_odio_sed_lectus_vulputate_finibus">Praesent imperdiet odio sed lectus vulputate finibus</span>. Donec placerat ex arcu, eget fermentum metus ullamcorper vitae. Cras interdum libero a tellus sagittis, sed ultricies sapien tincidunt. Aliquam sit amet vehicula sem. Mauris neque nisl, pellentesque ut molestie id, laoreet nec tortor. Sed tempus ornare est, nec dapibus enim ornare eu. Cras risus ligula, blandit ut faucibus ut, vulputate id ipsum. In vel purus at orci hendrerit cursus. Aliquam interdum lorem id dui maximus volutpat. Vestibulum mi velit, efficitur nec neque eu, posuere porta risus.</span>',
					'post_type'    => 'post',
					'post_status'  => 'publish',
				),
		),
		array(
			'post' =>
				array(
					'post_name'    => 'post_2',
					'post_title'   => 'Nullam tempor lectus sit amet tincidunt euismod',
					'post_content' => '<span><span id="urn:local-text-annotation-p8i5o4279ex3rsbwqkrx9z5mh1ox91ae" class="textannotation disambiguated wl-place" itemid="{dataset-uri}/entity/nullam_tempor_lectus_sit_amet_tincidunt_euismod">Nullam tempor lectus sit amet tincidunt euismod</span>. Nunc posuere libero augue, eu pretium erat interdum id. Vivamus aliquam dui in mauris tempor, vitae vestibulum odio aliquet. Proin quis bibendum diam, nec tempus dui. Pellentesque sit amet justo vitae urna ornare volutpat quis consectetur nisl. Sed hendrerit purus et magna varius, sodales tincidunt velit finibus. Donec malesuada faucibus mattis. Morbi viverra sagittis justo nec luctus. Nullam et justo sed nisi fringilla rutrum sit amet a urna. Integer elementum, risus in condimentum rhoncus, nisi velit cursus tellus, sed sagittis ante tellus hendrerit ante. Donec et semper libero, vitae imperdiet ligula. Donec eleifend iaculis nisi sed mollis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin faucibus magna ac lectus tempor iaculis quis in nisi. Mauris ac nibh lacinia, ultrices erat quis, rhoncus lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</span>',
					'post_type'    => 'post',
					'post_status'  => 'publish',
				),
		),
		array(
			'post' =>
				array(
					'post_name'    => 'post_3',
					'post_title'   => 'Praesent luctus tincidunt odio quis aliquam',
					'post_content' => '<span><span id="urn:enhancement-b3487a20-4696-b6d9-6c55-842445f5c263" class="textannotation disambiguated wl-organization" itemid="{dataset-uri}/entity/praesent_luctus_tincidunt_odio_quis_aliquam">Praesent luctus tincidunt odio quis aliquam</span>. Ut pellentesque odio nec turpis placerat, at rhoncus mauris elementum. Proin vehicula lectus a dolor bibendum, ut pretium lacus volutpat. Integer luctus enim sed odio dapibus tempus. Fusce elementum purus in diam dictum, sit amet ultricies leo molestie. Etiam id nunc tincidunt sapien tristique interdum ac at purus. Nulla eget laoreet turpis. Nullam id cursus nulla.</span>',
					'post_type'    => 'post',
					'post_status'  => 'publish',
				),
		),
		array(
			'post' =>
				array(
					'post_name'    => 'post_4',
					'post_title'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
					'post_content' => '<span><span id="urn:enhancement-4edc3bde-d275-22f9-8d50-0b707596b292" class="textannotation disambiguated wl-thing" itemid="{dataset-uri}/entity/lorem_ipsum_dolor_sit_amet__consectetur_adipiscing_elit">Lorem ipsum dolor sit amet, consectetur adipiscing elit</span>. Proin rutrum ultrices nulla ut elementum. Nunc nec lacus tortor. Curabitur bibendum imperdiet luctus. Vivamus a faucibus dolor. Donec blandit malesuada risus. Vestibulum volutpat ut tellus sed tincidunt. Sed id tincidunt velit. Integer sed felis id libero fringilla molestie vitae id orci. Ut vel purus ullamcorper, feugiat tortor non, iaculis neque. Vivamus vitae vehicula sem. Mauris fermentum, metus id vestibulum sodales, lorem lacus efficitur ante, non vestibulum ligula ligula a turpis. Vivamus quis scelerisque massa.</span>',
					'post_type'    => 'post',
					'post_status'  => 'publish',
				),
		),
		array(
			'post' => array(
				'post_name'    => 'post_5',
				'post_title'   => 'Lorem ipsum',
				'post_content' => '
					<span id="urn:enhancement-28cb4112-64cf-bd49-ef97-a2ee54727de7" class="textannotation disambiguated wl-thing" itemid="{dataset-uri}/entity/lorem_ipsum_dolor_sit_amet__consectetur_adipiscing_elit">Lorem ipsum</span> dolor sit amet, consectetur adipiscing elit. Proin rutrum ultrices nulla ut elementum. Nunc nec lacus tortor. Curabitur bibendum imperdiet luctus. Vivamus a faucibus dolor. Donec blandit malesuada risus. Vestibulum volutpat ut tellus sed tincidunt. Sed id tincidunt velit. Integer sed felis id libero fringilla molestie vitae id orci. Ut vel purus ullamcorper, feugiat tortor non, iaculis neque. Vivamus vitae vehicula sem. Mauris fermentum, metus id vestibulum sodales, lorem lacus efficitur ante, non vestibulum ligula ligula a turpis. Vivamus quis scelerisque massa.
					
					[wl_navigator]
					
					<span id="urn:local-text-annotation-p4pre3y4tccnq00prifn6lzkowgcw6ip" class="textannotation disambiguated wl-organization" itemid="{dataset-uri}/entity/praesent_luctus_tincidunt_odio_quis_aliquam">Praesent luctus tincidunt odio quis aliquam</span>. Ut pellentesque odio nec turpis placerat, at rhoncus mauris elementum. Proin vehicula lectus a dolor bibendum, ut pretium lacus volutpat. Integer luctus enim sed odio dapibus tempus. Fusce elementum purus in diam dictum, sit amet ultricies leo molestie. Etiam id nunc tincidunt sapien tristique interdum ac at purus. Nulla eget laoreet turpis. Nullam id cursus nulla.
					
					[wl_navigator]
					
					<span id="urn:local-text-annotation-th789do93h8xdgz7zquk7c6qxy4kx0jk" class="textannotation disambiguated wl-place" itemid="{dataset-uri}/entity/nullam_tempor_lectus_sit_amet_tincidunt_euismod">Nullam tempor lectus sit amet tincidunt euismod</span>. Nunc posuere libero augue, eu pretium erat interdum id. Vivamus aliquam dui in mauris tempor, vitae vestibulum odio aliquet. Proin quis bibendum diam, nec tempus dui. Pellentesque sit amet justo vitae urna ornare volutpat quis consectetur nisl. Sed hendrerit purus et magna varius, sodales tincidunt velit finibus. Donec malesuada faucibus mattis. Morbi viverra sagittis justo nec luctus. Nullam et justo sed nisi fringilla rutrum sit amet a urna. Integer elementum, risus in condimentum rhoncus, nisi velit cursus tellus, sed sagittis ante tellus hendrerit ante. Donec et semper libero, vitae imperdiet ligula. Donec eleifend iaculis nisi sed mollis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin faucibus magna ac lectus tempor iaculis quis in nisi. Mauris ac nibh lacinia, ultrices erat quis, rhoncus lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
					
					[wl_navigator]\
					
					<span id="urn:local-text-annotation-v0kqdtx685n6cg9jrfvl67amkhm28hxh" class="textannotation disambiguated wl-event" itemid="{dataset-uri}/entity/praesent_imperdiet_odio_sed_lectus_vulputate_finibus">Praesent imperdiet odio sed lectus vulputate finibus</span>. Donec placerat ex arcu, eget fermentum metus ullamcorper vitae. Cras interdum libero a tellus sagittis, sed ultricies sapien tincidunt. Aliquam sit amet vehicula sem. Mauris neque nisl, pellentesque ut molestie id, laoreet nec tortor. Sed tempus ornare est, nec dapibus enim ornare eu. Cras risus ligula, blandit ut faucibus ut, vulputate id ipsum. In vel purus at orci hendrerit cursus. Aliquam interdum lorem id dui maximus volutpat. Vestibulum mi velit, efficitur nec neque eu, posuere porta risus.
					',
				'post_type'    => 'post',
				'post_status'  => 'publish',
			),
		),
	);

	/**
	 * The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.12.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * The {@link Wordlift_User_Service} instance.
	 *
	 * @since  3.16.0
	 * @access private
	 * @var \Wordlift_User_Service $user_service The {@link Wordlift_User_Service} instance.
	 */
	private $user_service;

	/**
	 * Create a {@link Wordlift_Sample_Data_Service} instance.
	 *
	 * @param \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 * @param \Wordlift_User_Service        $user_service The {@link Wordlift_User_Service} instance.
	 *
	 * @since 3.12.0
	 */
	protected function __construct( $entity_type_service, $user_service ) {

		$this->entity_type_service = $entity_type_service;
		$this->user_service        = $user_service;

	}

	private static $instance = null;

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( Wordlift_Entity_Type_Service::get_instance(), Wordlift_User_Service::get_instance() );
		}

		return self::$instance;
	}

	/**
	 * Create sample data in this WordPress instance.
	 *
	 * @since 3.12.0
	 */
	public function create() {

		// Get the source image path.
		$source = plugin_dir_path( __DIR__ ) . 'images/rome.png';

		// Create an attachment with the local file.
		$attachment_id = $this->create_attachment_from_local_file( $source );

		// Add a flag to signal the attachment is sample data and allow easy delete
		// afterwards.
		add_post_meta( $attachment_id, '_wl_sample_data', 1, true );

		// Get the dataset URI, used for replacements in the `post_content`.
		$dataset_uri = untrailingslashit( Wordlift_Configuration_Service::get_instance()->get_dataset_uri() );

		// Create the author and get its id.
		$author_id = $this->create_author();

		// Create 4 entities.
		// Create 4 posts referencing each one entity.
		// Create 1 post referencing all the entities.
		foreach ( $this->samples as $sample ) {

			// Get the post data.
			$post = array_replace_recursive(
				$sample['post'],
				array(
					'post_content' => str_replace( '{dataset-uri}', $dataset_uri, $sample['post']['post_content'] ),
				)
			);

			// Set the author.
			$post['post_author'] = $author_id;

			// Insert the post.
			$post_id = wp_insert_post( $post );

			// Add a flag to signal the post is sample data and allow easy delete
			// afterwards.
			add_post_meta( $post_id, '_wl_sample_data', 1, true );

			// Set the post thumbnail.
			set_post_thumbnail( $post_id, $attachment_id );

			// If the `entity_type_uri` property is set, set it on the post.
			if ( isset( $sample['entity_type_uri'] ) ) {
				$this->entity_type_service->set( $post_id, $sample['entity_type_uri'] );
			}
		}

	}

	/**
	 * Create an author to bind to posts.
	 *
	 * @return int The {@link WP_User}'s id.
	 * @since 3.16.0
	 */
	private function create_author() {

		$user_id        = wp_create_user( 'wl-sample-data', wp_generate_password() );
		$author_post_id = wp_insert_post(
			array(
				'post_type'  => 'entity',
				'post_title' => 'WordLift Sample Data Person',
			)
		);
		// Add a flag to signal the attachment is sample data and allow easy delete
		// afterwards.
		add_post_meta( $author_post_id, '_wl_sample_data', 1, true );

		$this->entity_type_service->set( $author_post_id, 'http://schema.org/Person' );
		$this->user_service->set_entity( $user_id, $author_post_id );

		return $user_id;
	}

	/**
	 * Remove the sample data from this WordPress instance.
	 *
	 * @since 3.12.0
	 */
	public function delete() {

		$this->delete_by_type( 'post' );
		$this->delete_by_type( 'entity' );
		$this->delete_by_type( 'attachment' );

		// Get and delete the user.
		$user = get_user_by( 'login', 'wl-sample-data' );
		wp_delete_user( $user->ID );

	}

	/**
	 * Remove the sample data of the specified type (e.g. `post`, `entity`, `attachment`)
	 * from the local WordPress instance.
	 *
	 * @param string $type WordPress {@link WP_Post}'s type, e.g. `post`, `entity`, `attachment`.
	 *
	 * @since 3.12.0
	 */
	private function delete_by_type( $type ) {

		$posts = get_posts(
			array(
				'meta_key'    => '_wl_sample_data',
				'meta_value'  => 1,
				'post_status' => 'any',
				'post_type'   => $type,
			)
		);

		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}

	}

	/**
	 * Create a WordPress' attachment using the specified file.
	 *
	 * @param string $source The source file path.
	 *
	 * @return int WordPress' attachment's id.
	 * @since 3.12.0
	 */
	private function create_attachment_from_local_file( $source ) {

		// Get the path to the upload directory.
		$upload_dir  = wp_upload_dir();
		$upload_path = $upload_dir['path'];

		// Get the destination image path.
		$destination = $upload_path . '/wl-sample-data.png';

		// Copy the source file to the destination.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@copy( $source, $destination );

		return $this->create_attachment( $destination );
	}

	/**
	 * Create a WordPress attachment using the specified file in the upload folder.
	 *
	 * @see   https://codex.wordpress.org/Function_Reference/wp_insert_attachment
	 *
	 * @since 3.12.0
	 *
	 * @param string $filename The image filename.
	 *
	 * @return int The attachment id.
	 */
	private function create_attachment( $filename ) {

		// Check the type of file. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( basename( $filename ), null );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Insert the attachment.
		$attachment_id = wp_insert_attachment( $attachment, $filename );

		// Generate the metadata for the attachment, and update the database record.
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );

		// Update the attachment metadata.
		wp_update_attachment_metadata( $attachment_id, $attachment_data );

		return $attachment_id;
	}

}
