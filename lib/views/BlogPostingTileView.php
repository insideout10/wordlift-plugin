<?php

/**
 * Creates an Html fragment with a tile for a BlogPosting.
 */
class BlogPostingTileView implements IView {

	// The BlogPosting to display.
	private $blog_posting;
	
	const CLASS_NAME = 'blog-posting-tile-view';
	
	/**
	 * Creates an instance of the BlogPostingTileView by passing the BlogPosting to display.
	 */
	function __construct(&$blog_posting) {
		$this->blog_posting = $blog_posting;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IView::getContent()
	 */
	public function getContent($content=null) {
		
		$name = $this->blog_posting->name;
		
		return <<<EOD

		<div class="{CLASS_NAME}">$name</div>
		
EOD;
		
	}
	
}

?>