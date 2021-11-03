<?php

?>
        <? settings_errors( 'wl_webhook_error' ); ?>
        <form method="post" action="options.php">
            <?php settings_fields('wl_webhooks_settings'); ?>
            <table>
                <tr>
                </tr>
                <tr>
                    <td>Enter the Webhook URL</td>
                    <td><input type="text" name="wl_webhook_url" value="https://" /></td>
                    <td colspan="2"><?php echo submit_button( "Add" ); ?></td>
                </tr>
            </table>
        </form>
