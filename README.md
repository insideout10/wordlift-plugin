<div style="float: right"><a href="https://travis-ci.org/insideout10/wordlift-plugin"><img src="https://travis-ci.org/insideout10/wordlift-plugin.png?branch=wordlift-3.0" /></a></div>

WordLift Plug-in for WordPress
==============================

## Overview

The official WordLift Web Site: [wordlift.it](http://wordlift.it)

### Features

* Post/Page semantic analysis.

## Versioning

We use [Semantic Versioning](http://semver.org/). We add the *SNAPSHOT* suffix to releases that are not yet usable.

## Coding Standard

We try to stick to the [WordPress coding standards](http://make.wordpress.org/core/handbook/coding-standards/php/) as much as possible.

## Development

To enable development from a directory which is sym-linked to a wp-content/plugins/wordlift directory in WordPress set the following in the `wp-config.php` file:
```php
define('WORDLIFT_DEVELOPMENT', '');
```

### Dependencies

#### Grunt dependencies

```sh
npm install grunt-contrib-coffee --save-dev
npm install grunt-contrib-less --save-dev
npm install grunt-contrib-uglify --save-dev
npm install clean-css
```