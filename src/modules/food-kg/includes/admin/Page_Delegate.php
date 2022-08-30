<?php

namespace Wordlift\Modules\Food_Kg\Admin;

interface Page_Delegate {

	function render();

	function admin_enqueue_scripts();

}
