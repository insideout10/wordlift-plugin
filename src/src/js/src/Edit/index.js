/*global wp jQuery */
/**
 * App: WordLift Admin.
 *
 * This is the main entry point for WordLift's admin client application,
 * currently handling the classification box in the post/page edit screen.
 *
 * The application is structured in a Redux provider which encloses:
 *  * an EntityListContainer container, based on `react-redux` binds state and
 *    dispatchers, which contains:
 *  * an EntityList component which, in turn, loads
 *  * an EntityTile component for each entity.
 *
 * The application is activated when an `analysis.result` is fired via WP's
 * Backbone subsystem in the `wordlift` namespace.
 *
 * @since 3.20.0 The single load functions are split in the index.*.js files.
 * @since 3.11.0
 */
/*
 * Internal dependencies
 */
import "./index.scss";
// Load the Classification Box.
import "./index.classification-box";
// Load the Autocomplete Select.
import "./index.autocomplete-select";
// Load the Schema Class Tree.
import "./index.schema-class-tree";
// Load the Schema Properties Form.
import "./index.schema-property-form";
