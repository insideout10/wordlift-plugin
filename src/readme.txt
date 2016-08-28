=== WordLift - Ordering Knowledge ===
Author URL: https://wordlift.io
Plugin URL: https://wordlift.io
Contributors: wordlift
Tags: artificial intelligence, content recommendation, semantic editor, linked open data, structured data, knowledge graph, seo, semantic seo, schema.org, google rich snippets, google rich card, interactive widgets, apache stanbol, semantic web, wikipedia, data visualization, internal links, content discovery
Requires at least: 4.2
Tested up to: 4.5.3
Stable tag: {version}
License: GPLv2 or later

WordLift brings the power of Artificial Intelligence to beautifully organize content. Attract new readers and get their true attention. 

== Description ==

> #### WordLift - Activating the Plugin
> **WordLift** is a lightweight plugin that brings **state-of-the-art semantic technologies** to the hands of any bloggers and publishers. <br />
> **WordLift**, without requiring any technical skills, helps you produce richer content and organize it around your audience. <br />
> **WordLift** is **available to all for a monthly fee**. Find out more and [get your activation key](https://wordlift.io) directly on our website. <br />

[vimeo http://vimeo.com/164538710]

**WordLift** helps you organize posts and pages adding facts, links and media to build **beautifully structured websites**, for both humans and search engines. <br />
**WordLift** lets you create, own and publish your own [knowledge graph](http://docs.wordlift.it/en/latest/key-concepts.html#knowledge-graph).<br />
**WordLift** publishes your content as [Linked Open Data](http://docs.wordlift.it/en/latest/key-concepts.html#linked-open-data) following [Tim Berners-Lee‘s Linked Data Principles](http://www.w3.org/DesignIssues/LinkedData.html).<br />

= Features =

**WordLift** is a plug-in for online content creators to:

* Support your writing process with **trustworthy and contextual facts** <br />
* Enrich content with **images**, **links** and **interactive visualizations** <br />
* Keep readers engaged with relevant **content recommendations** <br />
* Produce content compatible with **schema.org markup**, allowing search engines to **best index and display your website**.  <br />
* Engage readers with **relevant content recommendations** <br />
* Help you create your own **personal Wikipedia** <br />
* Publish metadata to **share, sell and distribute content** <br />

= WordLift brings to your publishing workflow =
_____________


* The technology to **self-organize content** using publicly or privately available [knowledge graphs](http://docs.wordlift.it/en/latest/key-concepts.html#knowledge-graph) <br />
* An easy way to **build datasets** and **full data ownership** <br />
* Support for creating web content using **contextually relevant information** <br />
* Valued and **free to use photos and illustrations** from the Commons community ranging from maps to astronomical imagery to photographs, artworks and more <br />
* New means to drive business growth with **meaningful content discovery paths** <br />
* Content tagging for **better SEO** <br />

= Supported languages =
_____________

WordLift currently supports the following languages: English, 中文 (Chinese), Español (Spanish), Русский (Russian), Português (Portuguese), Deutsch (German), Italiano (Italian), Nederlands (Dutch), Svenska (Swedish) and Dansk (Danish). 

The Plug-in is built on **open source software**. 

== Installation ==

1. Upload `wordlift.zip` to the `/wp-content/plugins/` directory
2. Extract the files in the wordlift subfolder
3. Activate the plug-in using a [WordLift key](http://docs.wordlift.it/en/latest/key-concepts.html#wordlift-key). You receive this key from us after purchasing the monthly service from [our website](https://wordlift.io). Once you have received the key go to the WordPress administration menu, click on Plugins / Installed Plugins. Then click on Settings on the WordLift plugin and add the key there. 

> #### WordLift - Activating the Plugin
> To activate the plugin you will need a *WordLift key*. <br />
> **WordLift** is now available to all for a monthly fee. <br />
> Find out more and [get your activation key](https://wordlift.io) directly on our website. <br />

== Frequently Asked Questions ==

= Why shall I use WordLift? = 

Throwing content online without context and analysis simply doesn’t work when the focus for digital news is on *interactivity*, *engagement* and *community*. <br />
**WordLift** organizes knowledge, **reducing the complexity of content management and digital marketing operations**, letting bloggers and site owner **focus on stories and communities**. It offers meaningful *cross-media discovery* and *recommendations* that **increases content quality, exposure, trustworthiness and readership engagement**. <br />
**WordLift** also publishes *linked data* as a new way to syndicate content and create new business models. <br />


= How does it work? =

**WordLift** is a semantic editor and works in subsequent stages. The first step provides a full text analysis and suggests entities and relationships to the user, classifying contents according to concepts stored in the *semantic graph* (DBpedia, Wikidata, GeoNames, etc.) and using schema.org vocabulary. Textual contents are structured: they can now be processed by machines and be connected to other datasets. Users can then create new entities, to complement the entities suggested automatically and form a proprietary vocabulary, according to the editorial plan. <br />

**WordLift** provides means to record all these relationships in a graph database combining structured, semistructured and unstructured data, which allows queries like *“find all contents related to concept_y and relevant for target_z”*. <br />

Watch the [video tutorials](https://wordlift.io/#how-it-works) on our [website](https://wordlift.io). <br />

== Screenshots ==

1. The slick [WordLift Edit Post Widget](http://docs.wordlift.it/en/latest/analysis.html#wordlift-edit-post-widget). 
2. The WordLift Edit Post Widget explained.
3. The WordLift Event Entity.
4. The WordLift Place Entity.
5. The [Navigator Widget](http://docs.wordlift.it/en/latest/discover.html#the-navigator-widget) providing content-recommendations.
6. The [Faceted Search Widget](http://docs.wordlift.it/en/latest/discover.html#the-faceted-search-widget).
7. The [Chord Widget](http://docs.wordlift.it/en/latest/discover.html#the-chord-widget).
8. The WordLift Dashboard. Your [knowledge graph](http://docs.wordlift.it/en/latest/key-concepts.html#knowledge-graph) at a glance.

== Changelog ==

= 3.5.3 (2016-08-28) =
* Fix: [#262](https://github.com/insideout10/wordlift-plugin/issues/262): Posting a site URL on Google+ uses an entity title instead of the post title
* Fix: [#333](https://github.com/insideout10/wordlift-plugin/issues/333) Germanic umlaut causing troubles when saving sameAs links
* Fix: [#334](https://github.com/insideout10/wordlift-plugin/issues/334): New Entities/Thing created without the sameAs attribute are duplicated as Entities/Person

= 3.5.2 (2016-07-16) =
* Fix: [#285](https://github.com/insideout10/wordlift-plugin/issues/285): Avoid unexpected alerts after content disambiguation
* Fix: [#319](https://github.com/insideout10/wordlift-plugin/issues/319): Fix chord widget content filtering limit
* Fix: [#315](https://github.com/insideout10/wordlift-plugin/issues/315): Activation on large web sites fails

= 3.5.1 (2016-06-16) =
* Enhancement: [#312](https://github.com/insideout10/wordlift-plugin/issues/312): Reduce chord entities trashold to improve widget usability.

= 3.5.0 (2016-05-16) =
* Fix: [#300](https://github.com/insideout10/wordlift-plugin/issues/300): Ensure only published entities are returned as facets by faceted search widget.
* Fix: [#299](https://github.com/insideout10/wordlift-plugin/issues/299): Featured images are now properly updated on RL.
* Enhancement: [#295](https://github.com/insideout10/wordlift-plugin/issues/295): New UI refinements.
* Enhancement: [#297](https://github.com/insideout10/wordlift-plugin/issues/297): Detect classification scope from current entity type.
* Enhancement: [#284](https://github.com/insideout10/wordlift-plugin/issues/284): Disambiguation widget UI refactoring.
* Enhancement: [#289](https://github.com/insideout10/wordlift-plugin/issues/289): Introduced html static templates for angularjs layer components.
* Enhancement: [#288](https://github.com/insideout10/wordlift-plugin/issues/288): Removed selected entities tags from disambiguation widget.
* Enhancement: [#283](https://github.com/insideout10/wordlift-plugin/issues/283): Dbpedia topics are now mapped also on a custom taxonomy.
* Enhancement: [#229](https://github.com/insideout10/wordlift-plugin/issues/229): Add article classification. 
* Enhancement: [#294](https://github.com/insideout10/wordlift-plugin/issues/294): Fix disambiguation failure use case. 
* Enhancement: [#280](https://github.com/insideout10/wordlift-plugin/issues/280): Fix disambiguation failure use case. 
* Enhancement: [#279](https://github.com/insideout10/wordlift-plugin/issues/279): Disambiguation fixed for entities with escaped chars contained in the uri.
* Enhancement: [#276](https://github.com/insideout10/wordlift-plugin/issues/276): Fix facets layout with entities long titles.
* Enhancement: [#275](https://github.com/insideout10/wordlift-plugin/issues/275): Exclude posts without featured image from navigator results.
* Enhancement: [#274](https://github.com/insideout10/wordlift-plugin/issues/274): Simplify facets layout grid. 
* Enhancement: [#273](https://github.com/insideout10/wordlift-plugin/issues/273): Remove entity type icon and counter from facets within faceted search widget to simplify the layout.
* Enhancement: [#270](https://github.com/insideout10/wordlift-plugin/issues/270): Show wl-carousel controls on mouseover only.
* Enhancement: [#269](https://github.com/insideout10/wordlift-plugin/issues/269): Fix NaN in WL dashboard.
* Enhancement: [#268](https://github.com/insideout10/wordlift-plugin/issues/268): Flaoting configurable layout is now available both for navigator and faceted search.
* Enhancement: [#267](https://github.com/insideout10/wordlift-plugin/issues/267): Force override for entities with same schema type and label within disambiguation workflow.
* Enhancement: [#264](https://github.com/insideout10/wordlift-plugin/issues/264): Improve data selection strategy for navigation widget.
* Enhancement: [#253](https://github.com/insideout10/wordlift-plugin/issues/253): Introduce navigator and faceted search configuration.
* Enhancement: [#258](https://github.com/insideout10/wordlift-plugin/issues/258): Entity titles are now also published in the graph as dc:title. 
* Enhancement: [#147](https://github.com/insideout10/wordlift-plugin/issues/147): Navigator widget works also on entity pages. 
* Enhancement: [#232](https://github.com/insideout10/wordlift-plugin/issues/232): Navigator widget refactoring. 
* Enhancement: [#224](https://github.com/insideout10/wordlift-plugin/issues/224): Enable entity partial match in autocomplete.
* Enhancement: [#215](https://github.com/insideout10/wordlift-plugin/issues/215): Allow to create multiple entities with same label and different entity types safely (without any overlapping).  
* Enhancement: [#130](https://github.com/insideout10/wordlift-plugin/issues/130): Remove angularjs bower dependency. CDN is used instead. 

= 3.4.0 (2016-02-12) =
* Enhancement: [#263](https://github.com/insideout10/wordlift-plugin/issues/263): Sorting and smart auto-limit added for entities in faceted search widget.
* Enhancement: [#255](https://github.com/insideout10/wordlift-plugin/issues/255): Disable entity url editing. 
* Fix: [#251](https://github.com/insideout10/wordlift-plugin/issues/251): avoid entity duplication for entities with an updated label used in disambiguation.
* Fix: [#244](https://github.com/insideout10/wordlift-plugin/issues/244): Tinymce does not remain idle anymore switching between Visual and Text mode.
* Enhancement: [#233](https://github.com/insideout10/wordlift-plugin/issues/233): Add WordLift dashboard widget. 
* Enhancement: [#231](https://github.com/insideout10/wordlift-plugin/issues/231): Faceted search widget is now available also for standard posts 
* Enhancement: [#223](https://github.com/insideout10/wordlift-plugin/issues/223): Remove unavailable entity images from images suggestions.
* Enhancement: [#214](https://github.com/insideout10/wordlift-plugin/issues/214): Faceted search 4W revamp.
* Enhancement: [#180](https://github.com/insideout10/wordlift-plugin/issues/180): Enable minified js files for faceted search shortcode.
* Enhancement: [#115](https://github.com/insideout10/wordlift-plugin/issues/115): Filter out the current entity from the analysis results to avoid to link a given entity with itself.

= 3.3.5 (2016-02-10) =
* Fix: [#260](https://github.com/insideout10/wordlift-plugin/issues/260): Autosave disabled for entity posts to avoid unexpected entities duplication
* Fix: [#259](https://github.com/insideout10/wordlift-plugin/issues/259): Fix php notice on media library
* Fix: [#256](https://github.com/insideout10/wordlift-plugin/issues/256): Fix compatibility issue with truemag theme

= 3.3.4 (2016-02-06) =
* Fix: [#252](https://github.com/insideout10/wordlift-plugin/issues/252): Disable scrollInput on entities metaboxes datetimepickers
* Fix: [#248](https://github.com/insideout10/wordlift-plugin/issues/248): Include also LocalBusiness entities as suggestion for affiliation property for entities of type Person
* Fix: [#246](https://github.com/insideout10/wordlift-plugin/issues/246): Include also LocalBusiness entities as suggestion for location property for entities of type Event

= 3.3.3 (2016-01-17) =
* Fix: [#243](https://github.com/insideout10/wordlift-plugin/issues/243): Post status for published entities is properly preserved when used to disambiguate a post draft.

= 3.3.2 (2016-01-11) =
* Fix: [#239](https://github.com/insideout10/wordlift-plugin/issues/239): Fix disambiguation widget look & feel on WP 4.4.+
* Fix: [#237](https://github.com/insideout10/wordlift-plugin/issues/237): Fix disambiguation for internal entities sameAs of other entities
* Fix: [#234](https://github.com/insideout10/wordlift-plugin/issues/234): Fix text annotation removing for annotation containing blank html markup
* Fix: [#228](https://github.com/insideout10/wordlift-plugin/issues/228): 
Flush properly rewrite rules on plugin activation to prevent 404 on entity pages
* Fix: [#227](https://github.com/insideout10/wordlift-plugin/issues/227): 
Change wording for invalid or missing text selection on entity creation workflow

= 3.3.1 (2016-01-06) =
* Fix: [#225](https://github.com/insideout10/wordlift-plugin/issues/225): Return safely when get_current_screen() is not defined (yet).

= 3.3.0 (2016-01-06) =
* Enhancement: [#151](https://github.com/insideout10/wordlift-plugin/issues/151): Download and save Place coordinates from RL.
* Enhancement: [#161](https://github.com/insideout10/wordlift-plugin/issues/161): Geomap shows current entity if it is a Place (or child of Place).
* Enhancement: [#207](https://github.com/insideout10/wordlift-plugin/issues/207): Add rating score and consistency check for entities.
* Enhancement: [#209](https://github.com/insideout10/wordlift-plugin/issues/209): Add thumbnail preview within the entity listing.
* Enhancement: [#208](https://github.com/insideout10/wordlift-plugin/issues/208): Add classification scopes filter - aka 4W filter - within entity listing.
* Enhancement: [#199](https://github.com/insideout10/wordlift-plugin/issues/199) & [#101](https://github.com/insideout10/wordlift-plugin/issues/101): Improve new entity creation workflow usability within the content post editing.
* Enhancement: [#171](https://github.com/insideout10/wordlift-plugin/issues/171): Add related posts counter within the entity listing.
* Enhancement: [#121](https://github.com/insideout10/wordlift-plugin/issues/121): Improve UI consinstency
* Enhancement: [#140](https://github.com/insideout10/wordlift-plugin/issues/140): Add new properties for Organization 

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

InSideOut10 delivers strategic digital communication tools for enterprises and organisations. 

InSideOut10 uses artificial intelligence and semantic networks to collect, analyse and link relevant contents with data.

Our goal is to **help blogger, journalists and content creators connect and share experiences with their readers** as well as **structuring knowledge in machine-readable form**. 

= Why we are doing this =

Our mission is an *utopian one*: **organize the world general knowledge** by providing tools that everyone can use.

= In open source we trust =

**WordLift** is built on **open source software**. <br /> 
**WordLift** uses **open source tools for natural language and semantic processing**. <br />

= In data ownership we trust =

We believe content creators should **own, retain and exploit** the **value of the metadata they create**. 
