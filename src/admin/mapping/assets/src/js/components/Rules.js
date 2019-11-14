import React from 'react';

import { MappingContext } from './MappingApp';

export const Rules = () => {
	return (
		<MappingContext.Consumer>
			{
				( { and, orButtonHandler } ) => {
					let i;
					let rows = [];
					let outerIndex;
					return (
						<>
							{
								and.map( ( item, index ) => {
									outerIndex = index;
									rows[ index ] = [];

									for( i = 0; i < item; i++ ) {
										rows[ index ].push( <RuleRow key={ i } setNumber={ index } /> );
									}
								} )
							}
							{
								rows.map( ( rowArray, rowArrayIndex ) => {
									return (
										<div key={ rowArrayIndex } className="wl-mapping__row-set">
											{
												rowArray.map( ( row, rowIndex ) => ( row ) )
											}
											<h1>OR</h1>
										</div>
									)
								} )
							}
							<button type="button" onClick={ ( e ) => orButtonHandler( e, outerIndex ) }>Or</button>
						</>
					)
				}
			}
		</MappingContext.Consumer>
	)
}

const RuleRow = ( { setNumber } ) => (
	<MappingContext.Consumer>
		{
			( { andButtonHandler } ) => (
				<div className="wl-edit-mapping__rules-row">
					{/* Object Type */}
					<select name="rule-row[object-type]">
						<option>Object Type</option>
						<option>Post Category</option>
					</select>

					{/* Relation */}
					<select name="rule-row[relation]">
						<option>Equal To</option>
						<option>Less than</option>
					</select>

					{/* Post Type */}
					<select name="rule-row[post-type]">
						<option>Post Type</option>
						<option>Post Category</option>
					</select>

				<button type="button" onClick={ ( e ) => { andButtonHandler( e, setNumber ) } }>And</button>
				</div>
			)
		}
	</MappingContext.Consumer>
);