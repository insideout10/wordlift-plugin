/**
 * Components: Switch component.
 *
 * Represents a switch, forwards clicks to the parent. State is handled by the
 * parent via the `on` property.
 *
 * @since 3.11.0
 */

/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import Wrapper from './Wrapper';
import Background from './Background';
import Bullet from './Bullet';
import Label from './Label';

/**
 * Define the `Switch`.
 *
 * @since 3.11.0
 */
class Switch extends React.PureComponent {

	/**
	 * @inheritDoc
	 */
	constructor( props ) {
		super( props );

		// Bind the event handler.
		this.onClick = this.onClick.bind( this );
	}

	/**
	 * Handle clicks.
	 *
	 * @since 3.11.0
	 * @param {Event} e The source {@link Event}.
	 */
	onClick( e ) {
		e.preventDefault();

		// Forward the click event.
		this.props.onClick( e );
	}

	/**
	 * @inheritDoc
	 */
	render() {
		return (
			<Wrapper onClick={ this.onClick }>
				<Background selected={ this.props.selected }>
					<Bullet selected={ this.props.selected } />
				</Background>
				<Label selected={ this.props.selected }>
					{ this.props.children }</Label>
			</Wrapper>
		);
	}

}

// Finally export the `Switch`.
export default Switch;
