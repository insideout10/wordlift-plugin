<?php

/**
 * The PostView renders a Post, by adding a Tab view with a link to
 *     a) the Post contents.
 *     b) the Entities list.
 *     c) Related articles.
 *
 * The PostView is using the jQuery UI library to set-up the Tab view and it is calling other views to render the inner contents.
 */
class PostView {
	
	// The EntitiesView instance used to populate the entities content.
	private $entities_view;
	
	function __construct($entities_view = NULL) {
		$this->entities_view = $entities_view;
	}
	
	public function getContent($content) {
		
		return <<<EOD
		
<div id="tabs">
<ul>
	<li><a href="#tabs-1">Article</a></li>
	<li><a href="#tabs-2">Entities</a></li>
	<li><a href="#tabs-3">Related</a></li>
</ul>

<div id="tabs-1">$content</div>
<div id="tabs-2">{$this->entities_view->getContent()}</div>
<div id="tabs-3"></div>

</div>

<script type="text/javascript">
	jQuery(function($) {
		$( "#tabs" ).tabs();
	});
</script>

EOD;
		
	}
	
}


?>