/**
 * FaqApplyList shows a list of questions without answer.
 * @since 3.26.0
 * @author Naveen Muthusamy
 *
 */

/**
 * External dependencies.
 */
import React from 'react'
import {connect} from "react-redux";
import {WlCard} from "../../blocks/wl-card";
import Question from "../question";
import {WlContainer} from "../../../mappings/blocks/wl-container";
import WlActionButton from "../wl-action-button";
import {WlColumn} from "../../../mappings/blocks/wl-column";

class FaqApplyList extends React.Component {
    render() {
        return this.props.faqItems.filter(e => e.answer.length === 0)
            .map( e =>  {
                return (
                    <WlCard>
                        <WlContainer>
                            <WlColumn className={"wl-col--width-90"}>
                                <Question question={e.question} />
                            </WlColumn>
                            <WlColumn className={"wl-col--width-10"}>
                                <WlActionButton text={"apply"} className={"wl-action-button--primary"} />
                            </WlColumn>
                        </WlContainer>
                    </WlCard>
                )
            })
    }
}

export default connect(state => ({
    faqItems: state.faqListOptions.faqItems
}))(FaqApplyList);
