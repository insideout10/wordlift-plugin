<?php

class PodsField_WlEntity extends \PodsField_Pick {

	/**
	 * {@inheritdoc}
	 */
	public static $type = 'wlentity';


	public function setup() {
		static::$group = __( 'WordLift', 'pods' );
		static::$label = __( 'Entity', 'pods' );
		static::$type  = 'wlentity';
	}

	public function options() {
		$fallback_help = __( 'More details on our %s.', 'pods' );

		return array(
			static::$type . '_format_type'   => array(
				'label'                 => __( 'Selection Type', 'pods' ),
				'help'                  => $fallback_help,
				'default'               => 'single',
				'required'              => true,
				'type'                  => 'pick',
				'data'                  => array(
					'single' => __( 'Single Select', 'pods' ),
					'multi'  => __( 'Multiple Select', 'pods' ),
				),
				'pick_show_select_text' => 0,
				'dependency'            => true,
			),
			static::$type . '_format_single' => array(
				'label'                 => __( 'Input Type', 'pods' ),
				'help'                  => $fallback_help,
				'depends-on'            => array(
					static::$type . '_format_type' => 'single',
				),
				'default'               => 'autocomplete',
				'required'              => true,
				'type'                  => 'pick',
				'data'                  => apply_filters(
					'pods_form_ui_field_pick_format_single_options',
					array(
						'autocomplete' => __( 'Autocomplete', 'pods' ),

					)
				),
				'pick_show_select_text' => 0,
				'dependency'            => true,
			),
		);
	}


}
