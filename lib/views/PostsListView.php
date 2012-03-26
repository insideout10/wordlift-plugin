<?php

class PostsListView {
	
	private $logger;
	private $posts;
	
	public function __construct(&$posts) {
		$this->logger = Logger::getLogger(__CLASS__);
		
		$this->posts = $posts;
	}
	
	public function display() {
		
		echo '<ul class="posts-list-view">';
		
		foreach ($this->posts as $post_id) {
			$post_list_view = new PostListView($post_id);
			$post_list_view->display();
		}
		
		echo '</ul>';
	}
	
}

?>