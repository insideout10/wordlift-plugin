/**
 * Angular Controllers: Edit Post Widget Controller.
 *
 * This adapter provides a bridge to the legacy AngularJS app.
 *
 * @since 3.11.0
 */

// Cache the instance.
let scope;

/**
 * Get the `EditPostWidgetController`'s scope.
 *
 * @since 3.11.0
 * @returns {{onSelectedEntityTile: (function)}} The `EditPostWidgetController`
 *     instance.
 * @constructor
 */
function EditPostWidgetController() {
	// Return the cached instance or get the instance and cache it.
	return scope ? scope : scope = angular.element(
			jQuery( '[ng-controller="EditPostWidgetController"]' )
		).scope();
}

// Finally export the function.
export default EditPostWidgetController;
