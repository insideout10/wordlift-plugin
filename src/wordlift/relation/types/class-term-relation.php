<?php
/**
 * This class represents the post row on relation table ( this represents the WordPress
 * type post, not to be confused with post_type )
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Relation
 */

namespace Wordlift\Relation\Types;

use Wordlift\Object_Type_Enum;

class Term_Relation extends Relation {

	public function __construct( $id, $relation_type ) {
		parent::__construct( $id, $relation_type, Object_Type_Enum::TERM );
	}

}
