<?php

namespace Wordlift\Dataset\Background\Stages;

interface Sync_Background_Process_Stage {

	function count();

	function get_sync_object_adapters( $offset, $batch_size );

}
