<a href="https://travis-ci.org/insideout10/wordlift-plugin"><img align="right" src="https://travis-ci.org/insideout10/wordlift-plugin.png?branch=wordlift-3.0" /></a>

WordLift Plug-in for WordPress
==============================

## Overview

The official WordLift Web Site: [wordlift.it](http://wordlift.it)

## Development

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
 * in the WordPress configuration file `wp-config.php` file, set:
```php
define('WORDLIFT_DEVELOPMENT', '');
```

## Testing

Run tests by executing the command ```./run-tests.sh```