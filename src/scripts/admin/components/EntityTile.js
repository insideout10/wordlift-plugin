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
import {
	Wrapper,
	Main,
	Count,
	Label,
	Cloud,
	Trigger,
	Arrow
} from './styles';
import {
	Drawer,
	LinkWrap,
//	Switch,
	LinkInd,
	Category,
	QuickEdit
} from './styles/Drawer';
import Switch from './Switch';

/**
 *
 */
class EntityTile extends React.Component {

	/**
	 * @inheritDoc
	 */
	constructor( props ) {
		super( props );

		// Bind our functions.
		this.onEditClick = this.onEditClick.bind( this );
		this.onLinkClick = this.onLinkClick.bind( this );
		this.onMainClick = this.onMainClick.bind( this );
		this.onTriggerClick = this.onTriggerClick.bind( this );

		// Set the initial state.
		this.state = { open: false };
	}

	/**
	 * Handles clicks on the `QuickEdit` element and forwards it to the parent
	 * handlers.
	 *
	 * @since 3.11.0
	 *
	 * @param {Event} e The source {@link Event}.
	 */
	onEditClick( e ) {
		// Prevent propagation.
		e.preventDefault();

		// Call the handler.
		this.props.onEditClick( this.props.entity );

		// Close the drawer.
		this.setState( { open: false } );
	}

	/**
	 * Handle clicks by forwarding the event to the handler (defined in
	 * `EntityListContainer`).
	 *
	 * @since 3.11.0
	 *
	 * @param {Event} e The source {@link Event}.
	 */
	onMainClick( e ) {
		// Prevent propagation.
		e.preventDefault();

		// Call the handler.
		this.props.onClick( this.props.entity );
	}

	/**
	 * Handles clicks on the `LinkWrap` element and forwards them to the parent
	 * handler.
	 *
	 * @since 3.11.0
	 *
	 * @param {Event} e The source {@link Event}.
	 */
	onLinkClick( e ) {
		// Prevent propagation.
		e.preventDefault();

		// Call the handler.
		this.props.onLinkClick( this.props.entity );

		// Close the drawer.
		this.setState( { open: false } );
	}

	/**
	 * Handle trigger clicks, toggling the drawer's open/close state.
	 *
	 * @since 3.11.0
	 *
	 * @param {Event} e The source {@link Event}.
	 */
	onTriggerClick( e ) {
		// Prevent propagation.
		e.preventDefault();

		// Call the handler.
		this.setState( { open: ! this.state.open } );
	}

	/**
	 * Render the component.
	 *
	 * @since 3.11.0
	 * @returns {XML} The render tree.
	 */
	render() {
		return (
			<Wrapper entity={ this.props.entity }>
				<Main onClick={ this.onMainClick }
					  open={ this.state.open }>
					<Count entity={ this.props.entity }>
						{ this.props.entity.occurrences.length }</Count>
					<Label entity={ this.props.entity }>
						{ this.props.entity.label }</Label>
					<Cloud className="fa fa-cloud"
						   entity={ this.props.entity } />
				</Main>
				<Drawer open={ this.state.open }>
					<LinkWrap onClick={ this.onLinkClick }>
						<Switch link={ this.props.entity.link } />
						<LinkInd link={ this.props.entity.link }>
							Link </LinkInd>
					</LinkWrap>
					<Category>{ this.props.entity.mainType }</Category>
					<QuickEdit onClick={ this.onEditClick }
							   className="fa fa-pencil" />
				</Drawer>
				<Trigger entity={ this.props.entity }
						 onClick={ this.onTriggerClick }>
					<Arrow open={ this.state.open } />
				</Trigger>
			</Wrapper>
		);
	}

}

// Finally export the class.
export default EntityTile;
