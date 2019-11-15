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
		{ ( { andButtonHandler, deleteButtonHandler, defaultRuleset } ) => (
			<div>
				{
					Object.keys( defaultRuleset ).map( ( nestedObject, outerIndex ) => (
						<select key={ outerIndex }>
							{
								Object.keys( defaultRuleset[ nestedObject ] ).map( ( key, innerIndex ) => (
									<option key={ innerIndex } value={ key }>{ defaultRuleset[ nestedObject ][ key ] }</option>
								) )
							}
						</select>
					) )
				}

				<input type="button" value="And" onClick={ ( e ) => andButtonHandler( e, setNumber ) } />
				<input type="button" value="Delete" onClick={ ( e ) => deleteButtonHandler( e, setNumber, rowNumber ) } />{ setNumber }
			</div>
		) }
	</MappingContext.Consumer> ) :
	( <div>
		<MappingContext.Consumer>
			{ ( { andButtonHandler, deleteButtonHandler } ) => (
				<>
					<SelectBox ruleData={ ruleData } />
					<input type="button" value="And" onClick={ ( e ) => andButtonHandler( e, setNumber ) } />
					<input type="button" value="Delete" onClick={ ( e ) => deleteButtonHandler( e, setNumber, rowNumber ) } />{ setNumber }
				</>
			) }
		</MappingContext.Consumer>
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
