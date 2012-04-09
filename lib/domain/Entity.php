<?php

/**
 * This class represents an Entity. The entity shall be a mix between three different data-sets and a JSON output:
 *  1) the Entity itself as defined according to the schema.org specifications and its extension.
 *  2) the Entity as a result of analysis, therefore including information such as:
 *     a) the relevance,
 *     b) the score,
 *     c) the number of occurrences,
 *     d) and other information such its location in the content, etc.
 *  3) the Entity as it is related to the Blog itself, i.e.:
 *     a) accepted posts,
 *     b) rejected posts,
 *     c) permanent rejection,
 *     d) ...
 *  4) a JSON representation of the entity.
 */
class Entity implements IEntityPost {

	public $text;
	public $type;
	public $slug;

	public $count;
	public $relevance;
	public $about;
	public $score;
	public $rank;
	public $properties;
	
	public $post_id;
	public $accepted = false;
	public $rejected = false;
	
	public $posts = array();
	public $accepted_posts = array();
	public $rejected_posts = array();

	public $relative_rank;
	
	// whether this entity post is bogus, i.e. not valid in this blog context.
	private $is_bogus;
	
	private $logger;

	function __construct() {
	}
	
	/************************************************************************
	 * IEntityPost implementation											*
	 ************************************************************************/
	
	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::getPostId()
	 */
	public function getPostId() {
		return $this->post_id;
	}

	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::setPostId()
	 */
	public function setPostId($post_id) {
		$this->post_id = $post_id;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::getPosts()
	 */
	public function getPosts() {
		return $this->posts;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::setPosts()
	 */
	public function setPosts(&$posts) {
		$this->posts = $posts;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::getAcceptedPosts()
	 */
	public function getAcceptedPosts() {
		return $this->accepted_posts;	
	}

	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::setAcceptedPosts()
	 */
	public function setAcceptedPosts(&$posts) {
		$this->accepted_posts = $posts;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::getRejectPosts()
	 */
	public function getRejectedPosts() {
		return $this->rejected_posts;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::setRejectedPosts()
	 */
	public function setRejectedPosts(&$posts) {
		$this->rejected_posts = $posts;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::isBogus()
	 */
	public function isBogus() {
		return $this->is_bogus;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IEntityPost::setBogus()
	 */
	public function setBogus($bogus) {
		$this->is_bogus = $bogus;
	}
	
	/************************************************************************/

	function get_id() {
		return $this->about;
	}

	function get_term() {
		return $this->text.' ('.$this->type.')';
	}

	function to_string() {
		return '[text:'.$this->text.'][type:'.$this->type.'][slug:'.$this->slug.']';
	}

}

?>