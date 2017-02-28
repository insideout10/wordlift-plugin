/**
 * Components: Link.
 *
 * The `Link` component is a link which handles an `onClick` event.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import React from 'react';

/**
 * Define the `Link` class.
 *
 * @since 3.11.0
 */
class Link extends React.PureComponent {

	/**
	 * @inheritDoc
	 */
	constructor( props ) {
		super( props );

		this.onClick = this.onClick.bind( this );
	}

	/**
	 * Reroute clicks to the container.
	 *
	 * @since 3.11.0
	 *
	 * @param {Event} e The source {@link Event}.
	 */
	onClick( e ) {
		e.preventDefault();

		this.props.onClick( this.props.filter );
	}

	/**
	 * @inheritDoc
	 */
	render() {
		return (
			<a href="javascript:void(0);"
			   className={ this.props.active ? 'wl-active' : '' }
			   onClick={ this.onClick }>{ this.props.children }</a>
		);
	}

}

// Finally export the `Link`.
export default Link;
