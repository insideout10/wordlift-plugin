/**
 * AddRuleGroupButton : it handles adding the new rule group.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";
import {connect} from "react-redux"

/**
 * Internal dependencies.
 */
import {ADD_NEW_RULE_GROUP_ACTION} from "../../actions/actions";

class _AddRuleGroupButton extends React.Component {
    constructor(props) {
        super(props);
        this.addNewRuleGroupHandler = this.addNewRuleGroupHandler.bind(this);
    }
    addNewRuleGroupHandler() {
        this.props.dispatch(ADD_NEW_RULE_GROUP_ACTION);
    }
    render()
    {
        return (
            <div className="wl-container">
                <div className="wl-col">
                    <button
                        className="button action wl-add-rule-group"
                        onClick={() => {
                            this.addNewRuleGroupHandler();
                        }}
                    >
                        Add Rule Group
                    </button>
                </div>
            </div>
        )
    }
}

export const AddRuleGroupButton = connect()(_AddRuleGroupButton);