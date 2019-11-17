import React from 'react';

import { RuleSets } from './Rules';
export const MappingContext = React.createContext();

export class MappingApp extends React.Component {
	constructor() {
		super();

		this.addRuleHandler = this.addRuleHandler.bind( this );
		this.deleteRuleHandler = this.deleteRuleHandler.bind( this );
		this.addRuleButtonHandler = this.addRuleButtonHandler.bind( this );

		this.state = {
			addRuleHandler: this.addRuleHandler,
			deleteRuleHandler: this.deleteRuleHandler,
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

	addRuleHandler( event, ruleSetIndex ) {
		let savedRules = this.state.savedRules;
		savedRules[ ruleSetIndex ].push( {} );

		this.setState( { savedRules } );
	}

	deleteRuleHandler( event, ruleSetIndex, ruleIndex ) {
		let savedRules = this.state.savedRules;
		let ruleSet = savedRules[ ruleSetIndex ];
		let updatedRuleSet = ruleSet.filter( ( rule, index ) => index !== ruleIndex );

		savedRules[ ruleSetIndex ] = updatedRuleSet;

		this.setState( { savedRules } );
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
