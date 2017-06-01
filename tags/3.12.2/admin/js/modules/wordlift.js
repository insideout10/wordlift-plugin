window.wp = window.wp || {};
window.wp.wordlift = window.wp.wordlift || {};

if ( typeof window.wp.wordlift.trigger === 'undefined' ) {
	_.extend( window.wp.wordlift, Backbone.Events );
}

export default window.wp.wordlift;
