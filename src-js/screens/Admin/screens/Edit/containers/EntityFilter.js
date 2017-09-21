/**
 * External dependencies
 */
import { connect } from 'react-redux';

/**
 * Internal dependencies
 */
import { setEntityVisibility } from '../actions';
import Link from '../components/Link';

/**
 * @inheritDoc
 */
const mapStateToProps = ( state, ownProps ) => {
	return {
		active: ownProps.filter === state.visibilityFilter
	};
};

/**
 * @inheritDoc
 */
const mapDispatchToProps = ( dispatch ) => {
	return {
		/**
		 * Set the entity visibility filter when a link is clicked.
		 *
		 * @since 3.11.0
		 *
		 * @param {string} filter The filter (who, where, when, what, all).
		 */
		onClick: ( filter ) => {
			dispatch( setEntityVisibility( filter ) );
		}
	};
};

/**
 * The `EntityListContainer` instance built by `react-redux` by connecting the
 * store with the state and dispatchers merged to properties passed to the React
 * components.
 *
 * @since 3.11.0
 */
const EntityFilter = connect(
	mapStateToProps,
	mapDispatchToProps
)( Link );

// Finally export the `EntityFilter`.
export default EntityFilter;
