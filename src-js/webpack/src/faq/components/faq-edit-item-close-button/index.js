/**
 * FaqEditItemCloseButton for closing the edit screen.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */
/**
 * External dependencies.
 */
import React from "react";
import {connect} from "react-redux";
/**
 * Internal dependencies.
 */
import {closeEditScreen} from "../../actions";
import {WlContainer} from "../../../mappings/blocks/wl-container";
import {WlColumn} from "../../../mappings/blocks/wl-column";
import "./index.scss"

class FaqEditItemCloseButton extends React.Component {
  render() {
    return (
      <React.Fragment>
        <WlContainer>
          <WlColumn className={"wl-col--width-90 wl-col--less-padding"} />
          <WlColumn className={"wl-col--width-10 wl-col--less-padding"}>
            <span
              className="dashicons dashicons-no-alt faq-edit-item-close-button"
              onClick={() => {
                this.props.dispatch(closeEditScreen());
              }}
            />
          </WlColumn>
        </WlContainer>
      </React.Fragment>
    );
  }
}
export default connect()(FaqEditItemCloseButton);
