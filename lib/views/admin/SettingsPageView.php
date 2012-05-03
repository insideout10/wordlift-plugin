<?php

/**
 * Displays the settings page.
 * @service options_page
 * @title WordLift settings
 * @menu WordLift
 * @capability manage_options
 * @slug wordlift-20-settings
 * @callback display
 */
class SettingsPageView implements IView {
	
	const CLASS_NAME = 'settings-page-view';
	
	/**
	 * Initializes a new class instance.
	 */
	function __construct() {
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IView::getContent()
	 */
	public function getContent($content=null) {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		
		
		return <<<EOD

		<div class="wrap">
		
			<h2>WordLift settings</h2>
			
			<div class="settings-page-view">
			
				<form method="post">
			
				<div class="field">
				<label for="wordlift-server-url">WordLift Server URL:</label>
				<input name="wordlift-server-url" type="text" />
				</div>
				
				<div class="field">
				<label for="wordlift-server-username">Username</label>
				<input name="wordlift-server-username" type="text" />
				</div>
				
				<div class="field">
				<label for="wordlift-server-password">Password</label>
				<input name="wordlift-server-password" type="password" />
				</div>
				
				<div class="field">
				<label for="wordlift-server-site-id">Site ID</label>
				<input name="wordlift-server-site-id" type="text" />
				</div>
				
				<div class="field">
				<label for="wordlift-server-analysis-configuration">Analysis Configuration</label>
				<input name="wordlift-server-analysis-configuration" type="text" />
				</div>
				
				<input value="Save" type="submit" />
				
				</form>

			</div>
			
		</div>
		
EOD;

	}
	
	/**
	 * Prints out the generated content.
	 */
	public function display() {

		echo $this->getContent();
	}
	
}
?>