<?php

// EntityPost

class Entity {

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
	
	private $logger;

	function __construct() {
	}

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