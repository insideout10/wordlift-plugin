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
import Wrapper from './Wrapper';
import Main from './Main';
import Count from './Count';
import Label from './Label';
import Cloud from './Cloud';
import Drawer from './Drawer';
import Switch from '../Switch';
import Category from './Category';
import EditLink from './EditLink';
import ArrowToggle from '../ArrowToggle';

/**
 * @inheritDoc
 */
class EntityTile extends React.Component {

	/**
	 * @inheritDoc
	 */
	constructor( props ) {
		super( props );

		// Bind our functions.
		this.onEditClick = this.onEditClick.bind( this );
		this.onSwitchClick = this.onSwitchClick.bind( this );
		this.onMainClick = this.onMainClick.bind( this );
		this.onArrowToggleClick = this.onArrowToggleClick.bind( this );
		this.close = this.close.bind( this );
		this.setWrapperRef = this.setWrapperRef.bind( this );

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
	onSwitchClick( e ) {
		// Prevent propagation.
		e.preventDefault();

		// Call the handler.
		this.props.onLinkClick( this.props.entity );
	}

	/**
	 * Handle trigger clicks, toggling the drawer's open/close state.
	 *
	 * @since 3.11.0
	 *
	 * @param {Event} e The source {@link Event}.
	 */
	onArrowToggleClick( e ) {
		e.preventDefault();

		// Call the handler.
		this.setState( { open: ! this.state.open } );
	}

	/**
	 * Close the `Drawer` (if open).
	 *
	 * @since 3.11.0
	 * @param {Event} e The source {@link Event}.
	 */
	close( e ) {
		e.preventDefault();

		// Close if open.
		if ( ! e.currentTarget.contains( document.activeElement ) && this.state.open ) {
			this.setState( { open: false } );
		}
	}

	/**
	 * When the component is updated with the open flag, set the focus.
	 *
	 * @since 3.11.0
	 */
	componentDidUpdate() {
		if ( this.state.open && this.setWrapperRef ) {
			this.setWrapperRef.focus();
		}
	}

	/**
	 * Set a reference to the `Wrapper` element.
	 *
	 * @since 3.11.0
	 *
	 * @param {Object} element The `Wrapper` DOM element.
	 */
	setWrapperRef( element ) {
		// Set the reference to the wrapper.
		this.setWrapperRef = element;
	}

	/**
	 * Render the component.
	 *
	 * @since 3.11.0
	 * @returns {XML} The render tree.
	 */
	render() {
		return (
			<Wrapper entity={ this.props.entity }
					 onBlur={ this.close }
					 innerRef={ this.setWrapperRef }
					 tabIndex="0">
				<Main onClick={ this.onMainClick }
					  open={ this.state.open }>
					<Count entity={ this.props.entity }>
						{ this.props.entity.occurrences.length }</Count>
					<Label entity={ this.props.entity }>
						{ this.props.entity.label }</Label>
					<Cloud className="fa fa-cloud"
						   local={ this.props.entity.local } />
				</Main>
				<Drawer open={ this.state.open }>
					<Switch onClick={ this.onSwitchClick }
							selected={ this.props.entity.link }>
						Link </Switch>
					<Category>{ this.props.entity.mainType }</Category>
					<EditLink onClick={ this.onEditClick }
							  className="fa fa-pencil" />
				</Drawer>
				<ArrowToggle onClick={ this.onArrowToggleClick }
							 open={ this.state.open }
							 show={ 0 < this.props.entity.occurrences.length } />
			</Wrapper>
		);
	}

}

// Finally export the class.
export default EntityTile;
