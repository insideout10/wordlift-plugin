<?php

class PostListView {
	
	private $logger;
	private $post_id;
	
	public function __construct($post_id) {
		$this->logger = Logger::getLogger(__CLASS__);
		$this->post_id = $post_id;
	}
	
	public function display() {
		$post = get_post($this->post_id);
		$permalink = get_permalink( $this->post_id);
		$title = htmlentities($post->post_title, ENT_QUOTES | ENT_HTML5, 'UTF-8' );;
		$date = mysql2date(get_option('date_format'),$post->post_date);
		
		echo '<li><a href="'.$permalink.'">'.$title.'</a> <span class="date">'.$date.'</span></li>';
	}
	
}

?>