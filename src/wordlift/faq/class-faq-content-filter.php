<?php
/**
 * This file helps to remove all the highlight tags from the post content which
 * are added by FAQ js plugin
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\FAQ
 */

namespace Wordlift\FAQ;

class Faq_Content_Filter {

	const FAQ_HIGHLIGHT_CLASS_NAME = 'wl-faq-highlight';

	public function __construct() {
		add_filter('content_save_pre', array($this, 'filter_post_content'), 10, 1);
	}

	/**
	 * Remove the highlighted tags before saving.
	 *
	 * @param $post_data string Post data html string.
	 *
	 * @return string html string after removing the highlighted text.
	 */
	public function filter_post_content( $post_data ) {
		// Lets create the DOM from the html.
		$dom = new \DOMDocument();
		$dom->loadHTML( $post_data );
		$xpath = new \DomXPath($dom);
		$node_list = $xpath->query("//span[@class='". self::FAQ_HIGHLIGHT_CLASS_NAME ."']");
		// If the class exists then replace the node with its child.
		$selectedNodesLength = $node_list->length;
		for ($i = 0; $i < $selectedNodesLength; $i++) {
			$wrapper_node = $node_list->item($i);
			$child_nodes = $wrapper_node->childNodes;
			$fragment = $dom->createDocumentFragment();
			foreach( $child_nodes as $child_node ) {
					$fragment->appendChild( $child_node );
			}
			$wrapper_node->parentNode->replaceChild($fragment, $wrapper_node);
		}
		// Remove extra string returned by DOM html method.
		return preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML());
	}
}