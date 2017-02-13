/**
 * Components: Entity Tile.
 *
 * The `EntityTile` component is loaded from an `EntityList` component. It's
 * tile representing a single entity. It expects two properties:
 *  * `entity`, representing the entity for the tile,
 *
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
import { Wrapper, Main, Count, Label, Cloud, Drawer } from './styles';

/**
 *
 */
class EntityTile extends React.PureComponent {

	/**
	 * @inheritDoc
	 */
	constructor() {
		super();

		// Bind our functions.
		this.onClick = this.onClick.bind( this );
	}

	/**
	 * Handle clicks by forwarding the event to the handler (defined in
	 * `EntityListContainer`).
	 *
	 * @since 3.10.0
	 *
	 * @param {Event} e The source {@link Event}.
	 */
	onClick( e ) {
		// Prevent propagation.
		e.preventDefault();

		// Call the handler.
		this.props.onClick( this.props.entity );
	}

	/**
	 * Render the component.
	 *
	 * @since 3.11.0
	 * @returns {XML} The render tree.
	 */
	render() {
		// @todo: populate the count.
		return (
			<Wrapper entity={ this.props.entity }>
				<Main onClick={ this.onClick }>
					<Count entity={ this.props.entity }>0</Count>
					<Label
						entity={ this.props.entity }>{ this.props.entity.label }</Label>
					<Cloud className="fa fa-cloud"
						   entity={ this.props.entity } />
				</Main>
				<Drawer />
			</Wrapper>
		);
	}

}

// Finally export the class.
export default EntityTile;
