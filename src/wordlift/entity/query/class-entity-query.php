<?php
namespace Wordlift\Entity\Query;

interface Entity_Query {

	public function query( $query, $schema_types, $limit );

}