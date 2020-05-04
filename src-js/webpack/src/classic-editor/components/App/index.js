/**
 * Applications: Classification Box.
 *
 * This is the main entry point for the Classification Box application published
 * from the `index.js` file inside a Redux `Provider`.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';
import { Fragment } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Wrapper from './Wrapper';
import Header from '../Header';
import VisibleEntityList from '../../containers/VisibleEntityList';
import Accordion from '../Accordion';
import AddEntity from '../../components/AddEntity';
import { addEntityRequest, createEntityRequest } from '../AddEntity/actions';
import FaqScreen from '../../../faq/components/faq-screen';

const wlSettings = global['wlSettings'];
const canCreateEntities =
	'undefined' !== wlSettings['can_create_entities'] &&
	'yes' === wlSettings['can_create_entities'];

const withPortal = (WrappedComponent, elementId) => (props) =>
	ReactDOM.createPortal(
		<WrappedComponent {...props} />,
		document.getElementById(elementId)
	);

const FaqScreenPortal = withPortal(FaqScreen, 'wl-faq-meta-list-box');

export const ContentClassificationPanel = (props) => (
	<Fragment>
		<AddEntity {...props} />
		<Accordion open={true} label="Content classification">
			<Header />
			<VisibleEntityList />
		</Accordion>
	</Fragment>
);

/**
 * Define the {@link App}.
 *
 * @since 3.11.0
 * @return {Function} The `render` function.
 */
const App = ({ addEntityRequest, createEntityRequest }) => (
	<Wrapper>
		<ContentClassificationPanel
			createEntity={createEntityRequest}
			showCreate={canCreateEntities}
			selectEntity={addEntityRequest}
		/>
		<FaqScreenPortal />
	</Wrapper>
);

// Finally export the `App`.
export default connect(null, { addEntityRequest, createEntityRequest })(App);
