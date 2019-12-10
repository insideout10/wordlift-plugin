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
        const options = [
            { value: 'one', label: 'one' },
            { value: 'two', label: 'two' },
            { value: 'three', label: 'three' }
        ]
        const mock_rule_item = {
            ruleFieldOneOptions: options,
            ruleFieldTwoOptions: options,
            ruleLogicFieldOptions: options,
        }
        this.setState(prevState => ({
            rules: [...prevState.rules.slice(0, rule_index),
                    mock_rule_item,
                    ...prevState.rules.slice(rule_index)]
          }))
     }
     deleteCurrentRuleHandler = (ruleIndex)=> {
         console.log(this.state.rules)
         console.log(ruleIndex)
         // remove the clicked item by referrring to index
         this.setState(prevState => ({
            rules: prevState.rules.filter((_, index) => index !== ruleIndex)
         }))
     }
     render() {
         return (
             <div>
                 {
                    this.state.rules.map((ruleProps, ruleIndex)=> {
                        return <RuleComponent
                        key={ruleProps}
                        { ...ruleProps } 
                        addNewRuleHandler={this.addNewRuleHandler}
                        deleteCurrentRuleHandler={this.deleteCurrentRuleHandler}
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