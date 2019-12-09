/**
 * @since 3.24.0
 * 
 * RuleGroupComponent : it displays the list of rules, let the user
 * add new rules
 */
import React from 'react'
import PropTypes from 'prop-types';
import RuleComponent from './RuleComponent';

 class RuleGroupComponent extends React.Component {
     constructor(props) {
         super(props)
     }
     state = {
         rules: this.props.rules.length == 0 ? [{}] : this.props.rules
     }
     /**
      * Add a new rule after the rule_index, so that the item
      * appear right after the clicked rule.
      */
     addNewRuleHandler = (rule_index)=> {
        this.setState((prevState, props)=> {
            rules: prevState.rules.splice(rule_index + 1, 0, {})
        })
     }
     render() {
         return (
             <div>
                 {
                    this.state.rules.map((ruleProps, ruleIndex)=> {
                        return <RuleComponent
                        key={ruleIndex} 
                        addNewRuleHandler={this.addNewRuleHandler} 
                        ruleIndex={ruleIndex} />
                    })
                 }
             </div>
         )
     }
 }
RuleGroupComponent.propTypes = {
    rules: PropTypes.array
}
export default RuleGroupComponent