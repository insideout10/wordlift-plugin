<?php

class Property {
	
	public $name;
	public $type;
	public $size;
	public $description;
	public $multiline;
	
	public function to_string() {
		return '[name:'.$this->name.'][type:'.$this->type.'][size:'.$this->size.'][description:'.$this->description.'][multiline:'.$this->multiline.']';
	}
	
}

?>