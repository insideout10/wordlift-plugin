<?php

/**
 * Get the Redlink dataset URI.
 *
 * @return string The Redlink dataset URI.
 * @since      3.10.0 deprecated.
 * @since      3.0.0
 *
 * @deprecated use Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
 */
function wl_configuration_get_redlink_dataset_uri() {

	return Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
}

