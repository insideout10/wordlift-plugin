import React from 'react';

import { RuleSets } from './Rules';
export const MappingContext = React.createContext();

export class MappingApp extends React.Component {
	constructor() {
		super();

		this.andButtonHandler = this.andButtonHandler.bind( this );
		this.deleteButtonHandler = this.deleteButtonHandler.bind( this );
		this.addRuleButtonHandler = this.addRuleButtonHandler.bind( this );

		this.state = {
			andButtonHandler: this.andButtonHandler,
			deleteButtonHandler: this.deleteButtonHandler,
			addRuleButtonHandler: this.addRuleButtonHandler,

			wpObjects: [
				{
					label: 'Post Type',
					value: 'postType',
					data: [
						{ value: 'post', label: 'Post' },
						{ value: 'books', label: 'Books' },
						{ value: 'post', label: 'Post' },
					],
				},
				{
					label: 'Category',
					value: 'category',
					data: [
						{ value: 'art', label: 'Art' },
						{ value: 'science', label: 'Science' },
						{ value: 'history', label: 'History' },
					],
				},
			],

			relations: {
				'equals': 'Equals',
				'notEquals': 'Not Equals',
			},

			savedRules: [
				[
					{ wpObject: 'postType', relation: 'equals', value: 'post', },
					{ wpObject: 'category', relation: 'notEquals', value: 'art', },
					{ wpObject: 'category', relation: 'equals', value: 'science', },
				],
				[
					{ wpObject: 'category', relation: 'equals', value: 'history', },
				]
			]
		}
	}

	andButtonHandler( event, ruleSetIndex ) {
		let savedRules = this.state.savedRules;
		savedRules[ ruleSetIndex ].push( {} );

		this.setState( {
			savedRules: savedRules,
		} );
	}

	deleteButtonHandler( e, setNumber, rowNumber ) {

	}

	addRuleButtonHandler() {
		alert('what');
	}

	render() {
		return (
			<MappingContext.Provider value={ this.state }>
				<RuleSets />
			</MappingContext.Provider>
		);
	}
}
