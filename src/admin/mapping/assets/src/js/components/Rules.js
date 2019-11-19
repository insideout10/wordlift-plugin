import React from 'react';

import { MappingContext } from './MappingApp';

export const RuleSets = () => {
	return (
		<div className="wl-mapping__rules">
			<MappingContext.Consumer>
				{ ( { savedRules, addRuleGroupHandler } ) => ( <>
					{ savedRules.map( ( ruleSet, ruleSetIndex, currentArray ) => (
						ruleSet.length > 0 && <div className="wl-mapping__rule-set" key={ ruleSetIndex }>
							{ ruleSet.map( ( rule, ruleIndex ) => <RuleRow key={ ruleIndex } ruleRowData={ rule } ruleRowId={ ruleIndex } ruleSetIndex={ ruleSetIndex } /> ) }
							{ ruleSetIndex < currentArray.length - 1 && 0 !== currentArray[ ruleSetIndex ].length && <div className="wl-mapping__separator">OR</div> }
						</div>
					) ) }
					<button className="button" type="button" onClick={ addRuleGroupHandler }>Add rule group</button>
				</> ) }
			</MappingContext.Consumer>
		</div>
	)
}

const RuleRow = ( { ruleRowData, ruleSetIndex, ruleRowId } ) => (
	<MappingContext.Consumer>
		{ ( { addRuleHandler, deleteRuleHandler  } ) => ( <div className="wl-mapping__rule-item">
			<GenerateDropdown ruleRowData={ ruleRowData } ruleSetIndex={ ruleSetIndex} ruleRowId={ ruleRowId } />
			<button className="button" type="button" onClick={ ( e ) => addRuleHandler( e, ruleSetIndex ) }>And</button>
			<button className="button wl-mapping__delete-rule" type="button" onClick={ ( e ) => deleteRuleHandler( e, ruleSetIndex, ruleRowId ) }>Delete</button>
		</div> ) }
	</MappingContext.Consumer>
);

const GenerateDropdown = ( { ruleRowData, ruleSetIndex, ruleRowId } ) => (
	<MappingContext.Consumer>
		{ ( { wpObjects, relations, wpObjectChangeHandler } ) => ( <>
			<select defaultValue={ ruleRowData.wpObject } onChange={ ( e ) => wpObjectChangeHandler( e, ruleSetIndex, ruleRowId ) }>
				{ wpObjects.map( ( wpObject, wpObjectIndex ) => <option key={ wpObjectIndex } value={ wpObject.value }>{ wpObject.label }</option> ) }
			</select>
			<select defaultValue={ ruleRowData.relation }>
				{ Object.keys( relations ).map( ( relationKey, relationIndex ) => <option key={ relationIndex } value={ relationKey }>{ relations[ relationKey ] }</option> ) }
			</select>

			{ !! ruleRowData.value ? <select defaultValue={ ruleRowData.value } >
				{ wpObjects.map( ( wpObject, wpObjectIndex ) => (
					( wpObject.value === ruleRowData.wpObject ) && wpObject.data.map( ( valueItem, valueItemIndex ) => (
						<option key={ valueItemIndex } value={ valueItem.value }>{ valueItem.label }</option>
					) )
				) ) }
			</select> : <select>
				{ wpObjects[ 0 ].data.map( ( valueItem, valueItemIndex ) => <option key={ valueItemIndex } value={ valueItem.value }>{ valueItem.label }</option> ) }
			</select> }
		</> ) }
	</MappingContext.Consumer>
);
