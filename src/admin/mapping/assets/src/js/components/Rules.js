import React from 'react';

import { MappingContext } from './MappingApp';

export const Rules = () => {
	return (
		<MappingContext.Consumer>
			{ ( { ruleset } ) => ( 
				ruleset.map( ( ruleSetItem, ruleSetIndex ) => {
					return ( <div key={ ruleSetIndex } className="wl-mapping__ruleset">
						{ ruleSetItem.map( ( ruleRow, ruleRowIndex ) => {
							return <RuleRow key={ ruleRowIndex } ruleData={ ruleRow } setNumber={ ruleSetIndex } rowNumber={ ruleRowIndex } />
						} ) }
					</div> )
				} )
			) }
		</MappingContext.Consumer>
	)
}

const RuleRow = ( { ruleData, setNumber, rowNumber } ) => (
	ruleData.set ? ( <MappingContext.Consumer>
		{ ( { andButtonHandler } ) => (
			<div>
				<input defaultValue={ ruleData.objectType } />
				<input defaultValue={ ruleData.relation } />
				<input defaultValue={ ruleData.postType } />
				<input type="button" value="And" onClick={ ( e ) => andButtonHandler( e, setNumber, rowNumber ) } />
				<input type="button" value="Delete" />{ setNumber }
			</div>
		) }
	</MappingContext.Consumer> ) :
	( <div>
		<SelectBox ruleData={ ruleData } />
	</div> )
);

const SelectBox = ( { ruleData } ) => (
	<>
		<select>
			{ ruleData.objectType.map( ( item, index ) => (
				<option key={ index }>{ item }</option>
			) ) }
		</select>

		<select>
			{ ruleData.relation.map( ( item, index ) => (
				<option key={ index }>{ item }</option>
			) ) }
		</select>

		<select>
			{ ruleData.postType.map( ( item, index ) => (
				<option key={ index }>{ item }</option>
			) ) }
		</select>
	</>
);
