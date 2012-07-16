<?php

/**
 * This class has the sole purpose to register short-codes into WordPress.
 */
class ShortCodeService {
	
	// the function name that will process the short code call.
	const SHORT_CODE_FUNCTION = 'doShortCode';
	
	/**
	 * Registers short-codes into WordPress.
	 * http://codex.wordpress.org/Shortcode_API 
	 */
	public static function registerShortCodes() {
		
		$logger = Logger::getLogger(__CLASS__);

		// in order to register short-codes we need a Short-Code and and a function.
		//  a) by convention the method is called 'doShortCode',
		//  b) while the short-code is read via 'getShortCode'

		$logger->debug('registering short-code ['.EntitiesTreemapView::getShortCode().'].');
		add_shortcode(EntitiesTreemapView::getShortCode(), array("EntitiesTreemapView", self::SHORT_CODE_FUNCTION));
		
		$logger->debug('registering short-code ['.EntitiesGeomapView::getShortCode().'].');
		add_shortcode(EntitiesGeomapView::getShortCode(), array("EntitiesGeomapView", self::SHORT_CODE_FUNCTION));
		
	}
	
}

?>