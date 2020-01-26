/**
 * Components: Arrow Toggle component.
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
import Arrow from './Arrow';

/**
 * @inheritDoc
 */
class ArrowToggle extends React.PureComponent {

	/**
	 * @inheritDoc
	 */
	constructor( props ) {
		super( props );

		// Bind this.
		this.onClick = this.onClick.bind( this );
	}

	/**
	 * Handle clicks and forward them to the parent handler.
	 *
	 * @since 3.11.0
	 *
	 * @param {Event} e The source {@link Event}.
	 */
	onClick( e ) {
		e.preventDefault();

		// Forward the click.
		this.props.onClick( e );
	}

	/**
	 * @inheritDoc
	 */
	render() {
		return (
			<Wrapper onClick={ this.onClick }
					 show={ this.props.show }>
				<Arrow open={ this.props.open } />
			</Wrapper>
		);
	}
}

// Finally export the `ArrowToggle`.
export default ArrowToggle;
