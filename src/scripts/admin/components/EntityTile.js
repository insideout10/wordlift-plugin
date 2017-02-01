/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import { Wrapper, Main, Count, Label, Cloud, Drawer } from './styles';

export default class EntityTile extends React.PureComponent {

	/**
	 * @inheritDoc
	 */
	constructor() {
		super();

		// Bind our functions.
		this.onClick = this.onClick.bind( this );
	}

	/**
	 * Link an entity.
	 *
	 * @since 3.10.0
	 *
	 * @param {Event} e The source {@link Event}.
	 */
	onClick( e ) {
		this.props.onClick( e, this.props );
	}

	render() {
		return (
			<Wrapper>
				<Main onClick={ this.props.onClick }>
					<Count>0</Count>
					<Label>{ this.props.entity.label }</Label>
					<Cloud className="fa fa-cloud" />
				</Main>
				<Drawer />
			</Wrapper>
		);
	}

}
