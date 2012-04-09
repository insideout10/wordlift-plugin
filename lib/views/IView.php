<?php

/**
 * A class that generates a content for display.
 */
interface IView {
	
	/**
	 * Generates the content to be displayed, optionally re-using the provided content fragment.
	 * @param string $content
	 */
	public function getContent($content=null);
	
}

?>