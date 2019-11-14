import React from 'react';

import { Rules } from './Rules';
export const MappingContext = React.createContext();

export class MappingApp extends React.Component {
	constructor() {
		super();

		this.andButtonHandler = this.andButtonHandler.bind( this );
		this.orButtonHandler = this.orButtonHandler.bind( this );

		this.state = {
			andButtonHandler: this.andButtonHandler,
			orButtonHandler: this.orButtonHandler,
			ruleset: [
				[
					{
						set: true,
						objectType: 'Post Category',
						relation: 'Equal To',
						postType: 'Post',
					},
					{
						set: true,
						objectType: 'Post Taxonomy',
						relation: 'Less Than',
						postType: 'Books',
					}
				],
				[
					{
						set: true,
						objectType: 'Post Archive',
						relation: 'More Than',
						postType: 'Literature',
					}
				]
			],
		}
	}

	andButtonHandler( event, setNumber ) {
		const updatedSubSet = [
			...this.state.ruleset[ setNumber ],
			{
				set: false,
				objectType: [
					'Post Category',
					'Post Taxonomy',
					'Post Archive',
				],
				relation: [
					'Equal To',
					'Less Than',
					'More Than',
				],
				postType: [
					'Post',
					'Books',
					'Literature',
				]
			}
		];

		let updatedSet = this.state.ruleset;
		updatedSet[ setNumber ] = updatedSubSet;

		this.setState( {
			ruleset: updatedSet,
		} );
	}

	orButtonHandler() {
		
	}

	render() {
		return (
			<MappingContext.Provider value={ this.state }>
				<Rules />
			</MappingContext.Provider>
		);
	}
}
