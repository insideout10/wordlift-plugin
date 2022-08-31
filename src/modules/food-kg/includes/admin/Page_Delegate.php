<?php

namespace Wordlift\Modules\Food_Kg\Admin;

interface Page_Delegate {

	public function render();

	public function admin_enqueue_scripts();

}
