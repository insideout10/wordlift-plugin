/**
 * FaqList for showing the list of questions.
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
import Question from "../question";
import Answer from "../answer";
import {WlCard} from "../../../common/components/wl-card";
import {questionSelectedByUser} from "../../actions";

class FaqList extends React.Component {
  constructor(props) {
    super(props);
    this.faqItemClicked = this.faqItemClicked.bind(this);
    this.noFaqItemsText = window["_wlFaqSettings"]["noFaqItemsText"];
  }
  faqItemClicked(id) {
    this.props.dispatch(questionSelectedByUser(id));
  }
  render() {
    return (
      <React.Fragment>
        {this.props.faqItems.length === 0 && (
          <WlCard alignCenter={true}>
            <h3>{this.noFaqItemsText}</h3>
          </WlCard>
        )}
        {this.props.faqItems.map(item => {
          return (
            <React.Fragment key={item.id}>
              <WlCard
                onClickHandler={() => {
                  this.faqItemClicked(item.id);
                }}
              >
                <Question question={item.question} />
                <Answer answer={item.answer} />
              </WlCard>
            </React.Fragment>
          );
        })}
      </React.Fragment>
    );
  }
}

export default connect(state => ({
  faqItems: [] // state.faqListOptions.faqItems
}))(FaqList);
