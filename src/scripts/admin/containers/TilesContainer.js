/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import Tile from '../components/Tile';
import log from '../../modules/log';

// Define props and state
const tiles = [
	{
		occurrences: 10,
		entity: 'Orso Verde',
		category: 'Oggetto sulla scrivania',
		isOpen: false,
		isSelected: false,
		isLinked: false
	}, {
		occurrences: 13,
		entity: 'Orso Giallo',
		category: 'Oggetto sulla scrivania',
		isOpen: false,
		isSelected: false,
		isLinked: false
	}, {
		occurrences: 34,
		entity: 'Orso Blu',
		category: 'Oggetto sulla scrivania',
		isOpen: false,
		isSelected: false,
		isLinked: false
	}
];

export default class TilesContainer extends React.Component {

	/**
	 * @inheritDoc
	 */
	constructor( props ) {
		super();

		log( props.analysis.entities );

		// Bind our functions.
		this.select = this.select.bind( this );
		this.open = this.open.bind( this );
		this.link = this.link.bind( this );

		// Set the state.
		this.state = {
			entities: props.analysis.entities,
			tiles: tiles
		};
	}

	/**
	 * Select the {@link Tile} at the specified index.
	 *
	 * @since 3.10.0
	 *
	 * @param {Event} e The event.
	 * @param {number} i The {@link Tile} index.
	 */
	select( e, i ) {
		// Stop event propagation.
		e.stopPropagation();

		if ( ! this.state.tiles[ i ].isOpen ) {
			this.state.tiles[ i ].isSelected = ! this.state.tiles[ i ].isSelected;
			this.setState( { tiles: tiles } );
		}
	}

	/**
	 * Open the {@link Tile} at the specified index.
	 *
	 * @since 3.10.0
	 *
	 * @param {Event} e The event.
	 * @param {number} i The {@link Tile} index.
	 */
	open( e, i ) {
		e.stopPropagation();
		this.state.tiles[ i ].isOpen = ! this.state.tiles[ i ].isOpen;
		this.setState( { tiles: tiles } );
	}

	/**
	 * Link the {@link Tile} at the specified index.
	 *
	 * @since 3.10.0
	 *
	 * @param {Event} e The event.
	 * @param {number} i The {@link Tile} index.
	 */
	link( e, i ) {
		e.stopPropagation();
		this.state.tiles[ i ].isLinked = ! this.state.tiles[ i ].isLinked;
		this.setState( { tiles: tiles } );
	}

	/**
	 * @inheritDoc
	 */
	render() {
		const tile = { isOpen: false, isLinked: false };

		return (
			<div>
				{Object.keys( this.state.entities ).map(
					( key, i ) =>
						<Tile
							key={ i }
							index={ i }
							entity={ this.state.entities[ key ] }
							tile={ tile }
							select={ this.select }
							open={ this.open }
							link={ this.link } />
				)}
			</div>
		);
	}
}
