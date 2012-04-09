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
		
		// the style-sheets class-name.
		$class_name = self::CLASS_NAME;
		
		
		// The following properties are available in the BlogPosting.
		//  [1] Thing:
		//      a) description (Text)
		//      b) image (URL)
		//      c) name (Text)
		//      d) url (URL)
		//  [2] CreativeWork:
		//      e) dateCreated (Date)
		//      f) dateModified (Date)
		//      g) datePublished (Date)
		
		$description = HtmlService::htmlEncode($this->blog_posting->description);
		$image = HtmlService::getImageFragment( HtmlService::htmlEncode($this->blog_posting->image) );
		$name = HtmlService::htmlEncode($this->blog_posting->name);
		$url= HtmlService::htmlEncode($this->blog_posting->url);
		$name_with_link = HtmlService::getLinkFragment( $url, $name);
		$date_created = HtmlService::htmlEncode($this->blog_posting->dateCreated);
		$date_modified = HtmlService::htmlEncode($this->blog_posting->dateModified);
		$date_published = HtmlService::htmlEncode($this->blog_posting->datePublished);
		
		return <<<EOD

		<div class="$class_name">
			<div class="name">$name_with_link</div>
			<div class="description">$description</div>
			<div class="date-published">$date_published</div>
		</div>
		
EOD;
		
	}
	
}

?>