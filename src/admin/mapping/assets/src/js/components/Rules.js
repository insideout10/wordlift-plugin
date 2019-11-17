import React from 'react';

import { MappingContext } from './MappingApp';

export const RuleSets = () => {
	return (
		<MappingContext.Consumer>
			{ ( { savedRules } ) => savedRules.map( ( ruleSet, ruleSetIndex ) => <div style={ { borderBottom: '1px solid' } } key={ ruleSetIndex }>{ ruleSet.map( ( rule, ruleIndex ) => <RuleRow key={ ruleIndex } ruleRowData={ rule } ruleRowId={ ruleIndex } ruleSetIndex={ ruleSetIndex } /> ) }</div> ) }
		</MappingContext.Consumer>
	)
}

const RuleRow = ( { ruleRowData, ruleSetIndex, ruleRowId } ) => (
	<MappingContext.Consumer>
		{ ( { addRuleHandler, deleteRuleHandler  } ) => ( <div>
			<GenerateDropdown selectedValues={ ruleRowData } ruleRowId={ ruleRowId } />
			<button type="button" onClick={ ( e ) => addRuleHandler( e, ruleSetIndex ) }>Add</button>
			<button type="button" onClick={ ( e ) => deleteRuleHandler( e, ruleSetIndex, ruleRowId ) }>Delete</button>
		</div> ) }
	</MappingContext.Consumer>
);

const GenerateDropdown = ( { selectedValues } ) => (
	<MappingContext.Consumer>
		{ ( { wpObjects, relations } ) => ( <>
			<select defaultValue={ selectedValues.wpObject }>
				{ wpObjects.map( ( wpObject, wpObjectIndex ) => <option key={ wpObjectIndex } value={ wpObject.value }>{ wpObject.label }</option> ) }
			</select>
			<select defaultValue={ selectedValues.relation }>
				{ Object.keys( relations ).map( ( relationKey, relationIndex ) => <option key={ relationIndex } value={ relationKey }>{ relations[ relationKey ] }</option> ) }
			</select>

			{ !! selectedValues.value ? <select defaultValue={ selectedValues.value } >
				{ wpObjects.map( ( wpObject, wpObjectIndex ) => (
					( wpObject.value === selectedValues.wpObject ) && wpObject.data.map( ( valueItem, valueItemIndex ) => (
						<option key={ valueItemIndex } value={ valueItem.value }>{ valueItem.label }</option>
					) )
				) ) }
			</select> : <select>
				{ wpObjects[ 0 ].data.map( ( valueItem, valueItemIndex ) => <option key={ valueItemIndex } value={ valueItem.value }>{ valueItem.label }</option> ) }
			</select> }
		</> ) }
	</MappingContext.Consumer>
);
