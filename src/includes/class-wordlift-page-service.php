<?php

/**
 * The Wordlift_Page_Service alters the page output to add schema.org markup in order to provide sharing services
 * such as Google+ the correct page title (otherwise they would read the first entity name).
 *
 * See https://github.com/insideout10/wordlift-plugin/issues/262
 *
 * @since 3.5.3
 */
class Wordlift_Page_Service {

	/**
	 * Hook to wp_head and create a response buffer.
	 *
	 * @since 3.5.3
	 */
	public function wp_head() {

		// When the buffer is flushed, have the handler markup the content.
		ob_start( array( $this, 'handler' ) );

	}

	/**
	 * Hook to wp_footer and flush the response buffer.
	 *
	 * @since 3.5.3
	 */
	public function wp_footer() {

		ob_end_flush();

	}

	/**
	 * Handle the a single article HTML block, insert schema.org microdata markup with itemscope/itemtype/itemprop.
	 *
	 * This is an helper method for the gandler method
	 *
	 * @since 3.10
	 *
	 * @param array $match The regexp matches discovered while locating all articles in the HTML.
	 *                     $match[0] is the complete article HTML
	 *
	 * @return string The processed article HTML.
	 */
	private function manipulate_article($match) {		
		global $post;     
		$html = $match[0];

		// add itemscope and type to the article element itself
		$html = str_replace('<article ', '<article itemscope itemtype="http://schema.org/Article" ',$html);

		// add itemprop to the title
		$html = str_replace('<h1 ', '<h1 itemprop="headline" ',$html);
		
		// Check if the author name is in the HTML, if so enclose it in a span with metadata
		$count = 0; 
		$author = get_the_author_meta( 'display_name' );
		$html = str_replace( $author,'<span itemprop="author" itemscope="" itemtype="http://schema.org/Person">'.
		                     '<meta itemprop="name" content="'.esc_attr($author).'" />'.
							 $author.'</span>',
							 $html,$count );
							 
		// if not found, insert the author metadat into the article (at the bottom of it)
		if (0 == $count)
			$html = str_replace('</article>','<span itemprop="author" itemscope="" itemtype="http://schema.org/Person">'.
		                     '<meta itemprop="name" content="'.esc_attr($author).'" />'.
							 '</span></article>',
							 $html);

		// set properties on the featured image if it is displayed as part
		// of the article
		
		$featured = get_the_post_thumbnail_url($post->ID,'full');
		if ($featured) {
			$f = explode('.',$featured);
			$image_base = $f[0];
			$html = preg_replace('#<img ([^>]*) src="'.$image_base.'([\.\-])#','<img $1 itemprop="image" src="'.$image_base.'${2}',$html);
		}

		// insert publish time
		$datetime = get_post_time( 'Y-m-d\TH:i', true, $post, false );
		$html = preg_replace('#<time ([^>]*) datetime="'.$datetime.'#','<time $1 itemprop="startDate" datetime="'.$datetime,$html,-1,$count);
		if (0 == $count)
			$html = str_replace('</article>','<time itemprop="startDate" datetime="'.$datetime.'"></time></article>',$html);

		// insert publisher
		$configuration = Wordlift_Configuration_Service::get_instance();
		$publisher_id = $configuration->get_publisher_id();

		// do not try to add publisher if plugin setup is not completed
		if ($publisher_id) {
		
			$type = Wordlift_Entity_Type_Service::get_instance()->get($publisher_id);
			$logo = get_the_post_thumbnail_url($publisher_id,'full');
			
			$publisher_post = get_post($publisher_id);
			$name = $publisher_post->post_title;
			
			$publisher = '<span itemprop="publisher" itemscope itemtype="'.$type.'">'.
							'<span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">'.
								'<meta itemprop="url" content="'.esc_url($logo).'">'.
							'</span>'.
							'<meta itemprop="name" content="'.esc_attr($name).'">'.
						 '</span>';
			$html = str_replace('</article>',$publisher.'</article>',$html);
		}
		
		return $html;
	}
	
	/**
	 * Handle the buffer, by inserting schema.org microdata markup with itemscope/itemtype/itemprop.
	 *
	 * @since 3.5.3
	 *
	 * @param string $buffer The output buffer.
	 *
	 * @return string The processed output buffer.
	 */
	public function handler( $buffer ) {
		
		// just to be on the safer side, if we are not in a single post, don't even
		// try to processed
		
		if (!is_single())
			return $buffer;

		// identify the article block inwhich microdata needs to be inserted/changed
		// the main article is identified by having a "type-post" in its class which is usually
		// inserted by the post_class function		
		
		return preg_replace_callback('#<article\\s+[^>]*class="[^>"]*type-post[^>"]*".*?<\/article>#si',
		                             array($this,'manipulate_article'),
									 $buffer);		
		
	}

}
