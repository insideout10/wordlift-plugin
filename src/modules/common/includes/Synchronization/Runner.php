<?php

namespace Wordlift\Modules\Common\Synchronization;

interface Runner {

	public function start();

	public function run();

	public function get_offset();

	public function get_total();

}
