/**
 * RuleGroupListComponent : it displays the list of rule groups, let the user
 * add new rule groups
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */


import React from 'react'
import PropTypes from 'prop-types';
import RuleGroupComponent from './RuleGroupComponent';
import { connect } from 'react-redux'
import { ADD_NEW_RULE_GROUP_ACTION } from '../actions/actions'
class RuleGroupListComponent extends React.Component {
    constructor(props) {
        super(props)   
    }

    addNewRuleGroupHandler = ()=> {
        this.props.dispatch(ADD_NEW_RULE_GROUP_ACTION)
    }

    render() {
        return (
            <React.Fragment>
                {
                    this.props.ruleGroupList.map((item, index)=> {
                        return (
                            <React.Fragment>
                                <RuleGroupComponent 
                                rules={item.rules} 
                                ruleGroupIndex={index}/>
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

const mapStateToProps = function(state){ 
    return {
        ruleGroupList: state.RuleGroupData.ruleGroupList,
    }

}

export default connect(mapStateToProps)(RuleGroupListComponent)