---
description: Reference guide for background processing functions provided by the Action Scheduler job queue for WordPress.
---
# API Reference

Action Scheduler provides a range of functions for scheduling hooks to run at some time in the future on one or more occassions.

To understand the scheduling functions, it can help to think of them as extensions to WordPress' `do_action()` function that add the ability to delay and repeat when the hook will be triggered.

## WP-Cron APIs vs. Action Scheduler APIs

The Action Scheduler API functions are designed to mirror the WordPress [WP-Cron API functions](http://codex.wordpress.org/Category:WP-Cron_Functions).

Functions return similar values and accept similar arguments to their WP-Cron counterparts. The notable differences are:

* `as_schedule_single_action()` & `as_schedule_recurring_action()` will return the ID of the scheduled action rather than boolean indicating whether the event was scheduled
* `as_schedule_recurring_action()` takes an interval in seconds as the recurring interval rather than an arbitrary string
* `as_schedule_single_action()` & `as_schedule_recurring_action()` can accept a `$group` parameter to group different actions for the one plugin together.
* the `wp_` prefix is substituted with `as_` and the term `event` is replaced with `action`

## API Function Availability

As mentioned in the [Usage - Load Order](usage.md#load-order) section, Action Scheduler will initialize itself on the `'init'` hook with priority `1`. While API functions are loaded prior to this and can be called, they should not be called until after `'init'` with priority `1`, because each component, like the data store, has not yet been initialized.

Do not use Action Scheduler API functions prior to `'init'` hook with priority `1`. Doing so could lead to unexpected results, like data being stored in the incorrect location.

Action Scheduler provides `Action_Scheduler::is_initialized()` for use in hooks to confirm that the data stores have been initialized.

## Function Reference / `as_enqueue_async_action()`

### Description

Enqueue an action to run one time, as soon as possible.

### Usage

```php
as_enqueue_async_action( $hook, $args, $group );
```

### Parameters

- **$hook** (string)(required) Name of the action hook.
- **$args** (array) Arguments to pass to callbacks when the hook triggers. Default: _`array()`_.
- **$group** (string) The group to assign this job to. Default: _''_.
- **$unique** (boolean) Whether the action should be unique. Default: _`false`_.

### Return value

`(integer)` the action's ID.


## Function Reference / `as_schedule_single_action()`

### Description

Schedule an action to run one time at some defined point in the future.

### Usage

```php
as_schedule_single_action( $timestamp, $hook, $args, $group );
```

### Parameters

- **$timestamp** (integer)(required) The Unix timestamp representing the date you want the action to run.
- **$hook** (string)(required) Name of the action hook.
- **$args** (array) Arguments to pass to callbacks when the hook triggers. Default: _`array()`_.
- **$group** (string) The group to assign this job to. Default: _''_.
- **$unique** (boolean) Whether the action should be unique. Default: _`false`_.

### Return value

`(integer)` the action's ID.


## Function Reference / `as_schedule_recurring_action()`

### Description

Schedule an action to run repeatedly with a specified interval in seconds.

### Usage

```php
as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args, $group );
```

### Parameters

- **$timestamp** (integer)(required) The Unix timestamp representing the date you want the action to run.
- **$interval_in_seconds** (integer)(required) How long to wait between runs.
- **$hook** (string)(required) Name of the action hook.
- **$args** (array) Arguments to pass to callbacks when the hook triggers. Default: _`array()`_.
- **$group** (string) The group to assign this job to. Default: _''_.
- **$unique** (boolean) Whether the action should be unique. Default: _`false`_.

### Return value

`(integer)` the action's ID.


## Function Reference / `as_schedule_cron_action()`

### Description

Schedule an action that recurs on a cron-like schedule. 

If execution of a cron-like action is delayed, the next attempt will still be scheduled according to the provided cron expression.

### Usage

```php
as_schedule_cron_action( $timestamp, $schedule, $hook, $args, $group );
```

### Parameters

- **$timestamp** (integer)(required) The Unix timestamp representing the date you want the action to run.
- **$schedule** (string)(required) $schedule A cron-like schedule string, see http://en.wikipedia.org/wiki/Cron.
- **$hook** (string)(required) Name of the action hook.
- **$args** (array) Arguments to pass to callbacks when the hook triggers. Default: _`array()`_.
- **$group** (string) The group to assign this job to. Default: _''_.
- **$unique** (boolean) Whether the action should be unique. Default: _`false`_.

### Return value

`(integer)` the action's ID.


## Function Reference / `as_unschedule_action()`

### Description

Cancel the next occurrence of a scheduled action.

### Usage

```php
as_unschedule_action( $hook, $args, $group );
```

### Parameters

- **$hook** (string)(required) Name of the action hook.
- **$args** (array) Arguments passed to callbacks when the hook triggers. Default: _`array()`_.
- **$group** (string) The group the job is assigned to. Default: _''_.

### Return value

`(null)`

## Function Reference / `as_unschedule_all_actions()`

### Description

Cancel all occurrences of a scheduled action.

### Usage

```php
as_unschedule_all_actions( $hook, $args, $group )
```

### Parameters

- **$hook** (string)(required) Name of the action hook.
- **$args** (array) Arguments passed to callbacks when the hook triggers. Default: _`array()`_.
- **$group** (string) The group the job is assigned to. Default: _''_.

### Return value

`(string|null)` The scheduled action ID if a scheduled action was found, or null if no matching action found.


## Function Reference / `as_next_scheduled_action()`

### Description

Returns the next timestamp for a scheduled action.

### Usage

```php
as_next_scheduled_action( $hook, $args, $group );
```

### Parameters

- **$hook** (string)(required) Name of the action hook. Default: _none_.
- **$args** (array) Arguments passed to callbacks when the hook triggers. Default: _`array()`_.
- **$group** (string) The group the job is assigned to. Default: _''_.

### Return value

`(integer|boolean)` The timestamp for the next occurrence of a pending scheduled action, true for an async or in-progress action or false if there is no matching action.


## Function Reference / `as_has_scheduled_action()`

### Description

Check if there is a scheduled action in the queue, but more efficiently than as_next_scheduled_action(). It's recommended to use this function when you need to know whether a specific action is currently scheduled. _Available since 3.3.0._

### Usage

```php
as_has_scheduled_action( $hook, $args, $group );
```

### Parameters

- **$hook** (string)(required) Name of the action hook. Default: _none_.
- **$args** (array) Arguments passed to callbacks when the hook triggers. Default: _`array()`_.
- **$group** (string) The group the job is assigned to. Default: _''_.

### Return value

`(boolean)` True if a matching action is pending or in-progress, false otherwise.


## Function Reference / `as_get_scheduled_actions()`

### Description

Find scheduled actions.

### Usage

```php
as_get_scheduled_actions( $args, $return_format );
```

### Parameters

- **$args** (array) Arguments to search and filter results by. Possible arguments, with their default values:
    * `'hook' => ''` - the name of the action that will be triggered
    * `'args' => NULL` - the args array that will be passed with the action
    * `'date' => NULL` - the scheduled date of the action. Expects a DateTime object, a unix timestamp, or a string that can parsed with strtotime().
    * `'date_compare' => '<=`' - operator for testing "date". accepted values are '!=', '>', '>=', '<', '<=', '='
    * `'modified' => NULL` - the date the action was last updated. Expects a DateTime object, a unix timestamp, or a string that can parsed with strtotime().
    * `'modified_compare' => '<='` - operator for testing "modified". accepted values are '!=', '>', '>=', '<', '<=', '='
    * `'group' => ''` - the group the action belongs to
    * `'status' => ''` - ActionScheduler_Store::STATUS_COMPLETE or ActionScheduler_Store::STATUS_PENDING
    * `'claimed' => NULL` - TRUE to find claimed actions, FALSE to find unclaimed actions, a string to find a specific claim ID
    * `'per_page' => 5` - Number of results to return
    * `'offset' => 0`
    * `'orderby' => 'date'` - accepted values are 'hook', 'group', 'modified', or 'date'
    * `'order' => 'ASC'`
- **$return_format** (string) The format in which to return the scheduled actions: 'OBJECT', 'ARRAY_A', or 'ids'. Default: _'OBJECT'_.

### Return value

`(array)` Array of action rows matching the criteria specified with `$args`.
