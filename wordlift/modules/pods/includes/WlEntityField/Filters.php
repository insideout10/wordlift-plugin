<?php

namespace Wordlift\Modules\Pods\WlEntityField;

use Wordlift\Entity\Query\Entity_Query_Service;
use Wordlift\Object_Type_Enum;

class Filters {

	const FIELD_NAME = 'wlentity';

	public function __construct() {

		pods_register_related_object( self::FIELD_NAME, 'WordLift Entity', array( 'simple' => false ) );
		add_filter( 'pods_form_ui_field_pick_ajax', array( $this, 'ajax_filter' ), 10, 4 );
		add_filter( 'pods_api_get_table_info', array( $this, 'table_info_filter' ), 10, 6 );
		add_filter( 'pods_field_pick_object_data', array( $this, 'field_options_filter' ), 10, 7 );
		add_filter( 'pods_field_dfv_data', array( $this, 'data_filter' ), 10, 2 );
		add_filter( 'pods_field_pick_data_ajax', array( $this, 'admin_ajax_filter' ), 10, 4 );

		add_action(
			'pods_meta_save_taxonomy',
			function ( $data, $pod, $id, $groups, $term_id ) {
				$this->save_field( 'term', $term_id, $groups );
			},
			10,
			5
		);

		add_action(
			'pods_meta_save_post',
			function ( $data, $pod, $id, $groups ) {
				$this->save_field( 'post', $id, $groups );
			},
			10,
			4
		);

	}

	private function save_field( $type, $identifier, $groups ) {

		$entity_fields = $this->filter_entity_fields( $groups );

		foreach ( $entity_fields as $entity_field ) {
			delete_metadata( $type, $identifier, $entity_field );
			$key = sprintf( 'pods_meta_%s', $entity_field );

			$data = filter_var_array( $_REQUEST, array( $key => array( 'flags' => FILTER_REQUIRE_ARRAY ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! $data ) {
				continue;
			}
			$values = $data[ $key ];

			foreach ( $values as $value ) {
				add_metadata( $type, $identifier, $entity_field, $value );
			}
		}

	}

	private function filter_entity_fields( $groups ) {

		$pods = json_decode( wp_json_encode( $groups ), true );

		$fields = array_reduce(
			$pods,
			function ( $carry, $item ) {
				return array_merge( $carry, $item['fields'] );
			},
			array()
		);

		return array_map(
			function ( $item ) {
				return $item['name'];
			},
			array_filter(
				$fields,
				function ( $item ) {
					return is_array( $item ) && isset( $item['pick_object'] ) && self::FIELD_NAME === $item['pick_object'];
				}
			)
		);
	}

	public function wl_pods_transform_data_for_pick_field( $item ) {

		$content = $item->get_content();

		return array(
			'id'        => sprintf( '%s_%d', Object_Type_Enum::to_string( $content->get_object_type_enum() ), $content->get_id() ),
			'icon'      => 'https:\/\/wordlift.localhost\/wp-content\/plugins\/wordlift\/images\/svg\/wl-vocabulary-icon.svg',
			'name'      => $item->get_title() . ' (' . $item->get_schema_type() . ')',
			'edit_link' => $content->get_edit_link(),
			'link'      => $content->get_permalink(),
			'selected'  => false,
		);
	}

	public function admin_ajax_filter( $data, $name, $_, $field ) {

		if ( ( ! $field instanceof \Pods\Whatsit\Field ) || $field->get_arg( 'pick_object', false ) !== self::FIELD_NAME ) {
			return $data;
		}

		$query         = sanitize_text_field( wp_unslash( isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$query_service = Entity_Query_Service::get_instance();

		return array_map(
			array(
				$this,
				'wl_pods_transform_data_for_pick_field',
			),
			$query_service->query( $query, $field->get_arg( 'supported_schema_types', array( 'Thing' ) ) )
		);

	}

	public function data_filter( $data, $args ) {

		$args_arr   = json_decode( wp_json_encode( $args ), true );
		$field_data = $args_arr['options'];

		if ( ! isset( $field_data['pick_object'] ) || self::FIELD_NAME !== $field_data['pick_object'] ) {
			return $data;
		}

		if ( ! isset( $args_arr['pod']['data']['pod_data']['type'] )
			 || ! is_string( $args_arr['pod']['data']['pod_data']['type'] ) ) {
			return $data;
		}

		$name       = $field_data['name'];
		$identifier = $args->id;
		$type       = $args_arr['pod']['data']['pod_data']['type'];

		if ( 'post_type' === $type ) {
			$data['fieldValue'] = get_post_meta( $identifier, $name );
		} elseif ( 'taxonomy' === $type ) {
			$data['fieldValue'] = get_term_meta( $identifier, $name );
		}

		return $data;
	}

	public function ajax_filter( $result, $name, $value, $field_options ) {

		if ( ! isset( $field_options['pick_object'] ) ) {
			return $result;
		}

		return self::FIELD_NAME === $field_options['pick_object'];
	}

	public function table_info_filter( $info, $object_type, $object, $name, $pod, $field ) {

		if ( $field === null || self::FIELD_NAME !== $field->get_arg( 'pick_object', false ) ) {
			return $info;
		}
		// We need to return an non empty array here to prevent pods from querying a table.
		// This is necessary to prevent errors on ui.
		return array( 'foo' => 'bar' );
	}

	public function field_options_filter( $_, $name, $value, $options, $pod, $id, $object_params ) {

		$object_params = json_decode( wp_json_encode( $object_params ), true );

		$query_service = Entity_Query_Service::get_instance();

		if ( is_array( $object_params ) && isset( $object_params['options']['pick_object'] )
			 && is_string( $object_params['options']['pick_object'] )
			 && self::FIELD_NAME === $object_params['options']['pick_object']
			 && isset( $object_params['pod']['data']['pod_data']['type'] )
			 && is_string( $object_params['pod']['data']['pod_data']['type'] ) ) {

			$type            = $object_params['pod']['data']['pod_data']['type'];
			$linked_entities = array();
			if ( 'post_type' === $type ) {
				$linked_entities = get_post_meta( $id, $name );
			} elseif ( 'taxonomy' === $type ) {
				$linked_entities = get_term_meta( $id, $name );
			}
			$data            = array();
			$linked_entities = $query_service->get( $linked_entities );
			foreach ( $linked_entities as $linked_entity ) {
				$content     = $linked_entity->get_content();
				$id          = sprintf( '%s_%d', Object_Type_Enum::to_string( $content->get_object_type_enum() ), $content->get_id() );
				$text        = $linked_entity->get_title() . ' (' . $linked_entity->get_schema_type() . ')';
				$data[ $id ] = $text;
			}

			return $data;
		}

		return $_;

	}

}
