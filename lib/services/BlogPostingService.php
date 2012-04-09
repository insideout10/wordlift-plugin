<?php

/**
 * This class converts a WordPress post into a schema.org BlogPosting.
 */
class BlogPostingService {

	/**
	 * Creates an instance of the BlogPostingService.
	 */
	function __construct() {
		
	}
	
	/**
	 * Creates an Array of BlogPosting from the provided Post.
	 * @param Array $posts
	 */
	public function fromPosts(&$posts) {
		$blog_postings = array();
		
		foreach ($posts as $post) {
			$blog_postings[] = $this->fromPost($post);
		}
		
		return $blog_postings;
	}
		
	/**
	 * Creates an instance of BlogPosting from the provided Post.
	 * @param unknown_type $post
	 */
	public function fromPost(&$post) {
		
		// if we received a post ID, load the post.
		if (true == is_numeric($post)) {
			$post = get_post($post);
		}
		
		// http://schema.org/BlogPosting
		// At the moment we look for the following properties:
		//  [1] Thing:
		//      a) description (Text)
		//      b) image (URL)
		//      c) name (Text)
		//      d) url (URL)
		//  [2] CreativeWork:
		//      e) dateCreated (Date)
		//      f) dateModified (Date)
		//      g) datePublished (Date)
		
		// http://codex.wordpress.org/Function_Reference/get_post
		// WordPress Post has the following properties:
		//  a) ID (integer)
		//  b) post_author (integer) The post author's ID
		//  c) post_date (string) The datetime of the post (YYYY-MM-DD HH:MM:SS)
		//  d) post_date_gmt (string) The GMT datetime of the post (YYYY-MM-DD HH:MM:SS)
		//  e) post_content
		//  f) post_title
		//  g) post_category (integer) The post category's ID. Note that this will always be 0 (zero) from wordpress 2.1 onwards. To determine a post's category or categories, use get_the_category().
		//  h) post_excerpt
		//  i) post_status (string) The post status (publish|pending|draft|private|static|object|attachment|inherit|future|trash)
		//  j) post_name (string) The post's URL slug
		//  k) post_modified (string) The last modified datetime of the post (YYYY-MM-DD HH:MM:SS)
		//  l) post_modified_gmt (string) The last modified GMT datetime of the post (YYYY-MM-DD HH:MM:SS)
		//  m) guid (string) A link to the post. Note: One cannot rely upon the GUID to be the permalink (as it previously was in pre-2.5), Nor can you expect it to be a valid link to the post. It's merely a unique identifier, which so happens to be a link to the post at present.
		//  n) post_type (string) (post|page|attachment)
		// !!! the link to the post is obtained using get_permalink (http://codex.wordpress.org/Function_Reference/get_permalink)
		// !!! for the image we need to get the thumbnail (http://codex.wordpress.org/Function_Reference/get_post_thumbnail_id), then the url (http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src).

		// Now we'll match the properties:
		$blog_posting = new BlogPosting();
		$blog_posting->description = $post->post_excerpt;
		$blog_posting->image = $this->getThumbnailUrl($post->ID);
		$blog_posting->name = $post->post_title;
		$blog_posting->url = get_permalink($post->ID);
		$blog_posting->dateCreated = $post->post_date_gmt;
		$blog_posting->dateModified = $post->post_modified_gmt;
		
		if ('publish' == $post->post_status) {
			$blog_posting->datePublished = $blog_posting->dateModified; 
		}
		
		return $blog_posting;

	}
	
	/**
	 * Returns the Thumnail URL for a Post.
	 * @param integer $post_id
	 */
	private function getThumbnailUrl($post_id) {
		// for the image we need to get the thumbnail (http://codex.wordpress.org/Function_Reference/get_post_thumbnail_id), then the url (http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src).
		
		// get the ID of the thumbnail.
		$thumbnail_id = get_post_thumbnail_id($post_id);
		
		// if there's no thumbnail return NULL.
		if (NULL == $thumbnail_id) return NULL;
		
		// get the attachment structure for the thumbnail.
		$attachment = wp_get_attachment_image_src($thumbnail_id);
		
		// if the attachment is not found return NULL.
		if (false == $attachment) return NULL;
		
		// return the URL.
		return $attachment[0];
	}
	
}

?>