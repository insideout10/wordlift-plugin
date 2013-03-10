<?php
function h( $string ) {
	return htmlspecialchars( $string, ENT_COMPAT | ENT_HTML401, "UTF-8" );
}


class WordLift_EntityPage
{

	public $queryService;
	public $storeService;
	public $defaultLanguage;
	public $moreLinkText = " [...]";
	public $excerptWords = 55;
	public $postsLimit = 10;

	const TYPE_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
	const NAME_URI = "http://schema.org/name";
	const IMAGE_URI = "http://schema.org/image";
	const DESCRIPTION_URI = "http://schema.org/description";

	public function getContent( $attributes, $pageContent = "" ) {

		// initialize the empty fragment that will be returned along with the existing content.
		$fragment = "<div class=\"entity-page\">";

		$entityURI = $_GET[ "e" ];
		$escEntityURI = $this->queryService->escapeValue( $entityURI );

		$fragment .= $this->getEntityContent( $entityURI );

		$whereClause = <<<EOF

		[] fise:entity-reference <$entityURI> ;
			wordlift:postID ?postID .
EOF;

		$count = 0;
		$result = $this->queryService->execute( "DISTINCT ?postID", $whereClause, $this->postsLimit, NULL, $count, NULL, "desc(?postID)");
		$rows = &$result[ "result" ][ "rows" ];

		// this text will be shown after the excerpt.
		$htmlMoreLinkText = h( $this->moreLinkText );

		$fragment .= "<div class=\"posts\">";
		foreach ( $rows as &$row ) :

			$postID = &$row[ "postID" ];
			$post = get_post( $postID );
			if ("publish" !== $post->post_status)
			{
				continue;
			}

			// $fragment .= "<pre>" . var_export($post, true) . "</pre>";

			$permalink = get_permalink( $postID );
			$htmlTitle = h( $post->post_title );

			$link = "<a href=\"$permalink\">$htmlMoreLinkText</a>";

			$content = strip_shortcodes( $post->post_content );
			$content = wp_trim_words( $content, $this->excerptWords ) . $link;

			$htmlModified = h( $post->post_modified );

			$fragment .= "<div class=\"post\">";
			$fragment .= get_the_post_thumbnail( $postID );
			$fragment .= "<div class=\"title\">$htmlTitle</div>";
			$fragment .= "<div class=\"modified\">$htmlModified</div>";
			$fragment .= "<div class=\"content\">$content</div>";
			$fragment .= "</div>";

		endforeach;

		$fragment .= "</div></div>";

		return $pageContent . $fragment;
	}

	private function getEntityContent( $entityURI ) {

		$content = "";

		$predicates = $this->storeService->getResourcePredicates( $entityURI );

		// $name = $predicates[ self::NAME_URI ][ 0 ][ "value" ];
		$name = $this->getValue( $predicates[ self::NAME_URI ], array( "it", "en" ) );
		$htmlName = h( $name );

		$type = $predicates[ self::TYPE_URI ][ 0 ][ "value" ];
		// $type = $this->getValue( $predicates[ self::TYPE_URI ], array( "it", "en" ) );
		$htmlType = h( $type );

		$simpleType = substr( $type, strrpos( $type, "/" ) + 1 );
		$htmlSimpleType = h( $simpleType );

		// $description = $predicates[ self::DESCRIPTION_URI ][ 0 ][ "value" ];
		$description = $this->getValue( $predicates[ self::DESCRIPTION_URI ], array( "it", "en" ) );
		$htmlDescription = h( $description );


		$content .= "<div class=\"entity $htmlSimpleType\" itemscope itemtype=\"$htmlType\">";
		
		if ( array_key_exists( self::IMAGE_URI, $predicates ) ) :
			$image = $predicates[ self::IMAGE_URI ][ 0 ][ "value" ];
			$htmlImage = h( $image );
			$content .= "<img itemprop=\"image\" onerror=\"this.parentNode.removeChild(this);\" src=\"$htmlImage\" />";
		endif;

		$content .= "<div class=\"type $htmlSimpleType\"></div>";
		$content .= "<div class=\"name\">$htmlName</div>";
		$content .= "<div class=\"description\">$htmlDescription</div>";
		$content .= "</div>";

		// $content .= "<pre>" . var_export( $predicates, true ) . "</pre>";

		return $content;
	}

	private function getValue( $values, $languages ) {
		foreach ( $values as &$value ) :

			if ( $languages[0] === $value[ "lang" ] )
				return $value[ "value" ];

		endforeach;
	}

}

?>