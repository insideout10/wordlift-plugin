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
import { WlCard } from "../../../common/components/wl-card";
import { questionSelectedByUser } from "../../actions";
import { FaqListHeader } from "../faq-list-header";

class FaqList extends React.Component {
  constructor(props) {
    super(props);
    this.faqItemClicked = this.faqItemClicked.bind(this);
  }
  faqItemClicked(id) {
    const action = questionSelectedByUser();
    action.payload = id;
    this.props.dispatch(action);
  }
  render() {
    return (
      <React.Fragment>
        <FaqListHeader />
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
  faqItems: state.faqListOptions.faqItems
}))(FaqList);
