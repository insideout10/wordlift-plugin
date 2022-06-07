<?php

namespace Wordlift\Modules\Acf4so;

class Module {

	/**
	 * @var Installer
	 */
	private $installer;

	public function __construct( Installer $installer ) {
		$this->installer = $installer;
	}

	public function install_and_activate( $new_value, $old_value ) {
		if ( ! $new_value ) {
			return;
		}
		$this->installer->install();
		$this->installer->activate();

	}


	public function register_hooks() {
		add_action( 'wl_feature__change__entity-types-professional', [ $this, 'install_and_activate' ], 10, 2 );
		add_action( 'wl_feature__change__entity-types-business', [ $this, 'install_and_activate' ], 10, 2 );
	}

}