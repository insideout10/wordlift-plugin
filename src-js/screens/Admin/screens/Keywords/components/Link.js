/**
 * Components: Link.
 *
 * @since 3.18.0
 */

/**
 * External dependencies
 */
import React from 'react';

/**
 * Define the `Link` class.
 *
 * @since 3.18.0
 */
class Link extends React.Component {

	/**
	 * @inheritDoc
	 */
	render() {
		return (
			<a
				href={ this.props.href }
				className={ this.props.className }
				target={ this.props.blank ? '_blank' : '' }
			>
				{ this.props.children }
			</a>
		);
	}

}

// Finally export the `Link`.
export default Link;
