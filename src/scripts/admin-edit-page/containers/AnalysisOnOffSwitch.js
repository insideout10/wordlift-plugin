/**
 * External dependencies
 */
import { connect } from 'react-redux';

import Switch from '../components/Switch';
import { switchAnalysisOnOff } from '../actions';

/**
 * @inheritDoc
 */
const mapStateToProps = ( state, ownProps ) => (
	{ selected: state.analysisEnabled }
);

/**
 * @inheritDoc
 */
const mapDispatchToProps = ( dispatch ) => {
	return {
		/**
		 * Set the entity visibility filter when a link is clicked.
		 *
		 * @since 3.14.0
		 */
		onClick: () => dispatch( switchAnalysisOnOff() )
	};
};

/**
 * The `EntityListContainer` instance built by `react-redux` by connecting the
 * store with the state and dispatchers merged to properties passed to the React
 * components.
 *
 * @since 3.11.0
 */
const AnalysisOnOffSwitch = connect(
	mapStateToProps,
	mapDispatchToProps
)( Switch );

// Finally export the `EntityFilter`.
export default AnalysisOnOffSwitch;
