/**
 * @since 3.24.0
 * 
 * RuleGroupListComponent : it displays the list of rule groups, let the user
 * add new rule groups
 */

import React from 'react'
import PropTypes from 'prop-types';
import RuleGroupComponent from './RuleGroupComponent';

class RuleGroupListComponent extends React.Component {
    constructor(props) {
        super(props)
        
    }

    addNewRuleGroupHandler = ()=> {
        this.setState(prevState => ({
            ruleGroupList: [...prevState.ruleGroupList, {}]
        }))
    }

    state = {
        ruleGroupList: (this.props.ruleGroupList == undefined 
        || this.props.ruleGroupList.length == 0) ? [{}] : this.props.ruleGroupList
    }
    render() {
        return (
            <React.Fragment>
                {
                    this.state.ruleGroupList.map((item, index)=> {
                        return (
                            <React.Fragment>
                                <RuleGroupComponent rules={[]}/>
                                <div className="wl-container">
                                    <div className="wl-col">
                                        <b>Or</b>
                                    </div>
                                </div>
                            </React.Fragment> 
                        )
                    }) 
                }

                <div className="wl-container">
                    <div className="wl-col">
                        <button 
                        className="button action wl-add-rule-group"
                        onClick={()=> { this.addNewRuleGroupHandler() }}>
                             Add Rule Group 
                        </button>
                    </div>
                </div>
            </React.Fragment>
        )
    }
}

RuleGroupListComponent.propTypes = {
    ruleGroupList: PropTypes.array
}
export default RuleGroupListComponent