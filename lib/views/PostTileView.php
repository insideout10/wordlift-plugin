<?php

class PostTileView {
	
	private $logger;
	private $post_id;
	
	public function __construct($post_id) {
		$this->logger = Logger::getLogger(__CLASS__);
		$this->post_id = $post_id;
	}
	
	public function display() {
		$post = get_post($this->post_id);
		$permalink = htmlentities(get_permalink( $this->post_id ));

		echo '<div onclick="location.href=\''.$permalink.'\';" class="post-tile">';
		echo '<div class="title">';
		echo '<a href="'.$permalink.'">'.htmlentities($post->post_title).'</a></div>';
		echo '<div class="author">';
		echo htmlentities($post->post_author);
		echo '</div>';
		echo '<div class="date">';
		echo htmlentities($post->post_date);
		echo '</div>';
		echo '<div class="excerpt">';
		echo htmlentities($post->post_excerpt);
		echo '</div>';
		echo '</div>';
	}
	
}

?>