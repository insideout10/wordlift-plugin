// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', true );

// Disable display of errors and warnings
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
define( 'SCRIPT_DEBUG', true );

define( 'WL_DEBUG', WP_DEBUG );

// Set that we're in development mode. We use this to load the new Angular app from the localhost.
define( 'WL_ENV', 'dev' );

define( 'JETPACK_STAGING_MODE', true );
