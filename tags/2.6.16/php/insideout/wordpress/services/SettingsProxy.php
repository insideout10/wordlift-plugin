<?php

class WordPress_SettingsProxy
{

	public $logger;

	private $pageTitle;
	private $menuSlug;
	private $sections;

	public function __construct() {
		add_action( "admin_init", array( $this, "registerSettings" ) );
	}

	public function setPageTitle( $pageTitle ) {
		$this->pageTitle = $pageTitle;
	}

	public function setMenuSlug( $menuSlug ) {
		$this->menuSlug = $menuSlug;
	}

	public function setSections( $sections ) {
		$this->sections = $sections;		
	}

	public function registerSettings() {

		foreach ( $this->sections as $section ) {
			add_settings_section( $section[ "id" ], $section[ "title" ], array( $this, "writeSection" ), $this->menuSlug );

			foreach ( $section[ "fields" ] as $field ) {
				$id = $field[ "id" ];
				add_settings_field( $id, $field[ "title" ], array( $this, "writeField" ), $this->menuSlug, $section[ "id" ], $field );
				register_setting( $this->menuSlug, $id );
			}
		}

	}

	public function writePage() {

		$htmlPageTitle = htmlentities( $this->pageTitle );

echo <<<EOF
	<div>
	<h2>$htmlPageTitle</h2>

		<form action="options.php" method="post">
EOF;

		settings_fields( $this->menuSlug );
 		do_settings_sections( $this->menuSlug );

echo <<<EOF

			<input name="Submit" type="submit" value="Save" />
		</form>
	</div>
EOF;

	}

	public function writeSection( $args ) {

	}

	public function writeField( $args ) {

		$id = $args[ "id" ];
		$htmlId = htmlentities( $id );
		$options = get_option( $id );
		$value = ( is_array( $options ) && array_key_exists( "text_string", $options ) ? $options[ "text_string" ] : "" );
		$htmlValue = htmlentities( $value );

		echo "<input id=\"$htmlId\" name=\"" . $htmlId . "[text_string]\" size=\"40\" type=\"text\" value=\"$htmlValue\" />";
	}

}

?>
