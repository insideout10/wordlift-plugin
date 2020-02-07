/**
 * FaqEditItem for the faq item.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * external dependencies
 */
import React from "react";
import { connect } from "react-redux";
/**
 * Internal dependencies.
 */
import FaqEditButtonGroup from "../faq-edit-button-group";
import { WlContainer } from "../../../mappings/blocks/wl-container";
import { WlColumn } from "../../../mappings/blocks/wl-column";
import PropTypes from "prop-types";
import { updateFaqItem } from "../../actions";

export const faqEditItemType = {
  ANSWER: "ANSWER",
  QUESTION: "QUESTION"
};

class FaqEditItem extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      textAreaValue: ""
    };
    this.changeValueOnUserType = this.changeValueOnUserType.bind(this);
    this.updateFaqEditItem = this.updateFaqEditItem.bind(this);
  }
  updateFaqEditItem() {
    const action = updateFaqItem();
    action.payload = {
      id: this.props.id,
      type: this.props.type,
      value: this.state.textAreaValue
    };
    this.props.dispatch(action);
  }
  changeValueOnUserType(event) {
    this.setState({
      textAreaValue: event.target.value
    });
  }
  render() {
    return (
      <React.Fragment>
        <b>{this.props.title}</b>
        <br />
        <WlContainer>
          <WlColumn className={"wl-col--width-100 wl-col--less-padding"}>
            <textarea
              cols={25}
              rows={3}
              defaultValue={this.props.value}
              onChange={e => {
                this.changeValueOnUserType(e);
              }}
            />
          </WlColumn>
        </WlContainer>
        <FaqEditButtonGroup updateHandler={this.updateFaqEditItem}/>
      </React.Fragment>
    );
  }
}
FaqEditItem.propTypes = {
  // Edit item type it can be question or answer.
  type: PropTypes.string,
  // Faq item id.
  id: PropTypes.number
};
export default connect()(FaqEditItem);
