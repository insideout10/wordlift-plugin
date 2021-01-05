<?php

use Wordlift\Dataset\Sync_Hooks_Entity_Relation;
use Wordlift\Dataset\Sync_Page;
use Wordlift\Dataset\Sync_Service;
use Wordlift\Dataset\Sync_Wpjson_Endpoint;
use Wordlift\Features\Response_Adapter;
use Wordlift\Jsonld\Jsonld_Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

new Response_Adapter();
