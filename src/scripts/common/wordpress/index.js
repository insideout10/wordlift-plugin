/**
 * WordPress: a wrapper for WordPress' global objects.
 *
 * @since 3.12.0
 */

// Set the context to the main window, keeping the references to `jQuery` and
// `wp` also when loaded inside the TinyMCE `iframe` (see
// `admin-tinymce-views`).
const context = window.frameElement ? window.parent : window;

// Export the `jQuery` instance.
export const $ = context.jQuery;

export const _ = context._;

export const tinyMCE = context.tinyMCE;

export const wp = context.wp;

// Export the `wp.ajax.post` function.
export const post = context.wp.ajax.post;

// Export WordLift settings:
export const _wlSettings = context.wlSettings;
export const _wlNavigator = context._wlNavigator;
