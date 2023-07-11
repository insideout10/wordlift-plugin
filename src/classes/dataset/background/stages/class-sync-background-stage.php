<?php

namespace Wordlift\Dataset\Background\Stages;

interface Sync_Background_Process_Stage {

	public function count();

	public function get_sync_object_adapters( $offset, $batch_size );

}
