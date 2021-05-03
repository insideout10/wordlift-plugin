<?php

namespace Wordlift\Videoobject\Background_Process;

use Wordlift\Common\Background_Process\Data_Source;

class Videos_Data_Source extends Data_Source {

	const IMPORT_DONE_FLAG = 'wl_video_object_import_complete';

	public function next() {
		return get_posts( array(
			'fields'      => 'ids',
			'number'     => $this->get_batch_size(),
			'offset'     => $this->get_state()->index,
			'meta_query'  => array(
				array(
					'key'     => self::IMPORT_DONE_FLAG,
					'compare' => 'NOT EXISTS'
				)
			),
		) );
	}

	public function count() {
		return count( get_posts( array(
			'fields'      => 'ids',
			'numberposts' => - 1,
			'meta_query'  => array(
				array(
					'key'     => self::IMPORT_DONE_FLAG,
					'compare' => 'NOT EXISTS'
				)
			),
		) ) );

	}

	public function get_batch_size() {
		// For now use only 5 in order to prevent exceeding api limit.
		return 5;
	}

}