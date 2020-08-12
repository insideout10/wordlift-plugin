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
import React from "react";

/**
 * Internal dependencies
 */
import Wrapper from "./Wrapper";
import Main from "./Main";
import Label from "./Label";
import MainType from "./MainType";
import Cloud from "./Cloud";
import Drawer from "./Drawer";
import Switch from "../Switch";
import Category from "./Category";
import EditLink from "./EditLink";
import ArrowToggle from "../ArrowToggle";

const applyFilters = window.wp && window.wp.hooks && window.wp.hooks.applyFilters;

/**
 * @inheritDoc
 */
class EntityTile extends React.Component {
  /**
   * @inheritDoc
   */
  constructor(props) {
    super(props);

    // Create a reference to the DOM element, sine we want focus on the tiles
    // when they're active.
    this.ref = React.createRef();

    this.cloudIconClass = applyFilters ? applyFilters("wl_cloud_icon_class", "fa fa-cloud") : "fa fa-cloud";
    this.networkIconClass = applyFilters ? applyFilters("wl_network_icon_class", "fa fa-external-link") : "fa fa-cloud";

    // Bind our functions.
    this.onEditClick = this.onEditClick.bind(this);
    this.onSwitchClick = this.onSwitchClick.bind(this);
    this.onMainClick = this.onMainClick.bind(this);
    this.onArrowToggleClick = this.onArrowToggleClick.bind(this);
    this.close = this.close.bind(this);

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
  onEditClick(e) {
    // Prevent propagation.
    e.preventDefault();

    // Call the handler.
    this.props.onEditClick(this.props.entity);

    // Close the drawer.
    this.setState({ open: false });
  }

  /**
   * Handle clicks by forwarding the event to the handler (defined in
   * `EntityListContainer`).
   *
   * @since 3.11.0
   *
   * @param {Event} e The source {@link Event}.
   */
  onMainClick(e) {
    // Prevent propagation.
    e.preventDefault();

    // Call the handler.
    this.props.onClick(this.props.entity);
  }

  /**
   * Handles clicks on the `LinkWrap` element and forwards them to the parent
   * handler.
   *
   * @since 3.11.0
   *
   * @param {Event} e The source {@link Event}.
   */
  onSwitchClick(e) {
    // Prevent propagation.
    e.preventDefault();

    // Call the handler.
    this.props.onLinkClick(this.props.entity);
  }

  /**
   * Handle trigger clicks, toggling the drawer's open/close state.
   *
   * @since 3.11.0
   *
   * @param {Event} e The source {@link Event}.
   */
  onArrowToggleClick(e) {
    e.preventDefault();

    // Call the handler.
    this.setState({ open: !this.state.open });
  }

  /**
   * Close the `Drawer` (if open).
   *
   * @since 3.11.0
   * @param {Event} e The source {@link Event}.
   */
  close(e) {
    e.preventDefault();

    // Close if open.
    if (!e.currentTarget.contains(document.activeElement) && this.state.open) {
      this.setState({ open: false });
    }
  }

  /**
   * When the component is updated with the open flag, set the focus.
   *
   * @since 3.11.0
   */
  componentDidUpdate() {
    if (this.state.open && this.ref && this.ref.current && this.ref.current.focus) {
      this.ref.current.focus();
    }
  }

  /**
   * Render the component.
   *
   * @since 3.11.0
   * @returns {XML} The render tree.
   */
  render() {
    return (
      <Wrapper entity={this.props.entity} onBlur={this.close} ref={this.ref} tabIndex="0" key={this.props.entity.id}>
        <Main onClick={this.onMainClick} open={this.state.open}>
          <Label entity={this.props.entity}>
            {this.props.entity.label}
            <MainType entity={this.props.entity}>{this.props.entity.mainType}</MainType>
          </Label>
          {this.props.entity.local || (
            <Cloud
              className={
                this.props.entity.id.match(/https?:\/\/(?:\w+\\.)?dbpedia\.org/)
                  ? this.cloudIconClass
                  : this.networkIconClass
              }
            />
          )}
        </Main>
        <Drawer open={this.state.open}>
          <Switch onClick={this.onSwitchClick} selected={this.props.entity.link}>
            Link{" "}
          </Switch>
          <Category>{this.props.entity.mainType}</Category>
          <EditLink onClick={this.onEditClick} edit={this.props.entity.edit} className="fa fa-pencil" />
        </Drawer>
        <ArrowToggle
          onClick={this.onArrowToggleClick}
          open={this.state.open}
          show={this.props.entity.occurrences && 0 < this.props.entity.occurrences.length}
        />
      </Wrapper>
    );
  }
}

// Finally export the class.
export default EntityTile;
