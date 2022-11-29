When Include/Exclude is enabled, we want to push the configuration to the platform.

We're going to push the configuration when there's an update and once per day to ensure that the configuration is
updated
even if it has been changed outside of WordPress.

```php
		$include_exclude_data = get_option( 'wl_exclude_include_urls_settings', array() );
		$data = array(
			'type' => $include_exclude_data['include_exclude'], // ['exclude'|'include']
			'urls' => $include_exclude_data['urls'],
		);
```