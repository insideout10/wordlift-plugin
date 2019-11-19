import React from 'react';

import { RuleSets } from './Rules';
import { Mapping } from './Mapping';

export const MappingContext = React.createContext();

export class MappingApp extends React.Component {
	constructor() {
		super();

		this.addRuleHandler = this.addRuleHandler.bind( this );
		this.deleteRuleHandler = this.deleteRuleHandler.bind( this );
		this.wpObjectChangeHandler = this.wpObjectChangeHandler.bind( this );
		this.addRuleGroupHandler = this.addRuleGroupHandler.bind( this );

		this.state = {
			addRuleHandler: this.addRuleHandler,
			deleteRuleHandler: this.deleteRuleHandler,
			wpObjectChangeHandler: this.wpObjectChangeHandler,
			addRuleGroupHandler: this.addRuleGroupHandler,

			wpObjects: [
				{
					label: 'Post Type',
					value: 'postType',
					data: [
						{ value: 'post', label: 'Post' },
						{ value: 'books', label: 'Books' },
						{ value: 'movies', label: 'Movies' },
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
					{ wpObject: 'postType', relation: 'equals', value: 'books', },
					{ wpObject: 'category', relation: 'equals', value: 'history', },
				]
			]
		}
	}

	addRuleHandler( event, ruleSetIndex ) {
		let savedRules = [...this.state.savedRules];
		savedRules[ ruleSetIndex ].push( { wpObject: 'postType', relation: 'equals', value: 'post', } );

		this.setState( { savedRules } );
	}

	deleteRuleHandler( event, ruleSetIndex, ruleIndex ) {
		let savedRules = [...this.state.savedRules];
		let ruleSet = savedRules[ ruleSetIndex ];
		let updatedRuleSet = ruleSet.filter( ( rule, index ) => index !== ruleIndex );
		savedRules[ ruleSetIndex ] = updatedRuleSet;

		savedRules = savedRules.filter( ( item ) => 0 !== item.length )

		console.log( savedRules )

		this.setState( { savedRules } );
	}

	wpObjectChangeHandler( event, ruleSetIndex, ruleIndex ) {
		let savedRules = [...this.state.savedRules];
		savedRules[ ruleSetIndex ][ ruleIndex ].wpObject = event.target.value;

		this.setState( { savedRules } );
	}

	addRuleGroupHandler() {
		this.setState( {
			savedRules: [
				...this.state.savedRules,
				[ { wpObject: 'postType', relation: 'equals', value: 'post', } ]
			]
		} )
	}

	render() {
		return (
			<MappingContext.Provider value={ this.state }>
				<RuleSets />
				<Mapping />
			</MappingContext.Provider>
		);
	}
}
