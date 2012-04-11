<?php

/**
 * Creates an Html fragment with a tile for a BlogPosting.
 */
class BlogPostingTileView implements IView {

	// The BlogPosting to display.
	private $blog_posting;
	
	// If true, will echo the schema.org tags.
	private $enable_schema_org;
	
	const CLASS_NAME = 'blog-posting-tile-view';
	
	/**
	 * Creates an instance of the BlogPostingTileView by passing the BlogPosting to display.
	 */
	function __construct(&$blog_posting,$enable_schema_org=false) {
		$this->blog_posting = $blog_posting;
		$this->enable_schema_org = $enable_schema_org;
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

		if (true == $this->enable_schema_org) {
		
			$content = <<<EOD

				<div itemscope itemtype="http://schema.org/BlogPosting" class="$class_name">
					<div itemprop="name" class="name"><a itemprop="url" href="$url">$name</a></div>
					<div itemprop="description" class="description">$description</div>
					<div itemprop="datePublished" class="date-published">$date_published</div>
				</div>		
EOD;
		
		} else {

			$content = <<<EOD
			
				<div class="$class_name">
					<div class="name"><a href="$url">$name</a></div>
					<div class="description">$description</div>
					<div class="date-published">$date_published</div>
				</div>
EOD;
		}

		return $content;
			
	}
	
}

?>