import React from 'react';

import { MappingContext } from './MappingApp';

export const Rules = () => {
	return (
		<MappingContext.Consumer>
			{
				( { and } ) => {
					let i;
					let rows = [];
					return (
						and.map( ( item, index ) => {
							for( i = 0; i < item; i++ ) {
								rows.push( <RuleRow key={ i } /> )
							}

							return rows;
						} )
					)
				}
			}
		</MappingContext.Consumer>
	)
}

const RuleRow = () => (
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

					<button type="button" onClick={ andButtonHandler }>And</button>
				</div>
			)
		}
	</MappingContext.Consumer>
);