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
import { connect } from "react-redux";

/**
 * Internal dependencies.
 */
import Question from "../question";
import Answer from "../answer";
import { WlCard } from "../../blocks/wl-card";
import { questionSelectedByUser } from "../../actions";

class FaqList extends React.Component {
  constructor(props) {
    super(props);
    this.faqItemClicked = this.faqItemClicked.bind(this)
  }
  faqItemClicked(id) {
    const action = questionSelectedByUser();
    action.payload = id;
    this.props.dispatch(action);
  }
  render() {
    return (
      <React.Fragment>
        {this.props.faqItems.map(item => {
          return (
            <React.Fragment>
              <WlCard
                onClickHandler={() => {
                  this.faqItemClicked(item.id)
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
  faqItems: state.faqItems
}))(FaqList);
