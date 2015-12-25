=== WordLift - Ordering Knowledge ===
Author URL: https://blog.insideout.io/about-us
Plugin URL: https://join.wordlift.it
Contributors: wordlift, ziodave
Tags: artificial intelligence, semantic editor, linked open data, structured data, content recommendation, knowledge graph, seo,schema.org, google rich snippets, interactive widgets, apache stanbol, iks, semantic web, wikipedia
Requires at least: 4.2
Tested up to: 4.3.1
Stable tag: {version}
License: GPLv2 or later

WordLift brings the power of Artificial Intelligence to WordPress. Beautifully helps you reach your maximum potential audience.

== Description ==

> #### WordLift - Activating the Plugin
> To activate the plugin you will need a WordLift key. <br />
> Please signup to [join.wordlift.it](http://join.wordlift.it) and we will get in contact with you to send you the key. <br /> 

**WordLift** is a WordPress Plug-in to organise post and pages adding facts, text and media to build beautifully structured web sites.
**WordLift** publishes your content as [Linked Open Data](http://docs.wordlift.it/en/latest/key-concepts.html#linked-open-data) following [Tim Berners-Lee‘s Linked Data Principles](http://www.w3.org/DesignIssues/LinkedData.html).
**WordLift** is a **semantic editor** for WordPress.

= Features =

**WordLift** adds [semantic annotations](http://docs.wordlift.it/en/latest/key-concepts.html#semantic-fingerprint) and combines information publicly available as [linked open data](http://docs.wordlift.it/en/latest/key-concepts.html#linked-open-data) to support the editorial workflow by suggesting relevant information, images and links.

= WordLift brings to content editors =
_____________

* support for **self-organising** (or structuring) **contents** using publicly (or privately) available [knowledge graphs](http://docs.wordlift.it/en/latest/key-concepts.html#knowledge-graph)
* an easy way to **build a dataset** made of *web content*, *semantic annotations* and a *custom vocabulary* 
* support for creating web content using **contextually relevant fact-based information**
* valued and **free to use photos and illustrations** from the Commons community ranging from maps to astronomical imagery to photographs, artworks and more
* insightful **visualisations to engage the reader**
* new means to drive business growth with **meaningful content discovery paths**
* content tagging for **better SEO**

= Websites built with WordLift bring to readers =
_____________

* multiple means of searching and accessing **editorial contents around a specific topic** 
* **contextual information** helping readers with limited domain understanding
* an **intuitive overview of all content being written** *on the site* and *around a specific topic* or graph of topics
* meaningful **content recommendations** 

WordLift currently supports the following languages: English, 中文 (Chinese), Español (Spanish), Русский (Russian), Português (Portuguese), Deutsch (German), Italiano (Italian), Nederlands (Dutch), Svenska (Swedish) and Dansk (Danish). 

The Plug-in is powered by [RedLink](http://redlink.co): Europe's *open source* largest platform for semantic enrichment and search. 

== Installation ==

1. Upload `wordlift.zip` to the `/wp-content/plugins/` directory
2. Extract the files in the wordlift subfolder
3. Activate the plug-in using a [WordLift key](http://docs.wordlift.it/en/latest/key-concepts.html#wordlift-key). You might receive this key from us or from an automatic email system. Once you have received the key go to the WordPress administration menu, click on Plugins / Installed Plugins. Then click on Settings on the WordLift plugin and add the key there. 

> #### WordLift - Activating the Plugin
> To activate the plugin you will need a WordLift key. <br />
> Please signup to [join.wordlift.it](http://join.wordlift.it) and we will get in contact with you to send you the key. <br /> 

== Frequently Asked Questions ==

= Why shall I use WordLift? = 

The purpose of using WordLift is to (1) categorize your content, (2) help people find content of interest to them, and (3) help WordLift describe your contents in *machine-readable* format so that other computers can re-use it. 

= Why shall I publish my contents as Linked Data? =

Richer metadata helps making content discoverable, searchable, and provides new means to reaching your audience.
Organising web contents around concepts or entities rather than traditional web pages helps improve navigation, content re-use, content re-purposing and search engine rankings.

Having content aggregations based on semantic annotations that use unambiguous Linked Data identifiers creates a richer navigation bringing the user experience to new levels of engagement. 

More [Frequently Asked Questions](http://docs.wordlift.it/en/latest/faq.html) can be found on [docs.wordlift.it](http://docs.wordlift.it).  

== Screenshots ==

1. The slick [WordLift Edit Post Widget](http://docs.wordlift.it/en/latest/analysis.html#wordlift-edit-post-widget). 
2. The WordLift Edit Post Widget explained.
3. The WordLift Event Entity.
4. The WordLift Place Entity.
5. The [Navigator Widget](http://docs.wordlift.it/en/latest/discover.html#the-navigator-widget) providing content-recommendations.
6. The [Faceted Search Widget](http://docs.wordlift.it/en/latest/discover.html#the-faceted-search-widget).
7. The [Chord Widget](http://docs.wordlift.it/en/latest/discover.html#the-chord-widget)

== Changelog ==
= 3.2.5 (2015-12-25) =
* Fix: [#221](https://github.com/insideout10/wordlift-plugin/issues/221): Fix de-synch between Wordpress and RedLink when disambiguation is performed trough entity alternative title

= 3.2.4 (2015-12-13) =
* Fix: [#210](https://github.com/insideout10/wordlift-plugin/issues/210): Restore editable annotations to allow annotations formatting.
* Fix: [#194](https://github.com/insideout10/wordlift-plugin/issues/194): Add zero-width no-break space after each annotation to provide a caret container.

= 3.2.3 (2015-12-08) =
* Fix: [#202](https://github.com/insideout10/wordlift-plugin/issues/202): [PrimaShop](http://www.primathemes.com/products/primashop-for-woocommerce/) users may now enjoy header settings on entity pages.

= 3.2.2 (2015-12-07) =
* Fix: [#203](https://github.com/insideout10/wordlift-plugin/issues/203): alternative titles are spreading to related entities, fixed.

= 3.2.1 (2015-12-06) =
 * Fix: [#200](https://github.com/insideout10/wordlift-plugin/issues/200): Fix new entity form visibility with undefined current annotation.
 * Fix: [#194](https://github.com/insideout10/wordlift-plugin/issues/194): Make text annotations within tinymce editor not editable.

= 3.2.0 (2015-12-04) =
 * Enhancement: [#196](https://github.com/insideout10/wordlift-plugin/issues/196): renovate the Vocabulary icon with WordLift logo.
 * Enhancement: [#195](https://github.com/insideout10/wordlift-plugin/issues/195): re-enable title duplicates notices.
 * Enhancement: [#185](https://github.com/insideout10/wordlift-plugin/issues/185): cleaning up, remove the entity_view submodule.
 * Enhancement: [#184](https://github.com/insideout10/wordlift-plugin/issues/184): for the joy of the 100,000+ active installs, we're now compatible with the [ShareThis plugin](https://wordpress.org/plugins/share-this/).
 * Enhancement: [#181](https://github.com/insideout10/wordlift-plugin/issues/181): finally, you can add more titles to entities.
 * Enhancement: [#178](https://github.com/insideout10/wordlift-plugin/issues/178): some renovation, add WordLift in the naming of the Metabox.
 * Enhancement: [#177](https://github.com/insideout10/wordlift-plugin/issues/177): enjoy better admin notices.
 * Enhancement: [#176](https://github.com/insideout10/wordlift-plugin/issues/176): cleaning up, remove the option "color coding on front-end".
 * Enhancement: [#175](https://github.com/insideout10/wordlift-plugin/issues/175): cleaning up, remove SPARQL queries menu item.
 * Enhancement: [#174](https://github.com/insideout10/wordlift-plugin/issues/174): cleaning up, remove performance analysis menu item.
 * Enhancement: [#173](https://github.com/insideout10/wordlift-plugin/issues/173): cleaning up, remove WordLift upper-right corner icon.
 * Enhancement: [#170](https://github.com/insideout10/wordlift-plugin/issues/170): disable entity editing in disambiguation widget for internal entities.
 * Enhancement: [#159](https://github.com/insideout10/wordlift-plugin/issues/159): enable both date and datetime fields for the metabox.
 * Enhancement: [#149](https://github.com/insideout10/wordlift-plugin/issues/149): add email and organization properties to Person.
 * Enhancement: [#143](https://github.com/insideout10/wordlift-plugin/issues/143): it is now possible to specify many additional properties for addresses.
 * Fix: [#189](https://github.com/insideout10/wordlift-plugin/issues/189): fix entity recognition when bullet points are used.
 * Fix: [#122](https://github.com/insideout10/wordlift-plugin/issues/122): fire related posts loading on disambiguation widget loading.

= 3.1.8 (2015-11-30) =
 * Fix: [#192](https://github.com/insideout10/wordlift-plugin/issues/192): fix coordinates metabox field's HTML.

= 3.1.7 (2015-11-22) =
 * Fix: [#150](https://github.com/insideout10/wordlift-plugin/issues/150): the property schema-org:author on blog post lod view goes on error.

= 3.1.6 (2015-11-21) =
 * Fix: [#124](https://github.com/insideout10/wordlift-plugin/issues/124): entity featured image updating is not properly triggered on the triple store (fix tests and apply only to published posts).

= 3.1.5 (2015-11-21) =
 * Fix: [#124](https://github.com/insideout10/wordlift-plugin/issues/124): entity featured image updating is not properly triggered on the triple store.

= 3.1.4 (2015-11-20) =
 * Fix: [#183](https://github.com/insideout10/wordlift-plugin/issues/183): new text for the admin notice regarding a missing WL key.

= 3.1.3 (2015-11-18) =
 * Fix: [#179](https://github.com/insideout10/wordlift-plugin/issues/179): faceted search not running

= 3.1.2 (2015-11-16) =
 * Fix: [#104](https://github.com/insideout10/wordlift-plugin/issues/104): cannot load more than one navigator on the same page.

= 3.1.1 (2015-11-16) =
 * Fix: [#112](https://github.com/insideout10/wordlift-plugin/issues/112): chord tooltip has white background and black font to avoid themes conflicting with the widget.

= 3.1.0 (2015-11-13) =
 * Enhancement: [#145](https://github.com/insideout10/wordlift-plugin/issues/145): control new entities creation from metaboxes.
 * Enhancement: [#134](https://github.com/insideout10/wordlift-plugin/issues/134): scripts and styles source repository merged with PHP repository.
 * Enhancement: [#57](https://github.com/insideout10/wordlift-plugin/issues/57): on the Edit Entity page the referencing posts has been restored.
 * Fix: [#144](https://github.com/insideout10/wordlift-plugin/issues/144): changing type on WordLift doesn't reset the property list on Redlink is now fixed.
 * Fix: [#141](https://github.com/insideout10/wordlift-plugin/issues/141): properties when published to Redlink have no links (and no meaning) is now fixed.
 * Fix: [#139](https://github.com/insideout10/wordlift-plugin/issues/139): single founder while expecting multiple founders is now fixed.
 * Fix: [#138](https://github.com/insideout10/wordlift-plugin/issues/138): Uncaught TypeError: Cannot read property 'id' of undefined is now fixed.
 * Fix: [#169](https://github.com/insideout10/wordlift-plugin/issues/169): entities that are not events may be displayed in the timeline.
 * Fix: [#168](https://github.com/insideout10/wordlift-plugin/issues/168): WordPress shortcodes are displayed in timelines.
 * Fix: [#167](https://github.com/insideout10/wordlift-plugin/issues/167): schema.org markup is wrong for implicit contents.
 * Fix: [#166](https://github.com/insideout10/wordlift-plugin/issues/166): latitude and longitude are set to zero when not specified.
 * Fix: [#165](https://github.com/insideout10/wordlift-plugin/issues/165): entity type is lost in quickedit mode.
 * Fix: [#164](https://github.com/insideout10/wordlift-plugin/issues/164): timeline widget is showing unrelated events.
 * Fix: [#163](https://github.com/insideout10/wordlift-plugin/issues/163): incorrect markup for events' locations.
 * Fix: [#162](https://github.com/insideout10/wordlift-plugin/issues/162): only dates are stored for startDate/endDate properties in linked data.

= 3.0.16 (2015-11-11) =
 * Fix: [#152](https://github.com/insideout10/wordlift-plugin/issues/152): Entity description update from disambiguation widget is now disabled in order to prevent existing entity pages content overriding.

= 3.0.15 (2015-11-10) =
 * Fix: [#135](https://github.com/insideout10/wordlift-plugin/issues/135): Sanitize filename in order to properly save entity images as entity post attachments.

= 3.0.14 (2015-11-09) =
 * Fix: [#156](https://github.com/insideout10/wordlift-plugin/issues/156): Yoast compatibility issue which caused meta values to be copied to new entities created within a post is now solved.
 * Fix: [#148](https://github.com/insideout10/wordlift-plugin/issues/148): SEO Ultimate compatibility issue which caused meta values to be copied to new entities created within a post is now solved.

= 3.0.13 (2015-10-30) =
 * Fix: [#128](https://github.com/insideout10/wordlift-plugin/issues/128): now hashes in the text do not break anymore the annotations embedding after analysis execution.
 * Fix: [#95](https://github.com/insideout10/wordlift-plugin/issues/95): WordPress image edit controls disappears after installing WordLift is now fixed.

= 3.0.12 (2015-10-23) =
 * Enhancement: [#85](https://github.com/insideout10/wordlift-plugin/issues/85): now structured data are added in the entity pages for the current entity itself
 * Fix: [#128](https://github.com/insideout10/wordlift-plugin/issues/128): now hashes in the text do not break anymore the annotations embedding after analysis execution
 * Fix: [#96](https://github.com/insideout10/wordlift-plugin/issues/96): garbage response from api is no more returned

= 3.0.11 (2015-10-14) =
 * Enhancement: 'View Linked Data' button to visualize RDF triples with [LodView](https://github.com/dvcama/LodView)

= 3.0.10 (2015-10-14) =
  * Fix [#119](https://github.com/insideout10/wordlift-plugin/issues/119). Now public entities status is properly preserved when linked to draft posts.
  * Fix: install script in order to use branch-specific WP unit tests libs

= 2.6.19 =
 * Fix: [issue 13](https://github.com/insideout10/wordlift-plugin/issues/13): authorship tagging is now shown only on single pages and posts (thanks to Kevin Polley)

= 2.6.18 =
 * Enhancement: Twitter authentication is now back.

= 2.6.17 =
 * Fix: change regular expression to add image itemprops for In-Depth articles to avoid conflicts with linked images and plugins such Nav Menu Images (thanks to Lee Hodson).

= 2.6.16 =
 * Fix: removed useless references to jQuery UI libraries and conflicting CSS (thanks to Lee Hodson).

= 2.6.15 =
 * Fix: PHP warning in RecordSetService (thanks to Kevin Polley),
 * Fix: image alt attributes were incorrectly highlighted with entities (thanks to Lee Hodson).

= 2.6.14 =
 * Fix: post thumbnail html output even if there's no thumbnail.
 * Fix: adding schema.org title using the_title filter could cause issues with theme that use this function for the img tag alt attribute value.
 * Enhancement: add support for DW Focus theme.

= 2.6.13 =
 * Fix: overlap with Facebook admin menu.

= 2.6.12 =
 * Fix: enable authorship information only for regular posts (post type 'post').

= 2.6.11 =
 * Fix: the entity page might appear in the primary menu with some themes (e.g. Twenty Thirteen).
 * Fix: the entity page called without an entity parameter would return a warning.
 * Fix: a warning might appear in the entity page.

= 2.6.10 =
 * Fix: temporary disabled twitter authentication due to API changes.

= 2.6.9 =
 * Improvement: add better support for is_single call.

= 2.6.8 =
 * Other: fix repository versioning.

= 2.6.7 =
 * Fix: html tagging in the title did cause issues when the post title is being used as an html attribute.

= 2.6.6 =
 * Other: add new keywords.

= 2.6.5 =
 * Other: add compatibility up to WordPress 3.6.

= 2.6.4 =
 * Fix: fix a bug that would cause the interaction count to show up in the page title.
 * Fix: ensure adding schema.org mark-up happens only in single post views.

= 2.6.2 =
 * Fix: fix a bug that would cause rewrite rules to be incomplete (WordPress Framework).

= 2.6.1 =
 * Feature: add option to disable *In-Depth* features.

= 2.6.0 =
 * Feature: add new *In-Depth* features.

= 2.5.33 =
 * "Registration failed: undefined (undefined)": Fixed a configuration setting that didn't allow some blogs to register to WordLift Services. (Many thanks to http://www.pruk2digital.com/ for helping us out finding this error).

= 2.5.32 =
 * Added initial compatibility with WordPress 3.6 beta 1,
 * Fix an issue that displayed entities alway for the most recent post.

= 2.5.31 =
 * Fixed a 'notice' in the WordLift Bar,
 * Changed the WordLift Bar to show entities from the most recent post in the
   home page,
 * Added HTML encoding of entity data on the WordLift Bar.

= 2.5.30 =
 * WordLift Bar stays hidden for screen width <= 320px.

= 2.5.29 =
 * WordLift Bar hides/shows automatically when the page is scrolled down.

= 2.5.28 =
 * readme updated with links to WordLift Bar samples.

= 2.5.27 =
 * Now featuring the experimental WordLift Bar.

= 2.5.26 =
 * Cloud Services address changed to use standard ports to ease WordPress installations behind firewalls or proxies.

= 2.5.7 =
 * Major release with fixes on the user registration.

= 1.6 =
 * Fixed an issue that would prevent the plug-in from working. This upgrade is strongly recommended.

= 1.5 =
 * Fixed an issue that would block the plug-in when discovering corrupted type formats.
   (NOTE: this version does not work, please upgrade to 1.6)

= 1.4 =
 * Fixed some compatibility issues with Internet Explorer.

= 1.3 =
 * Added support for WordPress 3.0.x

= 1.2 =
 * The entity elements are now hidden by default.

= 1.1 =
* Removed the requirement for a logs folder

= 1.0 =
* First public release

== Upgrade Notice ==

= 1.0 =
* First public release

== More Information ==
WordLift is **happily developed** by [InSideOut10](http://blog.insideout.io/about-us).

[InSideOut10](http://blog.insideout.io/about-us) delivers strategic digital communication tools for enterprises and organisations. 

[InSideOut10](http://blog.insideout.io/about-us) uses artificial intelligence and semantic networks to collect, analyse and link relevant contents with data.

WordLift infrastructure runs on the semantic platform of [Redlink](http://redlink.co). 

[Redlink](http://redlink.co) is commercial spin-off based in Salzburg, Austria focused on *Semantic Technologies* and *Free Open Source Software* that has been co-founded by [InSideOut10](http://blog.insideout.io/about-us) in 2013.
