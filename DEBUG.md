### Enable WordPress debug mode

Edit the `wp-config.php` file of your WordPress instance and set the debug mode. We use to write the log to the `wp-content/debug.log` file - see [Example wp-config.php for Debugging](https://codex.wordpress.org/Debugging_in_WordPress#Example_wp-config.php_for_Debugging):
```php
// Enable WP_DEBUG mode
define('WP_DEBUG', true);

// Enable Debug logging to the /wp-content/debug.log file
define('WP_DEBUG_LOG', true);

// Disable display of errors and warnings
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors',0);

// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
define('SCRIPT_DEBUG', true);
```
