---
title: WordPress Background Processing at Scale - Action Scheduler Job Queue
description: Learn how to do WordPress background processing at scale by tuning the Action Scheduler job queue's default WP Cron runner.
---
# Background Processing at Scale

Action Scheduler's default processing is designed to work reliably across all different hosting environments. In order to achieve that, the default processing thresholds are more conservative than many servers could support.

Specifically, Action Scheduler will only process actions in a request until:

* 90% of available memory is used
* processing another 3 actions would exceed 30 seconds of total request time, based on the average processing time for the current batch
* in a single concurrent queue

While Action Scheduler will initiate an asychronous loopback request to process additional actions, on sites with very large queues, this can result in slow processing times.

While using [WP CLI to process queues](/wp-cli/) is the best approach to increasing processing speed, on occasion, that is not a viable option. In these cases, it's also possible to increase the processing thresholds in Action Scheduler to increase the rate at which actions are processed by the default queue runner.

## Increasing Time Limit

By default, Action Scheduler will only process actions for a maximum of 30 seconds in each request. This time limit minimises the risk of a script timeout on unknown hosting environments, some of which enforce 30 second timeouts.

If you know your host supports longer than this time limit for web requests, you can increase this time limit. This allows more actions to be processed in each request and reduces the lag between processing each queue, greatly speeding up the processing rate of scheduled actions.

For example, the following snippet will increase the time limit to 2 minutes (120 seconds):

```php
function eg_increase_time_limit( $time_limit ) {
	return 120;
}
add_filter( 'action_scheduler_queue_runner_time_limit', 'eg_increase_time_limit' );
```

Some of the known host time limits are:

* 60 second on WP Engine
* 120 seconds on Pantheon
* 120 seconds on SiteGround

## Increasing Batch Size

By default, Action Scheduler will claim a batch of 25 actions. This small batch size is because the default time limit is only 30 seconds; however, if you know your actions are processing very quickly, e.g. taking microseconds not seconds, or that you have more than 30 second available to process each batch, increasing the batch size can slightly improve performance.

This is because claiming a batch has some overhead, so the less often a batch needs to be claimed, the faster actions can be processed.

For example, to increase the batch size to 100, we can use the following function:

```php
function eg_increase_action_scheduler_batch_size( $batch_size ) {
	return 100;
}
add_filter( 'action_scheduler_queue_runner_batch_size', 'eg_increase_action_scheduler_batch_size' );
```

## Increasing Concurrent Batches

By default, Action Scheduler will run only one concurrent batches of actions. This is to prevent consuming a lot of available connections or processes on your webserver.

However, your server may allow a large number of connections, for example, because it has a high value for Apache's `MaxClients` setting or PHP-FPM's `pm.max_children` setting.

If this is the case, you can use the `'action_scheduler_queue_runner_concurrent_batches'` filter to increase the number of concurrent batches allowed, and therefore speed up processing large numbers of actions scheduled to be processed simultaneously.

For example, to increase the allowed number of concurrent queues to 10, we can use the following code:

```php
function eg_increase_action_scheduler_concurrent_batches( $concurrent_batches ) {
	return 10;
}
add_filter( 'action_scheduler_queue_runner_concurrent_batches', 'eg_increase_action_scheduler_concurrent_batches' );
```

> WARNING: because of the async queue runner introduced in Action Scheduler 3.0 will continue asynchronous loopback requests to process actions, increasing the number of concurrent batches can substantially increase server load and take down a site. [WP CLI](/wp-cli/) is a better method to achieve higher throughput.

## Increasing Initialisation Rate of Runners

By default, Action scheduler initiates at most one queue runner every time the `'action_scheduler_run_queue'` action is triggered by WP Cron.

Because this action is only triggered at most once every minute, then there will rarely be more than one queue processing actions even if the concurrent runners is increased.

To handle larger queues on more powerful servers, it's possible to initiate additional queue runners whenever the `'action_scheduler_run_queue'` action is run.

That can be done by initiated additional secure requests to our server via loopback requests.

The code below demonstrates how to create 5 loopback requests each time a queue begins:

```php
/**
 * Trigger 5 additional loopback requests with unique URL params.
 */
function eg_request_additional_runners() {

	// allow self-signed SSL certificates
	add_filter( 'https_local_ssl_verify', '__return_false', 100 );

	for ( $i = 0; $i < 5; $i++ ) {
		$response = wp_remote_post( admin_url( 'admin-ajax.php' ), array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => false,
			'headers'     => array(),
			'body'        => array(
				'action'     => 'eg_create_additional_runners',
				'instance'   => $i,
				'eg_nonce' => wp_create_nonce( 'eg_additional_runner_' . $i ),
			),
			'cookies'     => array(),
		) );
	}
}
add_action( 'action_scheduler_run_queue', 'eg_request_additional_runners', 0 );

/**
 * Handle requests initiated by eg_request_additional_runners() and start a queue runner if the request is valid.
 */
function eg_create_additional_runners() {

	if ( isset( $_POST['eg_nonce'] ) && isset( $_POST['instance'] ) && wp_verify_nonce( $_POST['eg_nonce'], 'eg_additional_runner_' . $_POST['instance'] ) ) {
		ActionScheduler_QueueRunner::instance()->run();
	}

	wp_die();
}
add_action( 'wp_ajax_nopriv_eg_create_additional_runners', 'eg_create_additional_runners', 0 );
```

> WARNING: because of the processing rate of scheduled actions, this kind of increase can very easily take down a site. Use only on high powered servers and be sure to test before attempting to use it in production.

## High Volume Plugin

It's not necessary to add all of this code yourself, there is a handy plugin to get access to each of these increases - the [Action Scheduler - High Volume](https://github.com/woocommerce/action-scheduler-high-volume) plugin.
