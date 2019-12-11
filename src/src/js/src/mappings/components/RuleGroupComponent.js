/**
 * @since 3.24.0
 * 
 * RuleGroupComponent : it displays the list of rules, let the user
 * add new rules
 */
import React from 'react'
import PropTypes from 'prop-types';
import RuleComponent from './RuleComponent';
import { connect } from 'react-redux'

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

        const mock_rule_item = {
        }
        this.setState(prevState => ({
            rules: [...prevState.rules.slice(0, rule_index),
                    mock_rule_item,
                    ...prevState.rules.slice(rule_index)]
          }))
     }
     deleteCurrentRuleHandler = (ruleIndex)=> {
         // remove the clicked item by referrring to index
         this.setState(prevState => ({
            rules: prevState.rules.filter((_, index) => index !== ruleIndex)
         }))
     }
     render() {
         return (
             <div className="rule-group-container">
                 {
                    this.state.rules.map((ruleProps, ruleIndex)=> {
                        return <RuleComponent />
                    })
                 }
             </div>
         )
     }
 }
RuleGroupComponent.propTypes = {
    rules: PropTypes.array
}
const mapStateToProps = function (state) {

}
export default connect(mapStateToProps)(RuleGroupComponent)