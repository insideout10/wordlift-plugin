<a href="https://travis-ci.org/insideout10/wordlift-plugin"><img align="right" src="https://travis-ci.org/insideout10/wordlift-plugin.png?branch=wordlift-3.0" /></a>

WordLift Plug-in for WordPress
==============================

## Overview

The official WordLift Web Site: [wordlift.it](http://wordlift.it)

## Development

bash bin/install-wp-tests.sh wordpress_test root '' localhost 3.8

### About GIT

http://git-scm.com/book

### Versioning

We use [Semantic Versioning](http://semver.org/). We add the *SNAPSHOT* suffix to releases that are not yet usable.

### Coding Standard

We try to stick to the [WordPress coding standards](http://make.wordpress.org/core/handbook/coding-standards/php/) as much as possible.

### Redlink Account

To join development you first need an account on [Redlink](http://redlink.co) with an Application Key. Follow these steps to get one:

* Register an account on [my.redlink.io](http://my.redlink.io) and login
  * from the **datasets** section, create a custom dataset
    * then release the dataset to *yourself* in *RDFS* format
    * publish the dataset
  * from the **analyses** section, create an analysis, then configure it with the following components:
    * **input**, use *plain text*
    * **languages**: select the *expert language pack*
    * **datasets**: select the dataset you created earlier and *dbpedia* and *freebase*
    * **post-processing**: select *entity co-mention* and *dereference entities*
    * **outputs**: choose *RDF output*
  * apply changes to the analysis settings by clicking the *Apply* button
  * from the **applications** section create an application, then bind
    * the dataset you created earlier
    * the analysis you created earlier
  * take note of the *Application Key* and of the *User ID* (the user ID appears in the dataset base URI, e.g.: http://data.redlink.io/<user-id>/<dataset-name>).

### WordLift Plugin

Get the code and set-up a WordPress instance:

 * Clone the code to a folder: ```git clone https://github.com/insideout10/wordlift-plugin.git```
 * from a WordPress instance, create a symbolic link to the plugin *src* folder: ```ln -s wordlift-plugin/src wordpress/wp-content/plugins/wordlift```

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

### Example Apache configuration

This is an example Apache configuration for a local web site instance pointing at the WordLift JavaScript files:

```
NameVirtualHost *:80

<VirtualHost *:80>
	ServerName wordpress.localhost

	RewriteEngine on

	DocumentRoot /var/www
	DirectoryIndex index.php

	<Location ~ "/wp-content/plugins/wordlift/(css|fonts|js)/(.+)$">
		ProxyPassMatch http://localhost:8000/app/$1/$2
	</Location>

	<Directory /var/www>
		AllowOverride All
		Order allow,deny
		Allow from All
	</Directory>

</VirtualHost>
```

## Testing

Run tests by executing the command ```./run-tests.sh```