/**
 * Components: Popup.
 *
 * @since 3.18.0
 */

/**
 * External dependencies
 */
import React from 'react';

import Overlay from './Overlay';
import Content from './Content';

/**
 * Define the `Popup` class.
 *
 * @since 3.18.0
 */
class Popup extends React.Component {

	/**
	 * @inheritDoc
	 */
	render() {
		return (
			<Overlay>
				<Content>
					<h4>{ this.props.heading }</h4>
					{ this.props.children }
				</Content>
			</Overlay>
		);
	}
}

// Finally export the `Popup`.
export default Popup;
