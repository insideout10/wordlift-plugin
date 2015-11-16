<a href="https://travis-ci.org/insideout10/wordlift-plugin"><img align="right" src="https://travis-ci.org/insideout10/wordlift-plugin.png?branch=develop" /></a><br/>
<a href="https://codeclimate.com/github/insideout10/wordlift-plugin"><img align="right" src="https://codeclimate.com/github/insideout10/wordlift-plugin/badges/gpa.svg" /></a><br/>
<a href="https://codeclimate.com/github/insideout10/wordlift-plugin/coverage"><img align="right" src="https://codeclimate.com/github/insideout10/wordlift-plugin/badges/coverage.svg" /></a>

WordLift Plug-in for WordPress
==============================

## Overview

The official WordLift Web Site: [wordlift.it](http://wordlift.it)

## Development

bash bin/install-wp-tests.sh wordpress_test root '' localhost latest

### About GIT

http://git-scm.com/book

### Versioning

We use [Semantic Versioning](http://semver.org/). We add the *dev* suffix to releases that are not yet usable.

### Coding Standard

We try to stick to the [WordPress coding standards](http://make.wordpress.org/core/handbook/coding-standards/php/) as much as possible.

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
