<?php

namespace Wordlift\Admin;

class Installation_Complete_Notice {


	public function __construct() {


		add_action( 'wordlift_admin_notices', function () {
			?>
            <div class="updated">
                <H3><?php echo WORDLIFT_PLUGIN_DATA['Name']; ?> <?php esc_html_e( 'has been successfully installed on your site!' ); ?></H3>
                <p><?php esc_html_e( 'we\'re now automatically enriching the structured data on your posts to create the best representation of your content that search engines will understand. Time to look forward to an increase in organic traffic!', 'wordlift' ); ?></p>
                <p><u><a href="#"><?php esc_html_e('Dismiss', 'wordlift');?></a></u></p>
            </div>
			<?php
		} );


	}


}