/**
 * External dependencies
 */
import { connect } from 'react-redux';

/**
 * Internal dependencies
 */
import { selectEntity } from '../actions';
import EntityList from '../components/EntityList';
import log from '../../modules/log';

const mapStateToProps = ( state ) => {
	return {
		entities: state.entities
	}
};

const mapDispatchToProps = ( dispatch ) => {
	return {
		onClick: ( id ) => {

			log( 'dispatching ' + id );
			dispatch( selectEntity( id ) )
		}
	}
};

const SimpleContainer = connect(
	mapStateToProps,
	mapDispatchToProps
)( EntityList );

export default SimpleContainer;
