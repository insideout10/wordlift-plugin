<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * This class registers the acf fields required for embedded video data.
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject;

class Acf_Fields {

	public function __construct() {

		if ( function_exists( 'acf_add_local_field_group' ) ):

			acf_add_local_field_group( array(
				'key'                   => 'group_5f4641d61a259',
				'title'                 => 'Embedded video data',
				'fields'                => array(
					array(
						'key'               => 'field_5f464265ade55',
						'label'             => 'video',
						'name'              => 'video',
						'type'              => 'repeater',
						'instructions'      => 'This data in these fields are added automatically when you embed a video from vimeo, dont add the data manually in these fields.',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'collapsed'         => '',
						'min'               => 0,
						'max'               => 0,
						'layout'            => 'block',
						'button_label'      => '',
						'sub_fields'        => array(
							array(
								'key'               => 'field_5f464288ade56',
								'label'             => 'name',
								'name'              => 'name',
								'type'              => 'text',
								'instructions'      => 'The title of the video',
								'required'          => 1,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
							array(
								'key'               => 'field_5f4642caade57',
								'label'             => 'description',
								'name'              => 'description',
								'type'              => 'textarea',
								'instructions'      => 'The description of the video. HTML tags are ignored.',
								'required'          => 1,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'maxlength'         => '',
								'rows'              => '',
								'new_lines'         => '',
							),
							array(
								'key'               => 'field_5f4642e1ade58',
								'label'             => 'upload_date',
								'name'              => 'upload_date',
								'type'              => 'text',
								'instructions'      => 'The date the video was first published, in ISO 8601 format',
								'required'          => 1,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
							array(
								'key'               => 'field_5f4643c7ade5c',
								'label'             => 'thumbnail_url',
								'name'              => 'thumbnail_url',
								'type'              => 'repeater',
								'instructions'      => 'A URL pointing to the video thumbnail image file.',
								'required'          => 1,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'collapsed'         => '',
								'min'               => 0,
								'max'               => 0,
								'layout'            => 'table',
								'button_label'      => '',
								'sub_fields'        => array(
									array(
										'key'               => 'field_5f4643deade5d',
										'label'             => 'url',
										'name'              => 'url',
										'type'              => 'url',
										'instructions'      => '',
										'required'          => 1,
										'conditional_logic' => 0,
										'wrapper'           => array(
											'width' => '',
											'class' => '',
											'id'    => '',
										),
										'default_value'     => '',
										'placeholder'       => '',
									),
									array(
										'key'               => 'field_5f4643fbade5e',
										'label'             => 'width',
										'name'              => 'width',
										'type'              => 'number',
										'instructions'      => '',
										'required'          => 1,
										'conditional_logic' => 0,
										'wrapper'           => array(
											'width' => '',
											'class' => '',
											'id'    => '',
										),
										'default_value'     => '',
										'placeholder'       => '',
										'prepend'           => '',
										'append'            => '',
										'min'               => '',
										'max'               => '',
										'step'              => '',
									),
									array(
										'key'               => 'field_5f46440fade5f',
										'label'             => 'height',
										'name'              => 'height',
										'type'              => 'number',
										'instructions'      => '',
										'required'          => 1,
										'conditional_logic' => 0,
										'wrapper'           => array(
											'width' => '',
											'class' => '',
											'id'    => '',
										),
										'default_value'     => '',
										'placeholder'       => '',
										'prepend'           => '',
										'append'            => '',
										'min'               => '',
										'max'               => '',
										'step'              => '',
									),
								),
							),
							array(
								'key'               => 'field_5f464312ade59',
								'label'             => 'content_url',
								'name'              => 'content_url',
								'type'              => 'url',
								'instructions'      => 'A URL pointing to the actual video media file, in one of the supported encoding formats. Don\'t link to the page where the video lives; this must be the URL of the video media file itself.',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
							),
							array(
								'key'               => 'field_5f464364ade5a',
								'label'             => 'duration',
								'name'              => 'duration',
								'type'              => 'text',
								'instructions'      => 'The duration of the video in ISO 8601 format. For example, T00H30M5S represents a duration of "thirty minutes and five seconds".',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
							array(
								'key'               => 'field_5f46439eade5b',
								'label'             => 'embed_url',
								'name'              => 'embed_url',
								'type'              => 'url',
								'instructions'      => 'A URL pointing to a player for the specific video, in one of the supported encoding formats. Don\'t link to the page where the video lives; this must be the URL of the video media file itself. Usually this is the information in the src element of an <embed> tag.',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
							),
						),
					),
				),
				'location'              => Config::get_acf_location(),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			) );

		endif;
	}

}
