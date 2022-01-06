<?php

namespace Wordlift\Content\Wordpress;

interface Wordpress_Entity_Id_Strategy {

	function get_entity_id( $content_id );

	function set_entity_id( $content_id, $rel_uri );

	function get_by_entity_id( $uri );

}
