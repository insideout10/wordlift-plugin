<?php

// EntityPost

class Entity {

	public $text;
	public $type;
	public $slug;

	public $count;
	public $relevance;
	public $reference;
	public $score;
	public $rank;
	public $properties;
	
	public $post_id;
	public $accepted = false;
	public $rejected = false;

	private $logger;

	function __construct() {
	}

	function get_id() {
		return $this->reference;
	}

	function get_term() {
		return $this->text.' ('.$this->type.')';
	}

	function to_string() {
		return '[text:'.$this->text.'][type:'.$this->type.'][slug:'.$this->slug.']';
	}

}

?>