import React from 'react';

import { MappingContext } from './MappingApp';

export const RuleSets = () => {
	return (
		<MappingContext.Consumer>
			{ ( { savedRules } ) => savedRules.map( ( ruleSet, ruleSetIndex ) => <div style={ { borderBottom: '1px solid' } } key={ ruleSetIndex }>{ ruleSet.map( ( rule, ruleIndex ) => <RuleRow key={ ruleIndex } ruleRowData={ rule } ruleRowId={ ruleIndex } ruleSetIndex={ ruleSetIndex } /> ) }</div> ) }
		</MappingContext.Consumer>
	)
}

const RuleRow = ( { ruleRowData, ruleSetIndex } ) => (
	<MappingContext.Consumer>
		{ ( { addRuleHandler, deleteButtonHandler  } ) => ( <div>
			<GenerateDropdown selectedValues={ ruleRowData } />
			<button type="button" onClick={ ( e ) => addRuleHandler( e, ruleSetIndex ) }>Add</button>
			<button type="button">Delete</button>
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
		</> ) }
	</MappingContext.Consumer>
);
