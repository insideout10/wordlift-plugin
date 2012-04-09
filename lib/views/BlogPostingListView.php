<?php

/**
 * This class builds a list of blog-posting tiles.
 * @author david
 *
 */
class BlogPostingListView implements IView {
	
	// the posts to display.
	private $blog_postings;
	
	/**
	 * Creates a list view of the blog posts.
	 * @param Array $posts
	 */
	function __construct(&$blog_postings) {
		$this->blog_postings = $blog_postings;
	}
	
	public function getContent($content=null) {
		
		$content = '';
		
		foreach ($this->blog_postings as $blog_posting) {
			$blog_posting_tile_view = new BlogPostingTileView($blog_posting);
			$content .= $blog_posting_tile_view->getContent();
		}
		
		return $content;
		
	}
	
}

?>