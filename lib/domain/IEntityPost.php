<?php

/**
 * This interface represents entity data that come from its belonging to a WordPress blog.
 */
interface IEntityPost {
	
	/**
	 * The WordPress Post ID for this entity.
	 */
	public function getPostId();
	
	/**
	 * Sets the WordPress Post ID for this entity post.
	 * @param integer $post_id
	 */
	public function setPostId($post_id);

	/**
	 * Get the posts related to this entity post.
	 */
	public function getPosts();

	/**
	 * Set the posts related to this entity post.
	 * @param unknown_type $posts
	 */
	public function setPosts(&$posts);
	
	/**
	 * An array of accepted posts for this entity post.
	 */
	public function getAcceptedPosts();
	
	/**
	 * Sets the array of post ids accepted for this entity post.
	 * @param Array $posts
	 */
	public function setAcceptedPosts(&$posts);

	/**
	 * An array of rejected posts for this entity post.
	 */
	public function getRejectedPosts();
	
	/**
	 * Sets the array of rejected post ids for this entity post.
	 * @param Array $posts
	 */
	public function setRejectedPosts(&$posts);
	
	/**
	 * A boolean, if true this entity post is bogus, i.e. must be set to rejected automatically.
	 */
	public function isBogus();
	
	/**
	 * Sets the entity post bogus or not.
	 * @param boolean $bogus
	 */
	public function setBogus($bogus);
	
}

?>