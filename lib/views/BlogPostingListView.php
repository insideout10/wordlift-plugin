<?php

/**
 * This class builds a list of blog-posting tiles.
 * @author david
 *
 */
class BlogPostingListView implements IView {
	
	// the posts to display.
	private $blog_postings;
	
	const CLASS_NAME = 'blog-posting-list-view';
	
	/**
	 * Creates a list view of the blog posts.
	 * @param Array $posts
	 */
	function __construct(&$blog_postings) {
		$this->blog_postings = $blog_postings;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IView::getContent()
	 */
	public function getContent($content=null) {
		
		// the style-sheets class name.
		$class_name = HtmlService::htmlEncode(self::CLASS_NAME);
		
		// enclose the content in the class.
		$content = '<div class="'.$class_name.'">';
		
		foreach ($this->blog_postings as $blog_posting) {
			$blog_posting_tile_view = new BlogPostingTileView($blog_posting);
			$content .= $blog_posting_tile_view->getContent();
		}
		
		return $content;
		
	}
	
}

?>