<?php

namespace Wordlift\Videoobject\Background_Process;

use Wordlift\Common\Background_Process\Background_Process;
use Wordlift\Videoobject\Video_Processor;

class Videoobject_Background_Process extends Background_Process {
	/**
	 * @var Video_Processor
	 */
	private $video_processor;

	/**
	 * Videoobject_Background_Process constructor.
	 *
	 * @param $video_processor Video_Processor
	 * @param $data_source
	 */
	public function __construct( $video_processor, $data_source ) {
		$this->video_processor = $video_processor;
		parent::__construct( $data_source );
	}

	protected function get_state_storage_key() {
		return '__wl_videoobject_import_state';
	}

	protected function get_action_key() {
		return 'wl_videoobject_import_background_action';
	}

	/**
	 * @param $items
	 *
	 * @return bool|void
	 */
	protected function process_items( $items ) {
		foreach ( $items as $item ) {
			$this->video_processor->process_video_urls( get_post( $item ), $item );
		}
		return true;
	}

}
